import vue from '@vitejs/plugin-vue';
import { resolve } from 'path';
import { defineConfig } from 'vitest/config';

export default defineConfig({
    plugins: [vue()],
    resolve: {
        alias: {
            '@': resolve(__dirname, 'resources/js'),
        },
    },
    test: {
        environment: 'jsdom',
        include: ['resources/js/tests/**/*.test.ts'],
        setupFiles: ['resources/js/tests/setup.ts'],
        coverage: {
            provider: 'v8',
            reporter: ['text', 'lcov', 'json-summary'],
            reportsDirectory: 'coverage/vue',
            include: ['resources/js/**/*.vue', 'resources/js/**/*.ts'],
            exclude: [
                'resources/js/app.ts',
                'resources/js/router.ts',
                'resources/js/ssr.ts',
                'resources/js/tests/**',
                'resources/js/actions/**',
                'resources/js/routes/**',
                'resources/js/wayfinder/**',
                'resources/js/types/**',
                'resources/js/utils/accessibility.ts',
            ],
            thresholds: {
                lines: 100,
                functions: 100,
                branches: 100,
                statements: 100,
            },
        },
    },
});
