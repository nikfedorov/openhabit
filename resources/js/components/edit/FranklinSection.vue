<script setup lang="ts">
import { computed, ref } from 'vue';
import type { EditHabit, EditTranslations } from '@/types/edit';

const props = defineProps<{
    habits: EditHabit[];
    translations: EditTranslations;
}>();

const emit = defineEmits<{
    'toggle-active': [habitId: number];
    'toggle-all-active': [];
}>();

const collapsed = ref(true);

const anyActive = computed(() => props.habits.some((h) => h.is_active));
</script>

<template>
    <div class="overflow-hidden rounded-xl bg-neutral-100 dark:bg-neutral-800">
        <!-- Collapsible Header -->
        <div
            class="flex cursor-pointer items-center gap-3 px-4 py-3 transition-colors hover:bg-neutral-200 dark:hover:bg-neutral-700"
            @click="collapsed = !collapsed"
        >
            <!-- Bulk Toggle Button -->
            <div class="flex-shrink-0">
                <button
                    type="button"
                    class="h-7 w-7 rounded-md align-middle transition-colors"
                    :class="
                        anyActive
                            ? 'bg-green-500 dark:bg-green-500'
                            : 'bg-neutral-300 dark:bg-neutral-600'
                    "
                    @click.stop="emit('toggle-all-active')"
                ></button>
            </div>

            <!-- Title -->
            <div class="flex min-w-0 flex-1 items-baseline gap-2">
                <span
                    class="text-sm font-medium text-neutral-700 dark:text-neutral-300"
                >
                    {{ translations.franklins_virtues }}
                </span>
                <span class="text-xs text-neutral-400 dark:text-neutral-500">
                    ({{ props.habits.length }})
                </span>
            </div>

            <!-- Collapse Arrow -->
            <svg
                class="h-4 w-4 flex-shrink-0 text-neutral-400 transition-transform duration-200 rtl:rotate-180 dark:text-neutral-500"
                :class="{ 'ltr:rotate-90 rtl:-rotate-90': !collapsed }"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M8.25 4.5l7.5 7.5-7.5 7.5"
                />
            </svg>
        </div>

        <!-- Collapsible Content -->
        <div
            v-show="!collapsed"
            class="border-t border-neutral-200 dark:border-neutral-700"
        >
            <div class="space-y-1 p-2">
                <div
                    v-for="habit in props.habits"
                    :key="habit.id"
                    class="group flex items-start gap-3 rounded-lg px-3 py-2.5 transition-all duration-150"
                    :class="{ 'opacity-50': !habit.is_active }"
                >
                    <!-- Status Indicator -->
                    <div class="mt-1.5 flex-shrink-0">
                        <button
                            type="button"
                            class="h-7 w-7 cursor-pointer rounded-md align-middle transition-colors"
                            :class="
                                habit.is_active
                                    ? 'bg-green-500 dark:bg-green-500'
                                    : 'bg-neutral-300 dark:bg-neutral-600'
                            "
                            @click.stop="emit('toggle-active', habit.id)"
                        ></button>
                    </div>

                    <!-- Content -->
                    <div class="min-w-0 flex-1">
                        <span
                            class="truncate text-sm font-medium text-neutral-900 dark:text-white"
                        >
                            {{ habit.name }}
                        </span>
                        <p
                            v-if="habit.description"
                            class="mt-0.5 text-xs text-neutral-500 dark:text-neutral-400"
                        >
                            {{ habit.description }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
