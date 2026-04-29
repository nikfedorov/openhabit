export type EditHabit = {
    id: number;
    name: string;
    description: string | null;
    is_active: boolean;
    sort_order: number;
    iterations_required: number;
    human_readable: string;
    is_franklin_virtue: boolean;
};

export type HabitFormData = {
    id?: number;
    name: string;
    description: string | null;
    iterations_required: number;
    is_active: boolean;
    frequency: 'DAILY' | 'WEEKLY' | 'MONTHLY';
    weekly_days: number[];
    monthly_days: number[];
    monthly_mode: 'day' | 'position';
    monthly_position: number;
    monthly_weekday: number;
    notifications: NotificationTime[];
};

export type NotificationTime = {
    time: string;
    is_active: boolean;
};

export type TemplateHabit = {
    id: number;
    name: string;
    human_readable: string;
    iterations_required: number;
    category: string;
    sort_order: number;
};

export type EditTranslations = {
    habits: string;
    manage_routines: string;
    new_habit: string;
    no_habits_yet: string;
    create_first_habit: string;
    create_a_habit: string;
    franklins_virtues: string;
    disable_all: string;
    enable_all: string;
    active: string;
    paused: string;
    delete: string;
    delete_habit: string;
    delete_confirm: string;
    save: string;
    cancel: string;
    time: string;
    times: string;
    templates: string;
    add_from_library: string;
    added: string;
};

export type HabitTranslations = {
    edit_habit: string;
    new_habit: string;
    name: string;
    name_placeholder: string;
    name_help: string;
    description: string;
    description_optional: string;
    description_placeholder: string;
    description_help: string;
    frequency: string;
    frequency_help: string;
    days_of_week: string;
    days_help: string;
    specific_days: string;
    positional: string;
    days_of_month: string;
    days_of_month_help: string;
    which_occurrence: string;
    day_of_week: string;
    positional_help: string;
    times_per_day: string;
    times_per_day_help: string;
    reminders: string;
    add: string;
    no_reminders: string;
    reminders_help: string;
    active: string;
    active_help: string;
    delete_habit: string;
    delete_confirm: string;
    save_changes: string;
    create_habit: string;
    cancel: string;
    freq_daily: string;
    freq_weekly: string;
    freq_monthly: string;
    day_mo: string;
    day_tu: string;
    day_we: string;
    day_th: string;
    day_fr: string;
    day_sa: string;
    day_su: string;
    position_1st: string;
    position_2nd: string;
    position_3rd: string;
    position_4th: string;
    position_last: string;
    select_one_day: string;
    time: string;
    times: string;
};
