<?php

declare(strict_types=1);

namespace App\Http\Requests\Track;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class ToggleRequest extends FormRequest
{
    /**
     * @return array<string, array<int, ValidationRule|string>>
     */
    public function rules(): array
    {
        return [
            'habit_id' => ['required', 'integer'],
            'date' => ['required', 'date', 'before_or_equal:today'],
        ];
    }

    public function habitId(): int
    {
        return $this->safe()->integer('habit_id');
    }

    public function completionDate(): string
    {
        return $this->safe()->string('date')->value();
    }
}
