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
        Alpine.data('scrollerComponent', () => ({
            init() {
                if (!window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
                    this.addAnimation();
                }
            },
            addAnimation() {
                this.$refs.scroller.setAttribute("data-animated", true);

                const scrollerContent = Array.from(this.$refs.scrollerInner.children);

                scrollerContent.forEach((item) => {
                    const duplicatedItem = item.cloneNode(true);
                    duplicatedItem.setAttribute("aria-hidden", true);
                    this.$refs.scrollerInner.appendChild(duplicatedItem);
                });
            }
        }));
        window.Alpine = Alpine;
        Alpine.start();
    }

    // 
    
}