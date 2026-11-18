<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use App\Support\InventoryPermission;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(InventoryPermission::VIEW_ANY_PRODUCT);
    }

    public function view(User $user, Product $product): bool
    {
        return $user->can(InventoryPermission::VIEW_PRODUCT);
    }

    public function create(User $user): bool
    {
        return $user->can(InventoryPermission::CREATE_PRODUCT);
    }

    public function update(User $user, Product $product): bool
    {
        return $user->can(InventoryPermission::UPDATE_PRODUCT);
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->can(InventoryPermission::DELETE_PRODUCT);
    }

    public function deleteAny(User $user): bool
    {
        return $user->can(InventoryPermission::DELETE_ANY_PRODUCT);
    }
}
