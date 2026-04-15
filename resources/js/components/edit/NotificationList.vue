<script setup lang="ts">
import type { HabitTranslations, NotificationTime } from '@/types/edit';
import TimePickerInput from '@/components/TimePickerInput.vue';
const model = defineModel<NotificationTime[]>({ required: true });

defineProps<{
    translations: HabitTranslations;
}>();

function addNotification() {
    model.value = [...model.value, { time: '09:00', is_active: true }];
}

function removeNotification(index: number) {
    const updated = [...model.value];
    updated.splice(index, 1);
    model.value = updated;
}

function toggleActive(index: number) {
    const updated = [...model.value];
    updated[index] = {
        ...updated[index],
        is_active: !updated[index].is_active,
    };
    model.value = updated;
}

function updateTime(index: number, time: string) {
    const updated = [...model.value];
    updated[index] = { ...updated[index], time };
    model.value = updated;
}
</script>

<template>
    <div>
        <div class="flex items-center justify-between">
            <label
                class="block text-sm font-medium text-neutral-700 dark:text-neutral-300"
            >
                {{ translations.reminders }}
            </label>
            <button
                v-if="model.length < 10"
                type="button"
                class="rounded-lg px-3 py-1 text-xs font-medium text-green-600 transition-colors hover:bg-green-50 dark:text-green-400 dark:hover:bg-green-900/20"
                @click="addNotification"
            >
                {{ translations.add }}
            </button>
        </div>

        <p
            v-if="model.length === 0"
            class="text-xs text-neutral-400 dark:text-neutral-500"
        >
            {{ translations.no_reminders }}
        </p>

        <div v-else class="mt-2 space-y-2">
            <div
                v-for="(notification, index) in model"
                :key="index"
                class="flex items-center gap-2"
            >
                <!-- Active toggle -->
                <button
                    type="button"
                    class="flex h-7 w-7 flex-shrink-0 cursor-pointer items-center justify-center rounded-md transition-colors"
                    :class="
                        notification.is_active
                            ? 'bg-green-500'
                            : 'bg-neutral-300 dark:bg-neutral-600'
                    "
                    @click="toggleActive(index)"
                >
                    <svg
                        v-if="notification.is_active"
                        class="h-3.5 w-3.5 text-white"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="3"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 6v6l4 2"
                        />
                    </svg>
                </button>

                <!-- Time picker -->
                <div class="flex flex-1 items-center">
                    <TimePickerInput
                        :model-value="notification.time"
                        align="left"
                        @update:model-value="updateTime(index, $event)"
                    />
                </div>

                <!-- Remove button -->
                <button
                    type="button"
                    class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg text-neutral-400 transition-colors hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-900/20"
                    @click="removeNotification(index)"
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
                            d="M6 18 18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Backdrop handled inside TimePickerInput -->

        <p
            v-if="model.length > 0"
            class="mt-1.5 text-xs text-neutral-400 dark:text-neutral-500"
        >
            {{ translations.reminders_help }}
        </p>
    </div>
</template>
