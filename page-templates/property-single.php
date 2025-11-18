<?php
/**
 * Template Name: Property Single
 * 
 * @package RentWord
 */

get_header();

$property_id = isset($_GET['id']) ? sanitize_text_field($_GET['id']) : '';

if (empty($property_id)) {
    ?>
    <main class="container my-5">
        <div class="alert alert-danger" role="alert">
            <p><?php esc_html_e('Propiedad no encontrada', 'rentword'); ?></p>
        </div>
    </main>
    <?php
    get_footer();
    exit;
}

$api = rentword_api();
$property = $api->get_property($property_id);

if (is_wp_error($property)) {
    ?>
    <main class="container my-5">
        <div class="alert alert-danger" role="alert">
            <p><?php echo esc_html($property->get_error_message()); ?></p>
        </div>
    </main>
    <?php
    get_footer();
    exit;
}

$title = rentword_get_property_field($property, 'title');
$price = rentword_get_property_field($property, 'price');
$location = rentword_get_property_field($property, 'location');
$description = rentword_get_property_field($property, 'description');
$bedrooms = rentword_get_property_field($property, 'bedrooms');
$bathrooms = rentword_get_property_field($property, 'bathrooms');
$area = rentword_get_property_field($property, 'area');
$property_type = rentword_get_property_field($property, 'property_type');

// Get host information from profiles
$host_phone = '';
$host_name = '';
$host_email = '';
$host_bio = '';
$host_image = '';

if (isset($property['profiles']) && is_array($property['profiles'])) {
    $profile = $property['profiles'];
    $host_phone = isset($profile['phone']) ? $profile['phone'] : '';
    $host_email = isset($profile['email']) ? $profile['email'] : '';
    $host_bio = isset($profile['bio']) ? $profile['bio'] : '';
    $host_image = isset($profile['profile_image_url']) ? $profile['profile_image_url'] : '';
    
    $host_name = isset($profile['first_name']) ? $profile['first_name'] : '';
    if (isset($profile['last_name'])) {
        $host_name .= ' ' . $profile['last_name'];
    }
    $host_name = trim($host_name);
}
?>

<main class="pb-5">
    <!-- Gallery -->
    <section class="py-4" style="background: #f9fafb;">
        <div class="container">
            <?php rentword_property_gallery($property); ?>
        </div>
    </section>
    
    <div class="container">
        <div class="row g-4 py-5">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Header -->
                <div class="vr-property-header mb-4">
                    <div class="d-flex gap-2 mb-3">
                        <?php if ($property_type): ?>
                        <span class="vr-badge vr-badge-primary">
                            <i class="bi bi-house-door me-1"></i>
                            <?php echo esc_html(ucfirst(str_replace('_', ' ', $property_type))); ?>
                        </span>
                        <?php endif; ?>
                        
                        <?php 
                        $guests = rentword_get_property_field($property, 'guests_capacity');
                        if ($guests): 
                        ?>
                        <span class="vr-badge vr-badge-light">
                            <i class="bi bi-people me-1"></i>
                            <?php echo esc_html($guests); ?> huéspedes
                        </span>
                        <?php endif; ?>
                    </div>
                    
                    <h1 class="vr-property-title mb-3"><?php echo esc_html($title); ?></h1>
                    
                    <?php if ($location): ?>
                    <div class="vr-property-location mb-4">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span><?php echo esc_html($location); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="vr-price-badge">
                        <div class="price-amount"><?php echo rentword_format_price($price); ?></div>
                        <div class="price-label">por noche</div>
                    </div>
                </div>
                
                <!-- Features Grid -->
                <div class="vr-features-grid mb-5">
                    <?php if ($bedrooms): ?>
                    <div class="vr-feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-door-closed"></i>
                        </div>
                        <div class="feature-content">
                            <div class="feature-value"><?php echo esc_html($bedrooms); ?></div>
                            <div class="feature-label"><?php esc_html_e('Habitaciones', 'rentword'); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($bathrooms): ?>
                    <div class="vr-feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-droplet"></i>
                        </div>
                        <div class="feature-content">
                            <div class="feature-value"><?php echo esc_html($bathrooms); ?></div>
                            <div class="feature-label"><?php esc_html_e('Baños', 'rentword'); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($area): ?>
                    <div class="vr-feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-rulers"></i>
                        </div>
                        <div class="feature-content">
                            <div class="feature-value"><?php echo esc_html($area); ?> m²</div>
                            <div class="feature-label"><?php esc_html_e('Superficie', 'rentword'); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php 
                    $beds = rentword_get_property_field($property, 'beds');
                    if ($beds): 
                    ?>
                    <div class="vr-feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-moon-stars"></i>
                        </div>
                        <div class="feature-content">
                            <div class="feature-value"><?php echo esc_html($beds); ?></div>
                            <div class="feature-label"><?php esc_html_e('Camas', 'rentword'); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Description -->
                <?php if ($description): ?>
                <div class="vr-section-card mb-4">
                    <h3 class="vr-section-title">
                        <i class="bi bi-text-paragraph me-2"></i>
                        <?php esc_html_e('Acerca de esta propiedad', 'rentword'); ?>
                    </h3>
                    <div class="vr-description-content">
                        <?php echo wp_kses_post(wpautop($description)); ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Host Information -->
                <?php if (!empty($host_name) || isset($property['profiles'])): ?>
                <div class="vr-section-card mb-4">
                    <h3 class="vr-section-title">
                        <i class="bi bi-person-circle me-2"></i>
                        <?php esc_html_e('Anfitrión', 'rentword'); ?>
                    </h3>
                    <div class="vr-host-card">
                        <div class="host-avatar">
                            <?php 
                            $host_image = isset($property['profiles']['profile_image_url']) ? $property['profiles']['profile_image_url'] : '';
                            if ($host_image): 
                            ?>
                                <img src="<?php echo esc_url($host_image); ?>" alt="<?php echo esc_attr($host_name); ?>">
                            <?php else: ?>
                                <div class="host-avatar-placeholder">
                                    <i class="bi bi-person"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="host-info">
                            <div class="host-name"><?php echo esc_html($host_name); ?></div>
                            <?php 
                            $host_bio = isset($property['profiles']['bio']) ? $property['profiles']['bio'] : '';
                            if ($host_bio): 
                            ?>
                                <div class="host-bio"><?php echo esc_html($host_bio); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Amenities -->
                <div class="mb-5 pb-4 border-bottom">
                    <?php rentword_property_amenities($property); ?>
                </div>
                
                <!-- Map -->
                <?php rentword_property_map($property); ?>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Booking Form -->
                <div class="card shadow-sm mb-4 sticky-top" style="top: 20px;">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-bold"><?php esc_html_e('Reservar Esta Propiedad', 'rentword'); ?></h5>
                    </div>
                    
                    <div class="card-body">
                        <form class="rw-booking-form" id="rw-booking-form">
                            <div class="mb-3">
                                <label for="check-in" class="form-label"><?php esc_html_e('Entrada', 'rentword'); ?></label>
                                <input type="date" id="check-in" name="check_in" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="check-out" class="form-label"><?php esc_html_e('Salida', 'rentword'); ?></label>
                                <input type="date" id="check-out" name="check_out" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="guests" class="form-label"><?php esc_html_e('Huéspedes', 'rentword'); ?></label>
                                <select id="guests" name="guests" class="form-select" required>
                                    <?php for ($i = 1; $i <= 10; $i++): ?>
                                        <option value="<?php echo $i; ?>"><?php echo $i; ?> <?php echo $i === 1 ? esc_html__('huésped', 'rentword') : esc_html__('huéspedes', 'rentword'); ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            
                            <div class="border-top pt-3 mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span><?php echo rentword_format_price($price); ?> x <span id="nights-count">0</span> <?php esc_html_e('noches', 'rentword'); ?></span>
                                    <span id="subtotal-price">$0</span>
                                </div>
                                <div class="d-flex justify-content-between fw-bold fs-5 text-danger">
                                    <strong><?php esc_html_e('Total', 'rentword'); ?></strong>
                                    <strong id="total-price">$0</strong>
                                </div>
                            </div>
                            
                            <?php if (!empty($host_phone)): ?>
                            <button type="button" 
                                    class="btn btn-success btn-lg w-100 mb-2" 
                                    id="whatsapp-reserve"
                                    data-price="<?php echo esc_attr($price); ?>"
                                    data-currency="<?php echo esc_attr(rentword_get_currency_symbol()); ?>"
                                    data-property="<?php echo esc_attr($title); ?>"
                                    data-phone="<?php echo esc_attr(preg_replace('/[^0-9]/', '', $host_phone)); ?>"
                                    disabled>
                                <i class="bi bi-whatsapp me-2"></i>
                                <?php esc_html_e('Reservar por WhatsApp', 'rentword'); ?>
                            </button>
                            <?php endif; ?>
                            
                            <button type="button" class="btn btn-outline-primary btn-lg w-100" id="contact-owner">
                                <?php esc_html_e('Contactar Propietario', 'rentword'); ?>
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Property Details -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-bold"><?php esc_html_e('Detalles de la Propiedad', 'rentword'); ?></h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between">
                                <span class="text-muted"><?php esc_html_e('ID', 'rentword'); ?></span>
                                <strong><?php echo esc_html($property_id); ?></strong>
                            </div>
                            <?php if ($property_type): ?>
                            <div class="list-group-item d-flex justify-content-between">
                                <span class="text-muted"><?php esc_html_e('Tipo', 'rentword'); ?></span>
                                <strong><?php echo esc_html($property_type); ?></strong>
                            </div>
                            <?php endif; ?>
                            <?php if ($bedrooms): ?>
                            <div class="list-group-item d-flex justify-content-between">
                                <span class="text-muted"><?php esc_html_e('Habitaciones', 'rentword'); ?></span>
                                <strong><?php echo esc_html($bedrooms); ?></strong>
                            </div>
                            <?php endif; ?>
                            <?php if ($bathrooms): ?>
                            <div class="list-group-item d-flex justify-content-between">
                                <span class="text-muted"><?php esc_html_e('Baños', 'rentword'); ?></span>
                                <strong><?php echo esc_html($bathrooms); ?></strong>
                            </div>
                            <?php endif; ?>
                            <?php if ($area): ?>
                            <div class="list-group-item d-flex justify-content-between">
                                <span class="text-muted"><?php esc_html_e('Área', 'rentword'); ?></span>
                                <strong><?php echo esc_html($area); ?> m²</strong>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Host Footer Section -->
        <?php if (!empty($host_name) || !empty($host_email) || !empty($host_phone)): ?>
        <div class="vr-host-footer mt-5">
            <div class="row align-items-center">
                <div class="col-md-2 text-center mb-3 mb-md-0">
                    <div class="host-footer-avatar">
                        <?php if ($host_image): ?>
                            <img src="<?php echo esc_url($host_image); ?>" alt="<?php echo esc_attr($host_name); ?>">
                        <?php else: ?>
                            <div class="host-footer-avatar-placeholder">
                                <i class="bi bi-person"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-7 mb-3 mb-md-0">
                    <h4 class="host-footer-title">Anfitrión</h4>
                    <?php if ($host_name): ?>
                        <div class="host-footer-name"><?php echo esc_html($host_name); ?></div>
                    <?php endif; ?>
                    <?php if ($host_bio): ?>
                        <p class="host-footer-bio"><?php echo esc_html($host_bio); ?></p>
                    <?php endif; ?>
                </div>
                <div class="col-md-3 text-md-end">
                    <?php if ($host_phone): ?>
                        <a href="tel:<?php echo esc_attr($host_phone); ?>" class="btn btn-outline-secondary btn-sm mb-2 w-100">
                            <i class="bi bi-telephone me-2"></i>Llamar
                        </a>
                    <?php endif; ?>
                    <?php if ($host_email): ?>
                        <a href="mailto:<?php echo esc_attr($host_email); ?>" class="btn btn-outline-secondary btn-sm w-100">
                            <i class="bi bi-envelope me-2"></i>Email
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</main>

<!-- Contact Modal -->
<div class="modal fade" id="contact-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php esc_html_e('Contactar al Propietario', 'rentword'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body">
                <form class="rw-contact-form" id="rw-contact-form">
                    <div class="mb-3">
                        <label for="contact-name" class="form-label"><?php esc_html_e('Nombre', 'rentword'); ?></label>
                        <input type="text" id="contact-name" name="name" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="contact-email" class="form-label"><?php esc_html_e('Email', 'rentword'); ?></label>
                        <input type="email" id="contact-email" name="email" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="contact-phone" class="form-label"><?php esc_html_e('Teléfono', 'rentword'); ?></label>
                        <input type="tel" id="contact-phone" name="phone" class="form-control">
                    </div>
                    
                    <div class="mb-3">
                        <label for="contact-message" class="form-label"><?php esc_html_e('Mensaje', 'rentword'); ?></label>
                        <textarea id="contact-message" name="message" class="form-control" rows="5" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <?php esc_html_e('Enviar Mensaje', 'rentword'); ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>
