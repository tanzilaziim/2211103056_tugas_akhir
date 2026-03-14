<?php

namespace App\Services;

use App\Models\AirQualityActual;
use App\Models\LstmPrediction;
use App\Models\LstmRun;
use App\Models\LstmRunMetric;
use App\Models\LstmRunStep;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class LstmPythonRunner
{
    public function process(int $runId, string $traceId): void
    {
        $run = LstmRun::query()->find($runId);
        if (! $run) {
            return;
        }

        $startedAt = $run->started_at ?? now();

        try {
            $rows = AirQualityActual::query()
                ->orderBy('observed_at')
                ->get(['observed_at', 'pm10', 'pm25']);

            if ($rows->isEmpty()) {
                throw new \RuntimeException('Data aktual tidak tersedia.');
            }

            $payload = $this->buildInputPayload($run, $rows);
            [$stdout, $stderr, $code, $outputPath] = $this->runPython($run, $payload);

            if ($code !== 0) {
                throw new \RuntimeException('Runner python gagal. ' . trim($stderr ?: $stdout));
            }

            if (! File::exists($outputPath)) {
                throw new \RuntimeException('Output runner tidak ditemukan.');
            }

            $outputRaw = File::get($outputPath);
            $output = json_decode($outputRaw, true);
            if (! is_array($output)) {
                throw new \RuntimeException('Output runner bukan JSON valid.');
            }

            $this->persistResult($run, $output, $startedAt);

            Log::info('lstm.run.job.success', [
                'trace_id' => $traceId,
                'run_id' => $run->id,
                'run_code' => $run->run_code,
            ]);
        } catch (\Throwable $e) {
            $run->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'finished_at' => now(),
                'duration_seconds' => (int) $startedAt->diffInSeconds(now()),
            ]);

            Log::error('lstm.run.job.failed', [
                'trace_id' => $traceId,
                'run_id' => $run->id,
                'run_code' => $run->run_code,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
        }
    }

    private function buildInputPayload(LstmRun $run, $rows): array
    {
        $latestObservedAt = Carbon::parse($rows->last()->observed_at);
        $forecastStart = $latestObservedAt->copy()->addMonthNoOverflow()->startOfMonth()->startOfDay();
        $forecastEnd = $forecastStart->copy()->endOfMonth()->endOfDay();

        $year = (int) $latestObservedAt->format('Y');
        $split = [
            'train_start' => Carbon::create($year, 1, 1, 0, 0, 0)->toDateTimeString(),
            'train_end' => Carbon::create($year, 10, 31, 23, 59, 59)->toDateTimeString(),
            'test_start' => Carbon::create($year, 11, 1, 0, 0, 0)->toDateTimeString(),
            'test_end' => Carbon::create($year, 12, 31, 23, 59, 59)->toDateTimeString(),
        ];

        return [
            'run_id' => $run->id,
            'run_code' => $run->run_code,
            'lookback' => (int) ($run->lookback ?: 48),
            'forecast' => [
                'start' => $forecastStart->toDateTimeString(),
                'end' => $forecastEnd->toDateTimeString(),
            ],
            'split' => $split,
            'rows' => $rows->map(fn ($row) => [
                'datetime' => Carbon::parse($row->observed_at)->toDateTimeString(),
                'pm10' => $row->pm10 !== null ? (float) $row->pm10 : null,
                'pm25' => $row->pm25 !== null ? (float) $row->pm25 : null,
            ])->values()->all(),
        ];
    }

    private function runPython(LstmRun $run, array $payload): array
    {
        $pythonBin = (string) (
            config('services.lstm.python_bin')
            ?: env('LSTM_PYTHON_BIN', '')
            ?: ($_ENV['LSTM_PYTHON_BIN'] ?? '')
            ?: getenv('LSTM_PYTHON_BIN')
        );
        $runnerPath = (string) (
            config('services.lstm.runner_path')
            ?: env('LSTM_RUNNER_PATH', 'scripts/lstm_runner.py')
            ?: ($_ENV['LSTM_RUNNER_PATH'] ?? 'scripts/lstm_runner.py')
            ?: getenv('LSTM_RUNNER_PATH')
        );

        $pythonBin = trim($pythonBin);
        $runnerPath = trim($runnerPath);

        Log::info('lstm.run.job.env_check', [
            'run_id' => $run->id,
            'python_bin_raw' => $pythonBin,
            'runner_path_raw' => $runnerPath,
            'config_cached' => app()->configurationIsCached(),
        ]);

        if (! $pythonBin || ! $runnerPath) {
            throw new \RuntimeException('Konfigurasi LSTM_PYTHON_BIN atau LSTM_RUNNER_PATH belum diatur.');
        }

        if (! preg_match('/^[A-Za-z]:[\\\\\\/]|^\//', $pythonBin)) {
            $relative = ltrim(str_replace('/', DIRECTORY_SEPARATOR, $pythonBin), '.\\/');
            $candidateBase = base_path($pythonBin);
            if (File::exists($candidateBase)) {
                $pythonBin = $candidateBase;
            } else {
                $userHome = (string) (getenv('USERPROFILE') ?: '');
                if ($userHome !== '') {
                    $candidateHome = rtrim($userHome, '\\/') . DIRECTORY_SEPARATOR . $relative;
                    if (File::exists($candidateHome)) {
                        $pythonBin = $candidateHome;
                    } else {
                        $pythonBin = $candidateBase;
                    }
                } else {
                    $pythonBin = $candidateBase;
                }
            }
        }
        $runnerAbsolute = base_path($runnerPath);

        Log::info('lstm.run.job.path_check', [
            'run_id' => $run->id,
            'python_bin' => $pythonBin,
            'python_exists' => File::exists($pythonBin),
            'runner_absolute' => $runnerAbsolute,
            'runner_exists' => File::exists($runnerAbsolute),
        ]);

        if (! File::exists($pythonBin)) {
            throw new \RuntimeException('Binary python tidak ditemukan: ' . $pythonBin);
        }
        if (! File::exists($runnerAbsolute)) {
            throw new \RuntimeException('File runner python tidak ditemukan: ' . $runnerAbsolute);
        }

        $dir = storage_path('app/lstm/runs/' . $run->id);
        File::ensureDirectoryExists($dir);
        $inputPath = $dir . DIRECTORY_SEPARATOR . 'input.json';
        $outputPath = $dir . DIRECTORY_SEPARATOR . 'output.json';
        File::put($inputPath, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        $command = '"' . $pythonBin . '" "' . $runnerAbsolute . '" --input "' . $inputPath . '" --output "' . $outputPath . '"';
        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];
        $process = proc_open($command, $descriptors, $pipes, base_path());
        if (! is_resource($process)) {
            throw new \RuntimeException('Gagal menjalankan proses python.');
        }

        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]) ?: '';
        $stderr = stream_get_contents($pipes[2]) ?: '';
        fclose($pipes[1]);
        fclose($pipes[2]);
        $code = proc_close($process);

        Log::info('lstm.run.job.python', [
            'run_id' => $run->id,
            'code' => $code,
            'stdout' => mb_substr($stdout, 0, 5000),
            'stderr' => mb_substr($stderr, 0, 5000),
        ]);

        return [$stdout, $stderr, $code, $outputPath];
    }

    private function persistResult(LstmRun $run, array $output, Carbon $startedAt): void
    {
        $metrics = $output['metrics'] ?? [];
        $predictions = $output['predictions'] ?? [];
        $steps = $output['steps'] ?? [];

        if (! is_array($metrics) || ! is_array($predictions)) {
            throw new \RuntimeException('Output runner tidak memiliki struktur metrics/predictions yang valid.');
        }

        DB::transaction(function () use ($run, $metrics, $predictions, $steps, $startedAt): void {
            LstmPrediction::query()->where('lstm_run_id', $run->id)->delete();
            LstmRunMetric::query()->where('lstm_run_id', $run->id)->delete();
            LstmRunStep::query()->where('lstm_run_id', $run->id)->delete();

            $predictionRows = [];
            foreach ($predictions as $row) {
                $predictionRows[] = [
                    'lstm_run_id' => $run->id,
                    'predicted_for' => Carbon::parse($row['predicted_for'])->toDateTimeString(),
                    'horizon_index' => (int) ($row['horizon_index'] ?? 0) ?: null,
                    'pm10_actual' => $this->toNullableFloat($row['pm10_actual'] ?? null),
                    'pm10_predicted' => $this->toNullableFloat($row['pm10_predicted'] ?? null),
                    'pm25_actual' => $this->toNullableFloat($row['pm25_actual'] ?? null),
                    'pm25_predicted' => $this->toNullableFloat($row['pm25_predicted'] ?? null),
                    'health_indicator' => $row['health_indicator'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            foreach (array_chunk($predictionRows, 500) as $chunk) {
                LstmPrediction::query()->insert($chunk);
            }

            $pm10Metric = $metrics['pm10'] ?? [];
            $pm25Metric = $metrics['pm25'] ?? [];
            LstmRunMetric::query()->insert([
                [
                    'lstm_run_id' => $run->id,
                    'pollutant' => 'pm10',
                    'mae' => $this->toNullableFloat($pm10Metric['mae'] ?? null),
                    'mse' => $this->toNullableFloat($pm10Metric['mse'] ?? null),
                    'rmse' => $this->toNullableFloat($pm10Metric['rmse'] ?? null),
                    'r2' => $this->toNullableFloat($pm10Metric['r2'] ?? null),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'lstm_run_id' => $run->id,
                    'pollutant' => 'pm25',
                    'mae' => $this->toNullableFloat($pm25Metric['mae'] ?? null),
                    'mse' => $this->toNullableFloat($pm25Metric['mse'] ?? null),
                    'rmse' => $this->toNullableFloat($pm25Metric['rmse'] ?? null),
                    'r2' => $this->toNullableFloat($pm25Metric['r2'] ?? null),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);

            $defaultKeys = ['preprocessing', 'scaling', 'windowing', 'training', 'generate', 'evaluation'];
            $stepRows = [];
            if (is_array($steps) && count($steps) > 0) {
                foreach ($steps as $idx => $step) {
                    $stepRows[] = [
                        'lstm_run_id' => $run->id,
                        'step_order' => (int) ($step['step_order'] ?? ($idx + 1)),
                        'step_key' => (string) ($step['step_key'] ?? $defaultKeys[$idx] ?? ('step_' . ($idx + 1))),
                        'status' => (string) ($step['status'] ?? 'success'),
                        'duration_seconds' => (int) ($step['duration_seconds'] ?? 1),
                        'summary' => json_encode($step['summary'] ?? [], JSON_UNESCAPED_UNICODE),
                        'error_message' => null,
                        'started_at' => $startedAt,
                        'finished_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            } else {
                foreach ($defaultKeys as $idx => $key) {
                    $stepRows[] = [
                        'lstm_run_id' => $run->id,
                        'step_order' => $idx + 1,
                        'step_key' => $key,
                        'status' => 'success',
                        'duration_seconds' => 1,
                        'summary' => json_encode([], JSON_UNESCAPED_UNICODE),
                        'error_message' => null,
                        'started_at' => $startedAt,
                        'finished_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            LstmRunStep::query()->insert($stepRows);

            $finishedAt = now();
            $run->update([
                'status' => 'success',
                'error_message' => null,
                'finished_at' => $finishedAt,
                'duration_seconds' => (int) $startedAt->diffInSeconds($finishedAt),
            ]);
        });
    }

    private function toNullableFloat($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        return round((float) $value, 4);
    }
}
