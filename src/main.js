// Vite HMR
if (import.meta.hot) {
	import.meta.hot.accept(() => {
		console.log('HMR update applied');
	});
}

// Core dependencies
import 'iconify-icon';
import '@tailwindplus/elements';
import { initializeAlpine } from './modules/js/module-alpine';
import { initializeHeadroom } from './modules/js/module-headroom';
import { initializeSwup, createSwupTransitions } from './modules/js/module-swup';
import { initMotionLoaded, navHeader, scrollAnimations } from './modules/js/module-motionOne';

// Styles
import './app.css';

// Auto-import component styles
import.meta.glob([
    '../templates/**/*.css',
    '../templates/**/*.scss'
], { eager: true });

// Initialize modules when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    try {
        // Initialize transition styles first
        createSwupTransitions();

        // Initialize core modules
        initializeAlpine();
        initializeHeadroom();
        initializeSwup();

        // Initialize animations (inview uses native CSS scroll-driven animations)
        initMotionLoaded();
        navHeader();
        scrollAnimations();

        // Store for Swup re-initialization
        window.scrollAnimations = scrollAnimations;

    } catch (error) {
        console.warn('Failed to initialize some modules:', error);
    }
});