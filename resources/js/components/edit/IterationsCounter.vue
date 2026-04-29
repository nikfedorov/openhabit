<script setup lang="ts">
import type { HabitTranslations } from '@/types/edit';

const model = defineModel<number>({ required: true });

defineProps<{
    translations: HabitTranslations;
}>();

function decrement() {
    if (model.value > 1) {
        model.value--;
    }
}

function increment() {
    if (model.value < 99) {
        model.value++;
    }
}
</script>

<template>
    <div>
        <label
            for="habit-iterations"
            class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300"
        >
            {{ translations.times_per_day }}
        </label>
        <div class="flex items-center gap-3">
            <button
                type="button"
                class="flex h-10 w-10 items-center justify-center rounded-lg bg-neutral-100 text-neutral-600 transition-colors hover:bg-neutral-200 dark:bg-neutral-800 dark:text-neutral-400 dark:hover:bg-neutral-700"
                @click="decrement"
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
                        d="M19.5 12h-15"
                    />
                </svg>
            </button>
            <input
                id="habit-iterations"
                v-model.number="model"
                type="number"
                min="1"
                max="99"
                class="w-16 rounded-xl border-0 bg-neutral-100 px-3 py-2 text-center text-sm font-medium text-neutral-900 transition-colors focus:bg-white focus:ring-2 focus:ring-green-500 dark:bg-neutral-800 dark:text-white dark:focus:bg-neutral-700"
            />
            <button
                type="button"
                class="flex h-10 w-10 items-center justify-center rounded-lg bg-neutral-100 text-neutral-600 transition-colors hover:bg-neutral-200 dark:bg-neutral-800 dark:text-neutral-400 dark:hover:bg-neutral-700"
                @click="increment"
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
                        d="M12 4.5v15m7.5-7.5h-15"
                    />
                </svg>
            </button>
            <span class="text-sm text-neutral-500 dark:text-neutral-400">
                {{ model === 1 ? translations.time : translations.times }}
            </span>
        </div>
        <p class="mt-1.5 text-xs text-neutral-400 dark:text-neutral-500">
            {{ translations.times_per_day_help }}
        </p>
    </div>
</template>
