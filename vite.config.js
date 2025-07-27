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
        // Enhanced optimization for maximum PageSpeed performance
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true,
                passes: 2,
                unsafe: true,
                unsafe_comps: true,
                unsafe_Function: true,
                unsafe_math: true,
                unsafe_symbols: true,
                unsafe_methods: true,
                unsafe_proto: true,
                unsafe_regexp: true,
                unsafe_undefined: true,
            },
            mangle: {
                safari10: true,
            },
            format: {
                comments: false,
            },
        },
        rollupOptions: {
            output: {
                // Optimized code splitting for better caching
                manualChunks: {
                    // Keep working chunks that don't cause issues
                    swiper: ['swiper'],
                    alpine: ['alpinejs'],
                    tippy: ['tippy.js'],
                },
                // Optimize chunk file names for better caching
                chunkFileNames: 'assets/js/[name]-[hash].js',
                entryFileNames: 'assets/js/[name]-[hash].js',
                assetFileNames: ({ name }) => {
                    if (/\.(gif|jpe?g|png|svg|webp|avif)$/.test(name ?? '')) {
                        return 'assets/images/[name]-[hash][extname]';
                    }
                    if (/\.css$/.test(name ?? '')) {
                        return 'assets/css/[name]-[hash][extname]';
                    }
                    if (/\.(woff2?|eot|ttf|otf)$/.test(name ?? '')) {
                        return 'assets/fonts/[name]-[hash][extname]';
                    }
                    return 'assets/[name]-[hash][extname]';
                },
            },
            // Tree-shaking optimizations
            treeshake: {
                moduleSideEffects: false,
                propertyReadSideEffects: false,
                tryCatchDeoptimization: false,
            },
        },
        // Performance settings for faster builds and smaller bundles
        chunkSizeWarningLimit: 1000,
        sourcemap: false,
        // Advanced CSS optimizations with lightningcss
        cssMinify: 'lightningcss',
        reportCompressedSize: false, // Faster builds
        // Asset inlining threshold for small assets
        assetsInlineLimit: 4096,
    },
    server: {
        // Development server optimizations
        hmr: {
            overlay: true
        },
        watch: {
            usePolling: false,
            interval: 100,
        },
    },
    // Enhanced asset optimization
    assetsInclude: ['**/*.webp', '**/*.avif', '**/*.png', '**/*.jpg', '**/*.jpeg', '**/*.svg', '**/*.ico'],
    
    // Advanced CSS preprocessing optimization
    css: {
        preprocessorOptions: {
            scss: {
                // Use modern Sass API (dart-sass) with performance options
                api: 'modern-compiler',
                charset: false,
            }
        },
        // Enhanced CSS processing
        devSourcemap: false,
    },
    // Optimize dependencies (exclude axios to avoid Node.js polyfill issues)
    optimizeDeps: {
        include: [
            'tippy.js',
            'alpinejs',
            'swiper',
        ],
        exclude: [
            'axios', // Avoid Node.js polyfill issues in build
        ],
    },
    // Performance optimizations
    esbuild: {
        // Optimize JavaScript compilation
        legalComments: 'none',
        // Remove all console logs in production
        drop: process.env.NODE_ENV === 'production' ? ['console', 'debugger'] : [],
        // Target modern browsers for better optimization
        target: 'es2020',
        // Minify identifiers
        minifyIdentifiers: true,
        minifySyntax: true,
        minifyWhitespace: true,
    },
    // Resolve optimizations
    resolve: {
        // Optimize module resolution
        dedupe: ['tippy.js', 'alpinejs'],
        // Prefer ES modules
        mainFields: ['module', 'main'],
        // Optimize extensions order
        extensions: ['.js', '.ts', '.jsx', '.tsx', '.json'],
    },
});
