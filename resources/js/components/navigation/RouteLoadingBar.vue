<script setup lang="ts">
import { useRouter } from 'vue-router';
import { useLoadingBar } from '@/composables/useLoadingBar';

const router = useRouter();
const { loading, progress, startLoading, stopLoading } = useLoadingBar();

router.beforeEach(() => {
    startLoading();
});

router.afterEach(() => {
    stopLoading();
});
</script>

<template>
    <transition name="loading-bar">
        <div v-if="loading" class="fixed inset-x-0 top-0 z-[100] h-0.5">
            <div
                class="h-full bg-green-500 transition-all duration-200 ease-out"
                :style="{ width: `${progress}%` }"
            />
        </div>
    </transition>
</template>

<style scoped>
.loading-bar-enter-active,
.loading-bar-leave-active {
    transition: opacity 200ms ease;
}

.loading-bar-enter-from,
.loading-bar-leave-to {
    opacity: 0;
}
</style>
