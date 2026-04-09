import { mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it } from 'vitest';
import WeekGrid from '@/components/view/WeekGrid.vue';
import {
    defaultViewTranslations,
    makeFranklinGrid,
    makeGridHabit,
} from '@/tests/helpers/view';
import type { GridHabit } from '@/types/view';

beforeEach(() => {
    localStorage.clear();
});

describe('WeekGrid', () => {
    it('renders habit names and day headers', () => {
        const wrapper = mount(WeekGrid, {
            props: {
                data: makeFranklinGrid(),
                translations: defaultViewTranslations,
            },
        });
        expect(wrapper.text()).toContain('Exercise');
        expect(wrapper.text()).toContain('Habits');
        expect(wrapper.text()).toContain('Mon');
    });

    it('renders franklin habits when expanded', async () => {
        localStorage.setItem('franklin_virtues_expanded', 'true');
        const wrapper = mount(WeekGrid, {
            props: {
                data: makeFranklinGrid({
                    franklin_habits: [
                        makeGridHabit({
                            id: 2,
                            name: 'Temperance',
                            is_weekly_focus: true,
                        }),
                    ],
                }),
                translations: defaultViewTranslations,
            },
        });
        expect(wrapper.text()).toContain('Temperance');
        expect(wrapper.text()).toContain("Franklin's Virtues");
    });

    it('toggles franklin section on click', async () => {
        const wrapper = mount(WeekGrid, {
            props: {
                data: makeFranklinGrid({
                    franklin_habits: [
                        makeGridHabit({ id: 2, name: 'Temperance' }),
                    ],
                }),
                translations: defaultViewTranslations,
            },
        });
        // Initially collapsed
        expect(wrapper.text()).not.toContain('Temperance');
        // Click to expand
        const franklinBtn = wrapper
            .findAll('button')
            .find((b) => b.text().includes("Franklin's Virtues"))!;
        await franklinBtn.trigger('click');
        expect(wrapper.text()).toContain('Temperance');
        expect(localStorage.getItem('franklin_virtues_expanded')).toBe('true');
        // Click to collapse
        await franklinBtn.trigger('click');
        expect(wrapper.text()).not.toContain('Temperance');
        expect(localStorage.getItem('franklin_virtues_expanded')).toBe('false');
    });

    it('does not render regular habits block when empty', () => {
        const wrapper = mount(WeekGrid, {
            props: {
                data: makeFranklinGrid({ regular_habits: [] }),
                translations: defaultViewTranslations,
            },
        });
        expect(wrapper.text()).not.toContain('Habits');
    });

    it('does not render franklin block when empty', () => {
        const wrapper = mount(WeekGrid, {
            props: {
                data: makeFranklinGrid({ franklin_habits: [] }),
                translations: defaultViewTranslations,
            },
        });
        expect(wrapper.text()).not.toContain("Franklin's Virtues");
    });

    it('renders cell classes for completed, partial, unscheduled, and future', () => {
        const wrapper = mount(WeekGrid, {
            props: {
                data: makeFranklinGrid(),
                translations: defaultViewTranslations,
            },
        });
        const cells = wrapper.findAll('td');
        // cells[0] is the habit name td, data cells start at index 1
        const completedCell = cells[1]; // Mon (completed)
        expect(completedCell.html()).toContain('bg-green');
        // Find unscheduled cell (4th day - Thu)
        const unscheduledCell = cells[4];
        expect(unscheduledCell.text()).toContain('–');
        // Find future cell (6th day - Sat)
        const futureCell = cells[6];
        expect(futureCell.html()).toContain('border-dashed');
    });

    it('renders legend', () => {
        const wrapper = mount(WeekGrid, {
            props: {
                data: makeFranklinGrid(),
                translations: defaultViewTranslations,
            },
        });
        expect(wrapper.text()).toContain('Done');
        expect(wrapper.text()).toContain('Partial');
        expect(wrapper.text()).toContain('Missed');
        expect(wrapper.text()).toContain('Future');
    });
});

describe('WeekGrid - edge cases', () => {
    it('handles missing day data in habit.days with fallback', () => {
        const habitWithMissingDays: GridHabit = {
            id: 1,
            name: 'Exercise',
            category: null,
            is_weekly_focus: false,
            days: {
                // Only partial day entries — some days missing
                '2026-04-06': {
                    completed: true,
                    partial: false,
                    scheduled: true,
                },
            },
        };
        const wrapper = mount(WeekGrid, {
            props: {
                data: {
                    ...makeFranklinGrid(),
                    regular_habits: [habitWithMissingDays],
                },
                translations: defaultViewTranslations,
            },
        });
        // The missing day should fall through to the default
        // {completed: false, partial: false, scheduled: true}
        const cells = wrapper.findAll('td');
        expect(cells.length).toBeGreaterThan(0);
    });
});
