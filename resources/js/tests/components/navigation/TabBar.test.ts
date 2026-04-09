import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import TabBar from '@/components/navigation/TabBar.vue';

describe('TabBar', () => {
    it('renders desktop and mobile tabs with fallback labels', () => {
        const wrapper = mount(TabBar, {
            props: { activeTab: 'track', translations: null },
        });
        // Desktop and mobile both render in jsdom
        expect(wrapper.text()).toContain('Track');
        expect(wrapper.text()).toContain('View');
    });

    it('uses translation labels when provided', () => {
        const wrapper = mount(TabBar, {
            props: {
                activeTab: 'track',
                translations: { track: 'Трекер', view: 'Обзор' },
            },
        });
        expect(wrapper.text()).toContain('Трекер');
        expect(wrapper.text()).toContain('Обзор');
    });

    it('emits navigate on desktop button click', async () => {
        const wrapper = mount(TabBar, {
            props: { activeTab: 'track', translations: null },
        });
        // Desktop buttons are first (hidden md:block nav)
        const desktopNav = wrapper.findAll('nav')[0];
        const viewBtn = desktopNav
            .findAll('button')
            .find((b) => b.text() === 'View')!;
        await viewBtn.trigger('click');
        expect(wrapper.emitted('navigate')![0]).toEqual(['view']);
    });

    it('emits navigate on mobile button click', async () => {
        const wrapper = mount(TabBar, {
            props: { activeTab: 'track', translations: null },
        });
        // Mobile buttons are in the second nav (md:hidden)
        const mobileNav = wrapper.findAll('nav')[1];
        const viewBtn = mobileNav
            .findAll('button')
            .find((b) => b.text() === 'View')!;
        await viewBtn.trigger('click');
        expect(wrapper.emitted('navigate')![0]).toEqual(['view']);
    });

    it('highlights active tab', () => {
        const wrapper = mount(TabBar, {
            props: { activeTab: 'view', translations: null },
        });
        const desktopNav = wrapper.findAll('nav')[0];
        const buttons = desktopNav.findAll('button');
        const viewBtn = buttons.find((b) => b.text() === 'View')!;
        const trackBtn = buttons.find((b) => b.text() === 'Track')!;
        expect(viewBtn.classes()).toContain('bg-white');
        expect(trackBtn.classes()).not.toContain('bg-white');
    });
});
