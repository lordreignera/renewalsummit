<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admins = [
            [
                'name'              => 'Summit Admin',
                'email'             => 'admin@renewalsummit.ug',
                'password'          => Hash::make('Summit@2026!'),
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'Registration Desk',
                'email'             => 'desk@renewalsummit.ug',
                'password'          => Hash::make('Desk@2026!'),
                'email_verified_at' => now(),
            ],
        ];

        foreach ($admins as $admin) {
            User::updateOrCreate(['email' => $admin['email']], $admin);
        }

        $this->command->info('✅ Admin users seeded');
        $this->command->table(
            ['Name', 'Email', 'Default Password'],
            [
                ['Summit Admin',      'admin@renewalsummit.ug', 'Summit@2026!'],
                ['Registration Desk', 'desk@renewalsummit.ug',  'Desk@2026!'],
            ]
        );
        $this->command->warn('⚠  Change default passwords before going live!');
    }
}
