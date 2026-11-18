<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Support\InventoryPermission;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (InventoryPermission::all() as $permission) {
            Permission::findOrCreate($permission);
        }

        Role::findOrCreate(RoleName::SuperAdmin->value);

        Role::findOrCreate(RoleName::Manager->value)
            ->syncPermissions(InventoryPermission::catalogManage());

        Role::findOrCreate(RoleName::Viewer->value)
            ->syncPermissions(InventoryPermission::catalogRead());

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
