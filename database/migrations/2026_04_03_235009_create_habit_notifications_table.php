<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('habit_notifications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('habit_id')
                ->constrained()->cascadeOnDelete();

            $table->string('time', 5);
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_notified_at')->nullable();

            $table->timestamps();

            $table->unique(['habit_id', 'time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('habit_notifications');
    }
};
