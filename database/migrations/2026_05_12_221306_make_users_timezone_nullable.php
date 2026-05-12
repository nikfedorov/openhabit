<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('timezone')->nullable()->default(null)->change();
        });

        // Clear the default 'UTC' for users who never customised their timezone
        // so client-side auto-detection can populate it on next visit.
        DB::table('users')->where('timezone', 'UTC')->update(['timezone' => null]);
    }

    public function down(): void
    {
        DB::table('users')->whereNull('timezone')->update(['timezone' => 'UTC']);

        Schema::table('users', function (Blueprint $table): void {
            $table->string('timezone')->default('UTC')->nullable(false)->change();
        });
    }
};
