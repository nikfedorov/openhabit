import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import FranklinSection from '@/components/edit/FranklinSection.vue';
import { makeEditHabit, makeEditTranslations } from '@/tests/helpers/edit';

const translations = makeEditTranslations();

describe('FranklinSection', () => {
    it('renders section title with count', () => {
        const habits = [
            makeEditHabit({ id: 1, name: 'Temperance' }),
            makeEditHabit({ id: 2, name: 'Silence' }),
        ];
        const wrapper = mount(FranklinSection, {
            props: { habits, translations },
        });
        expect(wrapper.text()).toContain("Franklin's Virtues");
        expect(wrapper.text()).toContain('(2)');
    });

    it('starts collapsed and expands on click', async () => {
        const habits = [makeEditHabit({ id: 1, name: 'Temperance' })];
        const wrapper = mount(FranklinSection, {
            props: { habits, translations },
        });

        // Content should be hidden when collapsed (v-show sets display:none)
        expect(wrapper.find('.border-t').exists()).toBe(true);
        expect(
            (wrapper.find('.border-t').element as HTMLElement).style.display,
        ).toBe('none');

        // Click header to expand
        await wrapper.find('.cursor-pointer').trigger('click');

        expect(
            (wrapper.find('.border-t').element as HTMLElement).style.display,
        ).not.toBe('none');
        expect(wrapper.text()).toContain('Temperance');
    });

    it('emits toggle-active when status button is clicked', async () => {
        const habits = [makeEditHabit({ id: 42, name: 'Order' })];
        const wrapper = mount(FranklinSection, {
            props: { habits, translations },
        });

        // Expand first
        await wrapper.find('.cursor-pointer').trigger('click');

        const statusBtn = wrapper.find('.border-t button');
        await statusBtn.trigger('click');
        expect(wrapper.emitted('toggle-active')?.[0]).toEqual([42]);
    });

    it('shows opacity for inactive habits', async () => {
        const habits = [makeEditHabit({ id: 1, is_active: false })];
        const wrapper = mount(FranklinSection, {
            props: { habits, translations },
        });

        // Expand
        await wrapper.find('.cursor-pointer').trigger('click');

        expect(wrapper.find('.opacity-50').exists()).toBe(true);
    });

    it('shows habit description when present', async () => {
        const habits = [
            makeEditHabit({
                id: 1,
                name: 'Temperance',
                description: 'Eat not to dullness',
            }),
        ];
        const wrapper = mount(FranklinSection, {
            props: { habits, translations },
        });

        await wrapper.find('.cursor-pointer').trigger('click');
        expect(wrapper.text()).toContain('Eat not to dullness');
    });

    it('hides description when null', async () => {
        const habits = [
            makeEditHabit({
                id: 1,
                name: 'Temperance',
                description: null,
            }),
        ];
        const wrapper = mount(FranklinSection, {
            props: { habits, translations },
        });

        await wrapper.find('.cursor-pointer').trigger('click');
        expect(wrapper.text()).toContain('Temperance');
        expect(wrapper.text()).not.toContain('Eat not to dullness');
    });

    it('emits toggle-all-active when bulk toggle button is clicked', async () => {
        const habits = [makeEditHabit({ id: 1, name: 'Temperance' })];
        const wrapper = mount(FranklinSection, {
            props: { habits, translations },
        });

        const bulkBtn = wrapper.find('.cursor-pointer button');
        await bulkBtn.trigger('click');
        expect(wrapper.emitted('toggle-all-active')).toHaveLength(1);
    });

    it('shows green bulk button when any habit is active', () => {
        const habits = [
            makeEditHabit({ id: 1, is_active: true }),
            makeEditHabit({ id: 2, is_active: false }),
        ];
        const wrapper = mount(FranklinSection, {
            props: { habits, translations },
        });

        const bulkBtn = wrapper.find('.cursor-pointer button');
        expect(bulkBtn.classes()).toContain('bg-green-500');
    });

    it('shows grey bulk button when no habits are active', () => {
        const habits = [
            makeEditHabit({ id: 1, is_active: false }),
            makeEditHabit({ id: 2, is_active: false }),
        ];
        const wrapper = mount(FranklinSection, {
            props: { habits, translations },
        });

        const bulkBtn = wrapper.find('.cursor-pointer button');
        expect(bulkBtn.classes()).toContain('bg-neutral-300');
    });
});
