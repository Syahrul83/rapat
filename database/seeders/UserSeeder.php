<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'super@admin.test'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => UserRole::SuperAdmin,
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin1@admin.test'],
            [
                'name' => 'Admin TU 1',
                'password' => Hash::make('password'),
                'role' => UserRole::AdminTu,
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin2@admin.test'],
            [
                'name' => 'Admin TU 2',
                'password' => Hash::make('password'),
                'role' => UserRole::AdminTu,
            ]
        );
    }
}
