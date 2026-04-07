<script setup lang="ts">
import { ref } from 'vue';
import Dashboard from '@/pages/Dashboard.vue';
import Track from '@/pages/Track.vue';

const currentPage = ref<'dashboard' | 'track'>('track');

function navigate(page: 'dashboard' | 'track') {
    currentPage.value = page;
}
</script>

<template>
    <Dashboard v-if="currentPage === 'dashboard'" @navigate="navigate" />
    <Track v-else @navigate="navigate" />
</template>
