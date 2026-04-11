import type { NavigationTranslations } from './navigation';
import type { ActivityDay, Habit, TrackData } from './track';

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
