import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import TimePickerInput from '@/components/TimePickerInput.vue';

describe('TimePickerInput', () => {
    it('shows default time 09:00 when modelValue is null', () => {
        const wrapper = mount(TimePickerInput, {
            props: { modelValue: null },
        });
        expect(wrapper.text()).toContain('09');
        expect(wrapper.text()).toContain('00');
    });

    it('shows the provided time', () => {
        const wrapper = mount(TimePickerInput, {
            props: { modelValue: '14:30' },
        });
        expect(wrapper.text()).toContain('14');
        expect(wrapper.text()).toContain('30');
    });

    it('snaps minutes to nearest 5 for display', () => {
        const wrapper = mount(TimePickerInput, {
            props: { modelValue: '08:07' },
        });
        // 7 → floor(7/5)*5 = 5
        expect(wrapper.text()).toContain('05');
    });

    it('toggle button opens the dropdown', async () => {
        const wrapper = mount(TimePickerInput, {
            props: { modelValue: '09:00' },
        });
        expect(wrapper.find('.overflow-y-auto').exists()).toBe(false);
        await wrapper.find('button').trigger('click');
        expect(wrapper.find('.overflow-y-auto').exists()).toBe(true);
    });

    it('toggle button closes the dropdown when already open', async () => {
        const wrapper = mount(TimePickerInput, {
            props: { modelValue: '09:00' },
        });
        await wrapper.find('button').trigger('click');
        expect(wrapper.find('.overflow-y-auto').exists()).toBe(true);
        await wrapper.find('button').trigger('click');
        expect(wrapper.find('.overflow-y-auto').exists()).toBe(false);
    });

    it('dropdown aligns left by default', async () => {
        const wrapper = mount(TimePickerInput, {
            props: { modelValue: '09:00' },
        });
        await wrapper.find('button').trigger('click');
        const dropdown = wrapper.find('[class*="absolute bottom-full"]');
        expect(dropdown.classes()).toContain('left-0');
        expect(dropdown.classes()).not.toContain('right-0');
    });

    it('dropdown aligns right when align="right"', async () => {
        const wrapper = mount(TimePickerInput, {
            props: { modelValue: '09:00', align: 'right' },
        });
        await wrapper.find('button').trigger('click');
        const dropdown = wrapper.find('[class*="absolute bottom-full"]');
        expect(dropdown.classes()).toContain('right-0');
        expect(dropdown.classes()).not.toContain('left-0');
    });

    it('renders 24 hour buttons', async () => {
        const wrapper = mount(TimePickerInput, {
            props: { modelValue: '09:00' },
        });
        await wrapper.find('button').trigger('click');
        const hourColumn = wrapper.findAll('.overflow-y-auto')[0];
        expect(hourColumn.findAll('button')).toHaveLength(24);
    });

    it('renders 12 minute buttons (00, 05 … 55)', async () => {
        const wrapper = mount(TimePickerInput, {
            props: { modelValue: '09:00' },
        });
        await wrapper.find('button').trigger('click');
        const minuteColumn = wrapper.findAll('.overflow-y-auto')[1];
        expect(minuteColumn.findAll('button')).toHaveLength(12);
        expect(minuteColumn.findAll('button')[0].text()).toBe('00');
        expect(minuteColumn.findAll('button')[11].text()).toBe('55');
    });

    it('active hour has data-active attribute', async () => {
        const wrapper = mount(TimePickerInput, {
            props: { modelValue: '14:00' },
        });
        await wrapper.find('button').trigger('click');
        const hourColumn = wrapper.findAll('.overflow-y-auto')[0];
        const activeBtn = hourColumn.find('[data-active]');
        expect(activeBtn.text()).toBe('14');
    });

    it('clicking hour emits update:modelValue and keeps dropdown open', async () => {
        const wrapper = mount(TimePickerInput, {
            props: { modelValue: '08:00' },
        });
        await wrapper.find('button').trigger('click');
        const hourColumn = wrapper.findAll('.overflow-y-auto')[0];
        const hour16 = hourColumn
            .findAll('button')
            .find((b) => b.text() === '16')!;
        await hour16.trigger('click');

        expect(wrapper.emitted('update:modelValue')?.[0]).toEqual(['16:00']);
        expect(wrapper.emitted('select')).toBeUndefined();
        expect(wrapper.find('.overflow-y-auto').exists()).toBe(true);
    });

    it('clicking minute emits update:modelValue and select, closes dropdown', async () => {
        const wrapper = mount(TimePickerInput, {
            props: { modelValue: '08:00' },
        });
        await wrapper.find('button').trigger('click');
        const minuteColumn = wrapper.findAll('.overflow-y-auto')[1];
        const min45 = minuteColumn
            .findAll('button')
            .find((b) => b.text() === '45')!;
        await min45.trigger('click');

        expect(wrapper.emitted('update:modelValue')?.[0]).toEqual(['08:45']);
        expect(wrapper.emitted('select')?.[0]).toEqual(['08:45']);
        expect(wrapper.find('.overflow-y-auto').exists()).toBe(false);
    });

    it('backdrop click closes the dropdown', async () => {
        const wrapper = mount(TimePickerInput, {
            props: { modelValue: '09:00' },
        });
        await wrapper.find('button').trigger('click');
        expect(wrapper.find('.overflow-y-auto').exists()).toBe(true);

        await wrapper.find('.fixed.inset-0').trigger('click');
        expect(wrapper.find('.overflow-y-auto').exists()).toBe(false);
    });
});
