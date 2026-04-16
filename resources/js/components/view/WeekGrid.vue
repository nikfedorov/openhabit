<script setup lang="ts">
import { computed, ref } from 'vue';
import type { GridHabit, WeekDay, WeekTranslations } from '@/types/view';
import { getIntensityColor } from '@/utils/intensity';

const props = defineProps<{
    days: WeekDay[];
    habits: GridHabit[];
    translations: WeekTranslations;
}>();

const franklinExpanded = ref(
    localStorage.getItem('franklin_virtues_expanded') === 'true',
);

function toggleFranklin() {
    franklinExpanded.value = !franklinExpanded.value;
    localStorage.setItem(
        'franklin_virtues_expanded',
        String(franklinExpanded.value),
    );
}

type HabitSection = {
    key: string;
    label: string;
    allHabits: GridHabit[];
    visibleHabits: GridHabit[];
    collapsible: boolean;
};

const habitSections = computed<HabitSection[]>(() => {
    const regularHabits = props.habits.filter((h) => !h.is_franklin_virtue);
    const franklinHabits = props.habits.filter((h) => h.is_franklin_virtue);

    return [
        {
            key: 'franklin',
            label: props.translations.franklins_virtues,
            allHabits: franklinHabits,
            visibleHabits: franklinExpanded.value
                ? franklinHabits
                : franklinHabits.filter((h) => h.is_weekly_focus),
            collapsible: true,
        },
        {
            key: 'regular',
            label: props.translations.habits,
            allHabits: regularHabits,
            visibleHabits: regularHabits,
            collapsible: false,
        },
    ].filter((s) => s.allHabits.length > 0);
});

function getCellClass(day: WeekDay, habit: GridHabit): string {
    const dayData = habit.days[day.date] ?? {
        completed: false,
        partial: false,
        scheduled: true,
    };

    if (day.is_future) {
        return 'border border-dashed border-neutral-300 dark:border-neutral-600';
    }

    if (dayData.completed) {
        return getIntensityColor(4);
    }

    if (dayData.partial) {
        return getIntensityColor(1);
    }

    return 'bg-neutral-200 dark:bg-neutral-700';
}

function isNotScheduled(day: WeekDay, habit: GridHabit): boolean {
    const dayData = habit.days[day.date];
    return dayData !== undefined && !dayData.scheduled;
}

function getCellLabel(day: WeekDay, habit: GridHabit): string {
    const dayData = habit.days[day.date];
    const prefix = `${habit.name}, ${day.day_name} ${day.day_number}`;

    if (day.is_future) {
        return `${prefix}: ${props.translations.future}`;
    }
    if (dayData && !dayData.scheduled) {
        return `${prefix}: ${props.translations.missed}`;
    }
    if (dayData?.completed) {
        return `${prefix}: ${props.translations.done}`;
    }
    if (dayData?.partial) {
        return `${prefix}: ${props.translations.partial}`;
    }
    return `${prefix}: ${props.translations.missed}`;
}

function onRowEnter(el: Element) {
    const htmlEl = el as HTMLElement;
    htmlEl.style.overflow = 'hidden';
    htmlEl.style.height = '0';
    htmlEl.style.opacity = '0';
    void htmlEl.offsetHeight;
    htmlEl.style.transition = 'height 0.2s ease, opacity 0.2s ease';
    htmlEl.style.height = `${htmlEl.scrollHeight}px`;
    htmlEl.style.opacity = '1';
}

function onRowAfterEnter(el: Element) {
    const htmlEl = el as HTMLElement;
    htmlEl.style.height = '';
    htmlEl.style.overflow = '';
    htmlEl.style.transition = '';
    htmlEl.style.opacity = '';
}

function onRowLeave(el: Element) {
    const htmlEl = el as HTMLElement;
    htmlEl.style.overflow = 'hidden';
    htmlEl.style.height = `${htmlEl.scrollHeight}px`;
    htmlEl.style.opacity = '1';
    void htmlEl.offsetHeight;
    htmlEl.style.transition = 'height 0.2s ease, opacity 0.2s ease';
    htmlEl.style.height = '0';
    htmlEl.style.opacity = '0';
}

function onRowAfterLeave(el: Element) {
    const htmlEl = el as HTMLElement;
    htmlEl.style.height = '';
    htmlEl.style.overflow = '';
    htmlEl.style.transition = '';
    htmlEl.style.opacity = '';
}
</script>

<template>
    <div class="space-y-3">
        <div
            v-for="section in habitSections"
            :key="section.key"
            class="overflow-hidden rounded-xl bg-neutral-100 dark:bg-neutral-800"
        >
            <!-- Static header for regular habits -->
            <div v-if="!section.collapsible" class="px-3 py-2">
                <h3
                    class="text-xs font-medium text-neutral-500 dark:text-neutral-400"
                >
                    {{ section.label }}
                </h3>
            </div>

            <!-- Collapsible header for franklin habits -->
            <button
                v-else
                type="button"
                class="flex w-full items-center justify-between px-3 py-2 text-start"
                @click="toggleFranklin"
            >
                <div class="flex items-center gap-2">
                    <svg
                        class="h-4 w-4 text-neutral-400 transition-transform duration-150 dark:text-neutral-500"
                        :class="{
                            '-rotate-90 rtl:rotate-90': !franklinExpanded,
                        }"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="2"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                        />
                    </svg>
                    <span
                        class="text-xs font-medium text-neutral-500 dark:text-neutral-400"
                    >
                        {{ section.label }}
                    </span>
                </div>
            </button>

            <!-- Day headers -->
            <div class="grid" style="grid-template-columns: 40% repeat(7, 1fr)">
                <div class="px-3 py-1"></div>
                <div
                    v-for="day in days"
                    :key="day.date"
                    class="px-0.5 py-1 text-center"
                >
                    <div
                        class="text-[10px] font-medium"
                        :class="
                            day.is_today
                                ? 'text-green-600 dark:text-green-400'
                                : 'text-neutral-400 dark:text-neutral-500'
                        "
                    >
                        {{ day.day_name }}
                    </div>
                    <div
                        class="text-[10px]"
                        :class="
                            day.is_today
                                ? 'font-semibold text-green-600 dark:text-green-400'
                                : 'text-neutral-400 dark:text-neutral-500'
                        "
                    >
                        {{ day.day_number }}
                    </div>
                </div>
            </div>

            <!-- Habit rows -->
            <TransitionGroup
                v-bind="
                    section.collapsible
                        ? {
                              onEnter: onRowEnter,
                              onAfterEnter: onRowAfterEnter,
                              onLeave: onRowLeave,
                              onAfterLeave: onRowAfterLeave,
                          }
                        : {}
                "
            >
                <div
                    v-for="habit in section.visibleHabits"
                    :key="habit.id"
                    class="grid transition-colors hover:bg-neutral-200/50 dark:hover:bg-neutral-700/50"
                    style="grid-template-columns: 40% repeat(7, 1fr)"
                >
                    <div class="px-3 py-1.5">
                        <div
                            class="truncate text-sm font-medium text-neutral-900 dark:text-white"
                            :title="habit.name"
                        >
                            {{ habit.name }}
                        </div>
                    </div>
                    <div
                        v-for="day in days"
                        :key="day.date"
                        class="px-0.5 py-1.5 text-center"
                    >
                        <div class="flex items-center justify-center">
                            <div
                                v-if="isNotScheduled(day, habit)"
                                class="flex h-4 w-4 items-center justify-center text-xs text-neutral-300 dark:text-neutral-600"
                                :aria-label="getCellLabel(day, habit)"
                            >
                                –
                            </div>
                            <div
                                v-else
                                class="h-4 w-4 rounded-sm"
                                :class="getCellClass(day, habit)"
                                role="img"
                                :aria-label="getCellLabel(day, habit)"
                            />
                        </div>
                    </div>
                </div>
            </TransitionGroup>
        </div>

        <!-- Legend -->
        <div
            class="flex items-center justify-end gap-3 text-[10px] text-neutral-500 dark:text-neutral-400"
        >
            <div class="flex items-center gap-1">
                <div class="h-3 w-3 rounded-sm" :class="getIntensityColor(4)" />
                <span>{{ translations.done }}</span>
            </div>
            <div class="flex items-center gap-1">
                <div class="h-3 w-3 rounded-sm" :class="getIntensityColor(1)" />
                <span>{{ translations.partial }}</span>
            </div>
            <div class="flex items-center gap-1">
                <div
                    class="h-3 w-3 rounded-sm bg-neutral-200 dark:bg-neutral-700"
                />
                <span>{{ translations.missed }}</span>
            </div>
            <div class="flex items-center gap-1">
                <div
                    class="h-3 w-3 rounded-sm border border-dashed border-neutral-300 dark:border-neutral-600"
                />
                <span>{{ translations.future }}</span>
            </div>
        </div>
    </div>
</template>
