<script setup lang="ts">
import { ref, watchEffect } from 'vue';
import type { Habit } from '@/types/track';

const props = defineProps<{
    habit: Habit;
    pending?: boolean;
}>();

const emit = defineEmits<{
    toggle: [habitId: number];
}>();

const pressing = ref(false);
const contentEl = ref<HTMLElement | null>(null);

function handleClick() {
    if (pressing.value) {
        return;
    }

    pressing.value = true;
}

function onAnimationEnd() {
    pressing.value = false;
    emit('toggle', props.habit.id);
}

watchEffect(
    (onCleanup) => {
        const el = contentEl.value;
        if (!el || !props.pending) return;

        el.style.opacity = '';
        el.style.transition = '';

        const animation = el.animate(
            [{ opacity: 1 }, { opacity: 0.5 }, { opacity: 1 }],
            { duration: 1200, easing: 'ease-in-out', iterations: Infinity },
        );

        onCleanup(() => {
            animation.pause();
            el.style.opacity = String(parseFloat(getComputedStyle(el).opacity));
            animation.cancel();
            void el.getBoundingClientRect();
            el.style.transition = 'opacity 500ms ease';
            el.style.opacity = '1';
            el.addEventListener(
                'transitionend',
                () => {
                    el.style.opacity = '';
                    el.style.transition = '';
                },
                { once: true },
            );
        });
    },
    { flush: 'post' },
);
</script>

<template>
    <div
        class="group flex cursor-pointer rounded-xl px-4 py-2 transition-all duration-150 hover:bg-neutral-100 dark:hover:bg-neutral-800"
        :class="{ 'habit-press': pressing }"
        data-testid="habit-item"
        @click="handleClick"
        @animationend.self="onAnimationEnd"
    >
        <div
            ref="contentEl"
            class="habit-item-content flex w-full gap-3"
            :data-pending="pending ? 'true' : undefined"
            data-testid="habit-item-content"
        >
            <!-- Checkbox -->
            <div class="flex h-5 flex-shrink-0 items-center">
                <div
                    class="relative h-[18px] w-[18px] rounded border-2 transition-all duration-150"
                    :class="
                        habit.is_completed
                            ? 'border-green-500 bg-green-500 dark:border-green-500 dark:bg-green-500'
                            : 'border-neutral-300 group-hover:border-neutral-400 dark:border-neutral-600 dark:group-hover:border-neutral-500'
                    "
                >
                    <svg
                        v-if="habit.is_completed"
                        class="absolute inset-0 h-full w-full p-0.5 text-white"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="3"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M5 13l4 4L19 7"
                        />
                    </svg>
                </div>
            </div>

            <!-- Content -->
            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-baseline gap-2">
                    <span
                        class="relative text-sm leading-5 font-medium transition-colors duration-150"
                        :class="
                            habit.is_completed
                                ? 'text-neutral-400 line-through decoration-neutral-400 dark:text-neutral-500 dark:decoration-neutral-500'
                                : 'text-neutral-900 dark:text-white'
                        "
                    >
                        {{ habit.name }}
                    </span>
                </div>
                <p
                    v-if="habit.description"
                    class="mt-0.5 text-xs leading-relaxed text-neutral-500 dark:text-neutral-400"
                >
                    {{ habit.description }}
                </p>
            </div>

            <!-- Progress Circle (multi-iteration) -->
            <div
                v-if="habit.iterations_required > 1"
                class="flex flex-shrink-0 items-center"
            >
                <div class="relative h-10 w-10">
                    <svg
                        class="h-10 w-10 -rotate-90 transform"
                        viewBox="0 0 36 36"
                    >
                        <circle
                            class="text-neutral-200 dark:text-neutral-700"
                            stroke="currentColor"
                            stroke-width="3"
                            fill="none"
                            cx="18"
                            cy="18"
                            r="15"
                        />
                        <circle
                            class="text-green-500 transition-all duration-300 dark:text-green-500"
                            stroke="currentColor"
                            stroke-width="3"
                            fill="none"
                            cx="18"
                            cy="18"
                            r="15"
                            :stroke-dasharray="`${(habit.current_iteration / habit.iterations_required) * 94.25} 94.25`"
                            stroke-linecap="round"
                        />
                    </svg>
                    <span
                        class="absolute inset-0 flex items-center justify-center text-[10px] font-semibold text-neutral-600 dark:text-neutral-300"
                    >
                        {{ habit.current_iteration }}/{{
                            habit.iterations_required
                        }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
@keyframes habit-press {
    0% {
        transform: scale(1);
    }
    40% {
        transform: scale(0.97);
    }
    100% {
        transform: scale(1);
    }
}

.habit-press {
    animation: habit-press 200ms ease-in-out;
}
</style>
