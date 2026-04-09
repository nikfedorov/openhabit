import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import ActivityCell from '@/components/view/ActivityCell.vue';

describe('ActivityCell', () => {
    it('renders with intensity color for lived cells', () => {
        const wrapper = mount(ActivityCell, {
            props: { intensity: 3, isLived: true },
        });
        expect(wrapper.html()).toContain('bg-green');
    });

    it('renders neutral color for non-lived cells', () => {
        const wrapper = mount(ActivityCell, {
            props: { intensity: 3 },
        });
        expect(wrapper.html()).toContain('bg-neutral');
    });

    it('renders current ring', () => {
        const wrapper = mount(ActivityCell, {
            props: { intensity: 0, isCurrent: true },
        });
        expect(wrapper.html()).toContain('ring-green-500');
    });

    it('renders future border', () => {
        const wrapper = mount(ActivityCell, {
            props: { intensity: 0, isFuture: true },
        });
        expect(wrapper.html()).toContain('border-dashed');
    });

    it('applies hover cursor for clickable cells', () => {
        const wrapper = mount(ActivityCell, {
            props: { intensity: 0, isClickable: true },
        });
        expect(wrapper.html()).toContain('cursor-pointer');
    });

    it('renders slot content', () => {
        const wrapper = mount(ActivityCell, {
            props: { intensity: 0 },
            slots: { default: '<span>10</span>' },
        });
        expect(wrapper.text()).toContain('10');
    });

    it('applies intensity color for lived cells', () => {
        const wrapper = mount(ActivityCell, {
            props: { intensity: 2, isLived: true },
        });
        expect(wrapper.html()).toContain('bg-green');
    });
});
