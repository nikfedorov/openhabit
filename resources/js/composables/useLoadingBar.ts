import { ref } from 'vue';

const loading = ref(false);
const progress = ref(0);
let timer: ReturnType<typeof setInterval> | null = null;
let hideTimeout: ReturnType<typeof setTimeout> | null = null;
let activeRequests = 0;

function startLoading() {
    activeRequests++;
    if (hideTimeout) {
        clearTimeout(hideTimeout);
        hideTimeout = null;
    }
    if (loading.value) return;
    loading.value = true;
    progress.value = 20;
    timer = setInterval(() => {
        progress.value = Math.min(progress.value + 10, 90);
    }, 100);
}

function stopLoading() {
    activeRequests = Math.max(0, activeRequests - 1);
    if (activeRequests > 0) return;
    if (timer) {
        clearInterval(timer);
        timer = null;
    }
    progress.value = 100;
    hideTimeout = setTimeout(() => {
        loading.value = false;
        progress.value = 0;
        hideTimeout = null;
    }, 200);
}

/**
 * Resets internal state (for testing only).
 */
function reset() {
    activeRequests = 0;
    if (timer) {
        clearInterval(timer);
        timer = null;
    }
    if (hideTimeout) {
        clearTimeout(hideTimeout);
        hideTimeout = null;
    }
    loading.value = false;
    progress.value = 0;
}

export function useLoadingBar() {
    return { loading, progress, startLoading, stopLoading, reset };
}
