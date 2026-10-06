// Vite HMR
if (import.meta.hot) {
	import.meta.hot.accept();
}

// Core dependencies
import 'iconify-icon';
import '@tailwindplus/elements';
import { initializeAlpine } from './modules/js/module-alpine';
import { initializeHeadroom } from './modules/js/module-headroom';
import { initMotionLoaded, navHeader, generalInView, staggerInView, scrollAnimations } from './modules/js/module-motionOne';

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
        // Initialize core modules
        initializeAlpine();
        initializeHeadroom();

        // Initialize animations
        initMotionLoaded();
        navHeader();
        generalInView();
        staggerInView();
        scrollAnimations();
    } catch (error) {
        console.warn('Failed to initialize some modules:', error);
    }
});