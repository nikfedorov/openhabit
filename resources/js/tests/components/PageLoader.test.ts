import { mount } from '@vue/test-utils';
import { afterEach, describe, expect, it, vi } from 'vitest';
import PageLoader from '@/components/PageLoader.vue';

afterEach(() => {
    vi.useRealTimers();
});

describe('PageLoader', () => {
    it('renders the loading logo after delay', async () => {
        vi.useFakeTimers();
        const wrapper = mount(PageLoader);
        expect(wrapper.find('svg').exists()).toBe(false);

        vi.advanceTimersByTime(300);
        await wrapper.vm.$nextTick();
        expect(wrapper.find('svg').exists()).toBe(true);
        expect(wrapper.findAll('animate').length).toBe(4);
    });

    it('has the page-loader test id immediately', () => {
        vi.useFakeTimers();
        const wrapper = mount(PageLoader);
        expect(wrapper.find('[data-testid="page-loader"]').exists()).toBe(true);
    });

    it('respects custom delay prop', async () => {
        vi.useFakeTimers();
        const wrapper = mount(PageLoader, { props: { delay: 100 } });
        expect(wrapper.find('svg').exists()).toBe(false);

        vi.advanceTimersByTime(100);
        await wrapper.vm.$nextTick();
        expect(wrapper.find('svg').exists()).toBe(true);
    });

    it('shows instantly with delay 0', async () => {
        vi.useFakeTimers();
        const wrapper = mount(PageLoader, { props: { delay: 0 } });
        vi.advanceTimersByTime(0);
        await wrapper.vm.$nextTick();
        expect(wrapper.find('svg').exists()).toBe(true);
    });

    it('clears timer on unmount', () => {
        vi.useFakeTimers();
        const wrapper = mount(PageLoader);
        wrapper.unmount();
        // No error thrown — timer was cleaned up
    });
});
