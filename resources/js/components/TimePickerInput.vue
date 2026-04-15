<script setup lang="ts">
import { nextTick, ref } from 'vue';

const props = withDefaults(
    defineProps<{
        modelValue: string | null;
        align?: 'left' | 'right';
    }>(),
    {
        align: 'left',
    },
);

const emit = defineEmits<{
    'update:modelValue': [value: string];
    select: [value: string];
}>();

const hours = Array.from({ length: 24 }, (_, i) =>
    i.toString().padStart(2, '0'),
);

const minutes = Array.from({ length: 12 }, (_, i) =>
    (i * 5).toString().padStart(2, '0'),
);

const isOpen = ref(false);
const dropdownEl = ref<HTMLElement | null>(null);

function getHour(time: string | null): string {
    return time?.split(':')[0] || '09';
}

function getNormalizedMinute(time: string | null): string {
    const raw = parseInt(time?.split(':')[1] ?? '0', 10);
    const snapped = Math.floor(raw / 5) * 5;
    return snapped.toString().padStart(2, '0');
}

function scrollToActive() {
    nextTick(() => {
        const activeEls =
            dropdownEl.value?.querySelectorAll<HTMLElement>('[data-active]');
        activeEls?.forEach((active) => {
            const column = active.parentElement;
            /* c8 ignore next */
            if (!column) return;
            column.scrollTop =
                active.offsetTop -
                column.clientHeight / 2 +
                active.clientHeight / 2;
        });
    });
}

function toggle() {
    isOpen.value = !isOpen.value;
    if (isOpen.value) scrollToActive();
}

function selectHour(hour: string) {
    const minute = getNormalizedMinute(props.modelValue);
    emit('update:modelValue', `${hour}:${minute}`);
}

function selectMinute(minute: string) {
    const hour = getHour(props.modelValue);
    const time = `${hour}:${minute}`;
    emit('update:modelValue', time);
    emit('select', time);
    isOpen.value = false;
}
</script>

<template>
    <div class="relative">
        <button
            type="button"
            class="inline-flex items-center gap-2.5 rounded-xl px-4 py-2.5 transition-colors"
            :class="
                isOpen
                    ? 'bg-neutral-200 dark:bg-neutral-700'
                    : 'bg-neutral-100 hover:bg-neutral-200/60 dark:bg-neutral-800 dark:hover:bg-neutral-700/60'
            "
            @click="toggle"
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
                class="text-sm font-medium tabular-nums text-neutral-900 dark:text-white"
            >
                {{ getHour(modelValue) }}:{{ getNormalizedMinute(modelValue) }}
            </span>
        </button>

        <!-- Dropdown -->
        <div
            v-if="isOpen"
            ref="dropdownEl"
            class="absolute bottom-full z-50 mb-1 w-40 overflow-hidden rounded-xl bg-white shadow-lg ring-1 ring-neutral-200 dark:bg-neutral-800 dark:ring-neutral-700"
            :class="align === 'right' ? 'right-0' : 'left-0'"
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
                            h === getHour(modelValue)
                                ? 'bg-green-500 font-medium text-white'
                                : 'text-neutral-700 hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-neutral-700'
                        "
                        :data-active="
                            h === getHour(modelValue) ? '' : undefined
                        "
                        @click="selectHour(h)"
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
                            m === getNormalizedMinute(modelValue)
                                ? 'bg-green-500 font-medium text-white'
                                : 'text-neutral-700 hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-neutral-700'
                        "
                        @click="selectMinute(m)"
                    >
                        {{ m }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Backdrop -->
        <div
            v-if="isOpen"
            class="fixed inset-0 z-40"
            @click="isOpen = false"
        ></div>
    </div>
</template>
