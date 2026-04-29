import type {
    EditHabit,
    EditTranslations,
    HabitTranslations,
    TemplateHabit,
} from '@/types/edit';

export function makeEditTranslations(
    overrides: Partial<EditTranslations> = {},
): EditTranslations {
    return {
        habits: 'Habits',
        manage_routines: 'Manage your daily routines',
        new_habit: 'New Habit',
        no_habits_yet: 'No habits yet',
        create_first_habit: 'Create your first habit',
        create_a_habit: 'Create a habit',
        franklins_virtues: "Franklin's Virtues",
        disable_all: 'Disable all',
        enable_all: 'Enable all',
        active: 'Active',
        paused: 'Paused',
        delete: 'Delete',
        delete_habit: 'Delete Habit',
        delete_confirm: 'Are you sure? All completion data will be lost.',
        save: 'Save',
        cancel: 'Cancel',
        time: 'time',
        times: 'times',
        templates: 'Templates',
        add_from_library: 'Add from library',
        added: 'Added!',
        ...overrides,
    };
}

export function makeHabitTranslations(
    overrides: Partial<HabitTranslations> = {},
): HabitTranslations {
    return {
        edit_habit: 'Edit Habit',
        new_habit: 'New Habit',
        name: 'Habit Name',
        name_placeholder: 'e.g. Morning Run',
        name_help: 'Give your habit a clear name',
        description: 'Description',
        description_optional: '(optional)',
        description_placeholder: 'Add a description...',
        description_help: 'A short note',
        frequency: 'Frequency',
        frequency_help: 'How often?',
        days_of_week: 'Days of Week',
        days_help: 'Select days',
        specific_days: 'Specific Days',
        positional: 'Positional',
        days_of_month: 'Days of Month',
        days_of_month_help: 'Select days',
        which_occurrence: 'Which occurrence?',
        day_of_week: 'Day of week',
        positional_help: 'e.g. 2nd Tuesday',
        times_per_day: 'Times per day',
        times_per_day_help: 'How many repetitions?',
        reminders: 'Reminders',
        add: 'Add',
        no_reminders: 'No reminders set',
        reminders_help: 'Set notification times',
        active: 'Active',
        active_help: 'Pause or activate',
        delete_habit: 'Delete Habit',
        delete_confirm: 'Are you sure?',
        save_changes: 'Save Changes',
        create_habit: 'Create Habit',
        cancel: 'Cancel',
        freq_daily: 'Daily',
        freq_weekly: 'Weekly',
        freq_monthly: 'Monthly',
        day_mo: 'Mo',
        day_tu: 'Tu',
        day_we: 'We',
        day_th: 'Th',
        day_fr: 'Fr',
        day_sa: 'Sa',
        day_su: 'Su',
        position_1st: '1st',
        position_2nd: '2nd',
        position_3rd: '3rd',
        position_4th: '4th',
        position_last: 'Last',
        select_one_day: 'Select at least one day',
        time: 'time',
        times: 'times',
        ...overrides,
    };
}

export function makeEditHabit(overrides: Partial<EditHabit> = {}): EditHabit {
    return {
        id: 1,
        name: 'Morning Run',
        description: 'Run 5km every morning',
        is_active: true,
        sort_order: 1,
        iterations_required: 1,
        human_readable: 'Every day',
        is_franklin_virtue: false,
        ...overrides,
    };
}

export function makeTemplateHabit(
    overrides: Partial<TemplateHabit> = {},
): TemplateHabit {
    return {
        id: 1,
        name: 'Morning Meditation',
        human_readable: 'Every day',
        iterations_required: 1,
        category: 'Health & Fitness',
        sort_order: 1,
        ...overrides,
    };
}
