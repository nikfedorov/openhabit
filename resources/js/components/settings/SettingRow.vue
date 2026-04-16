<script setup lang="ts">
defineProps<{
    title: string;
    subtitle: string;
}>();
</script>

<template>
    <div class="flex items-center justify-between gap-3">
        <div class="flex min-w-0 items-center gap-3">
            <div
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-neutral-200 text-neutral-500 dark:bg-neutral-700 dark:text-neutral-400"
            >
                <slot name="icon" />
            </div>
            <div class="min-w-0">
                <p class="text-sm font-medium text-neutral-900 dark:text-white">
                    {{ title }}
                </p>
                <p class="text-xs text-neutral-500 dark:text-neutral-400">
                    {{ subtitle }}
                </p>
            </div>
        </div>
        <slot />
    </div>
</template>
