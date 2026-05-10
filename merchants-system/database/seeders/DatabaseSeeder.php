<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
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
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        $superAdminRole->syncPermissions(Permission::all());

        $adminRole->syncPermissions([
            'view dashboard',
            'view merchants',
            'create merchants',
            'edit merchants',
            'print merchants',
            'export merchants',
        ]);

        $userRole->syncPermissions([
            'view merchants',
        ]);

        // Root User - يتجاوز كل شيء حتى بدون roles
        User::updateOrCreate(
            ['email' => 'root@admin.com'],
            [
                'name' => 'Root User',
                'password' => Hash::make('password123'),
                'is_root' => true,
            ]
        );

        $superAdmin = User::updateOrCreate(
            ['email' => 'super@admin.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
                'is_root' => false,
            ]
        );
        $superAdmin->syncRoles([$superAdminRole]);

        $admin = User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password123'),
                'is_root' => false,
            ]
        );
        $admin->syncRoles([$adminRole]);

        $user = User::updateOrCreate(
            ['email' => 'user@admin.com'],
            [
                'name' => 'User',
                'password' => Hash::make('password123'),
                'is_root' => false,
            ]
        );
        $user->syncRoles([$userRole]);
    }
}
