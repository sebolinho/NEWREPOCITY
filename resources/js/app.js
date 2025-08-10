import './bootstrap';
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';

// Lazy load tippy.js to reduce initial bundle size
const loadTippy = async () => {
    const { default: tippy } = await import('tippy.js');
    return tippy;
};

// Lazy load assets
import.meta.glob([
    '../img/**',
    '../fonts/**',
]);

Alpine.directive('clipboard', (el) => {
    let text = el.textContent

    el.addEventListener('click', () => {
        navigator.clipboard.writeText(text)
    })
})

Livewire.start()

// Initialize tooltips only when needed
document.addEventListener('DOMContentLoaded', async () => {
    const tooltipElements = document.querySelectorAll('.tooltip');
    if (tooltipElements.length > 0) {
        const tippy = await loadTippy();
        tippy('.tooltip', {
            theme: 'tailwind',
            animation: 'shift-toward',
            duration: 100,
            arrow: true,
        });
    }
});

// Light switcher - optimized to prevent layout shift
const initLightSwitcher = () => {
    const lightSwitches = document.querySelectorAll('.light-switch');

    if (lightSwitches.length > 0) {
        lightSwitches.forEach((lightSwitch, i) => {
            if (localStorage.getItem('dark-mode') === 'true') {
                lightSwitch.checked = true;
            }
            lightSwitch.addEventListener('change', () => {
                const { checked } = lightSwitch;
                lightSwitches.forEach((el, n) => {
                    if (n !== i) {
                        el.checked = checked;
                    }
                });
                if (lightSwitch.checked) {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('dark-mode', true);
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('dark-mode', false);
                }
            });
        });
    }
};

// Initialize after DOM is loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initLightSwitcher);
} else {
    initLightSwitcher();
}

