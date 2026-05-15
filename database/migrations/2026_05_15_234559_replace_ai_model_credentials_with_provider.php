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
        Schema::table('ai_models', function (Blueprint $table): void {
            $table->string('provider')->after('slug')->default('openrouter');
        });

        // Backfill all existing rows to OpenRouter.
        DB::table('ai_models')->update(['provider' => 'openrouter']);

        Schema::table('ai_models', function (Blueprint $table): void {
            $table->dropColumn(['base_url', 'api_key']);
        });
    }

    public function down(): void
    {
        Schema::table('ai_models', function (Blueprint $table): void {
            $table->string('base_url')->nullable();
            $table->text('api_key')->nullable();
        });

        Schema::table('ai_models', function (Blueprint $table): void {
            $table->dropColumn('provider');
        });
    }
};
