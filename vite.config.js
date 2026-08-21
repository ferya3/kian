import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: ['resources/views/**', 'routes/**', 'app/Support/**'],
        }),
        tailwindcss(),
    ],
    build: {
        // GSAP و ScrollTrigger فقط در صفحاتی که تایم‌لاین دارند بارگذاری می‌شوند
        // (dynamic import در modules/process-scroll.js) — باندل اصلی سبک می‌ماند.
        cssCodeSplit: true,
        rollupOptions: {
            output: {
                manualChunks: {
                    alpine: ['alpinejs'],
                },
            },
        },
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
