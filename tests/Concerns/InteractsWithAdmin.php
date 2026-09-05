<?php

namespace Tests\Concerns;

use App\Models\Branch;
use App\Models\User;

trait InteractsWithAdmin
{
    protected function admin(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => User::ROLE_ADMIN,
            'branch_id' => null,
        ], $attributes));
    }

    protected function manager(?Branch $branch = null, array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => User::ROLE_MANAGER,
            'branch_id' => ($branch ?? Branch::factory()->create())->id,
        ], $attributes));
    }

    protected function receptionist(?Branch $branch = null, array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => User::ROLE_RECEPTIONIST,
            'branch_id' => ($branch ?? Branch::factory()->create())->id,
        ], $attributes));
    }

    protected function stylistUser(?Branch $branch = null, array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => User::ROLE_STYLIST,
            'branch_id' => ($branch ?? Branch::factory()->create())->id,
        ], $attributes));
    }
}
