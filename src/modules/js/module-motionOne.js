import { animate, inView, stagger } from "motion";

const EASE = [0.17, 0.55, 0.55, 1];

/**
 * Add a class to indicate Motion is loaded
 * This allows CSS to hide `.inview-item` / `.stagger-inview-item` until they animate in
 */
export const initMotionLoaded = () => {
    document.documentElement.classList.add('motion-loaded');
};

// Initialize when module loads (Motion is loaded if import succeeds)
initMotionLoaded();

/**
 * Animate navigation header elements on page load
 * Targets: .site-header__logo, top-level items in .site-header__center, items in .site-header__ctas
 */
export const navHeader = () => {
  const navLogo = document.querySelector(".site-header__logo");
  const navItems = document.querySelectorAll(".site-header__center menu > li");
  const navCtas = document.querySelectorAll(".site-header__ctas li");

  if (!navLogo && !navItems.length && !navCtas.length) return;

  try {
    if (navLogo) {
      animate(navLogo,
        { opacity: [0, 1], y: ["1rem", "0"] },
        { duration: 0.5 }
      );
    }

    if (navItems.length) {
      animate(navItems,
        { opacity: [0, 1], y: ["-0.85rem", "0"] },
        { duration: 0.5, delay: stagger(0.1, { startDelay: 0.1 }) }
      );
    }

    if (navCtas.length) {
      animate(navCtas,
        { opacity: [0, 1] },
        { duration: 0.5, delay: stagger(0.1, { startDelay: 0.2 }) }
      );
    }
  } catch (error) {
    console.warn('Navigation animation failed:', error);
  }
};

/**
 * Animate sections when they come into view
 * Targets: .inview-container elements and their .inview-item children
 */
export const generalInView = () => {
    const items = document.querySelectorAll(".inview-container .inview-item");

    if (!items.length) return;

    inView(items, (element) => {
        animate(
            element,
            { opacity: [0, 1], y: ["1.5rem", "0"] },
            { duration: 0.6, ease: EASE }
        );
    }, {
        amount: 0.3,
        margin: "-100px"
    });
}

export const staggerInView = () => {
    const containers = document.querySelectorAll(".stagger-inview-container");

    if (!containers.length) return;

    inView(containers, (element) => {
        const items = element.querySelectorAll(".stagger-inview-item");

        animate(
            items,
            { opacity: [0, 1], y: ["0.5rem", "0"] },
            { duration: 0.85, ease: EASE, delay: stagger(0.2) }
        );

        return () => animate(
            items,
            { opacity: 0, y: "-0.5rem" },
            { duration: 0.3 }
        );
    }, {
        amount: 0.3,
        margin: "-50px"
    });
};


/**
 * Animate elements on scroll with more control
 * Alternative to generalInView for specific scroll-triggered animations
 */
export const scrollAnimations = () => {
  const scrollElements = document.querySelectorAll("[data-scroll-animate]");

  if (!scrollElements.length) return;

  const animations = {
    fadeUp: { opacity: [0, 1], y: ["2rem", "0"] },
    fadeDown: { opacity: [0, 1], y: ["-2rem", "0"] },
    fadeLeft: { opacity: [0, 1], x: ["2rem", "0"] },
    fadeRight: { opacity: [0, 1], x: ["-2rem", "0"] },
    scale: { opacity: [0, 1], scale: [0.8, 1] },
    slideUp: { y: ["100%", "0"] },
    slideDown: { y: ["-100%", "0"] }
  };

  try {
    scrollElements.forEach((element) => {
      const animationProps = animations[element.dataset.scrollAnimate] || animations.fadeUp;

      inView(element, () => {
        animate(element, animationProps, { duration: 0.8, ease: EASE });
      }, { amount: 0.3 });
    });
  } catch (error) {
    console.warn('Scroll animations failed:', error);
  }
};
