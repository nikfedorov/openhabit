import type { NavigationTranslations } from './navigation';

export type UserSettings = {
    theme: 'light' | 'dark' | 'system';
};

export type CommonData = {
    locale: string;
    navigationTranslations: NavigationTranslations;
    settings: UserSettings;
};

export type ApiResponse<T> = CommonData & {
    data: T;
};
