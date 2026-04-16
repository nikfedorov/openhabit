<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useSwipeToDelete } from '@/composables/useSwipeToDelete';
import type { EditHabit, EditTranslations } from '@/types/edit';

const props = defineProps<{
    habit: EditHabit;
    translations: EditTranslations;
}>();

const emit = defineEmits<{
    'toggle-active': [habitId: number];
    edit: [habitId: number];
    delete: [habitId: number];
    'confirm-delete': [habitId: number];
}>();

const swipeContentEl = ref<HTMLElement | null>(null);
const deleteBtnEl = ref<HTMLElement | null>(null);

const { setup: setupSwipe, resetSwipe } = useSwipeToDelete({
    contentEl: swipeContentEl,
    deleteBtnEl,
    onDeleteSwipe: () => emit('delete', props.habit.id),
    onDeleteTap: () => emit('confirm-delete', props.habit.id),
});

defineExpose({ resetSwipe });

onMounted(() => {
    setupSwipe();
});
</script>

<template>
    <div
        class="relative overflow-hidden rounded-xl bg-neutral-100 dark:bg-neutral-800"
        :class="{ 'opacity-50': !habit.is_active }"
        data-testid="edit-habit-item"
    >
        <!-- Delete button behind (revealed on swipe) -->
        <button
            ref="deleteBtnEl"
            type="button"
            class="absolute inset-y-0 end-0 flex w-12 items-center justify-center bg-red-500 text-white opacity-0"
            style="transition: opacity 0.2s ease"
            data-testid="swipe-delete-btn"
        >
            <svg
                class="h-5 w-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"
                />
            </svg>
        </button>

        <!-- Swipeable content container -->
        <div
            ref="swipeContentEl"
            class="relative flex items-center gap-2 px-4 py-3 select-none"
        >
            <!-- Status Indicator -->
            <div class="flex-shrink-0">
                <button
                    type="button"
                    class="h-7 w-7 rounded-md align-middle"
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
                class="min-w-0 flex-1 text-start hover:opacity-75"
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
                    class="h-4 w-4 text-neutral-300 hover:text-neutral-400 dark:text-neutral-600 dark:hover:text-neutral-500"
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
