import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import type { AttributeNode, ElementNode, NodeTransform } from '@vue/compiler-core';
import { defineConfig } from 'vite-plus';

/**
 * Strips `data-testid` attributes from production builds.
 */
const stripTestIds: NodeTransform = (node) => {
    if (process.env.NODE_ENV !== 'production') return;
    if (node.type !== 1 /* NodeTypes.ELEMENT */) return;

    (node as ElementNode).props = (node as ElementNode).props.filter(
        (p) =>
            !(
                p.type === 6 /* NodeTypes.ATTRIBUTE */ &&
                (p as AttributeNode).name === 'data-testid'
            ),
    );
};

export default defineConfig({
    fmt: {
        printWidth: 80,
        tabWidth: 4,
        useTabs: false,
        semi: true,
        singleQuote: true,
        overrides: [
            {
                files: ['**/*.yml'],
                options: {
                    tabWidth: 2,
                },
            },
        ],
        sortTailwindcss: {
            functions: ['clsx', 'cn'],
            stylesheet: 'resources/css/app.css',
        },
        sortImports: {
            groups: [
                'builtin',
                'external',
                'internal',
                'parent',
                'sibling',
                'index',
            ],
            newlinesBetween: false,
        },
        ignorePatterns: [
            'resources/views/mail/*',
        ],
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.ts'],
            refresh: true,
        }),
        tailwindcss(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
                compilerOptions: {
                    nodeTransforms: [stripTestIds],
                },
            },
        }),
    ],
});
