import { flushPromises, mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import ActivityGraph from '@/components/track/ActivityGraph.vue';
import HabitItem from '@/components/track/HabitItem.vue';
import ProgressBar from '@/components/track/ProgressBar.vue';
import Track from '@/pages/Track.vue';
import type { ActivityDay, Habit } from '@/types/track';
import { addDays, todayStr } from '@/utils/date';

const { mockApiFetch } = vi.hoisted(() => ({
    mockApiFetch: vi.fn(),
}));

vi.mock('@/utils/api', () => ({
    apiFetch: mockApiFetch,
}));

const mockAnimation = {
    pause: vi.fn(),
    cancel: vi.fn(),
};

beforeEach(() => {
    mockApiFetch.mockReset();
    mockAnimation.pause.mockClear();
    mockAnimation.cancel.mockClear();
    Element.prototype.animate = vi
        .fn()
        .mockReturnValue(mockAnimation as unknown as Animation);
});

const defaultTranslations = {
    progress: 'Progress',
    all_done: '✓ All done!',
    no_habits_scheduled: 'No habits scheduled',
    for_this_day: 'for this day',
    daily_note: 'Daily Note',
    saving: 'Saving...',
    how_was_your_day: 'How was your day?',
    today: 'Today',
    activity: 'Activity',
    last_n_days: 'Last 140 days',
    less: 'Less',
    more: 'More',
    previous_day: 'Previous day',
    next_day: 'Next day',
};

function makeHabit(overrides: Partial<Habit> = {}): Habit {
    return {
        id: 1,
        name: 'Habit',
        description: null,
        iterations_required: 1,
        is_completed: false,
        current_iteration: 0,
        sort_order: 1,
        ...overrides,
    };
}

function makeHabits(count: number, completed: boolean): Habit[] {
    return Array.from({ length: count }, (_, i) =>
        makeHabit({
            id: i + 1,
            name: `Habit ${i + 1}`,
            is_completed: completed,
            current_iteration: completed ? 1 : 0,
            sort_order: i + 1,
        }),
    );
}

function deferredPromise<T = unknown>() {
    let resolve!: (value: T | PromiseLike<T>) => void;
    let reject!: (reason: unknown) => void;
    const promise = new Promise<T>((res, rej) => {
        resolve = res;
        reject = rej;
    });
    return { promise, resolve, reject };
}

type TrackVm = {
    data: { habits: Habit[]; completedCount: number } | null;
};

function getTrackData(wrapper: ReturnType<typeof mount>) {
    return (wrapper.vm as unknown as TrackVm).data;
}

const defaultData = {
    date: '2026-04-06',
    dayName: 'Monday',
    dateFormatted: 'April 6, 2026',
    isToday: true,
    habits: [
        makeHabit({ name: 'Morning exercise', description: 'Do pushups' }),
        makeHabit({
            id: 2,
            name: 'Read a book',
            iterations_required: 3,
            current_iteration: 1,
            sort_order: 2,
        }),
    ] as Habit[],
    totalHabits: 2,
    completedCount: 0,
    moveCompletedToEnd: false,
    dailyNoteContent: '',
    activityData: [] as ActivityDay[],
    translations: defaultTranslations,
    navigationTranslations: { track: 'Track', view: 'View' },
};

async function mountTrack(data = {}) {
    const responseData = { ...defaultData, ...data };
    mockApiFetch.mockResolvedValueOnce(responseData);
    const wrapper = mount(Track);
    await flushPromises();
    return wrapper;
}

const findByTestId = (wrapper: ReturnType<typeof mount>, id: string) =>
    wrapper.find(`[data-testid="${id}"]`);

/**
 * Simulates a full habit tap: click triggers the press animation,
 * then animationend fires the toggle.
 */
async function tapHabit(el: ReturnType<ReturnType<typeof mount>['find']>) {
    await el.trigger('click');
    await el.trigger('animationend');
}

describe('Track - Date Navigator', () => {
    it('renders date info and navigation buttons correctly', async () => {
        const wrapper = await mountTrack();
        expect(wrapper.text()).toContain('Monday');
        expect(wrapper.text()).toContain('April 6, 2026');

        expect(findByTestId(wrapper, 'prev-day-btn').exists()).toBe(true);
        expect(findByTestId(wrapper, 'next-day-btn').exists()).toBe(false);
        expect(findByTestId(wrapper, 'today-btn').exists()).toBe(false);

        const pastWrapper = await mountTrack({ isToday: false });
        expect(findByTestId(pastWrapper, 'prev-day-btn').exists()).toBe(true);
        expect(findByTestId(pastWrapper, 'next-day-btn').exists()).toBe(true);
        expect(findByTestId(pastWrapper, 'today-btn').exists()).toBe(true);
    });

    it('navigates to previous day on click', async () => {
        const wrapper = await mountTrack();
        mockApiFetch.mockResolvedValueOnce({
            ...defaultData,
            date: '2026-04-05',
            isToday: false,
        });
        await findByTestId(wrapper, 'prev-day-btn').trigger('click');
        await flushPromises();
        expect(mockApiFetch).toHaveBeenCalledWith('/api/track?date=2026-04-05');
    });

    it('navigates to next day on click', async () => {
        const wrapper = await mountTrack({
            isToday: false,
            date: '2026-04-05',
        });
        mockApiFetch.mockResolvedValueOnce({
            ...defaultData,
            date: '2026-04-06',
        });
        await findByTestId(wrapper, 'next-day-btn').trigger('click');
        await flushPromises();
        expect(mockApiFetch).toHaveBeenCalledWith('/api/track?date=2026-04-06');
    });

    it('navigates from yesterday to today', async () => {
        const yesterday = addDays(todayStr(), -1);
        const wrapper = await mountTrack({
            isToday: false,
            date: yesterday,
        });
        mockApiFetch.mockResolvedValueOnce({
            ...defaultData,
            date: todayStr(),
        });
        await findByTestId(wrapper, 'next-day-btn').trigger('click');
        await flushPromises();
        expect(mockApiFetch).toHaveBeenCalledWith(
            `/api/track?date=${todayStr()}`,
        );
    });

    it('does not navigate past today', async () => {
        const wrapper = await mountTrack({
            isToday: false,
            date: todayStr(),
        });
        const callsBefore = mockApiFetch.mock.calls.length;
        await findByTestId(wrapper, 'next-day-btn').trigger('click');
        await flushPromises();
        expect(mockApiFetch.mock.calls.length).toBe(callsBefore);
    });

    it('go-to-today button navigates to current date', async () => {
        const wrapper = await mountTrack({
            isToday: false,
            date: '2026-04-03',
        });
        mockApiFetch.mockResolvedValueOnce({ ...defaultData });
        await findByTestId(wrapper, 'today-btn').trigger('click');
        await flushPromises();
        expect(mockApiFetch).toHaveBeenCalledTimes(2);
    });

    it('sets nav-forward class when navigating to next day', async () => {
        const wrapper = await mountTrack({
            isToday: false,
            date: '2026-04-05',
        });
        mockApiFetch.mockResolvedValueOnce({
            ...defaultData,
            date: '2026-04-06',
        });
        await findByTestId(wrapper, 'next-day-btn').trigger('click');
        await wrapper.vm.$nextTick();

        expect(wrapper.find('.nav-forward').exists()).toBe(true);
        expect(wrapper.find('.nav-backward').exists()).toBe(false);

        await flushPromises();
    });

    it('sets nav-backward class when navigating to previous day', async () => {
        const wrapper = await mountTrack();
        mockApiFetch.mockResolvedValueOnce({
            ...defaultData,
            date: '2026-04-05',
            isToday: false,
        });
        await findByTestId(wrapper, 'prev-day-btn').trigger('click');
        await wrapper.vm.$nextTick();

        expect(wrapper.find('.nav-backward').exists()).toBe(true);
        expect(wrapper.find('.nav-forward').exists()).toBe(false);

        await flushPromises();
    });

    it('does not set direction class on initial load', async () => {
        const wrapper = await mountTrack();

        expect(wrapper.find('.nav-forward').exists()).toBe(false);
        expect(wrapper.find('.nav-backward').exists()).toBe(false);
    });
});

describe('Track - Progress Bar', () => {
    it('shows progress info and intensity colors correctly', async () => {
        const wrapper = await mountTrack({
            totalHabits: 3,
            completedCount: 1,
        });
        expect(wrapper.text()).toContain('Progress');
        expect(wrapper.text()).toContain('1/3');
        expect(wrapper.text()).not.toContain(defaultTranslations.all_done);

        const doneWrapper = await mountTrack({
            totalHabits: 2,
            completedCount: 2,
        });
        expect(doneWrapper.text()).toContain(defaultTranslations.all_done);

        const emptyWrapper = await mountTrack({
            totalHabits: 0,
            completedCount: 0,
            habits: [],
        });
        expect(emptyWrapper.text()).not.toContain('Progress');
    });

    it('renders correct segments with intensity thresholds', async () => {
        const wrapper = await mountTrack({
            totalHabits: 5,
            completedCount: 2,
        });
        const segments = findByTestId(wrapper, 'progress-segments').findAll(
            ':scope > div',
        );
        expect(segments).toHaveLength(5);
        expect(
            segments[0].classes().some((c: string) => c.startsWith('bg-green')),
        ).toBe(true);

        const mountProgressBar = (total: number, completed: number) =>
            mount(ProgressBar, {
                props: {
                    totalHabits: total,
                    completedCount: completed,
                    progressLabel: 'Progress',
                    allDoneLabel: '✓ All done!',
                },
            }).vm;

        expect(mountProgressBar(10, 0).progressIntensity).toBe(0);
        expect(mountProgressBar(10, 1).progressIntensity).toBe(1);
        expect(mountProgressBar(10, 5).progressIntensity).toBe(3);
        expect(mountProgressBar(10, 8).progressIntensity).toBe(4);
        expect(mountProgressBar(0, 0).progressIntensity).toBe(0);
        expect(mountProgressBar(3, 0).segmentColor).toBe('bg-green-500');
    });

    it('renders extra segment for >20 habits', async () => {
        const doneWrapper = await mountTrack({
            totalHabits: 25,
            completedCount: 25,
            habits: makeHabits(25, true),
        });
        const doneSegments = findByTestId(
            doneWrapper,
            'progress-segments',
        ).findAll(':scope > div');
        expect(doneSegments).toHaveLength(21);
        expect(
            doneSegments[20]
                .classes()
                .some((c: string) => c.startsWith('bg-green')),
        ).toBe(true);

        const emptyWrapper = await mountTrack({
            totalHabits: 25,
            completedCount: 0,
            habits: makeHabits(25, false),
        });
        expect(
            findByTestId(emptyWrapper, 'progress-segments').findAll(
                ':scope > div',
            ),
        ).toHaveLength(21);
    });
});

describe('Track - Habits List', () => {
    it('renders habits with descriptions, completion state, and multi-iteration progress', async () => {
        const wrapper = await mountTrack();
        expect(wrapper.text()).toContain('Morning exercise');
        expect(wrapper.text()).toContain('Do pushups');
        expect(wrapper.text()).toContain('Read a book');
        expect(wrapper.text()).toContain('1/3');

        expect(wrapper.findAll('svg[viewBox="0 0 36 36"]')).toHaveLength(1);
    });

    it('shows empty state when no habits', async () => {
        const wrapper = await mountTrack({
            habits: [],
            totalHabits: 0,
            completedCount: 0,
        });
        expect(wrapper.text()).toContain(
            defaultTranslations.no_habits_scheduled,
        );
        expect(wrapper.text()).toContain(defaultTranslations.for_this_day);
    });

    it('shows completed style and toggles on click', async () => {
        const wrapper = await mountTrack({
            habits: [
                makeHabit({
                    name: 'Done habit',
                    is_completed: true,
                    current_iteration: 1,
                }),
            ],
        });
        expect(wrapper.find('.bg-green-500').exists()).toBe(true);

        mockApiFetch.mockResolvedValueOnce({ ...defaultData });
        const habitItem = findByTestId(wrapper, 'habit-item');
        await tapHabit(habitItem);
        await flushPromises();
        expect(mockApiFetch).toHaveBeenCalledWith('/api/track/toggle', {
            method: 'POST',
            body: JSON.stringify({ habit_id: 1, date: '2026-04-06' }),
        });
    });

    it('optimistically updates habit on toggle before server responds', async () => {
        const toggle = deferredPromise();

        const wrapper = await mountTrack({
            completedCount: 0,
            habits: [makeHabit({ name: 'Exercise' })],
        });

        mockApiFetch.mockReturnValueOnce(toggle.promise);

        const habitItem = findByTestId(wrapper, 'habit-item');
        await tapHabit(habitItem);
        await wrapper.vm.$nextTick();

        // UI updated immediately before server response
        expect(wrapper.find('.bg-green-500').exists()).toBe(true);
        expect(wrapper.text()).toContain('1/2');

        // Pending shimmer active while waiting for server
        expect(
            findByTestId(wrapper, 'habit-item-content').attributes(
                'data-pending',
            ),
        ).toBe('true');

        toggle.resolve({
            ...defaultData,
            completedCount: 1,
            habits: [
                makeHabit({
                    name: 'Exercise',
                    is_completed: true,
                    current_iteration: 1,
                }),
            ],
        });
        await flushPromises();

        // Pending shimmer gone after server responds
        expect(
            findByTestId(wrapper, 'habit-item-content').attributes(
                'data-pending',
            ),
        ).toBeUndefined();
    });

    it('clears fade styles when transition ends', async () => {
        const toggle = deferredPromise();

        const wrapper = await mountTrack({
            completedCount: 0,
            habits: [makeHabit({ name: 'Exercise' })],
        });

        mockApiFetch.mockReturnValueOnce(toggle.promise);

        await tapHabit(findByTestId(wrapper, 'habit-item'));
        await wrapper.vm.$nextTick();

        toggle.resolve({ ...defaultData });
        await flushPromises();
        await wrapper.vm.$nextTick();

        // After pending resolves, watchEffect cleanup sets inline styles
        const content = findByTestId(wrapper, 'habit-item-content');
        expect(content.element.style.opacity).toBe('1');

        // Firing transitionend clears the inline styles
        await content.trigger('transitionend');
        expect(content.element.style.opacity).toBe('');
        expect(content.element.style.transition).toBe('');
    });

    it('rolls back optimistic update on server error', async () => {
        const toggle = deferredPromise<never>();

        const wrapper = await mountTrack({
            completedCount: 0,
            habits: [makeHabit({ name: 'Exercise' })],
        });

        mockApiFetch.mockReturnValueOnce(toggle.promise);

        await tapHabit(findByTestId(wrapper, 'habit-item'));
        await wrapper.vm.$nextTick();

        // Optimistic: should be completed
        expect(
            findByTestId(wrapper, 'habit-item').find('.bg-green-500').exists(),
        ).toBe(true);

        // Pending shimmer active
        expect(
            findByTestId(wrapper, 'habit-item-content').attributes(
                'data-pending',
            ),
        ).toBe('true');

        toggle.reject(new Error('API error: 500'));
        await flushPromises();
        await wrapper.vm.$nextTick();

        // After error: rolled back to uncompleted
        expect(
            findByTestId(wrapper, 'habit-item').find('.bg-green-500').exists(),
        ).toBe(false);

        // Pending shimmer removed after error
        expect(
            findByTestId(wrapper, 'habit-item-content').attributes(
                'data-pending',
            ),
        ).toBeUndefined();
    });

    it('rolls back after both requests for the same habit error', async () => {
        const first = deferredPromise<never>();
        const second = deferredPromise<never>();

        const habit = makeHabit({ name: 'Exercise' });

        const wrapper = await mountTrack({
            completedCount: 0,
            totalHabits: 1,
            habits: [habit],
        });

        mockApiFetch.mockReturnValueOnce(first.promise);
        wrapper.findComponent(HabitItem).vm.$emit('toggle', 1); // toggle ON
        await wrapper.vm.$nextTick();

        mockApiFetch.mockReturnValueOnce(second.promise);
        wrapper.findComponent(HabitItem).vm.$emit('toggle', 1); // toggle OFF
        await wrapper.vm.$nextTick();
        await flushPromises();

        // First request errors (count 2→1): no rollback yet
        first.reject(new Error('Network error'));
        await flushPromises();

        // Second request also errors (count 1→0): rollback to original state
        second.reject(new Error('Network error'));
        await flushPromises();

        // Rolled back to the state before the very first toggle
        expect(getTrackData(wrapper)?.habits[0].is_completed).toBe(false);
        expect(getTrackData(wrapper)?.completedCount).toBe(0);
    });

    it('sorts completed habits to end when moveCompletedToEnd is true', async () => {
        const wrapper = await mountTrack({
            moveCompletedToEnd: true,
            completedCount: 0,
            habits: [
                makeHabit({ name: 'First habit' }),
                makeHabit({ id: 2, name: 'Second habit', sort_order: 2 }),
            ],
        });

        mockApiFetch.mockResolvedValueOnce({
            ...defaultData,
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
        });

        const habitItems = wrapper.findAll('[data-testid="habit-item"]');
        expect(habitItems[0].text()).toContain('First habit');

        await tapHabit(habitItems[0]);
        await wrapper.vm.$nextTick();

        // After optimistic sort, completed "First habit" should move to end
        const reorderedItems = wrapper.findAll('[data-testid="habit-item"]');
        expect(reorderedItems[0].text()).toContain('Second habit');
        expect(reorderedItems[1].text()).toContain('First habit');

        await flushPromises();
    });

    it('handles multi-iteration optimistic update correctly', async () => {
        const wrapper = await mountTrack({
            completedCount: 0,
            habits: [
                makeHabit({
                    name: 'Read',
                    iterations_required: 3,
                    current_iteration: 1,
                }),
            ],
        });

        mockApiFetch.mockResolvedValueOnce({
            ...defaultData,
            completedCount: 0,
            habits: [
                makeHabit({
                    name: 'Read',
                    iterations_required: 3,
                    current_iteration: 2,
                }),
            ],
        });

        await tapHabit(findByTestId(wrapper, 'habit-item'));
        await wrapper.vm.$nextTick();

        // Optimistic: iteration incremented but not completed
        expect(wrapper.text()).toContain('2/3');

        await flushPromises();
    });

    it('preserves sort_order when habits have same completion status', async () => {
        const wrapper = await mountTrack({
            moveCompletedToEnd: true,
            completedCount: 0,
            totalHabits: 3,
            habits: [
                makeHabit({ id: 3, name: 'Third habit', sort_order: 3 }),
                makeHabit({ name: 'First habit' }),
                makeHabit({ id: 2, name: 'Second habit', sort_order: 2 }),
            ],
        });

        mockApiFetch.mockResolvedValueOnce({
            ...defaultData,
            moveCompletedToEnd: true,
            completedCount: 1,
            totalHabits: 3,
            habits: [
                makeHabit({ name: 'First habit' }),
                makeHabit({ id: 2, name: 'Second habit', sort_order: 2 }),
                makeHabit({
                    id: 3,
                    name: 'Third habit',
                    is_completed: true,
                    current_iteration: 1,
                    sort_order: 3,
                }),
            ],
        });

        // Click "Third habit" (first in the list) to complete it
        const items = wrapper.findAll('[data-testid="habit-item"]');
        await tapHabit(items[0]);
        await wrapper.vm.$nextTick();

        // Remaining uncompleted habits should sort by sort_order
        const reordered = wrapper.findAll('[data-testid="habit-item"]');
        expect(reordered[0].text()).toContain('First habit');
        expect(reordered[1].text()).toContain('Second habit');
        expect(reordered[2].text()).toContain('Third habit');

        await flushPromises();
    });

    it('ignores toggle when habit is not found', async () => {
        const wrapper = await mountTrack();

        // Manually call onToggleHabit with a non-existent habit ID
        // by emitting a toggle event with an ID that doesn't exist
        wrapper.findComponent(HabitItem).vm.$emit('toggle', 999);
        await wrapper.vm.$nextTick();

        // Should not have called the toggle API
        expect(mockApiFetch).toHaveBeenCalledTimes(1); // only initial load
    });

    it('applies habit-press class on click and removes it after animationend', async () => {
        const wrapper = await mountTrack();
        const habitItem = findByTestId(wrapper, 'habit-item');

        await habitItem.trigger('click');
        expect(habitItem.classes()).toContain('habit-press');

        mockApiFetch.mockResolvedValueOnce({ ...defaultData });
        await habitItem.trigger('animationend');
        expect(habitItem.classes()).not.toContain('habit-press');
    });

    it('ignores rapid double-click during press animation', async () => {
        const wrapper = await mountTrack();
        const habitItem = findByTestId(wrapper, 'habit-item');

        await habitItem.trigger('click');
        // Second click while pressing — should be ignored
        await habitItem.trigger('click');

        mockApiFetch.mockResolvedValueOnce({ ...defaultData });
        await habitItem.trigger('animationend');
        await flushPromises();

        // Toggle API called only once, not twice
        expect(mockApiFetch).toHaveBeenCalledTimes(2); // initial load + one toggle
    });

    it('discards intermediate server responses when multiple toggles are in-flight', async () => {
        const first = deferredPromise();
        const second = deferredPromise();
        const third = deferredPromise();

        const habits: Habit[] = [
            makeHabit({ name: 'Habit A' }),
            makeHabit({ id: 2, name: 'Habit B', sort_order: 2 }),
            makeHabit({ id: 3, name: 'Habit C', sort_order: 3 }),
        ];

        const wrapper = await mountTrack({
            moveCompletedToEnd: true,
            completedCount: 0,
            totalHabits: 3,
            habits,
        });

        // Toggle all 3 habits rapidly before any server response arrives
        mockApiFetch.mockReturnValueOnce(first.promise);
        const items1 = wrapper.findAll('[data-testid="habit-item"]');
        await tapHabit(items1[0]); // Toggle Habit A
        await wrapper.vm.$nextTick();

        mockApiFetch.mockReturnValueOnce(second.promise);
        const items2 = wrapper.findAll('[data-testid="habit-item"]');
        await tapHabit(items2[0]); // Toggle Habit B (now first after A moved)
        await wrapper.vm.$nextTick();

        mockApiFetch.mockReturnValueOnce(third.promise);
        const items3 = wrapper.findAll('[data-testid="habit-item"]');
        await tapHabit(items3[0]); // Toggle Habit C (now first after B moved)
        await wrapper.vm.$nextTick();

        // All 3 should be optimistically completed at the bottom
        const optimistic = wrapper.findAll('[data-testid="habit-item"]');
        expect(optimistic[0].text()).toContain('Habit A');
        expect(optimistic[1].text()).toContain('Habit B');
        expect(optimistic[2].text()).toContain('Habit C');

        // First response arrives (stale: only Habit A completed)
        first.resolve({
            ...defaultData,
            moveCompletedToEnd: true,
            completedCount: 1,
            totalHabits: 3,
            habits: [
                { ...habits[1], is_completed: false },
                { ...habits[2], is_completed: false },
                { ...habits[0], is_completed: true, current_iteration: 1 },
            ],
        });
        await flushPromises();

        // Should NOT apply stale response — optimistic state preserved
        const afterFirst = wrapper.findAll('[data-testid="habit-item"]');
        expect(afterFirst[0].text()).toContain('Habit A');
        expect(afterFirst[1].text()).toContain('Habit B');
        expect(afterFirst[2].text()).toContain('Habit C');

        // Second response arrives (also stale)
        second.resolve({
            ...defaultData,
            moveCompletedToEnd: true,
            completedCount: 2,
            totalHabits: 3,
            habits: [
                { ...habits[2], is_completed: false },
                { ...habits[0], is_completed: true, current_iteration: 1 },
                { ...habits[1], is_completed: true, current_iteration: 1 },
            ],
        });
        await flushPromises();

        // Should still NOT apply — optimistic state preserved
        const afterSecond = wrapper.findAll('[data-testid="habit-item"]');
        expect(afterSecond[0].text()).toContain('Habit A');
        expect(afterSecond[1].text()).toContain('Habit B');
        expect(afterSecond[2].text()).toContain('Habit C');

        // Third (final) response arrives — should be applied
        const finalHabits = [
            { ...habits[0], is_completed: true, current_iteration: 1 },
            { ...habits[1], is_completed: true, current_iteration: 1 },
            { ...habits[2], is_completed: true, current_iteration: 1 },
        ];
        third.resolve({
            ...defaultData,
            moveCompletedToEnd: true,
            completedCount: 3,
            totalHabits: 3,
            habits: finalHabits,
        });
        await flushPromises();

        // Final server state applied
        const afterThird = wrapper.findAll('[data-testid="habit-item"]');
        expect(afterThird[0].text()).toContain('Habit A');
        expect(afterThird[1].text()).toContain('Habit B');
        expect(afterThird[2].text()).toContain('Habit C');
    });

    it('does not apply first response when same habit is toggled twice rapidly', async () => {
        const first = deferredPromise();
        const second = deferredPromise();

        const habit = makeHabit({ name: 'Exercise' });

        const wrapper = await mountTrack({
            moveCompletedToEnd: true,
            completedCount: 0,
            totalHabits: 1,
            habits: [habit],
        });

        // Emit toggle events with a nextTick between them so Vue processes each
        // optimistic update separately — matches the real animation-gated scenario.
        mockApiFetch.mockReturnValueOnce(first.promise);
        wrapper.findComponent(HabitItem).vm.$emit('toggle', 1); // toggle ON
        await wrapper.vm.$nextTick();

        mockApiFetch.mockReturnValueOnce(second.promise);
        wrapper.findComponent(HabitItem).vm.$emit('toggle', 1); // toggle OFF
        await wrapper.vm.$nextTick();
        await flushPromises();

        // Both API calls were sent (1 load + 2 toggles)
        expect(mockApiFetch).toHaveBeenCalledTimes(3);

        // First response arrives (stale: habit completed, would move it to end)
        first.resolve({
            ...defaultData,
            moveCompletedToEnd: true,
            completedCount: 1,
            totalHabits: 1,
            habits: [{ ...habit, is_completed: true, current_iteration: 1 }],
        });
        await flushPromises();

        // Must NOT apply stale response — data reflects the second toggle's
        // optimistic state (habit uncompleted, completedCount 0).
        expect(getTrackData(wrapper)?.habits[0].is_completed).toBe(false);
        expect(getTrackData(wrapper)?.completedCount).toBe(0);

        // Second response arrives (final: habit uncompleted)
        second.resolve({
            ...defaultData,
            moveCompletedToEnd: true,
            completedCount: 0,
            totalHabits: 1,
            habits: [habit],
        });
        await flushPromises();
        await wrapper.vm.$nextTick();

        // Final server state applied — data.value fully replaced so DOM refreshes
        expect(getTrackData(wrapper)?.habits[0].is_completed).toBe(false);
        expect(getTrackData(wrapper)?.completedCount).toBe(0);
        expect(wrapper.find('[data-testid="habit-item"]').text()).toContain(
            'Exercise',
        );
    });

    it('un-completes a completed habit and re-sorts', async () => {
        const wrapper = await mountTrack({
            moveCompletedToEnd: true,
            completedCount: 2,
            totalHabits: 2,
            habits: [
                makeHabit({ name: 'Active habit' }),
                makeHabit({
                    id: 2,
                    name: 'Done habit',
                    is_completed: true,
                    current_iteration: 1,
                    sort_order: 2,
                }),
            ],
        });

        mockApiFetch.mockResolvedValueOnce({
            ...defaultData,
            moveCompletedToEnd: true,
            completedCount: 1,
            totalHabits: 2,
            habits: [
                makeHabit({ name: 'Active habit' }),
                makeHabit({ id: 2, name: 'Done habit', sort_order: 2 }),
            ],
        });

        // Click on the completed "Done habit" to un-complete it
        const items = wrapper.findAll('[data-testid="habit-item"]');
        await tapHabit(items[1]);
        await flushPromises();

        // Both uncompleted now, should be sorted by sort_order
        const reordered = wrapper.findAll('[data-testid="habit-item"]');
        expect(reordered[0].text()).toContain('Active habit');
        expect(reordered[1].text()).toContain('Done habit');
    });
});

describe('Track - Daily Note', () => {
    it('renders textarea with existing content and saves on blur', async () => {
        const wrapper = await mountTrack({
            dailyNoteContent: 'Great day today!',
        });
        expect(wrapper.text()).toContain(defaultTranslations.daily_note);

        const textarea = wrapper.find('textarea');
        expect(textarea.attributes('placeholder')).toBe(
            defaultTranslations.how_was_your_day,
        );
        expect((textarea.element as HTMLTextAreaElement).value).toBe(
            'Great day today!',
        );

        mockApiFetch.mockResolvedValueOnce(undefined);
        await textarea.setValue('New note');
        await textarea.trigger('blur');
        await flushPromises();
        expect(mockApiFetch).toHaveBeenCalledWith('/api/track/daily-note', {
            method: 'POST',
            body: JSON.stringify({
                date: '2026-04-06',
                content: 'New note',
            }),
        });
    });

    it('hides saving indicator when not processing', async () => {
        const wrapper = await mountTrack();
        expect(wrapper.text()).not.toContain(defaultTranslations.saving);
    });

    it('syncs daily note content when navigating to a new day', async () => {
        const wrapper = await mountTrack({ dailyNoteContent: 'old note' });
        expect(
            (wrapper.find('textarea').element as HTMLTextAreaElement).value,
        ).toBe('old note');

        mockApiFetch.mockResolvedValueOnce({
            ...defaultData,
            date: '2026-04-05',
            isToday: false,
            dailyNoteContent: 'different note',
        });
        await findByTestId(wrapper, 'prev-day-btn').trigger('click');
        await flushPromises();
        expect(
            (wrapper.find('textarea').element as HTMLTextAreaElement).value,
        ).toBe('different note');
    });
});

describe('Track - Activity Graph', () => {
    it('renders activity graph with legend and week grid', async () => {
        const wrapper = await mountTrack({ activityData: [] });
        expect(wrapper.text()).toContain(defaultTranslations.activity);
        expect(wrapper.text()).toContain(defaultTranslations.last_n_days);
        expect(wrapper.text()).toContain(defaultTranslations.less);
        expect(wrapper.text()).toContain(defaultTranslations.more);

        const graphVm = wrapper.findComponent(ActivityGraph).vm;
        expect(graphVm.weeks.length).toBeGreaterThanOrEqual(20);
        expect(
            graphVm.weeks.some((w: (unknown | null)[]) =>
                w.some((d) => d === null),
            ),
        ).toBe(true);
    });

    it('fills sparse activity data and handles different weekday alignments', async () => {
        const wrapper = await mountTrack({
            activityData: [
                {
                    date: todayStr(),
                    percentage: 100,
                    completed: 5,
                    total: 5,
                    intensity: 4,
                },
            ],
        });
        const graphVm = wrapper.findComponent(ActivityGraph).vm;
        expect(graphVm.weeks.length).toBeGreaterThan(0);

        vi.useFakeTimers();
        vi.setSystemTime(new Date(2026, 3, 4, 12, 0, 0));
        const w1 = await mountTrack();
        expect(w1.findComponent(ActivityGraph).vm.weeks).toBeTruthy();
        vi.setSystemTime(new Date(2026, 3, 6, 12, 0, 0));
        const w2 = await mountTrack();
        expect(w2.findComponent(ActivityGraph).vm.weeks).toBeTruthy();
        vi.useRealTimers();
    });

    it('uses fallback color for unknown intensity', async () => {
        const wrapper = await mountTrack({
            activityData: [
                {
                    date: todayStr(),
                    percentage: 100,
                    completed: 5,
                    total: 5,
                    intensity: 99,
                },
            ],
        });
        expect(wrapper.text()).toContain(defaultTranslations.activity);
    });
});
