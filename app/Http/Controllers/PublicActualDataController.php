<?php

namespace App\Http\Controllers;

use App\Models\AirQualityActual;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicActualDataController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $availableDates = AirQualityActual::query()
            ->selectRaw('DATE(observed_at) as d')
            ->distinct()
            ->orderByRaw('DATE(observed_at)')
            ->pluck('d')
            ->map(fn ($d) => (string) $d)
            ->values()
            ->all();

        $date = $this->resolveDate($request->query('date'), $availableDates);
        $series24h = $this->build24HourSeries($date);
        $series7d = $this->build7DaySeries($date);
        $series30d = $this->buildMonthSeries($date);
        $availableMonths = collect($availableDates)
            ->map(fn ($d) => substr($d, 0, 7))
            ->unique()
            ->values()
            ->all();

        return response()->json([
            'date' => $date->toDateString(),
            'available_dates' => $availableDates,
            'available_months' => $availableMonths,
            'ranges' => [
                '24 Jam' => [
                    'pm10' => $series24h['pm10'],
                    'pm25' => $series24h['pm25'],
                ],
                '7 Hari' => [
                    'pm10' => $series7d['pm10'],
                    'pm25' => $series7d['pm25'],
                ],
                '30 Hari' => [
                    'pm10' => $series30d['pm10'],
                    'pm25' => $series30d['pm25'],
                ],
            ],
            'generated_at' => now()->toIso8601String(),
        ]);
    }

    private function resolveDate(mixed $rawDate, array $availableDates): CarbonImmutable
    {
        if (! empty($availableDates)) {
            if (is_string($rawDate) && in_array($rawDate, $availableDates, true)) {
                return CarbonImmutable::parse($rawDate);
            }

            return CarbonImmutable::parse($availableDates[count($availableDates) - 1]);
        }

        if (!is_string($rawDate) || trim($rawDate) === '') {
            return CarbonImmutable::today();
        }

        try {
            return CarbonImmutable::parse($rawDate);
        } catch (\Throwable) {
            return CarbonImmutable::today();
        }
    }

    private function build24HourSeries(CarbonImmutable $date): array
    {
        $rows = AirQualityActual::query()
            ->whereDate('observed_at', $date->toDateString())
            ->orderBy('observed_at')
            ->get(['pm10', 'pm25']);

        $pm10 = [];
        $pm25 = [];

        foreach ($rows as $idx => $row) {
            if ($row->pm10 !== null) {
                $pm10[] = ['hour' => $idx, 'value' => (float) $row->pm10];
            }
            if ($row->pm25 !== null) {
                $pm25[] = ['hour' => $idx, 'value' => (float) $row->pm25];
            }
        }

        return ['pm10' => $pm10, 'pm25' => $pm25];
    }

    private function build7DaySeries(CarbonImmutable $date): array
    {
        $start = $date->subDays(6)->startOfDay();
        $end = $date->endOfDay();

        return $this->buildDailyAverageSeries($start->toDateTimeString(), $end->toDateTimeString());
    }

    private function buildMonthSeries(CarbonImmutable $date): array
    {
        $start = $date->startOfMonth()->startOfDay();
        $end = $date->endOfMonth()->endOfDay();

        return $this->buildDailyAverageSeries($start->toDateTimeString(), $end->toDateTimeString());
    }

    private function buildDailyAverageSeries(string $start, string $end): array
    {
        $rows = AirQualityActual::query()
            ->selectRaw('DATE(observed_at) as day, AVG(pm10) as avg_pm10, AVG(pm25) as avg_pm25')
            ->whereBetween('observed_at', [$start, $end])
            ->groupByRaw('DATE(observed_at)')
            ->orderByRaw('DATE(observed_at)')
            ->get();

        $pm10 = [];
        $pm25 = [];

        foreach ($rows as $idx => $row) {
            $dayIndex = $idx + 1;
            if ($row->avg_pm10 !== null) {
                $pm10[] = ['hour' => $dayIndex, 'value' => round((float) $row->avg_pm10, 2)];
            }
            if ($row->avg_pm25 !== null) {
                $pm25[] = ['hour' => $dayIndex, 'value' => round((float) $row->avg_pm25, 2)];
            }
        }

        return ['pm10' => $pm10, 'pm25' => $pm25];
    }
}
