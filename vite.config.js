import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { viteStaticCopy } from 'vite-plugin-static-copy'

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/scss/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        viteStaticCopy({
            targets: [
                {
                    src: 'vendor/tinymce/tinymce',
                    dest: 'vendor/js'
                }
            ]
        })
    ],
    build: {
        // Optimize bundle splitting
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['alpinejs', 'tippy.js', 'axios'],
                    swiper: ['swiper']
                }
            }
        },
        // Improve compression
        minify: 'esbuild',
        cssMinify: true,
        // Target modern browsers for better performance
        target: 'esnext',
        // Improve build performance
        sourcemap: false,
        // Optimize chunk size warnings
        chunkSizeWarningLimit: 600
    }
});
