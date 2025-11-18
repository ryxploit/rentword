<?php
/**
 * Template Name: Properties Listing
 * 
 * @package RentWord
 */

get_header();

$api = rentword_api();
$per_page = get_theme_mod('rentword_properties_per_page', 12);
$paged = isset($_GET['paged']) ? intval($_GET['paged']) : 1;

// Check if we have search parameters from Vacation Rental search bar
$is_search = !empty($_GET['location']) || !empty($_GET['checkin']) || !empty($_GET['checkout']) || 
             !empty($_GET['guests']) || !empty($_GET['min_price']) || !empty($_GET['max_price']) || 
             !empty($_GET['bedrooms']) || !empty($_GET['bathrooms']) || !empty($_GET['property_type']) ||
             !empty($_GET['amenities']);

// Get all properties first
$all_properties = $api->get_properties(array('per_page' => 999));

if (is_wp_error($all_properties)) {
    ?>
    <main class="container my-5">
        <div class="alert alert-danger shadow-sm" role="alert">
            <h4 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Error de Conexión</h4>
            <p><?php echo esc_html($all_properties->get_error_message()); ?></p>
            <hr>
            <p class="mb-0">
                <a href="<?php echo admin_url('admin.php?page=rentword-settings'); ?>" class="btn btn-danger">
                    <i class="bi bi-gear"></i> Verificar Configuración
                </a>
            </p>
        </div>
    </main>
    <?php
    get_footer();
    exit;
}

$all_properties = is_array($all_properties) ? $all_properties : array();

// Apply client-side filters
if ($is_search && !empty($all_properties)) {
    $all_properties = array_filter($all_properties, function($property) {
        // Filter by location
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
        
        // Filter by bedrooms (derived from guests if not available)
        if (!empty($_GET['bedrooms'])) {
            $bedrooms = rentword_get_property_field($property, 'bedrooms');
            if ($bedrooms && $bedrooms < intval($_GET['bedrooms'])) {
                return false;
            }
        }
        
        // Filter by price
        if (!empty($_GET['min_price'])) {
            $price = rentword_get_property_field($property, 'price');
            if ($price && $price < floatval($_GET['min_price'])) {
                return false;
            }
        }
        
        if (!empty($_GET['max_price'])) {
            $price = rentword_get_property_field($property, 'price');
            if ($price && $price > floatval($_GET['max_price'])) {
                return false;
            }
        }
        
        return true;
    });
    
    // Re-index array
    $all_properties = array_values($all_properties);
}

$total = count($all_properties);
$properties = array_slice($all_properties, ($paged - 1) * $per_page, $per_page);
?>

<main>
    <!-- Vacation Rental Properties Listing -->
    <section class="py-4">
        <div class="container">
            <!-- Results Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1" style="color: var(--vr-black); font-size: 1.75rem; font-weight: 600;">
                        <?php
                        if ($is_search) {
                            printf(
                                esc_html__('%d alojamientos', 'rentword'),
                                $total
                            );
                        } else {
                            printf(
                                esc_html__('Más de %d alojamientos', 'rentword'),
                                $total
                            );
                        }
                        ?>
                    </h2>
                    <?php if ($is_search && !empty($_GET['location'])): ?>
                    <p class="mb-0" style="color: var(--vr-foggy); font-size: 15px;">
                        <?php printf(esc_html__('en %s', 'rentword'), esc_html($_GET['location'])); ?>
                    </p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Properties Grid (Vacation Rental Style) -->
            <?php if (!empty($properties)): ?>
            <div class="vr-properties-grid">
                <?php foreach ($properties as $property): ?>
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
                <p style="color: var(--vr-foggy);"><?php esc_html_e('Intenta ajustar tus filtros o busca en otra ubicación', 'rentword'); ?></p>
                <a href="<?php echo esc_url(home_url('/properties')); ?>" class="btn-vr-outline mt-3">
                    <?php esc_html_e('Limpiar filtros', 'rentword'); ?>
                </a>
            </div>
            <?php endif; ?>
            
            <!-- Pagination -->
            <?php if ($total > $per_page): ?>
            <div class="text-center mt-5">
                <?php rentword_pagination($total, $per_page, $paged); ?>
            </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php get_footer(); ?>
