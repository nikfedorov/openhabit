import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import { useLoadingBar } from '@/composables/useLoadingBar';

beforeEach(() => {
    vi.useFakeTimers();
    useLoadingBar().reset();
});

afterEach(() => {
    useLoadingBar().reset();
    vi.useRealTimers();
});

describe('useLoadingBar', () => {
    it('starts in idle state', () => {
        const { loading, progress } = useLoadingBar();
        expect(loading.value).toBe(false);
        expect(progress.value).toBe(0);
    });

    it('starts loading on startLoading', () => {
        const { loading, progress, startLoading, stopLoading } =
            useLoadingBar();
        startLoading();
        expect(loading.value).toBe(true);
        expect(progress.value).toBe(20);
        stopLoading();
        vi.advanceTimersByTime(200);
    });

    it('progresses over time', () => {
        const { progress, startLoading, stopLoading } = useLoadingBar();
        startLoading();
        vi.advanceTimersByTime(100);
        expect(progress.value).toBe(30);
        vi.advanceTimersByTime(100);
        expect(progress.value).toBe(40);
        stopLoading();
        vi.advanceTimersByTime(200);
    });

    it('caps progress at 90', () => {
        const { progress, startLoading, stopLoading } = useLoadingBar();
        startLoading();
        vi.advanceTimersByTime(1000);
        expect(progress.value).toBeLessThanOrEqual(90);
        stopLoading();
        vi.advanceTimersByTime(200);
    });

    it('stops loading after stopLoading and timeout', () => {
        const { loading, progress, startLoading, stopLoading } =
            useLoadingBar();
        startLoading();
        stopLoading();
        expect(progress.value).toBe(100);
        expect(loading.value).toBe(true);
        vi.advanceTimersByTime(200);
        expect(loading.value).toBe(false);
        expect(progress.value).toBe(0);
    });

    it('tracks multiple concurrent requests', () => {
        const { loading, startLoading, stopLoading } = useLoadingBar();
        startLoading();
        startLoading();
        expect(loading.value).toBe(true);
        stopLoading();
        // Still one request pending
        vi.advanceTimersByTime(200);
        expect(loading.value).toBe(true);
        stopLoading();
        vi.advanceTimersByTime(200);
        expect(loading.value).toBe(false);
    });

    it('does not restart bar when already loading', () => {
        const { progress, startLoading, stopLoading } = useLoadingBar();
        startLoading();
        vi.advanceTimersByTime(200);
        const current = progress.value;
        startLoading(); // second request
        expect(progress.value).toBe(current); // not reset to 20
        stopLoading();
        stopLoading();
        vi.advanceTimersByTime(200);
    });

    it('handles stopLoading when no timer is active', () => {
        const { loading, progress, stopLoading } = useLoadingBar();
        stopLoading();
        expect(progress.value).toBe(100);
        vi.advanceTimersByTime(200);
        expect(loading.value).toBe(false);
    });

    it('resets all state', () => {
        const { loading, progress, startLoading, reset } = useLoadingBar();
        startLoading();
        reset();
        expect(loading.value).toBe(false);
        expect(progress.value).toBe(0);
    });
});
