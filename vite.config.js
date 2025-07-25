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
        // Code splitting for better caching
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['alpinejs', 'axios'],
                    ui: ['@alpinejs/collapse', 'tippy.js'],
                    swiper: ['swiper']
                }
            }
        },
        // Enable source maps for production debugging
        sourcemap: true,
        // Minify for better compression
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true
            }
        },
        // Optimize chunk size
        chunkSizeWarningLimit: 1000,
        // Enable CSS code splitting
        cssCodeSplit: true
    },
    server: {
        // Enable HTTPS for better development experience
        hmr: {
            host: 'localhost'
        }
    },
    // Optimize dependencies
    optimizeDeps: {
        include: ['alpinejs', 'axios', 'swiper', 'tippy.js']
    },
    // Enable experimental features for better performance
    esbuild: {
        target: 'esnext',
        legalComments: 'none'
    }
});
