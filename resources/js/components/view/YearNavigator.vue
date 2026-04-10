<script setup lang="ts">
import { computed } from 'vue';
import type { ViewTranslations } from '@/types/view';

const props = defineProps<{
    selectedYear: number | null;
    currentAge: number | null;
    birthdate: string | null;
    translations: ViewTranslations;
}>();

const emit = defineEmits<{
    selectYear: [year: number];
}>();

const displayYear = computed(() => props.selectedYear ?? props.currentAge ?? 0);
const isCurrentYear = computed(() => displayYear.value === props.currentAge);

const yearRangeText = computed(() => {
    /* v8 ignore next */
    if (!props.birthdate) return '';
    const birth = new Date(props.birthdate);
    const yearStart = new Date(
        birth.getFullYear() + displayYear.value,
        birth.getMonth(),
        birth.getDate(),
    );
    const yearEnd = new Date(
        birth.getFullYear() + displayYear.value + 1,
        birth.getMonth(),
        birth.getDate() - 1,
    );
    const startYear = yearStart.getFullYear();
    const endYear = yearEnd.getFullYear();
    return startYear === endYear
        ? String(startYear)
        : `${startYear}–${endYear}`;
});

function ageText(): string {
    return props.translations.age.replace(':age', String(displayYear.value));
}
</script>

<template>
    <div v-if="birthdate">
        <div
            class="flex items-center justify-between gap-4 rounded-xl bg-neutral-100 px-4 py-3 dark:bg-neutral-800"
        >
            <!-- Previous Year Button -->
            <button
                v-if="displayYear > 0"
                type="button"
                class="flex h-10 w-10 items-center justify-center rounded-lg text-neutral-500 transition-all duration-150 hover:bg-neutral-200 hover:text-neutral-700 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-200"
                :title="translations.previous_year"
                @click="emit('selectYear', displayYear - 1)"
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
            <div v-else class="h-10 w-10" />

            <!-- Year Display -->
            <div class="relative flex-1 overflow-hidden text-center">
                <Transition name="nav-slide">
                    <div :key="displayYear">
                        <div
                            class="text-lg font-semibold text-neutral-900 dark:text-white"
                        >
                            <template v-if="isCurrentYear">
                                {{ translations.this_year }}
                            </template>
                            <template v-else>
                                {{ ageText() }}
                            </template>
                        </div>
                        <div
                            class="text-sm text-neutral-500 dark:text-neutral-400"
                        >
                            {{ yearRangeText }}
                        </div>
                    </div>
                </Transition>
            </div>

            <!-- Next Year Button -->
            <button
                v-if="displayYear < 79"
                type="button"
                class="flex h-10 w-10 items-center justify-center rounded-lg text-neutral-500 transition-all duration-150 hover:bg-neutral-200 hover:text-neutral-700 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-200"
                :title="translations.next_year"
                @click="emit('selectYear', displayYear + 1)"
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
            <div v-else class="h-10 w-10" />
        </div>

        <!-- Current Year Button -->
        <Transition name="current-nav-btn">
            <div v-if="!isCurrentYear" class="mt-2 flex justify-center">
                <button
                    type="button"
                    class="inline-flex items-center gap-1.5 rounded-full bg-neutral-100 px-3 py-1.5 text-xs font-medium text-neutral-600 transition-all duration-150 hover:bg-neutral-200 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                    @click="emit('selectYear', currentAge ?? 0)"
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
                    {{ translations.current_year }}
                </button>
            </div>
        </Transition>
    </div>
</template>
