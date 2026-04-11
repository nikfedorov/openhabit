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
    defaultWeekTranslations,
    defaultYearTranslations,
    defaultLifeTranslations,
    makeDays,
    makeGridHabit,
} from '@/tests/helpers/view';
import type {
    LifeStats,
    LifeTranslations,
    LifeViewData,
    WeekActivityData,
    WeekTranslations,
    WeekViewData,
    YearActivityData,
    YearTranslations,
    YearViewData,
} from '@/types/view';

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
    localStorage.clear();
});

// ─── Per-tab override types ──────────────────────────────────────

type WeekOverrides = {
    weekStart?: string;
    weekEnd?: string;
    weekStartFormatted?: string;
    weekEndFormatted?: string;
    weekEndFormattedFull?: string;
    weekYear?: string;
    isCurrentWeek?: boolean;
    translations?: WeekTranslations;
};

type YearOverrides = {
    selectedYear?: number | null;
    birthdate?: string | null;
    currentAge?: number | null;
    weeklyActivityData?: WeekActivityData[] | null;
    translations?: YearTranslations;
};

type LifeOverrides = {
    birthdate?: string | null;
    currentAge?: number | null;
    lifeStats?: LifeStats | null;
    yearlyActivityData?: YearActivityData[] | null;
    translations?: LifeTranslations;
};

// ─── Response builders ──────────────────────────────────────────

function makeCommonResponse() {
    return {
        navigationTranslations: defaultNavigationTranslations,
        settings: { locale: 'en', theme: 'system' as const },
    };
}

function makeWeekData(overrides: WeekOverrides = {}): WeekViewData {
    return {
        start: overrides.weekStart ?? '2026-04-06',
        end: overrides.weekEnd ?? '2026-04-12',
        startFormatted: overrides.weekStartFormatted ?? 'Apr 6',
        endFormatted: overrides.weekEndFormatted ?? 'Apr 12',
        endFormattedFull: overrides.weekEndFormattedFull ?? 'Apr 12, 2026',
        year: overrides.weekYear ?? '2026',
        isCurrent: overrides.isCurrentWeek ?? true,
    };
}

function makeYearData(overrides: YearOverrides = {}): YearViewData {
    return {
        selected:
            overrides.selectedYear !== undefined ? overrides.selectedYear : 25,
        birthdate:
            overrides.birthdate !== undefined
                ? overrides.birthdate
                : '2001-03-15',
        currentAge:
            overrides.currentAge !== undefined ? overrides.currentAge : 25,
    };
}

function makeLifeData(overrides: LifeOverrides = {}): LifeViewData {
    return {
        birthdate:
            overrides.birthdate !== undefined
                ? overrides.birthdate
                : '2001-03-15',
        currentAge:
            overrides.currentAge !== undefined ? overrides.currentAge : 25,
        weeksLived:
            overrides.lifeStats !== undefined
                ? (overrides.lifeStats?.weeksLived ?? null)
                : defaultLifeStats.weeksLived,
        yearsRemaining:
            overrides.lifeStats !== undefined
                ? (overrides.lifeStats?.yearsRemaining ?? null)
                : defaultLifeStats.yearsRemaining,
    };
}

function makeWeekResponse(overrides: WeekOverrides = {}) {
    return {
        data: makeWeekData(overrides),
        days: makeDays(),
        habits: [makeGridHabit()],
        translations: overrides.translations ?? defaultWeekTranslations,
        ...makeCommonResponse(),
    };
}

function makeYearResponse(overrides: YearOverrides = {}) {
    return {
        data: makeYearData(overrides),
        translations: overrides.translations ?? defaultYearTranslations,
        activityData:
            overrides.weeklyActivityData !== undefined
                ? overrides.weeklyActivityData
                : [],
        ...makeCommonResponse(),
    };
}

function makeLifeResponse(overrides: LifeOverrides = {}) {
    return {
        data: makeLifeData(overrides),
        translations: overrides.translations ?? defaultLifeTranslations,
        activityData:
            overrides.yearlyActivityData !== undefined
                ? overrides.yearlyActivityData
                : [],
        ...makeCommonResponse(),
    };
}

/** Mount View on the default week tab. */
async function mountView(weekOverrides: WeekOverrides = {}) {
    mockApiFetch.mockResolvedValueOnce(makeWeekResponse(weekOverrides));
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
        mockApiFetch.mockResolvedValueOnce(makeYearResponse());
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
        mockApiFetch.mockResolvedValueOnce(makeLifeResponse());
        const lifeBtn = wrapper
            .findAll('button')
            .find((b) => b.text() === 'Life')!;
        await lifeBtn.trigger('click');
        await flushPromises();
        expect(wrapper.findComponent(LifeHeader).exists()).toBe(true);
        expect(wrapper.findComponent(LifeGrid).exists()).toBe(true);
    });

    it('switches to year tab without year param when no previous year data', async () => {
        const wrapper = await mountView();
        mockApiFetch.mockResolvedValueOnce(
            makeYearResponse({ selectedYear: 25 }),
        );
        const yearBtn = wrapper
            .findAll('button')
            .find((b) => b.text() === 'Year')!;
        await yearBtn.trigger('click');
        await flushPromises();
        expect(mockApiFetch).toHaveBeenLastCalledWith('/api/view/year');
    });

    it('reloads data when switching to week tab', async () => {
        mockRouteQuery.value = { tab: 'year' };
        mockApiFetch.mockResolvedValueOnce(makeYearResponse());
        const wrapper = mount(View);
        await flushPromises();

        mockApiFetch.mockResolvedValueOnce(makeWeekResponse());
        const weekBtn = wrapper
            .findAll('button')
            .find((b) => b.text() === 'Week')!;
        await weekBtn.trigger('click');
        await flushPromises();
        expect(mockApiFetch).toHaveBeenCalledTimes(2);
        expect(mockApiFetch).toHaveBeenLastCalledWith('/api/view/week');
    });

    it('reloads current week without week param when isCurrent is true', async () => {
        const wrapper = await mountView({ isCurrentWeek: true });
        mockApiFetch.mockResolvedValueOnce(makeYearResponse());
        const yearBtn = wrapper
            .findAll('button')
            .find((b) => b.text() === 'Year')!;
        await yearBtn.trigger('click');
        await flushPromises();

        mockApiFetch.mockResolvedValueOnce(makeWeekResponse());
        const weekBtn = wrapper
            .findAll('button')
            .find((b) => b.text() === 'Week')!;
        await weekBtn.trigger('click');
        await flushPromises();
        expect(mockApiFetch).toHaveBeenLastCalledWith('/api/view/week');
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
            makeWeekResponse({
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
        mockApiFetch.mockResolvedValueOnce(makeWeekResponse());
        wrapper.findComponent(WeekNavigator).vm.$emit('nextWeek');
        await flushPromises();
        expect(mockApiFetch).toHaveBeenLastCalledWith(
            expect.stringContaining('week=2026-04-13'),
        );
    });

    it('navigates to current week', async () => {
        const wrapper = await mountView({ isCurrentWeek: false });
        mockApiFetch.mockResolvedValueOnce(makeWeekResponse());
        wrapper.findComponent(WeekNavigator).vm.$emit('currentWeek');
        await flushPromises();
        expect(mockApiFetch).toHaveBeenLastCalledWith('/api/view/week');
    });
});

// ─── Year Navigation ────────────────────────────────────────────

describe('View - Year Navigation', () => {
    it('selects a year via YearNavigator', async () => {
        mockRouteQuery.value = { tab: 'year' };
        mockApiFetch.mockResolvedValueOnce(makeYearResponse());
        const wrapper = mount(View);
        await flushPromises();

        mockApiFetch.mockResolvedValueOnce(
            makeYearResponse({ selectedYear: 10 }),
        );
        wrapper.findComponent(YearNavigator).vm.$emit('selectYear', 10);
        await flushPromises();
        expect(mockApiFetch).toHaveBeenLastCalledWith(
            expect.stringContaining('year=10'),
        );
    });

    it('selects a higher year via YearNavigator (forward direction)', async () => {
        mockRouteQuery.value = { tab: 'year' };
        mockApiFetch.mockResolvedValueOnce(
            makeYearResponse({ selectedYear: 10 }),
        );
        const wrapper = mount(View);
        await flushPromises();

        mockApiFetch.mockResolvedValueOnce(
            makeYearResponse({ selectedYear: 20 }),
        );
        wrapper.findComponent(YearNavigator).vm.$emit('selectYear', 20);
        await flushPromises();
        expect(mockApiFetch).toHaveBeenLastCalledWith(
            expect.stringContaining('year=20'),
        );
    });

    it('selects a week from year grid', async () => {
        mockRouteQuery.value = { tab: 'year' };
        mockApiFetch.mockResolvedValueOnce(
            makeYearResponse({ selectedYear: 25, birthdate: '2001-03-15' }),
        );
        const wrapper = mount(View);
        await flushPromises();

        mockApiFetch.mockResolvedValueOnce(makeWeekResponse());
        wrapper.findComponent(YearGrid).vm.$emit('selectWeek', 2);
        await flushPromises();
        expect(mockApiFetch).toHaveBeenLastCalledWith(
            expect.stringContaining('/api/view/week'),
        );
    });

    it('does not select week from year when birthdate is null', async () => {
        mockRouteQuery.value = { tab: 'year' };
        mockApiFetch.mockResolvedValueOnce(
            makeYearResponse({
                birthdate: null,
                selectedYear: null,
                currentAge: null,
                weeklyActivityData: null,
            }),
        );
        const wrapper = mount(View);
        await flushPromises();
        // No YearGrid rendered since no birthdate
        expect(wrapper.findComponent(YearGrid).exists()).toBe(false);
    });

    it('shows empty state when birthdate is null on year tab', async () => {
        mockRouteQuery.value = { tab: 'year' };
        mockApiFetch.mockResolvedValueOnce(
            makeYearResponse({
                birthdate: null,
                selectedYear: null,
                currentAge: null,
                weeklyActivityData: null,
            }),
        );
        const wrapper = mount(View);
        await flushPromises();
        expect(wrapper.text()).toContain('Set your birthdate in settings');
        expect(wrapper.text()).toContain('to see your year visualization');
    });
});

// ─── Life Tab ───────────────────────────────────────────────────

describe('View - Life Tab', () => {
    it('selects a year from life grid', async () => {
        mockRouteQuery.value = { tab: 'life' };
        mockApiFetch.mockResolvedValueOnce(makeLifeResponse());
        const wrapper = mount(View);
        await flushPromises();

        mockApiFetch.mockResolvedValueOnce(
            makeYearResponse({ selectedYear: 5 }),
        );
        wrapper.findComponent(LifeGrid).vm.$emit('selectYear', 5);
        await flushPromises();
        expect(mockApiFetch).toHaveBeenLastCalledWith(
            expect.stringContaining('year=5'),
        );
    });

    it('does not render LifeHeader when lifeStats is null', async () => {
        mockRouteQuery.value = { tab: 'life' };
        mockApiFetch.mockResolvedValueOnce(
            makeLifeResponse({ lifeStats: null }),
        );
        const wrapper = mount(View);
        await flushPromises();
        expect(wrapper.findComponent(LifeHeader).exists()).toBe(false);
    });

    it('does not render LifeGrid when birthdate is null', async () => {
        mockRouteQuery.value = { tab: 'life' };
        mockApiFetch.mockResolvedValueOnce(
            makeLifeResponse({
                birthdate: null,
                lifeStats: null,
                yearlyActivityData: null,
            }),
        );
        const wrapper = mount(View);
        await flushPromises();
        expect(wrapper.findComponent(LifeGrid).exists()).toBe(false);
    });

    it('shows empty state when birthdate is null on life tab', async () => {
        mockRouteQuery.value = { tab: 'life' };
        mockApiFetch.mockResolvedValueOnce(
            makeLifeResponse({
                birthdate: null,
                lifeStats: null,
                yearlyActivityData: null,
            }),
        );
        const wrapper = mount(View);
        await flushPromises();
        expect(wrapper.text()).toContain('Set your birthdate in settings');
        expect(wrapper.text()).toContain('to see your life visualization');
    });
});

describe('View - selectWeekFromYear branches', () => {
    it('handles birthday on Monday in selectWeekFromYear', async () => {
        // 2001-03-16 + 25 = 2026-03-16 (Monday)
        mockRouteQuery.value = { tab: 'year' };
        mockApiFetch.mockResolvedValueOnce(
            makeYearResponse({ birthdate: '2001-03-16', selectedYear: 25 }),
        );
        const wrapper = mount(View);
        await flushPromises();

        mockApiFetch.mockResolvedValueOnce(makeWeekResponse());
        wrapper.findComponent(YearGrid).vm.$emit('selectWeek', 0);
        await flushPromises();
        expect(mockApiFetch).toHaveBeenLastCalledWith(
            expect.stringContaining('/api/view/week'),
        );
    });

    it('handles birthday on Wednesday in selectWeekFromYear', async () => {
        // 2001-03-18 + 25 = 2026-03-18 (Wednesday)
        mockRouteQuery.value = { tab: 'year' };
        mockApiFetch.mockResolvedValueOnce(
            makeYearResponse({ birthdate: '2001-03-18', selectedYear: 25 }),
        );
        const wrapper = mount(View);
        await flushPromises();

        mockApiFetch.mockResolvedValueOnce(makeWeekResponse());
        wrapper.findComponent(YearGrid).vm.$emit('selectWeek', 2);
        await flushPromises();
        expect(mockApiFetch).toHaveBeenLastCalledWith(
            expect.stringContaining('/api/view/week'),
        );
    });

    it('does not selectWeekFromYear when selectedYear is null', async () => {
        mockRouteQuery.value = { tab: 'year' };
        mockApiFetch.mockResolvedValueOnce(
            makeYearResponse({
                birthdate: '2001-03-15',
                selectedYear: null,
                currentAge: 25,
            }),
        );
        const wrapper = mount(View);
        await flushPromises();

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

    it('sends no year param when yearData is null on setTab to year', async () => {
        const wrapper = await mountView();
        mockApiFetch.mockResolvedValueOnce(
            makeYearResponse({ selectedYear: null, currentAge: null }),
        );
        const yearBtn = wrapper
            .findAll('button')
            .find((b) => b.text() === 'Year')!;
        await yearBtn.trigger('click');
        await flushPromises();
        expect(mockApiFetch).toHaveBeenLastCalledWith('/api/view/year');
    });
});

// ─── URL Sync ───────────────────────────────────────────────────

describe('View - URL Sync', () => {
    it('includes tab=week for default state', async () => {
        await mountView();
        expect(mockReplace).toHaveBeenCalledWith({
            query: { tab: 'week' },
        });
    });

    it('adds tab query when not on week tab', async () => {
        mockRouteQuery.value = { tab: 'life' };
        mockApiFetch.mockResolvedValueOnce(makeLifeResponse());
        mount(View);
        await flushPromises();
        expect(mockReplace).toHaveBeenCalledWith({
            query: { tab: 'life' },
        });
    });

    it('adds week query when not on current week', async () => {
        await mountView({ isCurrentWeek: false, weekStart: '2026-03-30' });
        expect(mockReplace).toHaveBeenCalledWith({
            query: { tab: 'week', week: '2026-03-30' },
        });
    });

    it('adds year query for year tab', async () => {
        mockRouteQuery.value = { tab: 'year' };
        mockApiFetch.mockResolvedValueOnce(
            makeYearResponse({ selectedYear: 25 }),
        );
        mount(View);
        await flushPromises();
        expect(mockReplace).toHaveBeenCalledWith({
            query: { tab: 'year', year: '25' },
        });
    });

    it('loads initial params from URL query', async () => {
        mockRouteQuery.value = { tab: 'year', year: '10' };
        mockApiFetch.mockResolvedValueOnce(
            makeYearResponse({ selectedYear: 10 }),
        );
        mount(View);
        await flushPromises();
        expect(mockApiFetch).toHaveBeenCalledWith('/api/view/year?year=10');
    });

    it('loads week from URL query', async () => {
        mockRouteQuery.value = { week: '2026-03-30' };
        await mountView({ isCurrentWeek: false, weekStart: '2026-03-30' });
        expect(mockApiFetch).toHaveBeenCalledWith(
            expect.stringContaining('week=2026-03-30'),
        );
    });

    it('updates URL when switching tabs', async () => {
        const wrapper = await mountView();
        mockReplace.mockReset();
        mockApiFetch.mockResolvedValueOnce(
            makeYearResponse({ selectedYear: 25 }),
        );
        const yearBtn = wrapper
            .findAll('button')
            .find((b) => b.text() === 'Year')!;
        await yearBtn.trigger('click');
        await flushPromises();
        expect(mockReplace).toHaveBeenCalledWith({
            query: { tab: 'year', year: '25' },
        });
    });

    it('clears extra params when returning to default state', async () => {
        const wrapper = await mountView({ isCurrentWeek: false });
        mockReplace.mockReset();
        mockApiFetch.mockResolvedValueOnce(makeWeekResponse());
        wrapper.findComponent(WeekNavigator).vm.$emit('currentWeek');
        await flushPromises();
        expect(mockReplace).toHaveBeenCalledWith({
            query: { tab: 'week' },
        });
    });
});
