<?php

declare(strict_types=1);

use App\Models\Setting;

it('seeds the database', function (): void {
    // act
    $this->artisan('db:seed');

    // assert
    expect(Setting::query()->count())->toBeGreaterThan(1);
});
