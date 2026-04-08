<script setup lang="ts">
import { onMounted, ref } from 'vue';
import ActivityGraph from '@/components/track/ActivityGraph.vue';
import DailyNote from '@/components/track/DailyNote.vue';
import DateNavigator from '@/components/track/DateNavigator.vue';
import EmptyState from '@/components/track/EmptyState.vue';
import HabitItem from '@/components/track/HabitItem.vue';
import ProgressBar from '@/components/track/ProgressBar.vue';
import type { ActivityDay, Habit } from '@/types';
import type { TrackTranslations } from '@/types/translations';
import { apiFetch } from '@/utils/api';

interface TrackData {
    date: string;
    dayName: string;
    dateFormatted: string;
    isToday: boolean;
    habits: Habit[];
    totalHabits: number;
    completedCount: number;
    moveCompletedToEnd: boolean;
    dailyNoteContent: string;
    activityData: ActivityDay[];
    translations: TrackTranslations;
}

const data = ref<TrackData | null>(null);
const navDirection = ref<'nav-forward' | 'nav-backward' | null>(null);

// Optimistic update state:
// - pendingHabitIds: reactive set driving the shimmer UI on pending habits
// - inflightCount: how many API requests are in flight per habit
// - rollbackSnapshots: data state before the first toggle per habit (for error recovery)
const pendingHabitIds = ref(new Set<number>());
const inflightCount = new Map<number, number>();
const rollbackSnapshots = new Map<number, TrackData>();

async function loadTrack(date?: string) {
    if (date && data.value) {
        navDirection.value =
            date > data.value.date ? 'nav-forward' : 'nav-backward';
    }

    const query = date ? `?date=${date}` : '';
    data.value = await apiFetch<TrackData>(`/api/track${query}`);
}

function sortHabits(habits: Habit[], moveCompletedToEnd: boolean): Habit[] {
    if (!moveCompletedToEnd) {
        return habits;
    }

    const byOrder = (a: Habit, b: Habit) => a.sort_order - b.sort_order;
    const uncompleted = habits.filter((h) => !h.is_completed).sort(byOrder);
    const completed = habits.filter((h) => h.is_completed).sort(byOrder);

    return [...uncompleted, ...completed];
}

function applyOptimisticToggle(habit: Habit, trackData: TrackData): void {
    if (habit.is_completed) {
        habit.is_completed = false;
        habit.current_iteration = Math.max(0, habit.current_iteration - 1);
        trackData.completedCount = Math.max(0, trackData.completedCount - 1);
    } else {
        habit.current_iteration = Math.min(
            habit.iterations_required,
            habit.current_iteration + 1,
        );
        habit.is_completed =
            habit.current_iteration >= habit.iterations_required;
        if (habit.is_completed) {
            trackData.completedCount++;
        }
    }

    trackData.habits = sortHabits(
        trackData.habits,
        trackData.moveCompletedToEnd,
    );
}

function startInflight(habitId: number, snapshot: TrackData): void {
    if (!inflightCount.has(habitId)) {
        rollbackSnapshots.set(habitId, snapshot);
    }
    inflightCount.set(habitId, (inflightCount.get(habitId) ?? 0) + 1);
    pendingHabitIds.value.add(habitId);
}

/**
 * Decrements the in-flight counter for a habit.
 * Returns true when no habits have pending requests anymore.
 */
function resolveInflight(habitId: number): boolean {
    const count = inflightCount.get(habitId)!;
    if (count <= 1) {
        inflightCount.delete(habitId);
        pendingHabitIds.value.delete(habitId);
    } else {
        inflightCount.set(habitId, count - 1);
    }

    return inflightCount.size === 0;
}

async function onToggleHabit(habitId: number) {
    const currentData = data.value;
    const habit = currentData?.habits.find((h) => h.id === habitId);
    if (!currentData || !habit) {
        return;
    }

    const snapshot = JSON.parse(JSON.stringify(currentData)) as TrackData;
    applyOptimisticToggle(habit, currentData);
    startInflight(habitId, snapshot);

    try {
        const response = await apiFetch<TrackData>('/api/track/toggle', {
            method: 'POST',
            body: JSON.stringify({ habit_id: habitId, date: snapshot.date }),
        });

        const allSettled = resolveInflight(habitId);

        if (!inflightCount.has(habitId)) {
            rollbackSnapshots.delete(habitId);
        }
        if (allSettled) {
            data.value = response;
        }
    } catch {
        const allSettled = resolveInflight(habitId);

        if (allSettled) {
            data.value = rollbackSnapshots.get(habitId) as TrackData;
            rollbackSnapshots.delete(habitId);
        }
    }
}

onMounted(() => loadTrack());
</script>

<template>
    <div v-if="data" class="min-h-screen bg-white dark:bg-neutral-900">
        <div class="mx-auto max-w-2xl px-4 py-6" :class="navDirection">
            <DateNavigator
                :date="data.date"
                :day-name="data.dayName"
                :date-formatted="data.dateFormatted"
                :is-today="data.isToday"
                :previous-day-label="data.translations.previous_day"
                :next-day-label="data.translations.next_day"
                :today-label="data.translations.today"
                @navigate="loadTrack"
            />

            <ProgressBar
                v-if="data.totalHabits > 0"
                :total-habits="data.totalHabits"
                :completed-count="data.completedCount"
                :progress-label="data.translations.progress"
                :all-done-label="data.translations.all_done"
            />

            <TransitionGroup name="habit-list" tag="div">
                <HabitItem
                    v-for="habit in data.habits"
                    :key="habit.id"
                    :habit="habit"
                    :pending="pendingHabitIds.has(habit.id)"
                    @toggle="onToggleHabit"
                />

                <EmptyState
                    v-if="data.habits.length === 0"
                    key="empty-state"
                    :title="data.translations.no_habits_scheduled"
                    :subtitle="data.translations.for_this_day"
                />

                <DailyNote
                    key="daily-note"
                    :date="data.date"
                    :content="data.dailyNoteContent"
                    :note-label="data.translations.daily_note"
                    :saving-label="data.translations.saving"
                    :placeholder="data.translations.how_was_your_day"
                />

                <ActivityGraph
                    key="activity-graph"
                    :activity-data="data.activityData"
                    :activity-label="data.translations.activity"
                    :period-label="data.translations.last_n_days"
                    :less-label="data.translations.less"
                    :more-label="data.translations.more"
                />
            </TransitionGroup>
        </div>
    </div>
</template>

<style>
.habit-list-move {
    transition: transform 0.3s ease-in-out;
}

.habit-list-enter-active,
.habit-list-leave-active {
    transition: all 0.3s ease-in-out;
}

.habit-list-leave-active {
    position: absolute;
}

.habit-list-enter-from,
.habit-list-leave-to {
    opacity: 0;
}

/* Forward navigation: leave to left, enter from right */
.nav-forward .habit-list-enter-from {
    transform: translateX(30px);
}

.nav-forward .habit-list-leave-to {
    transform: translateX(-30px);
}

/* Backward navigation: leave to right, enter from left */
.nav-backward .habit-list-enter-from {
    transform: translateX(-30px);
}

.nav-backward .habit-list-leave-to {
    transform: translateX(30px);
}
</style>
