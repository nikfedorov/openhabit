<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\StatPeriod;
use Carbon\CarbonInterface;
use Database\Factories\StatFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property string $user_id
 * @property StatPeriod $period
 * @property CarbonInterface|string $period_start
 * @property int $planned_count
 * @property int $completed_count
 * @property-read float $completion_rate
 * @property-read int $intensity_level
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
final class Stat extends Model
{
    /** @use HasFactory<StatFactory> */
    use HasFactory;

    /**
     * Calculate the intensity level based on completion ratio.
     */
    public static function calculateIntensity(int $completed, int $total): int
    {
        if ($total === 0) {
            return 0;
        }

        $ratio = $completed / $total;

        return match (true) {
            $ratio >= 0.8 => 4,
            $ratio >= 0.5 => 3,
            $ratio >= 0.25 => 2,
            $ratio > 0 => 1,
            default => 0,
        };
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the completion rate as a percentage.
     *
     * @return Attribute<float, never>
     */
    protected function completionRate(): Attribute
    {
        return Attribute::get(function (): float {
            if ($this->planned_count === 0) {
                return 0.0;
            }

            return round(($this->completed_count / $this->planned_count) * 100, 1);
        });
    }

    /**
     * Get the intensity level (0-4).
     *
     * @return Attribute<int, never>
     */
    protected function intensityLevel(): Attribute
    {
        return Attribute::get(fn (): int => self::calculateIntensity($this->completed_count, $this->planned_count));
    }

    /**
     * @param  Builder<Stat>  $query
     * @return Builder<Stat>
     */
    #[Scope]
    protected function daily(Builder $query): Builder
    {
        return $query->where('period', StatPeriod::Daily);
    }

    /**
     * @param  Builder<Stat>  $query
     * @return Builder<Stat>
     */
    #[Scope]
    protected function weekly(Builder $query): Builder
    {
        return $query->where('period', StatPeriod::Weekly);
    }

    /**
     * @param  Builder<Stat>  $query
     * @return Builder<Stat>
     */
    #[Scope]
    protected function yearly(Builder $query): Builder
    {
        return $query->where('period', StatPeriod::Yearly);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'user_id' => 'string',
            'period' => StatPeriod::class,
            'period_start' => 'date',
            'completed_count' => 'integer',
            'planned_count' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
