<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Trial banner and premium modal data for a user.
 *
 * @property-read User $resource
 */
final class TrialResource extends JsonResource
{
    /**
     * @return array{
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
        return [
            'shouldShowBanner' => $this->resource->shouldShowTrialBanner(),
            'bannerText' => $this->resource->shouldShowTrialBanner()
                ? __('app.trial_remaining', ['remaining' => $this->resource->trialRemaining()])
                : '',
            'invoiceLink' => Invoice::premiumLink(),
            'learnMore' => __('app.learn_more'),
            'featuresTitle' => __('app.premium_features_title'),
            'featuresSubtitle' => __('app.premium_features_subtitle'),
            'featureNotifications' => __('app.premium_feature_notifications'),
            'featureAiDigest' => __('app.premium_feature_ai_digest'),
            'featureExport' => __('app.premium_feature_export'),
            'upgradeLabel' => __('app.upgrade'),
            'openInTelegramLabel' => __('app.upgrade_open_telegram'),
        ];
    }
}
