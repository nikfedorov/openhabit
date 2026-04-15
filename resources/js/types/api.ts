import type {
    EditHabit,
    EditTranslations,
    HabitFormData,
    HabitTranslations,
    TemplateHabit,
} from './edit';
import type { NavigationTranslations } from './navigation';
import type { SettingsTranslations } from './settings';
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
    timezone: string | null;
    dayStartsAt: string | null;
    birthdate: string | null;
    aiDigestTime: string | null;
    aiToneId: number | null;
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
    templates: TemplateHabit[];
};

export type HabitShowApiResponse = CommonData & {
    data: HabitFormData;
    habitTranslations: HabitTranslations;
};

export type HabitCreateApiResponse = CommonData & {
    habitTranslations: HabitTranslations;
};

export type AiTone = {
    id: number;
    name: string;
    description: string | null;
    icon: string;
};

export type SettingsApiResponse = {
    data: UserSettings;
    navigationTranslations: NavigationTranslations;
    locales: Record<string, string>;
    timezones: Record<string, Record<string, string>>;
    aiTones: AiTone[];
    hasPremium: boolean;
    translations: SettingsTranslations;
};

export type { SettingsTranslations };
