<script setup lang="ts">
import type { WeekTranslations } from '@/types/view';
import { addDays } from '@/utils/date';

const props = defineProps<{
    weekStart: string;
    weekStartFormatted: string;
    weekEndFormatted: string;
    weekEndFormattedFull: string;
    weekYear: string;
    isCurrentWeek: boolean;
    translations: WeekTranslations;
}>();

const emit = defineEmits<{
    selectWeek: [weekStart: string | undefined];
}>();
</script>

<template>
    <div>
        <div
            class="flex items-center justify-between gap-4 rounded-xl bg-neutral-100 px-4 py-3 dark:bg-neutral-800"
        >
            <!-- Previous Week Button -->
            <button
                type="button"
                class="flex h-10 w-10 items-center justify-center rounded-lg text-neutral-500 transition-all duration-150 hover:bg-neutral-200 hover:text-neutral-700 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-200"
                :title="translations.previous_week"
                @click="emit('selectWeek', addDays(props.weekStart, -7))"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 rtl:-scale-x-100"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M15 19l-7-7 7-7"
                    />
                </svg>
            </button>

            <!-- Week Display -->
            <div class="relative flex-1 overflow-hidden text-center">
                <Transition name="nav-slide">
                    <div :key="weekStartFormatted">
                        <div
                            class="text-lg font-semibold text-neutral-900 dark:text-white"
                        >
                            <template v-if="isCurrentWeek">
                                {{ translations.this_week }}
                            </template>
                            <template v-else>
                                {{ weekStartFormatted }} –
                                {{ weekEndFormatted }}
                            </template>
                        </div>
                        <div
                            class="text-sm text-neutral-500 dark:text-neutral-400"
                        >
                            <template v-if="isCurrentWeek">
                                {{ weekStartFormatted }} –
                                {{ weekEndFormattedFull }}
                            </template>
                            <template v-else>
                                {{ weekYear }}
                            </template>
                        </div>
                    </div>
                </Transition>
            </div>

            <!-- Next Week Button -->
            <button
                type="button"
                class="flex h-10 w-10 items-center justify-center rounded-lg text-neutral-500 transition-all duration-150 hover:bg-neutral-200 hover:text-neutral-700 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-200"
                :title="translations.next_week"
                @click="emit('selectWeek', addDays(props.weekStart, 7))"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 rtl:-scale-x-100"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M9 5l7 7-7 7"
                    />
                </svg>
            </button>
        </div>

        <!-- Current Week Button -->
        <Transition name="collapse-btn">
            <div v-if="!isCurrentWeek" class="mt-2 flex justify-center">
                <button
                    type="button"
                    class="inline-flex items-center gap-1.5 rounded-full bg-neutral-100 px-3 py-1.5 text-xs font-medium text-neutral-600 transition-all duration-150 hover:bg-neutral-200 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                    @click="emit('selectWeek', undefined)"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-3.5 w-3.5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                        />
                    </svg>
                    {{ translations.current_week }}
                </button>
            </div>
        </Transition>
    </div>
</template>
