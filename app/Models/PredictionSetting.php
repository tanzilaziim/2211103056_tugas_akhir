<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PredictionSetting extends Model
{
    protected $fillable = [
        'active_lstm_run_id',
        'active_model_key',
        'active_model_config',
        'notes',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'active_model_config' => 'array',
        ];
    }

    public function activeRun(): BelongsTo
    {
        return $this->belongsTo(LstmRun::class, 'active_lstm_run_id');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}

