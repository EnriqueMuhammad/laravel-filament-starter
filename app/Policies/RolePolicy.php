<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Support\InventoryPermission;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(InventoryPermission::VIEW_ANY_ROLE);
    }

    public function view(User $user, Role $role): bool
    {
        return $user->can(InventoryPermission::VIEW_ROLE);
    }

    public function create(User $user): bool
    {
        return $user->can(InventoryPermission::CREATE_ROLE);
    }

    public function update(User $user, Role $role): bool
    {
        return $user->can(InventoryPermission::UPDATE_ROLE);
    }

    public function delete(User $user, Role $role): bool
    {
        return $user->can(InventoryPermission::DELETE_ROLE);
    }

    public function deleteAny(User $user): bool
    {
        return $user->can(InventoryPermission::DELETE_ROLE);
    }
}
