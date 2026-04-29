<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_templates', function (Blueprint $table): void {
            $table->id();

            $table->json('name');
            $table->json('description')->nullable();
            $table->string('slug')->unique();
            $table->string('icon')->nullable();

            $table->boolean('is_active')->default(true);
            $table->boolean('copy_by_default')->default(true);
            $table->unsignedInteger('sort_order')->default(1);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_templates');
    }
};
