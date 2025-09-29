import Swup from 'swup';

/**
 * Initialize Swup for smooth page transitions
 * Handles WordPress theme integration with proper selectors
 */
export function initializeSwup() {
    // Check if Swup should be disabled (for admin, customizer, etc.)
    if (
        document.body.classList.contains('wp-admin') ||
        document.body.classList.contains('customize-support') ||
        window.location.search.includes('customize_messenger_channel')
    ) {
        return;
    }

    try {
        const swup = new Swup({
            // WordPress-specific containers
            containers: ['#swup', '#main', 'main', '.site-main'],

            // Animation classes
            animationSelector: '[class*="swup-transition-"]',

            // Cache settings for better performance
            cache: true,

            // Scroll behavior
            animateHistoryBrowsing: true,

            // Skip certain links
            linkSelector: 'a[href]:not([data-no-swup]):not([href^="#"]):not([href*="wp-admin"]):not([href*="wp-login"]):not([target="_blank"]):not([download])',

            // Form handling
            formSelector: 'form[data-swup-form]',

            // Debug mode (disable in production)
            debugMode: false
        });

        // Re-initialize modules after page transitions
        swup.on('contentReplaced', () => {
            reinitializeModules();
        });

        // Handle loading states
        swup.on('willReplaceContent', () => {
            document.body.classList.add('is-changing');
        });

        swup.on('contentReplaced', () => {
            document.body.classList.remove('is-changing');
        });

        // Handle scroll position
        swup.on('willReplaceContent', () => {
            // Store current scroll position
            window.swupScrollPosition = window.pageYOffset;
        });

        // Add page-specific classes
        swup.on('contentReplaced', () => {
            addPageClasses();
        });

        // Handle WordPress specific features
        handleWordPressIntegration(swup);

        console.log('Swup initialized successfully');
        return swup;

    } catch (error) {
        console.warn('Failed to initialize Swup:', error);
        return null;
    }
}

/**
 * Re-initialize modules that need to run after page transitions
 */
function reinitializeModules() {
    try {
        // Re-initialize Alpine.js components
        if (window.Alpine) {
            window.Alpine.initTree(document.body);
        }

        // Re-initialize motion animations
        if (window.generalInView) {
            window.generalInView();
        }
        if (window.scrollAnimations) {
            window.scrollAnimations();
        }

        // Re-initialize headroom if header exists
        const header = document.querySelector('.js-header');
        if (header && window.Headroom) {
            const headroom = new window.Headroom(header, {
                tolerance: 10,
                offset: 50,
            });
            headroom.init();
        }

        // Trigger custom re-initialization event
        document.dispatchEvent(new CustomEvent('swup:reinit'));

    } catch (error) {
        console.warn('Failed to reinitialize modules:', error);
    }
}

/**
 * Add page-specific body classes
 */
function addPageClasses() {
    try {
        // Remove old page classes
        document.body.className = document.body.className
            .replace(/page-id-\d+/g, '')
            .replace(/page-template-[\w-]+/g, '')
            .replace(/postid-\d+/g, '');

        // Add new classes based on current page
        const pathname = window.location.pathname;
        const slug = pathname.split('/').filter(Boolean).pop() || 'home';

        document.body.classList.add(`page-${slug}`);

        // Add template-specific classes if needed
        const main = document.querySelector('main');
        if (main) {
            const pageClass = main.className.match(/page-template-[\w-]+/);
            if (pageClass) {
                document.body.classList.add(pageClass[0]);
            }
        }

    } catch (error) {
        console.warn('Failed to add page classes:', error);
    }
}

/**
 * Handle WordPress-specific integrations
 */
function handleWordPressIntegration(swup) {
    // Handle WordPress admin bar
    if (document.getElementById('wpadminbar')) {
        document.body.classList.add('has-admin-bar');
    }

    // Handle WordPress customizer
    swup.on('contentReplaced', () => {
        // Trigger WordPress events
        if (window.wp && window.wp.customize) {
            window.wp.customize.trigger('preview-ready');
        }
    });

    // Handle contact forms and other WordPress forms
    swup.on('contentReplaced', () => {
        // Re-initialize Contact Form 7 if present
        if (window.wpcf7) {
            const forms = document.querySelectorAll('.wpcf7 > form');
            forms.forEach(form => {
                if (window.wpcf7.initForm) {
                    window.wpcf7.initForm(form);
                }
            });
        }

        // Re-initialize Gravity Forms if present
        if (window.gform && window.gform.initializeOnLoaded) {
            window.gform.initializeOnLoaded();
        }
    });
}

/**
 * Create smooth page transition animations
 */
/**
 * Create smooth page transition animations
 * Note: Transition styles are now in modules-utilities.css for better performance
 */
export function createSwupTransitions() {
    // Transition styles are now handled in CSS for better performance
    // This function can be used for dynamic transition adjustments if needed
    console.log('Swup transition styles loaded from CSS modules');
}