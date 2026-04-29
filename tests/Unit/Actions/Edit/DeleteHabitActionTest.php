<?php

declare(strict_types=1);

use App\Actions\Edit\DeleteHabitAction;
use App\Actions\Edit\ReorderHabitsAction;
use App\Models\Habit;
use App\Models\User;

it('deletes the habit and re-numbers remaining regular habits sequentially', function (): void {
    $user = User::factory()->create();
    $a = Habit::factory()->daily()->for($user)->create(['sort_order' => 200]);
    $b = Habit::factory()->daily()->for($user)->create(['sort_order' => 201]);
    $c = Habit::factory()->daily()->for($user)->create(['sort_order' => 202]);
    Habit::factory()->daily()->franklinVirtue()->for($user)->create(['sort_order' => 1]);

    resolve(DeleteHabitAction::class)->handle($user, $b);

    expect($b->fresh()?->trashed() ?? true)->toBeTrue()
        ->and($a->fresh()->sort_order)->toBe(ReorderHabitsAction::MIN_SORT_ORDER)
        ->and($c->fresh()->sort_order)->toBe(ReorderHabitsAction::MIN_SORT_ORDER + 1);
});
