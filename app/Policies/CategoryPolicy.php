<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use App\Support\InventoryPermission;

class CategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(InventoryPermission::VIEW_ANY_CATEGORY);
    }

    public function view(User $user, Category $category): bool
    {
        return $user->can(InventoryPermission::VIEW_CATEGORY);
    }

    public function create(User $user): bool
    {
        return $user->can(InventoryPermission::CREATE_CATEGORY);
    }

    public function update(User $user, Category $category): bool
    {
        return $user->can(InventoryPermission::UPDATE_CATEGORY);
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->can(InventoryPermission::DELETE_CATEGORY);
    }

    public function deleteAny(User $user): bool
    {
        return $user->can(InventoryPermission::DELETE_ANY_CATEGORY);
    }
}
