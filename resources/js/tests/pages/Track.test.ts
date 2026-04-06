import { flushPromises, mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import ActivityGraph from '@/components/track/ActivityGraph.vue';
import ProgressBar from '@/components/track/ProgressBar.vue';
import Track from '@/pages/Track.vue';
import type { ActivityDay, Habit } from '@/types';
import { addDays, todayStr } from '@/utils/date';

const { mockApiFetch } = vi.hoisted(() => ({
    mockApiFetch: vi.fn(),
}));

vi.mock('@/utils/api', () => ({
    apiFetch: mockApiFetch,
}));

beforeEach(() => {
    mockApiFetch.mockReset();
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

function makeHabits(count: number, completed: boolean): Habit[] {
    return Array.from({ length: count }, (_, i) => ({
        id: i + 1,
        name: `Habit ${i + 1}`,
        description: null,
        iterations_required: 1,
        is_completed: completed,
        current_iteration: completed ? 1 : 0,
        sort_order: i + 1,
    }));
}

const defaultData = {
    date: '2026-04-06',
    dayName: 'Monday',
    dateFormatted: 'April 6, 2026',
    isToday: true,
    habits: [
        {
            id: 1,
            name: 'Morning exercise',
            description: 'Do pushups',
            iterations_required: 1,
            is_completed: false,
            current_iteration: 0,
            sort_order: 1,
        },
        {
            id: 2,
            name: 'Read a book',
            description: null,
            iterations_required: 3,
            is_completed: false,
            current_iteration: 1,
            sort_order: 2,
        },
    ] as Habit[],
    totalHabits: 2,
    completedCount: 0,
    moveCompletedToEnd: false,
    dailyNoteContent: '',
    activityData: [] as ActivityDay[],
    translations: defaultTranslations,
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
                {
                    id: 1,
                    name: 'Done habit',
                    description: null,
                    iterations_required: 1,
                    is_completed: true,
                    current_iteration: 1,
                },
            ],
        });
        expect(wrapper.find('.bg-green-500').exists()).toBe(true);

        mockApiFetch.mockResolvedValueOnce({ ...defaultData });
        const habitItem = findByTestId(wrapper, 'habit-item');
        await habitItem.trigger('click');
        await flushPromises();
        expect(mockApiFetch).toHaveBeenCalledWith('/api/track/toggle', {
            method: 'POST',
            body: JSON.stringify({ habit_id: 1, date: '2026-04-06' }),
        });
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
