<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CategoryTemplateFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

/**
 * @property-read int $id
 * @property-read string $name
 * @property-read string|null $description
 * @property-read string|null $slug
 * @property-read string|null $icon
 * @property-read bool $is_active
 * @property-read bool $copy_by_default
 * @property-read int $sort_order
 */
final class CategoryTemplate extends Model
{
    /** @use HasFactory<CategoryTemplateFactory> */
    use HasFactory;

    use HasTranslations;

    public $timestamps = false;

    /** @var array<int, string> */
    public array $translatable = ['name', 'description'];

    /**
     * Convert to a category-creation array.
     *
     * @return array{name: string, slug: string|null}
     */
    public function toCategoryArray(): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
        ];
    }

    /**
     * @return HasMany<HabitTemplate, $this>
     */
    public function habitTemplates(): HasMany
    {
        return $this->hasMany(HabitTemplate::class);
    }

    /**
     * @param  Builder<CategoryTemplate>  $query
     * @return Builder<CategoryTemplate>
     */
    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<CategoryTemplate>  $query
     * @return Builder<CategoryTemplate>
     */
    #[Scope]
    protected function copyByDefault(Builder $query): Builder
    {
        return $query->where('copy_by_default', true);
    }

    /**
     * @param  Builder<CategoryTemplate>  $query
     * @return Builder<CategoryTemplate>
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
            'is_active' => 'boolean',
            'copy_by_default' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
