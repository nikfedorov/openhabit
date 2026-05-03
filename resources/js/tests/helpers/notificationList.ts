import { mount, type VueWrapper } from '@vue/test-utils';
import NotificationList from '@/components/edit/NotificationList.vue';
import { makeHabitTranslations } from '@/tests/helpers/edit';

type Notification = { time: string; is_active: boolean };

/**
 * Mount the `NotificationList` with default props plus optional overrides.
 * Returns the wrapper and a small toolbox for the most common interactions
 * the test suite performs (clicking the add button, opening the time pill,
 * reading the emitted update payload, etc.).
 */
export function mountNotificationList(
    props: {
        modelValue?: Notification[];
        hasPremium?: boolean;
    } = {},
) {
    const wrapper = mount(NotificationList, {
        props: {
            modelValue: props.modelValue ?? [],
            translations: makeHabitTranslations(),
            hasPremium: props.hasPremium ?? true,
        },
    });

    return {
        wrapper,
        rows: () => wrapper.findAll('.flex.items-center.gap-2'),
        addBtn: () => wrapper.findAll('button').find((b) => b.text() === 'Add'),
        timePill: (rowIndex = 0) =>
            wrapper
                .findAll('.flex.items-center.gap-2')
                [rowIndex].findAll('button')[1],
        toggleBtn: (rowIndex = 0) =>
            wrapper
                .findAll('.flex.items-center.gap-2')
                [rowIndex].findAll('button')[0],
        removeBtn: (rowIndex = 0) =>
            wrapper
                .findAll('.flex.items-center.gap-2')
                [rowIndex].findAll('button')[2],
        async openTimePill(rowIndex = 0) {
            await wrapper
                .findAll('.flex.items-center.gap-2')
                [rowIndex].findAll('button')[1]
                .trigger('click');
        },
        emitted: () =>
            wrapper.emitted('update:modelValue')?.[0]?.[0] as
                | Notification[]
                | undefined,
    };
}

/**
 * Resolve a `<button>` inside a column by its visible label.
 */
export async function clickColumnOption(
    column: ReturnType<VueWrapper['find']>,
    label: string,
) {
    const option = column.findAll('button').find((b) => b.text() === label);
    if (!option) {
        throw new Error(`Option "${label}" not found in column`);
    }
    await option.trigger('click');
}
