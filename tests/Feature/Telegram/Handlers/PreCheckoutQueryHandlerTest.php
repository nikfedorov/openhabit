<?php

declare(strict_types=1);

use App\Models\User;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\UpdateType;

it('approves pre-checkout query', function (): void {
    $user = User::factory()->telegramId()->create();

    resolve(Nutgram::class)
        ->comingFrom($user)
        ->hearUpdateType(UpdateType::PRE_CHECKOUT_QUERY, [
            'id' => 'test_query_id',
            'currency' => 'XTR',
            'total_amount' => 1299,
            'invoice_payload' => 'premium',
        ])
        ->reply()
        ->assertCalled('answerPreCheckoutQuery');
});
