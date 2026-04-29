<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();

            $table->string('name');
            $table->string('telegram_id')->unique()->nullable();
            $table->string('telegram_username')->nullable();
            $table->timestamp('telegram_bot_blocked_at')->nullable();
            $table->timestamp('telegram_user_deleted_at')->nullable();

            $table->string('email')->unique()->nullable();
            $table->string('password')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('is_admin')->default(false);
            $table->timestamp('subscription_expires_at')->nullable();

            $table->string('theme')->default('system');
            $table->string('timezone')->default('UTC');
            $table->string('locale', 10)->nullable();

            $table->time('day_starts_at')->default('03:00');
            $table->boolean('move_completed_to_end')->default(true);
            $table->date('birthdate')->nullable();

            $table->string('ai_digest_time', 5)->nullable()->default('08:00');
            $table->foreignId('ai_tone_id')->nullable()->default(1);

            $table->timestamp('last_active_at')->nullable();
            $table->timestamp('trial_banner_dismissed_at')->nullable();

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table): void {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }
};
