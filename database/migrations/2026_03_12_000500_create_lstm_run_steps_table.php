<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lstm_run_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lstm_run_id')->constrained('lstm_runs')->cascadeOnDelete();
            $table->unsignedTinyInteger('step_order');
            $table->enum('step_key', ['preprocessing', 'scaling', 'windowing', 'training', 'generate', 'evaluation']);
            $table->enum('status', ['pending', 'running', 'success', 'failed'])->default('pending');
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->json('summary')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->unique(['lstm_run_id', 'step_key']);
            $table->index(['lstm_run_id', 'step_order']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('lstm_run_steps');
    }
};

