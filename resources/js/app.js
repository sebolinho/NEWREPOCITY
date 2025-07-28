import './bootstrap';
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';

// Critical performance optimizations
const performanceOptimizations = {
    // Service Worker for caching
    registerServiceWorker() {
        if ('serviceWorker' in navigator && 'production' === process.env.NODE_ENV) {
            navigator.serviceWorker.register('/sw.js', { scope: '/' })
                .then(registration => console.log('SW registered:', registration))
                .catch(error => console.log('SW registration failed:', error));
        }
    },

    // Preload critical resources
    preloadCriticalResources() {
        const criticalResources = [
            { href: 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap', as: 'style' },
            { href: '/css/app.css', as: 'style' },
            { href: '/js/app.js', as: 'script' }
        ];

        criticalResources.forEach(resource => {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.href = resource.href;
            link.as = resource.as;
            if (resource.as === 'style') {
                link.onload = function() { this.rel = 'stylesheet'; };
            }
            document.head.appendChild(link);
        });
    },

    // Font optimization
    optimizeFonts() {
        // Add font-display: swap to existing fonts
        const fontLinks = document.querySelectorAll('link[href*="fonts.googleapis.com"]');
        fontLinks.forEach(link => {
            if (!link.href.includes('display=swap')) {
                link.href += (link.href.includes('?') ? '&' : '?') + 'display=swap';
            }
        });
    },

    // Lazy load non-critical resources
    lazyLoadResources() {
        // Lazy load third-party scripts when user interacts
        let interacted = false;
        const interactionEvents = ['mousedown', 'touchstart', 'keydown', 'wheel'];
        
        const loadThirdPartyResources = () => {
            if (interacted) return;
            interacted = true;
            
            // Load analytics, ads, etc. only after user interaction
            this.loadAnalytics();
            this.loadSocialWidgets();
            
            interactionEvents.forEach(event => {
                document.removeEventListener(event, loadThirdPartyResources);
            });
        };
        
        interactionEvents.forEach(event => {
            document.addEventListener(event, loadThirdPartyResources, { passive: true, once: true });
        });
        
        // Fallback after 5 seconds
        setTimeout(loadThirdPartyResources, 5000);
    },

    loadAnalytics() {
        // Load analytics only when needed
        if (window.gtag) return;
        
        const script = document.createElement('script');
        script.async = true;
        script.src = 'https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID';
        document.head.appendChild(script);
    },

    loadSocialWidgets() {
        // Load social media widgets only when needed
        const socialContainers = document.querySelectorAll('[data-social-widget]');
        if (socialContainers.length > 0) {
            // Load social scripts here
        }
    }
};

// Lazy load tippy.js for better performance
let tippyLoaded = false;
const loadTippy = async () => {
    if (!tippyLoaded) {
        const tippy = await import('tippy.js');
        tippyLoaded = true;
        return tippy.default;
    }
};

import.meta.glob([
    '../img/**',
    '../fonts/**',
]);

// Enhanced lazy loading for images with WebP support
const observerOptions = {
    root: null,
    rootMargin: '50px',
    threshold: 0.1
};

const imageObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const img = entry.target;
            
            // WebP support detection and loading
            if (img.dataset.src) {
                const webpSrc = img.dataset.webp;
                const fallbackSrc = img.dataset.src;
                
                if (webpSrc && supportsWebP()) {
                    img.src = webpSrc;
                } else {
                    img.src = fallbackSrc;
                }
                
                img.removeAttribute('data-src');
                img.removeAttribute('data-webp');
                img.classList.remove('lazy');
                img.classList.add('loaded');
                
                // Fade in effect
                img.style.opacity = '0';
                img.onload = () => {
                    img.style.transition = 'opacity 0.3s ease';
                    img.style.opacity = '1';
                };
                
                observer.unobserve(img);
            }
        }
    });
}, observerOptions);

// WebP support detection
function supportsWebP() {
    if (typeof supportsWebP.result === 'undefined') {
        const canvas = document.createElement('canvas');
        canvas.width = 1;
        canvas.height = 1;
        supportsWebP.result = canvas.toDataURL('image/webp').indexOf('data:image/webp') === 0;
    }
    return supportsWebP.result;
}

// Critical loading optimization
document.addEventListener('DOMContentLoaded', () => {
    // Observe all lazy images
    const lazyImages = document.querySelectorAll('img[data-src]');
    lazyImages.forEach(img => {
        // Add loading placeholder
        if (!img.src) {
            img.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300"%3E%3Crect width="100%25" height="100%25" fill="%23f1f5f9"/%3E%3C/svg%3E';
        }
        imageObserver.observe(img);
    });
    
    // Initialize performance optimizations
    performanceOptimizations.preloadCriticalResources();
    performanceOptimizations.optimizeFonts();
    performanceOptimizations.lazyLoadResources();
    performanceOptimizations.registerServiceWorker();
});

Alpine.directive('clipboard', (el) => {
    let text = el.textContent

    el.addEventListener('click', () => {
        navigator.clipboard.writeText(text).then(() => {
            // Optional: Show success feedback
            console.log('Text copied to clipboard');
        }).catch(err => {
            console.error('Failed to copy text: ', err);
        });
    })
})

Livewire.start()

// Initialize tooltips lazily with performance optimization
const initTooltips = async () => {
    const tooltipElements = document.querySelectorAll('.tooltip');
    if (tooltipElements.length > 0) {
        const tippy = await loadTippy();
        tippy(tooltipElements, {
            theme: 'tailwind',
            animation: 'shift-toward',
            duration: 100,
            arrow: true,
            lazy: true, // Lazy initialization
            performance: true
        });
    }
};

// Performance: Use requestIdleCallback if available
if (window.requestIdleCallback) {
    requestIdleCallback(initTooltips, { timeout: 2000 });
} else {
    setTimeout(initTooltips, 0);
}

// Optimized light switcher with reduced DOM queries and better performance
const initLightSwitcher = () => {
    const lightSwitches = document.querySelectorAll('.light-switch');
    
    if (lightSwitches.length === 0) return;
    
    // Check initial state
    const isDarkMode = localStorage.getItem('dark-mode') === 'true';
    const htmlElement = document.documentElement;
    
    // Apply initial state without transition
    htmlElement.style.transition = 'none';
    if (isDarkMode) {
        htmlElement.classList.add('dark');
    } else {
        htmlElement.classList.remove('dark');
    }
    
    // Re-enable transitions after applying initial state
    requestAnimationFrame(() => {
        htmlElement.style.transition = '';
    });
    
    lightSwitches.forEach((lightSwitch, i) => {
        lightSwitch.checked = isDarkMode;
        
        lightSwitch.addEventListener('change', () => {
            const { checked } = lightSwitch;
            
            // Update all switches
            lightSwitches.forEach((el, n) => {
                if (n !== i) el.checked = checked;
            });
            
            // Apply theme with optimized DOM manipulation
            if (checked) {
                htmlElement.classList.add('dark');
                localStorage.setItem('dark-mode', 'true');
            } else {
                htmlElement.classList.remove('dark');
                localStorage.setItem('dark-mode', 'false');
            }
        });
    });
};

// Initialize light switcher immediately for better UX
initLightSwitcher();

// Performance monitoring and Core Web Vitals tracking
const performanceMonitoring = {
    init() {
        if (!('performance' in window)) return;
        
        this.trackWebVitals();
        this.trackCustomMetrics();
    },
    
    trackWebVitals() {
        // Track Core Web Vitals
        const observer = new PerformanceObserver((list) => {
            list.getEntries().forEach((entry) => {
                if (entry.entryType === 'largest-contentful-paint') {
                    console.log('LCP:', entry.renderTime || entry.loadTime);
                }
                if (entry.entryType === 'first-input') {
                    console.log('FID:', entry.processingStart - entry.startTime);
                }
                if (entry.entryType === 'layout-shift' && !entry.hadRecentInput) {
                    console.log('CLS:', entry.value);
                }
            });
        });
        
        try {
            observer.observe({ type: 'largest-contentful-paint', buffered: true });
            observer.observe({ type: 'first-input', buffered: true });
            observer.observe({ type: 'layout-shift', buffered: true });
        } catch (e) {
            // Fallback for older browsers
            console.log('Performance Observer not supported');
        }
    },
    
    trackCustomMetrics() {
        window.addEventListener('load', () => {
            setTimeout(() => {
                const perfData = performance.getEntriesByType('navigation')[0];
                const metrics = {
                    'DNS Lookup': perfData.domainLookupEnd - perfData.domainLookupStart,
                    'TCP Handshake': perfData.connectEnd - perfData.connectStart,
                    'Request': perfData.responseStart - perfData.requestStart,
                    'Response': perfData.responseEnd - perfData.responseStart,
                    'DOM Parse': perfData.domContentLoadedEventStart - perfData.responseEnd,
                    'Total Load Time': perfData.loadEventEnd - perfData.loadEventStart
                };
                
                console.table(metrics);
            }, 0);
        });
    }
};

// Initialize performance monitoring
performanceMonitoring.init();

// Optimize scroll performance
let scrollTimer = null;
const optimizeScrollPerformance = () => {
    document.addEventListener('scroll', () => {
        if (scrollTimer) return;
        
        scrollTimer = requestAnimationFrame(() => {
            // Handle scroll-based optimizations here
            scrollTimer = null;
        });
    }, { passive: true });
};

// Initialize scroll optimization
optimizeScrollPerformance();

// Resource preloading for critical paths
const preloadCriticalPaths = () => {
    const criticalPaths = [
        '/movies',
        '/tv-shows',
        '/trending'
    ];
    
    criticalPaths.forEach(path => {
        const link = document.createElement('link');
        link.rel = 'prefetch';
        link.href = path;
        document.head.appendChild(link);
    });
};

// Preload critical paths when idle
if (window.requestIdleCallback) {
    requestIdleCallback(preloadCriticalPaths, { timeout: 3000 });
} else {
    setTimeout(preloadCriticalPaths, 100);
}

