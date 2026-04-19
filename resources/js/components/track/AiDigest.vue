<script setup lang="ts">
import { ref } from 'vue';

defineProps<{
    content: string;
    label: string;
}>();

const isOpen = ref(true);
</script>

<template>
    <div class="mt-4 rounded-xl bg-neutral-100 px-4 py-4 dark:bg-neutral-800">
        <button
            type="button"
            class="flex w-full cursor-pointer items-center justify-between"
            @click="isOpen = !isOpen"
        >
            <span
                class="flex items-center gap-1.5 text-sm font-medium text-neutral-600 dark:text-neutral-400"
            >
                <svg
                    class="h-4 w-4 shrink-0 text-neutral-500 dark:text-neutral-400"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 00-2.455 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z"
                    />
                </svg>
                {{ label }}
            </span>
            <svg
                class="h-4 w-4 text-neutral-400 transition-transform duration-200"
                :class="{ 'rotate-180': isOpen }"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                />
            </svg>
        </button>
        <Transition
            enter-active-class="transition-all duration-200 ease-out overflow-hidden"
            enter-from-class="opacity-0 max-h-0"
            enter-to-class="opacity-100 max-h-screen"
            leave-active-class="transition-all duration-200 ease-in overflow-hidden"
            leave-from-class="opacity-100 max-h-screen"
            leave-to-class="opacity-0 max-h-0"
        >
            <p
                v-if="isOpen"
                class="mt-3 text-sm leading-relaxed whitespace-pre-line text-neutral-700 dark:text-neutral-300"
            >
                {{ content }}
            </p>
        </Transition>
    </div>
</template>
