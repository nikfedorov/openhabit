<?php

declare(strict_types=1);

use App\Models\Invoice;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Exceptions\TelegramException;

it('warns when no invoices exist', function (): void {
    $bot = Mockery::mock(Nutgram::class);
    $this->app->instance(Nutgram::class, $bot);

    $this->artisan('app:generate-invoice-links')
        ->expectsOutputToContain('No invoices found')
        ->assertExitCode(0);
});

it('generates links for invoices', function (): void {
    config(['translatable.locales' => ['en']]);
    Invoice::factory()->create();

    $bot = Mockery::mock(Nutgram::class);
    $bot->shouldReceive('createInvoiceLink')
        ->once()
        ->andReturn('https://t.me/invoice/test');
    $this->app->instance(Nutgram::class, $bot);

    $this->artisan('app:generate-invoice-links')
        ->expectsOutputToContain('Link generated')
        ->assertExitCode(0);
});

it('handles null link result', function (): void {
    config(['translatable.locales' => ['en']]);
    Invoice::factory()->create();

    $bot = Mockery::mock(Nutgram::class);
    $bot->shouldReceive('createInvoiceLink')
        ->once()
        ->andReturn(null);
    $this->app->instance(Nutgram::class, $bot);

    $this->artisan('app:generate-invoice-links')
        ->expectsOutputToContain('Failed to generate link')
        ->assertExitCode(0);
});

it('handles connect exception', function (): void {
    config(['translatable.locales' => ['en']]);
    Invoice::factory()->create();

    $bot = Mockery::mock(Nutgram::class);
    $bot->shouldReceive('createInvoiceLink')
        ->once()
        ->andThrow(new ConnectException(
            'Connection refused',
            new Request('POST', 'test')
        ));
    $this->app->instance(Nutgram::class, $bot);

    $this->artisan('app:generate-invoice-links')
        ->expectsOutputToContain('Failed to connect to Telegram API')
        ->assertExitCode(0);
});

it('handles telegram exception', function (): void {
    config(['translatable.locales' => ['en']]);
    Invoice::factory()->create();

    $bot = Mockery::mock(Nutgram::class);
    $bot->shouldReceive('createInvoiceLink')
        ->once()
        ->andThrow(new TelegramException('Bad Request'));
    $this->app->instance(Nutgram::class, $bot);

    $this->artisan('app:generate-invoice-links')
        ->expectsOutputToContain('Telegram API error')
        ->assertExitCode(0);
});

it('generates links for multiple locales', function (): void {
    config(['translatable.locales' => ['en', 'ru']]);
    Invoice::factory()->create();

    $bot = Mockery::mock(Nutgram::class);
    $bot->shouldReceive('createInvoiceLink')
        ->twice()
        ->andReturn('https://t.me/invoice/test');
    $this->app->instance(Nutgram::class, $bot);

    $this->artisan('app:generate-invoice-links')
        ->assertExitCode(0);
});
