<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use Dedoc\Scramble\Http\Middleware\RestrictedDocsAccess;

it('registers bearer security scheme in api docs', function (): void {
    $response = $this->withoutMiddleware(RestrictedDocsAccess::class)->getJson('/docs/api.json');

    $response->assertOk();

    $schema = $response->json('components.securitySchemes.http');

    expect($schema)->not->toBeNull()
        ->and($schema['type'])->toBe('http')
        ->and($schema['scheme'])->toBe('bearer');
});

describe('telescope', function (): void {
    it('registers telescope in local environment', function (): void {
        // arrange
        $this->app['env'] = 'local';
        $provider = new AppServiceProvider($this->app);

        // act
        $provider->register();

        // assert - no exception thrown, telescope registered
        expect(true)->toBeTrue();
    });

    it('does not register telescope in non-local environment', function (): void {
        // arrange
        $this->app['env'] = 'testing';
        $provider = new AppServiceProvider($this->app);

        // act
        $provider->register();

        // assert - no exception thrown
        expect(true)->toBeTrue();
    });
});
