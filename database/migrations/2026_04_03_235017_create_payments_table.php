<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('currency');
            $table->unsignedInteger('total_amount');
            $table->string('invoice_payload');
            $table->timestamp('subscription_expiration_date')->nullable();
            $table->boolean('is_recurring')->nullable();
            $table->boolean('is_first_recurring')->nullable();
            $table->string('telegram_payment_charge_id')->unique();
            $table->string('provider_payment_charge_id');
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
