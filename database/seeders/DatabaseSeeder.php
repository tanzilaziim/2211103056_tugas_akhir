<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'superadmin@dlhindramayu.id'],
            [
                'name' => 'Super Admin DLH',
                'username' => 'superadmin',
                'password' => 'admin12345',
                'role' => 'super_admin',
                'is_active' => true,
            ]
        );
    }
}
