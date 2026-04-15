<script setup lang="ts">
import { computed } from 'vue';
import CollapsibleCard from '@/components/edit/CollapsibleCard.vue';
import type { EditHabit, EditTranslations } from '@/types/edit';

const props = defineProps<{
    habits: EditHabit[];
    translations: EditTranslations;
}>();

const emit = defineEmits<{
    'toggle-all-active': [];
}>();

const anyActive = computed(() => props.habits.some((h) => h.is_active));
</script>

<template>
    <CollapsibleCard>
        <template #header>
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
        </template>

        <div class="space-y-1 p-2">
            <div
                v-for="habit in props.habits"
                :key="habit.id"
                class="group flex items-start gap-3 rounded-lg px-3 py-2.5 transition-all duration-150"
                :class="{ 'opacity-50': !habit.is_active }"
            >
                <!-- Status Indicator -->
                <div class="mt-1.5 flex-shrink-0">
                    <div
                        class="h-7 w-7 rounded-md align-middle transition-colors"
                        :class="
                            habit.is_active
                                ? 'bg-green-500 dark:bg-green-500'
                                : 'bg-neutral-300 dark:bg-neutral-600'
                        "
                    ></div>
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
    </CollapsibleCard>
</template>
