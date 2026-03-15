<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LstmPrediction;
use App\Models\LstmRun;
use App\Models\PredictionSetting;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PredictionController extends Controller
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
                'message' => 'Belum ada run aktif.',
                'data' => ['rows' => [], 'active_run' => null],
            ]);
        }

        $range = (string) $request->query('range', '24jam');
        $query = $this->forecastQuery($run->id)
            ->orderBy('predicted_for');

        [$applied, $dates] = $this->applyRangeFilter($query, $run->id, $range, $request);
        $rows = $query->get(['predicted_for', 'pm10_predicted', 'pm25_predicted'])
            ->map(function (LstmPrediction $row) {
                $pm10 = $row->pm10_predicted !== null ? (float) $row->pm10_predicted : null;
                $pm25 = $row->pm25_predicted !== null ? (float) $row->pm25_predicted : null;

                return [
                    'waktu' => $row->predicted_for?->format('Y-m-d H:i:s'),
                    'jam' => $row->predicted_for?->format('H:i'),
                    'pm10' => $pm10,
                    'pm25' => $pm25,
                    'indikator' => $this->indicatorLabel($pm25),
                ];
            })
            ->values()
            ->all();

        return response()->json([
            'data' => [
                'active_run' => [
                    'id' => $run->id,
                    'run_code' => $run->run_code,
                    'status' => $run->status,
                ],
                'range' => $range,
                'applied' => $applied,
                'dates' => $dates,
                'rows' => $rows,
            ],
        ]);
    }

    public function chart(Request $request): JsonResponse
    {
        $run = $this->resolveActiveRun();
        if (! $run) {
            return response()->json([
                'message' => 'Belum ada run aktif.',
                'data' => [
                    'active_run' => null,
                    'selected_date' => null,
                    'dates' => [],
                    'pm10' => ['labels' => [], 'series' => [], 'stats' => $this->emptyStats()],
                    'pm25' => ['labels' => [], 'series' => [], 'stats' => $this->emptyStats()],
                ],
            ]);
        }

        $range = (string) $request->query('range', '24jam');
        $dates = $this->getRunDates($run->id);

        $baseQuery = $this->forecastQuery($run->id);
        [$applied] = $this->applyRangeFilter($baseQuery, $run->id, $range, $request);
        $rows = $baseQuery->orderBy('predicted_for')->get(['predicted_for', 'pm10_predicted', 'pm25_predicted']);

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
                    $pm10Avg = $pm10Vals->count() ? round($pm10Vals->avg(), 2) : null;
                    $pm25Avg = $pm25Vals->count() ? round($pm25Vals->avg(), 2) : null;

                    return [
                        'label' => Carbon::parse((string) $date)->format('d/m'),
                        'pm10' => $pm10Avg,
                        'pm25' => $pm25Avg,
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
                'range' => $range,
                'applied' => $applied,
                'selected_date' => $applied['date_single'] ?? null,
                'dates' => $dates,
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

    private function indicatorLabel(?float $pm25): string
    {
        if ($pm25 === null) return '-';
        if ($pm25 <= 15.5) return 'Baik';
        if ($pm25 <= 55.4) return 'Sedang';
        if ($pm25 <= 150.4) return 'Tidak Sehat';
        if ($pm25 <= 250.4) return 'Sangat Tidak Sehat';

        return 'Berbahaya';
    }

    private function stats(array $values): array
    {
        $numbers = array_values(array_filter($values, static fn ($v) => $v !== null));
        if (count($numbers) === 0) {
            return $this->emptyStats();
        }

        $avg = array_sum($numbers) / count($numbers);

        return [
            'avg' => round($avg, 1),
            'max' => max($numbers),
            'min' => min($numbers),
        ];
    }

    private function emptyStats(): array
    {
        return ['avg' => 0, 'max' => 0, 'min' => 0];
    }
}
