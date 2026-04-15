<script setup lang="ts">
import { ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import RouteLoadingBar from '@/components/navigation/RouteLoadingBar.vue';
import TabBar from '@/components/navigation/TabBar.vue';
import PageLoader from '@/components/PageLoader.vue';
import type { UserSettings } from '@/types/api';
import type { NavigationTranslations } from '@/types/navigation';

const RTL_LOCALES = ['ar', 'he', 'fa', 'ur'];

const route = useRoute();
const router = useRouter();
const navTranslations = ref<NavigationTranslations | null>(null);
const pageReady = ref(false);

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
}

/* Apply saved theme or fall back to system preference */
applyTheme(
    (localStorage.getItem('theme') as 'light' | 'dark' | 'system') || 'system',
);
</script>

<template>
    <div class="min-h-screen bg-white dark:bg-neutral-900">
        <RouteLoadingBar />
        <div class="mx-auto max-w-2xl px-4 py-6 pb-24">
            <TabBar
                :active-tab="String(route.name)"
                :translations="navTranslations"
            />

            <router-view v-slot="{ Component }">
                <PageLoader v-if="!pageReady || !Component" />
                <component
                    v-if="Component"
                    v-show="pageReady"
                    :is="Component"
                    @navigation-translations="updateNavTranslations"
                    @settings="updateSettings"
                    @ready="pageReady = true"
                />
            </router-view>
        </div>
    </div>
</template>
