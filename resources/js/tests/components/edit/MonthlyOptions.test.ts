import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import MonthlyOptions from '@/components/edit/MonthlyOptions.vue';
import { makeHabitTranslations } from '@/tests/helpers/edit';

const translations = makeHabitTranslations();

function mountMonthly(
    overrides: {
        mode?: 'day' | 'position';
        days?: number[];
        position?: number;
        weekday?: number;
    } = {},
) {
    return mount(MonthlyOptions, {
        props: {
            mode: overrides.mode ?? 'day',
            days: overrides.days ?? [],
            position: overrides.position ?? 1,
            weekday: overrides.weekday ?? 0,
            translations,
        },
    });
}

describe('MonthlyOptions', () => {
    it('renders mode toggle buttons', () => {
        const wrapper = mountMonthly();
        expect(wrapper.text()).toContain('Specific Days');
        expect(wrapper.text()).toContain('Positional');
    });

    it('shows day grid in day mode', () => {
        const wrapper = mountMonthly({ mode: 'day' });
        const dayButtons = wrapper
            .findAll('button')
            .filter((b) => /^\d+$/.test(b.text()));
        expect(dayButtons.length).toBe(31);
    });

    it('highlights selected days in day grid', () => {
        const wrapper = mountMonthly({ mode: 'day', days: [1, 15] });
        const dayButtons = wrapper
            .findAll('button')
            .filter((b) => /^\d+$/.test(b.text()));
        expect(dayButtons[0].classes()).toContain('bg-green-500');
        expect(dayButtons[14].classes()).toContain('bg-green-500');
        expect(dayButtons[1].classes()).not.toContain('bg-green-500');
    });

    it('emits toggled day on click', async () => {
        const wrapper = mountMonthly({ mode: 'day', days: [1] });
        const dayButtons = wrapper
            .findAll('button')
            .filter((b) => b.text() === '15');
        await dayButtons[0].trigger('click');
        expect(wrapper.emitted('update:days')?.[0]).toEqual([[1, 15]]);
    });

    it('emits day removed on click', async () => {
        const wrapper = mountMonthly({ mode: 'day', days: [1, 15] });
        const dayButtons = wrapper
            .findAll('button')
            .filter((b) => b.text() === '1');
        await dayButtons[0].trigger('click');
        expect(wrapper.emitted('update:days')?.[0]).toEqual([[15]]);
    });

    it('shows position and weekday selectors in position mode', () => {
        const wrapper = mountMonthly({ mode: 'position' });
        expect(wrapper.text()).toContain('1st');
        expect(wrapper.text()).toContain('Last');
        expect(wrapper.text()).toContain('Mo');
        expect(wrapper.text()).toContain('Su');
    });

    it('highlights selected position', () => {
        const wrapper = mountMonthly({ mode: 'position', position: 2 });
        const posButtons = wrapper
            .findAll('button')
            .filter((b) => b.text() === '2nd');
        expect(posButtons[0].classes()).toContain('bg-green-500');
    });

    it('emits position update', async () => {
        const wrapper = mountMonthly({ mode: 'position', position: 1 });
        const lastBtn = wrapper
            .findAll('button')
            .find((b) => b.text() === 'Last')!;
        await lastBtn.trigger('click');
        expect(wrapper.emitted('update:position')?.[0]).toEqual([-1]);
    });

    it('emits weekday update', async () => {
        const wrapper = mountMonthly({
            mode: 'position',
            weekday: 0,
        });
        const frBtn = wrapper.findAll('button').find((b) => b.text() === 'Fr')!;
        await frBtn.trigger('click');
        expect(wrapper.emitted('update:weekday')?.[0]).toEqual([4]);
    });

    it('emits mode update when switching', async () => {
        const wrapper = mountMonthly({ mode: 'day' });
        const posBtn = wrapper
            .findAll('button')
            .find((b) => b.text() === 'Positional')!;
        await posBtn.trigger('click');
        expect(wrapper.emitted('update:mode')?.[0]).toEqual(['position']);
    });

    it('emits mode update when switching back to day', async () => {
        const wrapper = mountMonthly({ mode: 'position' });
        const dayBtn = wrapper
            .findAll('button')
            .find((b) => b.text() === 'Specific Days')!;
        await dayBtn.trigger('click');
        expect(wrapper.emitted('update:mode')?.[0]).toEqual(['day']);
    });

    it('highlights selected weekday', () => {
        const wrapper = mountMonthly({ mode: 'position', weekday: 4 });
        const frBtn = wrapper.findAll('button').find((b) => b.text() === 'Fr')!;
        expect(frBtn.classes()).toContain('bg-green-500');
    });
});
