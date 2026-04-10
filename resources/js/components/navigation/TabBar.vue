<script setup lang="ts">
import type { NavigationTranslations } from '@/types/navigation';

const props = defineProps<{
    activeTab: string;
    translations: NavigationTranslations | null;
}>();

const tabs = [
    {
        key: 'track',
        fallbackLabel: 'Track',
        icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
        flipRtl: false,
    },
    {
        key: 'view',
        fallbackLabel: 'View',
        icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        flipRtl: true,
    },
] as const;

function getLabel(tab: (typeof tabs)[number]): string {
    return (
        props.translations?.[tab.key as keyof NavigationTranslations] ??
        tab.fallbackLabel
    );
}
</script>

<template>
    <!-- Desktop Tab Bar (top) -->
    <nav class="mb-6 hidden md:block">
        <div
            class="flex items-center gap-1 rounded-xl bg-neutral-100 p-1 dark:bg-neutral-800"
        >
            <router-link
                v-for="tab in tabs"
                :key="tab.key"
                :to="{ name: tab.key }"
                class="flex flex-1 items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition-all duration-150"
                :class="
                    activeTab === tab.key
                        ? 'bg-white text-neutral-900 shadow-sm dark:bg-neutral-700 dark:text-white'
                        : 'text-neutral-500 hover:bg-neutral-200/50 hover:text-neutral-700 dark:text-neutral-400 dark:hover:bg-neutral-700/50 dark:hover:text-neutral-300'
                "
            >
                <svg
                    class="h-4 w-4"
                    :class="{ 'rtl:-scale-x-100': tab.flipRtl }"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        :d="tab.icon"
                    />
                </svg>
                {{ getLabel(tab) }}
            </router-link>
        </div>
    </nav>

    <!-- Mobile Tab Bar (fixed bottom) -->
    <nav
        class="fixed inset-x-0 bottom-0 z-50 border-t border-neutral-200 bg-white pb-[env(safe-area-inset-bottom)] md:hidden dark:border-neutral-700 dark:bg-neutral-900"
    >
        <div class="flex h-16 items-center justify-around">
            <router-link
                v-for="tab in tabs"
                :key="tab.key"
                :to="{ name: tab.key }"
                class="flex h-full flex-1 flex-col items-center justify-center gap-1 transition-colors duration-150"
                :class="
                    activeTab === tab.key
                        ? 'text-green-600 dark:text-green-500'
                        : 'text-neutral-500 hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-300'
                "
            >
                <svg
                    class="h-6 w-6"
                    :class="{ 'rtl:-scale-x-100': tab.flipRtl }"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        :d="tab.icon"
                    />
                </svg>
                <span class="text-xs font-medium">{{ getLabel(tab) }}</span>
            </router-link>
        </div>
    </nav>
</template>
