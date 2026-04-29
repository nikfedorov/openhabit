import { mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it } from 'vitest';
import WeekGrid from '@/components/view/WeekGrid.vue';
import {
    defaultWeekTranslations,
    makeDays,
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
                days: makeDays(),
                habits: [makeGridHabit()],
                translations: defaultWeekTranslations,
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
                days: makeDays(),
                habits: [
                    makeGridHabit(),
                    makeGridHabit({
                        id: 2,
                        name: 'Temperance',
                        is_weekly_focus: true,
                        is_franklin_virtue: true,
                    }),
                ],
                translations: defaultWeekTranslations,
            },
        });
        expect(wrapper.text()).toContain('Temperance');
        expect(wrapper.text()).toContain("Franklin's Virtues");
    });

    it('toggles franklin section on click', async () => {
        const wrapper = mount(WeekGrid, {
            props: {
                days: makeDays(),
                habits: [
                    makeGridHabit(),
                    makeGridHabit({
                        id: 2,
                        name: 'Temperance',
                        is_franklin_virtue: true,
                    }),
                ],
                translations: defaultWeekTranslations,
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
                days: makeDays(),
                habits: [makeGridHabit({ is_franklin_virtue: true })],
                translations: defaultWeekTranslations,
            },
        });
        expect(wrapper.text()).not.toContain('Habits');
    });

    it('does not render franklin block when empty', () => {
        const wrapper = mount(WeekGrid, {
            props: {
                days: makeDays(),
                habits: [makeGridHabit()],
                translations: defaultWeekTranslations,
            },
        });
        expect(wrapper.text()).not.toContain("Franklin's Virtues");
    });

    it('renders cell classes for completed, partial, unscheduled, and future', () => {
        const wrapper = mount(WeekGrid, {
            props: {
                days: makeDays(),
                habits: [makeGridHabit()],
                translations: defaultWeekTranslations,
            },
        });
        // Completed day renders green
        expect(wrapper.html()).toContain('bg-green');
        // Unscheduled day renders dash
        expect(wrapper.text()).toContain('–');
        // Future day renders dashed border
        expect(wrapper.html()).toContain('border-dashed');
    });

    it('renders legend', () => {
        const wrapper = mount(WeekGrid, {
            props: {
                days: makeDays(),
                habits: [makeGridHabit()],
                translations: defaultWeekTranslations,
            },
        });
        expect(wrapper.text()).toContain('Done');
        expect(wrapper.text()).toContain('Partial');
        expect(wrapper.text()).toContain('Missed');
        expect(wrapper.text()).toContain('Future');
    });

    it('shows focus virtue when collapsed', () => {
        const wrapper = mount(WeekGrid, {
            props: {
                days: makeDays(),
                habits: [
                    makeGridHabit(),
                    makeGridHabit({
                        id: 2,
                        name: 'Temperance',
                        is_weekly_focus: true,
                        is_franklin_virtue: true,
                    }),
                    makeGridHabit({
                        id: 3,
                        name: 'Silence',
                        is_weekly_focus: false,
                        is_franklin_virtue: true,
                    }),
                ],
                translations: defaultWeekTranslations,
            },
        });
        // Focus virtue is visible even when collapsed
        expect(wrapper.text()).toContain('Temperance');
        // Non-focus habit is hidden when collapsed
        expect(wrapper.text()).not.toContain('Silence');
    });

    it('shows all franklin habits when expanded', async () => {
        localStorage.setItem('franklin_virtues_expanded', 'true');
        const wrapper = mount(WeekGrid, {
            props: {
                days: makeDays(),
                habits: [
                    makeGridHabit(),
                    makeGridHabit({
                        id: 2,
                        name: 'Temperance',
                        is_weekly_focus: true,
                        is_franklin_virtue: true,
                    }),
                    makeGridHabit({
                        id: 3,
                        name: 'Silence',
                        is_weekly_focus: false,
                        is_franklin_virtue: true,
                    }),
                ],
                translations: defaultWeekTranslations,
            },
        });
        expect(wrapper.text()).toContain('Temperance');
        expect(wrapper.text()).toContain('Silence');
    });
});

describe('WeekGrid - edge cases', () => {
    it('handles missing day data in habit.days with fallback', () => {
        const habitWithMissingDays: GridHabit = {
            id: 1,
            name: 'Exercise',
            category: null,
            is_weekly_focus: false,
            is_franklin_virtue: false,
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
                days: makeDays(),
                habits: [habitWithMissingDays],
                translations: defaultWeekTranslations,
            },
        });
        // The missing day should fall through to the default
        // {completed: false, partial: false, scheduled: true}
        expect(wrapper.text()).toContain('Exercise');
        expect(wrapper.findAll('.rounded-sm').length).toBeGreaterThan(0);
    });

    it('animation hooks set and clean inline styles', () => {
        const wrapper = mount(WeekGrid, {
            props: {
                days: makeDays(),
                habits: [
                    makeGridHabit(),
                    makeGridHabit({
                        id: 2,
                        name: 'Focus',
                        is_weekly_focus: true,
                        is_franklin_virtue: true,
                    }),
                    makeGridHabit({
                        id: 3,
                        name: 'Extra',
                        is_weekly_focus: false,
                        is_franklin_virtue: true,
                    }),
                ],
                translations: defaultWeekTranslations,
            },
        });

        const { onRowEnter, onRowAfterEnter, onRowLeave, onRowAfterLeave } = (
            wrapper.vm.$ as unknown as {
                setupState: Record<string, (el: Element) => void>;
            }
        ).setupState;

        const el = document.createElement('div');

        onRowEnter(el);
        expect(el.style.overflow).toBe('hidden');
        expect(el.style.opacity).toBe('1');

        onRowAfterEnter(el);
        expect(el.style.height).toBe('');
        expect(el.style.overflow).toBe('');
        expect(el.style.transition).toBe('');
        expect(el.style.opacity).toBe('');

        onRowLeave(el);
        expect(el.style.overflow).toBe('hidden');
        expect(el.style.opacity).toBe('0');
        expect(el.style.height).toBe('0px');

        onRowAfterLeave(el);
        expect(el.style.height).toBe('');
        expect(el.style.overflow).toBe('');
        expect(el.style.transition).toBe('');
        expect(el.style.opacity).toBe('');
    });
});
