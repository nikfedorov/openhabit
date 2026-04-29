<?php

declare(strict_types=1);

namespace App\Http\Controllers\Track;

use App\Actions\Track\SaveNoteAction;
use App\Http\Requests\Track\NoteRequest;
use App\Models\User;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\Response;

/**
 * Save a daily journal note.
 */
#[Group('Track', weight: 0)]
final readonly class NoteController
{
    public function __construct(private SaveNoteAction $saveNote) {}

    /**
     * Save daily note.
     *
     * Creates or updates the user's journal note for a specific date.
     * Send null content to clear the note.
     */
    public function store(NoteRequest $request, #[CurrentUser] User $user): Response
    {
        $this->saveNote->handle($user, $request->noteDate(), $request->content());

        return response()->noContent();
    }
}
