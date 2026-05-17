<?php

declare(strict_types=1);

use App\Enums\MemoryCategory;

it('has expected cases', function (): void {
    expect(MemoryCategory::cases())->toHaveCount(7);
});

it('returns correct strings', function (MemoryCategory $case, string $expected): void {
    expect($case->label())->toBe($expected);
})->with([
    [MemoryCategory::LongTerm, 'Long-term observations'],
    [MemoryCategory::ShortTerm, 'Recent/short-term context'],
    [MemoryCategory::Challenges, 'Main challenges & struggles'],
    [MemoryCategory::Successes, 'Achievements & successes'],
    [MemoryCategory::Goals, 'Goals & aspirations'],
    [MemoryCategory::Personality, 'Personality & preferences'],
    [MemoryCategory::CoachingLog, 'Coaching log (recent advice given, newest first)'],
]);
