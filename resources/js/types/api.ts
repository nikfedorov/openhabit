import type { NavigationTranslations } from './navigation';

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
