<?php
/**
 * AJAX Handlers
 * 
 * @package RentWord
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Sort properties by given criteria
 */
function rentword_sort_properties($properties, $sort_by = 'featured') {
    if (empty($properties) || !is_array($properties)) {
        return $properties;
    }
    
    $field_mapping = rentword_get_field_mapping();
    
    switch ($sort_by) {
        case 'price_low':
            usort($properties, function($a, $b) use ($field_mapping) {
                $price_a = isset($a[$field_mapping['price']]) ? floatval($a[$field_mapping['price']]) : 0;
                $price_b = isset($b[$field_mapping['price']]) ? floatval($b[$field_mapping['price']]) : 0;
                return $price_a - $price_b;
            });
            break;
            
        case 'price_high':
            usort($properties, function($a, $b) use ($field_mapping) {
                $price_a = isset($a[$field_mapping['price']]) ? floatval($a[$field_mapping['price']]) : 0;
                $price_b = isset($b[$field_mapping['price']]) ? floatval($b[$field_mapping['price']]) : 0;
                return $price_b - $price_a;
            });
            break;
            
        case 'rating':
            usort($properties, function($a, $b) use ($field_mapping) {
                $rating_a = isset($a[$field_mapping['rating']]) ? floatval($a[$field_mapping['rating']]) : 0;
                $rating_b = isset($b[$field_mapping['rating']]) ? floatval($b[$field_mapping['rating']]) : 0;
                return $rating_b - $rating_a;
            });
            break;
            
        case 'featured':
        default:
            usort($properties, function($a, $b) use ($field_mapping) {
                $featured_a = isset($a[$field_mapping['featured']]) ? $a[$field_mapping['featured']] : false;
                $featured_b = isset($b[$field_mapping['featured']]) ? $b[$field_mapping['featured']] : false;
                return $featured_b - $featured_a;
            });
            break;
    }
    
    return $properties;
}

/**
 * AJAX search properties
 */
function rentword_ajax_search_properties() {
    check_ajax_referer('rentword_nonce', 'nonce');
    
    $filters = array();
    
    if (isset($_POST['location']) && !empty($_POST['location'])) {
        $filters['location'] = sanitize_text_field($_POST['location']);
    }
    
    if (isset($_POST['min_price']) && !empty($_POST['min_price'])) {
        $filters['min_price'] = floatval($_POST['min_price']);
    }
    
    if (isset($_POST['max_price']) && !empty($_POST['max_price'])) {
        $filters['max_price'] = floatval($_POST['max_price']);
    }
    
    if (isset($_POST['bedrooms']) && !empty($_POST['bedrooms'])) {
        $filters['bedrooms'] = intval($_POST['bedrooms']);
    }
    
    if (isset($_POST['bathrooms']) && !empty($_POST['bathrooms'])) {
        $filters['bathrooms'] = intval($_POST['bathrooms']);
    }
    
    if (isset($_POST['property_type']) && !empty($_POST['property_type'])) {
        $filters['property_type'] = sanitize_text_field($_POST['property_type']);
    }
    
    if (isset($_POST['amenities']) && is_array($_POST['amenities'])) {
        $filters['amenities'] = array_map('sanitize_text_field', $_POST['amenities']);
    }
    
    // Get sort order
    $sort_by = isset($_POST['sort_by']) ? sanitize_text_field($_POST['sort_by']) : 'featured';
    
    // Get pagination
    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
    $per_page = get_option('rentword_per_page', 12);
    
    $api = rentword_api();
    $all_properties = $api->search_properties($filters);
    
    if (is_wp_error($all_properties)) {
        wp_send_json_error(array('message' => $all_properties->get_error_message()));
    }
    
    // Sort properties
    $all_properties = rentword_sort_properties($all_properties, $sort_by);
    
    // Paginate
    $total = count($all_properties);
    $properties = array_slice($all_properties, ($paged - 1) * $per_page, $per_page);
    
    ob_start();
    
    if (!empty($properties)) {
        foreach ($properties as $property) {
            echo '<div class="col-md-6 col-lg-4 property-card-wrapper">';
            rentword_property_card($property);
            echo '</div>';
        }
    } else {
        echo '<div class="col-12"><div class="text-center py-5">';
        echo '<h3 class="text-muted">' . esc_html__('No se encontraron propiedades', 'rentword') . '</h3>';
        echo '<p class="text-muted">' . esc_html__('Intenta ajustar tus filtros de búsqueda', 'rentword') . '</p>';
        echo '</div></div>';
    }
    
    $html = ob_get_clean();
    
    // Calculate pagination
    $total_pages = ceil($total / $per_page);
    $pagination = array(
        'current_page' => $paged,
        'total_pages' => $total_pages,
        'prev_page' => $paged > 1 ? $paged - 1 : null,
        'next_page' => $paged < $total_pages ? $paged + 1 : null
    );
    
    wp_send_json_success(array(
        'html' => $html,
        'total' => $total,
        'pagination' => $pagination
    ));
}
add_action('wp_ajax_rentword_search_properties', 'rentword_ajax_search_properties');
add_action('wp_ajax_nopriv_rentword_search_properties', 'rentword_ajax_search_properties');

/**
 * AJAX load more properties
 */
function rentword_ajax_load_more() {
    check_ajax_referer('rentword_nonce', 'nonce');
    
    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $per_page = isset($_POST['per_page']) ? intval($_POST['per_page']) : get_option('rentword_per_page', 12);
    
    $api = rentword_api();
    $properties = $api->get_properties(array(
        'page' => $page,
        'per_page' => $per_page
    ));
    
    if (is_wp_error($properties)) {
        wp_send_json_error(array('message' => $properties->get_error_message()));
    }
    
    ob_start();
    
    foreach ($properties as $property) {
        rentword_property_card($property);
    }
    
    $html = ob_get_clean();
    
    wp_send_json_success(array(
        'html' => $html,
        'has_more' => count($properties) === $per_page
    ));
}
add_action('wp_ajax_rentword_load_more', 'rentword_ajax_load_more');
add_action('wp_ajax_nopriv_rentword_load_more', 'rentword_ajax_load_more');

/**
 * AJAX get property details
 */
function rentword_ajax_get_property() {
    check_ajax_referer('rentword_nonce', 'nonce');
    
    $property_id = isset($_POST['property_id']) ? sanitize_text_field($_POST['property_id']) : '';
    
    if (empty($property_id)) {
        wp_send_json_error(array('message' => __('Property ID is required', 'rentword')));
    }
    
    $api = rentword_api();
    $property = $api->get_property($property_id);
    
    if (is_wp_error($property)) {
        wp_send_json_error(array('message' => $property->get_error_message()));
    }
    
    wp_send_json_success(array('property' => $property));
}
add_action('wp_ajax_rentword_get_property', 'rentword_ajax_get_property');
add_action('wp_ajax_nopriv_rentword_get_property', 'rentword_ajax_get_property');
