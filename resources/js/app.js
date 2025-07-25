import './bootstrap';
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';

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

// Performance optimizations
// Lazy loading for images
const observerOptions = {
    root: null,
    rootMargin: '50px',
    threshold: 0.1
};

const imageObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const img = entry.target;
            if (img.dataset.src) {
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                img.classList.remove('lazy');
                observer.unobserve(img);
            }
        }
    });
}, observerOptions);

// Observe all lazy images
document.addEventListener('DOMContentLoaded', () => {
    const lazyImages = document.querySelectorAll('img[data-src]');
    lazyImages.forEach(img => imageObserver.observe(img));
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

// Initialize tooltips lazily
const initTooltips = async () => {
    const tooltipElements = document.querySelectorAll('.tooltip');
    if (tooltipElements.length > 0) {
        const tippy = await loadTippy();
        tippy(tooltipElements, {
            theme: 'tailwind',
            animation: 'shift-toward',
            duration: 100,
            arrow: true,
        });
    }
};

// Performance: Use requestIdleCallback if available
if (window.requestIdleCallback) {
    requestIdleCallback(initTooltips);
} else {
    setTimeout(initTooltips, 0);
}

// Optimized light switcher with reduced DOM queries
const initLightSwitcher = () => {
    const lightSwitches = document.querySelectorAll('.light-switch');
    
    if (lightSwitches.length === 0) return;
    
    // Check initial state
    const isDarkMode = localStorage.getItem('dark-mode') === 'true';
    
    lightSwitches.forEach((lightSwitch, i) => {
        lightSwitch.checked = isDarkMode;
        
        lightSwitch.addEventListener('change', () => {
            const { checked } = lightSwitch;
            
            // Update all switches
            lightSwitches.forEach((el, n) => {
                if (n !== i) el.checked = checked;
            });
            
            // Apply theme
            const htmlElement = document.documentElement;
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

// Initialize light switcher
document.addEventListener('DOMContentLoaded', initLightSwitcher);

// Performance: Preload critical resources
const preloadCriticalResources = () => {
    // Preload fonts
    const fontPreload = document.createElement('link');
    fontPreload.rel = 'preload';
    fontPreload.href = 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap';
    fontPreload.as = 'style';
    fontPreload.onload = function() { this.rel = 'stylesheet'; };
    document.head.appendChild(fontPreload);
};

// Preload resources when idle
if (window.requestIdleCallback) {
    requestIdleCallback(preloadCriticalResources);
} else {
    setTimeout(preloadCriticalResources, 100);
}

// Performance monitoring (optional)
if ('performance' in window && 'measure' in window.performance) {
    window.addEventListener('load', () => {
        setTimeout(() => {
            const perfData = performance.getEntriesByType('navigation')[0];
            console.log('Page load time:', perfData.loadEventEnd - perfData.loadEventStart, 'ms');
        }, 0);
    });
}

