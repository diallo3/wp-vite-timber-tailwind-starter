// Vite Stuff
// Accept HMR as per: https://vitejs.dev/guide/api-hmr.html
if (import.meta.hot) {
	import.meta.hot.accept(() => {
		console.log('HMR');
	});
}

// import JS files if applicable
import { initializeAlpine } from './modules/js/module-alpine';
import { initializeSwup } from './modules/js/module-swup';
import { generalInView } from './modules/js/module-motionOne';
import { initializeHeadroom } from './modules/js/module-headroom';

// import SCSS files if applicable
import '../src/app.css';

// glob import all css or scss files
import.meta.glob([
    '../templates/**/*.css'
], { eager: true });

// component imports
document.addEventListener('DOMContentLoaded', function() {
    initializeAlpine();
    initializeSwup();
    generalInView();
    initializeHeadroom();
});