<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\AiToneFactory;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

/**
 * @property-read int $id
 * @property-read string $slug
 * @property-read string $name
 * @property-read string|null $description
 * @property-read string $system_instruction
 * @property-read int $sort_order
 * @property-read bool $is_active
 */
#[WithoutTimestamps]
final class AiTone extends Model
{
    /** @use HasFactory<AiToneFactory> */
    use HasFactory;

    use HasTranslations;

    /** @var array<int, string> */
    public array $translatable = ['name', 'description'];

    /**
     * Get the default AI tone ID.
     */
    public static function defaultId(): ?int
    {
        /** @var int|null $id */
        $id = self::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->value('id');

        return $id;
    }

    /**
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
