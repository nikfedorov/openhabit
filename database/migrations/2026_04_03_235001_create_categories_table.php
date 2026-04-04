<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table): void {
            $table->id();
            $table->foreignUuid('user_id')
                ->constrained()->cascadeOnDelete();

            $table->json('name');
            $table->json('description')->nullable();
            $table->string('slug')->nullable()->index();

            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(1);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
