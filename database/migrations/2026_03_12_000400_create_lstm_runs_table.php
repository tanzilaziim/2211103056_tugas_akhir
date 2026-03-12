<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lstm_runs', function (Blueprint $table) {
            $table->id();
            $table->string('run_code')->unique();
            $table->enum('model_variant', ['bivariate'])->default('bivariate');
            $table->unsignedSmallInteger('lookback')->default(48);
            $table->unsignedSmallInteger('horizon')->default(24);
            $table->enum('status', ['pending', 'running', 'success', 'failed'])->default('pending');
            $table->timestamp('train_start_at')->nullable();
            $table->timestamp('train_end_at')->nullable();
            $table->timestamp('test_start_at')->nullable();
            $table->timestamp('test_end_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->json('preprocessing_summary')->nullable();
            $table->text('error_message')->nullable();
            $table->foreignId('executed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('lstm_runs');
    }
};
