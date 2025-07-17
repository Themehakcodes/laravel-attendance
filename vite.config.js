import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue'; // ✅ import vue plugin
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(), // ✅ enable Vue plugin here
    ],
});
