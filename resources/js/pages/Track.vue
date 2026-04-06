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

async function loadTrack(date?: string) {
    const query = date ? `?date=${date}` : '';
    data.value = await apiFetch<TrackData>(`/api/track${query}`);
}

async function onToggleHabit(habitId: number) {
    data.value = await apiFetch<TrackData>('/api/track/toggle', {
        method: 'POST',
        body: JSON.stringify({ habit_id: habitId, date: data.value!.date }),
    });
}

onMounted(() => loadTrack());
</script>

<template>
    <div v-if="data" class="min-h-screen bg-white dark:bg-neutral-900">
        <div class="mx-auto max-w-2xl px-4 py-6">
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

            <div class="space-y-1">
                <template v-if="data.habits.length > 0">
                    <HabitItem
                        v-for="habit in data.habits"
                        :key="habit.id"
                        :habit="habit"
                        @toggle="onToggleHabit"
                    />
                </template>

                <EmptyState
                    v-else
                    :title="data.translations.no_habits_scheduled"
                    :subtitle="data.translations.for_this_day"
                />
            </div>

            <DailyNote
                :date="data.date"
                :content="data.dailyNoteContent"
                :note-label="data.translations.daily_note"
                :saving-label="data.translations.saving"
                :placeholder="data.translations.how_was_your_day"
            />

            <ActivityGraph
                :activity-data="data.activityData"
                :activity-label="data.translations.activity"
                :period-label="data.translations.last_n_days"
                :less-label="data.translations.less"
                :more-label="data.translations.more"
            />
        </div>
    </div>
</template>
