import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import ActivityLegend from '@/components/view/ActivityLegend.vue';

describe('ActivityLegend', () => {
    it('renders less/more labels and intensity squares', () => {
        const wrapper = mount(ActivityLegend, {
            props: { lessLabel: 'Less', moreLabel: 'More' },
        });
        expect(wrapper.text()).toContain('Less');
        expect(wrapper.text()).toContain('More');
        // 5 intensity squares (0-4)
        const squares = wrapper.findAll('.h-3.w-3');
        expect(squares.length).toBeGreaterThanOrEqual(5);
    });

    it('renders future label when showFuture is true', () => {
        const wrapper = mount(ActivityLegend, {
            props: {
                lessLabel: 'Less',
                moreLabel: 'More',
                futureLabel: 'Future',
                showFuture: true,
            },
        });
        expect(wrapper.text()).toContain('Future');
    });

    it('hides future when showFuture is false', () => {
        const wrapper = mount(ActivityLegend, {
            props: {
                lessLabel: 'Less',
                moreLabel: 'More',
                futureLabel: 'Future',
                showFuture: false,
            },
        });
        expect(wrapper.text()).not.toContain('Future');
    });

    it('hides future when futureLabel is not provided', () => {
        const wrapper = mount(ActivityLegend, {
            props: {
                lessLabel: 'Less',
                moreLabel: 'More',
                showFuture: true,
            },
        });
        expect(wrapper.text()).not.toContain('Future');
    });
});
