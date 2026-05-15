<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Database\Factories\AiModelFactory;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Ai\Enums\Lab;

/**
 * @property-read int $id
 * @property-read string $slug
 * @property-read string $name
 * @property-read Lab $provider
 * @property-read int $priority
 * @property-read bool $is_active
 * @property CarbonInterface|null $disabled_until
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 */
#[Table(name: 'ai_models')]
final class AiModel extends Model
{
    /** @use HasFactory<AiModelFactory> */
    use HasFactory;

    /**
     * Get all active models ordered by priority.
     *
     * @return Collection<int, AiModel>
     */
    public static function getOrdered(): Collection
    {
        return self::query()
            ->where('is_active', true)
            ->where(function (Builder $query): void {
                $query->whereNull('disabled_until')
                    ->orWhere('disabled_until', '<=', now());
            })
            ->orderBy('priority')
            ->get();
    }

    /**
     * Temporarily disable this model for the given number of hours.
     */
    public function disableFor(int $hours = 24): void
    {
        $this->disabled_until = now()->addHours($hours);
        $this->save();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'priority' => 'integer',
            'is_active' => 'boolean',
            'provider' => Lab::class,
            'disabled_until' => 'datetime',
        ];
    }
}
