<?php

declare(strict_types=1);

use App\Models\User;

it('seeds the database', function (): void {
    // act
    $this->artisan('db:seed');

    // assert
    expect(User::query()->count())->toBe(1);
});
