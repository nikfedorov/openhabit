<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use App\Models\Concerns\HasRRule;
use Database\Factories\HabitTemplateFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

/**
 * @property-read int $id
 * @property-read int $category_template_id
 * @property-read string $name
 * @property-read string|null $description
 * @property-read string|null $rrule
 * @property-read int $iterations_required
 * @property-read bool $is_active
 * @property-read bool $copy_by_default
 * @property-read bool $show_in_templates
 * @property-read int $sort_order
 */
#[WithoutTimestamps]
final class HabitTemplate extends Model
{
    /** @use HasFactory<HabitTemplateFactory> */
    use HasFactory;

    use HasRRule;
    use HasTranslations;

    /** @var array<int, string> */
    public array $translatable = ['name', 'description'];

    /**
     * Convert to a habit-creation array.
     *
     * Copies all translations so the habit works in every locale.
     *
     * @return array{name: array<string, string>, description: array<string, string|null>, sort_order: int, rrule: string|null, iterations_required: int}
     */
    public function toHabitArray(): array
    {
        /** @var array<string, string> $name */
        $name = $this->getTranslations('name');

        /** @var array<string, string|null> $description */
        $description = $this->getTranslations('description');

        return [
            'name' => $name,
            'description' => $description,
            'sort_order' => $this->sort_order,
            'rrule' => $this->rrule,
            'iterations_required' => $this->iterations_required,
        ];
    }

    /**
     * @return BelongsTo<CategoryTemplate, $this>
     */
    public function categoryTemplate(): BelongsTo
    {
        return $this->belongsTo(CategoryTemplate::class);
    }

    /**
     * @param  Builder<HabitTemplate>  $query
     * @return Builder<HabitTemplate>
     */
    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<HabitTemplate>  $query
     * @return Builder<HabitTemplate>
     */
    #[Scope]
    protected function copyByDefault(Builder $query): Builder
    {
        return $query->where('copy_by_default', true);
    }

    /**
     * @param  Builder<HabitTemplate>  $query
     * @return Builder<HabitTemplate>
     */
    #[Scope]
    protected function showInTemplates(Builder $query): Builder
    {
        return $query->where('show_in_templates', true);
    }

    /**
     * @param  Builder<HabitTemplate>  $query
     * @return Builder<HabitTemplate>
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
            'category_template_id' => 'integer',
            'iterations_required' => 'integer',
            'is_active' => 'boolean',
            'copy_by_default' => 'boolean',
            'show_in_templates' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
