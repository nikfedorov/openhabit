import { flushPromises, mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import LifeGrid from '@/components/view/LifeGrid.vue';
import LifeHeader from '@/components/view/LifeHeader.vue';
import WeekGrid from '@/components/view/WeekGrid.vue';
import WeekNavigator from '@/components/view/WeekNavigator.vue';
import YearGrid from '@/components/view/YearGrid.vue';
import YearNavigator from '@/components/view/YearNavigator.vue';
import View from '@/pages/View.vue';
import {
    defaultLifeStats,
    defaultNavigationTranslations,
    defaultViewTranslations,
    makeFranklinGrid,
} from '@/tests/helpers/view';
import type { ViewData } from '@/types/view';

const { mockApiFetch } = vi.hoisted(() => ({
    mockApiFetch: vi.fn(),
}));

vi.mock('@/utils/api', () => ({
    apiFetch: mockApiFetch,
}));

beforeEach(() => {
    mockApiFetch.mockReset();
    localStorage.clear();
});

function makeViewData(overrides: Partial<ViewData> = {}): ViewData {
    return {
        tab: 'week',
        weekStart: '2026-04-06',
        weekEnd: '2026-04-12',
        weekStartFormatted: 'Apr 6',
        weekEndFormatted: 'Apr 12',
        weekEndFormattedFull: 'Apr 12, 2026',
        weekYear: '2026',
        isCurrentWeek: true,
        franklinGrid: makeFranklinGrid(),
        selectedYear: 25,
        birthdate: '2001-03-15',
        currentAge: 25,
        lifeStats: defaultLifeStats,
        weeklyActivityData: [],
        yearlyActivityData: [],
        translations: defaultViewTranslations,
        navigationTranslations: defaultNavigationTranslations,
        ...overrides,
    };
}

async function mountView(data?: Partial<ViewData>) {
    const responseData = makeViewData(data);
    mockApiFetch.mockResolvedValueOnce(responseData);
    const wrapper = mount(View);
    await flushPromises();
    return wrapper;
}

// ─── View Page ──────────────────────────────────────────────────

describe('View - Tab Navigation', () => {
    it('renders three tab buttons', async () => {
        const wrapper = await mountView();
        const buttons = wrapper.findAll('button');
        const tabButtons = buttons.filter(
            (b) =>
                b.text() === 'Week' ||
                b.text() === 'Year' ||
                b.text() === 'Life',
        );
        expect(tabButtons).toHaveLength(3);
    });

    it('shows week tab content by default', async () => {
        const wrapper = await mountView();
        expect(wrapper.findComponent(WeekNavigator).exists()).toBe(true);
        expect(wrapper.findComponent(WeekGrid).exists()).toBe(true);
        expect(wrapper.findComponent(YearNavigator).exists()).toBe(false);
        expect(wrapper.findComponent(LifeHeader).exists()).toBe(false);
    });

    it('switches to year tab on click', async () => {
        const wrapper = await mountView();
        mockApiFetch.mockResolvedValueOnce(makeViewData({ tab: 'year' }));
        const yearBtn = wrapper
            .findAll('button')
            .find((b) => b.text() === 'Year')!;
        await yearBtn.trigger('click');
        await flushPromises();
        expect(mockApiFetch).toHaveBeenCalledTimes(2);
        expect(wrapper.findComponent(YearNavigator).exists()).toBe(true);
    });

    it('switches to life tab on click', async () => {
        const wrapper = await mountView();
        mockApiFetch.mockResolvedValueOnce(makeViewData({ tab: 'life' }));
        const lifeBtn = wrapper
            .findAll('button')
            .find((b) => b.text() === 'Life')!;
        await lifeBtn.trigger('click');
        await flushPromises();
        expect(wrapper.findComponent(LifeHeader).exists()).toBe(true);
        expect(wrapper.findComponent(LifeGrid).exists()).toBe(true);
    });

    it('switching to year tab sets selectedYear to currentAge when null', async () => {
        const wrapper = await mountView({
            selectedYear: null,
            currentAge: 30,
        });
        mockApiFetch.mockResolvedValueOnce(
            makeViewData({ tab: 'year', selectedYear: 30 }),
        );
        const yearBtn = wrapper
            .findAll('button')
            .find((b) => b.text() === 'Year')!;
        await yearBtn.trigger('click');
        await flushPromises();
        expect(mockApiFetch).toHaveBeenLastCalledWith(
            expect.stringContaining('year=30'),
        );
    });

    it('does not reload data when switching to week tab', async () => {
        const wrapper = await mountView();
        const weekBtn = wrapper
            .findAll('button')
            .find((b) => b.text() === 'Week')!;
        await weekBtn.trigger('click');
        await flushPromises();
        // Only the initial load call
        expect(mockApiFetch).toHaveBeenCalledTimes(1);
    });

    it('emits navigation-translations on load', async () => {
        const wrapper = await mountView();
        expect(wrapper.emitted('navigation-translations')).toBeTruthy();
        expect(wrapper.emitted('navigation-translations')![0]).toEqual([
            defaultNavigationTranslations,
        ]);
    });
});

// ─── Week Navigation ────────────────────────────────────────────

describe('View - Week Navigation', () => {
    it('navigates to previous week', async () => {
        const wrapper = await mountView();
        mockApiFetch.mockResolvedValueOnce(
            makeViewData({
                weekStart: '2026-03-30',
                isCurrentWeek: false,
            }),
        );
        wrapper.findComponent(WeekNavigator).vm.$emit('previousWeek');
        await flushPromises();
        expect(mockApiFetch).toHaveBeenLastCalledWith(
            expect.stringContaining('week=2026-03-30'),
        );
    });

    it('navigates to next week', async () => {
        const wrapper = await mountView({ isCurrentWeek: false });
        mockApiFetch.mockResolvedValueOnce(makeViewData());
        wrapper.findComponent(WeekNavigator).vm.$emit('nextWeek');
        await flushPromises();
        expect(mockApiFetch).toHaveBeenLastCalledWith(
            expect.stringContaining('week=2026-04-13'),
        );
    });

    it('navigates to current week', async () => {
        const wrapper = await mountView({ isCurrentWeek: false });
        mockApiFetch.mockResolvedValueOnce(makeViewData());
        wrapper.findComponent(WeekNavigator).vm.$emit('currentWeek');
        await flushPromises();
        expect(mockApiFetch).toHaveBeenLastCalledWith('/api/view?tab=week');
    });
});

// ─── Year Navigation ────────────────────────────────────────────

describe('View - Year Navigation', () => {
    it('selects a year via YearNavigator', async () => {
        const wrapper = await mountView({ tab: 'year' });
        mockApiFetch.mockResolvedValueOnce(
            makeViewData({ tab: 'year', selectedYear: 10 }),
        );
        wrapper.findComponent(YearNavigator).vm.$emit('selectYear', 10);
        await flushPromises();
        expect(mockApiFetch).toHaveBeenLastCalledWith(
            expect.stringContaining('year=10'),
        );
    });

    it('selects a week from year grid', async () => {
        const wrapper = await mountView({
            tab: 'year',
            selectedYear: 25,
            birthdate: '2001-03-15',
        });
        mockApiFetch.mockResolvedValueOnce(makeViewData({ tab: 'week' }));
        wrapper.findComponent(YearGrid).vm.$emit('selectWeek', 2);
        await flushPromises();
        expect(mockApiFetch).toHaveBeenLastCalledWith(
            expect.stringContaining('tab=week'),
        );
    });

    it('does not select week from year when birthdate is null', async () => {
        const wrapper = await mountView({
            tab: 'year',
            birthdate: null,
            selectedYear: null,
            currentAge: null,
            lifeStats: null,
            weeklyActivityData: null,
            yearlyActivityData: null,
        });
        // No YearGrid rendered since no birthdate
        expect(wrapper.findComponent(YearGrid).exists()).toBe(false);
    });
});

// ─── Life Tab ───────────────────────────────────────────────────

describe('View - Life Tab', () => {
    it('selects a year from life grid', async () => {
        const wrapper = await mountView({ tab: 'life' });
        mockApiFetch.mockResolvedValueOnce(
            makeViewData({ tab: 'year', selectedYear: 5 }),
        );
        wrapper.findComponent(LifeGrid).vm.$emit('selectYear', 5);
        await flushPromises();
        expect(mockApiFetch).toHaveBeenLastCalledWith(
            expect.stringContaining('year=5'),
        );
    });

    it('does not render LifeHeader when lifeStats is null', async () => {
        const wrapper = await mountView({
            tab: 'life',
            lifeStats: null,
        });
        expect(wrapper.findComponent(LifeHeader).exists()).toBe(false);
    });

    it('does not render LifeGrid when birthdate is null', async () => {
        const wrapper = await mountView({
            tab: 'life',
            birthdate: null,
            lifeStats: null,
            weeklyActivityData: null,
            yearlyActivityData: null,
        });
        expect(wrapper.findComponent(LifeGrid).exists()).toBe(false);
    });
});

describe('View - selectWeekFromYear branches', () => {
    it('handles birthday on Monday in selectWeekFromYear', async () => {
        // 2001-03-16 + 25 = 2026-03-16 (Monday)
        const wrapper = await mountView({
            tab: 'year',
            birthdate: '2001-03-16',
            selectedYear: 25,
        });
        mockApiFetch.mockResolvedValueOnce(makeViewData({ tab: 'week' }));
        wrapper.findComponent(YearGrid).vm.$emit('selectWeek', 0);
        await flushPromises();
        expect(mockApiFetch).toHaveBeenLastCalledWith(
            expect.stringContaining('tab=week'),
        );
    });

    it('handles birthday on Wednesday in selectWeekFromYear', async () => {
        // 2001-03-18 + 25 = 2026-03-18 (Wednesday)
        const wrapper = await mountView({
            tab: 'year',
            birthdate: '2001-03-18',
            selectedYear: 25,
        });
        mockApiFetch.mockResolvedValueOnce(makeViewData({ tab: 'week' }));
        wrapper.findComponent(YearGrid).vm.$emit('selectWeek', 2);
        await flushPromises();
        expect(mockApiFetch).toHaveBeenLastCalledWith(
            expect.stringContaining('tab=week'),
        );
    });

    it('does not selectWeekFromYear when selectedYear is null', async () => {
        const wrapper = await mountView({
            tab: 'year',
            birthdate: '2001-03-15',
            selectedYear: null,
            currentAge: 25,
        });
        // YearGrid still renders (birthdate set), but selectWeekFromYear
        // should early-return because selectedYear is null
        const yearGrid = wrapper.findComponent(YearGrid);
        if (yearGrid.exists()) {
            const callsBefore = mockApiFetch.mock.calls.length;
            yearGrid.vm.$emit('selectWeek', 0);
            await flushPromises();
            // No additional API call since selectWeekFromYear returns early
            expect(mockApiFetch).toHaveBeenCalledTimes(callsBefore);
        }
    });

    it('handles setTab to year when currentAge is also null', async () => {
        const wrapper = await mountView({
            selectedYear: null,
            currentAge: null,
            birthdate: null,
            lifeStats: null,
            weeklyActivityData: null,
            yearlyActivityData: null,
        });
        mockApiFetch.mockResolvedValueOnce(
            makeViewData({
                tab: 'year',
                selectedYear: null,
                currentAge: null,
            }),
        );
        const yearBtn = wrapper
            .findAll('button')
            .find((b) => b.text() === 'Year')!;
        await yearBtn.trigger('click');
        await flushPromises();
        // selectedYear stays null since currentAge is also null
        expect(mockApiFetch).toHaveBeenCalledTimes(2);
    });
});
