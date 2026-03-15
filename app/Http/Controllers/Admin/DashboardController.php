<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AirQualityActual;
use App\Models\DataImport;
use App\Models\LstmPrediction;
use App\Models\LstmRun;
use App\Models\PredictionSetting;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalData = AirQualityActual::query()->count();
        $range = AirQualityActual::query()
            ->selectRaw('MIN(observed_at) as min_observed_at, MAX(observed_at) as max_observed_at')
            ->first();

        $missingCount = AirQualityActual::query()
            ->whereNull('pm10')
            ->orWhereNull('pm25')
            ->count();

        $latestImport = DataImport::query()
            ->where('status', 'processed')
            ->latest('id')
            ->first();

        $duplicateCount = 0;
        if ($latestImport && is_string($latestImport->notes) && $latestImport->notes !== '') {
            $notes = json_decode($latestImport->notes, true);
            if (is_array($notes)) {
                $duplicateCount = (int) ($notes['duplicate_in_file'] ?? 0);
            }
        }

        $pm10Stats = $this->buildPollutantStats('pm10');
        $pm25Stats = $this->buildPollutantStats('pm25');
        [$activeRun, $latestRun] = $this->resolveRunContext();
        $accuracy = $this->buildAccuracyStats($activeRun);
        $predictionPm10 = $this->buildPredictionStats($activeRun, 'pm10_predicted');
        $predictionPm25 = $this->buildPredictionStats($activeRun, 'pm25_predicted');
        $processSteps = $this->buildProcessSteps($activeRun ?? $latestRun);

        return view('admin.dashboard.index', [
            'dashboard' => [
                'total_data' => $totalData,
                'start_date' => $range?->min_observed_at,
                'end_date' => $range?->max_observed_at,
                'missing_count' => $missingCount,
                'duplicate_count' => $duplicateCount,
                'pm10' => $pm10Stats,
                'pm25' => $pm25Stats,
                'accuracy' => $accuracy,
                'prediction_pm10' => $predictionPm10,
                'prediction_pm25' => $predictionPm25,
                'process_steps' => $processSteps,
                'active_run' => $activeRun ? [
                    'id' => $activeRun->id,
                    'run_code' => $activeRun->run_code,
                    'status' => $activeRun->status,
                    'started_at' => $activeRun->started_at?->toDateTimeString(),
                ] : null,
            ],
        ]);
    }

    private function resolveRunContext(): array
    {
        $setting = PredictionSetting::query()->first();
        $activeRun = null;

        if ($setting?->active_lstm_run_id) {
            $activeRun = LstmRun::query()
                ->with(['metrics', 'predictions', 'steps'])
                ->find($setting->active_lstm_run_id);
        }

        if (! $activeRun) {
            $activeRun = LstmRun::query()
                ->where('status', 'success')
                ->with(['metrics', 'predictions', 'steps'])
                ->latest('id')
                ->first();
        }

        $latestRun = LstmRun::query()
            ->with('steps')
            ->latest('id')
            ->first();

        return [$activeRun, $latestRun];
    }

    private function buildAccuracyStats(?LstmRun $run): array
    {
        if (! $run) {
            return ['mae' => '-', 'rmse' => '-', 'r2' => '-'];
        }

        $pm10 = $run->metrics->firstWhere('pollutant', 'pm10');
        $pm25 = $run->metrics->firstWhere('pollutant', 'pm25');

        $avgMae = $this->averageNumbers([$pm10?->mae, $pm25?->mae]);
        $avgRmse = $this->averageNumbers([$pm10?->rmse, $pm25?->rmse]);
        $avgR2 = $this->averageNumbers([$pm10?->r2, $pm25?->r2]);

        return [
            'mae' => $avgMae !== null ? number_format($avgMae, 4, ',', '.') : '-',
            'rmse' => $avgRmse !== null ? number_format($avgRmse, 4, ',', '.') : '-',
            'r2' => $avgR2 !== null ? number_format($avgR2, 4, ',', '.') : '-',
        ];
    }

    private function buildPredictionStats(?LstmRun $run, string $column): array
    {
        if (! $run) {
            return [
                'average' => '0,0',
                'highest' => '0',
                'lowest' => '0',
                'category' => $this->classify(0.0),
            ];
        }

        $row = LstmPrediction::query()
            ->where('lstm_run_id', $run->id)
            ->selectRaw("AVG({$column}) as avg_value, MAX({$column}) as max_value, MIN({$column}) as min_value")
            ->whereNotNull($column)
            ->first();

        $avg = $row?->avg_value !== null ? (float) $row->avg_value : 0.0;
        $max = $row?->max_value !== null ? (float) $row->max_value : 0.0;
        $min = $row?->min_value !== null ? (float) $row->min_value : 0.0;

        return [
            'average' => number_format($avg, 1, ',', '.'),
            'highest' => $this->formatValue($max),
            'lowest' => $this->formatValue($min),
            'category' => $this->classify($avg),
        ];
    }

    private function buildProcessSteps(?LstmRun $run): array
    {
        $fallback = ['Preprocessing', 'Scaling', 'Windowing', 'Training', 'Generate', 'Evaluasi'];
        $stepLabels = [
            'preprocessing' => 'Preprocessing',
            'scaling' => 'Scaling',
            'windowing' => 'Windowing',
            'training' => 'Training',
            'generate' => 'Generate',
            'evaluation' => 'Evaluasi',
        ];

        if (! $run || $run->steps->isEmpty()) {
            return array_map(fn (string $name) => ['label' => $name, 'status' => 'pending'], $fallback);
        }

        return $run->steps
            ->sortBy('step_order')
            ->map(function ($step) use ($stepLabels) {
                $status = strtolower((string) $step->status);
                if (! in_array($status, ['success', 'failed', 'running', 'pending'], true)) {
                    $status = 'pending';
                }

                return [
                    'label' => $stepLabels[$step->step_key] ?? ucfirst((string) $step->step_key),
                    'status' => $status,
                ];
            })
            ->values()
            ->all();
    }

    private function buildPollutantStats(string $column): array
    {
        $row = AirQualityActual::query()
            ->selectRaw("AVG({$column}) as avg_value, MAX({$column}) as max_value, MIN({$column}) as min_value")
            ->whereNotNull($column)
            ->first();

        $avg = $row?->avg_value !== null ? (float) $row->avg_value : 0.0;
        $max = $row?->max_value !== null ? (float) $row->max_value : 0.0;
        $min = $row?->min_value !== null ? (float) $row->min_value : 0.0;
        $category = $this->classify($avg);

        return [
            'average' => number_format($avg, 1, ',', '.'),
            'highest' => $this->formatValue($max),
            'lowest' => $this->formatValue($min),
            'category' => $category,
        ];
    }

    private function classify(float $value): array
    {
        if ($value <= 15.5) {
            return ['key' => 'baik', 'label' => 'Baik', 'hex' => '#16A34A', 'text_class' => 'text-ispu-baik'];
        }

        if ($value <= 55.4) {
            return ['key' => 'sedang', 'label' => 'Sedang', 'hex' => '#2563EB', 'text_class' => 'text-ispu-sedang'];
        }

        if ($value <= 150.4) {
            return ['key' => 'tidak_sehat', 'label' => 'Tidak Sehat', 'hex' => '#FACC15', 'text_class' => 'text-ispu-tidak-sehat'];
        }

        if ($value <= 250.4) {
            return ['key' => 'sangat_tidak_sehat', 'label' => 'Sangat Tidak Sehat', 'hex' => '#DC2626', 'text_class' => 'text-ispu-sangat-tidak-sehat'];
        }

        return ['key' => 'berbahaya', 'label' => 'Berbahaya', 'hex' => '#111827', 'text_class' => 'text-ispu-berbahaya'];
    }

    private function formatValue(float $value): string
    {
        if ((int) $value === $value) {
            return (string) (int) $value;
        }

        return rtrim(rtrim(number_format($value, 2, ',', '.'), '0'), ',');
    }

    private function averageNumbers(array $values): ?float
    {
        $numbers = array_values(array_filter($values, static fn ($value) => $value !== null));
        if (count($numbers) === 0) {
            return null;
        }

        return array_sum($numbers) / count($numbers);
    }
}
