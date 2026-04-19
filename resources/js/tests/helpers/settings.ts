import { flushPromises, mount } from '@vue/test-utils';
import Settings from '@/pages/Settings.vue';
import type {
    AiTone,
    SettingsApiResponse,
    SettingsTranslations,
    TrialData,
    UserSettings,
} from '@/types/api';

export const defaultSettingsTranslations: SettingsTranslations = {
    title: 'Settings',
    subtitle: 'Customize your experience',
    appearance: 'Appearance',
    theme: 'Theme',
    color_scheme: 'Color scheme',
    theme_light: 'Light',
    theme_dark: 'Dark',
    theme_system: 'System',
    language_and_region: 'Language & Region',
    language: 'Language',
    display_language: 'Display language',
    timezone: 'Timezone',
    your_timezone: 'Your timezone',
    search_timezone: 'Search timezone...',
    no_timezones_found: 'No timezones found',
    personal: 'Personal',
    move_completed_to_end: 'Move completed to end',
    completed_habits_sink_to_bottom: 'Completed habits sink to bottom',
    birthdate: 'Birthdate',
    for_life_calendar: 'For life calendar',
    day_starts_at: 'Day starts at',
    when_the_day_begins: 'When the day begins',
    ai_digest: 'AI Digest',
    enable_ai_digest: 'Enable AI digest',
    daily_ai_summary: 'Daily AI summary of your habits',
    digest_time: 'Digest time',
    when_to_send_the_digest: 'When to send the digest',
    ai_tone: 'AI tone',
    tone_of_the_daily_summary: 'Tone of the daily summary',
    data: 'Data',
    export_data: 'Export data',
    export_data_desc: 'Download all your data as CSV',
    export: 'Export',
    exporting: 'Exporting...',
};

export const defaultTrial: TrialData = {
    hasPremium: false,
    shouldShowBanner: false,
    bannerText: '',
    invoiceLink: 'https://t.me/test-invoice',
    learnMore: 'Learn more',
    featuresTitle: 'Premium Features',
    featuresSubtitle: 'Upgrade to unlock all features',
    featureNotifications: 'Multiple reminders per habit',
    featureAiDigest: 'Daily AI digest of your progress',
    featureExport: 'Export all your data as CSV',
    upgradeLabel: 'Upgrade',
    openInTelegramLabel: 'To upgrade, please open the app in Telegram.',
};

export const defaultSettings: UserSettings = {
    locale: 'en',
    theme: 'system',
    moveCompletedToEnd: true,
    timezone: 'UTC',
    dayStartsAt: '03:00',
    birthdate: null,
    aiDigestTime: null,
    aiToneId: null,
    trial: defaultTrial,
};

export const defaultAiTones: AiTone[] = [
    {
        id: 1,
        name: 'Motivational',
        description: 'Energising tone',
        icon: 'bolt',
    },
    { id: 2, name: 'Calm', description: 'Relaxing tone', icon: 'sun' },
    { id: 3, name: 'Caring', description: 'Warm tone', icon: 'heart' },
    { id: 4, name: 'Strict', description: 'Disciplined tone', icon: 'shield' },
];

export function makeSettingsResponse(
    settingsOverrides: Partial<UserSettings> = {},
    extras: Partial<
        Omit<SettingsApiResponse, 'data' | 'navigationTranslations'>
    > = {},
): SettingsApiResponse {
    return {
        data: { ...defaultSettings, ...settingsOverrides },
        navigationTranslations: {
            track: 'Track',
            view: 'View',
            settings: 'Settings',
        },
        locales: { en: 'English', ru: 'Russian' },
        timezones: {
            America: {
                'America/New_York': 'New York',
                'America/Chicago': 'Chicago',
            },
            Europe: { 'Europe/London': 'London', 'Europe/Paris': 'Paris' },
        },
        aiTones: extras.aiTones ?? defaultAiTones,
        hasPremium: extras.hasPremium ?? false,
        translations: defaultSettingsTranslations,
    };
}

export async function mountSettings(
    mockApiFetch: ReturnType<typeof import('vitest').vi.fn>,
    settingsOverrides: Partial<UserSettings> = {},
    extras: Partial<
        Omit<SettingsApiResponse, 'data' | 'navigationTranslations'>
    > = {},
) {
    mockApiFetch.mockResolvedValueOnce(
        makeSettingsResponse(settingsOverrides, extras),
    );
    const wrapper = mount(Settings);
    await flushPromises();
    return wrapper;
}
