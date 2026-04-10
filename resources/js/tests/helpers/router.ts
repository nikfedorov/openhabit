import { createRouter, createMemoryHistory } from 'vue-router';

export function createTestRouter(initialRoute = '/') {
    const router = createRouter({
        history: createMemoryHistory('/app'),
        routes: [
            {
                path: '/track',
                name: 'track',
                component: { template: '<div>Track</div>' },
            },
            {
                path: '/view',
                name: 'view',
                component: { template: '<div>View</div>' },
            },
            {
                path: '/dashboard',
                name: 'dashboard',
                component: { template: '<div>Dashboard</div>' },
            },
        ],
    });

    router.push(initialRoute);

    return router;
}
