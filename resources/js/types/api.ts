import type { NavigationTranslations } from './navigation';

export type CommonData = {
    locale: string;
    navigationTranslations: NavigationTranslations;
};

export type ApiResponse<T> = CommonData & {
    data: T;
};
