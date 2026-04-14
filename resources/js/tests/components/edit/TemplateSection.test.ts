import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import TemplateSection from '@/components/edit/TemplateSection.vue';
import { makeEditTranslations, makeTemplateHabit } from '@/tests/helpers/edit';

const translations = makeEditTranslations();

describe('TemplateSection', () => {
    it('renders section title with subtitle', () => {
        const templates = [makeTemplateHabit()];
        const wrapper = mount(TemplateSection, {
            props: { templates, translations },
        });
        expect(wrapper.text()).toContain('Templates');
        expect(wrapper.text()).toContain('Add from library');
    });

    it('starts collapsed and expands on click', async () => {
        const templates = [makeTemplateHabit()];
        const wrapper = mount(TemplateSection, {
            props: { templates, translations },
        });

        // Content wrapper should have collapsed grid class
        const grid = wrapper.find('.grid');
        expect(grid.classes()).toContain('grid-rows-[0fr]');

        // Click header to expand
        await wrapper.find('.cursor-pointer').trigger('click');

        expect(grid.classes()).toContain('grid-rows-[1fr]');
    });

    it('renders category names', async () => {
        const templates = [
            makeTemplateHabit({ id: 1, category: 'Health & Fitness' }),
            makeTemplateHabit({ id: 2, category: 'Productivity' }),
        ];
        const wrapper = mount(TemplateSection, {
            props: { templates, translations },
        });

        await wrapper.find('.cursor-pointer').trigger('click');
        expect(wrapper.text()).toContain('Health & Fitness');
        expect(wrapper.text()).toContain('Productivity');
    });

    it('renders template habits with name and schedule', async () => {
        const templates = [
            makeTemplateHabit({
                id: 1,
                name: 'Morning Meditation',
                human_readable: 'Every day',
            }),
        ];
        const wrapper = mount(TemplateSection, {
            props: { templates, translations },
        });

        await wrapper.find('.cursor-pointer').trigger('click');
        expect(wrapper.text()).toContain('Morning Meditation');
        expect(wrapper.text()).toContain('Every day');
    });

    it('shows iterations when greater than 1', async () => {
        const templates = [
            makeTemplateHabit({ id: 1, iterations_required: 3 }),
        ];
        const wrapper = mount(TemplateSection, {
            props: { templates, translations },
        });

        await wrapper.find('.cursor-pointer').trigger('click');
        expect(wrapper.text()).toContain('3×');
    });

    it('hides iterations when equal to 1', async () => {
        const templates = [
            makeTemplateHabit({ id: 1, iterations_required: 1 }),
        ];
        const wrapper = mount(TemplateSection, {
            props: { templates, translations },
        });

        await wrapper.find('.cursor-pointer').trigger('click');
        expect(wrapper.text()).not.toContain('×');
    });

    it('emits copy when template is clicked', async () => {
        const templates = [makeTemplateHabit({ id: 42 })];
        const wrapper = mount(TemplateSection, {
            props: { templates, translations },
        });

        await wrapper.find('.cursor-pointer').trigger('click');
        const templateBtn = wrapper.find('.border-t button');
        await templateBtn.trigger('click');
        expect(wrapper.emitted('copy')?.[0]).toEqual([42]);
    });

    it('shows added state after copying a template', async () => {
        const templates = [makeTemplateHabit({ id: 1 })];
        const wrapper = mount(TemplateSection, {
            props: { templates, translations },
        });

        await wrapper.find('.cursor-pointer').trigger('click');
        const templateBtn = wrapper.find('.border-t button');
        await templateBtn.trigger('click');

        expect(wrapper.text()).toContain('Added!');
        // Check for green ring class
        expect(templateBtn.classes()).toContain('ring-1');
    });

    it('does not emit copy again for already added template', async () => {
        const templates = [makeTemplateHabit({ id: 1 })];
        const wrapper = mount(TemplateSection, {
            props: { templates, translations },
        });

        await wrapper.find('.cursor-pointer').trigger('click');
        const templateBtn = wrapper.find('.border-t button');
        await templateBtn.trigger('click');
        await templateBtn.trigger('click');

        expect(wrapper.emitted('copy')).toHaveLength(1);
    });

    it('renders multiple categories with multiple templates', async () => {
        const templates = [
            makeTemplateHabit({ id: 1, name: 'Exercise', category: 'Health' }),
            makeTemplateHabit({
                id: 2,
                name: 'Meditation',
                category: 'Health',
            }),
            makeTemplateHabit({
                id: 3,
                name: 'Read 30 min',
                category: 'Learning',
            }),
        ];
        const wrapper = mount(TemplateSection, {
            props: { templates, translations },
        });

        await wrapper.find('.cursor-pointer').trigger('click');
        expect(wrapper.text()).toContain('Health');
        expect(wrapper.text()).toContain('Exercise');
        expect(wrapper.text()).toContain('Meditation');
        expect(wrapper.text()).toContain('Learning');
        expect(wrapper.text()).toContain('Read 30 min');
    });
});
