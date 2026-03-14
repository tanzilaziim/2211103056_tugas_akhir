<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prediction_settings', function (Blueprint $table) {
            $table->string('active_model_key', 120)->nullable()->after('active_lstm_run_id');
            $table->json('active_model_config')->nullable()->after('active_model_key');
        });
    }

    public function down(): void
    {
        Schema::table('prediction_settings', function (Blueprint $table) {
            $table->dropColumn(['active_model_key', 'active_model_config']);
        });
    }
};

