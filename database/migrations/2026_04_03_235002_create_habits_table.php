<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('habits', function (Blueprint $table): void {
            $table->id();
            $table->foreignUuid('user_id')
                ->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')
                ->nullable()
                ->constrained()->nullOnDelete();

            $table->json('name');
            $table->json('description')->nullable();

            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(1);

            $table->unsignedTinyInteger('iterations_required')->default(1);

            $table->string('rrule')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('habits');
    }
};
