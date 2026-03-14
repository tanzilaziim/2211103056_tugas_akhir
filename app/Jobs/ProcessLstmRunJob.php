<?php

namespace App\Jobs;

use App\Services\LstmPythonRunner;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessLstmRunJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 0;

    public int $tries = 1;

    public function __construct(
        public readonly int $runId,
        public readonly string $traceId,
    ) {}

    public function handle(LstmPythonRunner $runner): void
    {
        $runner->process($this->runId, $this->traceId);
    }
}

