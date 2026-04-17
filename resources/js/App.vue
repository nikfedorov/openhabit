<script setup lang="ts">
import { ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import RouteLoadingBar from '@/components/navigation/RouteLoadingBar.vue';
import TabBar from '@/components/navigation/TabBar.vue';
import PageLoader from '@/components/PageLoader.vue';
import PaymentSuccessConfetti from '@/components/PaymentSuccessConfetti.vue';
import PremiumModal from '@/components/PremiumModal.vue';
import TrialBanner from '@/components/TrialBanner.vue';
import { useTrialUiState } from '@/composables/useTrialUiState';
import type { UserSettings } from '@/types/api';
import type { NavigationTranslations } from '@/types/navigation';
import { prefersReducedMotion } from '@/utils/accessibility';

const RTL_LOCALES = ['ar', 'he', 'fa', 'ur'];

const route = useRoute();
const router = useRouter();
const navTranslations = ref<NavigationTranslations | null>(null);
const pageReady = ref(false);
const showPaymentConfetti = ref(false);
const {
    trialData,
    showPremiumModal,
    setTrialData,
    openPremiumModal,
    closePremiumModal,
    dismissTrialBanner,
} = useTrialUiState();

router.beforeEach((to, from) => {
    if (to.name !== from.name) {
        pageReady.value = false;
    }
});

function updateNavTranslations(translations: NavigationTranslations) {
    navTranslations.value = translations;
}

function applyLocale(locale: string) {
    const dir = RTL_LOCALES.includes(locale) ? 'rtl' : 'ltr';
    document.documentElement.dir = dir;
    document.documentElement.lang = locale;
}

function applyTheme(theme: 'light' | 'dark' | 'system') {
    const prefersDark = window.matchMedia(
        '(prefers-color-scheme: dark)',
    ).matches;
    const isDark = theme === 'dark' || (theme === 'system' && prefersDark);

    document.documentElement.classList.toggle('dark', isDark);
    localStorage.setItem('theme', theme);

    // Fix overscroll bounce revealing wrong background on iOS.
    const bgColor = isDark ? '#171717' : '#ffffff';
    document.documentElement.style.backgroundColor = bgColor;

    // Sync Telegram Mini App header and background colors with current theme.
    const tg = window.Telegram?.WebApp;
    if (tg?.isVersionAtLeast?.('6.1')) {
        tg.setHeaderColor?.(bgColor);
        tg.setBackgroundColor?.(bgColor);
    }
}

function updateSettings(settings: UserSettings) {
    applyLocale(settings.locale);
    applyTheme(settings.theme);

    setTrialData(settings.trial);
}

async function handleTrialBannerDismiss() {
    try {
        await dismissTrialBanner();
    } catch (error) {
        console.error(error);
    }
}

function handlePaymentSuccess() {
    closePremiumModal();

    if (trialData.value && trialData.value.shouldShowBanner) {
        setTrialData({
            ...trialData.value,
            shouldShowBanner: false,
        });
    }

    if (prefersReducedMotion()) {
        return;
    }

    showPaymentConfetti.value = true;
}

function handlePaymentConfettiComplete() {
    showPaymentConfetti.value = false;
}

/* Apply saved theme or fall back to system preference */
applyTheme(
    (localStorage.getItem('theme') as 'light' | 'dark' | 'system') || 'system',
);
</script>

<template>
    <div class="min-h-screen bg-white dark:bg-neutral-900">
        <a
            href="#main-content"
            class="sr-only focus:not-sr-only focus:fixed focus:start-4 focus:top-4 focus:z-[100] focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:text-sm focus:font-medium focus:text-neutral-900 focus:shadow-lg focus:ring-2 focus:ring-green-500 focus:outline-none dark:focus:bg-neutral-800 dark:focus:text-white"
        >
            Skip to main content
        </a>
        <RouteLoadingBar />
        <div class="mx-auto max-w-2xl px-4 py-6 pb-24">
            <TabBar
                :active-tab="String(route.name)"
                :translations="navTranslations"
            />

            <TrialBanner
                v-if="trialData"
                :trial-data="trialData"
                @dismiss="handleTrialBannerDismiss"
                @open-premium-modal="openPremiumModal"
            />

            <main id="main-content" tabindex="-1">
                <router-view v-slot="{ Component }">
                    <Transition name="loader-fade">
                        <PageLoader v-if="!pageReady || !Component" />
                    </Transition>
                    <Transition name="page-fade">
                        <component
                            v-if="Component"
                            v-show="pageReady"
                            :is="Component"
                            @navigation-translations="updateNavTranslations"
                            @settings="updateSettings"
                            @ready="pageReady = true"
                        />
                    </Transition>
                </router-view>
            </main>
        </div>

        <PaymentSuccessConfetti
            :show="showPaymentConfetti"
            @complete="handlePaymentConfettiComplete"
        />

        <PremiumModal
            v-if="trialData"
            :show="showPremiumModal"
            :trial-data="trialData"
            @close="closePremiumModal"
            @payment-success="handlePaymentSuccess"
        />
    </div>
</template>

<style scoped>
.loader-fade-leave-active {
    transition: opacity 300ms ease;
}

.loader-fade-leave-to {
    opacity: 0;
}

.page-fade-enter-active {
    transition: opacity 300ms ease;
}

.page-fade-enter-from {
    opacity: 0;
}
</style>
