<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { apiFetch } from '@/utils/api';

const props = defineProps<{
    date: string;
    content: string;
    noteLabel: string;
    savingLabel: string;
    saveLabel: string;
    placeholder: string;
}>();

const dailyNote = ref(props.content);
const processing = ref(false);
const focused = ref(false);

const showButton = computed(() => focused.value || processing.value);

watch(
    () => props.content,
    (val) => {
        dailyNote.value = val;
    },
);

function onFocus() {
    focused.value = true;
}

function onBlur() {
    focused.value = false;
    save();
}

async function save() {
    processing.value = true;
    try {
        await apiFetch('/api/track/daily-note', {
            method: 'POST',
            body: JSON.stringify({
                date: props.date,
                content: dailyNote.value,
            }),
        });
    } finally {
        processing.value = false;
    }
}
</script>

<template>
    <div class="mt-6">
        <div class="rounded-xl bg-neutral-100 px-4 py-4 dark:bg-neutral-800">
            <div class="mb-3 flex items-center gap-2">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="h-4 w-4 flex-shrink-0 text-neutral-500 dark:text-neutral-400"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"
                    />
                </svg>
                <span
                    class="text-sm font-medium text-neutral-600 dark:text-neutral-400"
                >
                    {{ noteLabel }}
                </span>
                <span
                    v-if="processing"
                    class="text-xs text-neutral-400 dark:text-neutral-500"
                >
                    {{ savingLabel }}
                </span>
            </div>
            <textarea
                v-model="dailyNote"
                :placeholder="placeholder"
                rows="3"
                maxlength="5000"
                class="w-full resize-none overflow-hidden rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 placeholder-neutral-400 transition-all duration-150 focus:border-transparent focus:ring-2 focus:ring-green-500 focus:outline-none dark:border-neutral-600 dark:bg-neutral-900 dark:text-white dark:placeholder-neutral-500"
                @focus="onFocus"
                @blur="onBlur"
            />
            <Transition name="save">
                <div v-show="showButton" class="save-btn mt-2 flex justify-end">
                    <button
                        type="button"
                        :disabled="processing"
                        class="rounded-lg bg-green-600 px-4 py-1.5 text-sm font-medium text-white transition-colors hover:bg-green-700 disabled:opacity-50 dark:bg-green-500 dark:hover:bg-green-600"
                        @click="save"
                    >
                        {{ processing ? savingLabel : saveLabel }}
                    </button>
                </div>
            </Transition>
        </div>
    </div>
</template>

<style scoped>
.save-enter-active,
.save-leave-active {
    transition:
        opacity 200ms ease,
        max-height 200ms ease,
        margin-top 200ms ease;
    overflow: hidden;
    max-height: 40px;
}

.save-enter-from,
.save-leave-to {
    opacity: 0;
    max-height: 0;
    margin-top: 0;
}
</style>
