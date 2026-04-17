<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Gate;

test('viewHorizon gate allows admins and denies non-admins', function (): void {
    $admin = User::factory()->admin()->create();
    $regular = User::factory()->create(['is_admin' => false]);

    expect(Gate::has('viewHorizon'))->toBeTrue();

    $this->actingAs($admin);
    expect(Gate::check('viewHorizon'))->toBeTrue();

    $this->actingAs($regular);
    expect(Gate::check('viewHorizon'))->toBeFalse();
});
