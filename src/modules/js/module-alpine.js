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
    // Prevent multiple initializations
    if (window.Alpine) return;

    try {
        Alpine.data('visibleNavHighlighter', moduleAlpineNavHighlighter);
        Alpine.data('scrollerComponent', () => ({
            init() {
                if (!window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
                    this.addAnimation();
                }
            },
            addAnimation() {
                if (!this.$refs.scroller || !this.$refs.scrollerInner) return;

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
    } catch (error) {
        console.warn('Failed to initialize Alpine.js:', error);
    }
}