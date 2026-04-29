<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('habit_completions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('habit_id')
                ->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')
                ->constrained()->cascadeOnDelete();

            $table->date('completed_at');

            $table->unsignedTinyInteger('current_iteration')->default(1);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['habit_id', 'completed_at']);

            $table->index(['user_id', 'completed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('habit_completions');
    }
};
