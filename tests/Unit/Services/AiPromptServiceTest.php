<?php

declare(strict_types=1);

use App\Enums\MemoryCategory;
use App\Models\AiDigest;
use App\Models\AiTone;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserMemory;
use App\Services\AiPromptService;
use Carbon\CarbonImmutable;

test('sanitize strips HTML, truncates, and passes clean content through', function (): void {
    expect(AiPromptService::sanitizeUserContent('<script>alert("xss")</script>Hello', 1000))
        ->toBe('alert("xss")Hello');

    $long = str_repeat('a', 500);
    $result = AiPromptService::sanitizeUserContent($long, 100);
    expect(mb_strlen($result))->toBe(101)
        ->and($result)->toEndWith('…');

    $clean = 'I completed my reading habit today. Feeling great about my progress!';
    expect(AiPromptService::sanitizeUserContent($clean, 1000))->toBe($clean);
});

test('parseResponse extracts digest and categorized memory updates from JSON', function (): void {
    $service = new AiPromptService();

    // Valid JSON with memory updates
    $json = json_encode([
        'digest' => 'Great job today!',
        'memory_updates' => [
            'long_term' => 'User is consistent with reading.',
            'challenges' => 'Struggles with morning exercise.',
        ],
    ], JSON_THROW_ON_ERROR);
    $parsed = $service->parseResponse($json);
    expect($parsed['digest'])->toBe('Great job today!')
        ->and($parsed['memory_updates'])->toBe([
            'long_term' => 'User is consistent with reading.',
            'challenges' => 'Struggles with morning exercise.',
        ]);

    // Empty memory updates
    $json = json_encode(['digest' => 'Just a digest.', 'memory_updates' => []], JSON_THROW_ON_ERROR);
    expect($service->parseResponse($json)['memory_updates'])->toBe([]);

    // Code-fenced JSON
    $response = "```json\n".json_encode([
        'digest' => 'Fenced response.',
        'memory_updates' => ['goals' => 'Wants to run a marathon.'],
    ], JSON_THROW_ON_ERROR)."\n```";
    $parsed = $service->parseResponse($response);
    expect($parsed['digest'])->toBe('Fenced response.')
        ->and($parsed['memory_updates'])->toBe(['goals' => 'Wants to run a marathon.']);

    // Non-JSON fallback
    $parsed = $service->parseResponse('Just a plain text response.');
    expect($parsed['digest'])->toBe('Just a plain text response.')
        ->and($parsed['memory_updates'])->toBe([]);

    // Invalid categories are filtered out
    $json = json_encode([
        'digest' => 'Test digest.',
        'memory_updates' => [
            'long_term' => 'Valid category.',
            'invalid_category' => 'Should be filtered.',
            'goals' => 'Also valid.',
        ],
    ], JSON_THROW_ON_ERROR);
    expect($service->parseResponse($json)['memory_updates'])->toBe([
        'long_term' => 'Valid category.',
        'goals' => 'Also valid.',
    ]);
});

test('buildSystemPrompt replaces placeholders and includes categorized memories', function (): void {
    $service = new AiPromptService();

    Setting::query()->upsert(
        [['key' => 'ai_system_prompt', 'value' => 'System prompt. Locale: {{LOCALE}}. Tone: {{TONE}}.{{MEMORY}}', 'type' => 'text']],
        ['key'],
        ['value'],
    );

    // With memories (all categories to ensure full coverage)
    $tone = AiTone::factory()->create(['system_instruction' => 'Be a kind coach.']);
    $userWithMemory = User::factory()->create([
        'locale' => 'en',
        'ai_tone_id' => $tone->id,
    ]);
    foreach (MemoryCategory::cases() as $category) {
        UserMemory::factory()->for($userWithMemory)->create([
            'category' => $category,
            'content' => sprintf('Content for %s.', $category->value),
        ]);
    }

    $userWithMemory->load('aiTone', 'memories');

    $prompt = $service->buildSystemPrompt($userWithMemory);
    expect($prompt)->toContain('Locale: en')
        ->and($prompt)->toContain('Tone: Be a kind coach.')
        ->and($prompt)->toContain('## What You Remember About This User');

    foreach (MemoryCategory::cases() as $category) {
        expect($prompt)->toContain('### '.$category->label())
            ->and($prompt)->toContain(sprintf('Content for %s.', $category->value));
    }

    // Without memories
    $toneStrict = AiTone::factory()->create(['system_instruction' => 'Be strict.']);
    $userNoMemory = User::factory()->create([
        'locale' => 'ru',
        'ai_tone_id' => $toneStrict->id,
    ]);
    $userNoMemory->load('aiTone', 'memories');

    $prompt = $service->buildSystemPrompt($userNoMemory);
    expect($prompt)->not->toContain('## What You Remember About This User')
        ->and($prompt)->toContain('Locale: ru')
        ->and($prompt)->toContain('Tone: Be strict.');
});

test('buildUserPrompt includes habits, daily note, and recent digests', function (): void {
    $habitsData = [
        [
            'name' => 'Morning Run',
            'description' => 'Run 5km every morning',
            'scheduled' => true,
            'completed' => true,
            'partial' => false,
            'current_iteration' => 1,
            'iterations_required' => 1,
        ],
        [
            'name' => 'Read Book',
            'description' => null,
            'scheduled' => true,
            'completed' => false,
            'partial' => true,
            'current_iteration' => 2,
            'iterations_required' => 3,
        ],
        [
            'name' => 'Meditate',
            'description' => null,
            'scheduled' => true,
            'completed' => false,
            'partial' => false,
            'current_iteration' => 0,
            'iterations_required' => 1,
        ],
        [
            'name' => 'Unscheduled Habit',
            'description' => null,
            'scheduled' => false,
            'completed' => false,
            'partial' => false,
            'current_iteration' => 0,
            'iterations_required' => 1,
        ],
    ];

    $user = User::factory()->create();
    $digest = AiDigest::factory()->for($user)->forDate(now()->subDays(2)->toDateString())->create([
        'content' => 'Previous digest content.',
    ]);

    $date = CarbonImmutable::parse('2026-03-26');
    $service = new AiPromptService();

    // Without daily note
    $prompt = $service->buildUserPrompt($habitsData, null, collect(), $date);
    expect($prompt)->toContain('Morning Run')
        ->and($prompt)->toContain('Done')
        ->and($prompt)->toContain('Run 5km every morning')
        ->and($prompt)->toContain('Partial (2/3)')
        ->and($prompt)->toContain('Meditate')
        ->and($prompt)->toContain('Missed')
        ->and($prompt)->toContain('Completion: 1/3 (33%)')
        ->and($prompt)->not->toContain('Unscheduled Habit')
        ->and($prompt)->not->toContain('## Daily Note');

    // With daily note and recent digests
    $prompt = $service->buildUserPrompt([], 'Had a great productive day!', collect([$digest]), $date);
    expect($prompt)->toContain('## Daily Note:')
        ->and($prompt)->toContain('Had a great productive day!')
        ->and($prompt)->toContain('## Recent digest history')
        ->and($prompt)->toContain('Previous digest content.');
});

test('getRecentDigests returns up to 5 in chronological order', function (): void {
    $user = User::factory()->create();

    for ($i = 6; $i >= 1; $i--) {
        AiDigest::factory()->for($user)->forDate(now()->subDays($i)->toDateString())->create([
            'content' => 'Digest '.$i,
        ]);
    }

    $service = new AiPromptService();
    $digests = $service->getRecentDigests($user);

    expect($digests)->toHaveCount(5)
        ->and($digests->first()->content)->toBe('Digest 5')
        ->and($digests->last()->content)->toBe('Digest 1');
});
