<script setup lang="ts">
import { computed } from 'vue';
import { getIntensityColor } from '@/utils/intensity';

const props = defineProps<{
    totalHabits: number;
    completedCount: number;
    progressLabel: string;
    allDoneLabel: string;
}>();

const progressIntensity = computed(() => {
    if (props.totalHabits === 0) return 0;
    const ratio = props.completedCount / props.totalHabits;
    if (ratio >= 0.8) return 4;
    if (ratio >= 0.5) return 3;
    if (ratio >= 0.25) return 2;
    if (ratio > 0) return 1;
    return 0;
});

const segmentColor = computed(() => {
    const intensity = progressIntensity.value;
    return intensity > 0 ? getIntensityColor(intensity) : 'bg-green-500';
});

const emptySegmentColor = getIntensityColor(0);

const segmentCount = computed(() => Math.min(props.totalHabits, 20));

const isAllDone = computed(
    () => props.completedCount === props.totalHabits && props.totalHabits > 0,
);

defineExpose({ progressIntensity, segmentColor });
</script>

<template>
    <div class="mb-6 rounded-xl bg-neutral-100 px-4 py-4 dark:bg-neutral-800">
        <div class="mb-2 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span
                    class="text-sm font-medium text-neutral-600 dark:text-neutral-400"
                >
                    {{ progressLabel }}
                </span>
                <span
                    v-if="isAllDone"
                    class="text-xs font-medium text-green-600 dark:text-green-400"
                >
                    {{ allDoneLabel }}
                </span>
            </div>
            <span
                class="text-sm font-semibold text-neutral-900 dark:text-white"
            >
                {{ completedCount }}/{{ totalHabits }}
            </span>
        </div>

        <TransitionGroup
            v-if="totalHabits > 0"
            tag="div"
            name="progress-segment"
            class="flex h-2"
            data-testid="progress-segments"
        >
            <div
                v-for="i in segmentCount"
                :key="i"
                class="flex-1 overflow-hidden rounded-sm transition-colors duration-300"
                :class="[
                    i <= completedCount ? segmentColor : emptySegmentColor,
                    i > 1 ? 'ms-1' : '',
                ]"
            />
            <div
                v-if="totalHabits > 20"
                :key="'extra'"
                class="ms-1 flex-1 overflow-hidden rounded-sm transition-colors duration-300"
                :class="
                    completedCount >= totalHabits
                        ? segmentColor
                        : emptySegmentColor
                "
            />
        </TransitionGroup>
    </div>
</template>

<style>
.progress-segment-enter-active,
.progress-segment-leave-active {
    overflow: hidden;
    transition:
        max-width 0.3s ease-out,
        margin-inline-start 0.3s ease-out,
        opacity 0.3s ease-out;
}

.progress-segment-enter-from,
.progress-segment-leave-to {
    max-width: 0 !important;
    margin-inline-start: 0 !important;
    opacity: 0;
}

.progress-segment-enter-to,
.progress-segment-leave-from {
    max-width: 10rem;
    opacity: 1;
}
</style>
