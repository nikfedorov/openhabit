<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MemoryCategory;
use Carbon\CarbonInterface;
use Database\Factories\UserMemoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property-read string $user_id
 * @property-read MemoryCategory $category
 * @property-read string $content
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
final class UserMemory extends Model
{
    /** @use HasFactory<UserMemoryFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'user_id' => 'string',
            'category' => MemoryCategory::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
