<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\AiLogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property-read int $user_id
 * @property-read string $model
 * @property-read string $system_prompt
 * @property-read string $user_prompt
 * @property-read string|null $response
 * @property-read int|null $input_tokens
 * @property-read int|null $output_tokens
 * @property-read int|null $duration_ms
 * @property-read bool $is_successful
 * @property-read string|null $error
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
final class AiLog extends Model
{
    /** @use HasFactory<AiLogFactory> */
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
            'user_id' => 'integer',
            'input_tokens' => 'integer',
            'output_tokens' => 'integer',
            'duration_ms' => 'integer',
            'is_successful' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
