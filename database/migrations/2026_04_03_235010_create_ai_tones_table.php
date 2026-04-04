<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_tones', function (Blueprint $table): void {
            $table->id();
            $table->string('slug')->unique();
            $table->json('name');
            $table->json('description');
            $table->string('icon');
            $table->text('system_instruction');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->foreign('ai_tone_id')->references('id')->on('ai_tones');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_tones');
    }
};
