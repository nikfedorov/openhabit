<?php

declare(strict_types=1);

use App\Enums\SettingType;

test('has expected cases', function (): void {
    expect(SettingType::cases())->toHaveCount(5);
});

test('getLabel returns ucfirst value', function (SettingType $case): void {
    expect($case->getLabel())->toBe(ucfirst($case->value));
})->with(SettingType::cases());
