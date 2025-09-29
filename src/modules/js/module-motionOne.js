import { animate, inView } from "motion";

/**
 * Animate navigation header elements on page load
 * Targets: .site-header__logo, .menu-header-nav li, .site-header__cta
 */
export const navHeader = () => {
  const navHeader = document.querySelector(".site-header__logo");
  const navItems = document.querySelectorAll(".menu-header-nav li");
  const navSections = document.querySelectorAll(".site-header__cta");

  // Only animate if at least one element exists
  if (!navHeader && !navItems.length && !navSections.length) return;

  try {
    const animations = [];

    // Logo animation
    if (navHeader) {
      animations.push(
        animate(navHeader,
          { opacity: [0, 1], y: ["1rem", "0"] },
          { duration: 0.5, delay: 0 }
        )
      );
    }

    // Navigation items with staggered animation
    if (navItems.length) {
      navItems.forEach((item, index) => {
        animations.push(
          animate(item,
            { opacity: [0, 1], y: ["-0.85rem", "0"] },
            { duration: 0.5, delay: index * 0.1 + 0.1 }
          )
        );
      });
    }

    // CTA sections with staggered animation
    if (navSections.length) {
      navSections.forEach((section, index) => {
        animations.push(
          animate(section,
            { opacity: [0, 1] },
            { duration: 0.5, delay: index * 0.1 + 0.2 }
          )
        );
      });
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
  const sections = document.querySelectorAll(".inview-container");

  if (!sections.length) return;

  try {
    sections.forEach((section) => {
      inView(section, ({ target }) => {
        const sectionItems = target.querySelectorAll(".inview-item");

        // Animate the container first
        animate(target,
          { opacity: [0, 1], y: ["1.5rem", "0"] },
          { delay: 0.1, duration: 0.6, easing: [0.17, 0.55, 0.55, 1] }
        );

        // Then animate child items with stagger
        if (sectionItems.length) {
          sectionItems.forEach((item, index) => {
            animate(item,
              { opacity: [0, 1], y: ["1.5rem", "0"] },
              {
                delay: 0.3 + (index * 0.1),
                duration: 0.6,
                easing: [0.17, 0.55, 0.55, 1]
              }
            );
          });
        }
      }, {
        amount: 0.3,
        margin: "-100px"  // Start animation 100px before element is visible
      });
    });
  } catch (error) {
    console.warn('InView animation failed:', error);
  }
};

/**
 * Animate elements on scroll with more control
 * Alternative to generalInView for specific scroll-triggered animations
 */
export const scrollAnimations = () => {
  const scrollElements = document.querySelectorAll("[data-scroll-animate]");

  if (!scrollElements.length) return;

  try {
    scrollElements.forEach((element) => {
      const animationType = element.dataset.scrollAnimate || "fadeUp";

      const animations = {
        fadeUp: { opacity: [0, 1], y: ["2rem", "0"] },
        fadeDown: { opacity: [0, 1], y: ["-2rem", "0"] },
        fadeLeft: { opacity: [0, 1], x: ["2rem", "0"] },
        fadeRight: { opacity: [0, 1], x: ["-2rem", "0"] },
        scale: { opacity: [0, 1], scale: [0.8, 1] },
        slideUp: { y: ["100%", "0"] },
        slideDown: { y: ["-100%", "0"] }
      };

      const animationProps = animations[animationType] || animations.fadeUp;

      inView(element, () => {
        animate(element, animationProps, {
          duration: 0.8,
          easing: [0.17, 0.55, 0.55, 1]
        });
      }, { amount: 0.3 });
    });
  } catch (error) {
    console.warn('Scroll animations failed:', error);
  }
};
