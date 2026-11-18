<?php

use App\Enums\StockMovementType;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Validation\ValidationException;

it('adds inbound units to the quantity on hand', function () {
    $product = Product::factory()->create();

    StockMovement::factory()->for($product)->inbound(8)->create();

    expect($product->refresh()->quantity_on_hand)->toBe(8)
        ->and($product->isLowStock())->toBeFalse();
});

it('applies a negative adjustment after inbound stock', function () {
    $product = Product::factory()->create(['reorder_level' => 2]);

    StockMovement::factory()->for($product)->inbound(5)->create();
    StockMovement::factory()->for($product)->adjustment(-2)->create();

    expect($product->refresh()->quantity_on_hand)->toBe(3);
});

it('rejects an outbound movement that would go below zero', function () {
    $product = Product::factory()->create();

    StockMovement::factory()->for($product)->inbound(3)->create();

    expect(fn () => StockMovement::factory()->for($product)->outbound(4)->create())
        ->toThrow(ValidationException::class);

    expect($product->refresh()->quantity_on_hand)->toBe(3);
});

it('marks an active product as low stock at the reorder level', function () {
    $low = Product::factory()->create([
        'quantity_on_hand' => 2,
        'reorder_level' => 2,
    ]);
    $archived = Product::factory()->inactive()->create([
        'quantity_on_hand' => 0,
        'reorder_level' => 5,
    ]);

    expect($low->isLowStock())->toBeTrue()
        ->and($archived->isLowStock())->toBeFalse()
        ->and(Product::query()->lowStock()->pluck('id')->all())->toBe([$low->id]);
});

it('signs outbound quantities even when they are stored as positives', function () {
    $movement = new StockMovement([
        'type' => StockMovementType::Outbound,
        'quantity' => 4,
    ]);

    expect($movement->signedQuantity())->toBe(-4);
});
