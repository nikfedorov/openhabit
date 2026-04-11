import { flushPromises } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import { ref } from 'vue';
import { useHabitToggle } from '@/composables/useHabitToggle';
import {
    deferredPromise,
    makeHabit,
    makeTrackResponse,
} from '@/tests/helpers/track';
import type { TrackData } from '@/types/track';

const { mockApiFetch } = vi.hoisted(() => ({
    mockApiFetch: vi.fn(),
}));

vi.mock('@/utils/api', () => ({
    apiFetch: mockApiFetch,
}));

/** Creates a reactive data ref and a composable instance for a test. */
function setup(overrides: Parameters<typeof makeTrackResponse>[0] = {}) {
    const data = ref<TrackData>(makeTrackResponse(overrides).data);
    const { pendingHabitIds, toggle } = useHabitToggle(data);
    return { data, pendingHabitIds, toggle };
}

beforeEach(() => {
    mockApiFetch.mockReset();
});

// ─── Basic toggle ────────────────────────────────────────────────────────────

describe('useHabitToggle - basic toggle', () => {
    it('completes an incomplete habit', async () => {
        const { data, toggle } = setup({
            habits: [makeHabit({ name: 'Exercise' })],
            completedCount: 0,
        });

        mockApiFetch.mockResolvedValueOnce(
            makeTrackResponse({
                completedCount: 1,
                habits: [
                    makeHabit({
                        name: 'Exercise',
                        is_completed: true,
                        current_iteration: 1,
                    }),
                ],
            }),
        );

        await toggle(1);

        expect(data.value.habits[0].is_completed).toBe(true);
        expect(data.value.completedCount).toBe(1);
    });

    it('un-completes a completed habit', async () => {
        const { data, toggle } = setup({
            habits: [
                makeHabit({
                    name: 'Exercise',
                    is_completed: true,
                    current_iteration: 1,
                }),
            ],
            completedCount: 1,
        });

        mockApiFetch.mockResolvedValueOnce(
            makeTrackResponse({
                completedCount: 0,
                habits: [makeHabit({ name: 'Exercise' })],
            }),
        );

        await toggle(1);

        expect(data.value.habits[0].is_completed).toBe(false);
        expect(data.value.completedCount).toBe(0);
    });

    it('increments iteration without completing for a multi-iteration habit', async () => {
        const { data, toggle } = setup({
            habits: [
                makeHabit({
                    name: 'Read',
                    iterations_required: 3,
                    current_iteration: 1,
                }),
            ],
            completedCount: 0,
        });

        mockApiFetch.mockResolvedValueOnce(
            makeTrackResponse({
                completedCount: 0,
                habits: [
                    makeHabit({
                        name: 'Read',
                        iterations_required: 3,
                        current_iteration: 2,
                    }),
                ],
            }),
        );

        // Do not await — verify optimistic state before server responds
        const pending = toggle(1);

        expect(data.value.habits[0].current_iteration).toBe(2);
        expect(data.value.habits[0].is_completed).toBe(false);

        await pending;
    });
});

// ─── pendingHabitIds ─────────────────────────────────────────────────────────

describe('useHabitToggle - pendingHabitIds', () => {
    it('adds habitId immediately on toggle and removes it after server responds', async () => {
        const deferred = deferredPromise();
        const { pendingHabitIds, toggle } = setup({
            habits: [makeHabit({ name: 'Exercise' })],
        });

        mockApiFetch.mockReturnValueOnce(deferred.promise);

        const pending = toggle(1);

        // Pending before server responds
        expect(pendingHabitIds.value.has(1)).toBe(true);

        deferred.resolve(
            makeTrackResponse({
                habits: [
                    makeHabit({
                        name: 'Exercise',
                        is_completed: true,
                        current_iteration: 1,
                    }),
                ],
            }),
        );
        await flushPromises();

        // Not pending after server responds
        expect(pendingHabitIds.value.has(1)).toBe(false);

        await pending;
    });

    it('keeps habitId pending while multiple requests for the same habit are in-flight', async () => {
        const first = deferredPromise();
        const second = deferredPromise();
        const habit = makeHabit({ name: 'Exercise' });

        const { pendingHabitIds, toggle } = setup({
            habits: [habit],
            completedCount: 0,
        });

        mockApiFetch.mockReturnValueOnce(first.promise);
        toggle(1); // first toggle

        mockApiFetch.mockReturnValueOnce(second.promise);
        toggle(1); // second toggle

        expect(pendingHabitIds.value.has(1)).toBe(true);

        first.resolve(
            makeTrackResponse({
                habits: [
                    { ...habit, is_completed: true, current_iteration: 1 },
                ],
            }),
        );
        await flushPromises();

        // Still pending because second request is still in-flight
        expect(pendingHabitIds.value.has(1)).toBe(true);

        second.resolve(makeTrackResponse({ habits: [habit] }));
        await flushPromises();

        expect(pendingHabitIds.value.has(1)).toBe(false);
    });
});

// ─── Sorting ─────────────────────────────────────────────────────────────────

describe('useHabitToggle - sorting', () => {
    it('moves completed habit to the end when moveCompletedToEnd is true', async () => {
        const { data, toggle } = setup({
            moveCompletedToEnd: true,
            completedCount: 0,
            habits: [
                makeHabit({ name: 'First habit', sort_order: 1 }),
                makeHabit({ id: 2, name: 'Second habit', sort_order: 2 }),
            ],
        });

        mockApiFetch.mockResolvedValueOnce(
            makeTrackResponse({
                moveCompletedToEnd: true,
                completedCount: 1,
                habits: [
                    makeHabit({ id: 2, name: 'Second habit', sort_order: 2 }),
                    makeHabit({
                        name: 'First habit',
                        is_completed: true,
                        current_iteration: 1,
                    }),
                ],
            }),
        );

        // Do not await — verify optimistic sort before server responds
        const pending = toggle(1);

        // Completed "First habit" should be pushed to end
        expect(data.value.habits[0].name).toBe('Second habit');
        expect(data.value.habits[1].name).toBe('First habit');

        await pending;
    });

    it('preserves sort_order within the same completion group', async () => {
        const { data, toggle } = setup({
            moveCompletedToEnd: true,
            completedCount: 0,
            totalHabits: 3,
            habits: [
                makeHabit({ id: 3, name: 'Third', sort_order: 3 }),
                makeHabit({ name: 'First', sort_order: 1 }),
                makeHabit({ id: 2, name: 'Second', sort_order: 2 }),
            ],
        });

        mockApiFetch.mockResolvedValueOnce(
            makeTrackResponse({
                moveCompletedToEnd: true,
                completedCount: 1,
                totalHabits: 3,
                habits: [
                    makeHabit({ name: 'First', sort_order: 1 }),
                    makeHabit({ id: 2, name: 'Second', sort_order: 2 }),
                    makeHabit({
                        id: 3,
                        name: 'Third',
                        is_completed: true,
                        current_iteration: 1,
                        sort_order: 3,
                    }),
                ],
            }),
        );

        // Do not await — verify optimistic sort before server responds
        const pending = toggle(3);

        // Remaining uncompleted habits sorted by sort_order
        expect(data.value.habits[0].name).toBe('First');
        expect(data.value.habits[1].name).toBe('Second');
        expect(data.value.habits[2].name).toBe('Third');

        await pending;
    });

    it('moves un-completed habit back to its sort_order position', async () => {
        const { data, toggle } = setup({
            moveCompletedToEnd: true,
            completedCount: 2,
            totalHabits: 2,
            habits: [
                makeHabit({ name: 'Active habit', sort_order: 1 }),
                makeHabit({
                    id: 2,
                    name: 'Done habit',
                    is_completed: true,
                    current_iteration: 1,
                    sort_order: 2,
                }),
            ],
        });

        mockApiFetch.mockResolvedValueOnce(
            makeTrackResponse({
                moveCompletedToEnd: true,
                completedCount: 1,
                totalHabits: 2,
                habits: [
                    makeHabit({ name: 'Active habit', sort_order: 1 }),
                    makeHabit({ id: 2, name: 'Done habit', sort_order: 2 }),
                ],
            }),
        );

        // Do not await — verify optimistic sort before server responds
        const pending = toggle(2);

        // Both uncompleted now — sorted by sort_order
        expect(data.value.habits[0].name).toBe('Active habit');
        expect(data.value.habits[1].name).toBe('Done habit');

        await pending;
    });
});

// ─── Rollback ────────────────────────────────────────────────────────────────

describe('useHabitToggle - rollback', () => {
    it('restores original state on server error', async () => {
        const deferred = deferredPromise<never>();
        const { data, toggle } = setup({
            completedCount: 0,
            habits: [makeHabit({ name: 'Exercise' })],
        });

        mockApiFetch.mockReturnValueOnce(deferred.promise);

        toggle(1);

        // Optimistic: completed
        expect(data.value.habits[0].is_completed).toBe(true);

        deferred.reject(new Error('API error: 500'));
        await flushPromises();

        // Rolled back to original
        expect(data.value.habits[0].is_completed).toBe(false);
        expect(data.value.completedCount).toBe(0);
    });

    it('restores original state after all rapid-toggle requests error', async () => {
        const first = deferredPromise<never>();
        const second = deferredPromise<never>();
        const habit = makeHabit({ name: 'Exercise' });

        const { data, toggle } = setup({
            completedCount: 0,
            totalHabits: 1,
            habits: [habit],
        });

        mockApiFetch.mockReturnValueOnce(first.promise);
        toggle(1); // toggle ON

        mockApiFetch.mockReturnValueOnce(second.promise);
        toggle(1); // toggle OFF (reverts optimistic)

        first.reject(new Error('Network error'));
        await flushPromises();

        // Not yet rolled back — second request still in-flight;
        // optimistic state is un-completed (completedCount 0)
        expect(data.value.completedCount).toBe(0);

        second.reject(new Error('Network error'));
        await flushPromises();

        // Now rolled back to the original snapshot
        expect(data.value.habits[0].is_completed).toBe(false);
        expect(data.value.completedCount).toBe(0);
    });
});

// ─── Inflight deduplication ──────────────────────────────────────────────────

describe('useHabitToggle - inflight deduplication', () => {
    it('discards stale responses while any request is still in-flight', async () => {
        const first = deferredPromise();
        const second = deferredPromise();
        const third = deferredPromise();

        const habits = [
            makeHabit({ name: 'Habit A', sort_order: 1 }),
            makeHabit({ id: 2, name: 'Habit B', sort_order: 2 }),
            makeHabit({ id: 3, name: 'Habit C', sort_order: 3 }),
        ];

        const { data, toggle } = setup({
            moveCompletedToEnd: true,
            completedCount: 0,
            totalHabits: 3,
            habits,
        });

        // Toggle all three habits rapidly before any response arrives
        mockApiFetch.mockReturnValueOnce(first.promise);
        toggle(1);
        mockApiFetch.mockReturnValueOnce(second.promise);
        toggle(2);
        mockApiFetch.mockReturnValueOnce(third.promise);
        toggle(3);

        // Stale first response (only Habit A completed)
        first.resolve(
            makeTrackResponse({
                moveCompletedToEnd: true,
                completedCount: 1,
                totalHabits: 3,
                habits: [
                    habits[1],
                    habits[2],
                    { ...habits[0], is_completed: true, current_iteration: 1 },
                ],
            }),
        );
        await flushPromises();

        // Optimistic state preserved — all habits still completed
        expect(data.value.habits.every((h) => h.is_completed)).toBe(true);

        // Stale second response (Habit A + B completed)
        second.resolve(
            makeTrackResponse({
                moveCompletedToEnd: true,
                completedCount: 2,
                totalHabits: 3,
                habits: [
                    habits[2],
                    { ...habits[0], is_completed: true, current_iteration: 1 },
                    { ...habits[1], is_completed: true, current_iteration: 1 },
                ],
            }),
        );
        await flushPromises();

        // Optimistic state still preserved
        expect(data.value.habits.every((h) => h.is_completed)).toBe(true);

        // Final response — written because all in-flight requests have settled
        third.resolve(
            makeTrackResponse({
                moveCompletedToEnd: true,
                completedCount: 3,
                totalHabits: 3,
                habits: habits.map((h) => ({
                    ...h,
                    is_completed: true,
                    current_iteration: 1,
                })),
            }),
        );
        await flushPromises();

        expect(data.value.completedCount).toBe(3);
    });

    it('discards first response when same habit is toggled twice rapidly', async () => {
        const first = deferredPromise();
        const second = deferredPromise();
        const habit = makeHabit({ name: 'Exercise' });

        const { data, toggle } = setup({
            moveCompletedToEnd: true,
            completedCount: 0,
            totalHabits: 1,
            habits: [habit],
        });

        mockApiFetch.mockReturnValueOnce(first.promise);
        toggle(1); // toggle ON

        mockApiFetch.mockReturnValueOnce(second.promise);
        toggle(1); // toggle OFF

        // Stale first response (habit completed)
        first.resolve(
            makeTrackResponse({
                moveCompletedToEnd: true,
                completedCount: 1,
                totalHabits: 1,
                habits: [
                    { ...habit, is_completed: true, current_iteration: 1 },
                ],
            }),
        );
        await flushPromises();

        // Response must be discarded — optimistic state is un-completed
        expect(data.value.habits[0].is_completed).toBe(false);
        expect(data.value.completedCount).toBe(0);

        // Final response (habit un-completed)
        second.resolve(
            makeTrackResponse({
                moveCompletedToEnd: true,
                completedCount: 0,
                totalHabits: 1,
                habits: [habit],
            }),
        );
        await flushPromises();

        expect(data.value.habits[0].is_completed).toBe(false);
        expect(data.value.completedCount).toBe(0);
    });
});

// ─── Edge cases ──────────────────────────────────────────────────────────────

describe('useHabitToggle - edge cases', () => {
    it('is a no-op when habitId is not found in the habits list', async () => {
        const { data, toggle } = setup({
            habits: [makeHabit({ name: 'Exercise' })],
        });

        const snapshot = JSON.stringify(data.value);

        await toggle(999);

        expect(JSON.stringify(data.value)).toBe(snapshot);
        expect(mockApiFetch).not.toHaveBeenCalled();
    });

    it('is a no-op when data is null', async () => {
        const data = ref<TrackData | null>(null);
        const { toggle } = useHabitToggle(data);

        await toggle(1);

        expect(mockApiFetch).not.toHaveBeenCalled();
    });
});
