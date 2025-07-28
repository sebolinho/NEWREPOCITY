import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { resolve } from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/scss/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        })
    ],
    build: {
        // Basic code splitting
        rollupOptions: {
            output: {
                manualChunks: {
                    // Vendor chunk for external libraries
                    vendor: ['alpinejs', 'axios'],
                    // UI components chunk
                    ui: ['@alpinejs/collapse', 'tippy.js'],
                    // Swiper in its own chunk due to size
                    swiper: ['swiper']
                }
            }
        },
        // Advanced minification with terser
        minify: 'terser',
        // Optimize chunk size warning limit
        chunkSizeWarningLimit: 1000,
        // Enable CSS code splitting for better caching
        cssCodeSplit: true,
        // Advanced CSS minification
        cssMinify: true,
        // Report compressed size
        reportCompressedSize: true,
        // Optimize assets
        assetsInlineLimit: 4096, // Inline assets smaller than 4kb
        // Target modern browsers for smaller bundles
        target: ['es2015']
    },
    server: {
        // Enable HTTPS for better development experience
        hmr: {
            host: 'localhost'
        },
        // Optimize dev server
        middlewareMode: false,
        fs: {
            strict: true,
            allow: ['..']
        }
    },
    // Optimize dependencies
    optimizeDeps: {
        include: [
            'alpinejs', 
            'axios', 
            'swiper', 
            'tippy.js',
            '@alpinejs/collapse'
        ],
        // Force optimization for these packages
        force: true,
        // Exclude from optimization
        exclude: [],
        // Use esbuild for faster dep optimization
        esbuildOptions: {
            target: 'es2015',
            supported: {
                bigint: true
            }
        }
    },
    // Enhanced esbuild configuration
    esbuild: {
        target: 'es2015',
        legalComments: 'none',
        // Optimize for smaller bundles
        minifyIdentifiers: true,
        minifySyntax: true,
        minifyWhitespace: true,
        // Drop console in production
        drop: process.env.NODE_ENV === 'production' ? ['console', 'debugger'] : [],
        // Define globals for dead code elimination
        define: {
            'process.env.NODE_ENV': JSON.stringify(process.env.NODE_ENV || 'development')
        }
    },
    // CSS optimization
    css: {
        // Enable CSS modules if needed
        modules: false,
        // PostCSS optimization
        postcss: {
            plugins: [
                // Add any additional PostCSS plugins here
            ]
        },
        // CSS preprocessing options
        preprocessorOptions: {
            scss: {
                // Add global SCSS variables/mixins
                additionalData: `
                    // Performance-optimized SCSS variables
                    $enable-caret: false;
                    $enable-rounded: true;
                    $enable-shadows: true;
                    $enable-gradients: false;
                    $enable-transitions: true;
                    $enable-reduced-motion: true;
                    $enable-smooth-scroll: true;
                    $enable-grid-classes: true;
                    $enable-button-pointers: true;
                    $enable-rfs: true;
                    $enable-validation-icons: false;
                    $enable-negative-margins: false;
                    $enable-deprecation-messages: false;
                    $enable-important-utilities: false;
                `
            }
        },
        devSourcemap: true
    },
    // Resolve optimization
    resolve: {
        alias: {
            '@': resolve(__dirname, 'resources/js'),
            '~': resolve(__dirname, 'resources'),
            'vue': 'vue/dist/vue.esm-bundler.js'
        },
        extensions: ['.js', '.json', '.jsx', '.mjs', '.ts', '.tsx', '.vue']
    },
    // JSON optimization
    json: {
        namedExports: true,
        stringify: false
    },
    // Worker optimization
    worker: {
        format: 'es',
        plugins: () => []
    },
    // Preview server configuration
    preview: {
        port: 4173,
        strictPort: true,
        cors: true
    },
    // Environment variables
    envPrefix: ['VITE_', 'MIX_'],
    // Logging
    logLevel: 'info',
    clearScreen: true
});
