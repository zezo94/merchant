<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view dashboard',
            'view merchants',
            'create merchants',
            'edit merchants',
            'delete merchants',
            'print merchants',
            'export merchants',
            'manage users',
            'manage roles',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $userRole = Role::firstOrCreate(['name' => 'user']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);

        $userRole->syncPermissions([
            'view merchants',
        ]);

        $adminRole->syncPermissions([
            'view dashboard',
            'view merchants',
            'create merchants',
            'edit merchants',
            'print merchants',
            'export merchants',
        ]);

        $superAdminRole->syncPermissions(Permission::all());

        // إنشاء مستخدم سوبر أدمن افتراضي إذا لم يوجد
        $superAdmin = User::firstOrCreate(
            ['email' => '[email protected]'],
            [
                'name' => 'Super Admin',
                'password' => 'password123',
            ]
        );

        if (!$superAdmin->hasRole('super-admin')) {
            $superAdmin->assignRole('super-admin');
        }

        // إنشاء مستخدم أدمن افتراضي
        $admin = User::firstOrCreate(
            ['email' => '[email prote cted]'],
            [
                'name' => 'Admin User',
                'password' => 'password123',
            ]
        );

        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // إنشاء مستخدم عادي افتراضي
        $user = User::firstOrCreate(
            ['email' => '[email protected]'],
            [
                'name' => 'Normal User',
                'password' => 'password123',
            ]
        );

        if (!$user->hasRole('user')) {
            $user->assignRole('user');
        }
     }
}
