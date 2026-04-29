<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import PageLoader from '@/components/PageLoader.vue';
import ActivityGraph from '@/components/track/ActivityGraph.vue';
import AiDigest from '@/components/track/AiDigest.vue';
import DailyNote from '@/components/track/DailyNote.vue';
import DateNavigator from '@/components/track/DateNavigator.vue';
import EmptyState from '@/components/track/EmptyState.vue';
import HabitItem from '@/components/track/HabitItem.vue';
import ProgressBar from '@/components/track/ProgressBar.vue';
import { useHabitToggle } from '@/composables/useHabitToggle';
import type { TrackApiResponse, UserSettings } from '@/types/api';
import type { NavigationTranslations } from '@/types/navigation';
import type { TrackData } from '@/types/track';
import { apiFetch } from '@/utils/api';

const route = useRoute();
const router = useRouter();

const emit = defineEmits<{
    'navigation-translations': [translations: NavigationTranslations];
    settings: [settings: UserSettings];
    ready: [];
}>();

const data = ref<TrackData | null>(null);
const settings = ref<UserSettings>({
    locale: 'en',
    theme: 'system',
    moveCompletedToEnd: false,
});
const navDirection = ref<'nav-forward' | 'nav-backward' | null>(null);
const { pendingHabitIds, toggle: onToggleHabit } = useHabitToggle(
    data,
    settings,
);

async function loadTrack(date?: string) {
    if (date && data.value) {
        navDirection.value =
            date > data.value.date ? 'nav-forward' : 'nav-backward';
    }

    const query = date ? `?date=${date}` : '';
    const response = await apiFetch<TrackApiResponse>(`/api/track${query}`);
    data.value = {
        ...response.data,
        habits: response.habits,
        activityData: response.activityData,
    };
    settings.value = response.settings;
    emit('navigation-translations', response.navigationTranslations);
    emit('settings', response.settings);
    emit('ready');

    router.replace({
        query: data.value.isToday ? {} : { date: data.value.date },
    });
}

onMounted(() => {
    const initialDate =
        typeof route.query.date === 'string' ? route.query.date : undefined;
    loadTrack(initialDate);
});
</script>

<template>
    <div v-if="data" :class="navDirection">
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

            <div key="add-habit-btn" class="flex gap-3 px-4 py-2">
                <div class="flex h-5 w-[18px] flex-shrink-0 items-center"></div>
                <button
                    type="button"
                    class="text-sm text-neutral-400 transition-colors hover:text-neutral-600 dark:text-neutral-500 dark:hover:text-neutral-300"
                    @click="router.push({ name: 'edit.create' })"
                >
                    {{ data.translations.add_habit }}
                </button>
            </div>

            <DailyNote
                key="daily-note"
                :date="data.date"
                :content="data.dailyNoteContent"
                :note-label="data.translations.daily_note"
                :saving-label="data.translations.saving"
                :save-label="data.translations.save_note"
                :placeholder="data.translations.how_was_your_day"
            />
            <AiDigest
                v-if="data.aiDigest?.content"
                key="ai-digest"
                :content="data.aiDigest.content"
                :label="data.translations.ai_digest"
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

.habit-list-enter-from {
    transform: translateX(var(--slide-enter));
}

.habit-list-leave-to {
    transform: translateX(var(--slide-leave));
}
</style>
