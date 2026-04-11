<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref } from 'vue';

const props = withDefaults(defineProps<{ delay?: number }>(), { delay: 300 });

const visible = ref(false);
let timer: ReturnType<typeof setTimeout> | undefined;

onMounted(() => {
    timer = setTimeout(() => {
        visible.value = true;
    }, props.delay);
});

onBeforeUnmount(() => {
    clearTimeout(timer);
});
</script>

<template>
    <div
        class="flex min-h-[calc(100vh-theme(spacing.24)-theme(spacing.12))] items-center justify-center"
        data-testid="page-loader"
    >
        <transition name="loader-fade">
            <svg
                v-if="visible"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 512 512"
                fill="none"
                class="h-16 w-16"
                aria-hidden="true"
            >
                <rect class="loader-bg" width="512" height="512" rx="96" />
                <rect
                    x="80"
                    y="272"
                    width="160"
                    height="160"
                    rx="24"
                    fill="#14532d"
                >
                    <animate
                        attributeName="opacity"
                        values="0.4;1;0.4"
                        dur="1.6s"
                        begin="0s"
                        repeatCount="indefinite"
                    />
                </rect>
                <rect
                    x="272"
                    y="272"
                    width="160"
                    height="160"
                    rx="24"
                    fill="#15803d"
                >
                    <animate
                        attributeName="opacity"
                        values="0.4;1;0.4"
                        dur="1.6s"
                        begin="0.2s"
                        repeatCount="indefinite"
                    />
                </rect>
                <rect
                    x="80"
                    y="80"
                    width="160"
                    height="160"
                    rx="24"
                    fill="#16a34a"
                >
                    <animate
                        attributeName="opacity"
                        values="0.4;1;0.4"
                        dur="1.6s"
                        begin="0.4s"
                        repeatCount="indefinite"
                    />
                </rect>
                <rect
                    x="272"
                    y="80"
                    width="160"
                    height="160"
                    rx="24"
                    fill="#22c55e"
                >
                    <animate
                        attributeName="opacity"
                        values="0.4;1;0.4"
                        dur="1.6s"
                        begin="0.6s"
                        repeatCount="indefinite"
                    />
                </rect>
            </svg>
        </transition>
    </div>
</template>

<style scoped>
.loader-fade-enter-active {
    transition: opacity 200ms ease;
}

.loader-fade-enter-from {
    opacity: 0;
}

.loader-bg {
    fill: #f5f5f5;
}

:where(.dark, .dark *) .loader-bg {
    fill: #171717;
}
</style>
