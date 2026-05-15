<?php

declare(strict_types=1);

use App\Ai\Support\DigestResult;
use App\Ai\Tools\TaskDone;
use Illuminate\JsonSchema\JsonSchemaTypeFactory;
use Laravel\Ai\Tools\Request;

it('captures the digest into the shared result', function (): void {
    $result = new DigestResult;
    $tool = new TaskDone($result);

    $message = $tool->handle(new Request(['digest' => '  Great day overall.  ']));

    expect($message)->toBe('Done.')
        ->and($result->digest)->toBe('Great day overall.');
});

it('returns an error for empty digest', function (): void {
    $result = new DigestResult;
    $message = new TaskDone($result)->handle(new Request(['digest' => '   ']));

    expect($message)->toBe('Error: digest must not be empty.')
        ->and($result->digest)->toBeNull();
});

it('exposes a description and a schema with a required digest field', function (): void {
    $tool = new TaskDone(new DigestResult);

    expect($tool->description())->toContain('digest');

    $schema = $tool->schema(new JsonSchemaTypeFactory);
    expect($schema)->toHaveKey('digest');
});
