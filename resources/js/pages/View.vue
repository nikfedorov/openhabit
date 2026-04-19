<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import BirthdateNotice from '@/components/view/BirthdateNotice.vue';
import LifeGrid from '@/components/view/LifeGrid.vue';
import LifeHeader from '@/components/view/LifeHeader.vue';
import WeekGrid from '@/components/view/WeekGrid.vue';
import WeekInsights from '@/components/view/WeekInsights.vue';
import WeekNavigator from '@/components/view/WeekNavigator.vue';
import YearGrid from '@/components/view/YearGrid.vue';
import YearNavigator from '@/components/view/YearNavigator.vue';
import type {
    LifeApiResponse,
    UserSettings,
    WeekApiResponse,
    YearApiResponse,
} from '@/types/api';
import type { NavigationTranslations } from '@/types/navigation';
import type {
    GridHabit,
    AiDigestItem,
    LifeStats,
    LifeTranslations,
    WeekActivityData,
    WeekDay,
    WeekTranslations,
    YearActivityData,
    YearTranslations,
    WeekViewData,
    YearViewData,
    LifeViewData,
} from '@/types/view';
import { apiFetch, getToken } from '@/utils/api';
import { findMondayOnOrAfter, formatDate } from '@/utils/date';

declare global {
    interface Window {
        Telegram?: {
            WebApp?: {
                initData?: string;
                shareToStory?: (url: string, params?: object) => void;
            };
        };
    }
}

const route = useRoute();
const router = useRouter();

const emit = defineEmits<{
    'navigation-translations': [translations: NavigationTranslations];
    settings: [settings: UserSettings];
    ready: [];
}>();

const tab = ref<'week' | 'year' | 'life'>('week');
const yearKey = ref(0);
const lifeKey = ref(0);
const weekData = ref<WeekViewData | null>(null);
const weekDays = ref<WeekDay[] | null>(null);
const weekHabits = ref<GridHabit[] | null>(null);
const weekDigests = ref<AiDigestItem[]>([]);
const yearData = ref<YearViewData | null>(null);
const yearActivityData = ref<WeekActivityData[] | null>(null);
const lifeData = ref<LifeViewData | null>(null);
const lifeActivityData = ref<YearActivityData[] | null>(null);
const weekTranslations = ref<WeekTranslations | null>(null);
const yearTranslations = ref<YearTranslations | null>(null);
const lifeTranslations = ref<LifeTranslations | null>(null);
const tabTranslations = computed(
    () =>
        weekTranslations.value ??
        yearTranslations.value ??
        lifeTranslations.value,
);
const lifeStats = computed<LifeStats | null>(() => {
    const d = lifeData.value;
    if (
        !d ||
        d.currentAge === null ||
        d.weeksLived === null ||
        d.yearsRemaining === null
    ) {
        return null;
    }
    return {
        currentAge: d.currentAge,
        weeksLived: d.weeksLived,
        yearsRemaining: d.yearsRemaining,
    };
});
const loading = ref(false);
const navDirection = ref<'nav-forward' | 'nav-backward' | null>(null);
const telegramBotUsername = ref<string | null>(null);
const isSharing = ref(false);
const isTelegramStoryAvailable = computed(
    () =>
        !!window.Telegram?.WebApp?.initData &&
        typeof window.Telegram?.WebApp?.shareToStory === 'function',
);

async function shareToStory() {
    if (isSharing.value || !isTelegramStoryAvailable.value) return;
    isSharing.value = true;
    try {
        const target =
            document.getElementById('view-panel-week') ??
            document.getElementById('view-panel-year') ??
            document.getElementById('view-panel-life');
        if (!target) return;

        const { default: html2canvas } = await import('html2canvas-pro');
        const isDark = document.documentElement.classList.contains('dark');
        const bg = isDark ? '#171717' : '#ffffff';

        const canvas = await html2canvas(target, {
            backgroundColor: bg,
            scale: 2,
            useCORS: true,
        });

        const storyCanvas = document.createElement('canvas');
        storyCanvas.width = 1080;
        storyCanvas.height = 1920;
        const ctx = storyCanvas.getContext('2d')!;

        ctx.fillStyle = bg;
        ctx.fillRect(0, 0, 1080, 1920);

        const padding = 40;
        const ratio = Math.min(
            (1080 - padding * 2) / canvas.width,
            (1920 - padding * 2) / canvas.height,
            1,
        );
        const drawW = Math.round(canvas.width * ratio);
        const drawH = Math.round(canvas.height * ratio);

        ctx.drawImage(
            canvas,
            Math.round((1080 - drawW) / 2),
            Math.round((1920 - drawH) / 2),
            drawW,
            drawH,
        );

        const token = getToken();
        const response = await fetch('/api/story/upload', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                ...(token ? { Authorization: `Bearer ${token}` } : {}),
            },
            body: JSON.stringify({ image: storyCanvas.toDataURL('image/png') }),
        });

        if (!response.ok) return;

        const data = (await response.json()) as { url: string };
        const params: Record<string, unknown> = {};
        if (telegramBotUsername.value) {
            params.widget_link = {
                url: `https://t.me/${telegramBotUsername.value}`,
                name: document.title,
            };
        }

        window.Telegram!.WebApp!.shareToStory!(data.url, params);
    } finally {
        isSharing.value = false;
    }
}

function queryParam(key: string): string | undefined {
    const v = route.query[key];
    return typeof v === 'string' ? v : undefined;
}

function buildViewQuery(): Record<string, string> {
    const query: Record<string, string> = { tab: tab.value };
    if (tab.value === 'week' && weekData.value && !weekData.value.isCurrent) {
        query.week = weekData.value.start;
    }
    if (
        tab.value === 'year' &&
        yearData.value?.selected !== null &&
        yearData.value?.selected !== undefined
    ) {
        query.year = String(yearData.value.selected);
    }
    return query;
}

function handleCommonResponse(
    response: WeekApiResponse | YearApiResponse | LifeApiResponse,
) {
    emit('navigation-translations', response.navigationTranslations);
    emit('settings', response.settings);
    telegramBotUsername.value = response.settings.telegramBotUsername;
}

async function loadWeek(params?: { week?: string }) {
    loading.value = true;
    const query = new URLSearchParams();
    if (params?.week) query.set('week', params.week);
    const queryStr = query.toString();
    const response = await apiFetch<WeekApiResponse>(
        `/api/view/week${queryStr ? `?${queryStr}` : ''}`,
    );
    weekData.value = response.data;
    weekDays.value = response.days;
    weekHabits.value = response.habits;
    weekDigests.value = response.aiDigests;
    weekTranslations.value = response.translations;
    tab.value = 'week';
    handleCommonResponse(response);
    router.replace({ query: buildViewQuery() });
    loading.value = false;
    emit('ready');
}

async function loadYear(params?: { year?: number }) {
    loading.value = true;
    const query = new URLSearchParams();
    if (params?.year !== undefined) query.set('year', String(params.year));
    const queryStr = query.toString();
    const response = await apiFetch<YearApiResponse>(
        `/api/view/year${queryStr ? `?${queryStr}` : ''}`,
    );
    const isNewYear = yearData.value?.selected !== response.data.selected;
    yearData.value = response.data;
    yearActivityData.value = response.activityData;
    yearTranslations.value = response.translations;
    tab.value = 'year';
    if (isNewYear) yearKey.value++;
    handleCommonResponse(response);
    router.replace({ query: buildViewQuery() });
    loading.value = false;
    emit('ready');
}

async function loadLife() {
    loading.value = true;
    const response = await apiFetch<LifeApiResponse>('/api/view/life');
    const isFirstLifeLoad = lifeData.value === null;
    lifeData.value = response.data;
    lifeActivityData.value = response.activityData;
    lifeTranslations.value = response.translations;
    tab.value = 'life';
    if (isFirstLifeLoad) lifeKey.value++;
    handleCommonResponse(response);
    router.replace({ query: buildViewQuery() });
    loading.value = false;
    emit('ready');
}

function setTab(newTab: 'week' | 'year' | 'life') {
    const tabOrder = ['week', 'year', 'life'];
    const oldIndex = tabOrder.indexOf(tab.value);
    const newIndex = tabOrder.indexOf(newTab);
    navDirection.value = newIndex >= oldIndex ? 'nav-forward' : 'nav-backward';
    tab.value = newTab;
    if (newTab === 'week') {
        loadWeek({
            week: weekData.value?.isCurrent ? undefined : weekData.value?.start,
        });
    } else if (newTab === 'year') {
        loadYear({ year: yearData.value?.selected ?? undefined });
    } else {
        loadLife();
    }
}

function selectWeek(weekStart?: string) {
    if (weekStart !== undefined) {
        navDirection.value =
            weekData.value && weekStart > weekData.value.start
                ? 'nav-forward'
                : 'nav-backward';
        loadWeek({ week: weekStart });
    } else {
        /* v8 ignore next */
        if (weekData.value) {
            const currentMonday = formatDate(findMondayOnOrAfter(new Date()));
            navDirection.value =
                weekData.value.start < currentMonday
                    ? 'nav-forward'
                    : 'nav-backward';
        }
        loadWeek();
    }
}

function selectYear(year: number) {
    navDirection.value =
        yearData.value?.selected !== null &&
        yearData.value?.selected !== undefined &&
        year >= yearData.value.selected
            ? 'nav-forward'
            : 'nav-backward';
    loadYear({ year });
}

function selectWeekFromYear(weekNum: number) {
    /* v8 ignore next */
    if (!yearData.value?.birthdate || yearData.value.selected === null) return;

    const birth = new Date(yearData.value.birthdate);
    const birthday = new Date(
        birth.getFullYear() + yearData.value.selected,
        birth.getMonth(),
        birth.getDate(),
    );
    const monday = findMondayOnOrAfter(birthday);
    monday.setDate(monday.getDate() + weekNum * 7);

    loadWeek({ week: formatDate(monday) });
}

onMounted(() => {
    const tabParam = queryParam('tab');
    const week = queryParam('week');
    const yearStr = queryParam('year');
    const year = yearStr !== undefined ? Number(yearStr) : undefined;

    if (tabParam === 'year') {
        loadYear({
            year: year !== undefined && !Number.isNaN(year) ? year : undefined,
        });
    } else if (tabParam === 'life') {
        loadLife();
    } else {
        loadWeek({ week });
    }
});
</script>

<template>
    <div v-if="weekData || yearData || lifeData" :class="navDirection">
        <!-- Tab Navigation -->
        <div class="mb-4 flex items-stretch gap-2">
            <div
                class="flex flex-1 gap-1 rounded-lg bg-neutral-100 p-1 dark:bg-neutral-800"
                role="tablist"
                aria-label="View range"
            >
                <button
                    v-for="tabName in ['week', 'year', 'life'] as const"
                    :key="tabName"
                    type="button"
                    role="tab"
                    :id="`view-tab-${tabName}`"
                    :aria-selected="tab === tabName"
                    :aria-controls="`view-panel-${tabName}`"
                    :tabindex="tab === tabName ? 0 : -1"
                    class="flex-1 rounded-md px-3 py-2 text-sm font-medium transition-all duration-150"
                    :class="
                        tab === tabName
                            ? 'bg-white text-neutral-900 shadow-sm dark:bg-neutral-700 dark:text-white'
                            : 'text-neutral-600 hover:text-neutral-900 dark:text-neutral-400 dark:hover:text-white'
                    "
                    @click="setTab(tabName)"
                >
                    {{ tabTranslations?.[tabName] }}
                </button>
            </div>

            <!-- Share Story Button -->
            <div
                v-if="isTelegramStoryAvailable"
                class="flex-shrink-0 self-stretch rounded-lg bg-neutral-100 p-1 dark:bg-neutral-800"
            >
                <button
                    type="button"
                    :disabled="isSharing"
                    data-testid="share-story-btn"
                    class="flex h-full w-10 items-center justify-center rounded-md text-neutral-500 transition-all duration-150 hover:bg-white hover:text-neutral-700 hover:shadow-sm dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-200"
                    :class="{ 'pointer-events-none opacity-50': isSharing }"
                    @click="shareToStory"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 rtl:-scale-x-100"
                        fill="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            d="M13 14h-2a8.999 8.999 0 0 0-7.968 4.81A10.136 10.136 0 0 1 3 18C3 12.477 7.477 8 13 8V3l10 8-10 8v-5z"
                        />
                    </svg>
                </button>
            </div>
        </div>

        <TransitionGroup
            name="view-content"
            tag="div"
            class="grid grid-cols-1 overflow-hidden"
        >
            <!-- Week Tab -->
            <div
                v-if="tab === 'week' && weekData"
                key="week"
                id="view-panel-week"
                role="tabpanel"
                aria-labelledby="view-tab-week"
                class="col-start-1 row-start-1 min-w-0"
            >
                <WeekNavigator
                    :week-start="weekData.start"
                    :week-start-formatted="weekData.startFormatted"
                    :week-end-formatted="weekData.endFormatted"
                    :week-end-formatted-full="weekData.endFormattedFull"
                    :week-year="weekData.year"
                    :is-current-week="weekData.isCurrent"
                    :translations="weekTranslations!"
                    @select-week="selectWeek"
                />

                <WeekGrid
                    v-if="weekDays && weekHabits"
                    class="mt-4"
                    :days="weekDays"
                    :habits="weekHabits"
                    :translations="weekTranslations!"
                />

                <!-- Weekly AI Insights -->
                <WeekInsights
                    v-if="weekDigests.length > 0"
                    class="mt-4"
                    :digests="weekDigests"
                    :label="weekTranslations!.insights"
                />
            </div>

            <!-- Year Tab -->
            <div
                v-if="tab === 'year' && yearData"
                key="year"
                id="view-panel-year"
                role="tabpanel"
                aria-labelledby="view-tab-year"
                class="col-start-1 row-start-1 min-w-0"
            >
                <YearNavigator
                    :selected-year="yearData.selected"
                    :current-age="yearData.currentAge"
                    :birthdate="yearData.birthdate"
                    :translations="yearTranslations!"
                    @select-year="selectYear"
                />

                <YearGrid
                    v-if="yearData.birthdate"
                    :key="yearKey"
                    class="mt-4"
                    :birthdate="yearData.birthdate"
                    :selected-year="yearData.selected"
                    :current-age="yearData.currentAge"
                    :weekly-activity="yearActivityData"
                    :translations="yearTranslations!"
                    @select-week="selectWeekFromYear"
                />

                <BirthdateNotice
                    v-else
                    :title="yearTranslations?.set_birthdate"
                    :description="yearTranslations?.to_see_year_visualization"
                />
            </div>

            <!-- Life Tab -->
            <div
                v-if="tab === 'life' && lifeData"
                key="life"
                id="view-panel-life"
                role="tabpanel"
                aria-labelledby="view-tab-life"
                class="col-start-1 row-start-1 min-w-0"
            >
                <LifeHeader
                    v-if="lifeStats"
                    :life-stats="lifeStats"
                    :translations="lifeTranslations!"
                />

                <LifeGrid
                    v-if="lifeData.birthdate"
                    :key="lifeKey"
                    class="mt-4"
                    :birthdate="lifeData.birthdate"
                    :current-age="lifeData.currentAge"
                    :yearly-activity="lifeActivityData"
                    :translations="lifeTranslations!"
                    @select-year="selectYear"
                />

                <BirthdateNotice
                    v-else
                    :title="lifeTranslations?.set_birthdate"
                    :description="lifeTranslations?.to_see_life_visualization"
                />
            </div>
        </TransitionGroup>
    </div>
</template>

<style>
/* Tab content transitions */
.view-content-move {
    transition: transform 0.3s ease-in-out;
}

.view-content-enter-active,
.view-content-leave-active {
    transition: all 0.3s ease-in-out;
}

.view-content-leave-active {
    z-index: 0;
}

.view-content-enter-active {
    z-index: 1;
}

.view-content-enter-from,
.view-content-leave-to {
    opacity: 0;
}

.view-content-enter-from {
    transform: translateX(var(--slide-enter));
}

.view-content-leave-to {
    transform: translateX(var(--slide-leave));
}
</style>
