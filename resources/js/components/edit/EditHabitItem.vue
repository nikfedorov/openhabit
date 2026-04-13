<script setup lang="ts">
import type { EditHabit, EditTranslations } from '@/types/edit';

defineProps<{
    habit: EditHabit;
    translations: EditTranslations;
}>();

const emit = defineEmits<{
    'toggle-active': [habitId: number];
    edit: [habitId: number];
    delete: [habitId: number];
}>();
</script>

<template>
    <div
        class="relative overflow-hidden rounded-xl"
        :class="{ 'opacity-50': !habit.is_active }"
    >
        <div
            class="relative flex items-center gap-2 bg-neutral-100 px-4 py-3 dark:bg-neutral-800"
        >
            <!-- Drag Handle -->
            <div
                class="drag-handle flex-shrink-0 cursor-grab touch-none active:cursor-grabbing"
            >
                <svg
                    class="h-5 w-5 text-neutral-300 transition-colors duration-150 dark:text-neutral-600"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"
                    />
                </svg>
            </div>

            <!-- Status Indicator -->
            <div class="flex-shrink-0">
                <button
                    type="button"
                    class="h-7 w-7 rounded-md align-middle transition-colors"
                    :class="
                        habit.is_active
                            ? 'bg-green-500 dark:bg-green-500'
                            : 'bg-neutral-300 dark:bg-neutral-600'
                    "
                    :title="
                        habit.is_active
                            ? translations.active
                            : translations.paused
                    "
                    @click="emit('toggle-active', habit.id)"
                ></button>
            </div>

            <!-- Content -->
            <button
                type="button"
                class="min-w-0 flex-1 text-start transition-opacity hover:opacity-75"
                @click="emit('edit', habit.id)"
            >
                <span
                    class="block truncate text-sm font-medium text-neutral-900 dark:text-white"
                >
                    {{ habit.name }}
                </span>
                <p
                    class="mt-0.5 truncate text-xs text-neutral-400 dark:text-neutral-500"
                >
                    {{ habit.human_readable }}
                </p>
            </button>

            <!-- Meta Info -->
            <div
                class="flex flex-shrink-0 items-center gap-3 text-xs text-neutral-400 dark:text-neutral-500"
            >
                <!-- Iterations -->
                <span
                    v-if="habit.iterations_required > 1"
                    class="flex items-center gap-1"
                    :title="`${habit.iterations_required.toString()} times per day`"
                >
                    <svg
                        class="h-3.5 w-3.5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"
                        />
                    </svg>
                    {{ habit.iterations_required }}×
                </span>

                <!-- Chevron -->
                <button
                    type="button"
                    class="h-4 w-4 text-neutral-300 transition-colors hover:text-neutral-400 dark:text-neutral-600 dark:hover:text-neutral-500"
                    @click="emit('edit', habit.id)"
                >
                    <svg
                        class="h-4 w-4 rtl:rotate-180"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M8.25 4.5l7.5 7.5-7.5 7.5"
                        />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>
