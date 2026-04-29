<script setup lang="ts">
import { getIntensityColor } from '@/utils/intensity';

defineProps<{
    intensity: number;
    isLived?: boolean;
    isCurrent?: boolean;
    isFuture?: boolean;
    isClickable?: boolean;
}>();
</script>

<template>
    <div
        class="rounded-sm"
        :class="[
            isFuture
                ? 'border border-dashed border-neutral-300 dark:border-neutral-600'
                : getIntensityColor(isLived || isCurrent ? intensity : 0),
            isClickable
                ? 'cursor-pointer transition-transform hover:scale-125'
                : '',
        ]"
    >
        <slot />
    </div>
</template>
