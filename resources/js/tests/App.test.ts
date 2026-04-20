import { flushPromises, mount } from '@vue/test-utils';
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import { createMemoryHistory, createRouter } from 'vue-router';
import App from '@/App.vue';
import PremiumModal from '@/components/PremiumModal.vue';
import { routes } from '@/router';
import { defaultTrial } from '@/tests/helpers/settings';

const { mockApiFetch } = vi.hoisted(() => ({
    mockApiFetch: vi.fn(),
}));

vi.mock('@/utils/api', () => ({
    apiFetch: mockApiFetch,
}));

vi.mock('canvas-confetti', () => ({
    default: vi.fn(),
}));

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
        emits: ['navigation-translations', 'settings', 'ready'],
        mounted() {
            this.$emit('ready');
        },
    },
}));

vi.mock('@/pages/View.vue', () => ({
    default: {
        name: 'View',
        template: '<div data-testid="view">View</div>',
        emits: ['navigation-translations', 'settings', 'ready'],
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

function trackComponent(
    wrapper: Awaited<ReturnType<typeof mountApp>>['wrapper'],
) {
    return wrapper.findComponent({ name: 'Track' });
}

async function emitTrackSettings(
    wrapper: Awaited<ReturnType<typeof mountApp>>['wrapper'],
    settings: Record<string, unknown>,
) {
    trackComponent(wrapper).vm.$emit('settings', settings);
    await wrapper.vm.$nextTick();
}

async function showTrialBanner(
    wrapper: Awaited<ReturnType<typeof mountApp>>['wrapper'],
    overrides: Record<string, unknown> = {},
) {
    await emitTrackSettings(wrapper, {
        locale: 'en',
        theme: 'system',
        trial: {
            ...defaultTrial,
            shouldShowBanner: true,
            ...overrides,
        },
    });
}

beforeEach(() => {
    mockApiFetch.mockReset();
    localStorage.clear();
    document.documentElement.className = '';
    document.documentElement.dir = 'ltr';
    document.documentElement.lang = 'en';
    document.documentElement.style.backgroundColor = '';
});

afterEach(() => {
    document.body.innerHTML = '';
    delete (window as unknown as Record<string, unknown>).Telegram;
    vi.useRealTimers();
});

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
        trackComponent(wrapper).vm.$emit('navigation-translations', {
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

    it('sets document dir to rtl when settings emit ar locale', async () => {
        const { wrapper } = await mountApp();
        await emitTrackSettings(wrapper, { locale: 'ar', theme: 'system' });
        expect(document.documentElement.dir).toBe('rtl');
        expect(document.documentElement.lang).toBe('ar');
    });

    it('sets document dir to ltr when settings emit en locale', async () => {
        document.documentElement.dir = 'rtl';
        const { wrapper } = await mountApp();
        await emitTrackSettings(wrapper, { locale: 'en', theme: 'system' });
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
        await emitTrackSettings(wrapper, { locale: 'en', theme: 'dark' });
        expect(document.documentElement.classList.contains('dark')).toBe(true);
    });

    it('removes dark class when settings emit light theme', async () => {
        document.documentElement.classList.add('dark');
        const { wrapper } = await mountApp();
        await emitTrackSettings(wrapper, { locale: 'en', theme: 'light' });
        expect(document.documentElement.classList.contains('dark')).toBe(false);
    });

    it('respects system preference when settings emit system theme', async () => {
        const { wrapper } = await mountApp();
        await emitTrackSettings(wrapper, { locale: 'en', theme: 'system' });
        // matchMedia mock returns matches: false, so no dark class
        expect(document.documentElement.classList.contains('dark')).toBe(false);
    });

    it('syncs Telegram WebApp header and background colors when theme changes', async () => {
        const setHeaderColor = vi.fn();
        const setBackgroundColor = vi.fn();

        Object.defineProperty(window, 'Telegram', {
            writable: true,
            configurable: true,
            value: {
                WebApp: {
                    isVersionAtLeast: vi.fn().mockReturnValue(true),
                    setHeaderColor,
                    setBackgroundColor,
                },
            },
        });

        const { wrapper } = await mountApp();
        await emitTrackSettings(wrapper, { locale: 'en', theme: 'dark' });

        expect(setHeaderColor).toHaveBeenCalledWith('#171717');
        expect(setBackgroundColor).toHaveBeenCalledWith('#171717');
    });

    it('shows trial banner when settings emit shouldShowBanner true', async () => {
        const { wrapper } = await mountApp();
        await showTrialBanner(wrapper, { bannerText: 'Free trial: 3 days' });
        expect(wrapper.find('[data-testid="trial-banner"]').exists()).toBe(
            true,
        );
        expect(wrapper.text()).toContain('Free trial: 3 days');
    });

    it('does not show trial banner when shouldShowBanner is false', async () => {
        const { wrapper } = await mountApp();
        await emitTrackSettings(wrapper, {
            locale: 'en',
            theme: 'system',
            trial: { ...defaultTrial, shouldShowBanner: false },
        });
        expect(wrapper.find('[data-testid="trial-banner"]').exists()).toBe(
            false,
        );
    });

    it('does not show trial banner before settings are received', async () => {
        const { wrapper } = await mountApp();
        expect(wrapper.find('[data-testid="trial-banner"]').exists()).toBe(
            false,
        );
    });

    it('hides trial banner and calls dismiss API when dismissed', async () => {
        mockApiFetch.mockResolvedValueOnce(undefined);

        const { wrapper } = await mountApp();
        await showTrialBanner(wrapper);

        expect(wrapper.find('[data-testid="trial-banner"]').exists()).toBe(
            true,
        );

        await wrapper
            .find('[data-testid="trial-banner-dismiss"]')
            .trigger('click');
        await flushPromises();

        expect(wrapper.find('[data-testid="trial-banner"]').exists()).toBe(
            false,
        );
        expect(mockApiFetch).toHaveBeenCalledWith(
            '/api/settings/trial-banner/dismiss',
            { method: 'POST' },
        );
    });

    it('ignores dismiss clicks when the trial banner is not visible', async () => {
        const { wrapper } = await mountApp();

        expect(wrapper.find('[data-testid="trial-banner"]').exists()).toBe(
            false,
        );
        expect(mockApiFetch).not.toHaveBeenCalled();
    });

    it('restores the trial banner when dismiss API fails', async () => {
        const consoleError = vi
            .spyOn(console, 'error')
            .mockImplementation(() => undefined);

        mockApiFetch.mockRejectedValueOnce(new Error('network error'));

        const { wrapper } = await mountApp();
        await showTrialBanner(wrapper, { bannerText: 'Free trial: 3 days' });

        await wrapper
            .find('[data-testid="trial-banner-dismiss"]')
            .trigger('click');
        await flushPromises();

        expect(wrapper.find('[data-testid="trial-banner"]').exists()).toBe(
            true,
        );

        consoleError.mockRestore();
    });

    it('opens premium modal when learn more is clicked on trial banner', async () => {
        const { wrapper } = await mountApp();
        await showTrialBanner(wrapper);

        await wrapper
            .find('[data-testid="trial-banner-learn-more"]')
            .trigger('click');
        await wrapper.vm.$nextTick();

        expect(
            document.body.querySelector('[data-testid="premium-modal"]'),
        ).not.toBeNull();
    });

    it('closes premium modal when close event is emitted', async () => {
        const { wrapper } = await mountApp();
        await showTrialBanner(wrapper);

        // Open premium modal
        await wrapper
            .find('[data-testid="trial-banner-learn-more"]')
            .trigger('click');
        await wrapper.vm.$nextTick();
        expect(
            document.body.querySelector('[data-testid="premium-modal"]'),
        ).not.toBeNull();

        // Close via component emit (PremiumModal.close → App sets showPremiumModal = false)
        const premiumModal = wrapper.findComponent(PremiumModal);
        expect(premiumModal.props('show')).toBe(true);
        premiumModal.vm.$emit('close');
        await wrapper.vm.$nextTick();
        expect(premiumModal.props('show')).toBe(false);
    });

    it('shows confetti and hides the trial banner after successful payment', async () => {
        const { wrapper } = await mountApp();
        await showTrialBanner(wrapper);

        await wrapper
            .find('[data-testid="trial-banner-learn-more"]')
            .trigger('click');
        await wrapper.vm.$nextTick();

        vi.useFakeTimers();

        const premiumModal = wrapper.findComponent(PremiumModal);
        premiumModal.vm.$emit('payment-success');
        await wrapper.vm.$nextTick();

        expect(premiumModal.props('show')).toBe(false);
        expect(wrapper.find('[data-testid="trial-banner"]').exists()).toBe(
            false,
        );
        expect(wrapper.find('[data-testid="payment-confetti"]').exists()).toBe(
            true,
        );

        // confetti still visible just before the auto-dismiss timeout
        vi.advanceTimersByTime(2999);
        await wrapper.vm.$nextTick();
        expect(wrapper.find('[data-testid="payment-confetti"]').exists()).toBe(
            true,
        );

        // confetti dismissed once the 3 s timeout fires
        vi.advanceTimersByTime(1);
        await wrapper.vm.$nextTick();
        expect(wrapper.find('[data-testid="payment-confetti"]').exists()).toBe(
            false,
        );
    });

    it('shows confetti after payment even when banner is already hidden', async () => {
        const { wrapper } = await mountApp();
        await emitTrackSettings(wrapper, {
            locale: 'en',
            theme: 'system',
            trial: { ...defaultTrial, shouldShowBanner: false },
        });

        // Open premium modal programmatically via the composable
        // PremiumModal is rendered (trialData is set) but banner is hidden
        const premiumModal = wrapper.findComponent(PremiumModal);
        premiumModal.vm.$emit('payment-success');
        await wrapper.vm.$nextTick();

        expect(wrapper.find('[data-testid="payment-confetti"]').exists()).toBe(
            true,
        );
    });

    it('skips confetti when reduced motion is enabled after successful payment', async () => {
        vi.mocked(window.matchMedia).mockImplementation((query: string) => ({
            matches: query === '(prefers-reduced-motion: reduce)',
            media: query,
            onchange: null,
            addListener: vi.fn(),
            removeListener: vi.fn(),
            addEventListener: vi.fn(),
            removeEventListener: vi.fn(),
            dispatchEvent: vi.fn(),
        }));

        const { wrapper } = await mountApp();
        await showTrialBanner(wrapper);

        await wrapper
            .find('[data-testid="trial-banner-learn-more"]')
            .trigger('click');
        await wrapper.vm.$nextTick();

        const premiumModal = wrapper.findComponent(PremiumModal);
        premiumModal.vm.$emit('payment-success');
        await wrapper.vm.$nextTick();

        expect(premiumModal.props('show')).toBe(false);
        expect(wrapper.find('[data-testid="trial-banner"]').exists()).toBe(
            false,
        );
        expect(wrapper.find('[data-testid="payment-confetti"]').exists()).toBe(
            false,
        );
    });

    it('hides mobile tabbar when a text input gains focus', async () => {
        const { wrapper } = await mountApp();
        const tabBar = wrapper.findComponent({ name: 'TabBar' });

        expect(tabBar.props('hiddenOnMobile')).toBe(false);

        // Attach an input to the document so that native dispatch sets target correctly.
        const input = document.createElement('input');
        document.body.appendChild(input);
        input.dispatchEvent(new FocusEvent('focusin', { bubbles: true }));
        await wrapper.vm.$nextTick();

        expect(tabBar.props('hiddenOnMobile')).toBe(true);

        input.dispatchEvent(new FocusEvent('focusout', { bubbles: true }));
        await wrapper.vm.$nextTick();

        expect(tabBar.props('hiddenOnMobile')).toBe(false);

        document.body.removeChild(input);
    });

    it('does not hide tabbar when a non-input element gains or loses focus', async () => {
        const { wrapper } = await mountApp();
        const tabBar = wrapper.findComponent({ name: 'TabBar' });

        const div = document.createElement('div');
        document.body.appendChild(div);

        div.dispatchEvent(new FocusEvent('focusin', { bubbles: true }));
        await wrapper.vm.$nextTick();
        expect(tabBar.props('hiddenOnMobile')).toBe(false);

        div.dispatchEvent(new FocusEvent('focusout', { bubbles: true }));
        await wrapper.vm.$nextTick();
        expect(tabBar.props('hiddenOnMobile')).toBe(false);

        document.body.removeChild(div);
    });

    it('hides mobile tabbar when a textarea gains focus', async () => {
        const { wrapper } = await mountApp();
        const tabBar = wrapper.findComponent({ name: 'TabBar' });

        const textarea = document.createElement('textarea');
        document.body.appendChild(textarea);
        textarea.dispatchEvent(new FocusEvent('focusin', { bubbles: true }));
        await wrapper.vm.$nextTick();

        expect(tabBar.props('hiddenOnMobile')).toBe(true);

        textarea.dispatchEvent(new FocusEvent('focusout', { bubbles: true }));
        await wrapper.vm.$nextTick();

        expect(tabBar.props('hiddenOnMobile')).toBe(false);

        document.body.removeChild(textarea);
    });

    it('removes focus listeners on unmount', async () => {
        const { wrapper } = await mountApp();
        const removeSpy = vi.spyOn(document, 'removeEventListener');

        wrapper.unmount();

        expect(removeSpy).toHaveBeenCalledWith('focusin', expect.any(Function));
        expect(removeSpy).toHaveBeenCalledWith(
            'focusout',
            expect.any(Function),
        );

        removeSpy.mockRestore();
    });
});
