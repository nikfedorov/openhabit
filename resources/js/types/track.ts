export type Habit = {
    id: number;
    name: string;
    description: string | null;
    iterations_required: number;
    is_completed: boolean;
    current_iteration: number;
    sort_order: number;
};

export type ActivityDay = {
    date: string;
    percentage: number;
    completed: number;
    total: number;
    intensity: number;
};

export type AiDigestItem = {
    date: string;
    dateLabel: string;
    content: string | null;
};

export type TrackTranslations = {
    progress: string;
    all_done: string;
    no_habits_scheduled: string;
    for_this_day: string;
    daily_note: string;
    saving: string;
    how_was_your_day: string;
    today: string;
    activity: string;
    last_n_days: string;
    less: string;
    more: string;
    previous_day: string;
    next_day: string;
    ai_digest: string;
    save_note: string;
    add_habit: string;
};

export type TrackData = {
    date: string;
    dayName: string;
    dateFormatted: string;
    isToday: boolean;
    habits: Habit[];
    totalHabits: number;
    completedCount: number;
    dailyNoteContent: string;
    activityData: ActivityDay[];
    translations: TrackTranslations;
    aiDigest: AiDigestItem | null;
};
