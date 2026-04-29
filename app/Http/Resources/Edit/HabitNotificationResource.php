<?php

declare(strict_types=1);

namespace App\Http\Resources\Edit;

use App\Models\HabitNotification;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A scheduled reminder notification for a habit.
 *
 * @mixin HabitNotification
 */
final class HabitNotificationResource extends JsonResource
{
    /**
     * @return array{time: string, is_active: bool}
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * Time of day for the notification in HH:MM format.
             *
             * @var string
             *
             * @example "08:00"
             */
            'time' => $this->time,

            /**
             * Whether this notification is currently enabled.
             *
             * @var bool
             */
            'is_active' => $this->is_active,
        ];
    }
}
