import { mount } from '@vue/test-utils';
import { afterEach, describe, expect, it } from 'vitest';
import TrialBanner from '@/components/TrialBanner.vue';
import type { TrialData } from '@/types/api';

const defaultTrialData: TrialData = {
    shouldShowBanner: true,
    bannerText: 'Free trial: 3 days',
    invoiceLink: 'https://t.me/invoice',
    learnMore: 'Learn more',
    featuresTitle: 'Premium Features',
    featuresSubtitle: 'Upgrade to unlock all features',
    featureNotifications: 'Multiple reminders per habit',
    featureAiDigest: 'Daily AI digest of your progress',
    featureExport: 'Export all your data as CSV',
    upgradeLabel: 'Upgrade',
    openInTelegramLabel: 'To upgrade, please open the app in Telegram.',
};

afterEach(() => {
    document.body.innerHTML = '';
});

describe('TrialBanner', () => {
    it('does not render when shouldShowBanner is false', () => {
        const wrapper = mount(TrialBanner, {
            props: {
                trialData: { ...defaultTrialData, shouldShowBanner: false },
            },
        });
        expect(wrapper.find('[data-testid="trial-banner"]').exists()).toBe(
            false,
        );
    });

    it('renders banner when shouldShowBanner is true', () => {
        const wrapper = mount(TrialBanner, {
            props: { trialData: defaultTrialData },
        });
        expect(wrapper.find('[data-testid="trial-banner"]').exists()).toBe(
            true,
        );
    });

    it('displays banner text', () => {
        const wrapper = mount(TrialBanner, {
            props: { trialData: defaultTrialData },
        });
        expect(wrapper.text()).toContain('Free trial: 3 days');
    });

    it('displays learn more label', () => {
        const wrapper = mount(TrialBanner, {
            props: { trialData: defaultTrialData },
        });
        expect(
            wrapper.find('[data-testid="trial-banner-learn-more"]').text(),
        ).toBe('Learn more');
    });

    it('emits openPremiumModal when learn more is clicked', async () => {
        const wrapper = mount(TrialBanner, {
            props: { trialData: defaultTrialData },
        });
        await wrapper
            .find('[data-testid="trial-banner-learn-more"]')
            .trigger('click');
        expect(wrapper.emitted('openPremiumModal')).toHaveLength(1);
    });

    it('emits dismiss when dismiss button is clicked', async () => {
        const wrapper = mount(TrialBanner, {
            props: { trialData: defaultTrialData },
        });
        await wrapper
            .find('[data-testid="trial-banner-dismiss"]')
            .trigger('click');
        expect(wrapper.emitted('dismiss')).toHaveLength(1);
    });
});
