<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            ['name' => 'view-users', 'description' => 'View users'],
            ['name' => 'create-users', 'description' => 'Create users'],
            ['name' => 'edit-users', 'description' => 'Edit users'],
            ['name' => 'delete-users', 'description' => 'Delete users'],
            ['name' => 'view-roles', 'description' => 'View roles'],
            ['name' => 'manage-roles', 'description' => 'Manage roles and permissions'],
            ['name' => 'view-dashboard', 'description' => 'View dashboard'],
        ];

        foreach ($permissions as $permissionData) {
            Permission::firstOrCreate(
                ['name' => $permissionData['name']],
                ['description' => $permissionData['description']]
            );
        }

        // Create roles
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'super-admin'],
            ['description' => 'Super Administrator with all permissions']
        );

        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['description' => 'Administrator with most permissions']
        );

        $userRole = Role::firstOrCreate(
            ['name' => 'user'],
            ['description' => 'Regular user with basic permissions']
        );

        // Assign all permissions to super-admin
        $allPermissions = Permission::all();
        $superAdminRole->permissions()->sync($allPermissions->pluck('id'));

        // Assign some permissions to admin
        $adminPermissions = Permission::whereIn('name', [
            'view-users',
            'create-users',
            'edit-users',
            'view-roles',
            'view-dashboard',
        ])->get();
        $adminRole->permissions()->sync($adminPermissions->pluck('id'));

        // Assign basic permissions to user
        $userPermissions = Permission::whereIn('name', [
            'view-dashboard',
        ])->get();
        $userRole->permissions()->sync($userPermissions->pluck('id'));

        // Create super admin user
        $superAdmin = User::firstOrCreate(
            ['email' => 'janes.oosthuizen@gmail.com'],
            [
                'name' => 'Janes Oosthuizen',
                'password' => Hash::make('password'),
            ]
        );

        // Assign super-admin role to the super admin user
        $superAdmin->roles()->sync([$superAdminRole->id]);

        $this->command->info('Roles, permissions, and super admin user created successfully!');
        $this->command->info('Super Admin Email: janes.oosthuizen@gmail.com');
        $this->command->info('Super Admin Password: password');
    }
}
