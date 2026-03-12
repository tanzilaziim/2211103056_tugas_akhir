<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prediction_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('active_lstm_run_id')->nullable()->constrained('lstm_runs')->nullOnDelete();
            $table->string('notes')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('prediction_settings');
    }
};

