<?php

declare(strict_types=1);

use App\Actions\Edit\ToggleFranklinHabitsAction;
use App\Models\Habit;
use App\Models\User;

it('deactivates all franklin virtue habits when at least one is active', function (): void {
    $user = User::factory()->create();
    $active = Habit::factory()->daily()->franklinVirtue()->for($user)->create(['is_active' => true]);
    $inactive = Habit::factory()->daily()->franklinVirtue()->for($user)->create(['is_active' => false]);
    $regular = Habit::factory()->daily()->for($user)->create(['is_active' => true]);

    resolve(ToggleFranklinHabitsAction::class)->handle($user);

    expect($active->fresh()->is_active)->toBeFalse()
        ->and($inactive->fresh()->is_active)->toBeFalse()
        ->and($regular->fresh()->is_active)->toBeTrue();
});

it('activates all franklin virtue habits when none are active', function (): void {
    $user = User::factory()->create();
    $first = Habit::factory()->daily()->franklinVirtue()->for($user)->create(['is_active' => false]);
    $second = Habit::factory()->daily()->franklinVirtue()->for($user)->create(['is_active' => false]);

    resolve(ToggleFranklinHabitsAction::class)->handle($user);

    expect($first->fresh()->is_active)->toBeTrue()
        ->and($second->fresh()->is_active)->toBeTrue();
});
