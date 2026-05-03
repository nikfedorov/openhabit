import { describe, expect, it } from 'vitest';
import {
    clickColumnOption,
    mountNotificationList,
} from '@/tests/helpers/notificationList';

describe('NotificationList', () => {
    it('shows empty state when no notifications', () => {
        const { wrapper } = mountNotificationList();
        expect(wrapper.text()).toContain('No reminders set');
    });

    it('shows add button when under 10 notifications', () => {
        const { wrapper } = mountNotificationList();
        expect(wrapper.text()).toContain('Add');
    });

    it('hides add button at 10 notifications', () => {
        const items = Array.from({ length: 10 }, (_, i) => ({
            time: `0${i}:00`.slice(-5),
            is_active: true,
        }));
        const { addBtn } = mountNotificationList({ modelValue: items });
        expect(addBtn()).toBeUndefined();
    });

    it('emits new notification on add click', async () => {
        const { addBtn, emitted } = mountNotificationList();
        await addBtn()!.trigger('click');
        const items = emitted()!;
        expect(items).toHaveLength(1);
        expect(items[0].time).toBe('09:00');
        expect(items[0].is_active).toBe(true);
    });

    it('renders notification items with time display', () => {
        const { wrapper } = mountNotificationList({
            modelValue: [{ time: '08:30', is_active: true }],
        });
        expect(wrapper.text()).toContain('08');
        expect(wrapper.text()).toContain('30');
    });

    it('emits toggled active state on status click', async () => {
        const { toggleBtn, emitted } = mountNotificationList({
            modelValue: [{ time: '08:00', is_active: true }],
        });
        await toggleBtn().trigger('click');
        expect(emitted()![0].is_active).toBe(false);
    });

    it('emits notification removed on remove click', async () => {
        const { removeBtn, emitted } = mountNotificationList({
            modelValue: [
                { time: '08:00', is_active: true },
                { time: '14:00', is_active: true },
            ],
        });
        await removeBtn().trigger('click');
        const items = emitted()!;
        expect(items).toHaveLength(1);
        expect(items[0].time).toBe('14:00');
    });

    it('opens combined dropdown and selects hour', async () => {
        const { wrapper, openTimePill, emitted } = mountNotificationList({
            modelValue: [{ time: '08:00', is_active: true }],
        });
        await openTimePill();

        // Dropdown should appear with both columns
        const columns = wrapper.findAll('.overflow-y-auto');
        expect(columns.length).toBeGreaterThanOrEqual(2);

        // Active hour should be highlighted
        const hourColumn = wrapper.find('.overflow-y-auto');
        const activeOption = hourColumn.find('[data-active]');
        expect(activeOption.text()).toBe('08');

        // Select hour 14 — dropdown stays open
        await clickColumnOption(hourColumn, '14');

        expect(emitted()![0].time).toBe('14:00');

        // Dropdown should still be open after hour selection
        expect(wrapper.find('.overflow-y-auto').exists()).toBe(true);
    });

    it('selects minute and closes dropdown', async () => {
        const { wrapper, openTimePill, emitted } = mountNotificationList({
            modelValue: [{ time: '08:00', is_active: true }],
        });
        await openTimePill();

        // Find minute column (second column)
        const minuteColumn = wrapper.findAll('.overflow-y-auto')[1];
        await clickColumnOption(minuteColumn, '45');

        expect(emitted()![0].time).toBe('08:45');

        // Dropdown closes after minute selection
        expect(wrapper.find('.overflow-y-auto').exists()).toBe(false);
    });

    it('closes dropdown when clicking backdrop', async () => {
        const { wrapper, openTimePill } = mountNotificationList({
            modelValue: [{ time: '08:00', is_active: true }],
        });
        await openTimePill();
        expect(wrapper.find('.overflow-y-auto').exists()).toBe(true);

        await wrapper.find('.fixed.inset-0').trigger('click');
        expect(wrapper.find('.overflow-y-auto').exists()).toBe(false);
    });

    it('toggles dropdown closed when clicking same button', async () => {
        const { wrapper, timePill } = mountNotificationList({
            modelValue: [{ time: '08:00', is_active: true }],
        });
        const pill = timePill();

        await pill.trigger('click');
        expect(wrapper.find('.overflow-y-auto').exists()).toBe(true);

        await pill.trigger('click');
        expect(wrapper.find('.overflow-y-auto').exists()).toBe(false);
    });

    it('normalizes non-multiple-of-5 minutes on hour change', async () => {
        const { wrapper, openTimePill, emitted } = mountNotificationList({
            modelValue: [{ time: '08:07', is_active: true }],
        });
        await openTimePill();

        const hourColumn = wrapper.find('.overflow-y-auto');
        await clickColumnOption(hourColumn, '10');

        expect(emitted()![0].time).toBe('10:05');
    });

    it('handles malformed time string gracefully', async () => {
        const { wrapper, openTimePill, emitted } = mountNotificationList({
            modelValue: [{ time: '', is_active: true }],
        });
        await openTimePill();

        const hourColumn = wrapper.find('.overflow-y-auto');
        await clickColumnOption(hourColumn, '11');

        expect(emitted()![0].time).toBe('11:00');
    });

    it('uses fallback hour when time has no colon', async () => {
        const { wrapper, openTimePill, emitted } = mountNotificationList({
            modelValue: [{ time: '', is_active: true }],
        });
        await openTimePill();

        const minuteColumn = wrapper.findAll('.overflow-y-auto')[1];
        await clickColumnOption(minuteColumn, '10');

        expect(emitted()![0].time).toBe('09:10');
    });

    it('emits open-premium-modal when non-premium user tries to add a second notification', async () => {
        const { wrapper, addBtn } = mountNotificationList({
            modelValue: [{ time: '09:00', is_active: true }],
            hasPremium: false,
        });
        await addBtn()!.trigger('click');

        expect(wrapper.emitted('open-premium-modal')).toHaveLength(1);
        expect(wrapper.emitted('update:modelValue')).toBeUndefined();
    });

    it('allows premium user to add a second notification', async () => {
        const { wrapper, addBtn, emitted } = mountNotificationList({
            modelValue: [{ time: '09:00', is_active: true }],
        });
        await addBtn()!.trigger('click');

        expect(wrapper.emitted('open-premium-modal')).toBeUndefined();
        expect(emitted()).toHaveLength(2);
    });

    it('allows non-premium user to add the first notification', async () => {
        const { wrapper, addBtn, emitted } = mountNotificationList({
            hasPremium: false,
        });
        await addBtn()!.trigger('click');

        expect(wrapper.emitted('open-premium-modal')).toBeUndefined();
        expect(emitted()).toHaveLength(1);
    });
});
