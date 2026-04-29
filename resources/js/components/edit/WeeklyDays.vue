<script setup lang="ts">
import { WEEKDAYS } from '@/components/edit/constants';
import type { HabitTranslations } from '@/types/edit';
import { toggleSortedItem } from '@/utils/array';

const model = defineModel<number[]>({ required: true });

defineProps<{
    translations: HabitTranslations;
}>();

function toggleDay(day: number) {
    model.value = toggleSortedItem(model.value, day);
}
</script>

<template>
    <div>
        <label
            class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300"
        >
            {{ translations.days_of_week }}
        </label>
        <div class="flex gap-1">
            <button
                v-for="day in WEEKDAYS"
                :key="day.index"
                type="button"
                class="flex-1 rounded-lg py-2.5 text-sm font-medium transition-colors"
                :class="
                    model.includes(day.index)
                        ? 'bg-green-500 text-white'
                        : 'bg-neutral-100 text-neutral-600 hover:bg-neutral-200 dark:bg-neutral-800 dark:text-neutral-400 dark:hover:bg-neutral-700'
                "
                @click="toggleDay(day.index)"
            >
                {{ translations[day.key] }}
            </button>
        </div>
        <p class="mt-1.5 text-xs text-neutral-400 dark:text-neutral-500">
            {{ translations.days_help }}
        </p>
    </div>
</template>
