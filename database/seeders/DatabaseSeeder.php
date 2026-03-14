<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $superAdmin = User::query()->updateOrCreate(
            ['email' => 'superadmin@dlhindramayu.id'],
            [
                'name' => 'Super Admin DLH',
                'username' => 'superadmin',
                'password' => 'admin12345',
                'role' => 'super_admin',
                'is_active' => true,
            ]
        );

        DB::table('prediction_settings')->updateOrInsert(
            ['id' => 1],
            [
                'active_lstm_run_id' => null,
                'active_model_key' => 'bivariate_30m_lb48_zero2null_interp_winsor',
                'active_model_config' => json_encode([
                    'model_variant' => 'bivariate',
                    'lookback' => 48,
                    'resolution_minutes' => 30,
                    'preprocessing' => [
                        'zero_to_null' => true,
                        'interpolation' => 'linear',
                        'winsorizing' => 'iqr',
                    ],
                ], JSON_UNESCAPED_UNICODE),
                'notes' => 'Model default aktif untuk prediksi publik dan admin.',
                'updated_by' => $superAdmin->id,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }
}
