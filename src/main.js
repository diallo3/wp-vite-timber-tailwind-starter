// Vite HMR
if (import.meta.hot) {
	import.meta.hot.accept(() => {
		console.log('HMR update applied');
	});
}

// Core dependencies
import 'iconify-icon';
import { initializeAlpine } from './modules/js/module-alpine';
import { initializeHeadroom } from './modules/js/module-headroom';
import { initializeSwup } from './modules/js/module-swup';
import { navHeader, generalInView, scrollAnimations } from './modules/js/module-motionOne';

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
        initializeAlpine();
        initializeHeadroom();
        initializeSwup();
        navHeader();
        generalInView();
        scrollAnimations();
    } catch (error) {
        console.warn('Failed to initialize some modules:', error);
    }
});