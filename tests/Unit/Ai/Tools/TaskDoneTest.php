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

    expect($message)->toStartWith('ok: digest_recorded')
        ->and($result->digest)->toBe('Great day overall.');
});

it('returns an error for empty digest', function (): void {
    $result = new DigestResult;
    $message = new TaskDone($result)->handle(new Request(['digest' => '   ']));

    expect($message)->toStartWith('error: empty_digest')
        ->and($result->digest)->toBeNull();
});

it('rejects a digest longer than the harness cap', function (): void {
    $result = new DigestResult;
    $long = str_repeat('a', TaskDone::MAX_DIGEST_CHARS + 1);

    $message = new TaskDone($result)->handle(new Request(['digest' => $long]));

    expect($message)->toStartWith('error: digest_too_long')
        ->and($result->digest)->toBeNull();
});

it('refuses a second call after the digest is already recorded', function (): void {
    $result = new DigestResult;
    $tool = new TaskDone($result);

    $tool->handle(new Request(['digest' => 'First.']));

    $message = $tool->handle(new Request(['digest' => 'Second.']));

    expect($message)->toStartWith('error: already_called')
        ->and($result->digest)->toBe('First.');
});

it('exposes a description and a schema with a required digest field', function (): void {
    $tool = new TaskDone(new DigestResult);

    expect($tool->description())->toContain('digest');

    $schema = $tool->schema(new JsonSchemaTypeFactory);
    expect($schema)->toHaveKey('digest');
});
