import Alpine from 'alpinejs';
import persist from '@alpinejs/persist';
import focus from '@alpinejs/focus';
import collapse from '@alpinejs/collapse';

// Alpine.js
window.Alpine = Alpine;

Alpine.plugin(
    [
        persist, 
        focus, 
        collapse
    ]
);
Alpine.start();