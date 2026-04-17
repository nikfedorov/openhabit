<script setup lang="ts">
import confetti from 'canvas-confetti';
import { watch } from 'vue';

const props = defineProps<{
    show: boolean;
}>();

const emit = defineEmits<{
    complete: [];
}>();

/** Fire confetti burst when shown, then auto-dismiss after animation completes. */
watch(
    () => props.show,
    (shown) => {
        if (!shown) {
            return;
        }

        confetti({
            particleCount: 160,
            spread: 90,
            origin: { y: 0.35 },
            colors: [
                '#f59e0b',
                '#10b981',
                '#22c55e',
                '#fb7185',
                '#facc15',
                '#38bdf8',
                '#f97316',
                '#a78bfa',
            ],
            gravity: 0.85,
            scalar: 0.9,
            ticks: 220,
        });

        setTimeout(() => emit('complete'), 3000);
    },
);
</script>

<template>
    <Transition name="payment-confetti">
        <div
            v-if="show"
            class="pointer-events-none fixed inset-0 z-[60]"
            data-testid="payment-confetti"
            aria-hidden="true"
        />
    </Transition>
</template>

<style scoped>
.payment-confetti-enter-active,
.payment-confetti-leave-active {
    transition: opacity 0.3s ease;
}

.payment-confetti-enter-from,
.payment-confetti-leave-to {
    opacity: 0;
}
</style>
