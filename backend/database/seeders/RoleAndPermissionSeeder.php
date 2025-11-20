<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'manage staff advances',
            'manage menu',
            'manage categories',
            'manage tables',
            'manage orders',
            'manage bills',
            'manage settings',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // Create roles and assign existing permissions
        $adminRole = Role::findOrCreate('admin');
        $adminRole->givePermissionTo([
            'manage staff advances',
            'manage menu',
            'manage categories',
            'manage tables',
            'manage orders',
            'manage bills',
            'manage settings',
        ]);

        $managerRole = Role::findOrCreate('manager');
        $managerRole->givePermissionTo([
            'manage staff advances',
            'manage tables',
            'manage orders',
            'manage bills',
        ]);

        $waiterRole = Role::findOrCreate('waiter');
        $waiterRole->givePermissionTo([
            'manage orders',
        ]);

        $staffRole = Role::findOrCreate('staff');
        $staffRole->givePermissionTo([
            'manage orders',
            'manage tables',
            'manage bills'
        ]);

        // Assign admin role to the first user (usually the owner)
        $user = \App\Models\User::first();
        if ($user) {
            $user->assignRole('admin');
        }
    }
}
