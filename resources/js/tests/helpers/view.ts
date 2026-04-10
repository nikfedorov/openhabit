import type { FranklinGrid, GridHabit, LifeStats, WeekDay } from '@/types/view';

export const defaultViewTranslations = {
    week: 'Week',
    year: 'Year',
    life: 'Life',
    previous_week: 'Previous week',
    next_week: 'Next week',
    this_week: 'This Week',
    current_week: 'Current week',
    previous_year: 'Previous year',
    next_year: 'Next year',
    this_year: 'This Year',
    current_year: 'Current year',
    age: 'Age :age',
    less: 'Less',
    more: 'More',
    future: 'Future',
    habits: 'Habits',
    done: 'Done',
    partial: 'Partial',
    missed: 'Missed',
    no_habits_yet: 'No habits yet',
    create_habits_to_track: 'Create habits to track them here',
    memento_mori: 'Memento Mori',
    years_old: 'years old',
    years_left: 'years left',
    weeks_lived: 'weeks lived',
    each_square_year: 'Each square = <strong>1 year</strong>',
    each_square_week: 'Each square = <strong>1 week</strong>',
    seneca_quote: 'It is not that we have a short time to live...',
    seneca_author: 'Seneca',
    franklins_virtues: "Franklin's Virtues",
    set_birthdate: 'Set your birthdate in settings',
    to_see_year_visualization: 'to see your year visualization',
    to_see_life_visualization: 'to see your life visualization',
};

export const defaultNavigationTranslations = {
    track: 'Track',
    view: 'View',
};

export const defaultLifeStats: LifeStats = {
    currentAge: 25,
    weeksLived: 1300,
    yearsRemaining: 55,
};

export function makeDays(): WeekDay[] {
    return [
        {
            date: '2026-04-06',
            day_name: 'Mon',
            day_number: 6,
            is_today: true,
            is_future: false,
        },
        {
            date: '2026-04-07',
            day_name: 'Tue',
            day_number: 7,
            is_today: false,
            is_future: false,
        },
        {
            date: '2026-04-08',
            day_name: 'Wed',
            day_number: 8,
            is_today: false,
            is_future: false,
        },
        {
            date: '2026-04-09',
            day_name: 'Thu',
            day_number: 9,
            is_today: false,
            is_future: false,
        },
        {
            date: '2026-04-10',
            day_name: 'Fri',
            day_number: 10,
            is_today: false,
            is_future: false,
        },
        {
            date: '2026-04-11',
            day_name: 'Sat',
            day_number: 11,
            is_today: false,
            is_future: true,
        },
        {
            date: '2026-04-12',
            day_name: 'Sun',
            day_number: 12,
            is_today: false,
            is_future: true,
        },
    ];
}

export function makeGridHabit(overrides: Partial<GridHabit> = {}): GridHabit {
    return {
        id: 1,
        name: 'Exercise',
        category: null,
        is_weekly_focus: false,
        days: {
            '2026-04-06': { completed: true, partial: false, scheduled: true },
            '2026-04-07': {
                completed: false,
                partial: false,
                scheduled: true,
            },
            '2026-04-08': {
                completed: false,
                partial: true,
                scheduled: true,
            },
            '2026-04-09': {
                completed: false,
                partial: false,
                scheduled: false,
            },
            '2026-04-10': {
                completed: false,
                partial: false,
                scheduled: true,
            },
            '2026-04-11': {
                completed: false,
                partial: false,
                scheduled: true,
            },
            '2026-04-12': {
                completed: false,
                partial: false,
                scheduled: true,
            },
        },
        ...overrides,
    };
}

export function makeFranklinGrid(
    overrides: Partial<FranklinGrid> = {},
): FranklinGrid {
    return {
        week_start: '2026-04-06',
        week_end: '2026-04-12',
        days: makeDays(),
        regular_habits: [makeGridHabit()],
        franklin_habits: [],
        ...overrides,
    };
}
