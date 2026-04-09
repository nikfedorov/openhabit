<script setup lang="ts">
import { ref } from 'vue';
import TabBar from '@/components/navigation/TabBar.vue';
import Dashboard from '@/pages/Dashboard.vue';
import Track from '@/pages/Track.vue';
import View from '@/pages/View.vue';
import type { NavigationTranslations } from '@/types/navigation';

const currentPage = ref<'dashboard' | 'track' | 'view'>('track');
const navTranslations = ref<NavigationTranslations | null>(null);

function navigate(page: string) {
    currentPage.value = page as 'dashboard' | 'track' | 'view';
}

function updateNavTranslations(translations: NavigationTranslations) {
    navTranslations.value = translations;
}
</script>

<template>
    <div class="min-h-screen bg-white dark:bg-neutral-900">
        <div class="mx-auto max-w-2xl px-4 py-6 pb-24">
            <TabBar
                :active-tab="currentPage"
                :translations="navTranslations"
                @navigate="navigate"
            />

            <Dashboard
                v-if="currentPage === 'dashboard'"
                @navigate="navigate"
            />
            <View
                v-else-if="currentPage === 'view'"
                @navigate="navigate"
                @navigation-translations="updateNavTranslations"
            />
            <Track
                v-else
                @navigate="navigate"
                @navigation-translations="updateNavTranslations"
            />
        </div>
    </div>
</template>
