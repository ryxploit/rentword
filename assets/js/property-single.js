/**
 * Property Single Page JS
 */

document.addEventListener('DOMContentLoaded', function() {
    initWhatsAppBooking();
    initPropertyGallery();
});

function initWhatsAppBooking() {
    const whatsappBtn = document.getElementById('whatsapp-reserve');
    const checkInInput = document.getElementById('check-in');
    const checkOutInput = document.getElementById('check-out');
    const guestsInput = document.getElementById('guests');
    const nightsDisplay = document.getElementById('nights-count');
    const totalDisplay = document.getElementById('total-price');
    
    if (!whatsappBtn || !checkInInput || !checkOutInput) {
        return;
    }
    
    const pricePerNight = parseFloat(whatsappBtn.dataset.price) || 0;
    const currency = whatsappBtn.dataset.currency || '$';
    const propertyTitle = whatsappBtn.dataset.property || '';
    const hostPhone = whatsappBtn.dataset.phone || '';
    
    function updateBooking() {
        const checkIn = checkInInput.value;
        const checkOut = checkOutInput.value;
        
        if (checkIn && checkOut) {
            const date1 = new Date(checkIn);
            const date2 = new Date(checkOut);
            const diffTime = Math.abs(date2 - date1);
            const nights = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            if (nights > 0) {
                const total = nights * pricePerNight;
                nightsDisplay.textContent = nights;
                totalDisplay.textContent = currency + total.toLocaleString('es-MX');
                whatsappBtn.disabled = false;
            }
        }
    }
    
    checkInInput.addEventListener('change', updateBooking);
    checkOutInput.addEventListener('change', updateBooking);
    
    whatsappBtn.addEventListener('click', function() {
        const checkIn = checkInInput.value;
        const checkOut = checkOutInput.value;
        const guests = guestsInput ? guestsInput.value : 1;
        const nights = nightsDisplay.textContent;
        const total = totalDisplay.textContent;
        
        if (!checkIn || !checkOut) {
            alert('Por favor selecciona las fechas de entrada y salida');
            return;
        }
        
        if (!hostPhone || hostPhone.length < 10) {
            alert('Número de teléfono del anfitrión no disponible');
            return;
        }
        
        const checkInFormatted = new Date(checkIn).toLocaleDateString('es-MX', { 
            day: 'numeric', 
            month: 'long', 
            year: 'numeric' 
        });
        const checkOutFormatted = new Date(checkOut).toLocaleDateString('es-MX', { 
            day: 'numeric', 
            month: 'long', 
            year: 'numeric' 
        });
        
        const message = `Hola! Me interesa reservar la propiedad:\n\n` +
            `${propertyTitle}\n\n` +
            `Entrada: ${checkInFormatted}\n` +
            `Salida: ${checkOutFormatted}\n` +
            `Noches: ${nights}\n` +
            `Huéspedes: ${guests}\n\n` +
            `Total: ${total}\n\n` +
            `¿Está disponible?`;
        
        const whatsappUrl = `https://wa.me/${hostPhone}?text=${encodeURIComponent(message)}`;
        window.open(whatsappUrl, '_blank');
    });
}

function initPropertyGallery() {
    // Check if Swiper is loaded
    if (typeof Swiper === 'undefined') {
        console.warn('Swiper not loaded');
        return;
    }
    
    // Wait for DOM to be ready
    const mainGallery = document.querySelector('.rw-gallery-main');
    const thumbGallery = document.querySelector('.rw-gallery-thumbs');
    
    if (!mainGallery) {
        console.warn('Gallery element not found');
        return;
    }
    
    // Initialize thumbnails first if they exist
    let galleryThumbs = null;
    if (thumbGallery) {
        galleryThumbs = new Swiper('.rw-gallery-thumbs', {
            spaceBetween: 10,
            slidesPerView: 4,
            freeMode: true,
            watchSlidesProgress: true,
            breakpoints: {
                320: {
                    slidesPerView: 3,
                },
                768: {
                    slidesPerView: 4,
                },
                1024: {
                    slidesPerView: 5,
                }
            }
        });
    }
    
    // Initialize main gallery
    const galleryMain = new Swiper('.rw-gallery-main', {
        spaceBetween: 10,
        loop: false,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        pagination: {
            el: '.swiper-pagination',
            type: 'fraction',
        },
        thumbs: galleryThumbs ? {
            swiper: galleryThumbs,
        } : undefined,
    });
    
    // Lightbox
    const lightbox = document.getElementById('gallery-lightbox');
    if (!lightbox) return;
    
    const lightboxImg = lightbox.querySelector('img');
    const lightboxCounter = lightbox.querySelector('.lightbox-counter');
    const images = Array.from(document.querySelectorAll('.gallery-image')).map(img => 
        img.querySelector('img').src
    );
    let currentIndex = 0;
    
    function openLightbox(index) {
        currentIndex = index;
        lightboxImg.src = images[index];
        lightboxCounter.textContent = (index + 1) + ' / ' + images.length;
        lightbox.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeLightbox() {
        lightbox.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    function nextImage() {
        currentIndex = (currentIndex + 1) % images.length;
        openLightbox(currentIndex);
    }
    
    function prevImage() {
        currentIndex = (currentIndex - 1 + images.length) % images.length;
        openLightbox(currentIndex);
    }
    
    document.querySelectorAll('.gallery-image').forEach((img, index) => {
        img.addEventListener('click', () => openLightbox(index));
    });
    
    lightbox.querySelector('.lightbox-close').addEventListener('click', closeLightbox);
    lightbox.querySelector('.lightbox-next').addEventListener('click', nextImage);
    lightbox.querySelector('.lightbox-prev').addEventListener('click', prevImage);
    
    lightbox.addEventListener('click', function(e) {
        if (e.target === lightbox) closeLightbox();
    });
    
    document.addEventListener('keydown', function(e) {
        if (lightbox.style.display === 'flex') {
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowRight') nextImage();
            if (e.key === 'ArrowLeft') prevImage();
        }
    });
}
