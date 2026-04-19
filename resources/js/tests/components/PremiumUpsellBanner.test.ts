import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import PremiumUpsellBanner from '@/components/PremiumUpsellBanner.vue';
import { defaultTrial } from '@/tests/helpers/settings';

function mountBanner() {
    return mount(PremiumUpsellBanner, {
        props: {
            trialData: defaultTrial,
        },
    });
}

describe('PremiumUpsellBanner', () => {
    it('renders the upgrade label and subtitle', () => {
        const wrapper = mountBanner();

        expect(wrapper.find('[data-testid="premium-upsell-banner"]').exists()).toBe(true);
        expect(wrapper.text()).toContain(defaultTrial.upgradeLabel);
        expect(wrapper.text()).toContain(defaultTrial.featuresSubtitle);
    });

    it('emits open-premium-modal when clicked', async () => {
        const wrapper = mountBanner();

        await wrapper.find('[data-testid="premium-upsell-banner"]').trigger('click');

        expect(wrapper.emitted('open-premium-modal')).toHaveLength(1);
    });
});
