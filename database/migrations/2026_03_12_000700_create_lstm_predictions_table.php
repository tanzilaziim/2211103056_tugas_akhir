<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lstm_predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lstm_run_id')->constrained('lstm_runs')->cascadeOnDelete();
            $table->dateTime('predicted_for');
            $table->unsignedInteger('horizon_index')->nullable();
            $table->decimal('pm10_actual', 10, 4)->nullable();
            $table->decimal('pm10_predicted', 10, 4)->nullable();
            $table->decimal('pm25_actual', 10, 4)->nullable();
            $table->decimal('pm25_predicted', 10, 4)->nullable();
            $table->string('health_indicator', 30)->nullable();
            $table->timestamps();

            $table->unique(['lstm_run_id', 'predicted_for']);
            $table->index(['predicted_for', 'lstm_run_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('lstm_predictions');
    }
};

