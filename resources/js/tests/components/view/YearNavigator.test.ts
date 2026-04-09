import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import YearNavigator from '@/components/view/YearNavigator.vue';
import { defaultViewTranslations } from '@/tests/helpers/view';

describe('YearNavigator', () => {
    it('shows "This Year" when on current year', () => {
        const wrapper = mount(YearNavigator, {
            props: {
                selectedYear: 25,
                currentAge: 25,
                birthdate: '2001-03-15',
                translations: defaultViewTranslations,
            },
        });
        expect(wrapper.text()).toContain('This Year');
    });

    it('shows age label when not current year', () => {
        const wrapper = mount(YearNavigator, {
            props: {
                selectedYear: 10,
                currentAge: 25,
                birthdate: '2001-03-15',
                translations: defaultViewTranslations,
            },
        });
        expect(wrapper.text()).toContain('Age 10');
        expect(wrapper.text()).toContain('Current year');
    });

    it('emits selectYear on previous button click', async () => {
        const wrapper = mount(YearNavigator, {
            props: {
                selectedYear: 10,
                currentAge: 25,
                birthdate: '2001-03-15',
                translations: defaultViewTranslations,
            },
        });
        const prevBtn = wrapper.findAll('button')[0];
        await prevBtn.trigger('click');
        expect(wrapper.emitted('selectYear')![0]).toEqual([9]);
    });

    it('emits selectYear on next button click', async () => {
        const wrapper = mount(YearNavigator, {
            props: {
                selectedYear: 10,
                currentAge: 25,
                birthdate: '2001-03-15',
                translations: defaultViewTranslations,
            },
        });
        const buttons = wrapper.findAll('button');
        const nextBtn = buttons[1];
        await nextBtn.trigger('click');
        expect(wrapper.emitted('selectYear')![0]).toEqual([11]);
    });

    it('emits selectYear with currentAge on current year button click', async () => {
        const wrapper = mount(YearNavigator, {
            props: {
                selectedYear: 10,
                currentAge: 25,
                birthdate: '2001-03-15',
                translations: defaultViewTranslations,
            },
        });
        const currentBtn = wrapper
            .findAll('button')
            .find((b) => b.text().includes('Current year'))!;
        await currentBtn.trigger('click');
        expect(wrapper.emitted('selectYear')![0]).toEqual([25]);
    });

    it('hides previous button at year 0', () => {
        const wrapper = mount(YearNavigator, {
            props: {
                selectedYear: 0,
                currentAge: 25,
                birthdate: '2001-03-15',
                translations: defaultViewTranslations,
            },
        });
        // Only next button and current year button visible
        const buttons = wrapper.findAll('button');
        expect(
            buttons.some((b) => b.attributes('title') === 'Previous year'),
        ).toBe(false);
    });

    it('hides next button at year 79', () => {
        const wrapper = mount(YearNavigator, {
            props: {
                selectedYear: 79,
                currentAge: 25,
                birthdate: '2001-03-15',
                translations: defaultViewTranslations,
            },
        });
        const buttons = wrapper.findAll('button');
        expect(buttons.some((b) => b.attributes('title') === 'Next year')).toBe(
            false,
        );
    });

    it('does not render when birthdate is null', () => {
        const wrapper = mount(YearNavigator, {
            props: {
                selectedYear: null,
                currentAge: null,
                birthdate: null,
                translations: defaultViewTranslations,
            },
        });
        expect(wrapper.text()).toBe('');
    });

    it('shows year range text', () => {
        const wrapper = mount(YearNavigator, {
            props: {
                selectedYear: 25,
                currentAge: 25,
                birthdate: '2001-03-15',
                translations: defaultViewTranslations,
            },
        });
        // Birth: 2001-03-15, year 25 → 2026-03-15 to 2027-03-14
        expect(wrapper.text()).toContain('2026');
    });
});

describe('YearNavigator - edge cases', () => {
    it('shows same year in range text when birthday is Jan 1', () => {
        // birthdate 2000-01-01, selectedYear 0: yearStart = 2000-01-01, yearEnd = 2000-12-31
        const wrapper = mount(YearNavigator, {
            props: {
                selectedYear: 0,
                currentAge: 25,
                birthdate: '2000-01-01',
                translations: defaultViewTranslations,
            },
        });
        // Both startYear and endYear should be 2000
        expect(wrapper.text()).toContain('2000');
        expect(wrapper.text()).not.toContain('–');
    });

    it('uses null selectedYear with fallback to currentAge', () => {
        const wrapper = mount(YearNavigator, {
            props: {
                selectedYear: null,
                currentAge: 25,
                birthdate: '2001-03-15',
                translations: defaultViewTranslations,
            },
        });
        // displayYear = currentAge (25), isCurrentYear = true
        expect(wrapper.text()).toContain('This Year');
    });

    it('uses null selectedYear and null currentAge as fallback 0', () => {
        const wrapper = mount(YearNavigator, {
            props: {
                selectedYear: null,
                currentAge: null,
                birthdate: '2001-03-15',
                translations: defaultViewTranslations,
            },
        });
        // displayYear = 0, isCurrentYear = (0 === null) = false
        expect(wrapper.text()).toContain('Age 0');
    });

    it('emits selectYear with 0 when currentAge is null and current year clicked', async () => {
        const wrapper = mount(YearNavigator, {
            props: {
                selectedYear: 10,
                currentAge: null,
                birthdate: '2001-03-15',
                translations: defaultViewTranslations,
            },
        });
        // isCurrentYear = (10 === null) = false, so button shows
        const currentBtn = wrapper
            .findAll('button')
            .find((b) => b.text().includes('Current year'))!;
        await currentBtn.trigger('click');
        // currentAge ?? 0 → null ?? 0 → 0
        expect(wrapper.emitted('selectYear')![0]).toEqual([0]);
    });
});
