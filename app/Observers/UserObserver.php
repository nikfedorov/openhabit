<?php

declare(strict_types=1);

namespace App\Observers;

use App\Jobs\SetTelegramMenuButtonJob;
use App\Models\AiTone;
use App\Models\User;
use App\Services\HabitTemplateService;

final readonly class UserObserver
{
    public function __construct(private HabitTemplateService $templateService) {}

    public function creating(User $user): void
    {
        if ($user->ai_tone_id === null) {
            $user->forceFill(['ai_tone_id' => AiTone::defaultId()]);
        }
    }

    public function created(User $user): void
    {
        $this->templateService->applyTemplatesToUser($user);

        if ($user->telegram_id !== null) {
            dispatch(new SetTelegramMenuButtonJob($user->id));
        }
    }
}
