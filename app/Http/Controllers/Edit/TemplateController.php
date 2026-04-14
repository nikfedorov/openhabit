<?php

declare(strict_types=1);

namespace App\Http\Controllers\Edit;

use App\Actions\Edit\GetUserHabitsAction;
use App\Http\Resources\Edit\EditHabitResource;
use App\Models\HabitTemplate;
use App\Models\User;
use App\Services\HabitTemplateService;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Copy habit templates.
 */
#[Group('Edit', weight: 1)]
final readonly class TemplateController
{
    public function __construct(
        private HabitTemplateService $templateService,
        private GetUserHabitsAction $getUserHabits,
    ) {}

    /**
     * Copy a template to the current user.
     *
     * Creates a new habit from the given template and returns
     * the updated habit list in edit format.
     */
    public function store(HabitTemplate $habitTemplate, #[CurrentUser] User $user): AnonymousResourceCollection
    {
        $this->templateService->copyTemplateToUser($user, $habitTemplate);

        return EditHabitResource::collection($this->getUserHabits->handle($user));
    }
}
