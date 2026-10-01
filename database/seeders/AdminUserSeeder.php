<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => config('services.demo.admin_email', 'admin@demo.local')],
            [
                'name' => 'Demo Admin',
                'password' => Hash::make(config('services.demo.admin_password', 'demo123456')),
                'email_verified_at' => now(),
            ]
        );
    }
}

