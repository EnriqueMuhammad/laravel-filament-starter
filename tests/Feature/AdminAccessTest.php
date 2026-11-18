<?php

use App\Enums\RoleName;
use App\Models\Product;
use App\Models\User;

beforeEach(function () {
    seedAccess();
});

it('shows the landing page', function () {
    $this->get('/')->assertOk();
});

it('sends guests from the panel to the login page', function () {
    $this->get('/admin')->assertRedirect('/admin/login');
    $this->get('/admin/login')->assertOk();
});

it('lets a demo admin open the dashboard', function () {
    $admin = User::factory()->create()->assignRole(RoleName::SuperAdmin->value);

    $this->actingAs($admin)->get('/admin')->assertOk();
});

it('refuses the panel to a user without a role', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/admin')->assertForbidden();
});

it('gives managers write access and keeps auditors read-only', function () {
    $manager = User::factory()->create()->assignRole(RoleName::Manager->value);
    $viewer = User::factory()->create()->assignRole(RoleName::Viewer->value);

    expect($manager->can('create', Product::class))->toBeTrue()
        ->and($manager->can('viewAny', Product::class))->toBeTrue()
        ->and($viewer->can('viewAny', Product::class))->toBeTrue()
        ->and($viewer->can('create', Product::class))->toBeFalse()
        ->and($viewer->can('delete', Product::factory()->create()))->toBeFalse();
});
