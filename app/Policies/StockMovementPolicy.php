<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\StockMovement;
use App\Models\User;
use App\Support\InventoryPermission;

class StockMovementPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(InventoryPermission::VIEW_ANY_STOCK_MOVEMENT);
    }

    public function view(User $user, StockMovement $stockMovement): bool
    {
        return $user->can(InventoryPermission::VIEW_STOCK_MOVEMENT);
    }

    public function create(User $user): bool
    {
        return $user->can(InventoryPermission::CREATE_STOCK_MOVEMENT);
    }

    public function update(User $user, StockMovement $stockMovement): bool
    {
        return $user->can(InventoryPermission::UPDATE_STOCK_MOVEMENT);
    }

    public function delete(User $user, StockMovement $stockMovement): bool
    {
        return $user->can(InventoryPermission::DELETE_STOCK_MOVEMENT);
    }

    public function deleteAny(User $user): bool
    {
        return $user->can(InventoryPermission::DELETE_ANY_STOCK_MOVEMENT);
    }
}
