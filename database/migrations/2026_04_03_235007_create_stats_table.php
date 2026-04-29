<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stats', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()->cascadeOnDelete();

            $table->string('period');
            $table->date('period_start');

            $table->unsignedInteger('planned_count')->default(0);
            $table->unsignedInteger('completed_count')->default(0);

            $table->timestamps();

            $table->unique(['user_id', 'period', 'period_start']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stats');
    }
};
