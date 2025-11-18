<?php
/**
 * RentWord Pro Theme Functions
 *
 * @package RentWord
 * @version 1.2.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define theme constants
define('RENTWORD_VERSION', '4.13.4');
define('RENTWORD_THEME_DIR', get_template_directory());
define('RENTWORD_THEME_URI', get_template_directory_uri());
define('RENTWORD_INC_DIR', RENTWORD_THEME_DIR . '/inc');
define('RENTWORD_ASSETS_URI', RENTWORD_THEME_URI . '/assets');

/**
 * Theme Setup
 */
function rentword_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script'
    ));
    add_theme_support('custom-logo');
    add_theme_support('responsive-embeds');
    add_theme_support('align-wide');
    add_theme_support('wp-block-styles');
    add_theme_support('editor-styles');
    
    // Register menus
    register_nav_menus(array(
        'primary' => __('Menú Principal', 'rentword'),
        'footer' => __('Menú Footer', 'rentword'),
    ));
    
    // Add image sizes
    add_image_size('rentword-property-thumb', 400, 300, true);
    add_image_size('rentword-property-square', 400, 400, true); // Vacation Rental square cards
    add_image_size('rentword-property-medium', 800, 600, true);
    add_image_size('rentword-property-large', 1200, 800, true);
    add_image_size('rentword-gallery', 600, 400, true);
}
add_action('after_setup_theme', 'rentword_setup');

/**
 * Auto-reset detection on theme activation
 */
function rentword_activation_reset() {
    delete_option('rentword_auto_detection_done');
    delete_option('rentword_auto_detection_fields');
    
    global $wpdb;
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_rentword_%'");
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_rentword_%'");
    
    // Create required pages
    rentword_create_required_pages();
}
add_action('after_switch_theme', 'rentword_activation_reset');

/**
 * Create required theme pages
 */
function rentword_create_required_pages() {
    $pages = array(
        'properties' => array(
            'title' => 'Propiedades',
            'template' => 'page-templates/properties-listing.php',
        ),
        'property' => array(
            'title' => 'Propiedad',
            'template' => 'page-templates/property-single.php',
        ),
    );
    
    foreach ($pages as $slug => $page_data) {
        // Check if page already exists
        $page = get_page_by_path($slug);
        
        if (!$page) {
            // Create the page
            $page_id = wp_insert_post(array(
                'post_title'    => $page_data['title'],
                'post_name'     => $slug,
                'post_status'   => 'publish',
                'post_type'     => 'page',
                'post_content'  => '',
            ));
            
            if ($page_id && !is_wp_error($page_id)) {
                // Set the page template
                update_post_meta($page_id, '_wp_page_template', $page_data['template']);
            }
        }
    }
}

/**
 * Force cache clear on admin init (once per hour)
 */
function rentword_admin_force_refresh() {
    if (is_admin() && !get_transient('rentword_admin_refreshed')) {
        global $wpdb;
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_rentword_%'");
        set_transient('rentword_admin_refreshed', true, HOUR_IN_SECONDS);
    }
}
add_action('admin_init', 'rentword_admin_force_refresh');

/**
 * Enqueue Scripts and Styles
 */
function rentword_enqueue_assets() {
    // Bootstrap 5.3.2 CSS
    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css', array(), '5.3.2');
    
    // Bootstrap Icons
    wp_enqueue_style('bootstrap-icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css', array(), '1.11.3');
    
    // Theme CSS - Correct cascade order: Base → Global → Design System → Components
    wp_enqueue_style('rentword-style', get_stylesheet_uri(), array('bootstrap-css'), RENTWORD_VERSION); // 1. Base variables
    wp_enqueue_style('rentword-main', RENTWORD_ASSETS_URI . '/css/main.css', array('rentword-style'), RENTWORD_VERSION); // 2. Global styles
    wp_enqueue_style('rentword-design', RENTWORD_ASSETS_URI . '/css/vacation-rental.css', array('rentword-main'), RENTWORD_VERSION); // 3. Design system
    wp_enqueue_style('rentword-components', RENTWORD_ASSETS_URI . '/css/components.css', array('rentword-design'), RENTWORD_VERSION); // 4. Component overrides
    
    // Leaflet for maps
    wp_enqueue_style('leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', array(), '1.9.4');
    
    // Swiper for sliders
    wp_enqueue_style('swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), '11.0.0');
    
    // Flatpickr for date range picker
    wp_enqueue_style('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css', array(), '4.6.13');
    
    // jQuery 3.7.1 (needed for Bootstrap and custom functionality)
    wp_enqueue_script('jquery', 'https://code.jquery.com/jquery-3.7.1.min.js', array(), '3.7.1', true);
    
    // Bootstrap 5.3.2 Bundle JS (includes Popper)
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js', array('jquery'), '5.3.2', true);
    
    // Leaflet for maps
    wp_enqueue_script('leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array(), '1.9.4', true);
    
    // Swiper for sliders
    wp_enqueue_script('swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), '11.0.0', true);
    
    // Flatpickr for date range picker
    wp_enqueue_script('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js', array(), '4.6.13', true);
    wp_enqueue_script('flatpickr-es', 'https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/l10n/es.js', array('flatpickr'), '4.6.13', true);
    
    // Custom scripts with jQuery dependency
    wp_enqueue_script('rentword-main', RENTWORD_ASSETS_URI . '/js/main-jquery.js', array('jquery', 'bootstrap-js'), RENTWORD_VERSION, true);
    wp_enqueue_script('rentword-search', RENTWORD_ASSETS_URI . '/js/search.js', array('jquery'), RENTWORD_VERSION, true);
    wp_enqueue_script('rentword-property', RENTWORD_ASSETS_URI . '/js/property.js', array('jquery', 'swiper', 'leaflet'), RENTWORD_VERSION, true);
    
    // Property single page scripts
    if (is_page_template('page-templates/property-single.php') || isset($_GET['id'])) {
        wp_enqueue_script('rentword-property-single', RENTWORD_ASSETS_URI . '/js/property-single.js', array('jquery', 'swiper'), RENTWORD_VERSION, true);
    }
    
    // Localize script
    wp_localize_script('rentword-main', 'rentwordData', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('rentword_nonce'),
        'apiUrl' => get_option('rentword_api_url', ''),
        'fieldMapping' => rentword_get_field_mapping(),
    ));
}
add_action('wp_enqueue_scripts', 'rentword_enqueue_assets');

/**
 * Include required files
 */
require_once RENTWORD_INC_DIR . '/admin/theme-options.php';
require_once RENTWORD_INC_DIR . '/api/rentinno-api.php';
require_once RENTWORD_INC_DIR . '/shortcodes/property-shortcodes.php';
require_once RENTWORD_INC_DIR . '/widgets/property-widgets.php';
require_once RENTWORD_INC_DIR . '/blocks/blocks-init.php';
require_once RENTWORD_INC_DIR . '/template-functions.php';
require_once RENTWORD_INC_DIR . '/ajax-handlers.php';
require_once RENTWORD_INC_DIR . '/customizer.php';

/**
 * Register Widget Areas
 */
function rentword_widgets_init() {
    register_sidebar(array(
        'name'          => __('Barra Lateral', 'rentword'),
        'id'            => 'sidebar-1',
        'description'   => __('Agrega widgets aquí.', 'rentword'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
    
    // Footer Widget Areas (4 columns)
    for ($i = 1; $i <= 4; $i++) {
        register_sidebar(array(
            'name'          => sprintf(__('Footer Column %d', 'rentword'), $i),
            'id'            => 'footer-' . $i,
            'description'   => sprintf(__('Widget area for footer column %d', 'rentword'), $i),
            'before_widget' => '<div class="vr-footer-widget">',
            'after_widget'  => '</div>',
            'before_title'  => '<h6 class="vr-footer-title">',
            'after_title'   => '</h6>',
        ));
    }
}
add_action('widgets_init', 'rentword_widgets_init');

/**
 * Get field mapping from theme options
 */
function rentword_get_field_mapping() {
    return array(
        'title' => get_option('rentword_field_title', 'title'),
        'price' => get_option('rentword_field_price', 'price'),
        'images' => get_option('rentword_field_images', 'images'),
        'location' => get_option('rentword_field_location', 'location'),
        'description' => get_option('rentword_field_description', 'description'),
        'amenities' => get_option('rentword_field_amenities', 'amenities'),
        'coordinates' => get_option('rentword_field_coordinates', 'coordinates'),
        'bedrooms' => get_option('rentword_field_bedrooms', 'bedrooms'),
        'bathrooms' => get_option('rentword_field_bathrooms', 'bathrooms'),
        'property_type' => get_option('rentword_field_property_type', 'property_type'),
        'area' => get_option('rentword_field_area', 'area'),
        'availability' => get_option('rentword_field_availability', 'availability'),
        'featured' => get_option('rentword_field_featured', 'featured'),
        'rating' => get_option('rentword_field_rating', 'rating'),
        'id' => get_option('rentword_field_id', 'id'),
    );
}

/**
 * Custom body classes
 */
function rentword_body_classes($classes) {
    if (!is_singular()) {
        $classes[] = 'hfeed';
    }
    
    if (is_page_template('page-templates/properties-listing.php')) {
        $classes[] = 'rw-properties-listing';
    }
    
    if (is_page_template('page-templates/property-single.php')) {
        $classes[] = 'rw-property-single';
    }
    
    return $classes;
}
add_filter('body_class', 'rentword_body_classes');

/**
 * Theme activation hook
 */
function rentword_theme_activation() {
    // Set default options
    if (!get_option('rentword_api_url')) {
        update_option('rentword_api_url', '');
    }
    
    // Set default field mappings
    $default_fields = array(
        'rentword_field_title' => 'title',
        'rentword_field_price' => 'price',
        'rentword_field_images' => 'images',
        'rentword_field_location' => 'location',
        'rentword_field_description' => 'description',
        'rentword_field_amenities' => 'amenities',
        'rentword_field_coordinates' => 'coordinates',
        'rentword_field_bedrooms' => 'bedrooms',
        'rentword_field_bathrooms' => 'bathrooms',
        'rentword_field_property_type' => 'property_type',
        'rentword_field_area' => 'area',
        'rentword_field_availability' => 'availability',
        'rentword_field_featured' => 'featured',
        'rentword_field_rating' => 'rating',
        'rentword_field_id' => 'id',
    );
    
    foreach ($default_fields as $key => $value) {
        if (!get_option($key)) {
            update_option($key, $value);
        }
    }
    
    // Create demo pages on theme activation
    rentword_create_demo_pages();
    
    // Show welcome notice
    set_transient('rentword_activation_notice', true, 60);
}
add_action('after_switch_theme', 'rentword_theme_activation');

/**
 * Also run on init to ensure pages exist
 */
function rentword_ensure_demo_pages() {
    if (!get_option('rentword_demo_pages_created')) {
        rentword_create_demo_pages();
    }
}
add_action('init', 'rentword_ensure_demo_pages', 20);

/**
 * Create demo pages
 */
function rentword_create_demo_pages() {
    $pages_to_create = array(
        'home' => array(
            'title'    => __('Inicio', 'rentword'),
            'slug'     => 'inicio',
            'template' => 'page-templates/home.php',
            'content'  => ''
        ),
        'properties' => array(
            'title'    => __('Propiedades', 'rentword'),
            'slug'     => 'properties',
            'template' => 'page-templates/properties-listing.php',
            'content'  => ''
        ),
        'property' => array(
            'title'    => __('Propiedad', 'rentword'),
            'slug'     => 'property',
            'template' => 'page-templates/property-single.php',
            'content'  => ''
        ),
        'about' => array(
            'title'    => __('Acerca de', 'rentword'),
            'slug'     => 'about',
            'template' => 'default',
            'content'  => '<!-- wp:heading {"level":1} -->
<h1>Sobre Rentinno</h1>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Somos una plataforma líder en México dedicada a transformar la experiencia de alquiler de propiedades vacacionales y de largo plazo.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>🏡 Nuestra Misión</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Conectar a propietarios con huéspedes de manera transparente, segura y eficiente, ofreciendo propiedades excepcionales en las mejores ubicaciones de México.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>✨ Por Qué Elegirnos</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul>
<li><strong>Propiedades Verificadas:</strong> Cada propiedad es inspeccionada y validada por nuestro equipo</li>
<li><strong>Atención 24/7:</strong> Soporte disponible en todo momento para huéspedes y propietarios</li>
<li><strong>Precios Transparentes:</strong> Sin cargos ocultos, todo es claro desde el inicio</li>
<li><strong>Tecnología de Punta:</strong> Plataforma moderna y fácil de usar</li>
<li><strong>Experiencias Locales:</strong> Conocemos cada destino a fondo</li>
</ul>
<!-- /wp:list -->

<!-- wp:heading -->
<h2>📊 Cifras que nos Respaldan</h2>
<!-- /wp:heading -->

<!-- wp:columns -->
<div class="wp-block-columns">
<div class="wp-block-column">
<h3>+500</h3>
<p>Propiedades activas</p>
</div>
<div class="wp-block-column">
<h3>+10,000</h3>
<p>Huéspedes satisfechos</p>
</div>
<div class="wp-block-column">
<h3>4.9⭐</h3>
<p>Calificación promedio</p>
</div>
<div class="wp-block-column">
<h3>15+</h3>
<p>Ciudades en México</p>
</div>
</div>
<!-- /wp:columns -->

<!-- wp:heading -->
<h2>🌟 Nuestro Equipo</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Contamos con un equipo apasionado de profesionales en tecnología, hospitalidad y servicio al cliente, comprometidos con ofrecer la mejor experiencia tanto para propietarios como huéspedes.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>¿Listo para comenzar?</strong> <a href="/properties">Explora nuestras propiedades</a> o <a href="/contact">contáctanos</a> para más información.</p>
<!-- /wp:paragraph -->'
        ),
        'contact' => array(
            'title'    => __('Contacto', 'rentword'),
            'slug'     => 'contact',
            'template' => 'default',
            'content'  => '<!-- wp:heading {"level":1} -->
<h1>Contáctanos</h1>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>¿Tienes preguntas sobre nuestras propiedades o servicios? Estamos aquí para ayudarte.</p>
<!-- /wp:paragraph -->

<!-- wp:columns -->
<div class="wp-block-columns">
<div class="wp-block-column" style="flex-basis:60%">

<!-- wp:heading -->
<h2>📧 Envíanos un Mensaje</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><strong>Email:</strong> <a href="mailto:hola@rentinno.com">hola@rentinno.com</a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>Teléfono:</strong> +52 (81) 1234-5678</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>WhatsApp:</strong> +52 811 234 5678</p>
<!-- /wp:paragraph -->

<!-- wp:separator -->
<hr class="wp-block-separator"/>
<!-- /wp:separator -->

<!-- wp:heading {"level":3} -->
<h3>Horarios de Atención</h3>
<!-- /wp:heading -->

<!-- wp:list -->
<ul>
<li><strong>Lunes a Viernes:</strong> 9:00 AM - 7:00 PM</li>
<li><strong>Sábados:</strong> 10:00 AM - 4:00 PM</li>
<li><strong>Domingos:</strong> Cerrado</li>
<li><strong>Soporte 24/7:</strong> Disponible para emergencias de huéspedes activos</li>
</ul>
<!-- /wp:list -->

</div>

<div class="wp-block-column" style="flex-basis:40%">

<!-- wp:heading -->
<h2>🏢 Oficinas</h2>
<!-- /wp:heading -->

<!-- wp:heading {"level":3} -->
<h3>Monterrey (Matriz)</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Av. Constitución 123<br>Col. Centro<br>Monterrey, N.L. 64000<br>México</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3>Ciudad de México</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Paseo de la Reforma 456<br>Col. Juárez<br>CDMX 06600<br>México</p>
<!-- /wp:paragraph -->

</div>
</div>
<!-- /wp:columns -->

<!-- wp:separator -->
<hr class="wp-block-separator"/>
<!-- /wp:separator -->

<!-- wp:heading -->
<h2>💼 Para Propietarios</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>¿Quieres listar tu propiedad con nosotros? Escríbenos a <a href="mailto:propietarios@rentinno.com">propietarios@rentinno.com</a> o llámanos directamente.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons">
<div class="wp-block-button">
<a class="wp-block-button__link" href="/properties">Ver Propiedades</a>
</div>
<div class="wp-block-button is-style-outline">
<a class="wp-block-button__link" href="mailto:hola@rentinno.com">Enviar Email</a>
</div>
</div>
<!-- /wp:buttons -->

<!-- wp:paragraph -->
<p style="margin-top:2rem;color:#666;font-size:0.9rem;"><em>Tiempo de respuesta promedio: menos de 2 horas en horario laboral</em></p>
<!-- /wp:paragraph -->'
        ),
    );
    
    $created_pages = array();
    foreach ($pages_to_create as $key => $page_data) {
        $existing_page = get_page_by_path($page_data['slug']);
        if ($existing_page) {
            $page_id = $existing_page->ID;
        } else {
            $page_id = wp_insert_post(array(
                'post_title'   => $page_data['title'],
                'post_name'    => $page_data['slug'],
                'post_content' => $page_data['content'],
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_author'  => get_current_user_id() ?: 1,
            ));
        }
        
        if ($page_id && !is_wp_error($page_id) && $page_data['template'] && 'default' !== $page_data['template']) {
            update_post_meta($page_id, '_wp_page_template', $page_data['template']);
        }
        
        $created_pages[$key] = $page_id;
    }
    
    if (!empty($created_pages['home'])) {
        update_option('page_on_front', $created_pages['home']);
        update_option('show_on_front', 'page');
    }
    
    rentword_setup_demo_menu($created_pages);
    update_option('rentword_demo_pages_created', true);
}

/**
 * Create default navigation menu and assign it
 */
function rentword_setup_demo_menu($page_ids = array()) {
    $menu_name = __('Menú RentWord', 'rentword');
    $menu = wp_get_nav_menu_object($menu_name);
    $menu_id = $menu ? $menu->term_id : wp_create_nav_menu($menu_name);
    
    if (!is_wp_error($menu_id)) {
        $current_items = wp_get_nav_menu_items($menu_id);
        if (empty($current_items)) {
            $menu_structure = array(
                array('title' => __('Inicio', 'rentword'), 'page' => 'home'),
                array('title' => __('Propiedades', 'rentword'), 'page' => 'properties'),
                array('title' => __('Acerca de', 'rentword'), 'page' => 'about'),
                array('title' => __('Contacto', 'rentword'), 'page' => 'contact'),
            );
            $order = 1;
            foreach ($menu_structure as $item) {
                if (empty($page_ids[$item['page']])) {
                    continue;
                }
                wp_update_nav_menu_item($menu_id, 0, array(
                    'menu-item-title'     => $item['title'],
                    'menu-item-object-id' => $page_ids[$item['page']],
                    'menu-item-object'    => 'page',
                    'menu-item-type'      => 'post_type',
                    'menu-item-status'    => 'publish',
                    'menu-item-position'  => $order,
                ));
                $order++;
            }
        }
        
        $locations = get_theme_mod('nav_menu_locations', array());
        if (!isset($locations['primary']) || $locations['primary'] !== $menu_id) {
            $locations['primary'] = $menu_id;
            set_theme_mod('nav_menu_locations', $locations);
        }
    }
}

/**
 * Admin notice on activation
 */
function rentword_activation_notice() {
    if (get_transient('rentword_activation_notice')) {
        $pages_created = get_option('rentword_demo_pages_created');
        ?>
        <div class="notice notice-success is-dismissible">
            <h2>🎉 ¡Bienvenido a RentWord Pro!</h2>
            <p><strong>El tema ha sido activado exitosamente.</strong></p>
            
            <?php if ($pages_created): ?>
            <p>✅ Se han creado las siguientes páginas automáticamente:</p>
            <ul style="list-style: disc; margin-left: 2rem;">
                <li><strong>Inicio</strong> - Configurada como página principal</li>
                <li><strong>Propiedades</strong> - Listado con filtros</li>
                <li><strong>Propiedad</strong> - Vista individual</li>
                <li><strong>Acerca de</strong> - Información de empresa</li>
                <li><strong>Contacto</strong> - Página de contacto</li>
            </ul>
            <?php else: ?>
            <p>⚠️ Las páginas no se crearon automáticamente.</p>
            <p>
                <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=rentword-options&action=create_pages'), 'create_rentword_pages'); ?>" class="button button-primary">
                    Crear Páginas Ahora
                </a>
            </p>
            <?php endif; ?>
            
            <p><strong>🎨 Diseño Naranja/Blanco:</strong> Tema con Tailwind CSS integrado</p>
            <p><strong>📊 Datos Demo:</strong> 13 propiedades de muestra. Configura tu API para datos reales:</p>
            <ol style="list-style: decimal; margin-left: 2rem;">
                <li>Ve a <strong>Apariencia → Opciones del Tema</strong></li>
                <li>Configura tu <strong>URL de API de Rentinno</strong></li>
                <li>Ajusta el <strong>Mapeo de Campos</strong> si es necesario</li>
            </ol>
            <p>
                <a href="<?php echo admin_url('themes.php?page=rentword-options'); ?>" class="button button-primary">Configurar API</a>
                <a href="<?php echo home_url(); ?>" class="button">Ver Sitio</a>
            </p>
            <p style="color: #666; font-size: 0.9em; margin-top: 1rem;">
                <em>Desarrollado por el <strong>Equipo Rentinno</strong></em>
            </p>
        </div>
        <?php
        delete_transient('rentword_activation_notice');
    }
}
add_action('admin_notices', 'rentword_activation_notice');

/**
 * Handle manual page creation from admin
 */
function rentword_handle_create_pages() {
    if (isset($_GET['page']) && $_GET['page'] === 'rentword-options' && 
        isset($_GET['action']) && $_GET['action'] === 'create_pages' &&
        check_admin_referer('create_rentword_pages')) {
        
        delete_option('rentword_demo_pages_created');
        rentword_create_demo_pages();
        
        wp_redirect(admin_url('themes.php?page=rentword-options&created=1'));
        exit;
    }
}
add_action('admin_init', 'rentword_handle_create_pages');

