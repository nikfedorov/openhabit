<?php

declare(strict_types=1);

namespace App\Http\Requests\Edit;

use App\Http\Concerns\AuthorizesHabitAccess;
use Illuminate\Foundation\Http\FormRequest;

final class ToggleHabitRequest extends FormRequest
{
    use AuthorizesHabitAccess;

    /**
     * The user must own the habit (including Franklin virtues).
     */
    public function authorize(): bool
    {
        return $this->authorizeHabitAccess(allowFranklinVirtues: true);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }
}
