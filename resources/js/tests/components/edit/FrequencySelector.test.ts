import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import FrequencySelector from '@/components/edit/FrequencySelector.vue';
import { makeHabitTranslations } from '@/tests/helpers/edit';

const translations = makeHabitTranslations();

function mountSelector(modelValue: 'DAILY' | 'WEEKLY' | 'MONTHLY' = 'DAILY') {
    return mount(FrequencySelector, {
        props: { modelValue, translations, 'onUpdate:modelValue': () => {} },
    });
}

describe('FrequencySelector', () => {
    it('renders three frequency options', () => {
        const wrapper = mountSelector();
        expect(wrapper.text()).toContain('Daily');
        expect(wrapper.text()).toContain('Weekly');
        expect(wrapper.text()).toContain('Monthly');
    });

    it('highlights the selected frequency', () => {
        const wrapper = mountSelector('WEEKLY');
        const buttons = wrapper.findAll('button');
        const weeklyBtn = buttons.find((b) => b.text() === 'Weekly')!;
        expect(weeklyBtn.classes()).toContain('bg-green-100');
    });

    it('emits update when clicking a frequency', async () => {
        const wrapper = mount(FrequencySelector, {
            props: { modelValue: 'DAILY', translations },
        });
        const monthlyBtn = wrapper
            .findAll('button')
            .find((b) => b.text() === 'Monthly')!;
        await monthlyBtn.trigger('click');
        expect(wrapper.emitted('update:modelValue')?.[0]).toEqual(['MONTHLY']);
    });

    it('shows frequency help text', () => {
        const wrapper = mountSelector();
        expect(wrapper.text()).toContain('How often?');
    });
});
