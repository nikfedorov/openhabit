import type {
    EditHabit,
    EditTranslations,
    HabitFormData,
    HabitTranslations,
} from './edit';
import type { NavigationTranslations } from './navigation';
import type { ActivityDay, Habit, TrackData } from './track';
import type {
    GridHabit,
    LifeTranslations,
    WeekActivityData,
    WeekDay,
    WeekTranslations,
    YearActivityData,
    YearTranslations,
    WeekViewData,
    YearViewData,
    LifeViewData,
} from './view';

export type UserSettings = {
    locale: string;
    theme: 'light' | 'dark' | 'system';
    moveCompletedToEnd: boolean;
};

export type CommonData = {
    navigationTranslations: NavigationTranslations;
    settings: UserSettings;
};

export type ApiResponse<T> = CommonData & {
    data: T;
};

export type TrackApiResponse = CommonData & {
    data: Omit<TrackData, 'habits' | 'activityData'>;
    habits: Habit[];
    activityData: ActivityDay[];
};

export type WeekApiResponse = CommonData & {
    data: WeekViewData;
    days: WeekDay[];
    habits: GridHabit[];
    translations: WeekTranslations;
};

export type YearApiResponse = CommonData & {
    data: YearViewData;
    translations: YearTranslations;
    activityData: WeekActivityData[] | null;
};

export type LifeApiResponse = CommonData & {
    data: LifeViewData;
    translations: LifeTranslations;
    activityData: YearActivityData[] | null;
};

export type EditApiResponse = CommonData & {
    data: EditHabit[];
    translations: EditTranslations;
    habitTranslations: HabitTranslations;
};

export type HabitShowApiResponse = {
    data: HabitFormData;
};
