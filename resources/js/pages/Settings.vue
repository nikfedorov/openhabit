<script setup lang="ts">
import { useTextareaAutosize } from '@vueuse/core';
import { vMaska } from 'maska/vue';
import { computed, onMounted, ref } from 'vue';
import PageLoader from '@/components/PageLoader.vue';
import PremiumUpsellBanner from '@/components/PremiumUpsellBanner.vue';
import SettingRow from '@/components/settings/SettingRow.vue';
import ToggleSwitch from '@/components/settings/ToggleSwitch.vue';
import TimePickerInput from '@/components/TimePickerInput.vue';
import { useTrialUiState } from '@/composables/useTrialUiState';
import type {
    AiTone,
    SettingsApiResponse,
    SettingsTranslations,
    TrialData,
    UserSettings,
} from '@/types/api';
import type { NavigationTranslations } from '@/types/navigation';
import { apiFetch } from '@/utils/api';

const emit = defineEmits<{
    'navigation-translations': [translations: NavigationTranslations];
    settings: [settings: UserSettings];
    'open-premium-modal': [];
    ready: [];
}>();

// ─── Page data ───────────────────────────────────────────────

const loading = ref(true);
const locales = ref<Record<string, string>>({});
const timezones = ref<Record<string, Record<string, string>>>({});
const aiTones = ref<AiTone[]>([]);
const hasPremium = ref(false);
const translations = ref<SettingsTranslations>({} as SettingsTranslations);

// Shared state — dismissing the trial banner in App.vue is immediately
// reflected here because both use the same module-level ref.
const { trialData, setTrialData } = useTrialUiState();

// ─── Current settings state ──────────────────────────────────

const theme = ref<'light' | 'dark' | 'system'>('system');
const locale = ref('en');
const timezone = ref('UTC');
const birthdate = ref('');
const birthdateDisplay = ref('');
const birthdateError = ref(false);
const dayStartsAt = ref('03:00');
const moveCompletedToEnd = ref(true);
const aiDigestEnabled = ref(false);
const aiDigestTime = ref('09:00');
const aiToneId = ref<number | null>(null);
const longTermGoal = ref('');
const goalFocused = ref(false);
const goalProcessing = ref(false);
const goalTextareaRef = ref<HTMLTextAreaElement>();

useTextareaAutosize({
    element: goalTextareaRef,
    input: computed(() => longTermGoal.value),
});

const showGoalButton = computed(
    () => goalFocused.value || goalProcessing.value,
);

// ─── Dropdown state ──────────────────────────────────────────

const localeOpen = ref(false);
const timezoneOpen = ref(false);
const timezoneSearch = ref('');

const filteredTimezones = computed(() => {
    if (!timezoneSearch.value) {
        return timezones.value;
    }
    const q = timezoneSearch.value.toLowerCase();
    const result: Record<string, Record<string, string>> = {};
    for (const [region, zones] of Object.entries(timezones.value)) {
        const matched: Record<string, string> = {};
        for (const [key, label] of Object.entries(zones)) {
            if (
                label.toLowerCase().includes(q) ||
                key.toLowerCase().includes(q)
            ) {
                matched[key] = label;
            }
        }
        if (Object.keys(matched).length > 0) {
            result[region] = matched;
        }
    }
    return result;
});

const currentLocaleLabel = computed(
    () => locales.value[locale.value] ?? locale.value,
);

const currentTimezoneLabel = computed(() => {
    for (const zones of Object.values(timezones.value)) {
        if (zones[timezone.value]) {
            return zones[timezone.value];
        }
    }
    return timezone.value;
});

// ─── AI tone icon paths ─────────────────────────────────────

const toneIconPaths: Record<string, string> = {
    sun: 'M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z',
    heart: 'M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z',
    bolt: 'M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z',
    shield: 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z',
};

const fallbackIconPath =
    'M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z';

function toneIconPath(tone: AiTone): string {
    return toneIconPaths[tone.icon] ?? fallbackIconPath;
}

// ─── Theme buttons config ────────────────────────────────────

const themeOptions = computed(() => [
    {
        value: 'light' as const,
        title: translations.value.theme_light,
        icon: 'M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z',
    },
    {
        value: 'dark' as const,
        title: translations.value.theme_dark,
        icon: 'M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z',
    },
    {
        value: 'system' as const,
        title: translations.value.theme_system,
        icon: 'M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25',
    },
]);

// ─── Dropdown option classes ─────────────────────────────────

const activeOptionClass =
    'bg-green-50 font-medium text-green-700 dark:bg-green-900/30 dark:text-green-400';
const inactiveOptionClass =
    'text-neutral-700 hover:bg-neutral-100 dark:text-neutral-200 dark:hover:bg-neutral-600';

// ─── Load page data ──────────────────────────────────────────

async function loadData() {
    const response = await apiFetch<SettingsApiResponse>('/api/settings');

    locales.value = response.locales;
    timezones.value = response.timezones;
    aiTones.value = response.aiTones;
    hasPremium.value = response.hasPremium;
    setTrialData(response.data.trial);
    translations.value = response.translations;

    const s = response.data;
    theme.value = s.theme;
    locale.value = s.locale;
    timezone.value = s.timezone ?? 'UTC';
    birthdate.value = s.birthdate ?? '';
    birthdateDisplay.value = s.birthdate
        ? formatDateToDisplay(s.birthdate)
        : '';
    dayStartsAt.value = s.dayStartsAt ?? '03:00';
    moveCompletedToEnd.value = s.moveCompletedToEnd;
    aiDigestEnabled.value = s.aiDigestTime !== null;
    aiDigestTime.value = s.aiDigestTime ?? '09:00';
    aiToneId.value = s.aiToneId;
    longTermGoal.value = s.longTermGoal ?? '';

    emit('navigation-translations', response.navigationTranslations);
    emit('settings', s);
    loading.value = false;
    emit('ready');
}

onMounted(loadData);

// ─── Helpers ─────────────────────────────────────────────────

/** Convert YYYY-MM-DD → DD.MM.YYYY for display. */
function formatDateToDisplay(iso: string): string {
    const [y, m, d] = iso.split('-');
    return `${d}.${m}.${y}`;
}

/** Convert DD.MM.YYYY → YYYY-MM-DD for API. Returns null if invalid. */
function parseDisplayDate(display: string): string | null {
    const match = display.match(/^(\d{2})\.(\d{2})\.(\d{4})$/);
    if (!match) {
        return null;
    }
    const [, day, month, year] = match.map(Number);
    if (month < 1 || month > 12 || day < 1 || day > 31) {
        return null;
    }
    const date = new Date(year, month - 1, day);
    if (date.getDate() !== day || date.getMonth() !== month - 1) {
        return null;
    }
    return `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
}

const TIME_REGEX = /^([01]?\d|2[0-3]):[0-5]\d$/;

// ─── Save individual settings ─────────────────────────────────

async function saveSetting(data: Partial<Record<string, unknown>>) {
    const updated = await apiFetch<{ data: UserSettings }>(
        '/api/settings',
        { method: 'PATCH', body: JSON.stringify(data) },
        { silent: true },
    );
    emit('settings', updated.data);
}

async function updateTheme(value: 'light' | 'dark' | 'system') {
    theme.value = value;
    await saveSetting({ theme: value });
}

async function updateLocale(code: string) {
    locale.value = code;
    localeOpen.value = false;
    await saveSetting({ locale: code });
    window.location.reload();
}

async function updateTimezone(tz: string) {
    timezone.value = tz;
    timezoneOpen.value = false;
    timezoneSearch.value = '';
    await saveSetting({ timezone: tz });
}

async function updateMoveCompletedToEnd() {
    await saveSetting({ moveCompletedToEnd: moveCompletedToEnd.value });
}

async function updateBirthdateFromDisplay() {
    const display = birthdateDisplay.value.trim();
    if (!display) {
        birthdateError.value = false;
        birthdate.value = '';
        await saveSetting({ birthdate: null });
        return;
    }
    const iso = parseDisplayDate(display);
    if (!iso) {
        birthdateError.value = true;
        return;
    }
    birthdateError.value = false;
    birthdate.value = iso;
    await saveSetting({ birthdate: iso });
}

async function updateDayStartsAt(time?: string) {
    const value = time ?? dayStartsAt.value;
    if (!TIME_REGEX.test(value)) {
        return;
    }
    dayStartsAt.value = value;
    await saveSetting({ dayStartsAt: value });
}

async function toggleAiDigest() {
    if (aiDigestEnabled.value && !hasPremium.value) {
        aiDigestEnabled.value = false;
        emit('open-premium-modal');
        return;
    }
    if (aiDigestEnabled.value) {
        await saveSetting({ aiDigestTime: aiDigestTime.value });
    } else {
        await saveSetting({ aiDigestTime: null });
    }
}

async function updateAiDigestTime(time?: string) {
    const value = time ?? aiDigestTime.value;
    if (!TIME_REGEX.test(value)) {
        return;
    }
    aiDigestTime.value = value;
    await saveSetting({ aiDigestTime: value });
}

async function updateAiTone(id: number) {
    aiToneId.value = id;
    await saveSetting({ aiToneId: id });
}

async function saveGoal() {
    goalProcessing.value = true;
    try {
        await saveSetting({ longTermGoal: longTermGoal.value || null });
    } finally {
        goalProcessing.value = false;
    }
}

function closeTimezoneDropdown() {
    timezoneOpen.value = false;
    timezoneSearch.value = '';
}

// ─── Export ──────────────────────────────────────────────────

const exporting = ref(false);

/**
 * Non-null while the premium upsell banner should be visible:
 * user is trialing (or trial expired without a paid subscription),
 * data is loaded, and the trial banner is not already showing.
 */
const premiumUpsellData = computed((): TrialData | null => {
    const data = trialData.value;
    if (!data || data.shouldShowBanner) {
        return null;
    }
    // hasPremium is true for both paid subscribers AND trialing users.
    // Show the upsell for trialing users (dismissed banner) and for
    // fully expired trial users, but not for paid subscribers.
    if (data.hasPremium && !data.isTrialing) {
        return null;
    }
    return data;
});

async function exportData() {
    if (!hasPremium.value) {
        emit('open-premium-modal');
        return;
    }

    exporting.value = true;
    try {
        const response = await apiFetch<{ url: string }>(
            '/api/settings/export',
            { method: 'POST' },
        );

        const tg = window.Telegram?.WebApp;
        if (tg?.openLink) {
            tg.openLink(response.url);
        } else {
            window.open(response.url, '_blank');
        }
    } finally {
        exporting.value = false;
    }
}
</script>

<style scoped>
.expand-enter-active,
.expand-leave-active {
    display: grid;
    grid-template-rows: 1fr;
    transition:
        grid-template-rows 0.25s ease-in-out,
        opacity 0.2s ease-in-out;
}

.expand-enter-from,
.expand-leave-to {
    grid-template-rows: 0fr;
    opacity: 0;
}

.expand-enter-active > *,
.expand-leave-active > * {
    overflow: hidden;
}

.dropdown-enter-active {
    transition: all 0.1s ease-out;
}
.dropdown-leave-active {
    transition: all 0.075s ease-in;
}
.dropdown-enter-from,
.dropdown-leave-to {
    opacity: 0;
    transform: scale(0.95);
}

.save-enter-active,
.save-leave-active {
    transition:
        opacity 200ms ease,
        max-height 200ms ease,
        margin-top 200ms ease;
    overflow: hidden;
    max-height: 40px;
}

.save-enter-from,
.save-leave-to {
    opacity: 0;
    max-height: 0;
    margin-top: 0;
}
</style>

<template>
    <PageLoader v-if="loading" />

    <div v-else class="space-y-6">
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">
                {{ translations.title }}
            </h1>
            <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                {{ translations.subtitle }}
            </p>
        </div>

        <!-- ═══ Premium Upsell ═══ -->
        <PremiumUpsellBanner
            v-if="premiumUpsellData"
            :trial-data="premiumUpsellData"
            @open-premium-modal="emit('open-premium-modal')"
        />

        <!-- ═══ Appearance ═══ -->
        <section
            class="rounded-xl bg-neutral-100 px-4 py-4 dark:bg-neutral-800"
        >
            <h2
                class="mb-4 text-sm font-medium text-neutral-500 dark:text-neutral-400"
            >
                {{ translations.appearance }}
            </h2>

            <SettingRow
                :title="translations.theme"
                :subtitle="translations.color_scheme"
            >
                <template #icon>
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 2v2m2.837 12.385a6 6 0 1 1-7.223-7.222c.624-.147.97.66.715 1.248a4 4 0 0 0 5.26 5.259c.589-.255 1.396.09 1.248.715M16 12a4 4 0 0 0-4-4m7-3-1.256 1.256M20 12h2"
                        />
                    </svg>
                </template>

                <div
                    class="flex gap-1 rounded-lg bg-white p-1 dark:bg-neutral-700"
                >
                    <button
                        v-for="opt in themeOptions"
                        :key="opt.value"
                        type="button"
                        :class="[
                            'flex h-8 w-8 items-center justify-center rounded-md transition-all duration-150',
                            theme === opt.value
                                ? 'bg-green-100 text-green-600 dark:bg-green-900/50 dark:text-green-400'
                                : 'text-neutral-500 hover:bg-neutral-100 dark:text-neutral-400 dark:hover:bg-neutral-600',
                        ]"
                        :title="opt.title"
                        @click="updateTheme(opt.value)"
                    >
                        <svg
                            class="h-4 w-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                :d="opt.icon"
                            />
                        </svg>
                    </button>
                </div>
            </SettingRow>
        </section>

        <!-- ═══ Language & Region ═══ -->
        <section
            class="space-y-4 rounded-xl bg-neutral-100 px-4 py-4 dark:bg-neutral-800"
        >
            <h2
                class="text-sm font-medium text-neutral-500 dark:text-neutral-400"
            >
                {{ translations.language_and_region }}
            </h2>

            <!-- Language -->
            <SettingRow
                :title="translations.language"
                :subtitle="translations.display_language"
            >
                <template #icon>
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M10.5 21l5.25-11.25L21 21m-9-3h7.5M3 5.621a48.474 48.474 0 016-.371m0 0c1.12 0 2.233.038 3.334.114M9 5.25V3m3.334 2.364C11.176 10.658 7.69 15.08 3 17.502m9.334-12.138c.896.061 1.785.147 2.666.257m-4.589 8.495a18.023 18.023 0 01-3.827-5.802"
                        />
                    </svg>
                </template>

                <div class="relative">
                    <div
                        v-if="localeOpen"
                        class="fixed inset-0 z-40"
                        @click="localeOpen = false"
                    />
                    <button
                        type="button"
                        class="flex min-w-[120px] cursor-pointer items-center justify-between gap-2 rounded-lg bg-white px-3 py-2 text-sm font-medium text-neutral-900 transition-colors hover:bg-neutral-50 dark:bg-neutral-700 dark:text-white dark:hover:bg-neutral-600"
                        @click="localeOpen = !localeOpen"
                    >
                        <span>{{ currentLocaleLabel }}</span>
                        <svg
                            class="h-4 w-4 text-neutral-400 transition-transform"
                            :class="{ 'rotate-180': localeOpen }"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                            />
                        </svg>
                    </button>

                    <Transition name="dropdown">
                        <div
                            v-if="localeOpen"
                            class="absolute end-0 z-50 mt-2 w-48 rounded-lg bg-white py-1 shadow-lg ring-1 ring-black/5 dark:bg-neutral-700 dark:ring-white/10"
                        >
                            <button
                                v-for="(name, code) in locales"
                                :key="code"
                                type="button"
                                class="w-full px-4 py-2 text-start text-sm transition-colors"
                                :class="
                                    locale === code
                                        ? activeOptionClass
                                        : inactiveOptionClass
                                "
                                @click="updateLocale(code)"
                            >
                                <div class="flex items-center justify-between">
                                    <span>{{ name }}</span>
                                    <svg
                                        v-if="locale === code"
                                        class="h-4 w-4"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M4.5 12.75l6 6 9-13.5"
                                        />
                                    </svg>
                                </div>
                            </button>
                        </div>
                    </Transition>
                </div>
            </SettingRow>

            <!-- Timezone -->
            <SettingRow
                :title="translations.timezone"
                :subtitle="translations.your_timezone"
            >
                <template #icon>
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418"
                        />
                    </svg>
                </template>

                <div class="relative">
                    <div
                        v-if="timezoneOpen"
                        class="fixed inset-0 z-40"
                        @click="closeTimezoneDropdown"
                    />
                    <button
                        type="button"
                        class="flex max-w-[200px] cursor-pointer items-center justify-between gap-2 rounded-lg bg-white px-3 py-2 text-sm font-medium text-neutral-900 transition-colors hover:bg-neutral-50 dark:bg-neutral-700 dark:text-white dark:hover:bg-neutral-600"
                        @click="timezoneOpen = !timezoneOpen"
                    >
                        <span class="truncate">{{ currentTimezoneLabel }}</span>
                        <svg
                            class="h-4 w-4 shrink-0 text-neutral-400 transition-transform"
                            :class="{ 'rotate-180': timezoneOpen }"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                            />
                        </svg>
                    </button>

                    <Transition name="dropdown">
                        <div
                            v-if="timezoneOpen"
                            class="absolute end-0 z-50 mt-2 w-72 rounded-lg bg-white shadow-lg ring-1 ring-black/5 dark:bg-neutral-700 dark:ring-white/10"
                        >
                            <!-- Search -->
                            <div
                                class="border-b border-neutral-200 p-2 dark:border-neutral-600"
                            >
                                <div class="relative">
                                    <svg
                                        class="absolute start-3 top-1/2 h-4 w-4 -translate-y-1/2 text-neutral-400"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"
                                        />
                                    </svg>
                                    <input
                                        v-model="timezoneSearch"
                                        type="text"
                                        :placeholder="
                                            translations.search_timezone
                                        "
                                        class="w-full rounded-md border-0 bg-neutral-100 py-2 ps-9 pe-3 text-sm text-neutral-900 placeholder-neutral-400 focus:bg-white focus:ring-2 focus:ring-green-500 dark:bg-neutral-600 dark:text-white dark:focus:bg-neutral-500"
                                        @keydown.escape="closeTimezoneDropdown"
                                    />
                                </div>
                            </div>
                            <!-- Options -->
                            <div class="max-h-64 overflow-y-auto py-1">
                                <template
                                    v-for="(zones, region) in filteredTimezones"
                                    :key="region"
                                >
                                    <div
                                        class="px-3 py-1.5 text-xs font-semibold tracking-wider text-neutral-400 uppercase dark:text-neutral-500"
                                    >
                                        {{ region }}
                                    </div>
                                    <button
                                        v-for="(label, tz) in zones"
                                        :key="tz"
                                        type="button"
                                        class="w-full px-4 py-2 text-start text-sm transition-colors"
                                        :class="
                                            tz === timezone
                                                ? activeOptionClass
                                                : inactiveOptionClass
                                        "
                                        @click="updateTimezone(tz)"
                                    >
                                        <div
                                            class="flex items-center justify-between"
                                        >
                                            <span>{{ label }}</span>
                                            <svg
                                                v-if="tz === timezone"
                                                class="h-4 w-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="2"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M4.5 12.75l6 6 9-13.5"
                                                />
                                            </svg>
                                        </div>
                                    </button>
                                </template>

                                <div
                                    v-if="
                                        Object.keys(filteredTimezones)
                                            .length === 0
                                    "
                                    class="px-4 py-3 text-center text-sm text-neutral-500 dark:text-neutral-400"
                                >
                                    {{ translations.no_timezones_found }}
                                </div>
                            </div>
                        </div>
                    </Transition>
                </div>
            </SettingRow>
        </section>

        <!-- ═══ Personal ═══ -->
        <section
            class="space-y-4 rounded-xl bg-neutral-100 px-4 py-4 dark:bg-neutral-800"
        >
            <h2
                class="text-sm font-medium text-neutral-500 dark:text-neutral-400"
            >
                {{ translations.personal }}
            </h2>

            <!-- Move completed to end -->
            <SettingRow
                :title="translations.move_completed_to_end"
                :subtitle="translations.completed_habits_sink_to_bottom"
            >
                <template #icon>
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M3 4.5h14.25M3 9h9.75M3 13.5h9.75m4.5-4.5v12m0 0-3.75-3.75M17.25 21 21 17.25"
                        />
                    </svg>
                </template>

                <ToggleSwitch
                    v-model="moveCompletedToEnd"
                    @update:model-value="updateMoveCompletedToEnd"
                />
            </SettingRow>

            <!-- Birthdate -->
            <SettingRow
                :title="translations.birthdate"
                :subtitle="translations.for_life_calendar"
            >
                <template #icon>
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.845 8.51 6 9.473 6 10.608v2.513m6-4.87c1.355 0 2.697.055 4.024.165C17.155 8.51 18 9.473 18 10.608v2.513m-3-4.87v-1.5m-6 1.5v-1.5m12 9.75l-1.5.75a3.354 3.354 0 01-3 0 3.354 3.354 0 00-3 0 3.354 3.354 0 01-3 0 3.354 3.354 0 00-3 0 3.354 3.354 0 01-3 0L3 16.5m15-3.38a48.474 48.474 0 00-6-.37c-2.032 0-4.034.125-6 .37m12 0c.39.049.777.102 1.163.16 1.07.16 1.837 1.094 1.837 2.175v5.17c0 .62-.504 1.124-1.125 1.124H4.125A1.125 1.125 0 013 20.625v-5.17c0-1.08.768-2.014 1.837-2.174A47.78 47.78 0 016 13.12M12.265 3.11a.375.375 0 11-.53 0L12 2.845l.265.265zm-3 0a.375.375 0 11-.53 0L9 2.845l.265.265zm6 0a.375.375 0 11-.53 0L15 2.845l.265.265z"
                        />
                    </svg>
                </template>

                <div
                    class="flex items-center gap-1.5 rounded-lg bg-white px-2 py-2 dark:bg-neutral-700"
                >
                    <input
                        v-model="birthdateDisplay"
                        v-maska="'##.##.####'"
                        type="text"
                        inputmode="numeric"
                        enterkeyhint="done"
                        placeholder="DD.MM.YYYY"
                        maxlength="10"
                        class="w-20 border-0 bg-transparent p-0 text-center text-sm font-medium placeholder-neutral-400 focus:ring-0"
                        :class="
                            birthdateError
                                ? 'text-red-500'
                                : 'text-neutral-900 dark:text-white'
                        "
                        @blur="updateBirthdateFromDisplay"
                        @keydown.enter.prevent="
                            ($event.target as HTMLInputElement).blur()
                        "
                    />
                    <svg
                        class="h-4 w-4 shrink-0 text-neutral-400"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"
                        />
                    </svg>
                </div>
            </SettingRow>

            <!-- Day starts at -->
            <SettingRow
                :title="translations.day_starts_at"
                :subtitle="translations.when_the_day_begins"
            >
                <template #icon>
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                </template>

                <TimePickerInput
                    v-model="dayStartsAt"
                    align="right"
                    @select="updateDayStartsAt"
                />
            </SettingRow>

            <!-- Long-term goal -->
            <div>
                <div class="mb-3 flex items-center gap-3">
                    <div
                        class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-neutral-200 text-neutral-500 dark:bg-neutral-700 dark:text-neutral-400"
                    >
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"
                            />
                        </svg>
                    </div>
                    <div>
                        <p
                            class="text-sm font-medium text-neutral-900 dark:text-white"
                        >
                            {{ translations.long_term_goal }}
                        </p>
                    </div>
                </div>
                <textarea
                    ref="goalTextareaRef"
                    v-model="longTermGoal"
                    :placeholder="translations.long_term_goal_placeholder"
                    rows="1"
                    maxlength="500"
                    class="min-h-[4rem] w-full resize-none rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 placeholder-neutral-400 transition-all duration-150 focus:border-transparent focus:ring-2 focus:ring-green-500 focus:outline-none dark:border-neutral-600 dark:bg-neutral-900 dark:text-white dark:placeholder-neutral-500"
                    @focus="goalFocused = true"
                    @blur="
                        goalFocused = false;
                        saveGoal();
                    "
                />
                <Transition name="save">
                    <div
                        v-show="showGoalButton"
                        class="save-btn mt-2 flex justify-end"
                    >
                        <button
                            type="button"
                            :disabled="goalProcessing"
                            class="rounded-lg bg-green-600 px-4 py-1.5 text-sm font-medium text-white transition-colors hover:bg-green-700 disabled:opacity-50 dark:bg-green-500 dark:hover:bg-green-600"
                            @click="saveGoal"
                        >
                            {{
                                goalProcessing
                                    ? translations.saving
                                    : translations.save
                            }}
                        </button>
                    </div>
                </Transition>
            </div>
        </section>

        <!-- ═══ AI Digest ═══ -->
        <section
            class="space-y-4 rounded-xl bg-neutral-100 px-4 py-4 dark:bg-neutral-800"
        >
            <h2
                class="text-sm font-medium text-neutral-500 dark:text-neutral-400"
            >
                {{ translations.ai_digest }}
            </h2>

            <!-- Toggle -->
            <SettingRow
                :title="translations.enable_ai_digest"
                :subtitle="translations.daily_ai_summary"
            >
                <template #icon>
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 00-2.455 2.456z"
                        />
                    </svg>
                </template>

                <ToggleSwitch
                    v-model="aiDigestEnabled"
                    @update:model-value="toggleAiDigest"
                />
            </SettingRow>

            <Transition name="expand">
                <div v-if="aiDigestEnabled">
                    <div class="space-y-4">
                        <!-- Digest time -->
                        <SettingRow
                            :title="translations.digest_time"
                            :subtitle="translations.when_to_send_the_digest"
                        >
                            <template #icon>
                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                            </template>

                            <TimePickerInput
                                v-model="aiDigestTime"
                                align="right"
                                @select="updateAiDigestTime"
                            />
                        </SettingRow>

                        <!-- AI tone -->
                        <div v-if="aiTones.length > 0">
                            <div class="mb-3 flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-neutral-200 text-neutral-500 dark:bg-neutral-700 dark:text-neutral-400"
                                >
                                    <svg
                                        class="h-5 w-5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"
                                        />
                                    </svg>
                                </div>
                                <div>
                                    <p
                                        class="text-sm font-medium text-neutral-900 dark:text-white"
                                    >
                                        {{ translations.ai_tone }}
                                    </p>
                                    <p
                                        class="text-xs text-neutral-500 dark:text-neutral-400"
                                    >
                                        {{
                                            translations.tone_of_the_daily_summary
                                        }}
                                    </p>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <button
                                    v-for="tone in aiTones"
                                    :key="tone.id"
                                    type="button"
                                    class="flex w-full items-center gap-3 rounded-lg px-3 py-3 text-start transition-all duration-150"
                                    :class="
                                        aiToneId === tone.id
                                            ? 'bg-green-50 ring-1 ring-green-500/30 dark:bg-green-900/30'
                                            : 'bg-white hover:bg-neutral-50 dark:bg-neutral-700 dark:hover:bg-neutral-600'
                                    "
                                    @click="updateAiTone(tone.id)"
                                >
                                    <div
                                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg"
                                        :class="
                                            aiToneId === tone.id
                                                ? 'bg-green-100 text-green-600 dark:bg-green-900/50 dark:text-green-400'
                                                : 'bg-neutral-100 text-neutral-500 dark:bg-neutral-600 dark:text-neutral-400'
                                        "
                                    >
                                        <svg
                                            class="h-4 w-4"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                :d="toneIconPath(tone)"
                                            />
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p
                                            class="text-sm font-medium"
                                            :class="
                                                aiToneId === tone.id
                                                    ? 'text-green-700 dark:text-green-400'
                                                    : 'text-neutral-900 dark:text-white'
                                            "
                                        >
                                            {{ tone.name }}
                                        </p>
                                        <p
                                            class="text-xs"
                                            :class="
                                                aiToneId === tone.id
                                                    ? 'text-green-600 dark:text-green-500'
                                                    : 'text-neutral-500 dark:text-neutral-400'
                                            "
                                        >
                                            {{ tone.description }}
                                        </p>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </Transition>
        </section>

        <!-- ═══ Data ═══ -->
        <section
            class="rounded-xl bg-neutral-100 px-4 py-4 dark:bg-neutral-800"
        >
            <h2
                class="mb-4 text-sm font-medium text-neutral-500 dark:text-neutral-400"
            >
                {{ translations.data }}
            </h2>

            <SettingRow
                :title="translations.export_data"
                :subtitle="translations.export_data_desc"
            >
                <template #icon>
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"
                        />
                    </svg>
                </template>

                <button
                    type="button"
                    class="flex cursor-pointer items-center gap-2 rounded-lg bg-white px-3 py-2 text-sm font-medium text-neutral-900 transition-colors hover:bg-neutral-50 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-neutral-700 dark:text-white dark:hover:bg-neutral-600"
                    :disabled="exporting"
                    @click="exportData"
                >
                    <svg
                        v-if="exporting"
                        class="h-4 w-4 animate-spin"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                        />
                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                        />
                    </svg>
                    <span>{{
                        exporting ? translations.exporting : translations.export
                    }}</span>
                </button>
            </SettingRow>
        </section>
    </div>
</template>
