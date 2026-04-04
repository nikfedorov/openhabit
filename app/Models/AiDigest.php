<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\AiDigestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property-read string $user_id
 * @property-read CarbonInterface $date
 * @property-read string|null $content
 * @property-read array<string, mixed>|null $habits_data
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
final class AiDigest extends Model
{
    /** @use HasFactory<AiDigestFactory> */
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
            'date' => 'date',
            'habits_data' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
