import { flushPromises, mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import TabBar from '@/components/navigation/TabBar.vue';
import { createTestRouter } from '@/tests/helpers/router';

async function mountTabBar(
    props: { activeTab: string; translations: object | null } = {
        activeTab: 'track',
        translations: null,
    },
) {
    const router = createTestRouter();
    await router.isReady();
    const wrapper = mount(TabBar, {
        props,
        global: { plugins: [router] },
    });
    return { wrapper, router };
}

describe('TabBar', () => {
    it('renders desktop and mobile tabs with fallback labels', async () => {
        const { wrapper } = await mountTabBar();
        expect(wrapper.text()).toContain('Track');
        expect(wrapper.text()).toContain('View');
    });

    it('uses translation labels when provided', async () => {
        const { wrapper } = await mountTabBar({
            activeTab: 'track',
            translations: { track: 'Трекер', view: 'Обзор' },
        });
        expect(wrapper.text()).toContain('Трекер');
        expect(wrapper.text()).toContain('Обзор');
    });

    it('navigates via desktop router-link click', async () => {
        const { wrapper, router } = await mountTabBar();
        const desktopNav = wrapper.findAll('nav')[0];
        const viewLink = desktopNav
            .findAll('a')
            .find((a) => a.text() === 'View')!;
        await viewLink.trigger('click');
        await flushPromises();
        expect(router.currentRoute.value.name).toBe('view');
    });

    it('navigates via mobile router-link click', async () => {
        const { wrapper, router } = await mountTabBar();
        const mobileNav = wrapper.findAll('nav')[1];
        const viewLink = mobileNav
            .findAll('a')
            .find((a) => a.text() === 'View')!;
        await viewLink.trigger('click');
        await flushPromises();
        expect(router.currentRoute.value.name).toBe('view');
    });

    it('highlights active tab', async () => {
        const { wrapper } = await mountTabBar({
            activeTab: 'view',
            translations: null,
        });
        const desktopNav = wrapper.findAll('nav')[0];
        const links = desktopNav.findAll('a');
        const viewLink = links.find((a) => a.text() === 'View')!;
        const trackLink = links.find((a) => a.text() === 'Track')!;
        expect(viewLink.classes()).toContain('bg-white');
        expect(trackLink.classes()).not.toContain('bg-white');
    });
});
