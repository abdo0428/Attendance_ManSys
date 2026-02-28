<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class RolesAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        $perms = [
            'employees.view','employees.create','employees.update','employees.delete',
            'attendance.view','attendance.checkin','attendance.checkout','attendance.update','attendance.delete',
            'reports.view',
            'settings.manage','users.manage','audit.view',
        ];

        foreach ($perms as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $hrRole    = Role::firstOrCreate(['name' => 'hr']);
        $viewerRole= Role::firstOrCreate(['name' => 'viewer']);

        $adminRole->syncPermissions($perms);
        $hrRole->syncPermissions([
            'employees.view','employees.create','employees.update',
            'attendance.view','attendance.checkin','attendance.checkout','attendance.update',
            'reports.view',
        ]);
        $viewerRole->syncPermissions([
            'employees.view','attendance.view','reports.view'
        ]);

        $admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            ['name' => 'Admin', 'password' => Hash::make('123456789')]
        );
        $admin->assignRole('admin');
    }
}
