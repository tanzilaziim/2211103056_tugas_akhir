<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AirQualityActual extends Model
{
    protected $fillable = [
        'observed_at',
        'pm10',
        'pm25',
        'data_import_id',
    ];

    protected function casts(): array
    {
        return [
            'observed_at' => 'datetime',
            'pm10' => 'decimal:4',
            'pm25' => 'decimal:4',
        ];
    }

    public function dataImport(): BelongsTo
    {
        return $this->belongsTo(DataImport::class, 'data_import_id');
    }
}

