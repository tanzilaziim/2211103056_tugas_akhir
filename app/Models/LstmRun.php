<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LstmRun extends Model
{
    protected $fillable = [
        'run_code',
        'model_variant',
        'lookback',
        'horizon',
        'status',
        'train_start_at',
        'train_end_at',
        'test_start_at',
        'test_end_at',
        'started_at',
        'finished_at',
        'duration_seconds',
        'preprocessing_summary',
        'error_message',
        'executed_by',
    ];

    protected function casts(): array
    {
        return [
            'lookback' => 'integer',
            'horizon' => 'integer',
            'duration_seconds' => 'integer',
            'train_start_at' => 'datetime',
            'train_end_at' => 'datetime',
            'test_start_at' => 'datetime',
            'test_end_at' => 'datetime',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
            'preprocessing_summary' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'executed_by');
    }

    public function metrics(): HasMany
    {
        return $this->hasMany(LstmRunMetric::class, 'lstm_run_id');
    }

    public function steps(): HasMany
    {
        return $this->hasMany(LstmRunStep::class, 'lstm_run_id')->orderBy('step_order');
    }

    public function predictions(): HasMany
    {
        return $this->hasMany(LstmPrediction::class, 'lstm_run_id')->orderBy('predicted_for');
    }
}

