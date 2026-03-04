import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    server: { //remove this
        host: '0.0.0.0',//remove this
        hmr: {//remove this
            host: 'localhost',//remove this
        },//remove this
    }, //remove this
});