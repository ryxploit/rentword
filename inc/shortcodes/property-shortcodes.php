<?php
/**
 * Property Shortcodes
 * 
 * @package RentWord
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Properties Listing Shortcode
 * Usage: [rentword_properties limit="6" featured="1"]
 */
function rentword_properties_shortcode($atts) {
    $atts = shortcode_atts(array(
        'limit' => 6,
        'featured' => '',
        'property_type' => '',
        'columns' => 3,
    ), $atts);
    
    $api = rentword_api();
    $properties = $api->get_properties(array('per_page' => 999));
    
    if (is_wp_error($properties)) {
        return '<p class="rw-error">' . esc_html($properties->get_error_message()) . '</p>';
    }
    
    // Filter by featured
    if ($atts['featured'] === '1') {
        $properties = array_filter($properties, function($property) {
            return rentword_get_property_field($property, 'featured');
        });
    }
    
    // Filter by property type
    if (!empty($atts['property_type'])) {
        $properties = array_filter($properties, function($property) use ($atts) {
            return rentword_get_property_field($property, 'property_type') === $atts['property_type'];
        });
    }
    
    // Limit results
    $properties = array_slice($properties, 0, intval($atts['limit']));
    
    ob_start();
    
    if (!empty($properties)) {
        echo '<div class="rw-properties-grid rw-columns-' . esc_attr($atts['columns']) . '">';
        foreach ($properties as $property) {
            rentword_property_card($property);
        }
        echo '</div>';
    } else {
        echo '<p class="rw-no-results">' . esc_html__('No properties found.', 'rentword') . '</p>';
    }
    
    return ob_get_clean();
}
add_shortcode('rentword_properties', 'rentword_properties_shortcode');

/**
 * Property Search Form Shortcode
 * Usage: [rentword_search]
 */
function rentword_search_shortcode($atts) {
    ob_start();
    rentword_search_form();
    return ob_get_clean();
}
add_shortcode('rentword_search', 'rentword_search_shortcode');

/**
 * Featured Properties Slider Shortcode
 * Usage: [rentword_featured_slider]
 */
function rentword_featured_slider_shortcode($atts) {
    $atts = shortcode_atts(array(
        'limit' => 10,
    ), $atts);
    
    $api = rentword_api();
    $properties = $api->get_properties(array('per_page' => 999));
    
    if (is_wp_error($properties)) {
        return '<p class="rw-error">' . esc_html($properties->get_error_message()) . '</p>';
    }
    
    // Filter featured
    $featured_properties = array_filter($properties, function($property) {
        return rentword_get_property_field($property, 'featured');
    });
    
    $featured_properties = array_slice($featured_properties, 0, intval($atts['limit']));
    
    ob_start();
    
    if (!empty($featured_properties)) {
        ?>
        <div class="swiper rw-featured-slider rw-shortcode-slider">
            <div class="swiper-wrapper">
                <?php foreach ($featured_properties as $property): ?>
                    <div class="swiper-slide">
                        <?php rentword_property_card($property); ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-pagination"></div>
        </div>
        <?php
    } else {
        echo '<p class="rw-no-results">' . esc_html__('No featured properties found.', 'rentword') . '</p>';
    }
    
    return ob_get_clean();
}
add_shortcode('rentword_featured_slider', 'rentword_featured_slider_shortcode');

/**
 * Property Types Grid Shortcode
 * Usage: [rentword_property_types]
 */
function rentword_property_types_shortcode($atts) {
    $api = rentword_api();
    $property_types = $api->get_property_types();
    
    if (empty($property_types)) {
        return '<p class="rw-no-results">' . esc_html__('No property types found.', 'rentword') . '</p>';
    }
    
    ob_start();
    
    ?>
    <div class="rw-property-types-grid">
        <?php foreach ($property_types as $type): ?>
            <a href="<?php echo esc_url(add_query_arg('property_type', $type, home_url('/properties'))); ?>" class="rw-property-type-card">
                <h3><?php echo esc_html($type); ?></h3>
            </a>
        <?php endforeach; ?>
    </div>
    <?php
    
    return ob_get_clean();
}
add_shortcode('rentword_property_types', 'rentword_property_types_shortcode');

/**
 * Single Property Shortcode
 * Usage: [rentword_property id="123"]
 */
function rentword_single_property_shortcode($atts) {
    $atts = shortcode_atts(array(
        'id' => '',
    ), $atts);
    
    if (empty($atts['id'])) {
        return '<p class="rw-error">' . esc_html__('Property ID is required.', 'rentword') . '</p>';
    }
    
    $api = rentword_api();
    $property = $api->get_property($atts['id']);
    
    if (is_wp_error($property)) {
        return '<p class="rw-error">' . esc_html($property->get_error_message()) . '</p>';
    }
    
    ob_start();
    rentword_property_card($property);
    return ob_get_clean();
}
add_shortcode('rentword_property', 'rentword_single_property_shortcode');

/**
 * Properties Map Shortcode
 * Usage: [rentword_map]
 */
function rentword_map_shortcode($atts) {
    $atts = shortcode_atts(array(
        'height' => '500px',
        'zoom' => 12,
    ), $atts);
    
    $api = rentword_api();
    $properties = $api->get_properties(array('per_page' => 999));
    
    if (is_wp_error($properties) || empty($properties)) {
        return '<p class="rw-error">' . esc_html__('No properties with coordinates found.', 'rentword') . '</p>';
    }
    
    $properties_with_coords = array();
    foreach ($properties as $property) {
        $coordinates = rentword_get_property_field($property, 'coordinates');
        if (!empty($coordinates)) {
            $properties_with_coords[] = $property;
        }
    }
    
    if (empty($properties_with_coords)) {
        return '<p class="rw-error">' . esc_html__('No properties with coordinates found.', 'rentword') . '</p>';
    }
    
    ob_start();
    
    ?>
    <div class="rw-properties-map" 
         id="rw-properties-map-<?php echo uniqid(); ?>" 
         style="height: <?php echo esc_attr($atts['height']); ?>;"
         data-properties='<?php echo esc_attr(json_encode($properties_with_coords)); ?>'
         data-zoom="<?php echo esc_attr($atts['zoom']); ?>">
    </div>
    <?php
    
    return ob_get_clean();
}
add_shortcode('rentword_map', 'rentword_map_shortcode');
