<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Date;
use SergiX44\Nutgram\Exception\InvalidDataException;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Web\WebAppData;
use SergiX44\Nutgram\Telegram\Web\WebAppUser;

function makeWebAppUser(int $id, string $firstName, ?string $lastName = null): WebAppUser
{
    $user = new WebAppUser;
    $user->id = $id;
    $user->is_bot = false;
    $user->first_name = $firstName;
    $user->last_name = $lastName;

    return $user;
}

function makeWebAppData(WebAppUser $user): WebAppData
{
    $data = new WebAppData;
    $data->user = $user;
    $data->auth_date = Date::now()->toDateTime();
    $data->hash = 'fake_hash';

    return $data;
}

test('telegram miniapp page loads', function (): void {
    $this->get('/telegram-miniapp')
        ->assertOk()
        ->assertSee('Loading...');
});

test('telegram miniapp auth rejects missing init data', function (): void {
    $this->postJson('/telegram-miniapp/auth')
        ->assertUnauthorized()
        ->assertJson(['error' => 'Missing init data']);
});

test('telegram miniapp auth rejects invalid init data', function (): void {
    $nutgram = Mockery::mock(Nutgram::class);
    $nutgram->shouldReceive('validateWebAppData')
        ->once()
        ->andThrow(new InvalidDataException('Invalid'));

    $this->app->instance(Nutgram::class, $nutgram);

    $this->postJson('/telegram-miniapp/auth', [], [
        'X-Telegram-Init-Data' => 'bad_data',
    ])
        ->assertUnauthorized()
        ->assertJson(['error' => 'Invalid init data']);
});

test('telegram miniapp auth rejects data without user', function (): void {
    $webAppData = new WebAppData;
    $webAppData->user = null;
    $webAppData->auth_date = Date::now()->toDateTime();
    $webAppData->hash = 'fake_hash';

    $nutgram = Mockery::mock(Nutgram::class);
    $nutgram->shouldReceive('validateWebAppData')
        ->once()
        ->andReturn($webAppData);

    $this->app->instance(Nutgram::class, $nutgram);

    $this->postJson('/telegram-miniapp/auth', [], [
        'X-Telegram-Init-Data' => 'valid_but_no_user',
    ])
        ->assertUnauthorized()
        ->assertJson(['error' => 'User data not found']);
});

test('telegram miniapp auth creates user and logs in', function (): void {
    $webAppUser = makeWebAppUser(123456789, 'John', 'Doe');
    $webAppData = makeWebAppData($webAppUser);

    $nutgram = Mockery::mock(Nutgram::class);
    $nutgram->shouldReceive('validateWebAppData')
        ->once()
        ->andReturn($webAppData);

    $this->app->instance(Nutgram::class, $nutgram);

    $this->postJson('/telegram-miniapp/auth', [], [
        'X-Telegram-Init-Data' => 'fake_init_data',
    ])
        ->assertOk()
        ->assertJson([
            'success' => true,
            'redirect' => route('dashboard'),
        ]);

    $this->assertAuthenticated();

    $user = User::query()->where('telegram_id', '123456789')->first();
    expect($user)->not->toBeNull()
        ->and($user->name)->toBe('John Doe');
});

test('telegram miniapp auth logs in existing user', function (): void {
    $existingUser = User::factory()->telegram()->create([
        'telegram_id' => '987654321',
        'name' => 'Existing User',
    ]);

    $webAppUser = makeWebAppUser(987654321, 'Existing', 'User');
    $webAppData = makeWebAppData($webAppUser);

    $nutgram = Mockery::mock(Nutgram::class);
    $nutgram->shouldReceive('validateWebAppData')
        ->once()
        ->andReturn($webAppData);

    $this->app->instance(Nutgram::class, $nutgram);

    $this->postJson('/telegram-miniapp/auth', [], [
        'X-Telegram-Init-Data' => 'fake_init_data',
    ])
        ->assertOk()
        ->assertJson(['success' => true]);

    $this->assertAuthenticatedAs($existingUser);

    expect(User::query()->where('telegram_id', '987654321')->count())->toBe(1);
});

test('dashboard requires authentication', function (): void {
    $this->get('/dashboard')
        ->assertRedirect('/telegram-miniapp');
});

test('dashboard loads for authenticated user', function (): void {
    $user = User::factory()->telegram()->create();

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->has('auth.user')
        );
});
