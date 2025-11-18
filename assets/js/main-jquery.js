/**
 * RentWord Theme - Main jQuery Scripts
 * Handles navigation, mobile menu, sticky header, sliders, and general interactions
 * @version 2.0.0 - Bootstrap 5 + jQuery 3.7+
 */

(function($) {
    'use strict';


    var debug = false;
    var log = function(msg) {
        if (debug && console) console.log('[RentWord] ' + msg);
    };

    // Mobile Menu Toggle
    var MobileMenu = {
        init: function() {
            log('Initializing Mobile Menu');
            
            $(document).on('click', '.navbar-toggler', function(e) {
                log('Mobile menu toggle clicked');
                var $collapse = $('#navbarContent');
                if ($collapse.hasClass('show')) {
                    $collapse.removeClass('show');
                } else {
                    $collapse.addClass('show');
                }
            });

            // Close menu when clicking on a link
            $(document).on('click', '.nav-link', function(e) {
                var $collapse = $('#navbarContent');
                if ($collapse.hasClass('show') && !$(this).data('bs-toggle')) {
                    $collapse.removeClass('show');
                }
            });
        }
    };

    // Sticky Header Behavior
    var StickyHeader = {
        lastScrollTop: 0,
        navbar: null,
        init: function() {
            log('Initializing Sticky Header');
            this.navbar = $('#masthead');
            
            if (this.navbar.length === 0) {
                log('Header not found');
                return;
            }

            var self = this;
            $(window).on('scroll', function() {
                self.handleScroll();
            });
        },
        handleScroll: function() {
            var scrollTop = $(window).scrollTop();
            
            // Add/remove shadow on scroll
            if (scrollTop > 10) {
                this.navbar.addClass('shadow-sm');
            } else {
                this.navbar.removeClass('shadow-sm');
            }
            
            this.lastScrollTop = scrollTop;
        }
    };

    // Search Form Handling
    var SearchForm = {
        init: function() {
            log('Initializing Search Form');
            
            // Advanced filters toggle
            $(document).on('click', '#rw-toggle-amenities', function(e) {
                e.preventDefault();
                log('Advanced filters toggled');
                
                var $icon = $(this).find('i');
                $('#rw-amenities-filter').slideToggle(300, function() {
                    if ($(this).is(':visible')) {
                        $icon.removeClass('bi-funnel').addClass('bi-funnel-fill');
                    } else {
                        $icon.removeClass('bi-funnel-fill').addClass('bi-funnel');
                    }
                });
            });
            
            // Legacy support
            $(document).on('click', '.rw-btn-link', function(e) {
                e.preventDefault();
                log('Advanced filters toggled (legacy)');
                
                $(this).toggleClass('active');
                $('.rw-amenities-filter').slideToggle(300);
            });

            // Search form submission
            $(document).on('submit', '.rw-search-form', function(e) {
                e.preventDefault();
                log('Search form submitted');
                SearchForm.handleSearch($(this));
            });

            // Price range handling
            var priceInputs = $('#min-price, #max-price');
            if (priceInputs.length > 0) {
                priceInputs.on('change', function() {
                    log('Price range changed');
                    SearchForm.validatePriceRange();
                });
            }
        },
        validatePriceRange: function() {
            var minPrice = parseFloat($('#min-price').val()) || 0;
            var maxPrice = parseFloat($('#max-price').val()) || 999999;

            if (minPrice > maxPrice) {
                alert('El precio mínimo no puede ser mayor que el máximo');
                $('#min-price').val('');
                return false;
            }
            return true;
        },
        handleSearch: function($form) {
            // Collect form data
            var formData = {
                action: 'rentword_search_properties',
                nonce: rentwordData.nonce,
                location: $form.find('[name="location"]').val(),
                min_price: $form.find('[name="min_price"]').val(),
                max_price: $form.find('[name="max_price"]').val(),
                bedrooms: $form.find('[name="bedrooms"]').val(),
                bathrooms: $form.find('[name="bathrooms"]').val(),
                property_type: $form.find('[name="property_type"]').val(),
                amenities: $form.find('[name="amenities[]"]:checked').map(function() {
                    return $(this).val();
                }).get()
            };

            log('Searching with: ' + JSON.stringify(formData));

            // Perform AJAX search
            $.ajax({
                url: rentwordData.ajaxUrl,
                type: 'POST',
                data: formData,
                success: function(response) {
                    log('Search successful');
                    if (response.success) {
                        // Update results if on properties page
                        if ($('.rw-properties-grid').length > 0) {
                            $('.rw-properties-grid').html(response.data.html);
                            $('html, body').animate({ scrollTop: $('.rw-properties-grid').offset().top - 100 }, 800);
                        } else {
                            // Redirect to properties page with filters
                            var params = new URLSearchParams(formData);
                            window.location.href = rentwordData.propertiesUrl + '?' + params.toString();
                        }
                    } else {
                        alert(response.data.message || 'Error en la búsqueda');
                    }
                },
                error: function(xhr, status, error) {
                    log('Search error: ' + error);
                    console.error('Search error:', xhr, status, error);
                }
            });
        }
    };

    // Lazy Load Images
    var LazyLoad = {
        init: function() {
            log('Initializing Lazy Load');
            
            if ('IntersectionObserver' in window) {
                this.useIntersectionObserver();
            } else {
                this.useFallback();
            }
        },
        useIntersectionObserver: function() {
            var options = {
                root: null,
                rootMargin: '50px',
                threshold: 0.01
            };

            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        var $img = $(entry.target);
                        var src = $img.data('src');
                        
                        if (src) {
                            $img.attr('src', src);
                            $img.removeAttr('data-src');
                            observer.unobserve(entry.target);
                            $img.addClass('lazy-loaded');
                        }
                    }
                });
            }, options);

            $('img[data-src]').each(function() {
                observer.observe(this);
            });
        },
        useFallback: function() {
            // Fallback for older browsers
            $('img[data-src]').each(function() {
                $(this).attr('src', $(this).data('src')).removeAttr('data-src');
            });
        }
    };

    // View Toggle (Grid/List)
    var ViewToggle = {
        init: function() {
            log('Initializing View Toggle');
            
            $(document).on('click', '.rw-view-btn', function() {
                var $btn = $(this);
                var view = $btn.data('view');
                
                log('View changed to: ' + view);
                
                // Update active button
                $('.rw-view-btn').removeClass('active');
                $btn.addClass('active');
                
                // Save preference
                localStorage.setItem('rentword_view', view);
                
                // Update grid/list class
                var $grid = $('.rw-properties-grid');
                $grid.removeClass('grid-view list-view');
                $grid.addClass(view + '-view');
            });

            // Load saved preference
            var savedView = localStorage.getItem('rentword_view') || 'grid-view';
            var $savedBtn = $('.rw-view-btn[data-view="' + savedView.replace('-view', '') + '"]');
            if ($savedBtn.length) {
                $savedBtn.click();
            }
        }
    };

    // Load More Functionality
    var LoadMore = {
        init: function() {
            log('Initializing Load More');
            
            $(document).on('click', '.rw-load-more', function(e) {
                e.preventDefault();
                log('Loading more properties');
                
                var $btn = $(this);
                var page = parseInt($btn.data('page')) || 1;
                
                LoadMore.loadMore(page, $btn);
            });
        },
        loadMore: function(page, $btn) {
            var $grid = $('.rw-properties-grid');
            
            $.ajax({
                url: rentwordData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'rentword_load_more',
                    nonce: rentwordData.nonce,
                    page: page,
                    query: window.currentQuery || {}
                },
                beforeSend: function() {
                    $btn.addClass('disabled').prop('disabled', true);
                    $btn.html('<span class="spinner-border spinner-border-sm me-2"></span>Cargando...');
                },
                success: function(response) {
                    if (response.success) {
                        $grid.append(response.data.html);
                        
                        if (response.data.has_more) {
                            $btn.data('page', page + 1);
                            $btn.html('Cargar más propiedades');
                            $btn.removeClass('disabled').prop('disabled', false);
                        } else {
                            $btn.remove();
                        }
                        
                        log('More properties loaded');
                    }
                },
                error: function() {
                    $btn.html('Error al cargar');
                    $btn.removeClass('disabled').prop('disabled', false);
                }
            });
        }
    };

    // Property Rating System
    var RatingSystem = {
        init: function() {
            log('Initializing Rating System');
            
            $(document).on('click', '.rw-star', function() {
                var $stars = $(this).closest('.rw-rating').find('.rw-star');
                var rating = $(this).data('rating');
                
                log('Rating: ' + rating);
                
                $stars.each(function() {
                    if ($(this).data('rating') <= rating) {
                        $(this).addClass('active');
                    } else {
                        $(this).removeClass('active');
                    }
                });
                
                $(this).closest('.rw-rating').data('rating', rating);
            });
        }
    };

    // Wishlist System
    var Wishlist = {
        init: function() {
            log('Initializing Wishlist');
            
            $(document).on('click', '.rw-wishlist-btn', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var propertyId = $btn.data('property-id');
                
                log('Wishlist toggled for property: ' + propertyId);
                
                $.ajax({
                    url: rentwordData.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'rentword_toggle_wishlist',
                        nonce: rentwordData.nonce,
                        property_id: propertyId
                    },
                    success: function(response) {
                        if (response.success) {
                            $btn.toggleClass('active');
                            
                            if (response.data.added) {
                                $btn.attr('title', 'Eliminar de mis favoritos');
                            } else {
                                $btn.attr('title', 'Agregar a mis favoritos');
                            }
                        }
                    }
                });
            });
        }
    };

    // Smooth Scroll to Sections
    var SmoothScroll = {
        init: function() {
            log('Initializing Smooth Scroll');
            
            $(document).on('click', 'a[href^="#"]', function(e) {
                var href = $(this).attr('href');
                if (href === '#' || href === '') return;
                
                var $target = $(href);
                if ($target.length === 0) return;
                
                e.preventDefault();
                log('Smooth scrolling to: ' + href);
                
                $('html, body').animate({
                    scrollTop: $target.offset().top - 100
                }, 800, function() {
                    window.location.hash = href;
                });
            });
        }
    };

    // Back to Top Button
    var BackToTop = {
        init: function() {
            log('Initializing Back to Top');
            
            var $btn = $('<button class="rw-back-to-top btn btn-primary rounded-circle" style="position:fixed;bottom:2rem;right:2rem;z-index:999;display:none;" title="Volver al inicio"><i class="bi bi-arrow-up"></i></button>');
            
            $('body').append($btn);
            
            $(window).on('scroll', function() {
                if ($(window).scrollTop() > 300) {
                    $btn.fadeIn();
                } else {
                    $btn.fadeOut();
                }
            });
            
            $btn.on('click', function() {
                $('html, body').animate({ scrollTop: 0 }, 800);
            });
        }
    };

    // Utility: Debounce
    var debounce = function(func, wait) {
        var timeout;
        return function() {
            var context = this, args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                func.apply(context, args);
            }, wait);
        };
    };

    // Initialize all components
    var App = {
        init: function() {
            log('=== RentWord Theme Initializing ===');
            
            MobileMenu.init();
            StickyHeader.init();
            SearchForm.init();
            LazyLoad.init();
            ViewToggle.init();
            LoadMore.init();
            RatingSystem.init();
            Wishlist.init();
            SmoothScroll.init();
            BackToTop.init();
            
            log('=== RentWord Theme Ready ===');
        }
    };

    // Document Ready
    $(document).ready(function() {
        App.init();
    });

    // Global namespace for debugging
    window.RentWord = {
        debug: function(flag) {
            debug = flag;
        },
        SearchForm: SearchForm,
        LoadMore: LoadMore,
        Wishlist: Wishlist
    };

})(jQuery);
