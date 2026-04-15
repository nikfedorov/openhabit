import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import WeekNavigator from '@/components/view/WeekNavigator.vue';
import { defaultWeekTranslations } from '@/tests/helpers/view';

const defaultProps = {
    weekStart: '2026-04-06',
    weekStartFormatted: 'Apr 6',
    weekEndFormatted: 'Apr 12',
    weekEndFormattedFull: 'Apr 12, 2026',
    weekYear: '2026',
    isCurrentWeek: true,
    translations: defaultWeekTranslations,
};

describe('WeekNavigator', () => {
    it('shows "This Week" when isCurrentWeek is true', () => {
        const wrapper = mount(WeekNavigator, { props: defaultProps });
        expect(wrapper.text()).toContain('This Week');
        expect(wrapper.text()).toContain('Apr 6');
        expect(wrapper.text()).toContain('Apr 12, 2026');
    });

    it('shows date range when not current week', () => {
        const wrapper = mount(WeekNavigator, {
            props: {
                ...defaultProps,
                weekStart: '2026-03-30',
                weekStartFormatted: 'Mar 30',
                weekEndFormatted: 'Apr 5',
                weekEndFormattedFull: 'Apr 5, 2026',
                isCurrentWeek: false,
            },
        });
        expect(wrapper.text()).toContain('Mar 30');
        expect(wrapper.text()).toContain('Apr 5');
        expect(wrapper.text()).toContain('2026');
        expect(wrapper.text()).toContain('Current week');
    });

    it('hides current week button when on current week', () => {
        const wrapper = mount(WeekNavigator, { props: defaultProps });
        expect(wrapper.text()).not.toContain('Current week');
    });

    it('emits selectWeek with previous week date on left button click', async () => {
        const wrapper = mount(WeekNavigator, { props: defaultProps });
        await wrapper.findAll('button')[0].trigger('click');
        expect(wrapper.emitted('selectWeek')).toBeTruthy();
        expect(wrapper.emitted('selectWeek')![0]).toEqual(['2026-03-30']);
    });

    it('emits selectWeek with next week date on right button click', async () => {
        const wrapper = mount(WeekNavigator, { props: defaultProps });
        await wrapper.findAll('button')[1].trigger('click');
        expect(wrapper.emitted('selectWeek')).toBeTruthy();
        expect(wrapper.emitted('selectWeek')![0]).toEqual(['2026-04-13']);
    });

    it('emits selectWeek with undefined when current week button clicked', async () => {
        const wrapper = mount(WeekNavigator, {
            props: { ...defaultProps, isCurrentWeek: false },
        });
        const currentBtn = wrapper
            .findAll('button')
            .find((b) => b.text().includes('Current week'))!;
        await currentBtn.trigger('click');
        expect(wrapper.emitted('selectWeek')).toBeTruthy();
        expect(wrapper.emitted('selectWeek')![0]).toEqual([undefined]);
    });
});
