<script setup lang="ts">
import { onMounted, ref } from 'vue';
import LifeGrid from '@/components/view/LifeGrid.vue';
import LifeHeader from '@/components/view/LifeHeader.vue';
import WeekGrid from '@/components/view/WeekGrid.vue';
import WeekNavigator from '@/components/view/WeekNavigator.vue';
import YearGrid from '@/components/view/YearGrid.vue';
import YearNavigator from '@/components/view/YearNavigator.vue';
import type { NavigationTranslations } from '@/types/navigation';
import type { ViewData } from '@/types/view';
import { apiFetch } from '@/utils/api';
import { addDays, findMondayOnOrAfter, formatDate } from '@/utils/date';

const emit = defineEmits<{
    navigate: [page: 'dashboard' | 'track' | 'view'];
    'navigation-translations': [translations: NavigationTranslations];
}>();

const data = ref<ViewData | null>(null);
const loading = ref(false);

async function loadView(params?: {
    tab?: string;
    week?: string;
    year?: number;
}) {
    loading.value = true;
    const query = new URLSearchParams();
    if (params?.tab) query.set('tab', params.tab);
    if (params?.week) query.set('week', params.week);
    if (params?.year !== undefined) query.set('year', String(params.year));

    const queryStr = query.toString();
    data.value = await apiFetch<ViewData>(
        `/api/view${queryStr ? `?${queryStr}` : ''}`,
    );
    emit('navigation-translations', data.value.navigationTranslations);
    loading.value = false;
}

function setTab(tab: string) {
    /* v8 ignore next */
    if (!data.value) return;
    data.value.tab = tab;

    if (
        tab === 'year' &&
        data.value.selectedYear === null &&
        data.value.currentAge !== null
    ) {
        data.value.selectedYear = data.value.currentAge;
    }

    // Reload data for year/life tabs that need server data
    if (tab !== 'week') {
        loadView({
            tab,
            week: data.value.weekStart,
            year: data.value.selectedYear ?? undefined,
        });
    }
}

function previousWeek() {
    /* v8 ignore next */
    if (!data.value) return;
    const prev = addDays(data.value.weekStart, -7);
    loadView({ tab: 'week', week: prev });
}

function nextWeek() {
    /* v8 ignore next */
    if (!data.value) return;
    const next = addDays(data.value.weekStart, 7);
    loadView({ tab: 'week', week: next });
}

function goToCurrentWeek() {
    loadView({ tab: 'week' });
}

function selectYear(year: number) {
    /* v8 ignore next */
    if (!data.value) return;
    data.value.selectedYear = year;
    data.value.tab = 'year';
    loadView({ tab: 'year', week: data.value.weekStart, year });
}

function selectWeekFromYear(weekNum: number) {
    /* v8 ignore next */
    if (!data.value?.birthdate || data.value.selectedYear === null) return;

    const birth = new Date(data.value.birthdate);
    const birthday = new Date(
        birth.getFullYear() + data.value.selectedYear,
        birth.getMonth(),
        birth.getDate(),
    );
    const monday = findMondayOnOrAfter(birthday);
    monday.setDate(monday.getDate() + weekNum * 7);

    loadView({ tab: 'week', week: formatDate(monday) });
}

onMounted(() => loadView());
</script>

<template>
    <div v-if="data">
        <!-- Tab Navigation -->
        <div class="mb-4 flex items-stretch gap-2">
            <div
                class="flex flex-1 gap-1 rounded-lg bg-neutral-100 p-1 dark:bg-neutral-800"
            >
                <button
                    v-for="tab in ['week', 'year', 'life'] as const"
                    :key="tab"
                    type="button"
                    class="flex-1 rounded-md px-3 py-2 text-sm font-medium transition-all duration-150"
                    :class="
                        data.tab === tab
                            ? 'bg-white text-neutral-900 shadow-sm dark:bg-neutral-700 dark:text-white'
                            : 'text-neutral-600 hover:text-neutral-900 dark:text-neutral-400 dark:hover:text-white'
                    "
                    @click="setTab(tab)"
                >
                    {{
                        data.translations[tab as keyof typeof data.translations]
                    }}
                </button>
            </div>
        </div>

        <!-- Week Tab -->
        <template v-if="data.tab === 'week'">
            <WeekNavigator
                :week-start-formatted="data.weekStartFormatted"
                :week-end-formatted="data.weekEndFormatted"
                :week-end-formatted-full="data.weekEndFormattedFull"
                :week-year="data.weekYear"
                :is-current-week="data.isCurrentWeek"
                :translations="data.translations"
                @previous-week="previousWeek"
                @next-week="nextWeek"
                @current-week="goToCurrentWeek"
            />

            <WeekGrid
                v-if="data.franklinGrid"
                class="mt-4"
                :data="data.franklinGrid"
                :translations="data.translations"
            />
        </template>

        <!-- Year Tab -->
        <template v-if="data.tab === 'year'">
            <YearNavigator
                :selected-year="data.selectedYear"
                :current-age="data.currentAge"
                :birthdate="data.birthdate"
                :translations="data.translations"
                @select-year="selectYear"
            />

            <YearGrid
                v-if="data.birthdate"
                class="mt-4"
                :birthdate="data.birthdate"
                :selected-year="data.selectedYear"
                :current-age="data.currentAge"
                :weekly-activity="data.weeklyActivityData"
                :translations="data.translations"
                @select-week="selectWeekFromYear"
            />
        </template>

        <!-- Life Tab -->
        <template v-if="data.tab === 'life'">
            <LifeHeader
                v-if="data.lifeStats"
                :life-stats="data.lifeStats"
                :translations="data.translations"
            />

            <LifeGrid
                v-if="data.birthdate"
                class="mt-4"
                :birthdate="data.birthdate"
                :current-age="data.currentAge"
                :yearly-activity="data.yearlyActivityData"
                :translations="data.translations"
                @select-year="selectYear"
            />
        </template>
    </div>
</template>
