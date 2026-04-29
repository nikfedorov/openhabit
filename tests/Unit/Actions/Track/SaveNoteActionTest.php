<?php

declare(strict_types=1);

use App\Actions\Track\SaveNoteAction;
use App\Models\DailyNote;
use App\Models\User;

it('creates or updates a daily note for the given date when content is non-empty', function (): void {
    $user = User::factory()->create();

    resolve(SaveNoteAction::class)->handle($user, '2025-01-10', 'First');
    resolve(SaveNoteAction::class)->handle($user, '2025-01-10', '  Second  ');

    expect($user->dailyNotes()->count())->toBe(1)
        ->and($user->dailyNotes()->where('date', '2025-01-10')->value('content'))->toBe('Second');
});

it('deletes the note when content is empty or only whitespace', function (?string $content): void {
    $user = User::factory()->create();
    DailyNote::factory()->for($user)->create(['date' => '2025-01-10', 'content' => 'old']);

    resolve(SaveNoteAction::class)->handle($user, '2025-01-10', $content);

    expect($user->dailyNotes()->count())->toBe(0);
})->with([
    'null' => [null],
    'empty' => [''],
    'whitespace' => ['   '],
]);
