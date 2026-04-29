<?php

declare(strict_types=1);

use App\Enums\SettingType;

it('has expected cases', function (): void {
    expect(SettingType::cases())->toHaveCount(5);
});

it('returns ucfirst value', function (SettingType $case): void {
    expect($case->getLabel())->toBe(ucfirst($case->value));
})->with(SettingType::cases());
