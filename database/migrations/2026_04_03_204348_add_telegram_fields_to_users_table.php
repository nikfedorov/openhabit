<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('telegram_id')->unique()->nullable()->after('id');
            $table->string('locale', 10)->nullable()->after('password');
            $table->timestamp('last_active_at')->nullable()->after('locale');

            // Make email/password optional for Telegram-only users
            $table->string('email')->nullable()->change();
            $table->string('password')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['telegram_id', 'locale', 'last_active_at']);
            $table->string('email')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
        });
    }
};
