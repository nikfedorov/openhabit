import { mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import EditHabitItem from '@/components/edit/EditHabitItem.vue';
import { makeEditHabit, makeEditTranslations } from '@/tests/helpers/edit';

const translations = makeEditTranslations();

beforeEach(() => {
    Element.prototype.animate = vi
        .fn()
        .mockReturnValue({ pause: vi.fn(), cancel: vi.fn() });
    vi.spyOn(window, 'requestAnimationFrame').mockImplementation((cb) => {
        cb(0);
        return 0;
    });
});

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

    it('does not apply opacity for inactive pending habits', () => {
        const habit = makeEditHabit({ is_active: false });
        const wrapper = mount(EditHabitItem, {
            props: { habit, translations, pending: true },
        });
        expect(wrapper.find('.opacity-50').exists()).toBe(false);
    });

    it('sets data-pending attribute when pending', () => {
        const habit = makeEditHabit();
        const wrapper = mount(EditHabitItem, {
            props: { habit, translations, pending: true },
        });
        expect(
            wrapper
                .find('[data-testid="edit-habit-item"]')
                .attributes('data-pending'),
        ).toBe('true');
    });

    it('does not set data-pending attribute when not pending', () => {
        const habit = makeEditHabit();
        const wrapper = mount(EditHabitItem, {
            props: { habit, translations },
        });
        expect(
            wrapper
                .find('[data-testid="edit-habit-item"]')
                .attributes('data-pending'),
        ).toBeUndefined();
    });

    it('clears fade styles when transition ends after pending stops', async () => {
        const habit = makeEditHabit();
        const wrapper = mount(EditHabitItem, {
            props: { habit, translations, pending: true },
        });

        const el = wrapper.find('[data-testid="edit-habit-item"]')
            .element as HTMLElement;

        // Stop pending — triggers cleanup
        await wrapper.setProps({ pending: false });

        // Cleanup sets transition and opacity
        expect(el.style.transition).toBe('opacity 500ms ease');
        expect(el.style.opacity).toBe('1');

        // Fire transitionend to clear inline styles
        el.dispatchEvent(new Event('transitionend'));

        expect(el.style.opacity).toBe('');
        expect(el.style.transition).toBe('');
    });

    it('animates height expansion for pending items on mount', async () => {
        const habit = makeEditHabit();
        const wrapper = mount(EditHabitItem, {
            props: { habit, translations, pending: true },
        });
        await wrapper.vm.$nextTick();

        // After mount + rAF, the wrapper transitions to expanded
        expect(wrapper.classes()).toContain('grid-rows-[1fr]');
        expect(wrapper.classes()).not.toContain('grid-rows-[0fr]');
    });

    it('does not animate height for non-pending items', () => {
        const habit = makeEditHabit();
        const wrapper = mount(EditHabitItem, {
            props: { habit, translations },
        });

        expect(wrapper.classes()).toContain('grid-rows-[1fr]');
    });

    it('emits toggle-active on status button click', async () => {
        const habit = makeEditHabit({ id: 42 });
        const wrapper = mount(EditHabitItem, {
            props: { habit, translations },
        });
        const statusBtn = wrapper.findAll('button')[0];
        await statusBtn.trigger('click');
        expect(wrapper.emitted('toggle-active')?.[0]).toEqual([42]);
    });

    it('emits edit on content click', async () => {
        const habit = makeEditHabit({ id: 42 });
        const wrapper = mount(EditHabitItem, {
            props: { habit, translations },
        });
        const contentBtn = wrapper.findAll('button')[1];
        await contentBtn.trigger('click');
        expect(wrapper.emitted('edit')?.[0]).toEqual([42]);
    });

    it('shows green status for active habit', () => {
        const habit = makeEditHabit({ is_active: true });
        const wrapper = mount(EditHabitItem, {
            props: { habit, translations },
        });
        const statusBtn = wrapper.findAll('button')[0];
        expect(statusBtn.classes()).toContain('bg-green-500');
    });

    it('shows neutral status for inactive habit', () => {
        const habit = makeEditHabit({ is_active: false });
        const wrapper = mount(EditHabitItem, {
            props: { habit, translations },
        });
        const statusBtn = wrapper.findAll('button')[0];
        expect(statusBtn.classes()).toContain('bg-neutral-300');
    });

    it('emits edit on chevron button click', async () => {
        const habit = makeEditHabit({ id: 42 });
        const wrapper = mount(EditHabitItem, {
            props: { habit, translations },
        });
        const chevronBtn = wrapper.findAll('button')[2];
        await chevronBtn.trigger('click');
        expect(wrapper.emitted('edit')?.[0]).toEqual([42]);
    });
});
