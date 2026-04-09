<script setup lang="ts">
import type { LifeStats, ViewTranslations } from '@/types/view';

defineProps<{
    lifeStats: LifeStats;
    translations: ViewTranslations;
}>();
</script>

<template>
    <div class="rounded-xl bg-neutral-100 px-4 py-3 dark:bg-neutral-800">
        <div
            class="text-center text-base font-semibold text-neutral-900 dark:text-white"
        >
            {{ translations.memento_mori }}
        </div>
        <div class="mt-2 flex items-baseline">
            <div class="flex-1 text-center">
                <div class="text-lg font-bold text-neutral-900 dark:text-white">
                    {{ lifeStats.currentAge }}
                </div>
                <div class="text-xs text-neutral-500 dark:text-neutral-400">
                    {{ translations.years_old }}
                </div>
            </div>
            <span class="shrink-0 text-neutral-300 dark:text-neutral-600">
                •
            </span>
            <div class="flex-1 text-center">
                <div class="text-lg font-bold text-neutral-900 dark:text-white">
                    {{ lifeStats.yearsRemaining }}
                </div>
                <div class="text-xs text-neutral-500 dark:text-neutral-400">
                    {{ translations.years_left }}
                </div>
            </div>
            <span class="shrink-0 text-neutral-300 dark:text-neutral-600">
                •
            </span>
            <div class="flex-1 text-center">
                <div class="text-lg font-bold text-neutral-900 dark:text-white">
                    {{ lifeStats.weeksLived.toLocaleString() }}
                </div>
                <div class="text-xs text-neutral-500 dark:text-neutral-400">
                    {{ translations.weeks_lived }}
                </div>
            </div>
        </div>
    </div>
</template>
