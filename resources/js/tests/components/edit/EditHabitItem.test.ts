import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import EditHabitItem from '@/components/edit/EditHabitItem.vue';
import { makeEditHabit, makeEditTranslations } from '@/tests/helpers/edit';

const translations = makeEditTranslations();

describe('EditHabitItem', () => {
    it('renders habit name and schedule', () => {
        const habit = makeEditHabit();
        const wrapper = mount(EditHabitItem, {
            props: { habit, translations },
        });
        expect(wrapper.text()).toContain('Morning Run');
        expect(wrapper.text()).toContain('Every day');
    });

    it('shows iterations badge for multi-iteration habits', () => {
        const habit = makeEditHabit({ iterations_required: 3 });
        const wrapper = mount(EditHabitItem, {
            props: { habit, translations },
        });
        expect(wrapper.text()).toContain('3×');
    });

    it('hides iterations badge for single-iteration habits', () => {
        const habit = makeEditHabit({ iterations_required: 1 });
        const wrapper = mount(EditHabitItem, {
            props: { habit, translations },
        });
        expect(wrapper.text()).not.toContain('1×');
    });

    it('applies opacity for inactive habits', () => {
        const habit = makeEditHabit({ is_active: false });
        const wrapper = mount(EditHabitItem, {
            props: { habit, translations },
        });
        expect(wrapper.find('.opacity-50').exists()).toBe(true);
    });

    it('emits toggle-active on status button click', async () => {
        const habit = makeEditHabit({ id: 42 });
        const wrapper = mount(EditHabitItem, {
            props: { habit, translations },
        });
        const statusBtn = wrapper.findAll('button')[1];
        await statusBtn.trigger('click');
        expect(wrapper.emitted('toggle-active')?.[0]).toEqual([42]);
    });

    it('emits edit on content click', async () => {
        const habit = makeEditHabit({ id: 42 });
        const wrapper = mount(EditHabitItem, {
            props: { habit, translations },
        });
        const contentBtn = wrapper.findAll('button')[2];
        await contentBtn.trigger('click');
        expect(wrapper.emitted('edit')?.[0]).toEqual([42]);
    });

    it('shows green status for active habit', () => {
        const habit = makeEditHabit({ is_active: true });
        const wrapper = mount(EditHabitItem, {
            props: { habit, translations },
        });
        const statusBtn = wrapper.findAll('button')[1];
        expect(statusBtn.classes()).toContain('bg-green-500');
    });

    it('shows neutral status for inactive habit', () => {
        const habit = makeEditHabit({ is_active: false });
        const wrapper = mount(EditHabitItem, {
            props: { habit, translations },
        });
        const statusBtn = wrapper.findAll('button')[1];
        expect(statusBtn.classes()).toContain('bg-neutral-300');
    });

    it('emits edit on chevron button click', async () => {
        const habit = makeEditHabit({ id: 42 });
        const wrapper = mount(EditHabitItem, {
            props: { habit, translations },
        });
        const chevronBtn = wrapper.findAll('button')[3];
        await chevronBtn.trigger('click');
        expect(wrapper.emitted('edit')?.[0]).toEqual([42]);
    });

    it('renders swipe delete button', () => {
        const habit = makeEditHabit();
        const wrapper = mount(EditHabitItem, {
            props: { habit, translations },
        });
        expect(wrapper.find('[data-testid="swipe-delete-btn"]').exists()).toBe(
            true,
        );
    });

    it('emits confirm-delete when swipe delete button is clicked', async () => {
        const habit = makeEditHabit({ id: 42 });
        const wrapper = mount(EditHabitItem, {
            props: { habit, translations },
        });
        const deleteBtn = wrapper.find('[data-testid="swipe-delete-btn"]');
        await deleteBtn.trigger('click');
        expect(wrapper.emitted('confirm-delete')?.[0]).toEqual([42]);
    });

    it('exposes resetSwipe method', () => {
        const habit = makeEditHabit();
        const wrapper = mount(EditHabitItem, {
            props: { habit, translations },
        });
        expect(typeof wrapper.vm.resetSwipe).toBe('function');
    });

    it('emits delete on large swipe gesture', async () => {
        const habit = makeEditHabit({ id: 42 });
        const wrapper = mount(EditHabitItem, {
            props: { habit, translations },
        });

        const swipeEl = wrapper.find('.select-none').element as HTMLElement;

        // Mock offsetWidth via parentElement
        Object.defineProperty(swipeEl, 'offsetWidth', { value: 400 });
        const parent = swipeEl.parentElement;
        if (parent) {
            Object.defineProperty(parent, 'offsetWidth', { value: 400 });
        }

        // Simulate a large left swipe (>75% of width)
        swipeEl.dispatchEvent(
            new TouchEvent('touchstart', {
                touches: [{ clientX: 400, clientY: 0 } as Touch],
            }),
        );
        swipeEl.dispatchEvent(
            new TouchEvent('touchmove', {
                cancelable: true,
                touches: [{ clientX: 80, clientY: 0 } as Touch],
            }),
        );
        swipeEl.dispatchEvent(new TouchEvent('touchend'));

        expect(wrapper.emitted('delete')?.[0]).toEqual([42]);
    });
});
