import Alpine from 'alpinejs';
import persist from '@alpinejs/persist';
import focus from '@alpinejs/focus';
import collapse from '@alpinejs/collapse';
import moduleAlpineNavHighlighter from '/src/modules/js/module-alpine-navHighlighter.js';

Alpine.plugin(
        [
            persist, 
            focus, 
            collapse
        ]
);
   

export function initializeAlpine() {
    // Alpine.js
    if(!window.Alpine) {
        Alpine.data('visibleNavHighlighter', moduleAlpineNavHighlighter);
        window.Alpine = Alpine;
        Alpine.start();
    }
    
}