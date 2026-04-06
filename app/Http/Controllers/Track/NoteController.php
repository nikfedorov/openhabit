<?php

declare(strict_types=1);

namespace App\Http\Controllers\Track;

use App\Actions\Track\NoteAction;
use App\Http\Requests\Track\NoteRequest;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\Response;

final readonly class NoteController
{
    public function __construct(private NoteAction $saveNote) {}

    public function store(NoteRequest $request, #[CurrentUser] User $user): Response
    {
        $this->saveNote->handle($user, $request->noteDate(), $request->content());

        return response()->noContent();
    }
}
