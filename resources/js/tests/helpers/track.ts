import { flushPromises, mount } from '@vue/test-utils';
import Track from '@/pages/Track.vue';
import type { ActivityDay, Habit, TrackData } from '@/types/track';

type TrackVm = {
    data: { habits: Habit[]; completedCount: number } | null;
};

export function getTrackData(wrapper: ReturnType<typeof mount>) {
    return (wrapper.vm as unknown as TrackVm).data;
}

export function deferredPromise<T = unknown>() {
    let resolve!: (value: T | PromiseLike<T>) => void;
    let reject!: (reason: unknown) => void;
    const promise = new Promise<T>((res, rej) => {
        resolve = res;
        reject = rej;
    });
    return { promise, resolve, reject };
}

export async function mountTrack(
    mockApiFetch: ReturnType<typeof import('vitest').vi.fn>,
    data = {},
) {
    const responseData = makeTrackResponse(data);
    mockApiFetch.mockResolvedValueOnce(responseData);
    const wrapper = mount(Track);
    await flushPromises();
    return wrapper;
}

export const findByTestId = (wrapper: ReturnType<typeof mount>, id: string) =>
    wrapper.find(`[data-testid="${id}"]`);

/**
 * Simulates a full habit tap: click triggers the press animation,
 * then animationend fires the toggle.
 */
export async function tapHabit(
    el: ReturnType<ReturnType<typeof mount>['find']>,
) {
    await el.trigger('click');
    await el.trigger('animationend');
}

export const defaultTrackTranslations = {
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

export function makeHabit(overrides: Partial<Habit> = {}): Habit {
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

export function makeHabits(count: number, completed: boolean): Habit[] {
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

export const defaultTrackData: TrackData = {
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
    translations: defaultTrackTranslations,
};

export function makeTrackResponse(overrides: Partial<TrackData> = {}) {
    return {
        data: { ...defaultTrackData, ...overrides },
        navigationTranslations: { track: 'Track', view: 'View' },
        settings: { locale: 'en', theme: 'system' as const },
    };
}
