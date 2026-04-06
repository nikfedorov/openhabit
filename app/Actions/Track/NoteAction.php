<?php

declare(strict_types=1);

namespace App\Actions\Track;

use App\Models\User;

final readonly class NoteAction
{
    public function handle(User $user, string $date, ?string $content): void
    {
        $content = mb_trim($content ?? '');

        if ($content === '') {
            $user->dailyNotes()
                ->where('date', $date)
                ->delete();
        } else {
            $user->dailyNotes()->updateOrCreate(
                ['date' => $date],
                ['content' => $content],
            );
        }
    }
}
