<?php

declare(strict_types=1);

use Dedoc\Scramble\Http\Middleware\RestrictedDocsAccess;

it('registers bearer security scheme in api docs', function (): void {
    $response = $this->withoutMiddleware(RestrictedDocsAccess::class)->getJson('/docs/api.json');

    $response->assertOk();

    $schema = $response->json('components.securitySchemes.http');

    expect($schema)->not->toBeNull()
        ->and($schema['type'])->toBe('http')
        ->and($schema['scheme'])->toBe('bearer');
});
