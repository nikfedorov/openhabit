<script setup lang="ts">
import { computed, ref } from 'vue';
import CollapsibleCard from '@/components/edit/CollapsibleCard.vue';
import type { EditTranslations, TemplateHabit } from '@/types/edit';

const props = defineProps<{
    templates: TemplateHabit[];
    translations: EditTranslations;
}>();

const emit = defineEmits<{
    copy: [templateId: number];
}>();

const HIGHLIGHT_DURATION_MS = 5000;
const addedIds = ref(new Set<number>());

const groupedTemplates = computed(() => {
    const groups: { category: string; templates: TemplateHabit[] }[] = [];
    let currentCategory = '';

    for (const template of props.templates) {
        if (template.category !== currentCategory) {
            currentCategory = template.category;
            groups.push({ category: currentCategory, templates: [] });
        }
        groups[groups.length - 1].templates.push(template);
    }

    return groups;
});

function copyTemplate(template: TemplateHabit) {
    if (addedIds.value.has(template.id)) return;

    addedIds.value.add(template.id);
    emit('copy', template.id);

    window.setTimeout(() => {
        addedIds.value.delete(template.id);
    }, HIGHLIGHT_DURATION_MS);
}
</script>

<template>
    <CollapsibleCard>
        <template #header>
            <!-- Title -->
            <div class="flex min-w-0 flex-1 items-baseline gap-2">
                <span
                    class="text-sm font-medium text-neutral-700 dark:text-neutral-300"
                >
                    {{ translations.templates }}
                </span>
                <span class="text-xs text-neutral-400 dark:text-neutral-500">
                    {{ translations.add_from_library }}
                </span>
            </div>
        </template>

        <div class="space-y-4 p-3">
            <div v-for="group in groupedTemplates" :key="group.category">
                <!-- Category Name -->
                <div class="mb-2 px-1">
                    <span
                        class="text-xs font-semibold tracking-wider text-neutral-400 uppercase dark:text-neutral-500"
                    >
                        {{ group.category }}
                    </span>
                </div>

                <!-- Template Habits -->
                <div class="space-y-1">
                    <button
                        v-for="template in group.templates"
                        :key="template.id"
                        type="button"
                        class="group/template flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-start transition-all duration-300"
                        :class="
                            addedIds.has(template.id)
                                ? 'bg-green-50 ring-1 ring-green-200 dark:bg-green-900/20 dark:ring-green-800'
                                : 'hover:bg-neutral-200 dark:hover:bg-neutral-700'
                        "
                        @click="copyTemplate(template)"
                    >
                        <!-- Status square -->
                        <div class="flex-shrink-0">
                            <div
                                class="h-6 w-6 rounded-md transition-all duration-300"
                                :class="
                                    addedIds.has(template.id)
                                        ? 'scale-110 bg-green-500'
                                        : 'bg-neutral-200 group-hover/template:bg-green-600 dark:bg-neutral-600'
                                "
                            ></div>
                        </div>

                        <!-- Content -->
                        <div class="min-w-0 flex-1">
                            <span
                                class="block truncate text-sm font-medium text-neutral-900 dark:text-white"
                            >
                                {{ template.name }}
                            </span>
                            <div class="mt-0.5 flex items-center gap-2">
                                <span
                                    class="truncate text-xs text-neutral-400 dark:text-neutral-500"
                                >
                                    {{ template.human_readable }}
                                </span>
                                <span
                                    v-if="template.iterations_required > 1"
                                    class="text-xs text-neutral-400 dark:text-neutral-500"
                                >
                                    &middot;
                                    {{ template.iterations_required }}&times;
                                </span>
                            </div>
                        </div>

                        <!-- Right side: plus / check + Added! -->
                        <div class="flex flex-shrink-0 items-center gap-1.5">
                            <!-- Plus icon (before adding) -->
                            <svg
                                v-if="!addedIds.has(template.id)"
                                class="h-4 w-4 text-neutral-400 transition-colors duration-200 group-hover/template:text-green-600 dark:text-neutral-500 dark:group-hover/template:text-green-400"
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

                            <!-- Check icon + Added text (after adding) -->
                            <template v-if="addedIds.has(template.id)">
                                <svg
                                    class="h-4 w-4 text-green-600 dark:text-green-400"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2.5"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M4.5 12.75l6 6 9-13.5"
                                    />
                                </svg>
                                <span
                                    class="text-xs font-medium text-green-600 dark:text-green-400"
                                >
                                    {{ translations.added }}
                                </span>
                            </template>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </CollapsibleCard>
</template>
