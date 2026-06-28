<?php

declare(strict_types=1);

test('welcome page presents the no guilt habit philosophy', function (): void {
    $response = $this->get('/');

    $response
        ->assertOk()
        ->assertSee('The habit tracker for people who')
        ->assertSee('quit habit trackers.')
        ->assertSee('No streaks. No guilt. Just steady progress.')
        ->assertSee('Maybe the problem is not you. Maybe it is the streak.')
        ->assertSee('Progress beats perfection.')
        ->assertSee('Never quit because you missed a day.');
});
