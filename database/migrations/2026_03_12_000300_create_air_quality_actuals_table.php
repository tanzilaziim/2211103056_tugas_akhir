<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('air_quality_actuals', function (Blueprint $table) {
            $table->id();
            $table->dateTime('observed_at');
            $table->decimal('pm10', 10, 4)->nullable();
            $table->decimal('pm25', 10, 4)->nullable();
            $table->foreignId('data_import_id')->nullable()->constrained('data_imports')->nullOnDelete();
            $table->timestamps();

            $table->unique('observed_at');
            $table->index('data_import_id');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('air_quality_actuals');
    }
};

