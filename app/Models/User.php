<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property-read string $id
 * @property-read string|null $telegram_id
 * @property string|null $name
 * @property-read string|null $email
 * @property-read CarbonInterface|null $email_verified_at
 * @property-read string|null $password
 * @property string|null $locale
 * @property CarbonInterface|null $last_active_at
 * @property-read string|null $remember_token
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
final class User extends Authenticatable implements HasLocalePreference
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasUuids;
    use Notifiable;

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function preferredLocale(): string
    {
        return $this->locale ?? 'en';
    }

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'id' => 'string',
            'telegram_id' => 'string',
            'name' => 'string',
            'email' => 'string',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'locale' => 'string',
            'last_active_at' => 'datetime',
            'remember_token' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
