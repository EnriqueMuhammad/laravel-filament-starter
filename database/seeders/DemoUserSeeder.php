<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $this->user(
            name: 'Enrique Muhammad',
            email: 'admin@example.com',
            role: RoleName::SuperAdmin,
        );

        $this->user(
            name: 'Inventory Manager',
            email: 'manager@example.com',
            role: RoleName::Manager,
        );

        $this->user(
            name: 'Stock Auditor',
            email: 'viewer@example.com',
            role: RoleName::Viewer,
        );
    }

    private function user(string $name, string $email, RoleName $role): void
    {
        $user = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        $user->syncRoles([$role->value]);
    }
}
