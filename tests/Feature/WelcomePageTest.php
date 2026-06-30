<?php

declare(strict_types=1);

test('welcome page presents the no guilt habit philosophy', function (): void {
    $response = $this->get('/');

    $response
        ->assertOk()
        ->assertSee('Every Monday you tell yourself this time will be different.')
        ->assertSee('By Thursday, one missed day already feels like failure.')
        ->assertSee('Maybe the problem is not you. Maybe it is the streak keeping score.')
        ->assertSee('Try a different way')
        ->assertSee('The habit tracker for people who')
        ->assertSee('quit habit trackers.')
        ->assertSee('No streaks. No guilt. Just steady progress.')
        ->assertSee('Progress beats perfection.')
        ->assertSee('Never quit because you missed a day.');
});
