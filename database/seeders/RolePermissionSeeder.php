<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // ── Permissions ───────────────────────────────────────────
        $permissions = [
            // Dashboard
            ['name' => 'view_dashboard_stats',  'display_name' => 'View Dashboard Stats',      'group' => 'Dashboard',       'description' => 'View financial revenue statistics on the dashboard'],
            // Registrations
            ['name' => 'view_registrations',    'display_name' => 'View Registrations',         'group' => 'Registrations',   'description' => 'View registration list and individual registration details'],
            ['name' => 'create_registration',   'display_name' => 'Create Registration',        'group' => 'Registrations',   'description' => 'Manually create new registrations'],
            ['name' => 'export_registrations',  'display_name' => 'Export Registrations CSV',   'group' => 'Registrations',   'description' => 'Export registration data to a CSV file'],
            ['name' => 'resend_qr',             'display_name' => 'Resend QR Email',            'group' => 'Registrations',   'description' => 'Resend the QR code confirmation email to a registrant'],
            // Check-In
            ['name' => 'checkin_scan',          'display_name' => 'Check-In Scanner',          'group' => 'Check-In',        'description' => 'Use the check-in scanner and confirm attendee check-ins'],
            // Hotels
            ['name' => 'manage_hotels',         'display_name' => 'Manage Hotels',              'group' => 'Hotels',          'description' => 'Create, edit and delete hotel listings and room types'],
            // Testimonials
            ['name' => 'manage_testimonials',   'display_name' => 'Manage Testimonials',        'group' => 'Testimonials',    'description' => 'Review, approve and delete testimonial videos'],
            // Users
            ['name' => 'manage_users',          'display_name' => 'Manage Admin Users',         'group' => 'Users',           'description' => 'Create, edit and delete admin user accounts'],
            // Access Control
            ['name' => 'manage_roles',          'display_name' => 'Manage Roles',               'group' => 'Access Control',  'description' => 'Create, edit and delete roles and assign permissions to them'],
            ['name' => 'manage_permissions',    'display_name' => 'Manage Permissions',         'group' => 'Access Control',  'description' => 'Create, edit and delete individual permission definitions'],
        ];

        foreach ($permissions as $data) {
            Permission::updateOrCreate(['name' => $data['name']], $data);
        }

        // ── Roles ────────────────────────────────────────────────
        $allIds = Permission::pluck('id')->all();

        $roles = [
            [
                'name'         => 'super_admin',
                'display_name' => 'Super Admin',
                'description'  => 'Full access to all features and settings.',
                'permissions'  => $allIds,          // all permissions
            ],
            [
                'name'         => 'finance',
                'display_name' => 'Finance',
                'description'  => 'View dashboard stats, registrations, export data and perform check-ins.',
                'permissions'  => ['view_dashboard_stats', 'view_registrations', 'export_registrations', 'checkin_scan'],
            ],
            [
                'name'         => 'registrar',
                'display_name' => 'Registrar',
                'description'  => 'View registrations, create new registrations, send payment prompts, and use the check-in scanner.',
                'permissions'  => ['view_registrations', 'create_registration', 'checkin_scan'],
            ],
        ];

        foreach ($roles as $data) {
            $role = Role::updateOrCreate(
                ['name' => $data['name']],
                ['display_name' => $data['display_name'], 'description' => $data['description']]
            );

            if (is_array($data['permissions']) && isset($data['permissions'][0]) && is_int($data['permissions'][0])) {
                // raw IDs (super_admin — all)
                $role->permissions()->sync($data['permissions']);
            } else {
                // permission slugs
                $ids = Permission::whereIn('name', $data['permissions'])->pluck('id');
                $role->permissions()->sync($ids);
            }
        }

        $this->command->info('✅ Roles and permissions seeded');
        $this->command->table(
            ['Role', 'Permissions'],
            Role::with('permissions')->get()->map(fn ($r) => [
                $r->display_name,
                $r->permissions->pluck('name')->implode(', ') ?: '—',
            ])->toArray()
        );
    }
}
