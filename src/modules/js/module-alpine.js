import Alpine from 'alpinejs';
import persist from '@alpinejs/persist';
import focus from '@alpinejs/focus';
import collapse from '@alpinejs/collapse';
import moduleAlpineNavHighlighter from '/src/modules/js/module-alpine-navHighlighter.js';

// Register Alpine.js plugins
Alpine.plugin(persist);
Alpine.plugin(focus);
Alpine.plugin(collapse);

/**
 * Initialize Alpine.js with WordPress-optimized components
 * Alpine.js v3.15.0 - Modern reactive JavaScript framework
 */
export function initializeAlpine() {
    // Prevent multiple initializations
    if (window.Alpine) return;

    try {
        // Register custom Alpine components
        registerAlpineComponents();

        // Register Alpine stores for global state
        registerAlpineStores();

        // Set global Alpine configuration
        configureAlpine();

        // Make Alpine available globally and start
        window.Alpine = Alpine;
        Alpine.start();

        console.log('Alpine.js v3.15.0 initialized successfully');

    } catch (error) {
        console.warn('Failed to initialize Alpine.js:', error);
    }
}

/**
 * Register custom Alpine.js components
 */
function registerAlpineComponents() {
    // Navigation highlighter component
    Alpine.data('visibleNavHighlighter', moduleAlpineNavHighlighter);

    // Enhanced scroller component with performance optimizations
    Alpine.data('scrollerComponent', () => ({
        isAnimated: false,

        init() {
            // Respect user motion preferences
            if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
                return;
            }

            // Use Intersection Observer for better performance
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

            // Clone items for infinite scroll effect
            scrollerContent.forEach((item) => {
                const duplicatedItem = item.cloneNode(true);
                duplicatedItem.setAttribute("aria-hidden", true);
                this.$refs.scrollerInner.appendChild(duplicatedItem);
            });

            this.isAnimated = true;
        }
    }));

    // Modal component with focus management
    Alpine.data('modalComponent', () => ({
        open: false,
        previousFocus: null,

        init() {
            // Listen for escape key
            this.$watch('open', (value) => {
                if (value) {
                    this.openModal();
                } else {
                    this.closeModal();
                }
            });
        },

        openModal() {
            // Store current focus
            this.previousFocus = document.activeElement;

            // Prevent body scroll
            document.body.style.overflow = 'hidden';

            // Focus first focusable element in modal
            this.$nextTick(() => {
                const firstFocusable = this.$el.querySelector('[tabindex]:not([tabindex="-1"]), input:not([disabled]), button:not([disabled]), textarea:not([disabled]), select:not([disabled])');
                if (firstFocusable) {
                    firstFocusable.focus();
                }
            });
        },

        closeModal() {
            // Restore body scroll
            document.body.style.overflow = '';

            // Restore previous focus
            if (this.previousFocus) {
                this.previousFocus.focus();
            }
        },

        handleEscape(event) {
            if (event.key === 'Escape') {
                this.open = false;
            }
        }
    }));

    // Dropdown component with keyboard navigation
    Alpine.data('dropdownComponent', () => ({
        open: false,
        activeIndex: -1,
        items: [],

        init() {
            // Get all dropdown items
            this.items = Array.from(this.$refs.menu?.querySelectorAll('[role="menuitem"]') || []);
        },

        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.activeIndex = -1;
                this.$nextTick(() => {
                    if (this.items[0]) {
                        this.items[0].focus();
                        this.activeIndex = 0;
                    }
                });
            }
        },

        handleKeydown(event) {
            if (!this.open) return;

            switch (event.key) {
                case 'ArrowDown':
                    event.preventDefault();
                    this.activeIndex = Math.min(this.activeIndex + 1, this.items.length - 1);
                    this.items[this.activeIndex]?.focus();
                    break;
                case 'ArrowUp':
                    event.preventDefault();
                    this.activeIndex = Math.max(this.activeIndex - 1, 0);
                    this.items[this.activeIndex]?.focus();
                    break;
                case 'Escape':
                    this.open = false;
                    break;
            }
        }
    }));

    // Toast notification component
    Alpine.data('toastComponent', () => ({
        notifications: Alpine.$persist([]),

        addNotification(message, type = 'info', duration = 5000) {
            const id = Date.now().toString();
            const notification = { id, message, type, duration };

            this.notifications.push(notification);

            // Auto remove after duration
            if (duration > 0) {
                setTimeout(() => {
                    this.removeNotification(id);
                }, duration);
            }

            return id;
        },

        removeNotification(id) {
            this.notifications = this.notifications.filter(n => n.id !== id);
        }
    }));

    // Tabs component with ARIA support
    Alpine.data('tabsComponent', () => ({
        activeTab: 0,

        init() {
            // Set initial active tab from data attribute or URL hash
            const hash = window.location.hash.slice(1);
            const tabIndex = this.$refs.tabs?.querySelector(`[data-tab="${hash}"]`)?.dataset.index;
            if (tabIndex) {
                this.activeTab = parseInt(tabIndex);
            }
        },

        setActiveTab(index) {
            this.activeTab = index;

            // Update URL hash if tab has data-tab attribute
            const tab = this.$refs.tabs?.children[index];
            const tabId = tab?.dataset.tab;
            if (tabId) {
                window.history.replaceState(null, null, `#${tabId}`);
            }
        },

        handleKeydown(event, index) {
            switch (event.key) {
                case 'ArrowLeft':
                    event.preventDefault();
                    this.setActiveTab(Math.max(0, index - 1));
                    break;
                case 'ArrowRight':
                    event.preventDefault();
                    this.setActiveTab(Math.min(this.$refs.tabs.children.length - 1, index + 1));
                    break;
            }
        }
    }));
}

/**
 * Register Alpine.js stores for global state management
 */
function registerAlpineStores() {
    // Theme store for dark/light mode
    Alpine.store('theme', {
        mode: Alpine.$persist('system'),

        init() {
            // Apply theme on initialization
            this.applyTheme();
        },

        toggle() {
            this.mode = this.mode === 'dark' ? 'light' : 'dark';
            this.applyTheme();
        },

        applyTheme() {
            const isDark = this.mode === 'dark' ||
                         (this.mode === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);

            document.documentElement.classList.toggle('dark', isDark);
        }
    });

    // Navigation store for mobile menu state
    Alpine.store('navigation', {
        mobileMenuOpen: false,

        toggleMobileMenu() {
            this.mobileMenuOpen = !this.mobileMenuOpen;
            document.body.style.overflow = this.mobileMenuOpen ? 'hidden' : '';
        },

        closeMobileMenu() {
            this.mobileMenuOpen = false;
            document.body.style.overflow = '';
        }
    });
}

/**
 * Configure Alpine.js global settings
 */
function configureAlpine() {
    // Set CSP-compliant prefix for Alpine directives
    Alpine.prefix('x-');

    // Configure magic helpers
    Alpine.magic('clipboard', () => {
        return (text) => {
            if (navigator.clipboard) {
                return navigator.clipboard.writeText(text);
            }
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            return Promise.resolve();
        };
    });

    Alpine.magic('toast', () => {
        return (message, type = 'info', duration = 5000) => {
            // Dispatch custom event for toast notifications
            window.dispatchEvent(new CustomEvent('toast', {
                detail: { message, type, duration }
            }));
        };
    });
}