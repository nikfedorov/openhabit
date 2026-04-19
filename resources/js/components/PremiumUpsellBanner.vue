<script setup lang="ts">
import type { TrialData } from '@/types/api';

defineProps<{
    trialData: TrialData;
}>();

const emit = defineEmits<{
    'open-premium-modal': [];
}>();
</script>

<template>
    <section
        class="flex cursor-pointer items-center gap-3 rounded-xl bg-yellow-50 px-4 py-4 transition-colors hover:bg-yellow-100 dark:bg-yellow-900/20 dark:hover:bg-yellow-900/30"
        data-testid="premium-upsell-banner"
        @click="emit('open-premium-modal')"
    >
        <div
            class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-yellow-100 text-yellow-500 dark:bg-yellow-900/30"
        >
            <svg
                class="size-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                aria-hidden="true"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 0 0-2.455 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z"
                />
            </svg>
        </div>
        <div class="min-w-0">
            <p class="text-sm font-medium text-yellow-700 dark:text-yellow-400">
                {{ trialData.upgradeLabel }}
            </p>
            <p class="text-xs text-yellow-600 dark:text-yellow-500">
                {{ trialData.featuresSubtitle }}
            </p>
        </div>
    </section>
</template>
