<?php

use App\Enums\RoleName;
use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    seedAccess();
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

it('lists products for an auditor and hides the create action', function () {
    $viewer = User::factory()->create()->assignRole(RoleName::Viewer->value);
    $product = Product::factory()->create();

    $this->actingAs($viewer);

    Livewire::test(ListProducts::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$product])
        ->assertActionHidden('create');
});

it('lets a manager create a product from the resource', function () {
    $manager = User::factory()->create()->assignRole(RoleName::Manager->value);
    $category = Category::factory()->create();

    $this->actingAs($manager);

    Livewire::test(CreateProduct::class)
        ->fillForm([
            'category_id' => $category->id,
            'sku' => 'test-mug',
            'name' => 'Test Mug',
            'unit_price' => 12.5,
            'reorder_level' => 4,
            'is_active' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $product = Product::query()->where('sku', 'TEST-MUG')->first();

    expect($product)->not->toBeNull()
        ->and($product->name)->toBe('Test Mug')
        ->and($product->category_id)->toBe($category->id);
});

it('filters the product table down to low stock', function () {
    $manager = User::factory()->create()->assignRole(RoleName::Manager->value);
    $low = Product::factory()->create([
        'quantity_on_hand' => 1,
        'reorder_level' => 5,
        'is_active' => true,
    ]);
    $healthy = Product::factory()->create([
        'quantity_on_hand' => 20,
        'reorder_level' => 5,
        'is_active' => true,
    ]);

    $this->actingAs($manager);

    Livewire::test(ListProducts::class)
        ->filterTable('low_stock')
        ->assertCanSeeTableRecords([$low])
        ->assertCanNotSeeTableRecords([$healthy]);
});
