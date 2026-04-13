<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RRuleFrequency;
use App\Models\Concerns\HasRRule;
use Carbon\CarbonInterface;
use Database\Factories\HabitFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

/**
 * User's habit instance created from a template or manually.
 *
 * @property-read int $id
 * @property-read string $user_id
 * @property-read int|null $category_id
 * @property-read string $name
 * @property-read string|null $description
 * @property-read bool $is_active
 * @property-read int $sort_order
 * @property-read int $iterations_required
 * @property-read string|null $rrule
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 * @property-read CarbonInterface|null $deleted_at
 * @property-read RRuleFrequency|null $frequency
 * @property-read string $human_readable
 * @property-read bool $is_franklin_virtue
 */
final class Habit extends Model
{
    /** @use HasFactory<HabitFactory> */
    use HasFactory;

    use HasRRule;
    use HasTranslations {
        HasTranslations::getTranslation as traitGetTranslation;
    }
    use SoftDeletes;

    /** @var array<int, string> */
    public array $translatable = ['name', 'description'];

    /**
     * Get a translation, falling back to any available locale.
     *
     * User-created habits are stored under one locale only.
     * This ensures they show in all locales instead of appearing empty.
     */
    public function getTranslation(string $key, string $locale, bool $useFallbackLocale = true): mixed
    {
        $translation = $this->traitGetTranslation($key, $locale, $useFallbackLocale);

        if ($translation !== '' && $translation !== null) {
            return $translation;
        }

        $translations = $this->getTranslations($key);
        foreach ($translations as $value) {
            if ($value !== '' && $value !== null) {
                return $value;
            }
        }

        return $translation;
    }

    /**
     * Delete the habit. Force deletes if no completions exist.
     */
    public function delete(): ?bool
    {
        if (! $this->isForceDeleting() && $this->completions()->doesntExist()) {
            return $this->forceDelete();
        }

        return parent::delete();
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return HasMany<HabitCompletion, $this>
     */
    public function completions(): HasMany
    {
        return $this->hasMany(HabitCompletion::class);
    }

    /**
     * @return HasMany<HabitNotification, $this>
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(HabitNotification::class);
    }

    /**
     * Whether this habit belongs to the Franklin's Virtues category.
     *
     * @return Attribute<bool, never>
     */
    protected function isFranklinVirtue(): Attribute
    {
        return Attribute::get(fn (): bool => $this->category?->is_franklin_virtues === true);
    }

    /**
     * @param  Builder<Habit>  $query
     * @return Builder<Habit>
     */
    #[Scope]
    protected function ordered(Builder $query): Builder
    {
        return $query->orderBy('sort_order');
    }

    /**
     * @param  Builder<Habit>  $query
     * @return Builder<Habit>
     */
    #[Scope]
    protected function franklinVirtues(Builder $query): Builder
    {
        return $query->whereHas('category', fn (Builder $q): Builder => $q->where('slug', Category::FRANKLIN_VIRTUES_SLUG));
    }

    /**
     * @param  Builder<Habit>  $query
     * @return Builder<Habit>
     */
    #[Scope]
    protected function excludingFranklinVirtues(Builder $query): Builder
    {
        return $query->whereDoesntHave('category', fn (Builder $q): Builder => $q->where('slug', Category::FRANKLIN_VIRTUES_SLUG));
    }

    /**
     * @param  Builder<Habit>  $query
     * @return Builder<Habit>
     */
    #[Scope]
    protected function activeOrCompletedDuring(Builder $query, CarbonInterface $from, CarbonInterface $to): Builder
    {
        return $query->withTrashed()
            ->where(function (Builder $q) use ($from, $to): void {
                $q->where(function (Builder $q): void {
                    $q->whereNull('deleted_at')->where('is_active', true);
                })->orWhereHas('completions', function (Builder $q) use ($from, $to): void {
                    $q->whereBetween('completed_at', [$from, $to]);
                });
            });
    }

    /**
     * @param  Builder<Habit>  $query
     * @return Builder<Habit>
     */
    #[Scope]
    protected function franklinVirtuesFirst(Builder $query): Builder
    {
        return $query->orderByRaw(
            'CASE WHEN category_id IN (SELECT id FROM categories WHERE slug = ?) THEN 0 ELSE 1 END',
            [Category::FRANKLIN_VIRTUES_SLUG]
        );
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'user_id' => 'string',
            'category_id' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'iterations_required' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }
}
