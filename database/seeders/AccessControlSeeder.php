<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Support\PermissionRegistry;
use Illuminate\Database\Seeder;

class AccessControlSeeder extends Seeder
{
    public function run(): void
    {
        Permission::query()
            ->whereIn('slug', PermissionRegistry::legacySlugs())
            ->delete();

        foreach (PermissionRegistry::definitions() as $permissionData) {
            Permission::query()->updateOrCreate(
                ['slug' => $permissionData['slug']],
                $permissionData,
            );
        }

        $roles = [
            [
                'slug' => 'superadmin',
                'name' => 'Superadmin',
                'description' => 'Memiliki akses penuh ke seluruh fitur',
                'is_system' => true,
            ],
            [
                'slug' => 'admin',
                'name' => 'Admin',
                'description' => 'Memiliki akses operasional sesuai permission yang diberikan',
                'is_system' => true,
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::query()->updateOrCreate(
                ['slug' => $roleData['slug']],
                $roleData,
            );

            $permissionIds = Permission::query()
                ->whereIn('slug', PermissionRegistry::defaultRolePermissions()[$role->slug] ?? [])
                ->pluck('id');

            $role->permissions()->sync($permissionIds);
        }
    }
}
