import { flushPromises, mount } from '@vue/test-utils';
import { describe, expect, it, vi } from 'vitest';
import { createMemoryHistory, createRouter } from 'vue-router';
import App from '@/App.vue';
import { routes } from '@/router';

Object.defineProperty(window, 'matchMedia', {
    writable: true,
    value: vi.fn().mockImplementation((query: string) => ({
        matches: false,
        media: query,
        onchange: null,
        addListener: vi.fn(),
        removeListener: vi.fn(),
        addEventListener: vi.fn(),
        removeEventListener: vi.fn(),
        dispatchEvent: vi.fn(),
    })),
});

vi.mock('@/pages/Track.vue', () => ({
    default: {
        name: 'Track',
        template: '<div data-testid="track">Track</div>',
        emits: ['navigation-translations', 'locale', 'settings', 'ready'],
        mounted() {
            this.$emit('ready');
        },
    },
}));

vi.mock('@/pages/View.vue', () => ({
    default: {
        name: 'View',
        template: '<div data-testid="view">View</div>',
        emits: ['navigation-translations', 'locale', 'settings', 'ready'],
        mounted() {
            this.$emit('ready');
        },
    },
}));

async function mountApp(initialRoute = '/track') {
    const router = createRouter({
        history: createMemoryHistory('/app'),
        routes,
    });
    router.push(initialRoute);
    await router.isReady();

    const wrapper = mount(App, {
        global: { plugins: [router] },
    });
    await flushPromises();
    return { wrapper, router };
}

describe('App', () => {
    it('renders Track by default', async () => {
        const { wrapper } = await mountApp();
        expect(wrapper.find('[data-testid="track"]').exists()).toBe(true);
        expect(wrapper.find('[data-testid="view"]').exists()).toBe(false);
    });

    it('navigates to view page via TabBar', async () => {
        const { wrapper } = await mountApp();
        const links = wrapper.findAll('a');
        const viewLink = links.find((l) => l.text() === 'View');
        expect(viewLink).toBeTruthy();
        await viewLink!.trigger('click');
        await flushPromises();
        expect(wrapper.find('[data-testid="view"]').exists()).toBe(true);
        expect(wrapper.find('[data-testid="track"]').exists()).toBe(false);
    });

    it('renders view when route is /view', async () => {
        const { wrapper } = await mountApp('/view');
        expect(wrapper.find('[data-testid="view"]').exists()).toBe(true);
        expect(wrapper.find('[data-testid="track"]').exists()).toBe(false);
    });

    it('updates tab bar labels when navigation-translations is emitted', async () => {
        const { wrapper } = await mountApp();
        const trackComponent = wrapper.findComponent({ name: 'Track' });
        trackComponent.vm.$emit('navigation-translations', {
            track: 'Трекер',
            view: 'Обзор',
        });
        await wrapper.vm.$nextTick();
        expect(wrapper.text()).toContain('Трекер');
        expect(wrapper.text()).toContain('Обзор');
    });

    it('shows PageLoader during slow navigation', async () => {
        vi.useFakeTimers();
        const { wrapper, router } = await mountApp();
        expect(wrapper.find('[data-testid="track"]').exists()).toBe(true);

        let resolveComponent!: (value: unknown) => void;
        router.addRoute({
            path: '/slow',
            name: 'slow',
            component: () =>
                new Promise((r) => {
                    resolveComponent = r;
                }),
        });

        router.push('/slow');
        await flushPromises();

        // page-loader shows immediately (pageReady=false)
        const loader = wrapper.find('[data-testid="page-loader"]');
        expect(loader.exists()).toBe(true);
        // SVG is hidden until PageLoader's internal delay fires
        expect(loader.find('svg').exists()).toBe(false);

        // After delay — SVG becomes visible
        vi.advanceTimersByTime(300);
        await wrapper.vm.$nextTick();
        expect(loader.find('svg').exists()).toBe(true);

        // Resolve the component — but it doesn't emit 'ready', so loader persists
        resolveComponent({
            default: {
                template: '<div data-testid="slow">Slow</div>',
                emits: ['ready'],
            },
        });
        await flushPromises();
        expect(wrapper.find('[data-testid="page-loader"]').exists()).toBe(true);

        // Component is mounted but hidden (v-show=false)
        const slow = wrapper.find('[data-testid="slow"]');
        expect(slow.exists()).toBe(true);
        expect(slow.isVisible()).toBe(false);
        vi.useRealTimers();
    });

    it('hides PageLoader when page emits ready', async () => {
        vi.useFakeTimers();
        const { wrapper, router } = await mountApp();

        let resolveComponent!: (value: unknown) => void;
        router.addRoute({
            path: '/lazy',
            name: 'lazy',
            component: () =>
                new Promise((r) => {
                    resolveComponent = r;
                }),
        });

        router.push('/lazy');
        await flushPromises();
        vi.advanceTimersByTime(300);
        await wrapper.vm.$nextTick();
        expect(wrapper.find('[data-testid="page-loader"]').exists()).toBe(true);

        // Resolve with a component that emits 'ready' on mount
        resolveComponent({
            default: {
                template: '<div data-testid="lazy">Lazy</div>',
                emits: ['ready'],
                mounted() {
                    this.$emit('ready');
                },
            },
        });
        await flushPromises();
        expect(wrapper.find('[data-testid="page-loader"]').exists()).toBe(
            false,
        );
        expect(wrapper.find('[data-testid="lazy"]').isVisible()).toBe(true);
        vi.useRealTimers();
    });

    it('does not show loader for fast navigation with ready emit', async () => {
        const { wrapper } = await mountApp();

        // Navigate to view — resolves instantly and emits 'ready' on mount
        const links = wrapper.findAll('a');
        const viewLink = links.find((l) => l.text() === 'View');
        await viewLink!.trigger('click');
        await flushPromises();

        // No loader because page emitted ready immediately
        expect(wrapper.find('[data-testid="page-loader"]').exists()).toBe(
            false,
        );
        expect(wrapper.find('[data-testid="view"]').exists()).toBe(true);
    });

    it('does not show loader for same-route query changes', async () => {
        const { wrapper, router } = await mountApp();
        expect(wrapper.find('[data-testid="track"]').exists()).toBe(true);

        // Navigate to same route with different query params
        await router.push({ name: 'track', query: { date: '2026-01-01' } });
        await flushPromises();

        // Component stays mounted, no loader
        expect(wrapper.find('[data-testid="page-loader"]').exists()).toBe(
            false,
        );
        expect(wrapper.find('[data-testid="track"]').exists()).toBe(true);
    });

    it('sets document dir to rtl when locale event emits ar', async () => {
        const { wrapper } = await mountApp();
        const trackComponent = wrapper.findComponent({ name: 'Track' });
        trackComponent.vm.$emit('locale', 'ar');
        await wrapper.vm.$nextTick();
        expect(document.documentElement.dir).toBe('rtl');
        expect(document.documentElement.lang).toBe('ar');
    });

    it('sets document dir to ltr when locale event emits en', async () => {
        document.documentElement.dir = 'rtl';
        const { wrapper } = await mountApp();
        const trackComponent = wrapper.findComponent({ name: 'Track' });
        trackComponent.vm.$emit('locale', 'en');
        await wrapper.vm.$nextTick();
        expect(document.documentElement.dir).toBe('ltr');
        expect(document.documentElement.lang).toBe('en');
    });

    it('shows PageLoader when route component has not resolved yet', async () => {
        let resolveComponent!: (value: unknown) => void;
        const pendingRouter = createRouter({
            history: createMemoryHistory('/app'),
            routes: [
                ...routes.filter((r) => r.path !== '/track'),
                {
                    path: '/track',
                    name: 'track',
                    component: () =>
                        new Promise((r) => {
                            resolveComponent = r;
                        }),
                },
            ],
        });
        pendingRouter.push('/track');

        const wrapper = mount(App, {
            global: { plugins: [pendingRouter] },
        });
        await flushPromises();

        // Component slot is null — PageLoader shows
        expect(wrapper.find('[data-testid="page-loader"]').exists()).toBe(true);

        resolveComponent({
            default: {
                template: '<div data-testid="pending">Pending</div>',
                emits: ['ready'],
                mounted() {
                    this.$emit('ready');
                },
            },
        });
        await pendingRouter.isReady();
        await flushPromises();
        expect(wrapper.find('[data-testid="pending"]').isVisible()).toBe(true);
    });

    it('applies dark class when settings emit dark theme', async () => {
        const { wrapper } = await mountApp();
        const trackComponent = wrapper.findComponent({ name: 'Track' });
        trackComponent.vm.$emit('settings', { theme: 'dark' });
        await wrapper.vm.$nextTick();
        expect(document.documentElement.classList.contains('dark')).toBe(true);
    });

    it('removes dark class when settings emit light theme', async () => {
        document.documentElement.classList.add('dark');
        const { wrapper } = await mountApp();
        const trackComponent = wrapper.findComponent({ name: 'Track' });
        trackComponent.vm.$emit('settings', { theme: 'light' });
        await wrapper.vm.$nextTick();
        expect(document.documentElement.classList.contains('dark')).toBe(false);
    });

    it('respects system preference when settings emit system theme', async () => {
        const { wrapper } = await mountApp();
        const trackComponent = wrapper.findComponent({ name: 'Track' });
        trackComponent.vm.$emit('settings', { theme: 'system' });
        await wrapper.vm.$nextTick();
        // matchMedia mock returns matches: false, so no dark class
        expect(document.documentElement.classList.contains('dark')).toBe(false);
    });
});
