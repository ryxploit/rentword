/**
 * RentWord Theme - Motion.js Animations
 * Modern web animations using Motion (https://motion.dev)
 * @version 1.0.0
 */

(function() {
    'use strict';

    // Check if Motion is available
    if (typeof Motion === 'undefined') {
        console.warn('[RentWord Motion] Motion.js not loaded');
        return;
    }

    const { animate, inView, scroll, stagger } = Motion;

    /**
     * Initialize all animations on DOM ready
     */
    function init() {
        initHeroAnimations();
        initPropertyCardAnimations();
        initScrollRevealAnimations();
        initHoverAnimations();
        initFilterAnimations();
        initButtonAnimations();
    }

    /**
     * Hero Section Animations
     */
    function initHeroAnimations() {
        const hero = document.querySelector('.rw-hero, .vr-hero');
        if (!hero) return;

        // Animate hero content on load
        const heroTitle = hero.querySelector('h1, .rw-hero-title, .vr-hero-title');
        const heroSubtitle = hero.querySelector('p, .rw-hero-subtitle, .vr-hero-subtitle');
        const heroButton = hero.querySelector('.btn, .rw-hero-btn');

        if (heroTitle) {
            animate(
                heroTitle,
                { opacity: [0, 1], y: [50, 0] },
                { duration: 0.8, easing: [0.22, 1, 0.36, 1] }
            );
        }

        if (heroSubtitle) {
            animate(
                heroSubtitle,
                { opacity: [0, 1], y: [30, 0] },
                { duration: 0.8, delay: 0.2, easing: [0.22, 1, 0.36, 1] }
            );
        }

        if (heroButton) {
            animate(
                heroButton,
                { opacity: [0, 1], scale: [0.9, 1] },
                { duration: 0.6, delay: 0.4, easing: [0.22, 1, 0.36, 1] }
            );
        }

        // Animate search form if present
        const searchForm = hero.querySelector('.rw-search-form, .vr-search-form');
        if (searchForm) {
            animate(
                searchForm,
                { opacity: [0, 1], y: [40, 0] },
                { duration: 0.8, delay: 0.3, easing: [0.22, 1, 0.36, 1] }
            );
        }
    }

    /**
     * Property Card Animations - Scroll reveal with stagger
     */
    function initPropertyCardAnimations() {
        const cards = document.querySelectorAll('.rw-property-card, .vr-card, .property-card');
        
        if (cards.length === 0) return;

        // Stagger animation for cards on scroll into view
        inView(
            cards,
            (info) => {
                animate(
                    info.target,
                    { opacity: [0, 1], y: [40, 0], scale: [0.95, 1] },
                    { duration: 0.6, easing: [0.22, 1, 0.36, 1] }
                );
            },
            { margin: "0px 0px -100px 0px" }
        );
    }

    /**
     * Scroll Reveal Animations - Generic sections
     */
    function initScrollRevealAnimations() {
        // Animate section headings
        const headings = document.querySelectorAll('.rw-section-title, .vr-section-title, h2.section-title');
        headings.forEach((heading) => {
            inView(
                heading,
                () => {
                    animate(
                        heading,
                        { opacity: [0, 1], x: [-30, 0] },
                        { duration: 0.7, easing: [0.22, 1, 0.36, 1] }
                    );
                },
                { margin: "0px 0px -50px 0px" }
            );
        });

        // Animate feature boxes
        const features = document.querySelectorAll('.rw-feature, .vr-feature, .feature-box');
        features.forEach((feature, index) => {
            inView(
                feature,
                () => {
                    animate(
                        feature,
                        { opacity: [0, 1], y: [30, 0] },
                        { duration: 0.6, delay: index * 0.1, easing: [0.22, 1, 0.36, 1] }
                    );
                },
                { margin: "0px 0px -80px 0px" }
            );
        });

        // Animate images on scroll
        const images = document.querySelectorAll('.rw-animate-image, .vr-card-image img');
        images.forEach((img) => {
            inView(
                img,
                () => {
                    animate(
                        img,
                        { opacity: [0, 1], scale: [1.1, 1] },
                        { duration: 0.8, easing: [0.22, 1, 0.36, 1] }
                    );
                },
                { margin: "0px 0px -100px 0px" }
            );
        });
    }

    /**
     * Hover Animations for Interactive Elements
     */
    function initHoverAnimations() {
        // Property cards hover effect
        const propertyCards = document.querySelectorAll('.rw-property-card, .vr-card');
        propertyCards.forEach((card) => {
            card.addEventListener('mouseenter', () => {
                animate(
                    card,
                    { y: -8, boxShadow: '0 12px 24px rgba(0,0,0,0.15)' },
                    { duration: 0.3, easing: [0.22, 1, 0.36, 1] }
                );
            });

            card.addEventListener('mouseleave', () => {
                animate(
                    card,
                    { y: 0, boxShadow: '0 4px 12px rgba(0,0,0,0.08)' },
                    { duration: 0.3, easing: [0.22, 1, 0.36, 1] }
                );
            });
        });

        // Button hover animations
        const buttons = document.querySelectorAll('.btn-vr-primary, .btn-vr-outline, .rw-btn');
        buttons.forEach((btn) => {
            btn.addEventListener('mouseenter', () => {
                animate(
                    btn,
                    { scale: 1.05 },
                    { duration: 0.2, easing: 'ease-out' }
                );
            });

            btn.addEventListener('mouseleave', () => {
                animate(
                    btn,
                    { scale: 1 },
                    { duration: 0.2, easing: 'ease-out' }
                );
            });
        });

        // Icon animations on hover
        const icons = document.querySelectorAll('.rw-icon, .vr-icon, .bi');
        icons.forEach((icon) => {
            const parent = icon.closest('a, button');
            if (!parent) return;

            parent.addEventListener('mouseenter', () => {
                animate(
                    icon,
                    { rotate: [0, 5, -5, 0] },
                    { duration: 0.4, easing: 'ease-in-out' }
                );
            });
        });
    }

    /**
     * Filter and Search Animations
     */
    function initFilterAnimations() {
        // Animate filter toggles
        const filterToggles = document.querySelectorAll('.rw-filter-toggle, .vr-filter-toggle');
        filterToggles.forEach((toggle) => {
            toggle.addEventListener('click', function() {
                const filterPanel = this.nextElementSibling;
                if (!filterPanel) return;

                const isExpanding = filterPanel.style.display === 'none' || !filterPanel.style.display;

                if (isExpanding) {
                    filterPanel.style.display = 'block';
                    animate(
                        filterPanel,
                        { opacity: [0, 1], height: [0, 'auto'] },
                        { duration: 0.4, easing: [0.22, 1, 0.36, 1] }
                    );
                } else {
                    animate(
                        filterPanel,
                        { opacity: [1, 0], height: ['auto', 0] },
                        { duration: 0.3, easing: [0.22, 1, 0.36, 1] }
                    ).finished.then(() => {
                        filterPanel.style.display = 'none';
                    });
                }
            });
        });

        // Animate search input focus
        const searchInputs = document.querySelectorAll('input[type="search"], .rw-search-input');
        searchInputs.forEach((input) => {
            input.addEventListener('focus', () => {
                animate(
                    input,
                    { scale: 1.02, boxShadow: '0 4px 12px rgba(255, 107, 0, 0.15)' },
                    { duration: 0.2 }
                );
            });

            input.addEventListener('blur', () => {
                animate(
                    input,
                    { scale: 1, boxShadow: '0 2px 8px rgba(0,0,0,0.08)' },
                    { duration: 0.2 }
                );
            });
        });
    }

    /**
     * Button Click Animations
     */
    function initButtonAnimations() {
        const animatedButtons = document.querySelectorAll('.btn, .rw-btn, .vr-btn');
        
        animatedButtons.forEach((btn) => {
            btn.addEventListener('click', function(e) {
                // Skip if it's a link that navigates away
                if (this.tagName === 'A' && !this.getAttribute('href').startsWith('#')) {
                    return;
                }

                // Pulse animation on click
                animate(
                    this,
                    { scale: [1, 0.95, 1] },
                    { duration: 0.3, easing: 'ease-in-out' }
                );
            });
        });

        // Back to top button animation
        const backToTop = document.querySelector('.rw-back-to-top');
        if (backToTop) {
            scroll(
                ({ y }) => {
                    if (y.progress > 0.1) {
                        animate(
                            backToTop,
                            { opacity: 1, scale: 1, display: 'block' },
                            { duration: 0.3 }
                        );
                    } else {
                        animate(
                            backToTop,
                            { opacity: 0, scale: 0.8 },
                            { duration: 0.3 }
                        ).finished.then(() => {
                            backToTop.style.display = 'none';
                        });
                    }
                }
            );
        }
    }

    /**
     * Parallax Effect on Hero Background
     */
    function initParallaxEffect() {
        const hero = document.querySelector('.rw-hero, .vr-hero');
        if (!hero) return;

        scroll(
            animate(hero, {
                backgroundPositionY: ['0%', '50%']
            }),
            { target: hero }
        );
    }

    /**
     * Initialize on DOM ready
     */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Export for debugging
    window.RentWordMotion = {
        init,
        animate,
        inView,
        scroll
    };

})();
