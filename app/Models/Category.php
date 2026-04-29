<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

/**
 * User's category instance created from a template or manually.
 *
 * @property-read int $id
 * @property-read int $user_id
 * @property-read string $name
 * @property-read string|null $description
 * @property-read string|null $slug
 * @property-read bool $is_active
 * @property-read int $sort_order
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 * @property-read bool $is_franklin_virtues
 */
final class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    use HasTranslations;

    /**
     * Slug identifier for Benjamin Franklin's 13 Virtues system.
     */
    public const string FRANKLIN_VIRTUES_SLUG = 'franklins-virtues';

    /** @var array<int, string> */
    public array $translatable = ['name', 'description'];

    /**
     * Get the user that owns the category.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the habits for the category.
     *
     * @return HasMany<Habit, $this>
     */
    public function habits(): HasMany
    {
        return $this->hasMany(Habit::class);
    }

    /**
     * Whether this category is the Franklin's Virtues category.
     *
     * @return Attribute<bool, never>
     */
    protected function isFranklinVirtues(): Attribute
    {
        return Attribute::get(fn (): bool => $this->slug === self::FRANKLIN_VIRTUES_SLUG);
    }

    /**
     * @param  Builder<Category>  $query
     * @return Builder<Category>
     */
    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<Category>  $query
     * @return Builder<Category>
     */
    #[Scope]
    protected function excludingFranklinVirtues(Builder $query): Builder
    {
        return $query->where('slug', '!=', self::FRANKLIN_VIRTUES_SLUG);
    }

    /**
     * @param  Builder<Category>  $query
     * @return Builder<Category>
     */
    #[Scope]
    protected function ordered(Builder $query): Builder
    {
        return $query->orderBy('sort_order');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'user_id' => 'integer',
            'slug' => 'string',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
