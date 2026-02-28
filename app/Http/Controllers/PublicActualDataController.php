<?php

namespace App\Http\Controllers;

use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicActualDataController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $date = $this->resolveDate($request->query('date'));

        $pm10Data24h = [
            ['hour' => 0, 'value' => 30], ['hour' => 1, 'value' => 30], ['hour' => 2, 'value' => 32],
            ['hour' => 3, 'value' => 32], ['hour' => 4, 'value' => 19], ['hour' => 5, 'value' => 38],
            ['hour' => 6, 'value' => 15], ['hour' => 7, 'value' => 48], ['hour' => 8, 'value' => 34],
            ['hour' => 9, 'value' => 27], ['hour' => 10, 'value' => 37], ['hour' => 11, 'value' => 25],
            ['hour' => 12, 'value' => 42], ['hour' => 13, 'value' => 17], ['hour' => 14, 'value' => 16],
            ['hour' => 15, 'value' => 44], ['hour' => 16, 'value' => 35], ['hour' => 17, 'value' => 28],
            ['hour' => 18, 'value' => 32], ['hour' => 19, 'value' => 39], ['hour' => 20, 'value' => 45],
            ['hour' => 21, 'value' => 25], ['hour' => 22, 'value' => 20], ['hour' => 23, 'value' => 17],
        ];

        $pm25Data24h = [
            ['hour' => 0, 'value' => 2], ['hour' => 1, 'value' => 10], ['hour' => 2, 'value' => 9],
            ['hour' => 3, 'value' => 9], ['hour' => 4, 'value' => 14], ['hour' => 5, 'value' => 5],
            ['hour' => 6, 'value' => 3], ['hour' => 7, 'value' => 14], ['hour' => 8, 'value' => 11],
            ['hour' => 9, 'value' => 3], ['hour' => 10, 'value' => 13], ['hour' => 11, 'value' => 13],
            ['hour' => 12, 'value' => 12], ['hour' => 13, 'value' => 2], ['hour' => 14, 'value' => 15],
            ['hour' => 15, 'value' => 6], ['hour' => 16, 'value' => 1], ['hour' => 17, 'value' => 3],
            ['hour' => 18, 'value' => 3], ['hour' => 19, 'value' => 1], ['hour' => 20, 'value' => 14],
            ['hour' => 21, 'value' => 12], ['hour' => 22, 'value' => 3], ['hour' => 23, 'value' => 3],
        ];

        $seed = (int) $date->format('Ymd');

        return response()->json([
            'date' => $date->toDateString(),
            'ranges' => [
                '24 Jam' => [
                    'pm10' => $pm10Data24h,
                    'pm25' => $pm25Data24h,
                ],
                '7 Hari' => [
                    'pm10' => $this->buildDailySeries($pm10Data24h, 7, $seed + 11),
                    'pm25' => $this->buildDailySeries($pm25Data24h, 7, $seed + 29),
                ],
                '30 Hari' => [
                    'pm10' => $this->buildDailySeries($pm10Data24h, 30, $seed + 47),
                    'pm25' => $this->buildDailySeries($pm25Data24h, 30, $seed + 71),
                ],
            ],
            'generated_at' => now()->toIso8601String(),
        ]);
    }

    private function resolveDate(mixed $rawDate): CarbonImmutable
    {
        if (!is_string($rawDate) || trim($rawDate) === '') {
            return CarbonImmutable::today();
        }

        try {
            return CarbonImmutable::parse($rawDate);
        } catch (\Throwable) {
            return CarbonImmutable::today();
        }
    }

    /**
     * @param array<int, array{hour:int, value:int}> $data24h
     * @return array<int, array{hour:int, value:int}>
     */
    private function buildDailySeries(array $data24h, int $days, int $seed): array
    {
        $baseAvg = collect($data24h)->avg('value') ?? 0;
        $amplitude = max(2, (int) round($baseAvg * 0.25));
        $phase = (($seed % 97) + 3) / 100;
        $offset = $seed % 11;

        $rows = [];
        for ($i = 1; $i <= $days; $i++) {
            $swing = sin(($i + $offset) * $phase) * $amplitude;
            $trend = (($i + $offset) % 6) - 2;
            $value = max(0, (int) round($baseAvg + $swing + $trend));
            $rows[] = ['hour' => $i, 'value' => $value];
        }

        return $rows;
    }
}

