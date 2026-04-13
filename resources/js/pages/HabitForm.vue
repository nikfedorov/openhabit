<script setup lang="ts">
import { useTextareaAutosize } from '@vueuse/core';
import { computed, onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import FrequencySelector from '@/components/edit/FrequencySelector.vue';
import IterationsCounter from '@/components/edit/IterationsCounter.vue';
import MonthlyOptions from '@/components/edit/MonthlyOptions.vue';
import NotificationList from '@/components/edit/NotificationList.vue';
import WeeklyDays from '@/components/edit/WeeklyDays.vue';
import type { HabitShowApiResponse, UserSettings } from '@/types/api';
import type { HabitFormData, HabitTranslations } from '@/types/edit';
import type { NavigationTranslations } from '@/types/navigation';
import { apiFetch } from '@/utils/api';

const route = useRoute();
const router = useRouter();

const emit = defineEmits<{
    'navigation-translations': [translations: NavigationTranslations];
    settings: [settings: UserSettings];
    ready: [];
}>();

const habitId = computed(() =>
    route.params.id ? Number(route.params.id) : null,
);
const isEditing = computed(() => habitId.value !== null);

const form = ref<HabitFormData>({
    name: '',
    description: null,
    iterations_required: 1,
    is_active: true,
    frequency: 'DAILY',
    weekly_days: [],
    monthly_days: [],
    monthly_mode: 'day',
    monthly_position: 1,
    monthly_weekday: 0,
    notifications: [],
});

const translations = ref<HabitTranslations | null>(null);
const saving = ref(false);
const loading = ref(true);
const showDeleteConfirm = ref(false);

const descriptionEl = ref<HTMLTextAreaElement>();
useTextareaAutosize({
    element: descriptionEl,
    input: computed(() => form.value.description ?? ''),
});

async function loadTranslations() {
    const response = await apiFetch<{
        habitTranslations: HabitTranslations;
        navigationTranslations: NavigationTranslations;
        settings: UserSettings;
    }>('/api/edit');
    translations.value = response.habitTranslations;
    emit('navigation-translations', response.navigationTranslations);
    emit('settings', response.settings);
}

async function loadHabit() {
    const response = await apiFetch<HabitShowApiResponse>(
        `/api/edit/habits/${habitId.value!.toString()}`,
    );
    form.value = { ...response.data };
}

async function submitForm() {
    saving.value = true;

    const url = isEditing.value
        ? `/api/edit/habits/${habitId.value!.toString()}`
        : '/api/edit/habits';
    const method = isEditing.value ? 'PUT' : 'POST';

    await apiFetch(url, {
        method,
        body: JSON.stringify(form.value),
    });

    saving.value = false;
    router.push({ name: 'edit' });
}

async function deleteHabit() {
    showDeleteConfirm.value = false;
    await apiFetch(`/api/edit/habits/${habitId.value!.toString()}`, {
        method: 'DELETE',
    });

    router.push({ name: 'edit' });
}

function goBack() {
    router.push({ name: 'edit' });
}

onMounted(async () => {
    await loadTranslations();
    if (isEditing.value) {
        await loadHabit();
    }
    loading.value = false;
    emit('ready');
});
</script>

<template>
    <div>
        <div v-if="!loading && translations" class="space-y-6">
            <!-- Page Header -->
            <div class="flex items-center gap-3">
                <button
                    type="button"
                    class="flex h-8 w-8 items-center justify-center rounded-lg text-neutral-400 transition-colors hover:bg-neutral-100 hover:text-neutral-600 dark:hover:bg-neutral-800 dark:hover:text-neutral-300"
                    @click="goBack"
                >
                    <svg
                        class="h-5 w-5 rtl:rotate-180"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15.75 19.5L8.25 12l7.5-7.5"
                        />
                    </svg>
                </button>
                <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">
                    {{
                        isEditing
                            ? translations.edit_habit
                            : translations.new_habit
                    }}
                </h1>
            </div>

            <!-- Form -->
            <form class="space-y-6" @submit.prevent="submitForm">
                <!-- Name -->
                <div>
                    <label
                        for="habit-name"
                        class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300"
                    >
                        {{ translations.name }}
                    </label>
                    <input
                        id="habit-name"
                        v-model="form.name"
                        type="text"
                        :placeholder="translations.name_placeholder"
                        autofocus
                        class="w-full rounded-xl border-0 bg-neutral-100 px-4 py-3 text-sm text-neutral-900 placeholder-neutral-400 transition-colors focus:bg-white focus:ring-2 focus:ring-green-500 dark:bg-neutral-800 dark:text-white dark:focus:bg-neutral-700"
                    />
                    <p
                        class="mt-1.5 text-xs text-neutral-400 dark:text-neutral-500"
                    >
                        {{ translations.name_help }}
                    </p>
                </div>

                <!-- Description -->
                <div>
                    <label
                        for="habit-description"
                        class="mb-2 block text-sm font-medium text-neutral-700 dark:text-neutral-300"
                    >
                        {{ translations.description }}
                        <span class="font-normal text-neutral-400">{{
                            translations.description_optional
                        }}</span>
                    </label>
                    <textarea
                        id="habit-description"
                        ref="descriptionEl"
                        v-model="form.description"
                        :placeholder="translations.description_placeholder"
                        rows="1"
                        class="w-full resize-none overflow-hidden rounded-xl border-0 bg-neutral-100 px-4 py-3 text-sm text-neutral-900 placeholder-neutral-400 transition-colors focus:bg-white focus:ring-2 focus:ring-green-500 dark:bg-neutral-800 dark:text-white dark:focus:bg-neutral-700"
                    ></textarea>
                    <p class="text-xs text-neutral-400 dark:text-neutral-500">
                        {{ translations.description_help }}
                    </p>
                </div>

                <!-- Frequency -->
                <FrequencySelector
                    v-model="form.frequency"
                    :translations="translations"
                />

                <!-- Weekly Days -->
                <WeeklyDays
                    v-if="form.frequency === 'WEEKLY'"
                    v-model="form.weekly_days"
                    :translations="translations"
                />

                <!-- Monthly Options -->
                <MonthlyOptions
                    v-if="form.frequency === 'MONTHLY'"
                    v-model:mode="form.monthly_mode"
                    v-model:days="form.monthly_days"
                    v-model:position="form.monthly_position"
                    v-model:weekday="form.monthly_weekday"
                    :translations="translations"
                />

                <!-- Iterations -->
                <IterationsCounter
                    v-model="form.iterations_required"
                    :translations="translations"
                />

                <!-- Notifications -->
                <NotificationList
                    v-model="form.notifications"
                    :translations="translations"
                />

                <!-- Active Toggle -->
                <div class="flex items-center justify-between py-2">
                    <div>
                        <p
                            class="text-sm font-medium text-neutral-700 dark:text-neutral-300"
                        >
                            {{ translations.active }}
                        </p>
                        <p
                            class="mt-0.5 text-xs text-neutral-400 dark:text-neutral-500"
                        >
                            {{ translations.active_help }}
                        </p>
                    </div>
                    <button
                        type="button"
                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
                        :class="
                            form.is_active
                                ? 'bg-green-500'
                                : 'bg-neutral-300 dark:bg-neutral-600'
                        "
                        @click="form.is_active = !form.is_active"
                    >
                        <span
                            class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                            :class="
                                form.is_active
                                    ? 'ltr:translate-x-6 rtl:-translate-x-6'
                                    : 'ltr:translate-x-1 rtl:-translate-x-1'
                            "
                        ></span>
                    </button>
                </div>

                <!-- Action Buttons -->
                <div
                    class="flex items-center border-t border-neutral-200 pt-4 dark:border-neutral-700"
                    :class="isEditing ? 'justify-between' : 'justify-end'"
                >
                    <button
                        v-if="isEditing"
                        type="button"
                        class="rounded-lg px-4 py-2 text-sm font-medium text-red-600 transition-colors hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-900/20 dark:hover:text-red-300"
                        @click="showDeleteConfirm = true"
                    >
                        {{ translations.delete_habit }}
                    </button>
                    <div class="flex items-center gap-3">
                        <button
                            type="button"
                            class="rounded-lg px-4 py-2 text-sm font-medium text-neutral-600 transition-colors hover:bg-neutral-100 hover:text-neutral-700 dark:text-neutral-400 dark:hover:bg-neutral-800 dark:hover:text-neutral-300"
                            @click="goBack"
                        >
                            {{ translations.cancel }}
                        </button>
                        <button
                            type="submit"
                            :disabled="saving"
                            class="rounded-lg bg-green-600 px-5 py-2 text-sm font-medium text-white transition-colors hover:bg-green-700 disabled:opacity-50 dark:bg-green-500 dark:hover:bg-green-600"
                        >
                            {{
                                isEditing
                                    ? translations.save_changes
                                    : translations.create_habit
                            }}
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Delete Confirm Modal -->
        <Teleport to="body">
            <Transition name="confirm-modal">
                <div
                    v-if="showDeleteConfirm"
                    class="fixed inset-0 z-50 flex items-center justify-center px-4"
                    @click.self="showDeleteConfirm = false"
                >
                    <!-- Backdrop -->
                    <div
                        class="fixed inset-0 bg-black/40 dark:bg-black/60"
                        @click="showDeleteConfirm = false"
                    />

                    <!-- Dialog -->
                    <div
                        class="confirm-modal-panel relative w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl dark:bg-neutral-900"
                    >
                        <!-- Icon -->
                        <div
                            class="mb-4 flex h-11 w-11 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30"
                        >
                            <svg
                                class="h-5 w-5 text-red-600 dark:text-red-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"
                                />
                            </svg>
                        </div>

                        <!-- Text -->
                        <h2
                            class="text-base font-semibold text-neutral-900 dark:text-white"
                        >
                            {{ translations.delete_habit }}
                        </h2>
                        <p
                            class="mt-1 text-sm text-neutral-500 dark:text-neutral-400"
                        >
                            {{ translations.delete_confirm }}
                        </p>

                        <!-- Buttons -->
                        <div class="mt-5 flex gap-3">
                            <button
                                type="button"
                                class="flex-1 rounded-xl bg-neutral-100 px-4 py-2.5 text-sm font-medium text-neutral-700 transition-colors hover:bg-neutral-200 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                                @click="showDeleteConfirm = false"
                            >
                                {{ translations.cancel }}
                            </button>
                            <button
                                type="button"
                                class="flex-1 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600"
                                @click="deleteHabit"
                            >
                                {{ translations.delete_habit }}
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>
