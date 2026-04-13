<script setup lang="ts">
import { WEEKDAYS } from '@/components/edit/constants';
import type { HabitTranslations } from '@/types/edit';
import { toggleSortedItem } from '@/utils/array';

const mode = defineModel<'day' | 'position'>('mode', { required: true });
const days = defineModel<number[]>('days', { required: true });
const position = defineModel<number>('position', { required: true });
const weekday = defineModel<number>('weekday', { required: true });

defineProps<{
    translations: HabitTranslations;
}>();

const positions = [
    { value: 1, key: 'position_1st' as const },
    { value: 2, key: 'position_2nd' as const },
    { value: 3, key: 'position_3rd' as const },
    { value: 4, key: 'position_4th' as const },
    { value: -1, key: 'position_last' as const },
];

function toggleMonthlyDay(day: number) {
    days.value = toggleSortedItem(days.value, day);
}
</script>

<template>
    <div>
        <!-- Mode Toggle -->
        <div class="mb-4 flex gap-2">
            <button
                type="button"
                class="flex-1 rounded-lg px-4 py-2 text-sm font-medium transition-colors"
                :class="
                    mode === 'day'
                        ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-400'
                        : 'bg-neutral-100 text-neutral-600 hover:bg-neutral-200 dark:bg-neutral-800 dark:text-neutral-400 dark:hover:bg-neutral-700'
                "
                @click="mode = 'day'"
            >
                {{ translations.specific_days }}
            </button>
            <button
                type="button"
                class="flex-1 rounded-lg px-4 py-2 text-sm font-medium transition-colors"
                :class="
                    mode === 'position'
                        ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-400'
                        : 'bg-neutral-100 text-neutral-600 hover:bg-neutral-200 dark:bg-neutral-800 dark:text-neutral-400 dark:hover:bg-neutral-700'
                "
                @click="mode = 'position'"
            >
                {{ translations.positional }}
            </button>
        </div>

        <!-- Specific Days Grid -->
        <div v-if="mode === 'day'">
            <label
                class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300"
            >
                {{ translations.days_of_month }}
            </label>
            <div class="grid grid-cols-7 gap-1">
                <button
                    v-for="day in 31"
                    :key="day"
                    type="button"
                    class="rounded-lg py-2 text-sm font-medium transition-colors"
                    :class="
                        days.includes(day)
                            ? 'bg-green-500 text-white'
                            : 'bg-neutral-100 text-neutral-600 hover:bg-neutral-200 dark:bg-neutral-800 dark:text-neutral-400 dark:hover:bg-neutral-700'
                    "
                    @click="toggleMonthlyDay(day)"
                >
                    {{ day }}
                </button>
            </div>
            <p class="mt-1.5 text-xs text-neutral-400 dark:text-neutral-500">
                {{ translations.days_of_month_help }}
            </p>
        </div>

        <!-- Positional Selection -->
        <div v-if="mode === 'position'" class="space-y-3">
            <!-- Position -->
            <div>
                <label
                    class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300"
                >
                    {{ translations.which_occurrence }}
                </label>
                <div class="flex flex-wrap gap-1">
                    <button
                        v-for="pos in positions"
                        :key="pos.value"
                        type="button"
                        class="rounded-lg px-4 py-2 text-sm font-medium transition-colors"
                        :class="
                            position === pos.value
                                ? 'bg-green-500 text-white'
                                : 'bg-neutral-100 text-neutral-600 hover:bg-neutral-200 dark:bg-neutral-800 dark:text-neutral-400 dark:hover:bg-neutral-700'
                        "
                        @click="position = pos.value"
                    >
                        {{ translations[pos.key] }}
                    </button>
                </div>
            </div>

            <!-- Day of Week -->
            <div>
                <label
                    class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300"
                >
                    {{ translations.day_of_week }}
                </label>
                <div class="flex gap-1">
                    <button
                        v-for="wd in WEEKDAYS"
                        :key="wd.index"
                        type="button"
                        class="flex-1 rounded-lg py-2.5 text-sm font-medium transition-colors"
                        :class="
                            weekday === wd.index
                                ? 'bg-green-500 text-white'
                                : 'bg-neutral-100 text-neutral-600 hover:bg-neutral-200 dark:bg-neutral-800 dark:text-neutral-400 dark:hover:bg-neutral-700'
                        "
                        @click="weekday = wd.index"
                    >
                        {{ translations[wd.key] }}
                    </button>
                </div>
            </div>
            <p class="mt-1.5 text-xs text-neutral-400 dark:text-neutral-500">
                {{ translations.positional_help }}
            </p>
        </div>
    </div>
</template>
