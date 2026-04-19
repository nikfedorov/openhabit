<?php

declare(strict_types=1);

use App\Actions\Settings\ExportUserDataAction;
use App\Models\Category;
use App\Models\DailyNote;
use App\Models\Habit;
use App\Models\HabitCompletion;
use App\Models\Payment;
use App\Models\Stat;
use App\Models\User;
use App\Models\UserMemory;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    Storage::fake('local');
});

it('exports all user data including soft-deleted habits as csv zip', function (): void {
    $user = User::factory()->create(['locale' => 'en']);
    $category = Category::factory()->for($user)->create();
    $habit = Habit::factory()->for($user)->for($category)->daily()->create(['name' => ['en' => 'Exercise']]);
    $deletedHabit = Habit::factory()->for($user)->daily()->create(['name' => ['en' => 'Deleted']]);
    HabitCompletion::factory()->for($user)->for($deletedHabit)->forDate('2025-02-01')->create();
    $deletedHabit->delete();
    HabitCompletion::factory()->for($user)->for($habit)->forDate('2025-01-15')->create();
    DailyNote::factory()->for($user)->today()->create();
    Stat::factory()->for($user)->forDate('2025-06-01')->create();
    UserMemory::factory()->for($user)->create();
    Payment::factory()->for($user)->create();

    $path = (new ExportUserDataAction)->handle($user);

    expect($path)->toStartWith('exports/user-'.$user->id.'-');
    Storage::disk('local')->assertExists($path);

    $zip = new ZipArchive;
    $zip->open(Storage::disk('local')->path($path));

    expect($zip->numFiles)->toBe(7);
    expect($zip->getFromName('categories.csv'))->toContain('id,name,description');
    expect($zip->getFromName('habits.csv'))->toContain('id,category_id,name');
    expect($zip->getFromName('habit_completions.csv'))->toContain('id,habit_id,completed_at');
    expect($zip->getFromName('daily_notes.csv'))->toContain('id,date,content');
    expect($zip->getFromName('stats.csv'))->toContain('id,period,period_start');
    expect($zip->getFromName('user_memories.csv'))->toContain('id,category,content');
    expect($zip->getFromName('payments.csv'))->toContain('id,total_amount,currency');

    // Habit names are plain text, not JSON
    $habitsCsv = $zip->getFromName('habits.csv');
    expect($habitsCsv)->toContain('Exercise');
    expect($habitsCsv)->not->toContain('{');

    // Soft-deleted habit is included
    $habitLines = array_filter(explode("\n", mb_trim($habitsCsv)));
    expect($habitLines)->toHaveCount(3); // header + active + soft-deleted

    $zip->close();
});

it('exports empty csvs with only headers when user has no data', function (): void {
    $user = User::factory()->create();

    $path = (new ExportUserDataAction)->handle($user);

    Storage::disk('local')->assertExists($path);

    $zip = new ZipArchive;
    $zip->open(Storage::disk('local')->path($path));

    $habitsCsv = $zip->getFromName('habits.csv');
    expect($habitsCsv)->toContain('id,category_id,name');
    expect(array_filter(explode("\n", mb_trim($habitsCsv))))->toHaveCount(1);

    $zip->close();
});
