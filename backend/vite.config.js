import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { fileURLToPath } from 'node:url';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js'
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        extensions: ['.mjs', '.js', '.ts', '.jsx', '.tsx', '.json', '.vue'],
        alias: {
            '@': fileURLToPath(new URL('./resources/js', import.meta.url)),
            'ziggy-js': path.resolve(__dirname, 'node_modules/ziggy-js')
        },
    },
    optimizeDeps: {
        include: ['ziggy-js'],
    },
    build: {
        commonjsOptions: {
            include: [/node_modules/],
        },
    },
});
