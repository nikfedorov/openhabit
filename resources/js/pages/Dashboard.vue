<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';

const page = usePage();
const user = page.props.auth.user;
</script>

<template>
    <Head title="Dashboard" />

    <div class="min-h-screen bg-gray-50">
        <div class="mx-auto max-w-2xl px-4 py-8">
            <h1 class="mb-6 text-2xl font-bold text-gray-900">
                {{ $page.props.name }}
            </h1>

            <div class="rounded-lg bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-lg font-semibold text-gray-800">
                    User Information
                </h2>

                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Name</dt>
                        <dd class="text-sm text-gray-900">
                            {{ user.name ?? '—' }}
                        </dd>
                    </div>

                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">
                            Telegram ID
                        </dt>
                        <dd class="text-sm text-gray-900">
                            {{ user.telegram_id ?? '—' }}
                        </dd>
                    </div>

                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="text-sm text-gray-900">
                            {{ user.email ?? '—' }}
                        </dd>
                    </div>

                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">
                            Locale
                        </dt>
                        <dd class="text-sm text-gray-900">
                            {{ user.locale ?? '—' }}
                        </dd>
                    </div>

                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">
                            Last Active
                        </dt>
                        <dd class="text-sm text-gray-900">
                            {{ user.last_active_at ?? '—' }}
                        </dd>
                    </div>

                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">
                            Member Since
                        </dt>
                        <dd class="text-sm text-gray-900">
                            {{ user.created_at }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</template>
