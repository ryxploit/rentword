<?php
/**
 * Template Name: Home Page
 * 
 * @package RentWord
 */

get_header();

// Get hero section settings from customizer
$hero_title = get_theme_mod('rentword_hero_title', __('Encuentra tu Hogar Perfecto', 'rentword'));
$hero_subtitle = get_theme_mod('rentword_hero_subtitle', __('Miles de propiedades vacacionales en los mejores destinos', 'rentword'));
$hero_image = get_theme_mod('rentword_hero_image', '');
$cta_text = get_theme_mod('rentword_cta_text', __('Explorar Propiedades', 'rentword'));
$properties_per_page = get_theme_mod('rentword_properties_per_page', 12);
?>

<main>
    <!-- Hero Section -->
    <section class="rw-hero" style="<?php if ($hero_image) { $hero_img = wp_get_attachment_image_src($hero_image, 'full'); if ($hero_img) echo 'background-image: url(' . esc_url($hero_img[0]) . ');'; } ?>background-size: cover; background-position: center; padding: 100px 0; position: relative; min-height: 400px;">
        <?php if ($hero_image): ?>
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, rgba(94, 191, 179, 0.8), rgba(45, 106, 106, 0.8));"></div>
        <?php else: ?>
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, #5EBFB3, #2D6A6A);"></div>
        <?php endif; ?>
        <div class="container" style="position: relative; z-index: 1;">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h1 class="display-3 fw-bold mb-4" style="color: #fff; text-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                        <?php echo esc_html($hero_title); ?>
                    </h1>
                    <p class="lead mb-4" style="color: #fff; font-size: 1.5rem; text-shadow: 0 1px 2px rgba(0,0,0,0.2);">
                        <?php echo esc_html($hero_subtitle); ?>
                    </p>
                    <a href="#properties" class="btn btn-lg" style="background: #fff; color: var(--rw-primary); padding: 15px 40px; border-radius: 30px; font-weight: 600; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: all 0.3s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(0,0,0,0.2)';" onmouseout="this.style.transform=''; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)';">
                        <?php echo esc_html($cta_text); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>

<?php
$api = rentword_api();
$all_properties = $api->get_properties(array('per_page' => 999));

if (is_wp_error($all_properties)) {
    ?>
    <div class="container my-5">
        <div class="alert alert-warning shadow-sm" role="alert">
            <h4 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Configuración Requerida</h4>
            <p class="mb-3">Para mostrar propiedades, necesitas configurar tu API primero.</p>
            <hr>
            <p class="mb-0">
                <a href="<?php echo admin_url('admin.php?page=rentword-settings'); ?>" class="btn btn-warning">
                    <i class="bi bi-gear"></i> Ir a Configuración
                </a>
            </p>
        </div>
        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title">Pasos para Configurar</h5>
                <ol>
                    <li>Ve a <strong>Apariencia → RentWord Settings</strong></li>
                    <li>Ingresa tu <strong>URL de API</strong> y <strong>API Key</strong></li>
                    <li>Haz clic en <strong>"Probar Conexión"</strong> para verificar</li>
                    <li>Configura el <strong>mapeo de campos</strong> en Field Mapping</li>
                </ol>
            </div>
        </div>
    </div>
    <?php
    get_footer();
    exit;
}

if (empty($all_properties)) {
    ?>
    <div class="container my-5">
        <div class="alert alert-info shadow-sm" role="alert">
            <h4 class="alert-heading"><i class="bi bi-info-circle"></i> No hay propiedades disponibles</h4>
            <p class="mb-0">Tu API está configurada correctamente pero no hay propiedades para mostrar. Verifica que tu API tenga datos.</p>
        </div>
    </div>
    <?php
    get_footer();
    exit;
}

$all_properties = (array) $all_properties;

// Check if we have search parameters
$is_search = !empty($_GET['location']) || !empty($_GET['guests']) || !empty($_GET['checkin']) || !empty($_GET['checkout']);

// Apply search filters
if ($is_search && !empty($all_properties)) {
    $all_properties = array_filter($all_properties, function($property) {
        // Filter by location/destination
        if (!empty($_GET['location'])) {
            $location = rentword_get_property_field($property, 'location');
            $title = rentword_get_property_field($property, 'title');
            $search_term = sanitize_text_field($_GET['location']);
            
            if (stripos($location, $search_term) === false && stripos($title, $search_term) === false) {
                return false;
            }
        }
        
        // Filter by guests/capacity
        if (!empty($_GET['guests'])) {
            $guests_needed = intval($_GET['guests']);
            $capacity = rentword_get_property_field($property, 'guests') 
                     ?: rentword_get_property_field($property, 'capacity')
                     ?: rentword_get_property_field($property, 'max_guests');
            
            if ($capacity && $capacity < $guests_needed) {
                return false;
            }
        }
        
        return true;
    });
    
    // Re-index array
    $all_properties = array_values($all_properties);
}

// Get featured properties
$featured_properties = array_filter($all_properties, function($property) {
    return rentword_get_property_field($property, 'featured');
});

// Get recent properties (first 6)
$recent_properties = array_slice($all_properties, 0, 6);

$monterrey_properties = array_filter($all_properties, function($property) {
    $location = rentword_get_property_field($property, 'location');
    return stripos($location, 'Monterrey') !== false;
});

$cdmx_properties = array_filter($all_properties, function($property) {
    $location = rentword_get_property_field($property, 'location');
    return stripos($location, 'Ciudad de México') !== false || stripos($location, 'CDMX') !== false;
});

$parras_properties = array_filter($all_properties, function($property) {
    $location = rentword_get_property_field($property, 'location');
    return stripos($location, 'Parras') !== false;
});
?>

    <!-- Vacation Rental Properties Grid -->
    <section class="py-4" id="properties">
        <div class="container">
            <!-- Filters Bar -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <?php if ($is_search): ?>
                    <h2 class="mb-1" style="color: var(--vr-black); font-size: 1.75rem; font-weight: 600;">
                        <?php printf(esc_html__('%d alojamientos', 'rentword'), count($all_properties)); ?>
                    </h2>
                    <?php if (!empty($_GET['location'])): ?>
                    <p class="mb-0" style="color: var(--vr-foggy); font-size: 15px;">
                        <?php printf(esc_html__('en %s', 'rentword'), esc_html($_GET['location'])); ?>
                    </p>
                    <?php endif; ?>
                    <?php else: ?>
                    <p class="mb-0" style="color: var(--vr-dark-gray);">
                        <?php printf(esc_html__('Más de %s alojamientos', 'rentword'), count($all_properties)); ?>
                    </p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Properties Grid (Vacation Rental Style) -->
            <?php if (!empty($all_properties)): ?>
            <div class="vr-properties-grid">
                <?php foreach ($all_properties as $property): ?>
                    <?php rentword_property_card($property); ?>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <!-- No Results -->
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="bi bi-search" style="font-size: 4rem; color: var(--vr-light-gray);"></i>
                </div>
                <h3 style="color: var(--vr-dark-gray); font-weight: 600;"><?php esc_html_e('No se encontraron alojamientos', 'rentword'); ?></h3>
                <p style="color: var(--vr-foggy);"><?php esc_html_e('Intenta ajustar tu búsqueda o busca en otra ubicación', 'rentword'); ?></p>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-lg mt-3" style="background: var(--rw-primary); color: #fff; padding: 12px 30px; border-radius: 30px; font-weight: 600;">
                    <?php esc_html_e('Ver todas las propiedades', 'rentword'); ?>
                </a>
            </div>
            <?php endif; ?>
            
            <!-- Load More -->
            <?php if (count($all_properties) > 20): ?>
            <div class="text-center mt-5">
                <button class="btn-vr-outline">
                    <?php esc_html_e('Seguir explorando', 'rentword'); ?>
                </button>
            </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php get_footer(); ?>
