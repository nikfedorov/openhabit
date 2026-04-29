<script setup lang="ts">
import type { HabitTranslations } from '@/types/edit';

const model = defineModel<'DAILY' | 'WEEKLY' | 'MONTHLY'>({ required: true });

defineProps<{
    translations: HabitTranslations;
}>();

const options = [
    { value: 'DAILY' as const, key: 'freq_daily' as const },
    { value: 'WEEKLY' as const, key: 'freq_weekly' as const },
    { value: 'MONTHLY' as const, key: 'freq_monthly' as const },
];
</script>

<template>
    <div>
        <label
            class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300"
        >
            {{ translations.frequency }}
        </label>
        <div class="flex gap-2">
            <button
                v-for="option in options"
                :key="option.value"
                type="button"
                class="rounded-lg px-4 py-2 text-sm font-medium transition-colors"
                :class="
                    model === option.value
                        ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-400'
                        : 'bg-neutral-100 text-neutral-600 hover:bg-neutral-200 dark:bg-neutral-800 dark:text-neutral-400 dark:hover:bg-neutral-700'
                "
                @click="model = option.value"
            >
                {{ translations[option.key] }}
            </button>
        </div>
        <p class="mt-1.5 text-xs text-neutral-400 dark:text-neutral-500">
            {{ translations.frequency_help }}
        </p>
    </div>
</template>
