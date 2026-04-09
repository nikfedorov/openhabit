import type { NavigationTranslations } from './navigation';

export type WeekDay = {
    date: string;
    day_name: string;
    day_number: number;
    is_today: boolean;
    is_future: boolean;
};

export type HabitDayStatus = {
    completed: boolean;
    partial: boolean;
    scheduled: boolean;
};

export type GridHabit = {
    id: number;
    name: string;
    category: string | null;
    is_weekly_focus: boolean;
    days: Record<string, HabitDayStatus>;
};

export type FranklinGrid = {
    week_start: string;
    week_end: string;
    days: WeekDay[];
    regular_habits: GridHabit[];
    franklin_habits: GridHabit[];
};

export type WeekActivityData = {
    weekNum: number;
    intensity: number;
    completed: number;
    total: number;
};

export type YearActivityData = {
    year: number;
    intensity: number;
    completed: number;
    total: number;
};

export type LifeStats = {
    currentAge: number;
    weeksLived: number;
    yearsRemaining: number;
};

export type ViewTranslations = {
    week: string;
    year: string;
    life: string;
    previous_week: string;
    next_week: string;
    this_week: string;
    current_week: string;
    previous_year: string;
    next_year: string;
    this_year: string;
    current_year: string;
    age: string;
    less: string;
    more: string;
    future: string;
    habits: string;
    done: string;
    partial: string;
    missed: string;
    no_habits_yet: string;
    create_habits_to_track: string;
    memento_mori: string;
    years_old: string;
    years_left: string;
    weeks_lived: string;
    each_square_year: string;
    each_square_week: string;
    seneca_quote: string;
    seneca_author: string;
};

export type ViewData = {
    tab: string;
    weekStart: string;
    weekEnd: string;
    weekStartFormatted: string;
    weekEndFormatted: string;
    weekEndFormattedFull: string;
    weekYear: string;
    isCurrentWeek: boolean;
    franklinGrid: FranklinGrid;
    selectedYear: number | null;
    birthdate: string | null;
    currentAge: number | null;
    lifeStats: LifeStats | null;
    weeklyActivityData: WeekActivityData[] | null;
    yearlyActivityData: YearActivityData[] | null;
    translations: ViewTranslations;
    navigationTranslations: NavigationTranslations;
};
