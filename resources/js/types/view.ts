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
    is_franklin_virtue: boolean;
    days: Record<string, HabitDayStatus>;
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

export type WeekTranslations = {
    week: string;
    year: string;
    life: string;
    previous_week: string;
    next_week: string;
    this_week: string;
    current_week: string;
    habits: string;
    franklins_virtues: string;
    done: string;
    partial: string;
    missed: string;
    future: string;
};

export type YearTranslations = {
    week: string;
    year: string;
    life: string;
    previous_year: string;
    next_year: string;
    this_year: string;
    current_year: string;
    age: string;
    less: string;
    more: string;
    future: string;
    each_square_week: string;
    set_birthdate: string;
    to_see_year_visualization: string;
};

export type LifeTranslations = {
    week: string;
    year: string;
    life: string;
    memento_mori: string;
    years_old: string;
    years_left: string;
    weeks_lived: string;
    less: string;
    more: string;
    future: string;
    each_square_year: string;
    seneca_quote: string;
    seneca_author: string;
    set_birthdate: string;
    to_see_life_visualization: string;
};

export type WeekViewData = {
    start: string;
    end: string;
    startFormatted: string;
    endFormatted: string;
    endFormattedFull: string;
    year: string;
    isCurrent: boolean;
};

export type YearViewData = {
    selected: number | null;
    birthdate: string | null;
    currentAge: number | null;
};

export type LifeViewData = {
    birthdate: string | null;
    currentAge: number | null;
    weeksLived: number | null;
    yearsRemaining: number | null;
};
