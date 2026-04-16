import { flushPromises } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import ActivityGraph from '@/components/track/ActivityGraph.vue';
import {
    defaultTrackTranslations,
    deferredPromise,
    findByTestId,
    makeHabit,
    makeHabits,
    makeTrackResponse,
    mountTrack,
    tapHabit,
} from '@/tests/helpers/track';
import { addDays, todayStr } from '@/utils/date';

const { mockApiFetch, mockRouteQuery, mockReplace } = vi.hoisted(() => ({
    mockApiFetch: vi.fn(),
    mockRouteQuery: { value: {} as Record<string, string> },
    mockReplace: vi.fn(),
}));

vi.mock('@/utils/api', () => ({
    apiFetch: mockApiFetch,
}));

vi.mock('vue-router', () => ({
    useRoute: () => ({ query: mockRouteQuery.value }),
    useRouter: () => ({ replace: mockReplace }),
}));

beforeEach(() => {
    mockApiFetch.mockReset();
    mockRouteQuery.value = {};
    mockReplace.mockReset();
    Element.prototype.animate = vi
        .fn()
        .mockReturnValue({ pause: vi.fn(), cancel: vi.fn() });
});

// ─── Date Navigator ─────────────────────────────────────────────

describe('Track - Date Navigator', () => {
    it('renders date info and navigation buttons correctly', async () => {
        const wrapper = await mountTrack(mockApiFetch);
        expect(wrapper.text()).toContain('Monday');
        expect(wrapper.text()).toContain('April 6, 2026');

        expect(findByTestId(wrapper, 'prev-day-btn').exists()).toBe(true);
        expect(findByTestId(wrapper, 'next-day-btn').exists()).toBe(false);
        expect(findByTestId(wrapper, 'today-btn').exists()).toBe(false);

        const pastWrapper = await mountTrack(mockApiFetch, { isToday: false });
        expect(findByTestId(pastWrapper, 'prev-day-btn').exists()).toBe(true);
        expect(findByTestId(pastWrapper, 'next-day-btn').exists()).toBe(true);
        expect(findByTestId(pastWrapper, 'today-btn').exists()).toBe(true);
    });

    it('navigates to previous day on click', async () => {
        const wrapper = await mountTrack(mockApiFetch);
        mockApiFetch.mockResolvedValueOnce(
            makeTrackResponse({
                date: '2026-04-05',
                isToday: false,
            }),
        );
        await findByTestId(wrapper, 'prev-day-btn').trigger('click');
        await flushPromises();
        expect(mockApiFetch).toHaveBeenCalledWith('/api/track?date=2026-04-05');
    });

    it('navigates to next day on click', async () => {
        const wrapper = await mountTrack(mockApiFetch, {
            isToday: false,
            date: '2026-04-05',
        });
        mockApiFetch.mockResolvedValueOnce(
            makeTrackResponse({
                date: '2026-04-06',
            }),
        );
        await findByTestId(wrapper, 'next-day-btn').trigger('click');
        await flushPromises();
        expect(mockApiFetch).toHaveBeenCalledWith('/api/track?date=2026-04-06');
    });

    it('navigates from yesterday to today', async () => {
        const yesterday = addDays(todayStr(), -1);
        const wrapper = await mountTrack(mockApiFetch, {
            isToday: false,
            date: yesterday,
        });
        mockApiFetch.mockResolvedValueOnce(
            makeTrackResponse({
                date: todayStr(),
            }),
        );
        await findByTestId(wrapper, 'next-day-btn').trigger('click');
        await flushPromises();
        expect(mockApiFetch).toHaveBeenCalledWith(
            `/api/track?date=${todayStr()}`,
        );
    });

    it('does not navigate past today', async () => {
        const wrapper = await mountTrack(mockApiFetch, {
            isToday: false,
            date: todayStr(),
        });
        const callsBefore = mockApiFetch.mock.calls.length;
        await findByTestId(wrapper, 'next-day-btn').trigger('click');
        await flushPromises();
        expect(mockApiFetch.mock.calls.length).toBe(callsBefore);
    });

    it('go-to-today button navigates to current date', async () => {
        const wrapper = await mountTrack(mockApiFetch, {
            isToday: false,
            date: '2026-04-03',
        });
        mockApiFetch.mockResolvedValueOnce(makeTrackResponse());
        await findByTestId(wrapper, 'today-btn').trigger('click');
        await flushPromises();
        expect(mockApiFetch).toHaveBeenCalledTimes(2);
    });

    it('sets nav-forward class when navigating to next day', async () => {
        const wrapper = await mountTrack(mockApiFetch, {
            isToday: false,
            date: '2026-04-05',
        });
        mockApiFetch.mockResolvedValueOnce(
            makeTrackResponse({
                date: '2026-04-06',
            }),
        );
        await findByTestId(wrapper, 'next-day-btn').trigger('click');
        await wrapper.vm.$nextTick();

        expect(wrapper.find('.nav-forward').exists()).toBe(true);
        expect(wrapper.find('.nav-backward').exists()).toBe(false);

        await flushPromises();
    });

    it('sets nav-backward class when navigating to previous day', async () => {
        const wrapper = await mountTrack(mockApiFetch);
        mockApiFetch.mockResolvedValueOnce(
            makeTrackResponse({
                date: '2026-04-05',
                isToday: false,
            }),
        );
        await findByTestId(wrapper, 'prev-day-btn').trigger('click');
        await wrapper.vm.$nextTick();

        expect(wrapper.find('.nav-backward').exists()).toBe(true);
        expect(wrapper.find('.nav-forward').exists()).toBe(false);

        await flushPromises();
    });

    it('does not set direction class on initial load', async () => {
        const wrapper = await mountTrack(mockApiFetch);

        expect(wrapper.find('.nav-forward').exists()).toBe(false);
        expect(wrapper.find('.nav-backward').exists()).toBe(false);
    });
});

// ─── Progress Bar ───────────────────────────────────────────────

describe('Track - Progress Bar', () => {
    it('shows progress info and intensity colors correctly', async () => {
        const wrapper = await mountTrack(mockApiFetch, {
            totalHabits: 3,
            completedCount: 1,
        });
        expect(wrapper.text()).toContain('Progress');
        expect(wrapper.text()).toContain('1/3');
        expect(wrapper.text()).not.toContain(defaultTrackTranslations.all_done);

        const doneWrapper = await mountTrack(mockApiFetch, {
            totalHabits: 2,
            completedCount: 2,
        });
        expect(doneWrapper.text()).toContain(defaultTrackTranslations.all_done);

        const emptyWrapper = await mountTrack(mockApiFetch, {
            totalHabits: 0,
            completedCount: 0,
            habits: [],
        });
        expect(emptyWrapper.text()).not.toContain('Progress');
    });

    it('renders correct segments with intensity thresholds', async () => {
        const wrapper = await mountTrack(mockApiFetch, {
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
    });

    it('renders extra segment for >20 habits', async () => {
        const doneWrapper = await mountTrack(mockApiFetch, {
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

        const emptyWrapper = await mountTrack(mockApiFetch, {
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

// ─── Habits List ────────────────────────────────────────────────

describe('Track - Habits List', () => {
    it('renders habits with descriptions, completion state, and multi-iteration progress', async () => {
        const wrapper = await mountTrack(mockApiFetch);
        expect(wrapper.text()).toContain('Morning exercise');
        expect(wrapper.text()).toContain('Do pushups');
        expect(wrapper.text()).toContain('Read a book');
        expect(wrapper.text()).toContain('1/3');

        expect(wrapper.findAll('svg[viewBox="0 0 36 36"]')).toHaveLength(1);
    });

    it('sets stroke-linecap to butt when current_iteration is 0 and round when > 0', async () => {
        const wrapper = await mountTrack(mockApiFetch, {
            habits: [
                makeHabit({
                    name: 'Zero iterations',
                    iterations_required: 3,
                    current_iteration: 0,
                }),
            ],
        });

        const circles = wrapper
            .find('svg[viewBox="0 0 36 36"]')
            .findAll('circle');
        const progressCircle = circles[1];
        expect(progressCircle.attributes('stroke-linecap')).toBe('butt');

        const wrapperWithProgress = await mountTrack(mockApiFetch, {
            habits: [
                makeHabit({
                    name: 'One iteration',
                    iterations_required: 3,
                    current_iteration: 1,
                }),
            ],
        });

        const progressCircles = wrapperWithProgress
            .find('svg[viewBox="0 0 36 36"]')
            .findAll('circle');
        expect(progressCircles[1].attributes('stroke-linecap')).toBe('round');
    });

    it('shows empty state when no habits', async () => {
        const wrapper = await mountTrack(mockApiFetch, {
            habits: [],
            totalHabits: 0,
            completedCount: 0,
        });
        expect(wrapper.text()).toContain(
            defaultTrackTranslations.no_habits_scheduled,
        );
        expect(wrapper.text()).toContain(defaultTrackTranslations.for_this_day);
    });

    it('shows completed style and toggles on click', async () => {
        const wrapper = await mountTrack(mockApiFetch, {
            habits: [
                makeHabit({
                    name: 'Done habit',
                    is_completed: true,
                    current_iteration: 1,
                }),
            ],
        });
        expect(wrapper.find('.bg-green-500').exists()).toBe(true);

        mockApiFetch.mockResolvedValueOnce(makeTrackResponse());
        const habitItem = findByTestId(wrapper, 'habit-item');
        await tapHabit(habitItem);
        await flushPromises();
        expect(mockApiFetch).toHaveBeenCalledWith(
            '/api/track/toggle',
            {
                method: 'POST',
                body: JSON.stringify({ habit_id: 1, date: '2026-04-06' }),
            },
            { silent: true },
        );
    });

    it('shows pending shimmer while server request is in-flight and clears it after', async () => {
        const toggle = deferredPromise();

        const wrapper = await mountTrack(mockApiFetch, {
            completedCount: 0,
            habits: [makeHabit({ name: 'Exercise' })],
        });

        mockApiFetch.mockReturnValueOnce(toggle.promise);

        const habitItem = findByTestId(wrapper, 'habit-item');
        await tapHabit(habitItem);
        await wrapper.vm.$nextTick();

        // Pending shimmer active while waiting for server
        expect(
            findByTestId(wrapper, 'habit-item-content').attributes(
                'data-pending',
            ),
        ).toBe('true');

        toggle.resolve(
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

        const wrapper = await mountTrack(mockApiFetch, {
            completedCount: 0,
            habits: [makeHabit({ name: 'Exercise' })],
        });

        mockApiFetch.mockReturnValueOnce(toggle.promise);

        await tapHabit(findByTestId(wrapper, 'habit-item'));
        await wrapper.vm.$nextTick();

        toggle.resolve(makeTrackResponse());
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

    it('applies habit-press class on click and removes it after animationend', async () => {
        const wrapper = await mountTrack(mockApiFetch);
        const habitItem = findByTestId(wrapper, 'habit-item');

        await habitItem.trigger('click');
        expect(habitItem.classes()).toContain('habit-press');

        mockApiFetch.mockResolvedValueOnce(makeTrackResponse());
        await habitItem.trigger('animationend');
        expect(habitItem.classes()).not.toContain('habit-press');
    });

    it('ignores rapid double-click during press animation', async () => {
        const wrapper = await mountTrack(mockApiFetch);
        const habitItem = findByTestId(wrapper, 'habit-item');

        await habitItem.trigger('click');
        // Second click while pressing — should be ignored
        await habitItem.trigger('click');

        mockApiFetch.mockResolvedValueOnce(makeTrackResponse());
        await habitItem.trigger('animationend');
        await flushPromises();

        // Toggle API called only once, not twice
        expect(mockApiFetch).toHaveBeenCalledTimes(2); // initial load + one toggle
    });

    it('toggles on Space and Enter keys for keyboard users', async () => {
        const wrapper = await mountTrack(mockApiFetch);
        const habitItem = findByTestId(wrapper, 'habit-item');

        // Space activates the press animation.
        await habitItem.trigger('keydown', { key: ' ' });
        expect(habitItem.classes()).toContain('habit-press');

        mockApiFetch.mockResolvedValueOnce(makeTrackResponse());
        await habitItem.trigger('animationend');
        await flushPromises();
        expect(habitItem.classes()).not.toContain('habit-press');

        // Enter also activates.
        await habitItem.trigger('keydown', { key: 'Enter' });
        expect(habitItem.classes()).toContain('habit-press');

        mockApiFetch.mockResolvedValueOnce(makeTrackResponse());
        await habitItem.trigger('animationend');
        await flushPromises();
        expect(habitItem.classes()).not.toContain('habit-press');

        // Other keys are ignored.
        await habitItem.trigger('keydown', { key: 'a' });
        expect(habitItem.classes()).not.toContain('habit-press');
    });

    it('exposes checkbox semantics for screen readers', async () => {
        const wrapper = await mountTrack(mockApiFetch, {
            habits: [
                makeHabit({
                    name: 'Meditate',
                    is_completed: true,
                    current_iteration: 1,
                }),
            ],
        });
        const habitItem = findByTestId(wrapper, 'habit-item');
        expect(habitItem.attributes('role')).toBe('checkbox');
        expect(habitItem.attributes('aria-checked')).toBe('true');
        expect(habitItem.attributes('aria-label')).toBe('Meditate');
        expect(habitItem.attributes('tabindex')).toBe('0');
    });
});

// ─── Daily Note ─────────────────────────────────────────────────

describe('Track - Daily Note', () => {
    it('renders textarea with existing content and saves on blur', async () => {
        const wrapper = await mountTrack(mockApiFetch, {
            dailyNoteContent: 'Great day today!',
        });
        expect(wrapper.text()).toContain(defaultTrackTranslations.daily_note);

        const textarea = wrapper.find('textarea');
        expect(textarea.attributes('placeholder')).toBe(
            defaultTrackTranslations.how_was_your_day,
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
        const wrapper = await mountTrack(mockApiFetch);
        expect(wrapper.text()).not.toContain(defaultTrackTranslations.saving);
    });

    it('syncs daily note content when navigating to a new day', async () => {
        const wrapper = await mountTrack(mockApiFetch, {
            dailyNoteContent: 'old note',
        });
        expect(
            (wrapper.find('textarea').element as HTMLTextAreaElement).value,
        ).toBe('old note');

        mockApiFetch.mockResolvedValueOnce(
            makeTrackResponse({
                date: '2026-04-05',
                isToday: false,
                dailyNoteContent: 'different note',
            }),
        );
        await findByTestId(wrapper, 'prev-day-btn').trigger('click');
        await flushPromises();
        expect(
            (wrapper.find('textarea').element as HTMLTextAreaElement).value,
        ).toBe('different note');
    });
});

// ─── Activity Graph ─────────────────────────────────────────────

describe('Track - Activity Graph', () => {
    it('renders activity graph with legend and week grid', async () => {
        vi.useFakeTimers();
        vi.setSystemTime(new Date(2026, 3, 4, 12, 0, 0)); // Saturday – ensures null padding days
        const wrapper = await mountTrack(mockApiFetch, { activityData: [] });
        expect(wrapper.text()).toContain(defaultTrackTranslations.activity);
        expect(wrapper.text()).toContain(defaultTrackTranslations.last_n_days);
        expect(wrapper.text()).toContain(defaultTrackTranslations.less);
        expect(wrapper.text()).toContain(defaultTrackTranslations.more);

        const graphVm = wrapper.findComponent(ActivityGraph).vm;
        expect(graphVm.weeks.length).toBeGreaterThanOrEqual(20);
        expect(
            graphVm.weeks.some((w: (unknown | null)[]) =>
                w.some((d) => d === null),
            ),
        ).toBe(true);
        vi.useRealTimers();
    });

    it('fills sparse activity data and handles different weekday alignments', async () => {
        const wrapper = await mountTrack(mockApiFetch, {
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
        const w1 = await mountTrack(mockApiFetch);
        expect(w1.findComponent(ActivityGraph).vm.weeks).toBeTruthy();
        vi.setSystemTime(new Date(2026, 3, 6, 12, 0, 0));
        const w2 = await mountTrack(mockApiFetch);
        expect(w2.findComponent(ActivityGraph).vm.weeks).toBeTruthy();
        vi.useRealTimers();
    });

    it('uses fallback color for unknown intensity', async () => {
        const wrapper = await mountTrack(mockApiFetch, {
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
        expect(wrapper.text()).toContain(defaultTrackTranslations.activity);
    });
});

// ─── URL Sync ───────────────────────────────────────────────────

describe('Track - URL Sync', () => {
    it('does not add date query when on today', async () => {
        await mountTrack(mockApiFetch);
        expect(mockReplace).toHaveBeenCalledWith({ query: {} });
    });

    it('adds date query when not on today', async () => {
        await mountTrack(mockApiFetch, { isToday: false, date: '2026-04-05' });
        expect(mockReplace).toHaveBeenCalledWith({
            query: { date: '2026-04-05' },
        });
    });

    it('loads initial date from URL query', async () => {
        mockRouteQuery.value = { date: '2026-04-01' };
        await mountTrack(mockApiFetch, { isToday: false, date: '2026-04-01' });
        expect(mockApiFetch).toHaveBeenCalledWith('/api/track?date=2026-04-01');
    });

    it('updates URL when navigating to a different date', async () => {
        const wrapper = await mountTrack(mockApiFetch);
        mockApiFetch.mockResolvedValueOnce(
            makeTrackResponse({
                date: '2026-04-05',
                isToday: false,
            }),
        );
        await findByTestId(wrapper, 'prev-day-btn').trigger('click');
        await flushPromises();
        expect(mockReplace).toHaveBeenLastCalledWith({
            query: { date: '2026-04-05' },
        });
    });

    it('clears URL query when navigating back to today', async () => {
        const wrapper = await mountTrack(mockApiFetch, {
            isToday: false,
            date: '2026-04-05',
        });
        mockReplace.mockReset();
        mockApiFetch.mockResolvedValueOnce(makeTrackResponse());
        await findByTestId(wrapper, 'today-btn').trigger('click');
        await flushPromises();
        expect(mockReplace).toHaveBeenCalledWith({ query: {} });
    });
});
