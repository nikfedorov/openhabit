<?php

declare(strict_types=1);

use App\Models\HabitTemplate;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

it('requires authentication for copying templates', function (): void {
    $template = HabitTemplate::factory()->daily()->create();

    $this->postJson(sprintf('/api/edit/templates/%d/copy', $template->id))
        ->assertUnauthorized();
});

it('copies a template and creates separate habits for each copy', function (): void {
    $user = User::factory()->create();
    $template = HabitTemplate::factory()->daily()->create([
        'name' => ['en' => 'Meditation'],
        'iterations_required' => 1,
    ]);

    $this->actingAs($user, 'sanctum')
        ->postJson(sprintf('/api/edit/templates/%d/copy', $template->id))
        ->assertOk()
        ->assertJson(fn (AssertableJson $json): AssertableJson => $json
            ->has('data', 1, fn (AssertableJson $json): AssertableJson => $json
                ->has('id')
                ->where('name', 'Meditation')
                ->where('iterations_required', 1)
                ->has('is_active')
                ->has('sort_order')
                ->has('human_readable')
                ->has('is_franklin_virtue')
                ->has('description')
            )
        );

    // Copy again — should create a second habit.
    $this->actingAs($user, 'sanctum')
        ->postJson(sprintf('/api/edit/templates/%d/copy', $template->id))
        ->assertOk()
        ->assertJson(fn (AssertableJson $json): AssertableJson => $json
            ->has('data', 2)
        );

    expect($user->habits()->count())->toBe(2);
});
