<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Permission keys follow Filament Shield's default builder:
 * PascalCase affix, a colon, then the model name.
 */
final class InventoryPermission
{
    public const VIEW_ANY_CATEGORY = 'ViewAny:Category';

    public const VIEW_CATEGORY = 'View:Category';

    public const CREATE_CATEGORY = 'Create:Category';

    public const UPDATE_CATEGORY = 'Update:Category';

    public const DELETE_CATEGORY = 'Delete:Category';

    public const DELETE_ANY_CATEGORY = 'DeleteAny:Category';

    public const VIEW_ANY_PRODUCT = 'ViewAny:Product';

    public const VIEW_PRODUCT = 'View:Product';

    public const CREATE_PRODUCT = 'Create:Product';

    public const UPDATE_PRODUCT = 'Update:Product';

    public const DELETE_PRODUCT = 'Delete:Product';

    public const DELETE_ANY_PRODUCT = 'DeleteAny:Product';

    public const VIEW_ANY_STOCK_MOVEMENT = 'ViewAny:StockMovement';

    public const VIEW_STOCK_MOVEMENT = 'View:StockMovement';

    public const CREATE_STOCK_MOVEMENT = 'Create:StockMovement';

    public const UPDATE_STOCK_MOVEMENT = 'Update:StockMovement';

    public const DELETE_STOCK_MOVEMENT = 'Delete:StockMovement';

    public const DELETE_ANY_STOCK_MOVEMENT = 'DeleteAny:StockMovement';

    public const VIEW_ANY_ROLE = 'ViewAny:Role';

    public const VIEW_ROLE = 'View:Role';

    public const CREATE_ROLE = 'Create:Role';

    public const UPDATE_ROLE = 'Update:Role';

    public const DELETE_ROLE = 'Delete:Role';

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return [
            self::VIEW_ANY_CATEGORY,
            self::VIEW_CATEGORY,
            self::CREATE_CATEGORY,
            self::UPDATE_CATEGORY,
            self::DELETE_CATEGORY,
            self::DELETE_ANY_CATEGORY,
            self::VIEW_ANY_PRODUCT,
            self::VIEW_PRODUCT,
            self::CREATE_PRODUCT,
            self::UPDATE_PRODUCT,
            self::DELETE_PRODUCT,
            self::DELETE_ANY_PRODUCT,
            self::VIEW_ANY_STOCK_MOVEMENT,
            self::VIEW_STOCK_MOVEMENT,
            self::CREATE_STOCK_MOVEMENT,
            self::UPDATE_STOCK_MOVEMENT,
            self::DELETE_STOCK_MOVEMENT,
            self::DELETE_ANY_STOCK_MOVEMENT,
            self::VIEW_ANY_ROLE,
            self::VIEW_ROLE,
            self::CREATE_ROLE,
            self::UPDATE_ROLE,
            self::DELETE_ROLE,
        ];
    }

    /**
     * @return list<string>
     */
    public static function catalogRead(): array
    {
        return [
            self::VIEW_ANY_CATEGORY,
            self::VIEW_CATEGORY,
            self::VIEW_ANY_PRODUCT,
            self::VIEW_PRODUCT,
            self::VIEW_ANY_STOCK_MOVEMENT,
            self::VIEW_STOCK_MOVEMENT,
        ];
    }

    /**
     * @return list<string>
     */
    public static function catalogManage(): array
    {
        return [
            self::VIEW_ANY_CATEGORY,
            self::VIEW_CATEGORY,
            self::CREATE_CATEGORY,
            self::UPDATE_CATEGORY,
            self::DELETE_CATEGORY,
            self::DELETE_ANY_CATEGORY,
            self::VIEW_ANY_PRODUCT,
            self::VIEW_PRODUCT,
            self::CREATE_PRODUCT,
            self::UPDATE_PRODUCT,
            self::DELETE_PRODUCT,
            self::DELETE_ANY_PRODUCT,
            self::VIEW_ANY_STOCK_MOVEMENT,
            self::VIEW_STOCK_MOVEMENT,
            self::CREATE_STOCK_MOVEMENT,
            self::UPDATE_STOCK_MOVEMENT,
            self::DELETE_STOCK_MOVEMENT,
            self::DELETE_ANY_STOCK_MOVEMENT,
        ];
    }
}
