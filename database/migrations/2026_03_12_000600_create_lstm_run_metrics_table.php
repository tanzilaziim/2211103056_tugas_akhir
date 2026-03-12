<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lstm_run_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lstm_run_id')->constrained('lstm_runs')->cascadeOnDelete();
            $table->enum('pollutant', ['pm10', 'pm25']);
            $table->decimal('mae', 14, 6)->nullable();
            $table->decimal('mse', 14, 6)->nullable();
            $table->decimal('rmse', 14, 6)->nullable();
            $table->decimal('r2', 14, 6)->nullable();
            $table->timestamps();

            $table->unique(['lstm_run_id', 'pollutant']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('lstm_run_metrics');
    }
};

