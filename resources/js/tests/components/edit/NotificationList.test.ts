import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import NotificationList from '@/components/edit/NotificationList.vue';
import { makeHabitTranslations } from '@/tests/helpers/edit';

const translations = makeHabitTranslations();

describe('NotificationList', () => {
    it('shows empty state when no notifications', () => {
        const wrapper = mount(NotificationList, {
            props: { modelValue: [], translations, hasPremium: true },
        });
        expect(wrapper.text()).toContain('No reminders set');
    });

    it('shows add button when under 10 notifications', () => {
        const wrapper = mount(NotificationList, {
            props: { modelValue: [], translations, hasPremium: true },
        });
        expect(wrapper.text()).toContain('Add');
    });

    it('hides add button at 10 notifications', () => {
        const items = Array.from({ length: 10 }, (_, i) => ({
            time: `0${i}:00`.slice(-5),
            is_active: true,
        }));
        const wrapper = mount(NotificationList, {
            props: { modelValue: items, translations, hasPremium: true },
        });
        const addBtn = wrapper
            .findAll('button')
            .find((b) => b.text() === 'Add');
        expect(addBtn).toBeUndefined();
    });

    it('emits new notification on add click', async () => {
        const wrapper = mount(NotificationList, {
            props: { modelValue: [], translations, hasPremium: true },
        });
        const addBtn = wrapper
            .findAll('button')
            .find((b) => b.text() === 'Add')!;
        await addBtn.trigger('click');
        const emitted = wrapper.emitted('update:modelValue')?.[0]?.[0] as {
            time: string;
            is_active: boolean;
        }[];
        expect(emitted).toHaveLength(1);
        expect(emitted[0].time).toBe('09:00');
        expect(emitted[0].is_active).toBe(true);
    });

    it('renders notification items with time display', () => {
        const wrapper = mount(NotificationList, {
            props: {
                modelValue: [{ time: '08:30', is_active: true }],
                translations,
                hasPremium: true,
            },
        });
        expect(wrapper.text()).toContain('08');
        expect(wrapper.text()).toContain('30');
    });

    it('emits toggled active state on status click', async () => {
        const wrapper = mount(NotificationList, {
            props: {
                modelValue: [{ time: '08:00', is_active: true }],
                translations,
                hasPremium: true,
            },
        });
        const rows = wrapper.findAll('.flex.items-center.gap-2');
        const toggleBtn = rows[0].findAll('button')[0];
        await toggleBtn.trigger('click');
        const emitted = wrapper.emitted('update:modelValue')?.[0]?.[0] as {
            time: string;
            is_active: boolean;
        }[];
        expect(emitted[0].is_active).toBe(false);
    });

    it('emits notification removed on remove click', async () => {
        const wrapper = mount(NotificationList, {
            props: {
                modelValue: [
                    { time: '08:00', is_active: true },
                    { time: '14:00', is_active: true },
                ],
                translations,
                hasPremium: true,
            },
        });
        // Buttons in row: toggle, time pill, remove
        const rows = wrapper.findAll('.flex.items-center.gap-2');
        const removeBtn = rows[0].findAll('button')[2];
        await removeBtn.trigger('click');
        const emitted = wrapper.emitted('update:modelValue')?.[0]?.[0] as {
            time: string;
            is_active: boolean;
        }[];
        expect(emitted).toHaveLength(1);
        expect(emitted[0].time).toBe('14:00');
    });

    it('opens combined dropdown and selects hour', async () => {
        const wrapper = mount(NotificationList, {
            props: {
                modelValue: [{ time: '08:00', is_active: true }],
                translations,
                hasPremium: true,
            },
        });
        // Click the time pill (second button in the row — first is toggle)
        const rows = wrapper.findAll('.flex.items-center.gap-2');
        const timePill = rows[0].findAll('button')[1];
        await timePill.trigger('click');

        // Dropdown should appear with both columns
        const columns = wrapper.findAll('.overflow-y-auto');
        expect(columns.length).toBeGreaterThanOrEqual(2);

        // Active hour should be highlighted
        const hourColumn = wrapper.find('.overflow-y-auto');
        const activeOption = hourColumn.find('[data-active]');
        expect(activeOption.text()).toBe('08');

        // Select hour 14 — dropdown stays open
        const option14 = hourColumn
            .findAll('button')
            .find((b) => b.text() === '14')!;
        await option14.trigger('click');

        const emitted = wrapper.emitted('update:modelValue')?.[0]?.[0] as {
            time: string;
            is_active: boolean;
        }[];
        expect(emitted[0].time).toBe('14:00');

        // Dropdown should still be open after hour selection
        expect(wrapper.find('.overflow-y-auto').exists()).toBe(true);
    });

    it('selects minute and closes dropdown', async () => {
        const wrapper = mount(NotificationList, {
            props: {
                modelValue: [{ time: '08:00', is_active: true }],
                translations,
                hasPremium: true,
            },
        });
        // Open dropdown
        const rows = wrapper.findAll('.flex.items-center.gap-2');
        const timePill = rows[0].findAll('button')[1];
        await timePill.trigger('click');

        // Find minute column (second column)
        const minuteColumn = wrapper.findAll('.overflow-y-auto')[1];
        const option45 = minuteColumn
            .findAll('button')
            .find((b) => b.text() === '45')!;
        await option45.trigger('click');

        const emitted = wrapper.emitted('update:modelValue')?.[0]?.[0] as {
            time: string;
            is_active: boolean;
        }[];
        expect(emitted[0].time).toBe('08:45');

        // Dropdown closes after minute selection
        expect(wrapper.find('.overflow-y-auto').exists()).toBe(false);
    });

    it('closes dropdown when clicking backdrop', async () => {
        const wrapper = mount(NotificationList, {
            props: {
                modelValue: [{ time: '08:00', is_active: true }],
                translations,
                hasPremium: true,
            },
        });
        const rows = wrapper.findAll('.flex.items-center.gap-2');
        const timePill = rows[0].findAll('button')[1];
        await timePill.trigger('click');
        expect(wrapper.find('.overflow-y-auto').exists()).toBe(true);

        await wrapper.find('.fixed.inset-0').trigger('click');
        expect(wrapper.find('.overflow-y-auto').exists()).toBe(false);
    });

    it('toggles dropdown closed when clicking same button', async () => {
        const wrapper = mount(NotificationList, {
            props: {
                modelValue: [{ time: '08:00', is_active: true }],
                translations,
                hasPremium: true,
            },
        });
        const rows = wrapper.findAll('.flex.items-center.gap-2');
        const timePill = rows[0].findAll('button')[1];

        await timePill.trigger('click');
        expect(wrapper.find('.overflow-y-auto').exists()).toBe(true);

        await timePill.trigger('click');
        expect(wrapper.find('.overflow-y-auto').exists()).toBe(false);
    });

    it('normalizes non-multiple-of-5 minutes on hour change', async () => {
        const wrapper = mount(NotificationList, {
            props: {
                modelValue: [{ time: '08:07', is_active: true }],
                translations,
                hasPremium: true,
            },
        });
        const rows = wrapper.findAll('.flex.items-center.gap-2');
        const timePill = rows[0].findAll('button')[1];
        await timePill.trigger('click');

        const hourColumn = wrapper.find('.overflow-y-auto');
        const option10 = hourColumn
            .findAll('button')
            .find((b) => b.text() === '10')!;
        await option10.trigger('click');

        const emitted = wrapper.emitted('update:modelValue')?.[0]?.[0] as {
            time: string;
            is_active: boolean;
        }[];
        expect(emitted[0].time).toBe('10:05');
    });

    it('handles malformed time string gracefully', async () => {
        const wrapper = mount(NotificationList, {
            props: {
                modelValue: [{ time: '', is_active: true }],
                translations,
                hasPremium: true,
            },
        });
        const rows = wrapper.findAll('.flex.items-center.gap-2');
        const timePill = rows[0].findAll('button')[1];
        await timePill.trigger('click');

        const hourColumn = wrapper.find('.overflow-y-auto');
        const option11 = hourColumn
            .findAll('button')
            .find((b) => b.text() === '11')!;
        await option11.trigger('click');

        const emitted = wrapper.emitted('update:modelValue')?.[0]?.[0] as {
            time: string;
            is_active: boolean;
        }[];
        expect(emitted[0].time).toBe('11:00');
    });

    it('uses fallback hour when time has no colon', async () => {
        const wrapper = mount(NotificationList, {
            props: {
                modelValue: [{ time: '', is_active: true }],
                translations,
                hasPremium: true,
            },
        });
        const rows = wrapper.findAll('.flex.items-center.gap-2');
        const timePill = rows[0].findAll('button')[1];
        await timePill.trigger('click');

        const minuteColumn = wrapper.findAll('.overflow-y-auto')[1];
        const option10 = minuteColumn
            .findAll('button')
            .find((b) => b.text() === '10')!;
        await option10.trigger('click');

        const emitted = wrapper.emitted('update:modelValue')?.[0]?.[0] as {
            time: string;
            is_active: boolean;
        }[];
        expect(emitted[0].time).toBe('09:10');
    });

    it('emits open-premium-modal when non-premium user tries to add a second notification', async () => {
        const wrapper = mount(NotificationList, {
            props: {
                modelValue: [{ time: '09:00', is_active: true }],
                translations,
                hasPremium: false,
            },
        });
        const addBtn = wrapper
            .findAll('button')
            .find((b) => b.text() === 'Add')!;
        await addBtn.trigger('click');

        expect(wrapper.emitted('open-premium-modal')).toHaveLength(1);
        expect(wrapper.emitted('update:modelValue')).toBeUndefined();
    });

    it('allows premium user to add a second notification', async () => {
        const wrapper = mount(NotificationList, {
            props: {
                modelValue: [{ time: '09:00', is_active: true }],
                translations,
                hasPremium: true,
            },
        });
        const addBtn = wrapper
            .findAll('button')
            .find((b) => b.text() === 'Add')!;
        await addBtn.trigger('click');

        expect(wrapper.emitted('open-premium-modal')).toBeUndefined();
        const emitted = wrapper.emitted('update:modelValue')?.[0]?.[0] as {
            time: string;
            is_active: boolean;
        }[];
        expect(emitted).toHaveLength(2);
    });

    it('allows non-premium user to add the first notification', async () => {
        const wrapper = mount(NotificationList, {
            props: {
                modelValue: [],
                translations,
                hasPremium: false,
            },
        });
        const addBtn = wrapper
            .findAll('button')
            .find((b) => b.text() === 'Add')!;
        await addBtn.trigger('click');

        expect(wrapper.emitted('open-premium-modal')).toBeUndefined();
        const emitted = wrapper.emitted('update:modelValue')?.[0]?.[0] as {
            time: string;
            is_active: boolean;
        }[];
        expect(emitted).toHaveLength(1);
    });
});
