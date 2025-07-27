import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { viteStaticCopy } from 'vite-plugin-static-copy';
import { resolve } from 'path';

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
                },
                {
                    src: 'public/sw.js',
                    dest: ''
                }
            ]
        })
    ],
    build: {
        // Advanced code splitting for better caching
        rollupOptions: {
            output: {
                manualChunks: {
                    // Vendor chunk for external libraries
                    vendor: ['alpinejs', 'axios'],
                    // UI components chunk
                    ui: ['@alpinejs/collapse', 'tippy.js'],
                    // Swiper in its own chunk due to size
                    swiper: ['swiper'],
                    // Livewire in its own chunk
                    livewire: ['../../vendor/livewire/livewire/dist/livewire.esm']
                },
                // Optimize chunk naming for better caching
                chunkFileNames: (chunkInfo) => {
                    const facadeModuleId = chunkInfo.facadeModuleId 
                        ? chunkInfo.facadeModuleId.split('/').pop().replace(/\.\w+$/, '') 
                        : 'chunk';
                    return `js/${facadeModuleId}-[hash].js`;
                },
                entryFileNames: 'js/[name]-[hash].js',
                assetFileNames: (assetInfo) => {
                    const info = assetInfo.name.split('.');
                    const ext = info[info.length - 1];
                    if (/\.(css)$/.test(assetInfo.name)) {
                        return `css/[name]-[hash].${ext}`;
                    }
                    if (/\.(png|jpe?g|svg|gif|tiff|bmp|ico)$/i.test(assetInfo.name)) {
                        return `images/[name]-[hash].${ext}`;
                    }
                    if (/\.(woff2?|eot|ttf|otf)$/i.test(assetInfo.name)) {
                        return `fonts/[name]-[hash].${ext}`;
                    }
                    return `assets/[name]-[hash].${ext}`;
                }
            },
            // External dependencies to reduce bundle size
            external: (id) => {
                // Keep Livewire internal as it's critical
                if (id.includes('livewire')) return false;
                return false; // Include everything for now
            }
        },
        // Enable source maps for production debugging (can be disabled for smaller builds)
        sourcemap: process.env.NODE_ENV === 'development',
        // Advanced minification with terser
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: process.env.NODE_ENV === 'production',
                drop_debugger: true,
                pure_funcs: ['console.log', 'console.info'],
                // Remove unused code
                dead_code: true,
                // Optimize conditions
                conditionals: true,
                // Optimize booleans
                booleans: true,
                // Remove unused variables
                unused: true,
                // Merge variables
                join_vars: true,
                // Collapse single-use variables
                collapse_vars: true,
                // Optimize loops
                loops: true,
                // Optimize if statements
                if_return: true,
                // Inline simple functions
                inline: 2,
                // Optimize property access
                properties: true
            },
            mangle: {
                // Mangle all except reserved words
                reserved: ['process', 'global', 'window', 'document']
            },
            format: {
                // Remove comments
                comments: false,
                // Compact output
                beautify: false
            }
        },
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
        target: ['es2020', 'chrome80', 'firefox74', 'safari13'],
        // Module preload polyfill
        modulePreload: {
            polyfill: true
        }
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
            target: 'es2020',
            supported: {
                bigint: true
            }
        }
    },
    // Enhanced esbuild configuration
    esbuild: {
        target: 'es2020',
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
        plugins: []
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
    clearScreen: true,
    // Advanced build optimizations
    experimental: {
        // Enable build optimizations
        renderBuiltUrl: (filename, { hostType }) => {
            if (hostType === 'js') {
                return { js: `/${filename}` };
            } else {
                return { css: `/${filename}` };
            }
        }
    }
});
