<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DataImport extends Model
{
    protected $fillable = [
        'import_code',
        'original_name',
        'stored_name',
        'storage_path',
        'file_type',
        'file_size_bytes',
        'checksum',
        'status',
        'row_count',
        'period_start',
        'period_end',
        'notes',
        'imported_by',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'datetime',
            'period_end' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'imported_by');
    }

    public function actuals(): HasMany
    {
        return $this->hasMany(AirQualityActual::class, 'data_import_id');
    }
}

