<?php

declare(strict_types=1);

use App\Enums\RRuleFrequency;

test('has expected cases', function (): void {
    expect(RRuleFrequency::cases())->toHaveCount(4);
});

test('getLabel returns translated strings', function (RRuleFrequency $case): void {
    expect($case->getLabel())->toBeString()->not->toBeEmpty();
})->with(RRuleFrequency::cases());

test('getColor returns valid colors', function (RRuleFrequency $case, string $expected): void {
    expect($case->getColor())->toBe($expected);
})->with([
    [RRuleFrequency::Daily, 'gray'],
    [RRuleFrequency::Weekly, 'info'],
    [RRuleFrequency::Monthly, 'warning'],
    [RRuleFrequency::Yearly, 'success'],
]);
