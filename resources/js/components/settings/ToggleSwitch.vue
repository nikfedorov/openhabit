<script setup lang="ts">
const model = defineModel<boolean>({ required: true });
</script>

<template>
    <button
        type="button"
        role="switch"
        :aria-checked="model"
        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer items-center rounded-full transition-colors duration-200 focus:outline-none"
        :class="model ? 'bg-green-500' : 'bg-neutral-300 dark:bg-neutral-600'"
        @click="model = !model"
    >
        <span
            class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform duration-200"
            :class="
                model
                    ? 'ltr:translate-x-6 rtl:-translate-x-6'
                    : 'ltr:translate-x-1 rtl:-translate-x-1'
            "
        />
    </button>
</template>
