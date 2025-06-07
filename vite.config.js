import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue2 from '@vitejs/plugin-vue2';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        vue2(),
    ],
    resolve: {
        alias: {
            // If you used '@' alias for 'resources/js' in your project
            // '@': '/resources/js',
            // Vue 2 requires an alias for 'vue' to ensure the correct build is used
            // (runtime-only vs compiler included). @vitejs/plugin-vue2 should handle this,
            // but if issues arise with "You are using the runtime-only build of Vue"
            // explicitly adding it might be needed:
            // 'vue': 'vue/dist/vue.esm.js', // For Vue 2
        },
    },
});
