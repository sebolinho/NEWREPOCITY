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
        // Optimization for production builds
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true,
            },
        },
        rollupOptions: {
            output: {
                // Code splitting for better caching
                manualChunks: {
                    vendor: ['axios', 'tippy.js'],
                    swiper: ['swiper'],
                    alpine: ['alpinejs'],
                }
            }
        },
        // Increase chunk size warning limit
        chunkSizeWarningLimit: 1000,
        // Enable source maps for production debugging (can be disabled for smaller builds)
        sourcemap: false,
        // Optimize CSS
        cssMinify: true,
    },
    server: {
        // Development server optimizations
        hmr: {
            overlay: true
        },
        watch: {
            usePolling: false,
        }
    },
    // Asset optimization
    assetsInclude: ['**/*.webp', '**/*.png', '**/*.jpg', '**/*.jpeg', '**/*.svg'],
    
    // CSS preprocessing optimization
    css: {
        preprocessorOptions: {
            scss: {
                // Use modern Sass API (dart-sass)
                api: 'modern-compiler'
            }
        },
        // CSS code splitting
        codeShell: true
    }
});
