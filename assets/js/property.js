/**
 * RentWord Theme - Property Details Module
 * Handles gallery, map, booking calculations, and interactions
 * @version 2.0.0 - Bootstrap 5 + jQuery 3.7+
 */

(function($) {
    'use strict';

    var PropertyModule = {
        debug: false,
        gallery: null,

        log: function(msg) {
            if (this.debug && console) {
                console.log('[RentWord Property] ' + msg);
            }
        },

        init: function() {
            this.log('Initializing Property Module');
            
            this.initGallery();
            this.initMap();
            this.initBooking();
            this.initReviews();
            this.initShare();
            this.initContact();
            this.initAmenities();
        },

        /**
         * Initialize gallery slider using Swiper
         */
        initGallery: function() {
            var self = this;
            var $gallery = $('.rw-gallery-slider');

            if ($gallery.length === 0 || typeof Swiper === 'undefined') {
                return;
            }

            this.log('Initializing gallery');

            this.gallery = new Swiper('.rw-gallery-slider', {
                slidesPerView: 1,
                spaceBetween: 0,
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                    dynamicBullets: true,
                },
                loop: true,
                keyboard: {
                    enabled: true,
                },
                on: {
                    slideChange: function() {
                        // Update active thumbnail
                        var activeIndex = this.realIndex;
                        $('.rw-gallery-thumb').removeClass('active');
                        $('.rw-gallery-thumb').eq(activeIndex).addClass('active');
                    }
                }
            });

            // Thumbnail click handler
            $(document).on('click', '.rw-gallery-thumb', function() {
                var index = $(this).data('index');
                self.gallery.slideToLoop(index);
            });

            // Click image to open lightbox
            $(document).on('click', '.rw-gallery-slider img', function() {
                self.openLightbox($(this).attr('src'));
            });
        },

        /**
         * Open lightbox for images
         */
        openLightbox: function(imgSrc) {
            this.log('Opening lightbox');

            var $lightbox = $('<div class="rw-lightbox d-flex align-items-center justify-content-center">' +
                '<img src="' + imgSrc + '" alt="Gallery" class="rw-lightbox-img" />' +
                '<button class="btn-close btn-close-white rw-lightbox-close" style="position:absolute;top:20px;right:20px;"></button>' +
                '</div>');

            $lightbox.css({
                'position': 'fixed',
                'top': 0,
                'left': 0,
                'width': '100%',
                'height': '100%',
                'background': 'rgba(0, 0, 0, 0.95)',
                'z-index': 9999,
                'cursor': 'pointer'
            });

            $('body').append($lightbox);

            $lightbox.on('click', function(e) {
                if ($(e.target).is('.rw-lightbox, .rw-lightbox-close')) {
                    $lightbox.fadeOut(300, function() {
                        $(this).remove();
                    });
                }
            });

            $(document).on('keydown', function(e) {
                if (e.key === 'Escape') {
                    $lightbox.fadeOut(300, function() {
                        $(this).remove();
                    });
                }
            });
        },

        /**
         * Initialize single property map
         */
        initMap: function() {
            var self = this;
            var $mapContainer = $('#rw-property-map');

            if ($mapContainer.length === 0 || typeof L === 'undefined') {
                return;
            }

            this.log('Initializing map');

            var lat = parseFloat($mapContainer.data('lat'));
            var lng = parseFloat($mapContainer.data('lng'));
            var title = $mapContainer.data('title') || 'Property Location';

            if (isNaN(lat) || isNaN(lng)) {
                return;
            }

            // Initialize map
            var map = L.map('rw-property-map').setView([lat, lng], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);

            // Add marker
            L.marker([lat, lng])
                .addTo(map)
                .bindPopup('<div class="p-2"><strong>' + title + '</strong></div>')
                .openPopup();
        },

        /**
         * Initialize booking calculations
         */
        initBooking: function() {
            var self = this;
            var $checkIn = $('#check-in');
            var $checkOut = $('#check-out');
            var $price = $('.rw-property-price-value');

            if ($checkIn.length === 0 || $checkOut.length === 0 || $price.length === 0) {
                return;
            }

            this.log('Initializing booking calculations');

            var pricePerNight = parseFloat($price.text().replace(/[^0-9.]/g, '')) || 0;

            function calculateTotal() {
                var checkIn = new Date($checkIn.val());
                var checkOut = new Date($checkOut.val());

                if (checkIn && checkOut && checkOut > checkIn) {
                    var nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
                    var subtotal = nights * pricePerNight;
                    var total = subtotal; // Could add fees here

                    $('#nights-count').text(nights);
                    $('#subtotal-price').text('$' + subtotal.toFixed(2));
                    $('#total-price').text('$' + total.toFixed(2));

                    self.log('Booking calculated: ' + nights + ' nights = $' + total.toFixed(2));
                } else {
                    $('#nights-count').text('0');
                    $('#subtotal-price').text('$0.00');
                    $('#total-price').text('$0.00');
                }
            }

            // Set min date to today
            var today = new Date().toISOString().split('T')[0];
            $checkIn.attr('min', today);
            $checkOut.attr('min', today);

            $checkIn.on('change', function() {
                var checkInDate = new Date($(this).val());
                checkInDate.setDate(checkInDate.getDate() + 1);
                var minCheckOut = checkInDate.toISOString().split('T')[0];
                $checkOut.attr('min', minCheckOut);
                calculateTotal();
            });

            $checkOut.on('change', calculateTotal);
        },

        /**
         * Initialize reviews section
         */
        initReviews: function() {
            this.log('Initializing reviews');

            // Add review form submission
            $(document).on('submit', '.rw-review-form', function(e) {
                e.preventDefault();
                var self = this;

                $.ajax({
                    url: rentwordData.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'rentword_submit_review',
                        nonce: rentwordData.nonce,
                        property_id: $('.rw-property-single').data('property-id'),
                        rating: $(this).find('[name="rating"]').val(),
                        title: $(this).find('[name="title"]').val(),
                        content: $(this).find('[name="content"]').val(),
                        name: $(this).find('[name="name"]').val(),
                        email: $(this).find('[name="email"]').val()
                    },
                    success: function(response) {
                        if (response.success) {
                            $('.rw-reviews-list').prepend(response.data.html);
                            $(self).trigger('reset');
                            var $alert = $('<div class="alert alert-success alert-dismissible fade show"><strong>Gracias!</strong> Tu reseña ha sido publicada. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
                            $('body').prepend($alert);
                            setTimeout(function() { $alert.remove(); }, 5000);
                        }
                    }
                });
            });

            // Rating stars interaction
            $(document).on('click', '.rw-star', function() {
                var $rating = $(this).closest('.rw-rating');
                var rating = $(this).data('rating');

                $rating.find('.rw-star').each(function() {
                    if ($(this).data('rating') <= rating) {
                        $(this).addClass('active');
                    } else {
                        $(this).removeClass('active');
                    }
                });

                $rating.find('[name="rating"]').val(rating);
            });
        },

        /**
         * Initialize share functionality
         */
        initShare: function() {
            this.log('Initializing share');

            $(document).on('click', '.rw-share-btn', function(e) {
                e.preventDefault();

                if (navigator.share) {
                    navigator.share({
                        title: document.title,
                        text: $('meta[name="description"]').attr('content'),
                        url: window.location.href
                    });
                } else {
                    // Fallback: copy URL
                    var $temp = $('<input />');
                    $('body').append($temp);
                    $temp.val(window.location.href).select();
                    document.execCommand('copy');
                    $temp.remove();

                    alert('Enlace copiado al portapapeles');
                }
            });
        },

        /**
         * Initialize contact form and modal
         */
        initContact: function() {
            var self = this;
            this.log('Initializing contact form');

            // Open contact modal
            $(document).on('click', '#contact-owner', function(e) {
                e.preventDefault();
                
                var contactModal = new bootstrap.Modal(document.getElementById('contact-modal'));
                contactModal.show();
            });

            // Submit contact form
            $(document).on('submit', '.rw-contact-form', function(e) {
                e.preventDefault();

                var $form = $(this);
                var $btn = $form.find('button[type="submit"]');

                $.ajax({
                    url: rentwordData.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'rentword_send_inquiry',
                        nonce: rentwordData.nonce,
                        property_id: $('#contact-modal').closest('body').find('[data-property-id]').data('property-id') || 
                                    $('.property-id').val() || 
                                    new URLSearchParams(window.location.search).get('id'),
                        name: $form.find('[name="name"]').val(),
                        email: $form.find('[name="email"]').val(),
                        message: $form.find('[name="message"]').val(),
                        phone: $form.find('[name="phone"]').val()
                    },
                    beforeSend: function() {
                        $btn.prop('disabled', true).text('Enviando...');
                    },
                    success: function(response) {
                        if (response.success) {
                            var $alert = $('<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>¡Éxito!</strong> ' + (response.data.message || 'Tu mensaje ha sido enviado.') + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
                            $form.before($alert);
                            $form.trigger('reset');
                            
                            setTimeout(function() {
                                var modal = bootstrap.Modal.getInstance(document.getElementById('contact-modal'));
                                if (modal) modal.hide();
                                $alert.remove();
                            }, 2000);
                        } else {
                            alert(response.data.message || 'Error al enviar');
                        }
                    },
                    error: function() {
                        alert('Error en la conexión');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('Enviar Mensaje');
                    }
                });
            });
        },

        /**
         * Initialize amenities display
         */
        initAmenities: function() {
            this.log('Initializing amenities');

            // Toggle amenities list
            $(document).on('click', '.rw-amenities-toggle', function(e) {
                e.preventDefault();

                var $list = $('.rw-amenities-list');
                $list.slideToggle(300);
                $(this).toggleClass('active');
            });
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        PropertyModule.init();
    });

    // Export for global access
    window.RentWordProperty = PropertyModule;

})(jQuery);
