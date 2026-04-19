<?php

declare(strict_types=1);

namespace App\Http\Requests\Edit;

use App\Actions\ResolvePremiumStateAction;
use App\Http\Concerns\AuthorizesHabitAccess;
use App\Models\Habit;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

final class StoreHabitRequest extends FormRequest
{
    use AuthorizesHabitAccess;

    /**
     * For updates, the user must own the habit and it must not be a Franklin virtue.
     * For creation (no habit bound), always allowed.
     */
    public function authorize(): bool
    {
        if (! $this->route('habit') instanceof Habit) {
            return true;
        }

        return $this->authorizeHabitAccess();
    }

    /**
     * @return array<string, array<int, ValidationRule|string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:1', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'frequency' => ['required', 'string', 'in:DAILY,WEEKLY,MONTHLY'],
            'iterations_required' => ['required', 'integer', 'min:1', 'max:99'],
            'is_active' => ['required', 'boolean'],
            'weekly_days' => ['array'],
            'weekly_days.*' => ['integer', 'min:0', 'max:6'],
            'monthly_days' => ['array'],
            'monthly_days.*' => ['integer', 'min:1', 'max:31'],
            'monthly_mode' => ['required', 'string', 'in:day,position'],
            'monthly_position' => ['required', 'integer', 'in:1,2,3,4,-1'],
            'monthly_weekday' => ['required', 'integer', 'min:0', 'max:6'],
            'notifications' => ['array', 'max:10'],
            'notifications.*.time' => ['required', 'string', 'regex:/^\d{2}:(00|05|10|15|20|25|30|35|40|45|50|55)$/'],
            'notifications.*.is_active' => ['required', 'boolean'],
        ];
    }

    /**
     * Validate that adding more than one notification requires a premium subscription.
     *
     * @return array<int, Closure>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $user = $this->user();

                /** @var array<int, mixed> $notifications */
                $notifications = $this->input('notifications', []);

                if (! $user instanceof User || count($notifications) <= 1) {
                    return;
                }

                if (! resolve(ResolvePremiumStateAction::class)->handle($user)->hasPremium) {
                    $validator->errors()->add('notifications', __('This feature requires a premium subscription.'));
                }
            },
        ];
    }

    /**
     * @return array{name: string, description: ?string, frequency: string, iterations_required: int, is_active: bool, weekly_days: array<int>, monthly_days: array<int>, monthly_mode: string, monthly_position: int, monthly_weekday: int, notifications: array<int, array{time: string, is_active: bool}>}
     */
    public function habitData(): array
    {
        $safe = $this->safe();

        /** @var ?string $description */
        $description = $safe['description'] ?? null;

        /** @var array<int> $weeklyDays */
        $weeklyDays = $safe['weekly_days'] ?? [];

        /** @var array<int> $monthlyDays */
        $monthlyDays = $safe['monthly_days'] ?? [];

        /** @var array<int, array{time: string, is_active: bool}> $notifications */
        $notifications = $safe['notifications'] ?? [];

        return [
            'name' => $safe->string('name')->value(),
            'description' => $description,
            'frequency' => $safe->string('frequency')->value(),
            'iterations_required' => $safe->integer('iterations_required'),
            'is_active' => (bool) $safe['is_active'],
            'weekly_days' => $weeklyDays,
            'monthly_days' => $monthlyDays,
            'monthly_mode' => $safe->string('monthly_mode')->value(),
            'monthly_position' => $safe->integer('monthly_position'),
            'monthly_weekday' => $safe->integer('monthly_weekday'),
            'notifications' => $notifications,
        ];
    }
}
