<script setup lang="ts">
import type { TrialData } from '@/types/api';

defineProps<{
    trialData: TrialData;
}>();

const emit = defineEmits<{
    dismiss: [];
    openPremiumModal: [];
}>();
</script>

<template>
    <div
        v-if="trialData.shouldShowBanner"
        class="mb-4 flex items-center justify-between gap-3 rounded-xl bg-yellow-50 px-4 py-3 dark:bg-yellow-900/20"
        data-testid="trial-banner"
    >
        <div class="flex min-w-0 items-center gap-3">
            <!-- Clock icon -->
            <svg
                class="size-5 shrink-0 text-yellow-500"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                aria-hidden="true"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                />
            </svg>

            <p class="text-xs text-yellow-700 dark:text-yellow-400">
                {{ trialData.bannerText }}
                <br />
                <button
                    class="font-medium underline transition-colors hover:text-yellow-800 dark:hover:text-yellow-300"
                    data-testid="trial-banner-learn-more"
                    @click="emit('openPremiumModal')"
                >
                    {{ trialData.learnMore }}
                </button>
            </p>
        </div>

        <!-- Dismiss button -->
        <button
            class="shrink-0 rounded-lg p-1 text-yellow-500 transition-colors hover:bg-yellow-100 dark:hover:bg-yellow-900/40"
            :aria-label="'Dismiss'"
            data-testid="trial-banner-dismiss"
            @click="emit('dismiss')"
        >
            <svg
                class="size-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="2"
                stroke="currentColor"
                aria-hidden="true"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M6 18 18 6M6 6l12 12"
                />
            </svg>
        </button>
    </div>
</template>
