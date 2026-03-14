<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LstmPrediction extends Model
{
    protected $fillable = [
        'lstm_run_id',
        'predicted_for',
        'horizon_index',
        'pm10_actual',
        'pm10_predicted',
        'pm25_actual',
        'pm25_predicted',
        'health_indicator',
    ];

    protected function casts(): array
    {
        return [
            'predicted_for' => 'datetime',
            'horizon_index' => 'integer',
            'pm10_actual' => 'float',
            'pm10_predicted' => 'float',
            'pm25_actual' => 'float',
            'pm25_predicted' => 'float',
        ];
    }

    public function run(): BelongsTo
    {
        return $this->belongsTo(LstmRun::class, 'lstm_run_id');
    }
}

