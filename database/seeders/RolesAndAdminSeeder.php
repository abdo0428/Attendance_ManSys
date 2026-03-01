<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Setting;
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
        $superPerms = ['companies.manage'];
        $allPerms = array_unique(array_merge($perms, $superPerms));

        foreach ($allPerms as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $hrRole    = Role::firstOrCreate(['name' => 'hr']);
        $viewerRole= Role::firstOrCreate(['name' => 'viewer']);
        $superRole = Role::firstOrCreate(['name' => 'super-admin']);

        $adminRole->syncPermissions($perms);
        $hrRole->syncPermissions([
            'employees.view','employees.create','employees.update',
            'attendance.view','attendance.checkin','attendance.checkout','attendance.update',
            'reports.view',
        ]);
        $viewerRole->syncPermissions([
            'employees.view','attendance.view','reports.view'
        ]);
        $superRole->syncPermissions($superPerms);

        $company = Company::firstOrCreate(['name' => 'Default Company']);
        Setting::setValue('company_name', $company->name, $company->id);

        $admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            ['name' => 'Admin', 'password' => Hash::make('123456789')]
        );
        if (!$admin->company_id) {
            $admin->company_id = $company->id;
            $admin->save();
        }
        if (!$company->owner_user_id) {
            $company->owner_user_id = $admin->id;
            $company->save();
        }
        $admin->assignRole('admin');

        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@test.com'],
            ['name' => 'Super Admin', 'password' => Hash::make('123456789')]
        );
        if ($superAdmin->company_id !== null) {
            $superAdmin->company_id = null;
            $superAdmin->save();
        }
        $superAdmin->assignRole('super-admin');
    }
}
