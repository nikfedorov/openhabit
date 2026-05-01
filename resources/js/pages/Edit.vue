<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { vDraggable } from 'vue-draggable-plus';
import { useRouter } from 'vue-router';
import DeleteConfirmModal from '@/components/DeleteConfirmModal.vue';
import EditHabitItem from '@/components/edit/EditHabitItem.vue';
import FranklinSection from '@/components/edit/FranklinSection.vue';
import TemplateSection from '@/components/edit/TemplateSection.vue';
import type { EditApiResponse } from '@/types/api';
import type { EditHabit, EditTranslations, TemplateHabit } from '@/types/edit';
import type { NavigationTranslations } from '@/types/navigation';
import { apiFetch } from '@/utils/api';

const router = useRouter();

const emit = defineEmits<{
    'navigation-translations': [translations: NavigationTranslations];
    settings: [
        settings: {
            locale: string;
            theme: string;
            moveCompletedToEnd: boolean;
        },
    ];
    ready: [];
}>();

// ─── Page state ──────────────────────────────────────────────

const habits = ref<EditHabit[]>([]);
const franklinHabits = ref<EditHabit[]>([]);
const templateHabits = ref<TemplateHabit[]>([]);
const translations = ref<EditTranslations | null>(null);
const loading = ref(true);

// ─── Highlight state ─────────────────────────────────────────

const HIGHLIGHT_DURATION_MS = 5000;
/** Set of habit IDs currently highlighted (supports multiple simultaneous). */
const highlightedHabitIds = ref<Set<number>>(new Set());

function highlightHabit(habitId: number) {
    highlightedHabitIds.value.add(habitId);
    window.setTimeout(() => {
        highlightedHabitIds.value.delete(habitId);
    }, HIGHLIGHT_DURATION_MS);
}

// ─── Delete confirmation ────────────────────────────────────

const showDeleteConfirm = ref(false);
const deleteTargetId = ref<number | null>(null);
const habitItemRefs = ref<Map<number, InstanceType<typeof EditHabitItem>>>(
    new Map(),
);

// ─── Data loading ───────────────────────────────────────────

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
    emit('navigation-translations', response.navigationTranslations);
    emit('settings', response.settings);
    loading.value = false;
    emit('ready');
}

// ─── Habit actions ──────────────────────────────────────────

async function toggleHabit(habitId: number) {
    const habit = habits.value.find((h) => h.id === habitId);
    if (habit) {
        habit.is_active = !habit.is_active;
    }

    await apiFetch(
        `/api/edit/habits/${habitId.toString()}/toggle`,
        { method: 'POST' },
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
    const idx = habits.value.findIndex((h) => h.id === habitId);
    if (idx !== -1) habits.value.splice(idx, 1);
    await apiFetch(
        `/api/edit/habits/${habitId.toString()}`,
        { method: 'DELETE' },
        { silent: true },
    );
    // splice mutates the array in-place to keep SortableJS in sync
}

async function reorderHabits() {
    await apiFetch(
        '/api/edit/habits/reorder',
        {
            method: 'POST',
            body: JSON.stringify({
                ordered_ids: habits.value.map((h) => h.id),
            }),
        },
        { silent: true },
    );
    // v-draggable already mutated habits.value in-place — no need to replace
}

// ─── Delete confirmation flow ───────────────────────────────

function confirmDelete(habitId: number) {
    deleteTargetId.value = habitId;
    showDeleteConfirm.value = true;
}

function cancelDelete() {
    habitItemRefs.value.get(deleteTargetId.value!)?.resetSwipe();
    showDeleteConfirm.value = false;
    deleteTargetId.value = null;
}

function executeDelete() {
    deleteHabit(deleteTargetId.value!);
    showDeleteConfirm.value = false;
    deleteTargetId.value = null;
}

function setHabitItemRef(
    habitId: number,
    el: InstanceType<typeof EditHabitItem> | null,
) {
    if (el) {
        habitItemRefs.value.set(habitId, el);
    } else {
        habitItemRefs.value.delete(habitId);
    }
}

// ─── Navigation ─────────────────────────────────────────────

function navigateToCreate() {
    router.push({ name: 'edit.create' });
}

function navigateToEdit(habitId: number) {
    router.push({ name: 'edit.habit', params: { id: habitId.toString() } });
}

// ─── Template copy ───────────────────────────────────────────

async function copyFromTemplate(templateId: number) {
    const template = templateHabits.value.find((t) => t.id === templateId);
    if (!template) return;

    const existingIds = new Set(habits.value.map((h) => h.id));

    const response = await apiFetch<{ data: EditHabit[] }>(
        `/api/edit/templates/${templateId.toString()}/copy`,
        { method: 'POST' },
    );

    // Mutate in-place to keep SortableJS in sync: replace existing items,
    // then append any newly added ones.
    const newRegular = response.data.filter((h) => !h.is_franklin_virtue);
    habits.value.splice(0, habits.value.length, ...newRegular);

    const newHabit = habits.value.find((h) => !existingIds.has(h.id));
    if (newHabit) {
        highlightHabit(newHabit.id);
    }
}

// ─── Lifecycle ───────────────────────────────────────────────

onMounted(() => {
    loadData();
});

// Exposed for component tests that simulate drag-end reordering.
defineExpose({ reorderHabits });
</script>

<template>
    <div v-if="!loading && translations">
        <!-- Page Header -->
        <div class="mb-6 flex items-center justify-between gap-4">
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
            class="flex flex-col items-center justify-center px-8 py-16 text-center"
        >
            <div
                class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-neutral-100 dark:bg-neutral-800"
            >
                <svg
                    class="h-8 w-8 text-neutral-400 dark:text-neutral-500"
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
            </div>
            <h2
                class="mb-1 text-lg font-semibold text-neutral-900 dark:text-white"
            >
                {{ translations.no_habits_yet }}
            </h2>
            <p class="max-w-xs text-sm text-neutral-500 dark:text-neutral-400">
                {{ translations.create_first_habit }}
            </p>
            <button
                type="button"
                class="mt-5 inline-flex items-center gap-1.5 rounded-full bg-green-600 px-4 py-2 text-sm font-medium text-white transition-colors duration-150 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600"
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
                {{ translations.create_a_habit }}
            </button>
        </div>

        <div class="space-y-4">
            <!-- Franklin's Virtues Section -->
            <FranklinSection
                v-if="franklinHabits.length > 0"
                :habits="franklinHabits"
                :translations="translations"
                @toggle-all-active="toggleFranklinHabits"
            />

            <!-- Regular Habits (draggable + animated) -->
            <TransitionGroup
                v-draggable="[
                    habits,
                    {
                        animation: 200,
                        handle: '.habit-drag-handle',
                        ghostClass: 'habit-ghost',
                        chosenClass: 'habit-chosen',
                        dragClass: 'habit-dragging',
                        onEnd: reorderHabits,
                    },
                ]"
                tag="div"
                name="habit"
                class="space-y-1"
            >
                <EditHabitItem
                    v-for="habit in habits"
                    :ref="(el: any) => setHabitItemRef(habit.id, el)"
                    :key="habit.id"
                    :habit="habit"
                    :translations="translations"
                    :highlighted="highlightedHabitIds.has(habit.id)"
                    @toggle-active="toggleHabit"
                    @edit="navigateToEdit"
                    @delete="deleteHabit"
                    @confirm-delete="confirmDelete"
                />
            </TransitionGroup>

            <!-- Templates Section -->
            <TemplateSection
                v-if="templateHabits.length > 0"
                :templates="templateHabits"
                :translations="translations"
                @copy="copyFromTemplate"
            />
        </div>

        <DeleteConfirmModal
            v-if="translations"
            :show="showDeleteConfirm"
            :title="translations.delete_habit"
            :message="translations.delete_confirm"
            :cancel-label="translations.cancel"
            :confirm-label="translations.delete_habit"
            @confirm="executeDelete"
            @cancel="cancelDelete"
        />
    </div>
</template>

<style scoped>
/* Enter/leave animations for habit list items. */
.habit-enter-active {
    transition:
        opacity 300ms ease,
        transform 300ms ease-in-out,
        max-height 300ms ease-in-out,
        margin 300ms ease-in-out,
        padding 300ms ease-in-out;
    overflow: hidden;
    max-height: 200px;
}

.habit-leave-active {
    transition:
        opacity 300ms ease-in-out,
        max-height 300ms ease-in-out,
        margin 300ms ease-in-out,
        padding 300ms ease-in-out;
    overflow: hidden;
    max-height: 200px;
}

.habit-enter-from {
    opacity: 0;
    max-height: 0;
    transform: translateY(-8px);
    margin-top: 0 !important;
    margin-bottom: 0 !important;
}

.habit-leave-to {
    opacity: 0;
    max-height: 0;
    margin-top: 0 !important;
    margin-bottom: 0 !important;
}

.habit-leave-active {
    position: relative;
}

/* SortableJS drag visuals. */
.habit-ghost {
    opacity: 0.4;
}

.habit-chosen {
    cursor: grabbing;
}

.habit-dragging {
    opacity: 0.9;
}
</style>
