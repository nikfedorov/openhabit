<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Actions\Settings\UpdateUserSettingsAction;
use App\Http\Requests\Settings\UpdateSettingsRequest;
use App\Http\Resources\UserSettingResource;
use App\Models\User;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Container\Attributes\CurrentUser;

/**
 * Update the authenticated user's settings.
 */
#[Group('Settings', weight: 2)]
final readonly class UpdateController
{
    public function __construct(private UpdateUserSettingsAction $updateUserSettings) {}

    /**
     * Update one or more user settings.
     */
    public function update(UpdateSettingsRequest $request, #[CurrentUser] User $user): UserSettingResource
    {
        $this->updateUserSettings->handle($user, $request->settingsData());

        return UserSettingResource::make($user->fresh());
    }
}
