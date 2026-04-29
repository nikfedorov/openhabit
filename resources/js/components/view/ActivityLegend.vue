<script setup lang="ts">
import { getIntensityColor } from '@/utils/intensity';

defineProps<{
    lessLabel: string;
    moreLabel: string;
    futureLabel?: string;
    showFuture?: boolean;
}>();
</script>

<template>
    <div class="flex items-center justify-end gap-1">
        <span class="me-1 text-[10px] text-neutral-500 dark:text-neutral-400">
            {{ lessLabel }}
        </span>
        <div
            v-for="level in [0, 1, 2, 3, 4]"
            :key="level"
            class="h-3 w-3 rounded-sm"
            :class="getIntensityColor(level)"
        />
        <span class="ms-1 text-[10px] text-neutral-500 dark:text-neutral-400">
            {{ moreLabel }}
        </span>
        <template v-if="showFuture !== false && futureLabel">
            <span
                class="ms-2 text-[10px] text-neutral-400 dark:text-neutral-500"
            >
                |
            </span>
            <div
                class="ms-1 h-3 w-3 rounded-sm border border-dashed border-neutral-300 dark:border-neutral-600"
            />
            <span class="text-[10px] text-neutral-500 dark:text-neutral-400">
                {{ futureLabel }}
            </span>
        </template>
    </div>
</template>
