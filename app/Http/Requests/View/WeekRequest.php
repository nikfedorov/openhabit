<?php

declare(strict_types=1);

namespace App\Http\Requests\View;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class WeekRequest extends FormRequest
{
    /**
     * @return array<string, array<int, ValidationRule|string>>
     */
    public function rules(): array
    {
        return [
            /**
             * Start date of the week to display.
             *
             * @example "2026-04-06"
             */
            'week' => ['nullable', 'date'],
        ];
    }

    public function selectedWeek(): ?string
    {
        $week = $this->safe()->string('week')->value();

        return $week !== '' ? $week : null;
    }
}
