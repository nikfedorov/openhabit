<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { VueDraggable } from 'vue-draggable-plus';
import { useRouter } from 'vue-router';
import EditHabitItem from '@/components/edit/EditHabitItem.vue';
import FranklinSection from '@/components/edit/FranklinSection.vue';
import TemplateSection from '@/components/edit/TemplateSection.vue';
import PageLoader from '@/components/PageLoader.vue';
import type { EditApiResponse, UserSettings } from '@/types/api';
import type {
    EditHabit,
    EditTranslations,
    HabitTranslations,
    TemplateHabit,
} from '@/types/edit';
import type { NavigationTranslations } from '@/types/navigation';
import { apiFetch } from '@/utils/api';

const router = useRouter();

const emit = defineEmits<{
    'navigation-translations': [translations: NavigationTranslations];
    settings: [settings: UserSettings];
    ready: [];
}>();

const habits = ref<EditHabit[]>([]);
const franklinHabits = ref<EditHabit[]>([]);
const templateHabits = ref<TemplateHabit[]>([]);
const translations = ref<EditTranslations | null>(null);
const habitTranslations = ref<HabitTranslations | null>(null);
const loading = ref(true);
const pendingHabitIds = ref(new Set<number>());
const newHabitIds = ref(new Set<number>());

/** Split all habits into regular and Franklin virtue lists. */
function updateHabitLists(allHabits: EditHabit[]) {
    habits.value = allHabits.filter((h) => !h.is_franklin_virtue);
    franklinHabits.value = allHabits.filter((h) => h.is_franklin_virtue);
}

async function loadData() {
    const response = await apiFetch<EditApiResponse>('/api/edit');
    updateHabitLists(response.data);
    templateHabits.value = response.templates;
    translations.value = response.translations;
    habitTranslations.value = response.habitTranslations;
    emit('navigation-translations', response.navigationTranslations);
    emit('settings', response.settings);
    loading.value = false;
    emit('ready');
}

async function toggleHabit(habitId: number) {
    const habit =
        habits.value.find((h) => h.id === habitId) ??
        franklinHabits.value.find((h) => h.id === habitId);
    if (habit) {
        habit.is_active = !habit.is_active;
    }

    await apiFetch(
        `/api/edit/habits/${habitId.toString()}/toggle`,
        {
            method: 'POST',
        },
        { silent: true },
    );
}

async function toggleFranklinHabits() {
    const anyActive = franklinHabits.value.some((h) => h.is_active);
    franklinHabits.value.forEach((h) => {
        h.is_active = !anyActive;
    });

    await apiFetch(
        '/api/edit/toggle-franklin',
        { method: 'POST' },
        { silent: true },
    );
}

async function deleteHabit(habitId: number) {
    habits.value = habits.value.filter((h) => h.id !== habitId);
    await apiFetch(
        `/api/edit/habits/${habitId.toString()}`,
        {
            method: 'DELETE',
        },
        { silent: true },
    );
}

async function saveOrder() {
    const orderedIds = habits.value.map((h) => h.id);
    await apiFetch(
        '/api/edit/habits/reorder',
        {
            method: 'POST',
            body: JSON.stringify({ ordered_ids: orderedIds }),
        },
        { silent: true },
    );
}

function navigateToCreate() {
    router.push({ name: 'edit.create' });
}

function navigateToEdit(habitId: number) {
    router.push({ name: 'edit.habit', params: { id: habitId.toString() } });
}

let nextOptimisticId = -1;

async function copyFromTemplate(templateId: number) {
    const template = templateHabits.value.find((t) => t.id === templateId);
    if (!template) return;

    const existingIds = new Set(habits.value.map((h) => h.id));

    // Insert optimistic placeholder while the API responds
    const optimisticId = nextOptimisticId--;
    const optimisticHabit: EditHabit = {
        id: optimisticId,
        name: template.name,
        description: null,
        is_active: true,
        sort_order: template.sort_order,
        iterations_required: template.iterations_required,
        human_readable: template.human_readable,
        is_franklin_virtue: false,
    };

    const insertIndex = habits.value.findIndex(
        (h) => h.sort_order > template.sort_order,
    );
    if (insertIndex === -1) {
        habits.value.push(optimisticHabit);
    } else {
        habits.value.splice(insertIndex, 0, optimisticHabit);
    }
    pendingHabitIds.value.add(optimisticId);

    // Replace optimistic data with the server response
    const response = await apiFetch<{ data: EditHabit[] }>(
        `/api/edit/templates/${templateId.toString()}/copy`,
        { method: 'POST' },
    );

    pendingHabitIds.value.delete(optimisticId);
    updateHabitLists(response.data);

    // Briefly highlight newly added habits
    for (const habit of habits.value) {
        if (!existingIds.has(habit.id)) {
            newHabitIds.value.add(habit.id);
            setTimeout(() => newHabitIds.value.delete(habit.id), 5000);
        }
    }
}

onMounted(() => {
    loadData();
});
</script>

<template>
    <div v-if="!loading && translations">
        <!-- Page Header -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">
                    {{ translations.habits }}
                </h1>
                <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                    {{ translations.manage_routines }}
                </p>
            </div>
            <button
                type="button"
                class="flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white transition-colors duration-150 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600"
                @click="navigateToCreate"
            >
                <svg
                    class="h-4 w-4"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 4.5v15m7.5-7.5h-15"
                    />
                </svg>
                <span>{{ translations.new_habit }}</span>
            </button>
        </div>

        <!-- Empty State -->
        <div
            v-if="habits.length === 0 && franklinHabits.length === 0"
            class="flex flex-col items-center justify-center py-16 text-center"
        >
            <svg
                class="mb-4 h-12 w-12 text-neutral-300 dark:text-neutral-600"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="1.5"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
            </svg>
            <h2 class="text-sm font-medium text-neutral-900 dark:text-white">
                {{ translations.no_habits_yet }}
            </h2>
            <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">
                {{ translations.create_first_habit }}
            </p>
            <button
                type="button"
                class="mt-4 text-sm font-medium text-green-600 transition-colors hover:text-green-700 dark:text-green-400 dark:hover:text-green-300"
                @click="navigateToCreate"
            >
                {{ translations.create_a_habit }}
            </button>
        </div>

        <div class="space-y-4">
            <!-- Franklin's Virtues Section -->
            <FranklinSection
                v-if="franklinHabits.length > 0"
                :habits="franklinHabits"
                :translations="translations"
                @toggle-active="toggleHabit"
                @toggle-all-active="toggleFranklinHabits"
            />

            <!-- Regular Habits -->
            <VueDraggable
                v-if="habits.length > 0"
                v-model="habits"
                :animation="200"
                handle=".drag-handle"
                ghost-class="drag-ghost"
                drag-class="drag-active"
                class="space-y-1"
                @end="saveOrder"
            >
                <EditHabitItem
                    v-for="habit in habits"
                    :key="habit.id"
                    :habit="habit"
                    :translations="translations"
                    :pending="pendingHabitIds.has(habit.id)"
                    :is-new="newHabitIds.has(habit.id)"
                    @toggle-active="toggleHabit"
                    @edit="navigateToEdit"
                    @delete="deleteHabit"
                />
            </VueDraggable>

            <!-- Templates Section -->
            <TemplateSection
                v-if="templateHabits.length > 0"
                :templates="templateHabits"
                :translations="translations"
                @copy="copyFromTemplate"
            />
        </div>
    </div>
</template>

<style>
/*
 * Drag-and-drop transitions for sortable habit lists.
 */
.drag-ghost {
    opacity: 0.3;
}

.drag-active {
    opacity: 0.9;
    transform: scale(1.02);
    box-shadow:
        0 10px 25px -5px rgba(0, 0, 0, 0.1),
        0 4px 10px -5px rgba(0, 0, 0, 0.04);
    border-radius: 0.75rem;
    z-index: 50;
}

:where(.dark) .drag-active {
    box-shadow:
        0 10px 25px -5px rgba(0, 0, 0, 0.4),
        0 4px 10px -5px rgba(0, 0, 0, 0.2);
}
</style>
