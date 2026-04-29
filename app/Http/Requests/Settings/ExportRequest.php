<?php

declare(strict_types=1);

namespace App\Http\Requests\Settings;

use App\Actions\ResolvePremiumStateAction;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Authorizes the export request by verifying the user has premium.
 */
final class ExportRequest extends FormRequest
{
    /**
     * Only premium users can export their data.
     */
    public function authorize(ResolvePremiumStateAction $resolvePremiumState): bool
    {
        /** @var User $user */
        $user = $this->user();

        return $resolvePremiumState->handle($user)->hasPremium;
    }

    /**
     * @return array<string, never>
     */
    public function rules(): array
    {
        return [];
    }
}
