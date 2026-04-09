<?php

declare(strict_types=1);

namespace App\Http\Requests\View;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class IndexRequest extends FormRequest
{
    /**
     * @return array<string, array<int, ValidationRule|string>>
     */
    public function rules(): array
    {
        return [
            'tab' => ['nullable', 'string', 'in:week,year,life'],
            'week' => ['nullable', 'date'],
            'year' => ['nullable', 'integer', 'min:0', 'max:79'],
        ];
    }

    public function selectedTab(): string
    {
        $tab = $this->safe()->string('tab')->value();

        return $tab !== '' ? $tab : 'week';
    }

    public function selectedWeek(): ?string
    {
        $week = $this->safe()->string('week')->value();

        return $week !== '' ? $week : null;
    }

    public function selectedYear(): ?int
    {
        /** @var int|null $year */
        $year = $this->safe()['year'] ?? null;

        return $year !== null ? (int) $year : null;
    }
}
