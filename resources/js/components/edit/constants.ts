import type { HabitTranslations } from '@/types/edit';

export type WeekdayOption = {
    index: number;
    key: keyof Pick<
        HabitTranslations,
        | 'day_mo'
        | 'day_tu'
        | 'day_we'
        | 'day_th'
        | 'day_fr'
        | 'day_sa'
        | 'day_su'
    >;
};

export const WEEKDAYS: WeekdayOption[] = [
    { index: 0, key: 'day_mo' },
    { index: 1, key: 'day_tu' },
    { index: 2, key: 'day_we' },
    { index: 3, key: 'day_th' },
    { index: 4, key: 'day_fr' },
    { index: 5, key: 'day_sa' },
    { index: 6, key: 'day_su' },
];
