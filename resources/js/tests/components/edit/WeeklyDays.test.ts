import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import WeeklyDays from '@/components/edit/WeeklyDays.vue';
import { makeHabitTranslations } from '@/tests/helpers/edit';

const translations = makeHabitTranslations();

describe('WeeklyDays', () => {
    it('renders seven day buttons', () => {
        const wrapper = mount(WeeklyDays, {
            props: { modelValue: [], translations },
        });
        const buttons = wrapper.findAll('button');
        expect(buttons).toHaveLength(7);
        expect(wrapper.text()).toContain('Mo');
        expect(wrapper.text()).toContain('Su');
    });

    it('highlights selected days', () => {
        const wrapper = mount(WeeklyDays, {
            props: { modelValue: [0, 2], translations },
        });
        const buttons = wrapper.findAll('button');
        expect(buttons[0].classes()).toContain('bg-green-500');
        expect(buttons[1].classes()).not.toContain('bg-green-500');
        expect(buttons[2].classes()).toContain('bg-green-500');
    });

    it('emits toggled day when clicking unselected day', async () => {
        const wrapper = mount(WeeklyDays, {
            props: { modelValue: [0], translations },
        });
        await wrapper.findAll('button')[2].trigger('click');
        expect(wrapper.emitted('update:modelValue')?.[0]).toEqual([[0, 2]]);
    });

    it('emits with day removed when clicking selected day', async () => {
        const wrapper = mount(WeeklyDays, {
            props: { modelValue: [0, 2], translations },
        });
        await wrapper.findAll('button')[0].trigger('click');
        expect(wrapper.emitted('update:modelValue')?.[0]).toEqual([[2]]);
    });

    it('shows help text', () => {
        const wrapper = mount(WeeklyDays, {
            props: { modelValue: [], translations },
        });
        expect(wrapper.text()).toContain('Select days');
    });
});
