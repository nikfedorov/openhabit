<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('habit_templates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('category_template_id')
                ->nullable()
                ->constrained()->nullOnDelete();

            $table->json('name');
            $table->json('description')->nullable();

            $table->boolean('is_active')->default(true);
            $table->boolean('copy_by_default')->default(true);
            $table->boolean('show_in_templates')->default(true);
            $table->unsignedInteger('sort_order')->default(1);

            $table->unsignedTinyInteger('iterations_required')->default(1);

            $table->string('rrule')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('habit_templates');
    }
};
