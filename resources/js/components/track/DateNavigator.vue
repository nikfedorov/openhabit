<script setup lang="ts">
import { addDays, todayStr } from '@/utils/date';

const props = defineProps<{
    date: string;
    dayName: string;
    dateFormatted: string;
    isToday: boolean;
    previousDayLabel: string;
    nextDayLabel: string;
    todayLabel: string;
}>();

const emit = defineEmits<{
    navigate: [date: string];
}>();

function previousDay() {
    emit('navigate', addDays(props.date, -1));
}

function nextDay() {
    const next = addDays(props.date, 1);
    if (next > todayStr()) return;
    emit('navigate', next);
}

function goToToday() {
    emit('navigate', todayStr());
}
</script>

<template>
    <div class="mb-6">
        <div
            class="flex items-center justify-between gap-4 rounded-xl bg-neutral-100 px-4 py-3 dark:bg-neutral-800"
            data-testid="date-navigator"
        >
            <button
                type="button"
                class="flex h-10 w-10 items-center justify-center rounded-lg text-neutral-500 transition-all duration-150 hover:bg-neutral-200 hover:text-neutral-700 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-200"
                data-testid="prev-day-btn"
                :title="previousDayLabel"
                @click="previousDay"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
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

            <div class="flex-1 text-center">
                <div
                    class="text-lg font-semibold text-neutral-900 dark:text-white"
                >
                    {{ dayName }}
                </div>
                <div class="text-sm text-neutral-500 dark:text-neutral-400">
                    {{ dateFormatted }}
                </div>
            </div>

            <template v-if="isToday">
                <div class="h-10 w-10" />
            </template>
            <template v-else>
                <button
                    type="button"
                    class="flex h-10 w-10 items-center justify-center rounded-lg text-neutral-500 transition-all duration-150 hover:bg-neutral-200 hover:text-neutral-700 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-200"
                    data-testid="next-day-btn"
                    :title="nextDayLabel"
                    @click="nextDay"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
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
            </template>
        </div>

        <div v-if="!isToday" class="mt-2 flex justify-center">
            <button
                type="button"
                class="inline-flex items-center gap-1.5 rounded-full bg-neutral-100 px-3 py-1.5 text-xs font-medium text-neutral-600 transition-all duration-150 hover:bg-neutral-200 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                data-testid="today-btn"
                @click="goToToday"
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
                {{ todayLabel }}
            </button>
        </div>
    </div>
</template>
