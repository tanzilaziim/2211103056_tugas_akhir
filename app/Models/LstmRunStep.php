<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LstmRunStep extends Model
{
    protected $fillable = [
        'lstm_run_id',
        'step_order',
        'step_key',
        'status',
        'duration_seconds',
        'summary',
        'error_message',
        'started_at',
        'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'step_order' => 'integer',
            'duration_seconds' => 'integer',
            'summary' => 'array',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function run(): BelongsTo
    {
        return $this->belongsTo(LstmRun::class, 'lstm_run_id');
    }
}

