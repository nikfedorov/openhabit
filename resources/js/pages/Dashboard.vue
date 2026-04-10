<script setup lang="ts">
import { onMounted, ref } from 'vue';
import type { User } from '@/types/dashboard';
import { apiFetch } from '@/utils/api';

const emit = defineEmits<{
    ready: [];
}>();

const user = ref<User | null>(null);
const appName = ref('OpenHabit');

onMounted(async () => {
    const data = await apiFetch<User>('/api/user');
    user.value = data;
    emit('ready');
});
</script>

<template>
    <div>
        <h1 class="mb-6 text-2xl font-bold text-neutral-900 dark:text-white">
            {{ appName }}
        </h1>

        <div
            v-if="user"
            class="rounded-xl bg-neutral-100 p-6 dark:bg-neutral-800"
        >
            <h2
                class="mb-4 text-lg font-semibold text-neutral-900 dark:text-white"
            >
                User Information
            </h2>

            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt
                        class="text-sm font-medium text-neutral-500 dark:text-neutral-400"
                    >
                        Name
                    </dt>
                    <dd class="text-sm text-neutral-900 dark:text-white">
                        {{ user.name ?? '—' }}
                    </dd>
                </div>

                <div class="flex justify-between">
                    <dt
                        class="text-sm font-medium text-neutral-500 dark:text-neutral-400"
                    >
                        Telegram ID
                    </dt>
                    <dd class="text-sm text-neutral-900 dark:text-white">
                        {{ user.telegram_id ?? '—' }}
                    </dd>
                </div>

                <div class="flex justify-between">
                    <dt
                        class="text-sm font-medium text-neutral-500 dark:text-neutral-400"
                    >
                        Email
                    </dt>
                    <dd class="text-sm text-neutral-900 dark:text-white">
                        {{ user.email ?? '—' }}
                    </dd>
                </div>

                <div class="flex justify-between">
                    <dt
                        class="text-sm font-medium text-neutral-500 dark:text-neutral-400"
                    >
                        Locale
                    </dt>
                    <dd class="text-sm text-neutral-900 dark:text-white">
                        {{ user.locale ?? '—' }}
                    </dd>
                </div>

                <div class="flex justify-between">
                    <dt
                        class="text-sm font-medium text-neutral-500 dark:text-neutral-400"
                    >
                        Last Active
                    </dt>
                    <dd class="text-sm text-neutral-900 dark:text-white">
                        {{ user.last_active_at ?? '—' }}
                    </dd>
                </div>

                <div class="flex justify-between">
                    <dt
                        class="text-sm font-medium text-neutral-500 dark:text-neutral-400"
                    >
                        Member Since
                    </dt>
                    <dd class="text-sm text-neutral-900 dark:text-white">
                        {{ user.created_at }}
                    </dd>
                </div>
            </dl>
        </div>
    </div>
</template>
