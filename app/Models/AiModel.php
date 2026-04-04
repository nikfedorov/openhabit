<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\AiModelFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property-read string $slug
 * @property-read string $name
 * @property-read int $priority
 * @property-read bool $is_active
 * @property-read bool $is_free
 */
final class AiModel extends Model
{
    /** @use HasFactory<AiModelFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $table = 'ai_models';

    /**
     * Get all active models ordered by priority.
     *
     * @return Collection<int, AiModel>
     */
    public static function getOrdered(): Collection
    {
        return self::query()
            ->where('is_active', true)
            ->orderBy('priority')
            ->get();
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
            'is_free' => 'boolean',
        ];
    }
}
