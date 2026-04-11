import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import WeekNavigator from '@/components/view/WeekNavigator.vue';
import { defaultWeekTranslations } from '@/tests/helpers/view';

describe('WeekNavigator', () => {
    it('shows "This Week" when isCurrentWeek is true', () => {
        const wrapper = mount(WeekNavigator, {
            props: {
                weekStartFormatted: 'Apr 6',
                weekEndFormatted: 'Apr 12',
                weekEndFormattedFull: 'Apr 12, 2026',
                weekYear: '2026',
                isCurrentWeek: true,
                translations: defaultWeekTranslations,
            },
        });
        expect(wrapper.text()).toContain('This Week');
        expect(wrapper.text()).toContain('Apr 6');
        expect(wrapper.text()).toContain('Apr 12, 2026');
    });

    it('shows date range when not current week', () => {
        const wrapper = mount(WeekNavigator, {
            props: {
                weekStartFormatted: 'Mar 30',
                weekEndFormatted: 'Apr 5',
                weekEndFormattedFull: 'Apr 5, 2026',
                weekYear: '2026',
                isCurrentWeek: false,
                translations: defaultWeekTranslations,
            },
        });
        expect(wrapper.text()).toContain('Mar 30');
        expect(wrapper.text()).toContain('Apr 5');
        expect(wrapper.text()).toContain('2026');
        expect(wrapper.text()).toContain('Current week');
    });

    it('hides current week button when on current week', () => {
        const wrapper = mount(WeekNavigator, {
            props: {
                weekStartFormatted: 'Apr 6',
                weekEndFormatted: 'Apr 12',
                weekEndFormattedFull: 'Apr 12, 2026',
                weekYear: '2026',
                isCurrentWeek: true,
                translations: defaultWeekTranslations,
            },
        });
        expect(wrapper.text()).not.toContain('Current week');
    });

    it('emits previousWeek on left button click', async () => {
        const wrapper = mount(WeekNavigator, {
            props: {
                weekStartFormatted: 'Apr 6',
                weekEndFormatted: 'Apr 12',
                weekEndFormattedFull: 'Apr 12, 2026',
                weekYear: '2026',
                isCurrentWeek: true,
                translations: defaultWeekTranslations,
            },
        });
        await wrapper.findAll('button')[0].trigger('click');
        expect(wrapper.emitted('previousWeek')).toBeTruthy();
    });

    it('emits nextWeek on right button click', async () => {
        const wrapper = mount(WeekNavigator, {
            props: {
                weekStartFormatted: 'Apr 6',
                weekEndFormatted: 'Apr 12',
                weekEndFormattedFull: 'Apr 12, 2026',
                weekYear: '2026',
                isCurrentWeek: true,
                translations: defaultWeekTranslations,
            },
        });
        await wrapper.findAll('button')[1].trigger('click');
        expect(wrapper.emitted('nextWeek')).toBeTruthy();
    });

    it('emits currentWeek when current week button clicked', async () => {
        const wrapper = mount(WeekNavigator, {
            props: {
                weekStartFormatted: 'Mar 30',
                weekEndFormatted: 'Apr 5',
                weekEndFormattedFull: 'Apr 5, 2026',
                weekYear: '2026',
                isCurrentWeek: false,
                translations: defaultWeekTranslations,
            },
        });
        const currentBtn = wrapper
            .findAll('button')
            .find((b) => b.text().includes('Current week'))!;
        await currentBtn.trigger('click');
        expect(wrapper.emitted('currentWeek')).toBeTruthy();
    });
});
