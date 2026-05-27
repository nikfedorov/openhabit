<?php

declare(strict_types=1);

use App\Models\AiTone;
use App\Models\User;

it('requires authentication', function (): void {
    $this->patchJson('/api/settings', ['theme' => 'dark'])
        ->assertUnauthorized();
});

it('updates individual settings fields', function (array $initial, array $payload, array $dbExpected): void {
    $user = User::factory()->create($initial);

    $this->actingAs($user, 'sanctum')
        ->patchJson('/api/settings', $payload)
        ->assertOk();

    $this->assertDatabaseHas('users', ['id' => $user->id, ...$dbExpected]);
})->with([
    'theme' => [
        ['theme' => 'system'],
        ['theme' => 'dark'],
        ['theme' => 'dark'],
    ],
    'locale' => [
        ['locale' => 'en'],
        ['locale' => 'ru'],
        ['locale' => 'ru'],
    ],
    'timezone' => [
        ['timezone' => 'UTC'],
        ['timezone' => 'Europe/London'],
        ['timezone' => 'Europe/London'],
    ],
    'birthdate' => [
        ['birthdate' => null],
        ['birthdate' => '1990-05-20'],
        ['birthdate' => '1990-05-20'],
    ],
    'clear birthdate' => [
        ['birthdate' => '1990-05-20'],
        ['birthdate' => null],
        ['birthdate' => null],
    ],
    'dayStartsAt' => [
        ['day_starts_at' => '03:00:00'],
        ['dayStartsAt' => '06:00'],
        ['day_starts_at' => '06:00:00'],
    ],
    'moveCompletedToEnd' => [
        ['move_completed_to_end' => true],
        ['moveCompletedToEnd' => false],
        ['move_completed_to_end' => false],
    ],
    'aiDigestTime null' => [
        ['ai_digest_time' => '09:00'],
        ['aiDigestTime' => null],
        ['ai_digest_time' => null],
    ],
    'longTermGoal' => [
        ['long_term_goal' => null],
        ['longTermGoal' => 'Run a marathon by end of year'],
        ['long_term_goal' => 'Run a marathon by end of year'],
    ],
    'clear longTermGoal' => [
        ['long_term_goal' => 'Run a marathon by end of year'],
        ['longTermGoal' => null],
        ['long_term_goal' => null],
    ],
]);

it('updates ai digest time for premium user', function (): void {
    $user = User::factory()->premium()->create(['ai_digest_time' => null]);

    $this->actingAs($user, 'sanctum')
        ->patchJson('/api/settings', ['aiDigestTime' => '09:00'])
        ->assertOk()
        ->assertJsonPath('data.aiDigestTime', '09:00');

    $this->assertDatabaseHas('users', ['id' => $user->id, 'ai_digest_time' => '09:00']);
});

it('updates ai tone id', function (): void {
    $tone = AiTone::factory()->create();
    $user = User::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->patchJson('/api/settings', ['aiToneId' => $tone->id])
        ->assertOk()
        ->assertJsonPath('data.aiToneId', $tone->id);

    $this->assertDatabaseHas('users', ['id' => $user->id, 'ai_tone_id' => $tone->id]);
});

it('only updates provided fields', function (): void {
    $user = User::factory()->create([
        'theme' => 'light',
        'locale' => 'en',
        'move_completed_to_end' => true,
    ]);

    $this->actingAs($user, 'sanctum')
        ->patchJson('/api/settings', ['theme' => 'dark'])
        ->assertOk();

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'theme' => 'dark',
        'locale' => 'en',
        'move_completed_to_end' => true,
    ]);
});

it('rejects invalid field values', function (array $payload, string $field): void {
    $user = User::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->patchJson('/api/settings', $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with([
    'invalid theme' => [['theme' => 'rainbow'],          'theme'],
    'invalid locale' => [['locale' => 'xx'],              'locale'],
    'invalid timezone' => [['timezone' => 'Not/ATimezone'], 'timezone'],
    'bad dayStartsAt' => [['dayStartsAt' => '25:00'],      'dayStartsAt'],
    'bad aiDigestTime' => [['aiDigestTime' => 'not-a-time'], 'aiDigestTime'],
]);

it('rejects ai digest time for non-premium user', function (): void {
    $user = User::factory()->trialExpired()->create();

    $this->actingAs($user, 'sanctum')
        ->patchJson('/api/settings', ['aiDigestTime' => '09:00'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['aiDigestTime']);
});

it('rejects inactive ai tone id', function (): void {
    $tone = AiTone::factory()->create(['is_active' => false]);
    $user = User::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->patchJson('/api/settings', ['aiToneId' => $tone->id])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['aiToneId']);
});

it('rejects non-existent ai tone id', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->patchJson('/api/settings', ['aiToneId' => 99999])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['aiToneId']);
});
