<?php

declare(strict_types=1);

use App\Models\CategoryTemplate;
use App\Models\Habit;
use App\Models\HabitTemplate;
use App\Models\User;
use App\Services\HabitTemplateService;

beforeEach(function (): void {
    $this->service = new HabitTemplateService;
});

it('creates categories and habits', function (): void {
    $user = User::factory()->create();

    $categoryTemplate = CategoryTemplate::factory()->create([
        'is_active' => true,
        'copy_by_default' => true,
    ]);
    HabitTemplate::factory()->for($categoryTemplate)->create([
        'is_active' => true,
        'copy_by_default' => true,
    ]);

    $habits = $this->service->applyTemplatesToUser($user);

    expect($habits)->toHaveCount(1)
        ->and($user->categories()->count())->toBe(1)
        ->and($user->habits()->count())->toBe(1);
});

it('maps category template to user category', function (): void {
    $user = User::factory()->create();

    $categoryTemplate = CategoryTemplate::factory()->create([
        'is_active' => true,
        'copy_by_default' => true,
    ]);
    HabitTemplate::factory()->for($categoryTemplate)->create([
        'is_active' => true,
        'copy_by_default' => true,
    ]);

    $this->service->applyTemplatesToUser($user);

    $habit = $user->habits()->first();
    $category = $user->categories()->first();

    expect($habit->category_id)->toBe($category->id);
});

it('creates categories from templates', function (): void {
    $user = User::factory()->create();

    $template = CategoryTemplate::factory()->create([
        'is_active' => true,
        'copy_by_default' => true,
    ]);

    $mapping = $this->service->createCategoriesFromTemplates($user);

    expect($mapping)->toHaveKey($template->id)
        ->and($user->categories()->count())->toBe(1);
});

it('creates habit without category when copying template to user', function (): void {
    $template = HabitTemplate::factory()->create();
    $user = User::factory()->create();

    $habit = $this->service->copyTemplateToUser($user, $template);

    expect($habit)->toBeInstanceOf(Habit::class)
        ->and($habit->category_id)->toBeNull();
});

it('uses category mapping when creating habit from template', function (): void {
    $categoryTemplate = CategoryTemplate::factory()->create();
    $template = HabitTemplate::factory()->for($categoryTemplate)->create();
    $user = User::factory()->create();
    $category = $user->categories()->create(['name' => 'Test']);

    $habit = $this->service->createHabitFromTemplate($user, $template, [
        $categoryTemplate->id => $category->id,
    ]);

    expect($habit->category_id)->toBe($category->id);
});

it('sets no category when creating habit from template without mapping', function (): void {
    $template = HabitTemplate::factory()->create();
    $user = User::factory()->create();

    $habit = $this->service->createHabitFromTemplate($user, $template);

    expect($habit->category_id)->toBeNull();
});
