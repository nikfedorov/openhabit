<?php

declare(strict_types=1);

use App\Actions\Edit\GetHabitTemplatesAction;
use App\Models\CategoryTemplate;
use App\Models\HabitTemplate;

it('returns only active templates from active categories that show in templates, ordered by category and template sort_order', function (): void {
    $second = CategoryTemplate::factory()->create(['sort_order' => 2]);
    $first = CategoryTemplate::factory()->create(['sort_order' => 1]);
    $inactiveCat = CategoryTemplate::factory()->inactive()->create();

    $b = HabitTemplate::factory()->daily()->create([
        'category_template_id' => $second->id,
        'show_in_templates' => true,
        'sort_order' => 1,
    ]);
    $a = HabitTemplate::factory()->daily()->create([
        'category_template_id' => $first->id,
        'show_in_templates' => true,
        'sort_order' => 1,
    ]);

    HabitTemplate::factory()->daily()->create([
        'category_template_id' => $inactiveCat->id,
        'show_in_templates' => true,
    ]);
    HabitTemplate::factory()->daily()->create([
        'category_template_id' => $first->id,
        'show_in_templates' => false,
    ]);
    HabitTemplate::factory()->inactive()->create([
        'category_template_id' => $first->id,
        'show_in_templates' => true,
    ]);

    $templates = resolve(GetHabitTemplatesAction::class)->handle();

    expect($templates)->toHaveCount(2)
        ->and($templates[0]->id)->toBe($a->id)
        ->and($templates[1]->id)->toBe($b->id)
        ->and($templates[0]->relationLoaded('categoryTemplate'))->toBeTrue();
});
