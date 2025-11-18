/**
 * RentWord Pro - Search & Filtering Module
 * Handles advanced property search with AJAX and jQuery
 * @version 2.0.0 - Bootstrap 5 + jQuery 3.7+
 */

(function($) {
    'use strict';

    var SearchModule = {
        debug: false,
        timeout: null,
        lastQuery: null,

        log: function(msg) {
            if (this.debug && console) {
                console.log('[RentWord Search] ' + msg);
            }
        },

        /**
         * Initialize search module
         */
        init: function() {
            this.log('Initializing Search Module');
            
            // Initialize search form
            if ($('.rw-search-form').length) {
                this.initSearchForm();
            }

            // Initialize search field listeners
            this.initFieldListeners();

            // Initialize filter handlers
            this.initFilterHandlers();

            // Load saved search parameters from URL
            this.loadSearchFromURL();
        },

        /**
         * Initialize search form
         */
        initSearchForm: function() {
            var self = this;

            // Search form submission
            $(document).on('submit', '.rw-search-form', function(e) {
                e.preventDefault();
                self.performSearch($(this));
            });

            // Checkbox auto-search
            $(document).on('change', '.rw-search-form input[type="checkbox"]', function() {
                self.debounceSearch();
            });

            // Select auto-search with delay
            $(document).on('change', '.rw-search-form select:not(.amenities)', function() {
                self.debounceSearch();
            });
        },

        /**
         * Initialize field change listeners
         */
        initFieldListeners: function() {
            var self = this;

            // Location field with autocomplete behavior
            $(document).on('input', '.rw-search-form input[name="location"]', function() {
                self.log('Location field changed');
                self.debounceSearch();
            });

            // Price range listeners with validation
            $(document).on('change', '.rw-search-form input[name="min_price"], .rw-search-form input[name="max_price"]', function() {
                self.log('Price range changed');
                
                if (self.validatePriceRange()) {
                    self.debounceSearch();
                }
            });

            // Bedrooms/bathrooms listeners
            $(document).on('change', '.rw-search-form input[name="bedrooms"], .rw-search-form input[name="bathrooms"]', function() {
                self.log('Bedrooms/bathrooms changed');
                self.debounceSearch();
            });
        },

        /**
         * Initialize filter handlers
         */
        initFilterHandlers: function() {
            var self = this;

            // Clear all filters
            $(document).on('click', '.rw-clear-filters', function(e) {
                e.preventDefault();
                self.log('Clearing all filters');
                self.clearAllFilters();
            });

            // Remove single filter
            $(document).on('click', '.rw-remove-filter', function(e) {
                e.preventDefault();
                var filterType = $(this).data('filter-type');
                self.log('Removing filter: ' + filterType);
                self.removeFilter(filterType);
            });

            // Sort options - handle both select and dropdown
            $(document).on('change', '.rw-sort-select', function() {
                var sortBy = $(this).val();
                self.log('Sorting by: ' + sortBy);
                self.setSortOrder(sortBy);
                self.performSearch();
            });
            
            $(document).on('click', '.rw-sort-select', function(e) {
                e.preventDefault();
                var sortBy = $(this).data('sort');
                self.log('Sorting by: ' + sortBy);
                
                // Update dropdown text
                var sortText = $(this).text();
                $('#sortDropdown').html('<i class="bi bi-sort-down"></i> ' + sortText);
                
                // Set sort and search
                $('input[name="sort_by"]').val(sortBy);
                self.performSearch();
            });
            
            // View toggle
            $(document).on('change', 'input[name="view-toggle"]', function() {
                var view = $(this).val();
                self.log('Changing view to: ' + view);
                self.toggleView(view);
            });
        },

        /**
         * Debounce search to avoid too many requests
         */
        debounceSearch: function() {
            var self = this;
            
            clearTimeout(this.timeout);
            this.timeout = setTimeout(function() {
                self.performSearch();
            }, 500);
        },

        /**
         * Validate price range
         */
        validatePriceRange: function() {
            var $form = $('.rw-search-form');
            var minPrice = parseFloat($form.find('input[name="min_price"]').val()) || 0;
            var maxPrice = parseFloat($form.find('input[name="max_price"]').val()) || 999999;

            if (minPrice > maxPrice) {
                this.showError('El precio mínimo no puede ser mayor que el máximo.');
                $form.find('input[name="min_price"]').val('');
                return false;
            }

            return true;
        },

        /**
         * Collect all search parameters from form
         */
        collectSearchParams: function($form) {
            if (!$form) {
                $form = $('.rw-search-form').length ? $('.rw-search-form') : null;
            }

            if (!$form) {
                return {};
            }

            var params = {
                action: 'rentword_search_properties',
                nonce: rentwordData.nonce,
                location: $form.find('input[name="location"]').val() || '',
                min_price: $form.find('input[name="min_price"]').val() || '',
                max_price: $form.find('input[name="max_price"]').val() || '',
                bedrooms: $form.find('select[name="bedrooms"]').val() || '',
                bathrooms: $form.find('select[name="bathrooms"]').val() || '',
                property_type: $form.find('select[name="property_type"]').val() || '',
                sort_by: $form.find('select[name="sort_by"]').val() || 'featured',
                paged: 1,
                amenities: []
            };

            // Collect selected amenities
            $form.find('input[name="amenities[]"]:checked').each(function() {
                params.amenities.push($(this).val());
            });

            this.log('Collected params: ' + JSON.stringify(params));
            return params;
        },

        /**
         * Perform search with current parameters
         */
        performSearch: function($form) {
            var self = this;
            var params = this.collectSearchParams($form);

            // Check if we're on the properties page
            if ($('.rw-properties-grid').length === 0) {
                this.log('Not on properties page, redirecting...');
                this.redirectToProperties(params);
                return;
            }

            this.log('Performing search...');
            this.executeSearch(params);
        },

        /**
         * Execute AJAX search
         */
        executeSearch: function(params) {
            var self = this;
            var $grid = $('.rw-properties-grid');
            var $loader = $('.rw-search-loader');

            // Show loader
            if ($loader.length === 0) {
                $grid.before('<div class="rw-search-loader text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Buscando...</span></div></div>');
                $loader = $('.rw-search-loader');
            }
            $loader.show();

            $.ajax({
                url: rentwordData.ajaxUrl,
                type: 'POST',
                data: params,
                success: function(response) {
                    self.log('Search successful');
                    
                    if (response.success) {
                        $grid.html(response.data.html);
                        
                        // Update filters display
                        self.updateActiveFilters(params);
                        
                        // Update pagination
                        if (response.data.pagination) {
                            self.updatePagination(response.data.pagination);
                        }
                        
                        // Update results count
                        if (response.data.total) {
                            self.updateResultsCount(response.data.total);
                        }
                        
                        // Smooth scroll to results
                        $('html, body').animate({
                            scrollTop: $grid.offset().top - 150
                        }, 300);
                        
                        // Store current query
                        window.currentQuery = params;
                    } else {
                        self.showError(response.data.message || 'No se encontraron propiedades.');
                        $grid.html('<div class="alert alert-info">No se encontraron propiedades que coincidan con tu búsqueda.</div>');
                    }
                },
                error: function(xhr, status, error) {
                    self.log('Search error: ' + error);
                    self.showError('Error en la búsqueda. Por favor, intenta de nuevo.');
                    console.error('Search error:', xhr, status, error);
                },
                complete: function() {
                    $loader.fadeOut(300);
                }
            });
        },

        /**
         * Redirect to properties page with search params in URL
         */
        redirectToProperties: function(params) {
            var queryString = $.param(params);
            var propertiesUrl = rentwordData.propertiesUrl || window.location.origin + '/properties';
            
            this.log('Redirecting to: ' + propertiesUrl + '?' + queryString);
            window.location.href = propertiesUrl + '?' + queryString;
        },

        /**
         * Load search parameters from URL
         */
        loadSearchFromURL: function() {
            var self = this;
            var params = new URLSearchParams(window.location.search);
            
            if (params.toString().length === 0) return;

            this.log('Loading search from URL');
            
            var $form = $('.rw-search-form');
            if ($form.length === 0) return;

            // Populate form fields from URL params
            params.forEach(function(value, key) {
                if (key === 'amenities') {
                    $form.find('input[name="amenities[]"][value="' + value + '"]').prop('checked', true);
                } else if (key === 'action' || key === 'nonce') {
                    return;
                } else {
                    $form.find('[name="' + key + '"]').val(value);
                }
            });

            // Perform search with loaded params
            this.performSearch($form);
        },

        /**
         * Update active filters display
         */
        updateActiveFilters: function(params) {
            var $filterDisplay = $('.rw-active-filters');
            if ($filterDisplay.length === 0) return;

            var filters = [];

            if (params.location) filters.push({ type: 'location', label: 'Ubicación: ' + params.location });
            if (params.min_price || params.max_price) {
                var priceLabel = '$' + (params.min_price || '0') + ' - $' + (params.max_price || '∞');
                filters.push({ type: 'price', label: 'Precio: ' + priceLabel });
            }
            if (params.bedrooms) filters.push({ type: 'bedrooms', label: params.bedrooms + ' dormitorios' });
            if (params.bathrooms) filters.push({ type: 'bathrooms', label: params.bathrooms + ' baños' });
            if (params.property_type) filters.push({ type: 'property_type', label: 'Tipo: ' + params.property_type });

            if (filters.length === 0) {
                $filterDisplay.html('');
                return;
            }

            var html = '<div class="rw-filters-list mb-3">';
            html += '<strong>Filtros activos:</strong> ';
            
            filters.forEach(function(filter) {
                html += '<span class="badge bg-primary me-2">' + filter.label + 
                        ' <button type="button" class="btn-close btn-close-white ms-1 rw-remove-filter" data-filter-type="' + filter.type + '"></button>' +
                        '</span>';
            });
            
            html += ' <a href="#" class="text-primary fw-semibold rw-clear-filters">Limpiar todo</a></div>';
            $filterDisplay.html(html);
        },

        /**
         * Remove specific filter
         */
        removeFilter: function(filterType) {
            var $form = $('.rw-search-form');

            switch(filterType) {
                case 'location':
                    $form.find('input[name="location"]').val('');
                    break;
                case 'price':
                    $form.find('input[name="min_price"]').val('');
                    $form.find('input[name="max_price"]').val('');
                    break;
                case 'bedrooms':
                    $form.find('select[name="bedrooms"]').val('');
                    break;
                case 'bathrooms':
                    $form.find('select[name="bathrooms"]').val('');
                    break;
                case 'property_type':
                    $form.find('select[name="property_type"]').val('');
                    break;
            }

            this.performSearch($form);
        },

        /**
         * Clear all filters
         */
        clearAllFilters: function() {
            var $form = $('.rw-search-form');
            $form[0].reset();
            $form.find('input[type="checkbox"]').prop('checked', false);
            
            this.performSearch($form);
        },

        /**
         * Set sort order
         */
        setSortOrder: function(sortBy) {
            $('input[name="sort_by"]').val(sortBy);
        },

        /**
         * Update pagination
         */
        updatePagination: function(pagination) {
            var $pagination = $('.rw-pagination');
            if (!$pagination.length) return;

            var html = '<nav><ul class="pagination justify-content-center">';

            if (pagination.prev_page) {
                html += '<li class="page-item"><a class="page-link rw-pagination-link" href="#" data-page="' + pagination.prev_page + '">← Anterior</a></li>';
            }

            for (var i = 1; i <= pagination.total_pages; i++) {
                if (i === pagination.current_page) {
                    html += '<li class="page-item active"><span class="page-link">' + i + '</span></li>';
                } else {
                    html += '<li class="page-item"><a class="page-link rw-pagination-link" href="#" data-page="' + i + '">' + i + '</a></li>';
                }
            }

            if (pagination.next_page) {
                html += '<li class="page-item"><a class="page-link rw-pagination-link" href="#" data-page="' + pagination.next_page + '">Siguiente →</a></li>';
            }

            html += '</ul></nav>';
            $pagination.html(html);

            // Add pagination link handler
            var self = this;
            $(document).on('click', '.rw-pagination-link', function(e) {
                e.preventDefault();
                var page = $(this).data('page');
                self.log('Going to page: ' + page);
                
                var params = self.collectSearchParams();
                params.paged = page;
                self.executeSearch(params);
            });
        },

        /**
         * Update results count
         */
        updateResultsCount: function(total) {
            var $count = $('.rw-results-count');
            if ($count.length) {
                $count.text(total + ' propiedades encontradas');
            }
        },
        
        /**
         * Toggle view (grid/list)
         */
        toggleView: function(view) {
            var $grid = $('.rw-properties-grid');
            
            if (view === 'list') {
                $grid.removeClass('row').addClass('d-flex flex-column');
                $grid.find('.property-card-wrapper').removeClass('col-md-6 col-lg-4').addClass('mb-3');
            } else {
                $grid.removeClass('d-flex flex-column').addClass('row g-4');
                $grid.find('.property-card-wrapper').removeClass('mb-3').addClass('col-md-6 col-lg-4');
            }
        },

        /**
         * Show error message
         */
        showError: function(message) {
            var $alertContainer = $('.rw-search-alerts');
            
            if ($alertContainer.length === 0) {
                $alertContainer = $('<div class="rw-search-alerts"></div>').insertBefore('.rw-properties-grid');
            }

            var html = '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                       message +
                       '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                       '</div>';

            $alertContainer.html(html);

            setTimeout(function() {
                $alertContainer.find('.alert').fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);
        },

        /**
         * Show success message
         */
        showSuccess: function(message) {
            var $alertContainer = $('.rw-search-alerts');
            
            if ($alertContainer.length === 0) {
                $alertContainer = $('<div class="rw-search-alerts"></div>').insertBefore('.rw-properties-grid');
            }

            var html = '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                       message +
                       '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                       '</div>';

            $alertContainer.html(html);

            setTimeout(function() {
                $alertContainer.find('.alert').fadeOut(function() {
                    $(this).remove();
                });
            }, 3000);
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        SearchModule.init();
        
        // Initialize Flatpickr for date range
        if (typeof flatpickr !== 'undefined' && $('#rw-daterange').length) {
            flatpickr('#rw-daterange', {
                mode: 'range',
                dateFormat: 'Y-m-d',
                minDate: 'today',
                locale: 'es',
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2) {
                        var checkin = selectedDates[0];
                        var checkout = selectedDates[1];
                        
                        // Format dates
                        var formatDate = function(date) {
                            var year = date.getFullYear();
                            var month = String(date.getMonth() + 1).padStart(2, '0');
                            var day = String(date.getDate()).padStart(2, '0');
                            return year + '-' + month + '-' + day;
                        };
                        
                        $('#rw-checkin').val(formatDate(checkin));
                        $('#rw-checkout').val(formatDate(checkout));
                    }
                },
                onReady: function(selectedDates, dateStr, instance) {
                    // Load dates from hidden inputs if they exist
                    var checkin = $('#rw-checkin').val();
                    var checkout = $('#rw-checkout').val();
                    
                    if (checkin && checkout) {
                        instance.setDate([checkin, checkout]);
                    }
                }
            });
        }
    });

    // Export for global access
    window.RentWordSearch = SearchModule;

})(jQuery);

/**
 * Guest Counter Functions
 * Standalone functions for guest counter (not jQuery dependent)
 */
function rwChangeGuests(change) {
    var input = document.getElementById('rw-guests');
    if (!input) return;
    
    var currentValue = parseInt(input.value) || 1;
    var newValue = currentValue + change;
    
    // Limit between 1 and 16 guests
    if (newValue < 1) newValue = 1;
    if (newValue > 16) newValue = 16;
    
    input.value = newValue;
    
    // Update button states
    var minusBtn = input.previousElementSibling;
    var plusBtn = input.nextElementSibling;
    
    if (minusBtn && minusBtn.classList.contains('rw-counter-btn')) {
        minusBtn.disabled = (newValue <= 1);
    }
    
    if (plusBtn && plusBtn.classList.contains('rw-counter-btn')) {
        plusBtn.disabled = (newValue >= 16);
    }
}

// Initialize guest counter button states on page load
document.addEventListener('DOMContentLoaded', function() {
    var guestInput = document.getElementById('rw-guests');
    if (guestInput) {
        var currentValue = parseInt(guestInput.value) || 1;
        var minusBtn = guestInput.previousElementSibling;
        var plusBtn = guestInput.nextElementSibling;
        
        if (minusBtn && minusBtn.classList.contains('rw-counter-btn')) {
            minusBtn.disabled = (currentValue <= 1);
        }
        
        if (plusBtn && plusBtn.classList.contains('rw-counter-btn')) {
            plusBtn.disabled = (currentValue >= 16);
        }
    }
});
