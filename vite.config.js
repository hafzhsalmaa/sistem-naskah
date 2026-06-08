import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/admin.css',
                'resources/css/penulis.css',
                'resources/css/editor.css',
                'resources/css/layouter.css',
                'resources/js/app.js',
                'resources/js/admin.js',
                'resources/js/penulis.js',
                'resources/js/editor.js',
                'resources/js/layouter.js',
            ],
            refresh: true,
        }),
    ],
});
