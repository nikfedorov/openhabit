import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import WeekInsights from '@/components/view/WeekInsights.vue';
import type { AiDigestItem } from '@/types/view';

const sampleDigests: AiDigestItem[] = [
    {
        date: '2024-01-08',
        dateLabel: 'January 8, 2024, Monday',
        content: 'You made great progress this week.',
    },
    {
        date: '2024-01-09',
        dateLabel: 'January 9, 2024, Tuesday',
        content: 'Keep up the momentum.',
    },
];

function mountWeekInsights(
    digests: AiDigestItem[] = sampleDigests,
    label = 'Insights',
) {
    return mount(WeekInsights, {
        props: { digests, label },
    });
}

describe('WeekInsights', () => {
    it('renders the section label', () => {
        const wrapper = mountWeekInsights();

        expect(wrapper.text()).toContain('Insights');
    });

    it('renders all digest date labels and content when open by default', () => {
        const wrapper = mountWeekInsights();

        expect(wrapper.text()).toContain('January 8, 2024, Monday');
        expect(wrapper.text()).toContain('You made great progress this week.');
        expect(wrapper.text()).toContain('January 9, 2024, Tuesday');
        expect(wrapper.text()).toContain('Keep up the momentum.');
    });

    it('hides digest cards when toggled closed', async () => {
        const wrapper = mountWeekInsights();
        const button = wrapper.find('button');

        await button.trigger('click');

        expect(wrapper.findAll('.rounded-xl').length).toBe(0);
    });

    it('shows digest cards again when toggled open after closing', async () => {
        const wrapper = mountWeekInsights();
        const button = wrapper.find('button');

        await button.trigger('click');
        await button.trigger('click');

        expect(wrapper.findAll('.rounded-xl').length).toBe(
            sampleDigests.length,
        );
    });

    it('renders nothing when digests array is empty', () => {
        const wrapper = mountWeekInsights([]);

        expect(wrapper.findAll('.rounded-xl').length).toBe(0);
    });
});
