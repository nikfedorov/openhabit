<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Queue;
use SergiX44\Nutgram\Exception\InvalidDataException;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Web\WebAppData;
use SergiX44\Nutgram\Telegram\Web\WebAppUser;

beforeEach(function (): void {
    Queue::fake();
});

function makeMiniAppWebAppUser(int $id, string $firstName, ?string $lastName = null): WebAppUser
{
    $user = new WebAppUser;
    $user->id = $id;
    $user->is_bot = false;
    $user->first_name = $firstName;
    $user->last_name = $lastName;

    return $user;
}

function makeMiniAppWebAppData(WebAppUser $user): WebAppData
{
    $data = new WebAppData;
    $data->user = $user;
    $data->auth_date = Date::now()->toDateTime();
    $data->hash = 'fake_hash';

    return $data;
}

it('loads telegram miniapp page', function (): void {
    $this->get('/telegram-miniapp')
        ->assertOk()
        ->assertSee('id="loading"', false);
});

it('rejects missing init data', function (): void {
    $this->postJson('/api/auth/telegram')
        ->assertUnauthorized()
        ->assertJson(['error' => 'Missing init data']);
});

it('rejects invalid init data', function (): void {
    $nutgram = Mockery::mock(Nutgram::class);
    $nutgram->shouldReceive('validateWebAppData')
        ->once()
        ->andThrow(new InvalidDataException('Invalid'));

    $this->app->instance(Nutgram::class, $nutgram);

    $this->postJson('/api/auth/telegram', ['init_data' => 'bad_data'])
        ->assertUnauthorized()
        ->assertJson(['error' => 'Invalid init data']);
});

it('rejects data without user', function (): void {
    $webAppData = new WebAppData;
    $webAppData->user = null;
    $webAppData->auth_date = Date::now()->toDateTime();
    $webAppData->hash = 'fake_hash';

    $nutgram = Mockery::mock(Nutgram::class);
    $nutgram->shouldReceive('validateWebAppData')
        ->once()
        ->andReturn($webAppData);

    $this->app->instance(Nutgram::class, $nutgram);

    $this->postJson('/api/auth/telegram', ['init_data' => 'valid_but_no_user'])
        ->assertUnauthorized()
        ->assertJson(['error' => 'User data not found']);
});

it('creates user and returns a token', function (): void {
    $webAppUser = makeMiniAppWebAppUser(123_456_789, 'John', 'Doe');
    $webAppData = makeMiniAppWebAppData($webAppUser);

    $nutgram = Mockery::mock(Nutgram::class);
    $nutgram->shouldReceive('validateWebAppData')
        ->once()
        ->andReturn($webAppData);

    $this->app->instance(Nutgram::class, $nutgram);

    $this->postJson('/api/auth/telegram', ['init_data' => 'fake_init_data'])
        ->assertOk()
        ->assertJsonStructure(['success', 'token'])
        ->assertJson(['success' => true]);

    $user = User::query()->where('telegram_id', '123456789')->first();
    expect($user)->not->toBeNull()
        ->and($user->name)->toBe('John Doe')
        ->and($user->tokens()->count())->toBe(1);
});

it('sets telegram_authenticated session flag when session is available', function (): void {
    $webAppUser = makeMiniAppWebAppUser(111_111_111, 'Session', 'User');
    $webAppData = makeMiniAppWebAppData($webAppUser);

    $nutgram = Mockery::mock(Nutgram::class);
    $nutgram->shouldReceive('validateWebAppData')
        ->once()
        ->andReturn($webAppData);

    $this->app->instance(Nutgram::class, $nutgram);

    $this->withHeader('Origin', 'http://localhost')
        ->postJson('/api/auth/telegram', ['init_data' => 'fake_init_data'])
        ->assertOk()
        ->assertJson(['success' => true]);
});

it('logs in existing user without creating a duplicate', function (): void {
    User::factory()->telegram()->create([
        'telegram_id' => '987654321',
        'name' => 'Existing User',
    ]);

    $webAppUser = makeMiniAppWebAppUser(987_654_321, 'Existing', 'User');
    $webAppData = makeMiniAppWebAppData($webAppUser);

    $nutgram = Mockery::mock(Nutgram::class);
    $nutgram->shouldReceive('validateWebAppData')
        ->once()
        ->andReturn($webAppData);

    $this->app->instance(Nutgram::class, $nutgram);

    $this->postJson('/api/auth/telegram', ['init_data' => 'fake_init_data'])
        ->assertOk()
        ->assertJson(['success' => true])
        ->assertJsonStructure(['token']);

    expect(User::query()->where('telegram_id', '987654321')->count())->toBe(1);
});
