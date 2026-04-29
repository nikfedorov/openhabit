import { mount } from '@vue/test-utils';
import { afterEach, describe, expect, it } from 'vitest';
import TrialBanner from '@/components/TrialBanner.vue';
import { defaultTrial } from '@/tests/helpers/settings';

function mountTrialBanner(shouldShowBanner = true) {
    return mount(TrialBanner, {
        props: {
            trialData: {
                ...defaultTrial,
                shouldShowBanner,
                bannerText: 'Free trial: 3 days',
            },
        },
    });
}

afterEach(() => {
    document.body.innerHTML = '';
});

describe('TrialBanner', () => {
    it('does not render when shouldShowBanner is false', () => {
        const wrapper = mountTrialBanner(false);

        expect(wrapper.find('[data-testid="trial-banner"]').exists()).toBe(
            false,
        );
    });

    it('renders the banner copy when shouldShowBanner is true', () => {
        const wrapper = mountTrialBanner();

        expect(wrapper.find('[data-testid="trial-banner"]').exists()).toBe(
            true,
        );
        expect(wrapper.text()).toContain('Free trial: 3 days');
        expect(
            wrapper.find('[data-testid="trial-banner-learn-more"]').text(),
        ).toBe('Learn more');
    });

    it('emits actions from the banner buttons', async () => {
        const wrapper = mountTrialBanner();

        await wrapper
            .find('[data-testid="trial-banner-learn-more"]')
            .trigger('click');
        await wrapper
            .find('[data-testid="trial-banner-dismiss"]')
            .trigger('click');

        expect(wrapper.emitted('open-premium-modal')).toHaveLength(1);
        expect(wrapper.emitted('dismiss')).toHaveLength(1);
    });
});
