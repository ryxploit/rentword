<?php
/**
 * The footer template file
 *
 * @package RentWord
 */
?>

    </div><!-- #content -->

    <!-- Vacation Rental Footer -->
    <footer id="colophon" class="vr-footer">
        <div class="container">
            <!-- Footer Columns -->
            <div class="row">
                <?php for ($i = 1; $i <= 4; $i++): ?>
                <div class="col-12 col-sm-6 col-lg-3 vr-footer-section">
                    <?php 
                    // Check if this is column 4 (host information)
                    if ($i === 4) {
                        // Always show host information from customizer for column 4
                        echo '<h6 class="vr-footer-title">' . __('Contacto', 'rentword') . '</h6>';
                        
                        // Get host information from customizer
                        $host_name = get_theme_mod('rentword_host_name', '');
                        $host_email = get_theme_mod('rentword_host_email', get_option('admin_email'));
                        $host_phone = get_theme_mod('rentword_host_phone', '');
                        $host_whatsapp = get_theme_mod('rentword_host_whatsapp', '');
                        $host_bio = get_theme_mod('rentword_host_bio', '');
                        $host_image = get_theme_mod('rentword_host_image', '');
                        
                        // Display host image if set
                        if ($host_image) {
                            $image = wp_get_attachment_image_src($host_image, 'thumbnail');
                            if ($image) {
                                echo '<div class="vr-host-image mb-3">';
                                echo '<img src="' . esc_url($image[0]) . '" alt="' . esc_attr($host_name) . '" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">';
                                echo '</div>';
                            }
                        }
                        
                        if ($host_name) {
                            echo '<p class="vr-footer-text"><strong>' . esc_html($host_name) . '</strong></p>';
                        }
                        
                        // Display host bio if set
                        if ($host_bio) {
                            echo '<p class="vr-footer-text" style="font-size: 0.9em; margin-bottom: 1rem;">' . wp_kses_post($host_bio) . '</p>';
                        }
                        
                        if ($host_email) {
                            echo '<p class="vr-footer-text"><i class="bi bi-envelope"></i> <a href="mailto:' . esc_attr($host_email) . '">' . esc_html($host_email) . '</a></p>';
                        }
                        
                        if ($host_phone) {
                            echo '<p class="vr-footer-text"><i class="bi bi-telephone"></i> <a href="tel:' . esc_attr($host_phone) . '">' . esc_html($host_phone) . '</a></p>';
                        }
                        
                        if ($host_whatsapp) {
                            echo '<p class="vr-footer-text"><i class="bi bi-whatsapp"></i> <a href="https://wa.me/' . esc_attr(preg_replace('/[^0-9]/', '', $host_whatsapp)) . '" target="_blank">WhatsApp</a></p>';
                        }
                        
                        // Social media from customizer
                        $facebook = get_theme_mod('rentword_facebook', '');
                        $instagram = get_theme_mod('rentword_instagram', '');
                        $twitter = get_theme_mod('rentword_twitter', '');
                        
                        if ($facebook || $instagram || $twitter) {
                            echo '<div class="vr-footer-social-default mt-3">';
                            if ($facebook) echo '<a href="' . esc_url($facebook) . '" target="_blank" aria-label="Facebook"><i class="bi bi-facebook"></i></a>';
                            if ($instagram) echo '<a href="' . esc_url($instagram) . '" target="_blank" aria-label="Instagram"><i class="bi bi-instagram"></i></a>';
                            if ($twitter) echo '<a href="' . esc_url($twitter) . '" target="_blank" aria-label="Twitter"><i class="bi bi-twitter"></i></a>';
                            echo '</div>';
                        }
                    } elseif (is_active_sidebar('footer-' . $i)) {
                        // For columns 1-3, show widgets if available
                        dynamic_sidebar('footer-' . $i);
                    } else {
                        // Default content for columns 1-3
                        switch($i) {
                            case 1:
                                echo '<h6 class="vr-footer-title">' . __('Acerca de', 'rentword') . '</h6>';
                                echo '<p class="vr-footer-text">' . get_bloginfo('description') . '</p>';
                                break;
                            case 2:
                                echo '<h6 class="vr-footer-title">' . __('Enlaces Rápidos', 'rentword') . '</h6>';
                                echo '<ul class="vr-footer-links">';
                                echo '<li><a href="' . home_url('/properties') . '">' . __('Propiedades', 'rentword') . '</a></li>';
                                echo '<li><a href="' . home_url('/about') . '">' . __('Nosotros', 'rentword') . '</a></li>';
                                echo '<li><a href="' . home_url('/contact') . '">' . __('Contacto', 'rentword') . '</a></li>';
                                echo '</ul>';
                                break;
                            case 3:
                                echo '<h6 class="vr-footer-title">' . __('Legal', 'rentword') . '</h6>';
                                echo '<ul class="vr-footer-links">';
                                echo '<li><a href="#">' . __('Términos y Condiciones', 'rentword') . '</a></li>';
                                echo '<li><a href="#">' . __('Política de Privacidad', 'rentword') . '</a></li>';
                                echo '<li><a href="#">' . __('Política de Cancelación', 'rentword') . '</a></li>';
                                echo '</ul>';
                                break;
                        }
                    }
                    ?>
                </div>
                <?php endfor; ?>
            </div>

            <!-- Footer Bottom -->
            <div class="vr-footer-bottom">
                <div>
                    <span><?php echo wp_kses_post(get_theme_mod('rentword_copyright', sprintf(__('&copy; %s %s. Todos los derechos reservados.', 'rentword'), date('Y'), get_bloginfo('name')))); ?></span>
                    <?php if (get_theme_mod('rentword_show_credits', true)): ?>
                        <span class="ms-2">| <?php esc_html_e('Tema RentWord', 'rentword'); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </footer>
</div><!-- #page -->

<script>
// Vacation Rental Header Scroll Effect
document.addEventListener('DOMContentLoaded', function() {
    const header = document.querySelector('[data-header]');
    let lastScroll = 0;

    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset;
        
        if (currentScroll > 10) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
        
        lastScroll = currentScroll;
    });
});

// Favorite Toggle Function
function toggleFavorite(propertyId) {
    const btn = event.currentTarget;
    const icon = btn.querySelector('i');
    
    // Get favorites from localStorage
    let favorites = JSON.parse(localStorage.getItem('airbnb_favorites') || '[]');
    
    // Toggle favorite
    const index = favorites.indexOf(propertyId);
    if (index > -1) {
        favorites.splice(index, 1);
        btn.classList.remove('active');
    } else {
        favorites.push(propertyId);
        btn.classList.add('active');
    }
    
    // Save to localStorage
    localStorage.setItem('airbnb_favorites', JSON.stringify(favorites));
    
    // Animate
    btn.style.transform = 'scale(1.2)';
    setTimeout(() => {
        btn.style.transform = '';
    }, 200);
}

// Load favorites on page load
document.addEventListener('DOMContentLoaded', function() {
    const favorites = JSON.parse(localStorage.getItem('airbnb_favorites') || '[]');
    favorites.forEach(function(propertyId) {
        const btn = document.querySelector(`[data-property-id="${propertyId}"] .vr-favorite-btn`);
        if (btn) {
            btn.classList.add('active');
        }
    });
});
</script>

<?php wp_footer(); ?>

</body>
</html>
