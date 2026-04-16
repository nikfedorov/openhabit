<script setup lang="ts">
defineProps<{
    show: boolean;
    title: string;
    message: string;
    cancelLabel: string;
    confirmLabel: string;
}>();

const emit = defineEmits<{
    confirm: [];
    cancel: [];
}>();
</script>

<template>
    <Teleport to="body">
        <Transition name="confirm-modal">
            <div
                v-if="show"
                class="fixed inset-0 z-50 flex items-center justify-center px-4"
                data-testid="delete-confirm-modal"
                @click.self="emit('cancel')"
            >
                <!-- Backdrop -->
                <div
                    class="fixed inset-0 bg-black/40 dark:bg-black/60"
                    @click="emit('cancel')"
                />

                <!-- Dialog -->
                <div
                    class="confirm-modal-panel relative w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl dark:bg-neutral-900"
                >
                    <!-- Warning Icon -->
                    <div
                        class="mb-4 flex h-11 w-11 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30"
                    >
                        <svg
                            class="h-5 w-5 text-red-600 dark:text-red-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"
                            />
                        </svg>
                    </div>

                    <!-- Text -->
                    <h2
                        class="text-base font-semibold text-neutral-900 dark:text-white"
                    >
                        {{ title }}
                    </h2>
                    <p
                        class="mt-1 text-sm text-neutral-500 dark:text-neutral-400"
                    >
                        {{ message }}
                    </p>

                    <!-- Buttons -->
                    <div class="mt-5 flex gap-3">
                        <button
                            type="button"
                            class="flex-1 rounded-xl bg-neutral-100 px-4 py-2.5 text-sm font-medium text-neutral-700 transition-colors hover:bg-neutral-200 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                            data-testid="delete-cancel-btn"
                            @click="emit('cancel')"
                        >
                            {{ cancelLabel }}
                        </button>
                        <button
                            type="button"
                            class="flex-1 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600"
                            data-testid="delete-confirm-btn"
                            @click="emit('confirm')"
                        >
                            {{ confirmLabel }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
