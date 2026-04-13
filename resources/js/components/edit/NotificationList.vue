<script setup lang="ts">
import { nextTick, ref } from 'vue';
import type { HabitTranslations, NotificationTime } from '@/types/edit';

const model = defineModel<NotificationTime[]>({ required: true });

defineProps<{
    translations: HabitTranslations;
}>();

const hours = Array.from({ length: 24 }, (_, i) =>
    i.toString().padStart(2, '0'),
);

const minutes = Array.from({ length: 12 }, (_, i) =>
    (i * 5).toString().padStart(2, '0'),
);

const openDropdown = ref<number | null>(null);
const dropdownEl = ref<HTMLElement[]>([]);

function getHour(time: string): string {
    return time.split(':')[0] || '09';
}

function getNormalizedMinute(time: string): string {
    const raw = parseInt(time.split(':')[1] ?? '0', 10);
    const snapped = Math.floor(raw / 5) * 5;
    return snapped.toString().padStart(2, '0');
}

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

function updateHour(index: number, hour: string) {
    const minute = getNormalizedMinute(model.value[index].time);
    updateTime(index, `${hour}:${minute}`);
}

function updateMinute(index: number, minute: string) {
    const hour = getHour(model.value[index].time);
    updateTime(index, `${hour}:${minute}`);
}

function scrollToActive(el: HTMLElement | undefined) {
    const active = el?.querySelector<HTMLElement>('[data-active]');
    const column = active?.parentElement;
    /* c8 ignore next -- defensive guard, always true when dropdown is rendered */
    if (!column) return;
    column.scrollTop =
        active!.offsetTop - column.clientHeight / 2 + active!.clientHeight / 2;
}

function toggleDropdown(index: number) {
    if (openDropdown.value === index) {
        openDropdown.value = null;
        return;
    }
    openDropdown.value = index;
    nextTick(() => scrollToActive(dropdownEl.value[0]));
}

function selectMinute(index: number, minute: string) {
    updateMinute(index, minute);
    openDropdown.value = null;
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

                <!-- Time picker — Notion style -->
                <div class="relative flex flex-1 items-center">
                    <button
                        type="button"
                        class="inline-flex items-center gap-2.5 rounded-xl px-4 py-2.5 transition-colors"
                        :class="
                            openDropdown === index
                                ? 'bg-neutral-200 dark:bg-neutral-700'
                                : 'bg-neutral-100 hover:bg-neutral-200/60 dark:bg-neutral-800 dark:hover:bg-neutral-700/60'
                        "
                        @click="toggleDropdown(index)"
                    >
                        <svg
                            class="h-4 w-4 flex-shrink-0 text-neutral-400 dark:text-neutral-500"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.5"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                            />
                        </svg>
                        <span
                            class="text-sm font-medium text-neutral-900 tabular-nums dark:text-white"
                        >
                            {{
                                getHour(notification.time) +
                                ':' +
                                getNormalizedMinute(notification.time)
                            }}
                        </span>
                    </button>

                    <!-- Combined dropdown -->
                    <div
                        v-if="openDropdown === index"
                        ref="dropdownEl"
                        class="absolute bottom-full left-0 z-50 mb-1 w-40 overflow-hidden rounded-xl bg-white shadow-lg ring-1 ring-neutral-200 dark:bg-neutral-800 dark:ring-neutral-700"
                    >
                        <div
                            class="flex divide-x divide-neutral-200 dark:divide-neutral-700"
                        >
                            <!-- Hours column -->
                            <div
                                class="max-h-56 flex-1 overflow-y-auto overscroll-contain py-1"
                            >
                                <button
                                    v-for="h in hours"
                                    :key="h"
                                    type="button"
                                    class="w-full px-4 py-2 text-center text-sm tabular-nums transition-colors"
                                    :class="
                                        h === getHour(notification.time)
                                            ? 'bg-green-500 font-medium text-white'
                                            : 'text-neutral-700 hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-neutral-700'
                                    "
                                    :data-active="
                                        h === getHour(notification.time)
                                            ? ''
                                            : undefined
                                    "
                                    @click="updateHour(index, h)"
                                >
                                    {{ h }}
                                </button>
                            </div>

                            <!-- Minutes column -->
                            <div
                                class="max-h-56 flex-1 overflow-y-auto overscroll-contain py-1"
                            >
                                <button
                                    v-for="m in minutes"
                                    :key="m"
                                    type="button"
                                    class="w-full px-4 py-2 text-center text-sm tabular-nums transition-colors"
                                    :class="
                                        m ===
                                        getNormalizedMinute(notification.time)
                                            ? 'bg-green-500 font-medium text-white'
                                            : 'text-neutral-700 hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-neutral-700'
                                    "
                                    @click="selectMinute(index, m)"
                                >
                                    {{ m }}
                                </button>
                            </div>
                        </div>
                    </div>
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

        <!-- Backdrop to close dropdown -->
        <div
            v-if="openDropdown !== null"
            class="fixed inset-0 z-40"
            @click="openDropdown = null"
        ></div>

        <p
            v-if="model.length > 0"
            class="mt-1.5 text-xs text-neutral-400 dark:text-neutral-500"
        >
            {{ translations.reminders_help }}
        </p>
    </div>
</template>
