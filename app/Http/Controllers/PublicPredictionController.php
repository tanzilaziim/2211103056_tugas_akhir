<?php

namespace App\Http\Controllers;

use App\Models\LstmPrediction;
use App\Models\LstmRun;
use App\Models\PredictionSetting;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicPredictionController extends Controller
{
    public function meta(): JsonResponse
    {
        $run = $this->resolveActiveRun();
        if (! $run) {
            return response()->json([
                'data' => [
                    'active_run' => null,
                    'dates' => [],
                    'months' => [],
                    'defaults' => [
                        'single' => null,
                        'start' => null,
                        'end' => null,
                        'month' => null,
                    ],
                ],
            ]);
        }

        $dates = $this->getRunDates($run->id);
        $months = collect($dates)
            ->map(fn (string $date) => substr($date, 0, 7))
            ->unique()
            ->values()
            ->all();
        $single = $dates[0] ?? null;

        return response()->json([
            'data' => [
                'active_run' => [
                    'id' => $run->id,
                    'run_code' => $run->run_code,
                    'status' => $run->status,
                    'started_at' => $run->started_at?->toDateTimeString(),
                ],
                'dates' => $dates,
                'months' => $months,
                'defaults' => [
                    'single' => $single,
                    'start' => $single,
                    'end' => $single ? Carbon::parse($single)->addDays(6)->toDateString() : null,
                    'month' => $single ? substr($single, 0, 7) : null,
                ],
            ],
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $run = $this->resolveActiveRun();
        if (! $run) {
            return response()->json([
                'data' => [
                    'active_run' => null,
                    'dates' => [],
                    'range' => (string) $request->query('range', '24jam'),
                    'applied' => [
                        'date_single' => null,
                        'date_start' => null,
                        'date_end' => null,
                        'date_month' => null,
                    ],
                    'pm10' => ['labels' => [], 'series' => [], 'stats' => $this->emptyStats()],
                    'pm25' => ['labels' => [], 'series' => [], 'stats' => $this->emptyStats()],
                ],
            ]);
        }

        $range = (string) $request->query('range', '24jam');
        $query = $this->forecastQuery($run->id);
        [$applied, $dates] = $this->applyRangeFilter($query, $run->id, $range, $request);
        $rows = $query->orderBy('predicted_for')->get(['predicted_for', 'pm10_predicted', 'pm25_predicted']);

        if ($range === '24jam') {
            $labels = $rows->map(fn (LstmPrediction $row) => $row->predicted_for?->format('H:i'))->values()->all();
            $pm10Series = $rows->map(fn (LstmPrediction $row) => $row->pm10_predicted !== null ? (float) $row->pm10_predicted : null)->values()->all();
            $pm25Series = $rows->map(fn (LstmPrediction $row) => $row->pm25_predicted !== null ? (float) $row->pm25_predicted : null)->values()->all();
        } else {
            $daily = $rows
                ->groupBy(fn (LstmPrediction $row) => $row->predicted_for?->format('Y-m-d'))
                ->map(function ($group, $date) {
                    $pm10Vals = $group->pluck('pm10_predicted')->filter(fn ($v) => $v !== null)->map(fn ($v) => (float) $v)->values();
                    $pm25Vals = $group->pluck('pm25_predicted')->filter(fn ($v) => $v !== null)->map(fn ($v) => (float) $v)->values();

                    return [
                        'label' => Carbon::parse((string) $date)->format('d/m'),
                        'pm10' => $pm10Vals->count() ? round($pm10Vals->avg(), 2) : null,
                        'pm25' => $pm25Vals->count() ? round($pm25Vals->avg(), 2) : null,
                    ];
                })
                ->values();

            $labels = $daily->pluck('label')->all();
            $pm10Series = $daily->pluck('pm10')->all();
            $pm25Series = $daily->pluck('pm25')->all();
        }

        return response()->json([
            'data' => [
                'active_run' => [
                    'id' => $run->id,
                    'run_code' => $run->run_code,
                    'status' => $run->status,
                ],
                'dates' => $dates,
                'range' => $range,
                'applied' => $applied,
                'pm10' => [
                    'labels' => $labels,
                    'series' => $pm10Series,
                    'stats' => $this->stats($pm10Series),
                ],
                'pm25' => [
                    'labels' => $labels,
                    'series' => $pm25Series,
                    'stats' => $this->stats($pm25Series),
                ],
            ],
        ]);
    }

    private function resolveActiveRun(): ?LstmRun
    {
        $activeRunId = PredictionSetting::query()->value('active_lstm_run_id');
        if (! $activeRunId) {
            return null;
        }

        return LstmRun::query()->find($activeRunId);
    }

    private function getRunDates(int $runId): array
    {
        return $this->forecastQuery($runId)
            ->selectRaw('DATE(predicted_for) as d')
            ->distinct()
            ->orderBy('d')
            ->pluck('d')
            ->map(fn ($d) => (string) $d)
            ->values()
            ->all();
    }

    private function forecastQuery(int $runId)
    {
        return LstmPrediction::query()
            ->where('lstm_run_id', $runId)
            ->whereNull('pm10_actual')
            ->whereNull('pm25_actual');
    }

    private function applyRangeFilter($query, int $runId, string $range, Request $request): array
    {
        $dates = $this->getRunDates($runId);
        $fallback = $dates[0] ?? null;

        if ($range === '7hari') {
            $start = (string) $request->query('date_start', $fallback);
            if (! in_array($start, $dates, true)) {
                $start = $fallback;
            }
            $end = $start ? Carbon::parse($start)->addDays(6)->toDateString() : null;
            if ($start && $end) {
                $query->whereBetween('predicted_for', ["{$start} 00:00:00", "{$end} 23:59:59"]);
            }

            return [[
                'date_start' => $start,
                'date_end' => $end,
            ], $dates];
        }

        if ($range === '30hari') {
            $months = collect($dates)->map(fn (string $d) => substr($d, 0, 7))->unique()->values()->all();
            $month = (string) $request->query('date_month', $months[0] ?? null);
            if (! in_array($month, $months, true)) {
                $month = $months[0] ?? null;
            }
            if ($month) {
                [$year, $mon] = explode('-', $month);
                $query->whereYear('predicted_for', (int) $year)->whereMonth('predicted_for', (int) $mon);
            }

            return [[
                'date_month' => $month,
            ], $dates];
        }

        $single = (string) $request->query('date_single', $fallback);
        if (! in_array($single, $dates, true)) {
            $single = $fallback;
        }
        if ($single) {
            $query->whereDate('predicted_for', $single);
        }

        return [[
            'date_single' => $single,
        ], $dates];
    }

    private function stats(array $values): array
    {
        $numbers = array_values(array_filter($values, static fn ($v) => $v !== null));
        if (count($numbers) === 0) {
            return $this->emptyStats();
        }

        return [
            'avg' => round(array_sum($numbers) / count($numbers), 1),
            'max' => max($numbers),
            'min' => min($numbers),
        ];
    }

    private function emptyStats(): array
    {
        return ['avg' => 0, 'max' => 0, 'min' => 0];
    }
}

