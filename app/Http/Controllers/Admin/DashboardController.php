<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AirQualityActual;
use App\Models\DataImport;
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

        return view('admin.dashboard.index', [
            'dashboard' => [
                'total_data' => $totalData,
                'start_date' => $range?->min_observed_at,
                'end_date' => $range?->max_observed_at,
                'missing_count' => $missingCount,
                'duplicate_count' => $duplicateCount,
                'pm10' => $pm10Stats,
                'pm25' => $pm25Stats,
            ],
        ]);
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
}

