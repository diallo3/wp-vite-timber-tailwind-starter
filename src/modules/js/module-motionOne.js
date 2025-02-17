import { animate, scroll, inView, stagger, timeline } from 'motion';

const navHeader = () => {
    const navHeader = document.querySelector('.site-header__logo');
    const navItems = document.querySelectorAll('.menu-header-nav li');
    const navSections = document.querySelectorAll('.site-header__cta');

    const navSequence = [
        // [navHeader, {opacity: ['0', '1'], y: ['1rem', '0']}, { duration: 0.5  }],
        [navItems, {opacity: ['0', '1'], y: ['-0.85rem', '0']}, { delay: stagger(0.1), duration: 0.5 }],
        [navSections, {opacity: ['0', '1']}, { delay: stagger(0.1), duration: 0.5 }, { at: '-0.85'}]
    ]
    
}




const generalInView = () => {
    const sections = document.querySelectorAll('.inview-container');

    inView(sections, ({ target }) => {

        const sectionItems = target.querySelectorAll('.inview-item');

        const sectionSequence = [
            [target, { opacity: ['0', '1'], y: ['1.5rem', '0'] }, { delay: 0.3, duration: 0.5, amount: 0.5, easing: [0.17, 0.55, 0.55, 1] }],
            [sectionItems, { opacity: ['0', '1'], y: ['1.5rem', '0'] }, { delay: stagger(0.1), duration: 0.5, easing: [0.17, 0.55, 0.55, 1] }]
        ]
        

    });
}



export { navHeader,  generalInView, };