import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/frontend/app.css',
                'resources/css/admin/admin.css',
                'resources/css/dashboard/item.css',
                'resources/css/dashboard/member.css',
                'resources/css/forms-app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});
