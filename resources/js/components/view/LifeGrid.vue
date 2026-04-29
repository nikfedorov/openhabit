<script setup lang="ts">
import { computed } from 'vue';
import ActivityCell from '@/components/view/ActivityCell.vue';
import ActivityLegend from '@/components/view/ActivityLegend.vue';
import type { LifeTranslations, YearActivityData } from '@/types/view';

const props = defineProps<{
    birthdate: string | null;
    currentAge: number | null;
    yearlyActivity: YearActivityData[] | null;
    translations: LifeTranslations;
}>();

const emit = defineEmits<{
    selectYear: [year: number];
}>();

const TOTAL_YEARS = 80;

function getYearData(year: number): YearActivityData {
    return (
        props.yearlyActivity?.[year] ?? {
            year,
            intensity: 0,
            completed: 0,
            total: 0,
        }
    );
}

function isLived(year: number): boolean {
    return props.currentAge !== null && year < props.currentAge;
}

function isCurrent(year: number): boolean {
    return props.currentAge !== null && year === props.currentAge;
}

function isFuture(year: number): boolean {
    return props.currentAge !== null && year > props.currentAge;
}

function getTooltip(year: number): string {
    const data = getYearData(year);
    let tooltip = `Year ${year}`;
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
                    v-html="translations.each_square_year"
                />
            </div>

            <!-- Years Grid (10 columns × 8 rows = 80 years) -->
            <div class="grid grid-cols-10 gap-1.5">
                <ActivityCell
                    v-for="year in TOTAL_YEARS"
                    :key="year - 1"
                    :intensity="getYearData(year - 1).intensity"
                    :is-lived="isLived(year - 1)"
                    :is-current="isCurrent(year - 1)"
                    :is-future="isFuture(year - 1)"
                    :is-clickable="true"
                    class="cell-appear relative aspect-square overflow-hidden transition-transform"
                    :style="{ animationDelay: `${300 + (year - 1) * 8}ms` }"
                    :title="getTooltip(year - 1)"
                    role="button"
                    @click="emit('selectYear', year - 1)"
                >
                    <span
                        v-if="(year - 1) % 10 === 0"
                        class="absolute inset-0 flex items-center justify-center text-[8px] font-medium"
                        :class="
                            isLived(year - 1) || isCurrent(year - 1)
                                ? 'text-neutral-600 dark:text-neutral-300'
                                : 'text-neutral-400 dark:text-neutral-500'
                        "
                    >
                        {{ year - 1 }}
                    </span>
                </ActivityCell>
            </div>

            <!-- Legend -->
            <ActivityLegend
                class="mt-4"
                :less-label="translations.less"
                :more-label="translations.more"
                :future-label="translations.future"
            />

            <!-- Quote -->
            <div
                class="mt-4 border-t border-neutral-200 pt-4 text-center dark:border-neutral-700"
            >
                <p
                    class="mx-auto max-w-md text-xs leading-relaxed text-neutral-500 italic dark:text-neutral-400"
                    v-html="translations.seneca_quote"
                />
                <p
                    class="mt-1 text-[10px] text-neutral-400 dark:text-neutral-500"
                >
                    — {{ translations.seneca_author }}
                </p>
            </div>
        </div>
    </div>
</template>
