<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Enums\RRuleFrequency;
use App\Services\RRuleService;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * Trait for models with rrule field.
 *
 * @property string|null $rrule
 */
trait HasRRule
{
    /**
     * Get the frequency from RRule.
     *
     * @return Attribute<RRuleFrequency|null, never>
     */
    protected function frequency(): Attribute
    {
        return Attribute::get(function (): ?RRuleFrequency {
            if (blank($this->rrule)) {
                return null;
            }

            return $this->getRRuleService()->getFrequency($this->rrule);
        });
    }

    /**
     * Get human-readable description.
     *
     * @return Attribute<string, never>
     */
    protected function humanReadable(): Attribute
    {
        return Attribute::get(function (): string {
            if (blank($this->rrule)) {
                return __('habit.not_set');
            }

            return $this->getRRuleService()->getDescription($this->rrule);
        });
    }

    /**
     * Get cached RRuleService instance.
     */
    private function getRRuleService(): RRuleService
    {
        /** @var RRuleService|null $service */
        static $service = null;

        if ($service === null) {
            $service = resolve(RRuleService::class);
        }

        return $service;
    }
}
