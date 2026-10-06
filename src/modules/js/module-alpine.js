import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import collapse from '@alpinejs/collapse';
import moduleAlpineNavHighlighter from '/src/modules/js/module-alpine-navHighlighter.js';

Alpine.plugin(focus);
Alpine.plugin(collapse);

/**
 * Register the theme's Alpine components and magics, then start Alpine.
 * Small interactions use inline `x-data` in templates instead.
 */
export function initializeAlpine() {
    // Prevent multiple initializations
    if (window.Alpine) return;

    try {
        registerAlpineComponents();
        registerMagics();

        window.Alpine = Alpine;
        Alpine.start();
    } catch (error) {
        console.warn('Failed to initialize Alpine.js:', error);
    }
}

function registerAlpineComponents() {
    // Scroll-spy navigation: x-data="visibleNavHighlighter('h2, h3')"
    Alpine.data('visibleNavHighlighter', moduleAlpineNavHighlighter);

    // Infinite scroller (Featured Logos): duplicates the items once in view
    Alpine.data('scrollerComponent', () => ({
        isAnimated: false,

        init() {
            if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
                return;
            }

            this.setupIntersectionObserver();
        },

        setupIntersectionObserver() {
            if (!this.$refs.scroller) return;

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !this.isAnimated) {
                        this.addAnimation();
                    }
                });
            }, { threshold: 0.1 });

            observer.observe(this.$refs.scroller);
        },

        addAnimation() {
            if (!this.$refs.scroller || !this.$refs.scrollerInner || this.isAnimated) return;

            this.$refs.scroller.setAttribute("data-animated", true);
            const scrollerContent = Array.from(this.$refs.scrollerInner.children);

            scrollerContent.forEach((item) => {
                const duplicatedItem = item.cloneNode(true);
                duplicatedItem.setAttribute("aria-hidden", true);
                this.$refs.scrollerInner.appendChild(duplicatedItem);
            });

            this.isAnimated = true;
        }
    }));
}

function registerMagics() {
    // $clipboard(text): returns a promise (used by the brand guidelines colour swatches)
    Alpine.magic('clipboard', () => {
        return (text) => {
            if (navigator.clipboard) {
                return navigator.clipboard.writeText(text);
            }
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            return Promise.resolve();
        };
    });
}
