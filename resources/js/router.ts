import type { RouteRecordRaw } from 'vue-router';
import { createRouter, createWebHistory } from 'vue-router';

export const routes: RouteRecordRaw[] = [
    {
        path: '/track',
        name: 'track',
        component: () => import('@/pages/Track.vue'),
    },
    {
        path: '/view',
        name: 'view',
        component: () => import('@/pages/View.vue'),
    },
    {
        path: '/edit',
        name: 'edit',
        component: () => import('@/pages/Edit.vue'),
    },
    {
        path: '/edit/create',
        name: 'edit.create',
        component: () => import('@/pages/HabitForm.vue'),
    },
    {
        path: '/edit/habits/:id',
        name: 'edit.habit',
        component: () => import('@/pages/HabitForm.vue'),
    },
    {
        path: '/:pathMatch(.*)*',
        redirect: { name: 'track' },
    },
];

const router = createRouter({
    history: createWebHistory('/app'),
    routes,
    scrollBehavior(_to, _from, savedPosition) {
        return savedPosition ?? { top: 0 };
    },
});

export default router;
