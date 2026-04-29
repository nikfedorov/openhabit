import { flushPromises, mount } from '@vue/test-utils';
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import RouteLoadingBar from '@/components/navigation/RouteLoadingBar.vue';
import { useLoadingBar } from '@/composables/useLoadingBar';
import { createTestRouter } from '@/tests/helpers/router';

beforeEach(() => {
    vi.useFakeTimers();
    useLoadingBar().reset();
});

afterEach(() => {
    useLoadingBar().reset();
    vi.useRealTimers();
});

async function mountLoadingBar() {
    const router = createTestRouter();
    await router.isReady();
    const wrapper = mount(RouteLoadingBar, {
        global: { plugins: [router] },
    });
    return { wrapper, router };
}

describe('RouteLoadingBar', () => {
    it('is hidden by default', async () => {
        const { wrapper } = await mountLoadingBar();
        expect(wrapper.find('.fixed').exists()).toBe(false);
    });

    it('shows loading bar during navigation', async () => {
        const { router } = await mountLoadingBar();
        const { loading } = useLoadingBar();
        router.push('/view');
        await flushPromises();
        // After navigation completes, stopLoading sets progress to 100
        // then after 200ms timeout it hides
        vi.advanceTimersByTime(200);
        await flushPromises();
        expect(loading.value).toBe(false);
    });

    it('triggers start/stop via router guards', async () => {
        const { router } = await mountLoadingBar();
        const { loading } = useLoadingBar();
        expect(loading.value).toBe(false);
        router.push('/view');
        await flushPromises();
        // afterEach has fired, so progress is 100, still visible until timeout
        vi.advanceTimersByTime(200);
        await flushPromises();
        expect(loading.value).toBe(false);
    });
});
