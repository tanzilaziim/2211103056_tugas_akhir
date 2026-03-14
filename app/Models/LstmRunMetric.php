<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LstmRunMetric extends Model
{
    protected $fillable = [
        'lstm_run_id',
        'pollutant',
        'mae',
        'mse',
        'rmse',
        'r2',
    ];

    protected function casts(): array
    {
        return [
            'mae' => 'float',
            'mse' => 'float',
            'rmse' => 'float',
            'r2' => 'float',
        ];
    }

    public function run(): BelongsTo
    {
        return $this->belongsTo(LstmRun::class, 'lstm_run_id');
    }
}

