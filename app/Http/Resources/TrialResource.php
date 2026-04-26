<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Data\PremiumState;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Trial banner and premium modal data for a user.
 *
 * @property-read PremiumState $resource
 */
final class TrialResource extends JsonResource
{
    /**
     * @return array{
     *     hasPremium: bool,
     *     isTrialing: bool,
     *     shouldShowBanner: bool,
     *     bannerText: string,
     *     invoiceLink: string|null,
     *     learnMore: string,
     *     featuresTitle: string,
     *     featuresSubtitle: string,
     *     featureNotifications: string,
     *     featureAiDigest: string,
     *     featureExport: string,
     *     upgradeLabel: string,
     *     openInTelegramLabel: string,
     * }
     */
    public function toArray(Request $request): array
    {
        $shouldShowBanner = $this->resource->shouldShowBanner;

        return [
            /**
             * Whether the user has an active premium subscription or is within the trial period.
             */
            'hasPremium' => $this->resource->hasPremium,

            /**
             * Whether the user is currently in the free trial (not a paying subscriber).
             * Used to decide when to show the premium upsell.
             */
            'isTrialing' => $this->resource->isTrialing,

            /**
             * Whether to show the trial banner in the UI.
             */
            'shouldShowBanner' => $shouldShowBanner,

            /**
             * Text to show in the trial banner, e.g. "Your trial expires in 3 days".
             *
             * @var string
             *
             * @example "Your trial expires in 3 days"
             */
            'bannerText' => $shouldShowBanner
                ? __('app.trial_remaining', ['remaining' => $this->resource->trialRemaining])
                : '',

            /**
             * Premium invoice link for the current locale, if available.
             * Used in the trial banner and premium modal.
             */
            'invoiceLink' => Invoice::premiumLink(),

            /**
             * Localization strings for the premium modal.
             *
             * @var string
             */
            'learnMore' => __('app.learn_more'),

            /**
             * Localization strings for the premium modal features list.
             *
             * @var string
             */
            'featuresTitle' => __('app.premium_features_title'),

            /**
             * Localization strings for the premium modal features list.
             *
             * @var string
             */
            'featuresSubtitle' => __('app.premium_features_subtitle'),

            /**
             * Localization strings for the premium modal features list.
             *
             * @var string
             */
            'featureNotifications' => __('app.premium_feature_notifications'),

            /**
             * Localization strings for the premium modal features list.
             *
             * @var string
             */
            'featureAiDigest' => __('app.premium_feature_ai_digest'),

            /**
             * Localization strings for the premium modal features list.
             *
             * @var string
             */
            'featureExport' => __('app.premium_feature_export'),

            /**
             * Localization strings for the premium modal upgrade button.
             *
             * @var string
             */
            'upgradeLabel' => __('app.upgrade'),

            /**
             * Localization strings for the premium modal button to open the Telegram channel.
             *
             * @var string
             */
            'openInTelegramLabel' => __('app.upgrade_open_telegram'),
        ];
    }
}
