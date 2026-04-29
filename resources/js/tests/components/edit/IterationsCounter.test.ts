import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import IterationsCounter from '@/components/edit/IterationsCounter.vue';
import { makeHabitTranslations } from '@/tests/helpers/edit';

const translations = makeHabitTranslations();

describe('IterationsCounter', () => {
    it('renders current value', () => {
        const wrapper = mount(IterationsCounter, {
            props: { modelValue: 3, translations },
        });
        const input = wrapper.find('input');
        expect(input.element.value).toBe('3');
    });

    it('emits decremented value on minus click', async () => {
        const wrapper = mount(IterationsCounter, {
            props: { modelValue: 3, translations },
        });
        const buttons = wrapper.findAll('button');
        await buttons[0].trigger('click');
        expect(wrapper.emitted('update:modelValue')?.[0]).toEqual([2]);
    });

    it('does not decrement below 1', async () => {
        const wrapper = mount(IterationsCounter, {
            props: { modelValue: 1, translations },
        });
        await wrapper.findAll('button')[0].trigger('click');
        expect(wrapper.emitted('update:modelValue')).toBeUndefined();
    });

    it('emits incremented value on plus click', async () => {
        const wrapper = mount(IterationsCounter, {
            props: { modelValue: 3, translations },
        });
        const buttons = wrapper.findAll('button');
        await buttons[1].trigger('click');
        expect(wrapper.emitted('update:modelValue')?.[0]).toEqual([4]);
    });

    it('does not increment above 99', async () => {
        const wrapper = mount(IterationsCounter, {
            props: { modelValue: 99, translations },
        });
        await wrapper.findAll('button')[1].trigger('click');
        expect(wrapper.emitted('update:modelValue')).toBeUndefined();
    });

    it('shows singular label for 1', () => {
        const wrapper = mount(IterationsCounter, {
            props: { modelValue: 1, translations },
        });
        expect(wrapper.text()).toContain('time');
        expect(wrapper.text()).not.toContain('times');
    });

    it('shows plural label for values greater than 1', () => {
        const wrapper = mount(IterationsCounter, {
            props: { modelValue: 3, translations },
        });
        expect(wrapper.text()).toContain('times');
    });

    it('emits updated value on direct input', async () => {
        const wrapper = mount(IterationsCounter, {
            props: { modelValue: 3, translations },
        });
        const input = wrapper.find('input');
        await input.setValue(5);
        expect(wrapper.emitted('update:modelValue')?.[0]).toEqual([5]);
    });
});
