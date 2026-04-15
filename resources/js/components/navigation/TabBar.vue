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
    {
        key: 'edit',
        fallbackLabel: 'Edit',
        icon: 'M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10',
        flipRtl: false,
    },
    {
        key: 'settings',
        fallbackLabel: 'Settings',
        icon: 'M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
        flipRtl: false,
    },
] as const;

function getLabel(tab: (typeof tabs)[number]): string {
    return (
        props.translations?.[tab.key as keyof NavigationTranslations] ??
        tab.fallbackLabel
    );
}

function isActive(tab: (typeof tabs)[number]): boolean {
    return (
        props.activeTab === tab.key || props.activeTab.startsWith(`${tab.key}.`)
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
                    isActive(tab)
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
        class="pb-safe fixed inset-x-0 bottom-0 z-50 border-t border-neutral-200 bg-white md:hidden dark:border-neutral-700 dark:bg-neutral-900"
    >
        <div class="flex h-16 items-center justify-around">
            <router-link
                v-for="tab in tabs"
                :key="tab.key"
                :to="{ name: tab.key }"
                class="flex h-full flex-1 flex-col items-center justify-center gap-1 transition-colors duration-150"
                :class="
                    isActive(tab)
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
