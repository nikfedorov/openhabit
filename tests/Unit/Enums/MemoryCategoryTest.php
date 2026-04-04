<?php

declare(strict_types=1);

use App\Enums\MemoryCategory;

test('has expected cases', function (): void {
    expect(MemoryCategory::cases())->toHaveCount(6);
});

test('label returns correct strings', function (MemoryCategory $case, string $expected): void {
    expect($case->label())->toBe($expected);
})->with([
    [MemoryCategory::LongTerm, 'Long-term observations'],
    [MemoryCategory::ShortTerm, 'Recent/short-term context'],
    [MemoryCategory::Challenges, 'Main challenges & struggles'],
    [MemoryCategory::Successes, 'Achievements & successes'],
    [MemoryCategory::Goals, 'Goals & aspirations'],
    [MemoryCategory::Personality, 'Personality & preferences'],
]);
