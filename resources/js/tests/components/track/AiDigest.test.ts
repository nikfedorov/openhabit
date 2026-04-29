import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import AiDigest from '@/components/track/AiDigest.vue';

function mountAiDigest(overrides: { content?: string; label?: string } = {}) {
    return mount(AiDigest, {
        props: {
            content: overrides.content ?? 'Some AI-generated digest content.',
            label: overrides.label ?? 'AI Digest',
        },
    });
}

describe('AiDigest', () => {
    it('renders the label', () => {
        const wrapper = mountAiDigest({ label: 'Daily Insights' });

        expect(wrapper.text()).toContain('Daily Insights');
    });

    it('renders the content when open by default', () => {
        const wrapper = mountAiDigest({ content: 'Today was productive.' });

        expect(wrapper.text()).toContain('Today was productive.');
    });

    it('hides content when toggled closed', async () => {
        const wrapper = mountAiDigest();
        const button = wrapper.find('button');

        await button.trigger('click');

        expect(wrapper.find('p').exists()).toBe(false);
    });

    it('shows content again when toggled open after closing', async () => {
        const wrapper = mountAiDigest({ content: 'Re-open test.' });
        const button = wrapper.find('button');

        await button.trigger('click');
        await button.trigger('click');

        expect(wrapper.text()).toContain('Re-open test.');
    });
});
