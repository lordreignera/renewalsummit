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
            ['email' => 'admin@renewalsummit.ug'],
            [
                'name'              => 'Summit Admin',
                'email'             => 'admin@renewalsummit.ug',
                'password'          => Hash::make('Summit@2026!'),
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Admin user created: admin@renewalsummit.ug / Summit@2026!');
    }
}
