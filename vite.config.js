import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { viteStaticCopy } from 'vite-plugin-static-copy';

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
        // Optimize for performance
        minify: 'esbuild', // Use esbuild instead of terser for now
        rollupOptions: {
            output: {
                // Split vendor code for better caching
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        return 'vendor';
                    }
                },
            },
        },
        // Enable source maps for production debugging
        sourcemap: false,
        // Optimize chunk size
        chunkSizeWarningLimit: 600,
    },
    // Optimize CSS
    css: {
        devSourcemap: false,
    },
    // Optimize dependencies
    optimizeDeps: {
        include: ['alpinejs', '@alpinejs/collapse', 'tippy.js'],
    },
});
