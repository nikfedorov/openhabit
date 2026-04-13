<?php

declare(strict_types=1);

namespace App\Http\Controllers\Edit;

use App\Actions\Edit\ReorderAction;
use App\Http\Requests\Edit\ReorderHabitsRequest;
use App\Models\User;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\Response;

/**
 * Reorder habits.
 */
#[Group('Edit', weight: 1)]
final readonly class ReorderController
{
    public function __construct(private ReorderAction $reorderAction) {}

    /**
     * Reorder habits by providing habit IDs in desired order.
     */
    public function store(ReorderHabitsRequest $request, #[CurrentUser] User $user): Response
    {
        $this->reorderAction->handle($user, $request->orderedIds());

        return response()->noContent();
    }
}
