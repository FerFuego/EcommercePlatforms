import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/chatbot.css',
                'resources/js/chatbot.js',
                'resources/css/pages/landing.css',
                'resources/js/pages/landing.js'
            ],
            refresh: true,
        }),
    ],
});
