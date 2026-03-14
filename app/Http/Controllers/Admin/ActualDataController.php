<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AirQualityActual;
use App\Models\DataImport;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ActualDataController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $dates = AirQualityActual::query()
            ->selectRaw('DATE(observed_at) as d')
            ->distinct()
            ->orderBy('d')
            ->pluck('d')
            ->map(fn ($d) => (string) $d)
            ->values()
            ->all();

        $selectedDate = $request->query('date');
        if (! is_string($selectedDate) || ! in_array($selectedDate, $dates, true)) {
            $selectedDate = $dates[0] ?? null;
        }

        $rows = [];
        if ($selectedDate) {
            $rows = AirQualityActual::query()
                ->whereDate('observed_at', $selectedDate)
                ->orderBy('observed_at')
                ->get(['observed_at', 'pm10', 'pm25'])
                ->map(fn (AirQualityActual $row) => [
                    'waktu' => $row->observed_at?->format('Y-m-d H:i:s'),
                    'pm10' => $row->pm10 !== null ? (float) $row->pm10 : null,
                    'pm25' => $row->pm25 !== null ? (float) $row->pm25 : null,
                ])
                ->values()
                ->all();
        }

        return response()->json([
            'data' => [
                'dates' => $dates,
                'selected_date' => $selectedDate,
                'rows' => $rows,
            ],
        ]);
    }

    public function import(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:20480'],
        ]);

        /** @var UploadedFile $file */
        $file = $validated['file'];
        $originalName = $file->getClientOriginalName();
        $storedName = now()->format('Ymd_His') . '_' . Str::random(8) . '.csv';
        $storagePath = $file->storeAs('imports/actual-data', $storedName);
        if (! $storagePath) {
            return response()->json(['message' => 'Gagal menyimpan file upload.'], 500);
        }

        $import = DataImport::query()->create([
            'import_code' => 'IMP-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(5)),
            'original_name' => $originalName,
            'stored_name' => $storedName,
            'storage_path' => $storagePath,
            'file_type' => 'csv',
            'file_size_bytes' => $file->getSize(),
            'checksum' => hash_file('sha256', $file->getRealPath()),
            'status' => 'uploaded',
            'imported_by' => $request->user()?->id,
        ]);

        try {
            [$upsertRows, $meta] = $this->parseCsvFile($file, $import->id);

            if (empty($upsertRows)) {
                $import->update([
                    'status' => 'failed',
                    'notes' => 'File tidak memiliki baris data yang valid.',
                ]);

                return response()->json(['message' => 'File tidak memiliki baris data yang valid.'], 422);
            }

            DB::transaction(function () use ($upsertRows, $import, $meta): void {
                foreach (array_chunk($upsertRows, 500) as $chunk) {
                    AirQualityActual::query()->upsert(
                        $chunk,
                        ['observed_at'],
                        ['pm10', 'pm25', 'data_import_id', 'updated_at']
                    );
                }

                $import->update([
                    'status' => 'processed',
                    'row_count' => count($upsertRows),
                    'period_start' => $meta['period_start'],
                    'period_end' => $meta['period_end'],
                    'notes' => json_encode([
                        'duplicate_in_file' => $meta['duplicate_in_file'],
                        'updated_existing' => $meta['updated_existing'],
                        'invalid_rows' => $meta['invalid_rows'],
                        'pm10_zero_to_null' => $meta['pm10_zero_to_null'],
                        'pm25_zero_to_null' => $meta['pm25_zero_to_null'],
                    ], JSON_UNESCAPED_UNICODE),
                ]);
            });

            $payload = $this->buildDatasetPayload($meta['selected_date']);

            return response()->json([
                'message' => 'File berhasil diunggah dan diproses.',
                'data' => [
                    'import' => [
                        'id' => $import->id,
                        'import_code' => $import->import_code,
                        'original_name' => $import->original_name,
                        'row_count' => count($upsertRows),
                        'period_start' => $meta['period_start'],
                        'period_end' => $meta['period_end'],
                        'duplicate_in_file' => $meta['duplicate_in_file'],
                        'updated_existing' => $meta['updated_existing'],
                        'invalid_rows' => $meta['invalid_rows'],
                        'pm10_zero_to_null' => $meta['pm10_zero_to_null'],
                        'pm25_zero_to_null' => $meta['pm25_zero_to_null'],
                    ],
                    ...$payload,
                ],
            ]);
        } catch (\Throwable $e) {
            $import->update([
                'status' => 'failed',
                'notes' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Gagal memproses file: ' . $e->getMessage(),
            ], 422);
        }
    }

    private function parseCsvFile(UploadedFile $file, int $importId): array
    {
        $handle = fopen($file->getRealPath(), 'r');
        if (! $handle) {
            throw new \RuntimeException('File tidak dapat dibaca.');
        }

        $header = fgetcsv($handle);
        if (! is_array($header)) {
            fclose($handle);
            throw new \RuntimeException('Header CSV tidak valid.');
        }

        $normalized = array_map(function ($cell) {
            $value = strtolower(trim((string) $cell));
            return str_replace([' ', '_', '-', '.'], '', $value);
        }, $header);

        $timeIdx = $this->findHeaderIndex($normalized, ['datetime', 'waktu', 'timestamp']);
        $pm10Idx = $this->findHeaderIndex($normalized, ['pm10']);
        $pm25Idx = $this->findHeaderIndex($normalized, ['pm25', 'pm2.5', 'pm2,5', 'pm2_5', 'pm25ugm3']);

        if ($timeIdx === null || $pm10Idx === null || $pm25Idx === null) {
            fclose($handle);
            throw new \RuntimeException('Kolom wajib tidak ditemukan. Gunakan kolom datetime/waktu, pm10, pm25/pm2.5.');
        }

        $rows = [];
        $seen = [];
        $duplicateInFile = 0;
        $invalidRows = 0;
        $pm10ZeroToNull = 0;
        $pm25ZeroToNull = 0;
        $timestamps = [];

        while (($cols = fgetcsv($handle)) !== false) {
            $timeRaw = trim((string) ($cols[$timeIdx] ?? ''));
            if ($timeRaw === '') {
                $invalidRows++;
                continue;
            }

            $observedAt = $this->parseDateTime($timeRaw);
            if (! $observedAt) {
                $invalidRows++;
                continue;
            }

            [$pm10, $pm10WasZero] = $this->normalizePollutantValue($cols[$pm10Idx] ?? null);
            [$pm25, $pm25WasZero] = $this->normalizePollutantValue($cols[$pm25Idx] ?? null);
            if ($pm10WasZero) {
                $pm10ZeroToNull++;
            }
            if ($pm25WasZero) {
                $pm25ZeroToNull++;
            }
            $key = $observedAt->format('Y-m-d H:i:s');

            if (isset($seen[$key])) {
                $duplicateInFile++;
            }
            $seen[$key] = true;

            $rows[$key] = [
                'observed_at' => $key,
                'pm10' => $pm10,
                'pm25' => $pm25,
                'data_import_id' => $importId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $timestamps[] = $key;
        }

        fclose($handle);

        $timestamps = array_values(array_unique($timestamps));
        $updatedExisting = 0;
        foreach (array_chunk($timestamps, 500) as $chunk) {
            $updatedExisting += AirQualityActual::query()
                ->whereIn('observed_at', $chunk)
                ->count();
        }

        $keys = array_keys($rows);
        sort($keys);

        return [
            array_values($rows),
            [
                'period_start' => $keys[0] ?? null,
                'period_end' => $keys[count($keys) - 1] ?? null,
                'duplicate_in_file' => $duplicateInFile,
                'updated_existing' => $updatedExisting,
                'invalid_rows' => $invalidRows,
                'pm10_zero_to_null' => $pm10ZeroToNull,
                'pm25_zero_to_null' => $pm25ZeroToNull,
                'selected_date' => isset($keys[0]) ? substr($keys[0], 0, 10) : null,
            ],
        ];
    }

    private function findHeaderIndex(array $header, array $candidates): ?int
    {
        foreach ($header as $idx => $name) {
            if (in_array($name, $candidates, true)) {
                return $idx;
            }
        }

        return null;
    }

    private function parseDateTime(string $value): ?Carbon
    {
        $formats = ['Y-m-d H:i:s', 'Y-m-d H:i', 'd-m-Y H:i:s', 'd-m-Y H:i', 'Y/m/d H:i:s', 'Y/m/d H:i'];
        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $value);
            } catch (\Throwable) {
            }
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    private function normalizePollutantValue(mixed $value): array
    {
        $parsed = $this->toNullableFloat($value, false);
        if ($parsed === 0.0) {
            return [null, true];
        }

        return [$parsed, false];
    }

    private function toNullableFloat(mixed $value, bool $zeroAsNull = false): ?float
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim((string) $value);
        if ($normalized === '') {
            return null;
        }

        $normalized = str_replace(',', '.', $normalized);
        if (! is_numeric($normalized)) {
            return null;
        }

        $number = (float) $normalized;
        if ($zeroAsNull && $number === 0.0) {
            return null;
        }

        return $number;
    }

    private function buildDatasetPayload(?string $preferredDate = null): array
    {
        $dates = AirQualityActual::query()
            ->selectRaw('DATE(observed_at) as d')
            ->distinct()
            ->orderBy('d')
            ->pluck('d')
            ->map(fn ($d) => (string) $d)
            ->values()
            ->all();

        $selectedDate = $preferredDate && in_array($preferredDate, $dates, true)
            ? $preferredDate
            : ($dates[0] ?? null);

        $rows = [];
        if ($selectedDate) {
            $rows = AirQualityActual::query()
                ->whereDate('observed_at', $selectedDate)
                ->orderBy('observed_at')
                ->get(['observed_at', 'pm10', 'pm25'])
                ->map(fn (AirQualityActual $row) => [
                    'waktu' => $row->observed_at?->format('Y-m-d H:i:s'),
                    'pm10' => $row->pm10 !== null ? (float) $row->pm10 : null,
                    'pm25' => $row->pm25 !== null ? (float) $row->pm25 : null,
                ])
                ->values()
                ->all();
        }

        return [
            'dates' => $dates,
            'selected_date' => $selectedDate,
            'rows' => $rows,
        ];
    }
}
