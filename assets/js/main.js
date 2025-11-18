/**
 * RentWord Theme Main JavaScript - Vanilla JS (No jQuery)
 */

(function() {
    'use strict';

    // Helper functions
    const $ = (selector) => document.querySelector(selector);
    const $$ = (selector) => document.querySelectorAll(selector);
    
    const addClass = (el, className) => el && el.classList.add(className);
    const removeClass = (el, className) => el && el.classList.remove(className);
    const toggleClass = (el, className) => el && el.classList.toggle(className);
    const hasClass = (el, className) => el && el.classList.contains(className);

    /**
     * Mobile Menu
     */
    function initMobileMenu() {
        const menuToggle = $('.rw-menu-toggle');
        const mainNav = $('.rw-main-navigation');

        if (menuToggle) {
            menuToggle.addEventListener('click', function() {
                toggleClass(this, 'active');
                toggleClass(mainNav, 'active');
            });
        }

        // Close menu on outside click
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.rw-header-content')) {
                removeClass(menuToggle, 'active');
                removeClass(mainNav, 'active');
            }
        });
    }

    /**
     * Sticky Header
     */
    function initStickyHeader() {
        const header = $('.site-header') || $('.rw-vacation-rental-header');
        
        if (!header) return;

        let lastScroll = 0;

        window.addEventListener('scroll', function() {
            const currentScroll = window.pageYOffset;
            
            if (currentScroll > 100) {
                addClass(header, 'sticky');
            } else {
                removeClass(header, 'sticky');
            }
            
            lastScroll = currentScroll;
        }, { passive: true });
    }

    /**
     * Collections Slider
     */
    function initCollectionsSlider() {
        $$('[data-rw-slider]').forEach(function(wrapper) {
            const track = wrapper.querySelector('[data-slider-track]');
            if (!track) return;
            
            const prev = wrapper.querySelector('[data-slider-prev]');
            const next = wrapper.querySelector('[data-slider-next]');
            const card = track.querySelector('.rw-spotlight-card');
            let scrollStep = card ? card.getBoundingClientRect().width + 24 : 320;

            function updateControls() {
                if (!prev || !next) return;
                
                const maxScroll = track.scrollWidth - track.clientWidth - 5;
                prev.disabled = track.scrollLeft <= 0;
                next.disabled = track.scrollLeft >= maxScroll;
            }

            if (prev) {
                prev.addEventListener('click', function() {
                    track.scrollBy({ left: -scrollStep, behavior: 'smooth' });
                });
            }

            if (next) {
                next.addEventListener('click', function() {
                    track.scrollBy({ left: scrollStep, behavior: 'smooth' });
                });
            }

            track.addEventListener('scroll', updateControls, { passive: true });
            
            window.addEventListener('resize', function() {
                scrollStep = card ? card.getBoundingClientRect().width + 24 : 320;
                updateControls();
            });

            updateControls();
        });
    }

    /**
     * Filter Toggle
     */
    function initFilterToggle() {
        const toggleBtn = $('#rw-toggle-amenities');
        const amenitiesFilter = $('#rw-amenities-filter');
        
        if (toggleBtn && amenitiesFilter) {
            toggleBtn.addEventListener('click', function() {
                toggleClass(amenitiesFilter, 'active');
                toggleClass(this, 'active');
                
                // Simple slide toggle
                if (hasClass(amenitiesFilter, 'active')) {
                    amenitiesFilter.style.display = 'block';
                } else {
                    amenitiesFilter.style.display = 'none';
                }
            });
        }
    }

    /**
     * View Toggle (Grid/List)
     */
    function initViewToggle() {
        const viewBtns = $$('.rw-view-btn');
        const container = $('#rw-properties-container');
        
        viewBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const view = this.dataset.view;
                
                viewBtns.forEach(b => removeClass(b, 'active'));
                addClass(this, 'active');
                
                if (view === 'list') {
                    addClass(container, 'list-view');
                } else {
                    removeClass(container, 'list-view');
                }
            });
        });
    }

    /**
     * Smooth Scroll
     */
    function initSmoothScroll() {
        $$('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const target = $(this.getAttribute('href'));
                
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    /**
     * Lazy Load Images
     */
    function initLazyLoad() {
        const images = $$('img[data-src]');
        
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                        imageObserver.unobserve(img);
                    }
                });
            });

            images.forEach(img => imageObserver.observe(img));
        } else {
            // Fallback for older browsers
            images.forEach(img => {
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
            });
        }
    }

    /**
     * Form Validation
     */
    function initFormValidation() {
        $$('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                form.querySelectorAll('[required]').forEach(field => {
                    if (!field.value) {
                        isValid = false;
                        addClass(field, 'error');
                    } else {
                        removeClass(field, 'error');
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                }
            });
        });
    }

    /**
     * Back to Top Button
     */
    function initBackToTop() {
        let backToTop = $('#rw-back-to-top');
        
        if (!backToTop) {
            backToTop = document.createElement('button');
            backToTop.id = 'rw-back-to-top';
            backToTop.className = 'rw-btn rw-btn-primary';
            backToTop.innerHTML = '↑';
            backToTop.style.cssText = 'position:fixed;bottom:2rem;right:2rem;z-index:999;display:none;width:3rem;height:3rem;border-radius:50%;';
            document.body.appendChild(backToTop);
        }

        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTop.style.display = 'block';
            } else {
                backToTop.style.display = 'none';
            }
        }, { passive: true });

        backToTop.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    /**
     * Initialize Everything
     */
    function init() {
        initMobileMenu();
        initStickyHeader();
        initCollectionsSlider();
        initFilterToggle();
        initViewToggle();
        initSmoothScroll();
        initLazyLoad();
        initFormValidation();
        initBackToTop();
    }

    // Run on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
