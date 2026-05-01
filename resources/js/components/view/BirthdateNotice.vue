<script setup lang="ts">
defineProps<{
    title: string;
    description: string;
}>();
</script>

<template>
    <div
        class="mt-4 flex flex-col items-center justify-center px-8 py-16 text-center"
    >
        <div
            class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-neutral-100 dark:bg-neutral-800"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="h-8 w-8 text-neutral-400 dark:text-neutral-500"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"
                />
            </svg>
        </div>
        <h3 class="mb-1 text-lg font-semibold text-neutral-900 dark:text-white">
            {{ title }}
        </h3>
        <p class="max-w-xs text-sm text-neutral-500 dark:text-neutral-400">
            {{ description }}
        </p>
    </div>
</template>
