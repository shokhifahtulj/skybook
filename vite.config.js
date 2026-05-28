import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            ssr: false,
        }),
    ],
    build: {
        outDir: 'public/build',
        manifest: true,
        minify: 'terser',
        sourcemap: false,
        rollupOptions: {
            output: {
                manualChunks: undefined,
            },
        },
    },
    server: {
        hmr: false,
        middlewareMode: true,
    },
});
