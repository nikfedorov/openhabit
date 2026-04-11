<script setup lang="ts">
import { computed } from 'vue';
import ActivityCell from '@/components/view/ActivityCell.vue';
import ActivityLegend from '@/components/view/ActivityLegend.vue';
import type { YearTranslations, WeekActivityData } from '@/types/view';
import { findMondayOnOrAfter } from '@/utils/date';

const props = defineProps<{
    birthdate: string | null;
    selectedYear: number | null;
    currentAge: number | null;
    weeklyActivity: WeekActivityData[] | null;
    translations: YearTranslations;
}>();

const emit = defineEmits<{
    selectWeek: [weekNum: number];
}>();

const WEEKS_PER_YEAR = 52;

const displayYear = computed(() => props.selectedYear ?? props.currentAge ?? 0);

/**
 * Calculate the start date of each week in the life year.
 */
const yearStartDate = computed(() => {
    /* v8 ignore next */
    if (!props.birthdate) return null;
    const birth = new Date(props.birthdate);
    const birthday = new Date(
        birth.getFullYear() + displayYear.value,
        birth.getMonth(),
        birth.getDate(),
    );
    return findMondayOnOrAfter(birthday);
});

const currentWeekStart = computed(() => {
    const now = new Date();
    const day = now.getDay();
    /* v8 ignore next */
    const diff = day === 0 ? 6 : day - 1;
    return new Date(now.getFullYear(), now.getMonth(), now.getDate() - diff);
});

function getWeekDate(weekNum: number): Date {
    /* v8 ignore next */
    if (!yearStartDate.value) return new Date();
    return new Date(
        yearStartDate.value.getFullYear(),
        yearStartDate.value.getMonth(),
        yearStartDate.value.getDate() + weekNum * 7,
    );
}

function isWeekLived(weekNum: number): boolean {
    return getWeekDate(weekNum) < currentWeekStart.value;
}

function isCurrentWeek(weekNum: number): boolean {
    const weekDate = getWeekDate(weekNum);
    return (
        weekDate.getFullYear() === currentWeekStart.value.getFullYear() &&
        weekDate.getMonth() === currentWeekStart.value.getMonth() &&
        weekDate.getDate() === currentWeekStart.value.getDate()
    );
}

function isWeekFuture(weekNum: number): boolean {
    return getWeekDate(weekNum) > currentWeekStart.value;
}

function getWeekData(weekNum: number): WeekActivityData {
    return (
        props.weeklyActivity?.[weekNum] ?? {
            weekNum,
            intensity: 0,
            completed: 0,
            total: 0,
        }
    );
}

function getTooltip(weekNum: number): string {
    const data = getWeekData(weekNum);
    let tooltip = `Week ${weekNum + 1}`;
    if (data.total > 0) {
        tooltip += `: ${data.completed}/${data.total} tasks`;
    }
    return tooltip;
}
</script>

<template>
    <div v-if="birthdate">
        <div class="rounded-xl bg-neutral-100 p-4 dark:bg-neutral-800">
            <!-- Header -->
            <div class="mb-4 text-center">
                <p
                    class="text-xs text-neutral-500 dark:text-neutral-400"
                    v-html="translations.each_square_week"
                />
            </div>

            <!-- Weeks Grid (4 rows × 13 weeks) -->
            <div class="grid grid-cols-13 gap-1">
                <!-- Header row with column numbers -->
                <div
                    v-for="col in 13"
                    :key="`header-${col}`"
                    class="pb-1 text-center text-[9px] text-neutral-400 dark:text-neutral-500"
                >
                    {{ col }}
                </div>

                <!-- 4 rows × 13 weeks = 52 weeks -->
                <template v-for="row in 4" :key="`row-${row}`">
                    <ActivityCell
                        v-for="col in 13"
                        :key="`${row}-${col}`"
                        :intensity="
                            getWeekData((row - 1) * 13 + (col - 1)).intensity
                        "
                        :is-lived="isWeekLived((row - 1) * 13 + (col - 1))"
                        :is-current="isCurrentWeek((row - 1) * 13 + (col - 1))"
                        :is-future="isWeekFuture((row - 1) * 13 + (col - 1))"
                        :is-clickable="true"
                        class="aspect-square transition-transform"
                        :title="getTooltip((row - 1) * 13 + (col - 1))"
                        role="button"
                        @click="emit('selectWeek', (row - 1) * 13 + (col - 1))"
                    />
                </template>
            </div>

            <!-- Legend -->
            <ActivityLegend
                class="mt-4"
                :less-label="translations.less"
                :more-label="translations.more"
                :future-label="translations.future"
            />
        </div>
    </div>
</template>

<style>
.grid-cols-13 {
    grid-template-columns: repeat(13, minmax(0, 1fr));
}
</style>
