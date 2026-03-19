<?php

namespace App\Http\Controllers;

use App\Models\LstmPrediction;
use App\Models\LstmRun;
use App\Models\PredictionSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class PublicHomeController extends Controller
{
    public function __invoke(): View
    {
        $run = $this->resolveActiveRun();

        $payload = [
            'window_title' => 'Ringkasan Prediksi 6 Jam ke Depan',
            'window_date_label' => '-',
            'labels' => [],
            'pm10' => [
                'series' => [],
                'summary' => $this->emptySummary(),
            ],
            'pm25' => [
                'series' => [],
                'summary' => $this->emptySummary(),
            ],
        ];

        if ($run) {
            $window = $this->resolveSixHourWindow($run->id);
            $rows = $window['rows'];

            $pm10Series = $rows
                ->map(fn (LstmPrediction $row) => $row->pm10_predicted !== null ? (float) $row->pm10_predicted : null)
                ->values()
                ->all();
            $pm25Series = $rows
                ->map(fn (LstmPrediction $row) => $row->pm25_predicted !== null ? (float) $row->pm25_predicted : null)
                ->values()
                ->all();

            $pm10Summary = $this->buildSummary($pm10Series);
            $pm25Summary = $this->buildSummary($pm25Series);

            $payload = [
                'window_title' => $window['title'],
                'window_date_label' => $this->buildWindowDateLabel($rows),
                'labels' => $rows->map(fn (LstmPrediction $row) => $row->predicted_for?->format('H:i'))->values()->all(),
                'pm10' => [
                    'series' => $pm10Series,
                    'summary' => $pm10Summary,
                ],
                'pm25' => [
                    'series' => $pm25Series,
                    'summary' => $pm25Summary,
                ],
            ];
        }

        return view('public.home.index', [
            'predictionPayload' => $payload,
        ]);
    }

    private function resolveActiveRun(): ?LstmRun
    {
        if (! Schema::hasTable('prediction_settings')) {
            return null;
        }

        $activeRunId = PredictionSetting::query()->value('active_lstm_run_id');
        if (! $activeRunId) {
            return null;
        }

        return LstmRun::query()->find($activeRunId);
    }

    private function forecastQuery(int $runId)
    {
        return LstmPrediction::query()
            ->where('lstm_run_id', $runId)
            ->whereNull('pm10_actual')
            ->whereNull('pm25_actual');
    }

    private function resolveSixHourWindow(int $runId): array
    {
        $now = Carbon::now();
        $todayStart = $now->copy()->startOfDay();
        $todayEnd = $now->copy()->endOfDay();

        $todayForward = $this->forecastQuery($runId)
            ->whereBetween('predicted_for', [$todayStart, $todayEnd])
            ->where('predicted_for', '>=', $now)
            ->orderBy('predicted_for')
            ->limit(6)
            ->get(['predicted_for', 'pm10_predicted', 'pm25_predicted']);

        if ($todayForward->count() > 0) {
            return [
                'title' => 'Ringkasan Prediksi 6 Jam ke Depan',
                'rows' => $todayForward,
            ];
        }

        $lastRows = $this->forecastQuery($runId)
            ->orderByDesc('predicted_for')
            ->limit(6)
            ->get(['predicted_for', 'pm10_predicted', 'pm25_predicted'])
            ->sortBy('predicted_for')
            ->values();

        return [
            'title' => 'Ringkasan Prediksi 6 Jam Terakhir',
            'rows' => $lastRows,
        ];
    }

    private function buildWindowDateLabel($rows): string
    {
        if ($rows->isEmpty()) {
            return '-';
        }

        $start = $rows->first()?->predicted_for;
        $end = $rows->last()?->predicted_for;

        if (! $start || ! $end) {
            return '-';
        }

        if ($start->isSameDay($end)) {
            return $start->copy()->locale('id')->translatedFormat('d F Y');
        }

        return $start->copy()->locale('id')->translatedFormat('d F Y')
            . ' - '
            . $end->copy()->locale('id')->translatedFormat('d F Y');
    }

    private function buildSummary(array $series): array
    {
        $numbers = collect($series)
            ->filter(fn ($v) => $v !== null)
            ->map(fn ($v) => (float) $v)
            ->values();

        if ($numbers->isEmpty()) {
            return $this->emptySummary();
        }

        $avg = round($numbers->avg(), 1);
        $max = round($numbers->max(), 4);
        $category = $this->indicatorFromValue($avg);

        return [
            'average' => number_format($avg, 1, ',', '.'),
            'highest' => $max,
            'lowest' => round($numbers->min(), 4),
            'category' => $category,
        ];
    }

    private function indicatorFromValue(float $value): array
    {
        if ($value <= 15.5) {
            return ['key' => 'baik', 'label' => 'Baik', 'hex' => '#16A34A', 'text_class' => 'text-ispu-baik'];
        }
        if ($value <= 55.4) {
            return ['key' => 'sedang', 'label' => 'Sedang', 'hex' => '#2563EB', 'text_class' => 'text-ispu-sedang'];
        }
        if ($value <= 150.4) {
            return ['key' => 'tidakSehat', 'label' => 'Tidak Sehat', 'hex' => '#FACC15', 'text_class' => 'text-ispu-tidak-sehat'];
        }
        if ($value <= 250.4) {
            return ['key' => 'sangatTidakSehat', 'label' => 'Sangat Tidak Sehat', 'hex' => '#DC2626', 'text_class' => 'text-ispu-sangat-tidak-sehat'];
        }

        return ['key' => 'berbahaya', 'label' => 'Berbahaya', 'hex' => '#111827', 'text_class' => 'text-ispu-berbahaya'];
    }

    private function emptySummary(): array
    {
        return [
            'average' => '0,0',
            'highest' => 0,
            'lowest' => 0,
            'category' => ['key' => 'sedang', 'label' => '-', 'hex' => '#2563EB', 'text_class' => 'text-surface-300'],
        ];
    }
}
