<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessLstmRunJob;
use App\Models\AirQualityActual;
use App\Models\LstmPrediction;
use App\Models\LstmRun;
use App\Models\LstmRunMetric;
use App\Models\LstmRunStep;
use App\Models\PredictionSetting;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LstmRunController extends Controller
{
    public function index(): JsonResponse
    {
        $activeRunId = PredictionSetting::query()->value('active_lstm_run_id');

        $runs = LstmRun::query()
            ->with('metrics')
            ->latest('id')
            ->limit(50)
            ->get()
            ->map(function (LstmRun $run) use ($activeRunId) {
                $start = $run->started_at?->format('d-m-Y H.i') ?? '-';
                $predictionMin = $run->predictions()
                    ->whereNull('pm10_actual')
                    ->whereNull('pm25_actual')
                    ->min('predicted_for');
                $predictionMax = $run->predictions()
                    ->whereNull('pm10_actual')
                    ->whereNull('pm25_actual')
                    ->max('predicted_for');

                $targetDate = '-';
                if ($predictionMin && $predictionMax) {
                    $min = Carbon::parse($predictionMin)->format('d M Y H:i');
                    $max = Carbon::parse($predictionMax)->format('d M Y H:i');
                    $targetDate = $min . ' s.d ' . $max;
                }

                $metricPm10 = $run->metrics->firstWhere('pollutant', 'pm10');
                $metricPm25 = $run->metrics->firstWhere('pollutant', 'pm25');

                return [
                    'id' => $run->id,
                    'run_code' => $run->run_code,
                    'waktu_eksekusi' => $start,
                    'tanggal_prediksi' => $targetDate,
                    'status' => Str::title($run->status),
                    'is_active' => (int) $run->id === (int) $activeRunId,
                    'metrics' => [
                        'pm10' => $metricPm10 ? $this->formatMetric($metricPm10) : null,
                        'pm25' => $metricPm25 ? $this->formatMetric($metricPm25) : null,
                    ],
                ];
            })
            ->values();

        return response()->json([
            'data' => [
                'active_run_id' => $activeRunId,
                'runs' => $runs,
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $traceId = (string) Str::uuid();

        $validated = $request->validate([
            'range' => ['required', 'in:24jam,7hari,30hari'],
            'date_single' => ['nullable', 'date'],
            'date_start' => ['nullable', 'date'],
            'date_end' => ['nullable', 'date'],
            'date_month' => ['nullable', 'regex:/^\d{4}\-\d{2}$/'],
        ]);

        Log::info('lstm.run.store.request', [
            'trace_id' => $traceId,
            'user_id' => $request->user()?->id,
            'payload' => $validated,
        ]);

        $allRows = AirQualityActual::query()
            ->orderBy('observed_at')
            ->get(['observed_at', 'pm10', 'pm25']);

        if ($allRows->isEmpty()) {
            Log::warning('lstm.run.store.empty_dataset', [
                'trace_id' => $traceId,
            ]);
            return response()->json(['message' => 'Data aktual belum tersedia.'], 422);
        }

        $latestObservedAt = Carbon::parse($allRows->last()->observed_at);
        if ($allRows->count() < 100) {
            return response()->json(['message' => 'Data aktual belum cukup untuk proses prediksi.'], 422);
        }

        $year = (int) $latestObservedAt->format('Y');
        $trainStartAt = Carbon::create($year, 1, 1, 0, 0, 0);
        $trainEndAt = Carbon::create($year, 10, 31, 23, 59, 59);
        $testStartAt = Carbon::create($year, 11, 1, 0, 0, 0);
        $testEndAt = Carbon::create($year, 12, 31, 23, 59, 59);

        $forecastStart = $latestObservedAt->copy()->addMonthNoOverflow()->startOfMonth()->startOfDay();
        $forecastEnd = $forecastStart->copy()->endOfMonth()->endOfDay();
        $forecastWindow = [
            'start' => $forecastStart->toDateTimeString(),
            'end' => $forecastEnd->toDateTimeString(),
        ];
        $evalWindow = [
            'start' => $testStartAt->toDateTimeString(),
            'end' => $testEndAt->toDateTimeString(),
        ];

        Log::info('lstm.run.store.dataset_loaded', [
            'trace_id' => $traceId,
            'all_rows' => $allRows->count(),
            'latest_observed_at' => $latestObservedAt->toDateTimeString(),
            'forecast_window' => $forecastWindow,
            'evaluation_window' => $evalWindow,
        ]);

        $lookback = 48;
        $startedAt = now();

        $run = LstmRun::query()->create([
            'run_code' => 'RUN-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(4)),
            'model_variant' => 'bivariate',
            'lookback' => $lookback,
            'horizon' => (int) (($forecastEnd->timestamp - $forecastStart->timestamp) / (30 * 60)) + 1,
            'status' => 'running',
            'train_start_at' => $trainStartAt,
            'train_end_at' => $trainEndAt,
            'test_start_at' => $testStartAt,
            'test_end_at' => $testEndAt,
            'started_at' => $startedAt,
            'executed_by' => $request->user()?->id,
            'preprocessing_summary' => [
                'zero_to_null' => true,
                'interpolation' => 'linear',
                'winsorizing' => 'iqr',
                'lookback' => 48,
                'resolution_minutes' => 30,
                'model_variant' => 'bivariate',
            ],
        ]);

        Log::info('lstm.run.store.run_created', [
            'trace_id' => $traceId,
            'run_id' => $run->id,
            'run_code' => $run->run_code,
        ]);

        ProcessLstmRunJob::dispatch($run->id, $traceId);

        return response()->json([
            'message' => 'Run LSTM masuk antrean dan sedang diproses.',
            'data' => [
                'run_id' => $run->id,
                'run_code' => $run->run_code,
            ],
        ], 202);
    }

    public function show(Request $request, LstmRun $lstmRun): JsonResponse
    {
        $validated = $request->validate([
            'range' => ['nullable', 'in:24jam,7hari,30hari'],
            'date_single' => ['nullable', 'date'],
            'date_start' => ['nullable', 'date'],
            'date_end' => ['nullable', 'date'],
            'date_month' => ['nullable', 'regex:/^\d{4}\-\d{2}$/'],
        ]);

        $range = $validated['range'] ?? '24jam';
        $window = $this->resolveWindow(['range' => $range] + $validated);
        if (! $window) {
            return response()->json(['message' => 'Parameter tanggal tidak valid.'], 422);
        }

        $baseQuery = LstmPrediction::query()
            ->where('lstm_run_id', $lstmRun->id)
            ->where(function ($query) {
                $query->whereNotNull('pm10_actual')
                    ->orWhereNotNull('pm25_actual');
            });

        $predictions = (clone $baseQuery)
            ->whereBetween('predicted_for', [$window['start'], $window['end']])
            ->orderBy('predicted_for')
            ->get();

        if ($predictions->isEmpty()) {
            $latestActualAt = (clone $baseQuery)->max('predicted_for');
            if ($latestActualAt) {
                $fallbackWindow = $this->resolveLatestActualWindow($range, Carbon::parse($latestActualAt));
                $predictions = (clone $baseQuery)
                    ->whereBetween('predicted_for', [$fallbackWindow['start'], $fallbackWindow['end']])
                    ->orderBy('predicted_for')
                    ->get();
                $window = $fallbackWindow;
            }
        }

        $chart = $this->buildChartPayload($predictions, $range);
        $metrics = $lstmRun->metrics()->get()->keyBy('pollutant');
        $steps = $lstmRun->steps()->get()->map(function (LstmRunStep $step) {
            return [
                'step_order' => $step->step_order,
                'step_key' => $step->step_key,
                'status' => $step->status,
                'duration_seconds' => $step->duration_seconds,
                'summary' => $step->summary,
            ];
        })->values();

        return response()->json([
            'data' => [
                'run' => [
                    'id' => $lstmRun->id,
                    'run_code' => $lstmRun->run_code,
                    'status' => $lstmRun->status,
                    'started_at' => $lstmRun->started_at?->toDateTimeString(),
                    'finished_at' => $lstmRun->finished_at?->toDateTimeString(),
                    'lookback' => $lstmRun->lookback,
                    'horizon' => $lstmRun->horizon,
                    'preprocessing_summary' => $lstmRun->preprocessing_summary,
                ],
                'metrics' => [
                    'pm10' => $metrics->has('pm10') ? $this->formatMetric($metrics->get('pm10')) : null,
                    'pm25' => $metrics->has('pm25') ? $this->formatMetric($metrics->get('pm25')) : null,
                ],
                'steps' => $steps,
                'chart' => $chart,
                'window' => $window,
            ],
        ]);
    }

    public function stop(Request $request, LstmRun $lstmRun): JsonResponse
    {
        if ($lstmRun->status !== 'running') {
            return response()->json([
                'message' => 'Run ini sudah tidak dalam proses.',
            ], 422);
        }

        $startedAt = $lstmRun->started_at ?? now();
        $lstmRun->update([
            'status' => 'failed',
            'error_message' => 'Dihentikan oleh admin.',
            'finished_at' => now(),
            'duration_seconds' => (int) $startedAt->diffInSeconds(now()),
        ]);

        Log::warning('lstm.run.stopped_by_admin', [
            'run_id' => $lstmRun->id,
            'run_code' => $lstmRun->run_code,
            'user_id' => $request->user()?->id,
        ]);

        return response()->json([
            'message' => 'Permintaan stop diterima.',
        ]);
    }

    public function activate(Request $request, LstmRun $lstmRun): JsonResponse
    {
        if ($lstmRun->status !== 'success') {
            return response()->json([
                'message' => 'Hanya run dengan status sukses yang bisa diaktifkan.',
            ], 422);
        }

        $setting = PredictionSetting::query()->firstOrCreate(['id' => 1], [
            'active_lstm_run_id' => null,
            'active_model_key' => 'bivariate_30m_lb48_zero2null_interp_winsor',
            'active_model_config' => [
                'model_variant' => 'bivariate',
                'lookback' => 48,
                'resolution_minutes' => 30,
                'preprocessing' => [
                    'zero_to_null' => true,
                    'interpolation' => 'linear',
                    'winsorizing' => 'iqr',
                ],
            ],
            'notes' => 'Model default aktif untuk prediksi publik dan admin.',
        ]);

        $setting->update([
            'active_lstm_run_id' => $lstmRun->id,
            'updated_by' => $request->user()?->id,
        ]);

        return response()->json(['message' => 'Run aktif berhasil diperbarui.']);
    }

    public function destroy(LstmRun $lstmRun): JsonResponse
    {
        $setting = PredictionSetting::query()->first();
        if ($setting && (int) $setting->active_lstm_run_id === (int) $lstmRun->id) {
            $setting->update(['active_lstm_run_id' => null]);
        }

        $lstmRun->delete();
        return response()->json(['message' => 'Run berhasil dihapus.']);
    }

    private function buildEvaluationResult(
        int $runId,
        array $series,
        int $lookback,
        Carbon $trainStart,
        Carbon $trainEnd,
        Carbon $testStart,
        Carbon $testEnd,
        array $preprocessStats = []
    ): array
    {
        $trainRows = array_values(array_filter($series, fn ($row) => $row['observed_at']->betweenIncluded($trainStart, $trainEnd)));
        $testRows = array_values(array_filter($series, fn ($row) => $row['observed_at']->betweenIncluded($testStart, $testEnd)));

        if (count($trainRows) <= $lookback || count($testRows) <= $lookback) {
            throw new \RuntimeException('Data train/test tidak cukup untuk lookback yang dipilih.');
        }

        $pm10Eval = $this->runBivariateEvaluation($trainRows, $testRows, $lookback, ['pm10', 'pm25'], 'pm10');
        $pm25Eval = $this->runBivariateEvaluation($trainRows, $testRows, $lookback, ['pm25', 'pm10'], 'pm25');

        $indexed = [];
        foreach ($pm10Eval['timestamps'] as $idx => $ts) {
            $key = $ts->toDateTimeString();
            $indexed[$key] = [
                'lstm_run_id' => $runId,
                'predicted_for' => $key,
                'horizon_index' => $idx + 1,
                'pm10_actual' => $pm10Eval['actual'][$idx] ?? null,
                'pm10_predicted' => $pm10Eval['predicted'][$idx] ?? null,
                'pm25_actual' => null,
                'pm25_predicted' => null,
                'health_indicator' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        foreach ($pm25Eval['timestamps'] as $idx => $ts) {
            $key = $ts->toDateTimeString();
            if (!isset($indexed[$key])) {
                $indexed[$key] = [
                    'lstm_run_id' => $runId,
                    'predicted_for' => $key,
                    'horizon_index' => count($indexed) + 1,
                    'pm10_actual' => null,
                    'pm10_predicted' => null,
                    'pm25_actual' => null,
                    'pm25_predicted' => null,
                    'health_indicator' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            $indexed[$key]['pm25_actual'] = $pm25Eval['actual'][$idx] ?? null;
            $indexed[$key]['pm25_predicted'] = $pm25Eval['predicted'][$idx] ?? null;
            $indexed[$key]['health_indicator'] = $this->indicatorFromPm25($indexed[$key]['pm25_predicted']);
        }

        ksort($indexed);
        $predictions = array_values($indexed);
        foreach ($predictions as $i => &$row) {
            $row['horizon_index'] = $i + 1;
        }
        unset($row);

        $pm10Metric = $this->computeMetrics($pm10Eval['actual'], $pm10Eval['predicted']);
        $pm25Metric = $this->computeMetrics($pm25Eval['actual'], $pm25Eval['predicted']);

        $stepSummaries = [
            'preprocessing' => [
                'missing_pm10' => $preprocessStats['pm10_null_before_interp'] ?? 0,
                'missing_pm25' => $preprocessStats['pm25_null_before_interp'] ?? 0,
                'zero_pm10_to_null' => $preprocessStats['pm10_zero_to_null'] ?? 0,
                'zero_pm25_to_null' => $preprocessStats['pm25_zero_to_null'] ?? 0,
                'zero_to_null' => true,
                'interpolation' => 'linear',
                'winsorizing' => 'iqr',
                'duration_seconds' => 2,
            ],
            'scaling' => [
                'method' => 'minmax',
                'duration_seconds' => 1,
            ],
            'windowing' => [
                'lookback' => $lookback,
                'horizon' => count($predictions),
                'train_rows' => count($trainRows),
                'test_rows' => count($testRows),
                'duration_seconds' => 1,
            ],
            'training' => [
                'status' => 'Trained',
                'training_loss' => $this->safeRound(($pm10Eval['train_mse'] + $pm25Eval['train_mse']) / 2),
                'validation_loss' => $this->safeRound(($pm10Metric['mse'] + $pm25Metric['mse']) / 2),
                'duration_seconds' => 3,
            ],
            'generate' => [
                'jumlah_titik_prediksi' => count($predictions),
                'timestamp_run' => now()->format('Y-m-d H:i:s'),
                'periode_evaluasi' => $testStart->format('Y-m-d H:i:s') . ' s.d ' . $testEnd->format('Y-m-d H:i:s'),
                'duration_seconds' => 1,
            ],
            'evaluation' => [
                'pm10_r2' => $pm10Metric['r2'],
                'pm25_r2' => $pm25Metric['r2'],
                'duration_seconds' => 1,
            ],
        ];

        return [
            $predictions,
            ['pm10' => $pm10Metric, 'pm25' => $pm25Metric],
            $stepSummaries,
        ];
    }

    private function buildForecastResult(
        int $runId,
        array $series,
        int $lookback,
        Carbon $forecastStart,
        Carbon $forecastEnd
    ): array {
        $modelPm10 = $this->fitBivariateModel($series, $lookback, ['pm10', 'pm25'], 'pm10');
        $modelPm25 = $this->fitBivariateModel($series, $lookback, ['pm25', 'pm10'], 'pm25');

        $history = $series;
        $predictions = [];
        $cursor = $forecastStart->copy();
        $index = 1;

        while ($cursor->lte($forecastEnd)) {
            $predPm10 = $this->predictNextFromHistory($history, $lookback, $modelPm10);
            $predPm25 = $this->predictNextFromHistory($history, $lookback, $modelPm25);

            $predictions[] = [
                'lstm_run_id' => $runId,
                'predicted_for' => $cursor->toDateTimeString(),
                'horizon_index' => $index,
                'pm10_actual' => null,
                'pm10_predicted' => $predPm10,
                'pm25_actual' => null,
                'pm25_predicted' => $predPm25,
                'health_indicator' => $this->indicatorFromPm25($predPm25),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $history[] = [
                'observed_at' => $cursor->copy(),
                'pm10' => $predPm10,
                'pm25' => $predPm25,
            ];

            $cursor->addMinutes(30);
            $index++;
        }

        return $predictions;
    }

    private function prepareModelSeries($allRows): array
    {
        $raw = $allRows->map(function ($row) {
            return [
                'observed_at' => Carbon::parse($row->observed_at),
                'pm10' => $row->pm10 !== null ? (float) $row->pm10 : null,
                'pm25' => $row->pm25 !== null ? (float) $row->pm25 : null,
            ];
        })->values();

        $minTs = $raw->first()['observed_at']->copy()->second(0);
        $maxTs = $raw->last()['observed_at']->copy()->second(0);
        $indexMap = [];
        foreach ($raw as $row) {
            $indexMap[$row['observed_at']->format('Y-m-d H:i:00')] = $row;
        }

        $series = [];
        $cursor = $minTs->copy();
        while ($cursor->lte($maxTs)) {
            $key = $cursor->format('Y-m-d H:i:00');
            $found = $indexMap[$key] ?? null;
            $pm10 = $found ? $found['pm10'] : null;
            $pm25 = $found ? $found['pm25'] : null;
            if ($pm10 === 0.0) {
                $pm10 = null;
            }
            if ($pm25 === 0.0) {
                $pm25 = null;
            }
            $series[] = [
                'observed_at' => $cursor->copy(),
                'pm10' => $pm10,
                'pm25' => $pm25,
            ];
            $cursor->addMinutes(30);
        }

        $pm10Before = count(array_filter(array_column($series, 'pm10'), fn ($v) => $v === null));
        $pm25Before = count(array_filter(array_column($series, 'pm25'), fn ($v) => $v === null));

        $pm10Values = array_column($series, 'pm10');
        $pm25Values = array_column($series, 'pm25');
        $pm10Interpolated = $this->interpolateSeries($pm10Values);
        $pm25Interpolated = $this->interpolateSeries($pm25Values);
        $pm10Winsor = $this->winsorize($pm10Interpolated);
        $pm25Winsor = $this->winsorize($pm25Interpolated);

        foreach ($series as $i => &$row) {
            $row['pm10'] = $this->safeRound((float) $pm10Winsor[$i]);
            $row['pm25'] = $this->safeRound((float) $pm25Winsor[$i]);
        }
        unset($row);

        return [
            'series' => $series,
            'stats' => [
                'pm10_zero_to_null' => count(array_filter(array_column($raw->all(), 'pm10'), fn ($v) => $v === 0.0)),
                'pm25_zero_to_null' => count(array_filter(array_column($raw->all(), 'pm25'), fn ($v) => $v === 0.0)),
                'pm10_null_before_interp' => $pm10Before,
                'pm25_null_before_interp' => $pm25Before,
                'prepared_rows' => count($series),
            ],
        ];
    }

    private function resolveTrainTestSplit(array $series, Carbon $latestObservedAt): array
    {
        $year = (int) $latestObservedAt->format('Y');
        $trainStart = Carbon::create($year, 1, 1, 0, 0, 0);
        $trainEnd = Carbon::create($year, 10, 31, 23, 59, 59);
        $testStart = Carbon::create($year, 11, 1, 0, 0, 0);
        $testEnd = Carbon::create($year, 12, 31, 23, 59, 59);

        $trainCount = count(array_filter($series, fn ($row) => $row['observed_at']->betweenIncluded($trainStart, $trainEnd)));
        $testCount = count(array_filter($series, fn ($row) => $row['observed_at']->betweenIncluded($testStart, $testEnd)));

        if ($trainCount > 100 && $testCount > 100) {
            return [
                'train_start' => $trainStart,
                'train_end' => $trainEnd,
                'test_start' => $testStart,
                'test_end' => $testEnd,
            ];
        }

        $n = count($series);
        $splitIdx = (int) floor($n * 0.8);
        $splitIdx = max(1, min($splitIdx, $n - 1));

        return [
            'train_start' => $series[0]['observed_at']->copy(),
            'train_end' => $series[$splitIdx - 1]['observed_at']->copy(),
            'test_start' => $series[$splitIdx]['observed_at']->copy(),
            'test_end' => $series[$n - 1]['observed_at']->copy(),
        ];
    }

    private function runBivariateEvaluation(
        array $trainRows,
        array $testRows,
        int $lookback,
        array $featureCols,
        string $targetCol
    ): array {
        $model = $this->fitBivariateModel($trainRows, $lookback, $featureCols, $targetCol);
        [$xTest, $yTestScaled, $tsList] = $this->buildSequencesForRows($testRows, $lookback, $featureCols, $targetCol, $model['feature_scaler'], $model['target_scaler']);
        $yPredScaled = $this->predictLinearBatch($xTest, $model['weights']);

        $actual = array_map(fn ($v) => $this->inverseMinMax($v, $model['target_scaler']['min'], $model['target_scaler']['max']), $yTestScaled);
        $predicted = array_map(fn ($v) => $this->inverseMinMax($v, $model['target_scaler']['min'], $model['target_scaler']['max']), $yPredScaled);

        return [
            'actual' => array_map(fn ($v) => $this->safeRound($v), $actual),
            'predicted' => array_map(fn ($v) => $this->safeRound($v), $predicted),
            'timestamps' => $tsList,
            'train_mse' => $model['train_mse'],
        ];
    }

    private function fitBivariateModel(array $rows, int $lookback, array $featureCols, string $targetCol): array
    {
        $featureScalers = [];
        foreach ($featureCols as $col) {
            $vals = array_map(fn ($row) => (float) $row[$col], $rows);
            $featureScalers[$col] = $this->fitMinMax($vals);
        }
        $targetVals = array_map(fn ($row) => (float) $row[$targetCol], $rows);
        $targetScaler = $this->fitMinMax($targetVals);

        [$xTrain, $yTrain] = $this->buildSequencesForRows($rows, $lookback, $featureCols, $targetCol, $featureScalers, $targetScaler);
        if (count($xTrain) === 0) {
            throw new \RuntimeException('Sequence train kosong.');
        }

        $weights = $this->trainLinearModel($xTrain, $yTrain, 120, 0.01, 1e-4);
        $trainPred = $this->predictLinearBatch($xTrain, $weights);
        $trainMse = $this->computeMseRaw($yTrain, $trainPred);

        return [
            'weights' => $weights,
            'feature_scaler' => $featureScalers,
            'target_scaler' => $targetScaler,
            'feature_cols' => $featureCols,
            'target_col' => $targetCol,
            'lookback' => $lookback,
            'train_mse' => $this->safeRound($trainMse),
        ];
    }

    private function buildSequencesForRows(
        array $rows,
        int $lookback,
        array $featureCols,
        string $targetCol,
        array $featureScalers,
        array $targetScaler
    ): array {
        $features = [];
        $target = [];
        $timestamps = [];
        foreach ($rows as $row) {
            $features[] = array_map(function ($col) use ($row, $featureScalers) {
                return $this->normalizeMinMax((float) $row[$col], $featureScalers[$col]['min'], $featureScalers[$col]['max']);
            }, $featureCols);
            $target[] = $this->normalizeMinMax((float) $row[$targetCol], $targetScaler['min'], $targetScaler['max']);
            $timestamps[] = $row['observed_at'];
        }

        $x = [];
        $y = [];
        $tsOut = [];
        $n = count($features);
        for ($i = 0; $i < $n - $lookback; $i++) {
            $window = array_slice($features, $i, $lookback);
            $flat = [];
            foreach ($window as $item) {
                foreach ($item as $val) {
                    $flat[] = $val;
                }
            }
            $x[] = $flat;
            $y[] = $target[$i + $lookback];
            $tsOut[] = $timestamps[$i + $lookback];
        }

        return [$x, $y, $tsOut];
    }

    private function trainLinearModel(array $x, array $y, int $epochs, float $lr, float $l2 = 0.0): array
    {
        $featureCount = count($x[0]);
        $weights = array_fill(0, $featureCount + 1, 0.0);
        $n = count($x);

        for ($epoch = 0; $epoch < $epochs; $epoch++) {
            $grad = array_fill(0, $featureCount + 1, 0.0);

            foreach ($x as $i => $row) {
                $pred = $weights[0];
                foreach ($row as $j => $val) {
                    $pred += $weights[$j + 1] * $val;
                }
                $err = $pred - $y[$i];
                $grad[0] += $err;
                foreach ($row as $j => $val) {
                    $grad[$j + 1] += $err * $val;
                }
            }

            $scale = 2.0 / $n;
            $weights[0] -= $lr * $scale * $grad[0];
            for ($j = 1; $j <= $featureCount; $j++) {
                $regularized = ($scale * $grad[$j]) + ($l2 * $weights[$j]);
                $weights[$j] -= $lr * $regularized;
            }
        }

        return $weights;
    }

    private function predictLinearBatch(array $x, array $weights): array
    {
        return array_map(function ($row) use ($weights) {
            $pred = $weights[0];
            foreach ($row as $j => $val) {
                $pred += $weights[$j + 1] * $val;
            }
            return $pred;
        }, $x);
    }

    private function computeMseRaw(array $actual, array $predicted): float
    {
        $n = min(count($actual), count($predicted));
        if ($n === 0) {
            return 0.0;
        }

        $sum = 0.0;
        for ($i = 0; $i < $n; $i++) {
            $d = $actual[$i] - $predicted[$i];
            $sum += $d * $d;
        }
        return $sum / $n;
    }

    private function predictNextFromHistory(array $history, int $lookback, array $model): ?float
    {
        if (count($history) < $lookback) {
            return null;
        }

        $window = array_slice($history, -$lookback);
        $flat = [];
        foreach ($window as $row) {
            foreach ($model['feature_cols'] as $col) {
                $flat[] = $this->normalizeMinMax((float) $row[$col], $model['feature_scaler'][$col]['min'], $model['feature_scaler'][$col]['max']);
            }
        }

        $predScaled = $this->predictLinearBatch([$flat], $model['weights'])[0] ?? null;
        if ($predScaled === null) {
            return null;
        }

        $pred = $this->inverseMinMax($predScaled, $model['target_scaler']['min'], $model['target_scaler']['max']);
        return $this->safeRound($pred);
    }

    private function fitMinMax(array $values): array
    {
        $min = min($values);
        $max = max($values);
        if ($min === $max) {
            $max = $min + 1.0;
        }
        return ['min' => (float) $min, 'max' => (float) $max];
    }

    private function normalizeMinMax(float $value, float $min, float $max): float
    {
        if ($max <= $min) {
            return 0.0;
        }
        return ($value - $min) / ($max - $min);
    }

    private function inverseMinMax(float $value, float $min, float $max): float
    {
        return ($value * ($max - $min)) + $min;
    }

    private function interpolateSeries(array $values): array
    {
        $n = count($values);
        if ($n === 0) {
            return [];
        }

        $result = $values;
        $known = [];
        for ($i = 0; $i < $n; $i++) {
            if ($result[$i] !== null) {
                $known[] = $i;
            }
        }

        if (count($known) === 0) {
            return array_fill(0, $n, 0.0);
        }

        $first = $known[0];
        for ($i = 0; $i < $first; $i++) {
            $result[$i] = (float) $result[$first];
        }

        for ($k = 0; $k < count($known) - 1; $k++) {
            $left = $known[$k];
            $right = $known[$k + 1];
            $leftVal = (float) $result[$left];
            $rightVal = (float) $result[$right];
            $gap = $right - $left;
            if ($gap <= 1) {
                continue;
            }
            for ($i = $left + 1; $i < $right; $i++) {
                $ratio = ($i - $left) / $gap;
                $result[$i] = $leftVal + (($rightVal - $leftVal) * $ratio);
            }
        }

        $last = $known[count($known) - 1];
        for ($i = $last + 1; $i < $n; $i++) {
            $result[$i] = (float) $result[$last];
        }

        return array_map(fn ($v) => (float) $v, $result);
    }

    private function winsorize(array $values): array
    {
        if (count($values) === 0) {
            return [];
        }
        $q1 = $this->quantile($values, 0.25);
        $q3 = $this->quantile($values, 0.75);
        $iqr = $q3 - $q1;
        $lower = $q1 - (1.5 * $iqr);
        $upper = $q3 + (1.5 * $iqr);

        return array_map(function ($v) use ($lower, $upper) {
            if ($v < $lower) {
                return $lower;
            }
            if ($v > $upper) {
                return $upper;
            }
            return $v;
        }, $values);
    }

    private function quantile(array $values, float $q): float
    {
        sort($values);
        $n = count($values);
        if ($n === 1) {
            return (float) $values[0];
        }
        $pos = ($n - 1) * $q;
        $low = (int) floor($pos);
        $high = (int) ceil($pos);
        if ($low === $high) {
            return (float) $values[$low];
        }
        $weight = $pos - $low;
        return ((1 - $weight) * (float) $values[$low]) + ($weight * (float) $values[$high]);
    }

    private function computeMetrics(array $actual, array $predicted): array
    {
        $n = min(count($actual), count($predicted));
        if ($n === 0) {
            return ['mae' => null, 'mse' => null, 'rmse' => null, 'r2' => null];
        }

        $sumAbs = 0.0;
        $sumSq = 0.0;
        for ($i = 0; $i < $n; $i++) {
            $err = $actual[$i] - $predicted[$i];
            $sumAbs += abs($err);
            $sumSq += $err * $err;
        }

        $mae = $sumAbs / $n;
        $mse = $sumSq / $n;
        $rmse = sqrt($mse);
        $meanActual = array_sum($actual) / $n;
        $ssTot = 0.0;
        for ($i = 0; $i < $n; $i++) {
            $diff = $actual[$i] - $meanActual;
            $ssTot += $diff * $diff;
        }
        $r2 = $ssTot > 0 ? (1 - ($sumSq / $ssTot)) : null;

        return [
            'mae' => $this->safeRound($mae),
            'mse' => $this->safeRound($mse),
            'rmse' => $this->safeRound($rmse),
            'r2' => $r2 !== null ? $this->safeRound($r2) : null,
        ];
    }

    private function resolveWindow(array $payload): ?array
    {
        $range = $payload['range'] ?? '24jam';

        if ($range === '30hari') {
            if (! empty($payload['date_month'])) {
                [$year, $month] = explode('-', $payload['date_month']);
                $start = Carbon::create((int) $year, (int) $month, 1, 0, 0, 0);
            } else {
                $start = Carbon::now()->startOfMonth();
            }
            return [
                'start' => $start->copy()->startOfMonth()->toDateTimeString(),
                'end' => $start->copy()->endOfMonth()->toDateTimeString(),
            ];
        }

        if ($range === '7hari') {
            $start = ! empty($payload['date_start']) ? Carbon::parse($payload['date_start'])->startOfDay() : Carbon::today()->startOfDay();
            $end = ! empty($payload['date_end']) ? Carbon::parse($payload['date_end'])->endOfDay() : $start->copy()->addDays(6)->endOfDay();
            if ($end->lt($start)) {
                return null;
            }
            return [
                'start' => $start->toDateTimeString(),
                'end' => $end->toDateTimeString(),
            ];
        }

        $single = ! empty($payload['date_single']) ? Carbon::parse($payload['date_single']) : Carbon::today();
        return [
            'start' => $single->copy()->startOfDay()->toDateTimeString(),
            'end' => $single->copy()->endOfDay()->toDateTimeString(),
        ];
    }

    private function resolveLatestActualWindow(string $range, Carbon $latestActual): array
    {
        if ($range === '30hari') {
            $start = $latestActual->copy()->startOfMonth()->startOfDay();
            $end = $latestActual->copy()->endOfMonth()->endOfDay();
            return [
                'start' => $start->toDateTimeString(),
                'end' => $end->toDateTimeString(),
            ];
        }

        if ($range === '7hari') {
            $end = $latestActual->copy()->endOfDay();
            $start = $latestActual->copy()->subDays(6)->startOfDay();
            return [
                'start' => $start->toDateTimeString(),
                'end' => $end->toDateTimeString(),
            ];
        }

        $single = $latestActual->copy();
        return [
            'start' => $single->startOfDay()->toDateTimeString(),
            'end' => $single->endOfDay()->toDateTimeString(),
        ];
    }

    private function indicatorFromPm25(?float $value): ?string
    {
        if ($value === null) {
            return null;
        }
        if ($value <= 15.5) {
            return 'Baik';
        }
        if ($value <= 55.4) {
            return 'Sedang';
        }
        if ($value <= 150.4) {
            return 'Tidak Sehat';
        }
        if ($value <= 250.4) {
            return 'Sangat Tidak Sehat';
        }
        return 'Berbahaya';
    }

    private function buildChartPayload($predictions, string $range): array
    {
        $formatLabel = fn (Carbon $date) => $range === '24jam'
            ? $date->format('H:00')
            : $date->format('d M');

        $grouped = [];
        foreach ($predictions as $row) {
            $dt = Carbon::parse($row->predicted_for);
            $groupKey = $range === '24jam'
                ? $dt->format('Y-m-d H:00:00')
                : $dt->format('Y-m-d');
            if (! isset($grouped[$groupKey])) {
                $grouped[$groupKey] = [
                    'label' => $formatLabel($dt),
                    'pm10_actual' => [],
                    'pm10_predicted' => [],
                    'pm25_actual' => [],
                    'pm25_predicted' => [],
                ];
            }
            if ($row->pm10_actual !== null) {
                $grouped[$groupKey]['pm10_actual'][] = (float) $row->pm10_actual;
            }
            if ($row->pm10_predicted !== null) {
                $grouped[$groupKey]['pm10_predicted'][] = (float) $row->pm10_predicted;
            }
            if ($row->pm25_actual !== null) {
                $grouped[$groupKey]['pm25_actual'][] = (float) $row->pm25_actual;
            }
            if ($row->pm25_predicted !== null) {
                $grouped[$groupKey]['pm25_predicted'][] = (float) $row->pm25_predicted;
            }
        }

        ksort($grouped);
        $labels = [];
        $pm10Actual = [];
        $pm10Pred = [];
        $pm25Actual = [];
        $pm25Pred = [];
        foreach ($grouped as $item) {
            $labels[] = $item['label'];
            $pm10Actual[] = $this->avg($item['pm10_actual']);
            $pm10Pred[] = $this->avg($item['pm10_predicted']);
            $pm25Actual[] = $this->avg($item['pm25_actual']);
            $pm25Pred[] = $this->avg($item['pm25_predicted']);
        }

        return [
            'labels' => $labels,
            'pm10' => ['actual' => $pm10Actual, 'predicted' => $pm10Pred],
            'pm25' => ['actual' => $pm25Actual, 'predicted' => $pm25Pred],
        ];
    }

    private function avg(array $values): ?float
    {
        if (count($values) === 0) {
            return null;
        }

        return $this->safeRound(array_sum($values) / count($values));
    }

    private function safeRound(?float $value): ?float
    {
        return $value === null ? null : round($value, 4);
    }

    private function formatMetric(LstmRunMetric $metric): array
    {
        return [
            'mae' => $metric->mae,
            'mse' => $metric->mse,
            'rmse' => $metric->rmse,
            'r2' => $metric->r2,
        ];
    }
}
