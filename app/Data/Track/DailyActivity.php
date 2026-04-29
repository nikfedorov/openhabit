<?php

declare(strict_types=1);

namespace App\Data\Track;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Daily activity data point for the track heatmap.
 *
 * @implements Arrayable<string, mixed>
 */
final readonly class DailyActivity implements Arrayable
{
    public function __construct(
        public string $date,
        public float $percentage,
        public int $completed,
        public int $total,
        public int $intensity,
    ) {}

    /**
     * @param  array{date: string, percentage: float, completed: int, total: int, intensity: int}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            date: $data['date'],
            percentage: $data['percentage'],
            completed: $data['completed'],
            total: $data['total'],
            intensity: $data['intensity'],
        );
    }

    /**
     * @return array{date: string, percentage: float, completed: int, total: int, intensity: int}
     */
    public function toArray(): array
    {
        return [
            'date' => $this->date,
            'percentage' => $this->percentage,
            'completed' => $this->completed,
            'total' => $this->total,
            'intensity' => $this->intensity,
        ];
    }
}
