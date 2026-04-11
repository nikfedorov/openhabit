<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import BirthdateNotice from '@/components/view/BirthdateNotice.vue';
import LifeGrid from '@/components/view/LifeGrid.vue';
import LifeHeader from '@/components/view/LifeHeader.vue';
import WeekGrid from '@/components/view/WeekGrid.vue';
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
import { apiFetch } from '@/utils/api';
import { addDays, findMondayOnOrAfter, formatDate } from '@/utils/date';

const route = useRoute();
const router = useRouter();

const emit = defineEmits<{
    'navigation-translations': [translations: NavigationTranslations];
    settings: [settings: UserSettings];
    ready: [];
}>();

const tab = ref<'week' | 'year' | 'life'>('week');
const weekData = ref<WeekViewData | null>(null);
const weekDays = ref<WeekDay[] | null>(null);
const weekHabits = ref<GridHabit[] | null>(null);
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
    yearData.value = response.data;
    yearActivityData.value = response.activityData;
    yearTranslations.value = response.translations;
    tab.value = 'year';
    handleCommonResponse(response);
    router.replace({ query: buildViewQuery() });
    loading.value = false;
    emit('ready');
}

async function loadLife() {
    loading.value = true;
    const response = await apiFetch<LifeApiResponse>('/api/view/life');
    lifeData.value = response.data;
    lifeActivityData.value = response.activityData;
    lifeTranslations.value = response.translations;
    tab.value = 'life';
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

function previousWeek() {
    /* v8 ignore next */
    if (!weekData.value) return;
    navDirection.value = 'nav-backward';
    const prev = addDays(weekData.value.start, -7);
    loadWeek({ week: prev });
}

function nextWeek() {
    /* v8 ignore next */
    if (!weekData.value) return;
    navDirection.value = 'nav-forward';
    const next = addDays(weekData.value.start, 7);
    loadWeek({ week: next });
}

function goToCurrentWeek() {
    loadWeek();
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
            >
                <button
                    v-for="tabName in ['week', 'year', 'life'] as const"
                    :key="tabName"
                    type="button"
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
                class="col-start-1 row-start-1 min-w-0"
            >
                <WeekNavigator
                    :week-start-formatted="weekData.startFormatted"
                    :week-end-formatted="weekData.endFormatted"
                    :week-end-formatted-full="weekData.endFormattedFull"
                    :week-year="weekData.year"
                    :is-current-week="weekData.isCurrent"
                    :translations="weekTranslations!"
                    @previous-week="previousWeek"
                    @next-week="nextWeek"
                    @current-week="goToCurrentWeek"
                />

                <WeekGrid
                    v-if="weekDays && weekHabits"
                    class="mt-4"
                    :days="weekDays"
                    :habits="weekHabits"
                    :translations="weekTranslations!"
                />
            </div>

            <!-- Year Tab -->
            <div
                v-if="tab === 'year' && yearData"
                key="year"
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
                class="col-start-1 row-start-1 min-w-0"
            >
                <LifeHeader
                    v-if="lifeStats"
                    :life-stats="lifeStats"
                    :translations="lifeTranslations!"
                />

                <LifeGrid
                    v-if="lifeData.birthdate"
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
