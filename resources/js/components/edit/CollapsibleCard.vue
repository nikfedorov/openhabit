<script setup lang="ts">
import { ref } from 'vue';

const collapsed = ref(true);
const cardEl = ref<HTMLElement | null>(null);

function toggle() {
    collapsed.value = !collapsed.value;

    if (!collapsed.value) {
        // After the grid-row expansion starts, smoothly scroll the card header to the top.
        window.setTimeout(() => {
            cardEl.value?.scrollIntoView?.({
                behavior: 'smooth',
                block: 'start',
            });
        }, 50);
    }
}
</script>

<template>
    <div
        ref="cardEl"
        class="overflow-hidden rounded-xl bg-neutral-100 dark:bg-neutral-800"
    >
        <!-- Clickable header that toggles the collapsible content -->
        <div
            class="flex cursor-pointer items-center gap-3 px-4 py-3 transition-colors hover:bg-neutral-200 dark:hover:bg-neutral-700"
            @click="toggle"
        >
            <slot name="header" />

            <!-- Collapse arrow -->
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

        <!--
            Animated collapsible body using CSS grid rows trick:
            grid-rows-[0fr] collapses to 0 height, grid-rows-[1fr] expands.
        -->
        <div
            class="grid transition-[grid-template-rows] duration-200"
            :class="collapsed ? 'grid-rows-[0fr]' : 'grid-rows-[1fr]'"
        >
            <div class="overflow-hidden">
                <div
                    class="border-t border-neutral-200 dark:border-neutral-700"
                >
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
