<?php

declare(strict_types=1);

use App\Enums\Theme;

it('has expected cases', function (): void {
    expect(Theme::cases())->toHaveCount(3);
});

it('returns correct strings', function (Theme $case, string $expected): void {
    expect($case->getLabel())->toBe($expected);
})->with([
    [Theme::Light, 'Light'],
    [Theme::Dark, 'Dark'],
    [Theme::System, 'System'],
]);

it('returns correct icons', function (Theme $case, string $expected): void {
    expect($case->getIcon())->toBe($expected);
})->with([
    [Theme::Light, 'sun'],
    [Theme::Dark, 'moon'],
    [Theme::System, 'computer'],
]);
