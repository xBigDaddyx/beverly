import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            buildDirectory: 'vendor/xbigdaddyx/beverly',
            input: [
                'resources/css/beverly.css',
                'resources/js/beverly.js',
            ],
            refresh: true,
        }),
    ],

});
