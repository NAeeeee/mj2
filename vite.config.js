// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/style.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        proxy: {
            '/': 'http://localhost:8000',  // Laravel 백엔드 서버로 프록시
        },
        port: 3000,
    },
});