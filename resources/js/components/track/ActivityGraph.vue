<script setup lang="ts">
import { computed } from 'vue';
import type { ActivityDay } from '@/types';
import { formatDate } from '@/utils/date';
import { getIntensityColor } from '@/utils/intensity';

const EMPTY_DAY: ActivityDay = {
    date: '',
    percentage: 0,
    completed: 0,
    total: 0,
    intensity: 0,
};

const TOTAL_DAYS = 140;

const props = defineProps<{
    activityData: ActivityDay[];
    activityLabel: string;
    periodLabel: string;
    lessLabel: string;
    moreLabel: string;
}>();

const weeks = computed(() => {
    const today = new Date();
    const dataByDate = new Map(props.activityData.map((d) => [d.date, d]));

    const earliest = new Date(
        today.getFullYear(),
        today.getMonth(),
        today.getDate() - (TOTAL_DAYS - 1),
    );

    const dayOfWeek = earliest.getDay();
    const mondayOffset = dayOfWeek === 0 ? 6 : dayOfWeek - 1;
    const startMonday = new Date(earliest);
    startMonday.setDate(earliest.getDate() - mondayOffset);

    const result: (ActivityDay | null)[][] = [];
    const current = new Date(startMonday);

    while (current <= today) {
        const week: (ActivityDay | null)[] = [];
        for (let d = 0; d < 7; d++) {
            if (current > today || current < earliest) {
                week.push(null);
            } else {
                const dateStr = formatDate(current);
                week.push(
                    dataByDate.get(dateStr) ?? { ...EMPTY_DAY, date: dateStr },
                );
            }
            current.setDate(current.getDate() + 1);
        }
        result.push(week);
    }
    return result;
});

defineExpose({ weeks });
</script>

<template>
    <div class="mt-8">
        <div class="rounded-xl bg-neutral-100 px-4 py-4 dark:bg-neutral-800">
            <div class="mb-3 flex items-center justify-between">
                <span
                    class="text-sm font-medium text-neutral-600 dark:text-neutral-400"
                >
                    {{ activityLabel }}
                </span>
                <span class="text-xs text-neutral-500 dark:text-neutral-400">
                    {{ periodLabel }}
                </span>
            </div>

            <div
                class="grid gap-[3px]"
                :style="`grid-template-columns: repeat(${weeks.length}, minmax(0, 1fr))`"
            >
                <div
                    v-for="(week, wi) in weeks"
                    :key="wi"
                    class="flex flex-col gap-[3px]"
                >
                    <template v-for="(day, di) in week" :key="di">
                        <div
                            v-if="day === null"
                            class="aspect-square rounded-sm"
                        />
                        <div
                            v-else
                            class="aspect-square rounded-sm"
                            :class="getIntensityColor(day.intensity)"
                            :title="`${day.date}: ${day.completed}/${day.total} (${day.percentage}%)`"
                        />
                    </template>
                </div>
            </div>

            <!-- Legend -->
            <div class="mt-3 flex items-center justify-end gap-1">
                <span
                    class="me-1 text-[10px] text-neutral-500 dark:text-neutral-400"
                >
                    {{ lessLabel }}
                </span>
                <div
                    class="h-2.5 w-2.5 rounded-sm bg-neutral-200 dark:bg-neutral-700"
                />
                <div
                    v-for="level in [1, 2, 3, 4]"
                    :key="level"
                    class="h-2.5 w-2.5 rounded-sm"
                    :class="getIntensityColor(level)"
                />
                <span
                    class="ms-1 text-[10px] text-neutral-500 dark:text-neutral-400"
                >
                    {{ moreLabel }}
                </span>
            </div>
        </div>
    </div>
</template>
