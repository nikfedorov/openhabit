<script setup lang="ts">
import { ref } from 'vue';
import type { TrialData } from '@/types/api';

defineProps<{
    show: boolean;
    trialData: TrialData;
}>();

const emit = defineEmits<{
    close: [];
}>();

/** Whether the Telegram WebApp context is unavailable for payment. */
const notInTelegram = ref(false);

function upgrade(invoiceLink: string | null) {
    if (!invoiceLink) {
        notInTelegram.value = true;
        return;
    }

    const tg = window.Telegram?.WebApp;

    if (tg) {
        tg.openInvoice(invoiceLink);
    } else {
        window.open(invoiceLink, '_blank');
    }
}
</script>

<template>
    <Teleport to="body">
        <Transition name="premium-modal">
            <div
                v-if="show"
                class="fixed inset-0 z-50 flex items-center justify-center px-4"
                data-testid="premium-modal"
                role="dialog"
                aria-modal="true"
                :aria-labelledby="'premium-modal-title'"
                @keydown.escape="emit('close')"
            >
                <!-- Backdrop -->
                <div
                    class="fixed inset-0 bg-black/50"
                    aria-hidden="true"
                    @click="emit('close')"
                />

                <!-- Dialog panel -->
                <div
                    class="premium-modal-panel relative w-full max-w-sm space-y-4 rounded-2xl bg-white p-6 shadow-xl dark:bg-neutral-800"
                    data-testid="premium-modal-panel"
                >
                    <!-- Close button -->
                    <button
                        class="absolute end-4 top-4 rounded-lg p-1 text-neutral-400 transition-colors hover:text-neutral-600 dark:hover:text-neutral-200"
                        data-testid="premium-modal-close"
                        @click="emit('close')"
                    >
                        <svg
                            class="size-5"
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

                    <!-- Header -->
                    <div class="text-center">
                        <div
                            class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-yellow-100 dark:bg-yellow-900/30"
                        >
                            <svg
                                class="size-6 text-yellow-500"
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
                        <h3
                            id="premium-modal-title"
                            class="text-lg font-semibold text-neutral-900 dark:text-white"
                        >
                            {{ trialData.featuresTitle }}
                        </h3>
                        <p
                            class="mt-1 text-xs text-neutral-500 dark:text-neutral-400"
                        >
                            {{ trialData.featuresSubtitle }}
                        </p>
                    </div>

                    <!-- Features list -->
                    <ul class="space-y-3" data-testid="premium-features-list">
                        <li
                            v-for="feature in [
                                trialData.featureNotifications,
                                trialData.featureAiDigest,
                                trialData.featureExport,
                            ]"
                            :key="feature"
                            class="flex items-start gap-3"
                        >
                            <svg
                                class="mt-0.5 size-5 shrink-0 text-green-500"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="2"
                                stroke="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="m4.5 12.75 6 6 9-13.5"
                                />
                            </svg>
                            <span
                                class="text-sm text-neutral-700 dark:text-neutral-300"
                            >
                                {{ feature }}
                            </span>
                        </li>
                    </ul>

                    <!-- Upgrade button -->
                    <button
                        class="w-full rounded-xl bg-yellow-500 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-yellow-600"
                        data-testid="premium-modal-upgrade"
                        @click="upgrade(trialData.invoiceLink)"
                    >
                        {{ trialData.upgradeLabel }}
                    </button>

                    <!-- Not in Telegram message -->
                    <p
                        v-if="notInTelegram"
                        class="text-center text-xs text-red-500 dark:text-red-400"
                        data-testid="premium-modal-not-in-telegram"
                    >
                        {{ trialData.openInTelegramLabel }}
                    </p>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
