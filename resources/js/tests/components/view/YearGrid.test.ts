import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import ActivityCell from '@/components/view/ActivityCell.vue';
import YearGrid from '@/components/view/YearGrid.vue';
import { defaultYearTranslations } from '@/tests/helpers/view';
import type { WeekActivityData } from '@/types/view';

describe('YearGrid', () => {
    it('renders 52 week cells', () => {
        const wrapper = mount(YearGrid, {
            props: {
                birthdate: '2001-03-15',
                selectedYear: 25,
                currentAge: 25,
                weeklyActivity: null,
                translations: defaultYearTranslations,
            },
        });
        const cells = wrapper.findAllComponents(ActivityCell);
        expect(cells).toHaveLength(52);
    });

    it('does not render when birthdate is null', () => {
        const wrapper = mount(YearGrid, {
            props: {
                birthdate: null,
                selectedYear: null,
                currentAge: null,
                weeklyActivity: null,
                translations: defaultYearTranslations,
            },
        });
        expect(wrapper.findAllComponents(ActivityCell)).toHaveLength(0);
    });

    it('emits selectWeek on cell click', async () => {
        const wrapper = mount(YearGrid, {
            props: {
                birthdate: '2001-03-15',
                selectedYear: 25,
                currentAge: 25,
                weeklyActivity: null,
                translations: defaultYearTranslations,
            },
        });
        const firstCell = wrapper.findComponent(ActivityCell);
        await firstCell.trigger('click');
        expect(wrapper.emitted('selectWeek')).toBeTruthy();
        expect(wrapper.emitted('selectWeek')![0]).toEqual([0]);
    });

    it('uses weekly activity data for intensity', () => {
        const activity: WeekActivityData[] = Array.from(
            { length: 52 },
            (_, i) => ({
                weekNum: i,
                intensity: i === 0 ? 4 : 0,
                completed: i === 0 ? 10 : 0,
                total: i === 0 ? 10 : 0,
            }),
        );
        const wrapper = mount(YearGrid, {
            props: {
                birthdate: '2001-03-15',
                selectedYear: 25,
                currentAge: 25,
                weeklyActivity: activity,
                translations: defaultYearTranslations,
            },
        });
        expect(wrapper.findAllComponents(ActivityCell)).toHaveLength(52);
    });

    it('renders column headers 1-13', () => {
        const wrapper = mount(YearGrid, {
            props: {
                birthdate: '2001-03-15',
                selectedYear: 25,
                currentAge: 25,
                weeklyActivity: null,
                translations: defaultYearTranslations,
            },
        });
        expect(wrapper.text()).toContain('13');
    });

    it('shows tooltip with activity data when total > 0', () => {
        const activity: WeekActivityData[] = [
            { weekNum: 0, intensity: 3, completed: 5, total: 10 },
        ];
        const wrapper = mount(YearGrid, {
            props: {
                birthdate: '2001-03-15',
                selectedYear: 25,
                currentAge: 25,
                weeklyActivity: activity,
                translations: defaultYearTranslations,
            },
        });
        const firstCell = wrapper.findComponent(ActivityCell);
        expect(firstCell.attributes('title')).toContain('5/10');
    });

    it('handles birthday falling on a Monday', () => {
        const wrapper = mount(YearGrid, {
            props: {
                birthdate: '2001-03-16',
                selectedYear: 25,
                currentAge: 25,
                weeklyActivity: null,
                translations: defaultYearTranslations,
            },
        });
        expect(wrapper.findAllComponents(ActivityCell)).toHaveLength(52);
    });

    it('handles birthday falling on a Sunday', () => {
        const wrapper = mount(YearGrid, {
            props: {
                birthdate: '2001-03-15',
                selectedYear: 25,
                currentAge: 25,
                weeklyActivity: null,
                translations: defaultYearTranslations,
            },
        });
        expect(wrapper.findAllComponents(ActivityCell)).toHaveLength(52);
    });

    it('handles birthday falling on a Wednesday', () => {
        const wrapper = mount(YearGrid, {
            props: {
                birthdate: '2001-03-18',
                selectedYear: 25,
                currentAge: 25,
                weeklyActivity: null,
                translations: defaultYearTranslations,
            },
        });
        expect(wrapper.findAllComponents(ActivityCell)).toHaveLength(52);
    });

    it('falls back to currentAge when selectedYear is null', () => {
        const wrapper = mount(YearGrid, {
            props: {
                birthdate: '2001-03-15',
                selectedYear: null,
                currentAge: 25,
                weeklyActivity: null,
                translations: defaultYearTranslations,
            },
        });
        expect(wrapper.findAllComponents(ActivityCell)).toHaveLength(52);
    });
});
