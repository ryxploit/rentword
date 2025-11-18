<?php
/**
 * Rentinno API Handler
 * 
 * Handles all API communication with Rentinno
 *
 * @package RentWord
 */

if (!defined('ABSPATH')) {
    exit;
}

class RentWord_API {
    
    /**
     * API URL
     */
    private $api_url;
    
    /**
     * API Key
     */
    private $api_key;
    
    /**
     * Cache duration in hours
     */
    private $cache_duration;
    
    /**
     * Field mapping
     */
    private $field_mapping;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->api_url = get_option('rentword_api_url', '');
        $this->api_key = get_option('rentword_api_key', '');
        $this->cache_duration = get_option('rentword_cache_duration', 1);
        $this->field_mapping = rentword_get_field_mapping();
    }
    
    /**
     * Get demo properties
     */
    public function get_demo_properties() {
        return rentword_get_demo_properties();
    }
    
    /**
     * Get all properties from API
     * 
     * @param array $args Query arguments
     * @return array|WP_Error
     */
    public function get_properties($args = array()) {
        $defaults = array(
            'per_page' => get_option('rentword_per_page', 12),
            'page' => 1,
            'orderby' => 'date',
            'order' => 'DESC',
        );
        
        $args = wp_parse_args($args, $defaults);
        
        // Generate cache key based on arguments
        $cache_key = 'rentword_properties_' . md5(serialize($args));
        
        // Try to get from cache
        if ($this->cache_duration > 0) {
            $cached = get_transient($cache_key);
            if ($cached !== false) {
                return $cached;
            }
        }
        
        // Make API request
        $properties = $this->fetch_from_api($args);
        
        if (is_wp_error($properties)) {
            // Return demo data if API not configured
            return $this->get_demo_properties();
        }
        
        // Map fields
        $mapped_properties = $this->map_properties($properties);
        
        // Cache the result
        if ($this->cache_duration > 0) {
            set_transient($cache_key, $mapped_properties, $this->cache_duration * HOUR_IN_SECONDS);
        }
        
        return $mapped_properties;
    }
    
    /**
     * Get a single property by ID
     * 
     * @param string $property_id
     * @return array|WP_Error
     */
    public function get_property($property_id) {
        $cache_key = 'rentword_property_' . $property_id;
        
        // Try to get from cache
        if ($this->cache_duration > 0) {
            $cached = get_transient($cache_key);
            if ($cached !== false) {
                return $cached;
            }
        }
        
        // Get all properties and find the one we need
        $all_properties = $this->get_properties(array('per_page' => 999));
        
        if (is_wp_error($all_properties)) {
            // Try demo data
            $demo_properties = rentword_get_demo_properties();
            foreach ($demo_properties as $property) {
                if ($property['id'] == $property_id) {
                    return $property;
                }
            }
            return new WP_Error('property_not_found', __('Propiedad no encontrada', 'rentword'));
        }
        
        $id_field = $this->field_mapping['id'];
        
        foreach ($all_properties as $property) {
            if (isset($property[$id_field]) && $property[$id_field] == $property_id) {
                // Cache single property
                if ($this->cache_duration > 0) {
                    set_transient($cache_key, $property, $this->cache_duration * HOUR_IN_SECONDS);
                }
                return $property;
            }
        }
        
        return new WP_Error('property_not_found', __('Property not found', 'rentword'));
    }
    
    /**
     * Search properties with filters
     * 
     * @param array $filters
     * @return array|WP_Error
     */
    public function search_properties($filters = array()) {
        $all_properties = $this->get_properties(array('per_page' => 999));
        
        if (is_wp_error($all_properties)) {
            return $all_properties;
        }
        
        $filtered = $all_properties;
        
        // Apply filters
        if (!empty($filters['min_price'])) {
            $filtered = array_filter($filtered, function($property) use ($filters) {
                $price_field = $this->field_mapping['price'];
                return isset($property[$price_field]) && $property[$price_field] >= $filters['min_price'];
            });
        }
        
        if (!empty($filters['max_price'])) {
            $filtered = array_filter($filtered, function($property) use ($filters) {
                $price_field = $this->field_mapping['price'];
                return isset($property[$price_field]) && $property[$price_field] <= $filters['max_price'];
            });
        }
        
        if (!empty($filters['bedrooms'])) {
            $filtered = array_filter($filtered, function($property) use ($filters) {
                $bedrooms_field = $this->field_mapping['bedrooms'];
                return isset($property[$bedrooms_field]) && $property[$bedrooms_field] >= $filters['bedrooms'];
            });
        }
        
        if (!empty($filters['bathrooms'])) {
            $filtered = array_filter($filtered, function($property) use ($filters) {
                $bathrooms_field = $this->field_mapping['bathrooms'];
                return isset($property[$bathrooms_field]) && $property[$bathrooms_field] >= $filters['bathrooms'];
            });
        }
        
        if (!empty($filters['property_type'])) {
            $filtered = array_filter($filtered, function($property) use ($filters) {
                $type_field = $this->field_mapping['property_type'];
                return isset($property[$type_field]) && $property[$type_field] == $filters['property_type'];
            });
        }
        
        if (!empty($filters['location'])) {
            $filtered = array_filter($filtered, function($property) use ($filters) {
                $location_field = $this->field_mapping['location'];
                if (!isset($property[$location_field])) {
                    return false;
                }
                return stripos($property[$location_field], $filters['location']) !== false;
            });
        }
        
        if (!empty($filters['amenities'])) {
            $filtered = array_filter($filtered, function($property) use ($filters) {
                $amenities_field = $this->field_mapping['amenities'];
                if (!isset($property[$amenities_field]) || !is_array($property[$amenities_field])) {
                    return false;
                }
                
                // Check if property has all requested amenities
                foreach ($filters['amenities'] as $amenity) {
                    if (!in_array($amenity, $property[$amenities_field])) {
                        return false;
                    }
                }
                return true;
            });
        }
        
        return array_values($filtered);
    }
    
    /**
     * Fetch data from API
     * 
     * @param array $args
     * @return array|WP_Error
     */
    private function fetch_from_api($args = array()) {
        if (empty($this->api_url)) {
            return new WP_Error('no_api_url', __('API URL is not configured', 'rentword'));
        }
        
        $request_args = array(
            'timeout' => 30,
            'headers' => array(
                'Accept' => 'application/json',
            ),
        );
        
        if (!empty($this->api_key)) {
            $request_args['headers']['Authorization'] = 'Bearer ' . $this->api_key;
        }
        
        // Build query string
        $query_params = array();
        if (isset($args['per_page'])) {
            $query_params['per_page'] = $args['per_page'];
        }
        if (isset($args['page'])) {
            $query_params['page'] = $args['page'];
        }
        
        $url = $this->api_url;
        if (!empty($query_params)) {
            $url = add_query_arg($query_params, $url);
        }
        
        $response = wp_remote_get($url, $request_args);
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $code = wp_remote_retrieve_response_code($response);
        
        if ($code !== 200) {
            return new WP_Error('api_error', sprintf(__('API returned status code %d', 'rentword'), $code));
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new WP_Error('invalid_json', __('API returned invalid JSON', 'rentword'));
        }
        
        // Handle different response formats
        // Format 1: { "data": [...] }
        if (isset($data['data']) && is_array($data['data'])) {
            return $data['data'];
        } 
        // Format 2: { "properties": [...] }
        elseif (isset($data['properties']) && is_array($data['properties'])) {
            return $data['properties'];
        } 
        // Format 3: { "items": [...] } (common pagination format)
        elseif (isset($data['items']) && is_array($data['items'])) {
            return $data['items'];
        } 
        // Format 4: { "results": [...] } (another pagination format)
        elseif (isset($data['results']) && is_array($data['results'])) {
            return $data['results'];
        } 
        // Format 5: Direct array of properties
        elseif (is_array($data) && isset($data[0]) && is_array($data[0])) {
            return $data;
        }
        
        return array();
    }
    
    /**
     * Map API fields to internal field names
     * 
     * @param array $properties
     * @return array
     */
    private function map_properties($properties) {
        if (!is_array($properties) || empty($properties)) {
            return array();
        }
        
        // ALWAYS run auto-detection on first API call
        $auto_detection_done = get_option('rentword_auto_detection_done', false);
        
        if (!empty($properties[0]) && !$auto_detection_done) {
            error_log('RentWord: Running FIRST-TIME auto-detection...');
            $this->auto_detect_and_save_mapping($properties[0]);
        }
        
        $mapped = array();
        
        foreach ($properties as $property) {
            if (!is_array($property)) {
                continue;
            }
            
            // Keep ALL original data - the new smart functions will find the fields
            $mapped[] = $property;
        }
        
        return $mapped;
    }
    
    /**
     * Check if we should auto-detect field mapping
     * 
     * @return bool
     */
    private function should_auto_detect() {
        // Check if auto-detection has ever been run
        $auto_detection_done = get_option('rentword_auto_detection_done', false);
        
        // If never run before, definitely run it
        if (!$auto_detection_done) {
            return true;
        }
        
        $mapping = $this->field_mapping;
        
        // If all fields are empty or identical to their keys, we should auto-detect
        $empty_count = 0;
        foreach ($mapping as $key => $value) {
            if (empty($value) || $value === $key) {
                $empty_count++;
            }
        }
        
        // If more than half the fields are empty/default, trigger auto-detection
        return $empty_count > (count($mapping) / 2);
    }
    
    /**
     * Auto-detect field mapping from a sample property
     * 
     * @param array $sample_property
     */
    private function auto_detect_and_save_mapping($sample_property) {
        $detected = array();
        $available_keys = array_keys($sample_property);
        
        // Log all available fields for debugging
        error_log('RentWord Auto-Detection: Found ' . count($available_keys) . ' fields in API response');
        error_log('Available fields: ' . implode(', ', $available_keys));
        
        // Try to match each internal field with API fields
        foreach (array('id', 'title', 'price', 'images', 'location', 'description', 'bedrooms', 'bathrooms', 'amenities', 'property_type', 'area', 'rating', 'featured', 'coordinates') as $field) {
            $variations = rentword_get_field_variations($field);
            
            foreach ($variations as $variation) {
                if (in_array($variation, $available_keys, true)) {
                    $detected[$field] = $variation;
                    error_log("RentWord Auto-Detection: Matched '$field' to '$variation'");
                    break;
                }
                
                // Case-insensitive fallback
                foreach ($available_keys as $api_key) {
                    if (strtolower($api_key) === strtolower($variation)) {
                        $detected[$field] = $api_key;
                        error_log("RentWord Auto-Detection: Matched '$field' to '$api_key' (case-insensitive)");
                        break 2;
                    }
                }
            }
        }
        
        // Save detected mapping to options
        if (!empty($detected)) {
            foreach ($detected as $internal => $api_field) {
                update_option('rentword_field_' . $internal, $api_field);
            }
            
            // Store detection timestamp
            update_option('rentword_auto_detection_done', current_time('mysql'));
            update_option('rentword_auto_detection_fields', $detected);
            
            // Update internal mapping
            $this->field_mapping = rentword_get_field_mapping();
            
            error_log('RentWord Auto-Detection: Successfully mapped ' . count($detected) . ' fields');
        }
    }
    
    /**
     * Get default value for a field
     * 
     * @param string $field
     * @return mixed
     */
    private function get_default_value($field) {
        $defaults = array(
            'id' => '',
            'title' => '',
            'price' => 0,
            'images' => array(),
            'location' => '',
            'description' => '',
            'amenities' => array(),
            'coordinates' => array(),
            'bedrooms' => 0,
            'bathrooms' => 0,
            'property_type' => '',
            'area' => 0,
            'availability' => array(),
            'featured' => false,
        );
        
        return isset($defaults[$field]) ? $defaults[$field] : '';
    }
    
    /**
     * Clear all cache
     */
    public function clear_cache() {
        global $wpdb;
        
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_rentword_%' OR option_name LIKE '_transient_timeout_rentword_%'");
        
        return true;
    }
    
    /**
     * Get property types from all properties
     * 
     * @return array
     */
    public function get_property_types() {
        $properties = $this->get_properties(array('per_page' => 999));
        
        if (is_wp_error($properties)) {
            return array();
        }
        
        $types = array();
        $type_field = $this->field_mapping['property_type'];
        
        foreach ($properties as $property) {
            if (isset($property[$type_field]) && !empty($property[$type_field])) {
                $types[] = $property[$type_field];
            }
        }
        
        return array_unique($types);
    }
    
    /**
     * Get all unique amenities
     * 
     * @return array
     */
    public function get_all_amenities() {
        $properties = $this->get_properties(array('per_page' => 999));
        
        if (is_wp_error($properties)) {
            return array();
        }
        
        $amenities = array();
        $amenities_field = $this->field_mapping['amenities'];
        
        foreach ($properties as $property) {
            if (isset($property[$amenities_field]) && is_array($property[$amenities_field])) {
                $amenities = array_merge($amenities, $property[$amenities_field]);
            }
        }
        
        return array_unique($amenities);
    }
}

/**
 * Get API instance
 * 
 * @return RentWord_API
 */
function rentword_api() {
    static $instance = null;
    
    if ($instance === null) {
        $instance = new RentWord_API();
    }
    
    return $instance;
}

/**
 * Add admin action to clear cache
 */
function rentword_clear_cache_action() {
    check_ajax_referer('rentword_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => __('Unauthorized', 'rentword')));
    }
    
    rentword_api()->clear_cache();
    
    wp_send_json_success(array('message' => __('Cache cleared successfully', 'rentword')));
}
add_action('wp_ajax_rentword_clear_cache', 'rentword_clear_cache_action');

/**
 * Add clear cache button to admin bar
 */
function rentword_admin_bar_cache_button($wp_admin_bar) {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    $args = array(
        'id'    => 'rentword-clear-cache',
        'title' => __('Clear RentWord Cache', 'rentword'),
        'href'  => '#',
        'meta'  => array(
            'class' => 'rentword-clear-cache-btn',
            'onclick' => 'return false;',
        ),
    );
    $wp_admin_bar->add_node($args);
}
add_action('admin_bar_menu', 'rentword_admin_bar_cache_button', 100);

/**
 * Add script for cache button
 */
function rentword_cache_button_script() {
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const clearCacheLink = document.querySelector('#wp-admin-bar-rentword-clear-cache a');
        if (clearCacheLink) {
            clearCacheLink.addEventListener('click', function(e) {
                e.preventDefault();
                
                if (!confirm('<?php echo esc_js(__('Are you sure you want to clear the API cache?', 'rentword')); ?>')) {
                    return;
                }
                
                const params = new URLSearchParams({
                    action: 'rentword_clear_cache',
                    nonce: '<?php echo wp_create_nonce('rentword_nonce'); ?>'
                });
                
                fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: params.toString()
                })
                .then(response => response.json())
                .then(response => {
                    if (response.success) {
                        alert(response.data.message);
                        location.reload();
                    }
                });
            });
        }
    });
    </script>
    <?php
}
add_action('wp_footer', 'rentword_cache_button_script');
add_action('admin_footer', 'rentword_cache_button_script');

/**
 * Get demo properties for initial setup
 * Returns sample data when API is not configured
 */
function rentword_get_demo_properties() {
    return array(
        array(
            'id' => 'demo-1',
            'title' => 'Departamento en Monterrey Centro',
            'price' => 1285,
            'location' => 'Monterrey Centro, Nuevo León',
            'description' => 'Hermoso departamento totalmente equipado en el corazón de Monterrey. Perfecto para estancias cortas o largas, incluye todos los servicios y amenidades necesarias.',
            'bedrooms' => 2,
            'bathrooms' => 2,
            'area' => 85,
            'property_type' => 'Departamento',
            'featured' => true,
            'rating' => 4.83,
            'images' => array(
                'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=1200&h=900&fit=crop',
                'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=1200&h=900&fit=crop',
            ),
            'amenities' => array('WiFi', 'Cocina', 'Estacionamiento', 'Aire Acondicionado'),
            'coordinates' => array('lat' => 25.6866, 'lng' => -100.3161),
            'availability' => true,
        ),
        array(
            'id' => 'demo-2',
            'title' => 'Loft en Monterrey Centro',
            'price' => 3084,
            'location' => 'Valle Oriente, Monterrey',
            'description' => 'Loft moderno con vista panorámica y acabados de lujo. Ubicado en Valle Oriente con acceso a restaurantes y centros comerciales.',
            'bedrooms' => 1,
            'bathrooms' => 1,
            'area' => 68,
            'property_type' => 'Loft',
            'featured' => true,
            'rating' => 4.93,
            'images' => array(
                'https://images.unsplash.com/photo-1493809842364-78817add7ffb?w=1200&h=900&fit=crop',
                'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=1200&h=900&fit=crop',
            ),
            'amenities' => array('WiFi', 'Gimnasio', 'Alberca', 'Seguridad 24/7'),
            'coordinates' => array('lat' => 25.6515, 'lng' => -100.2895),
            'availability' => true,
        ),
        array(
            'id' => 'demo-3',
            'title' => 'Departamento en Mitras Centro',
            'price' => 1162,
            'location' => 'Mitras Centro, Monterrey',
            'description' => 'Acogedor departamento ideal para parejas o viajeros de negocios. Ubicación céntrica con acceso a transporte público.',
            'bedrooms' => 1,
            'bathrooms' => 1,
            'area' => 55,
            'property_type' => 'Departamento',
            'featured' => false,
            'rating' => 4.88,
            'images' => array(
                'https://images.unsplash.com/photo-1556020685-ae41abfc9365?w=1200&h=900&fit=crop',
            ),
            'amenities' => array('WiFi', 'Cocina', 'TV Cable'),
            'coordinates' => array('lat' => 25.7378, 'lng' => -100.3515),
            'availability' => true,
        ),
        array(
            'id' => 'demo-4',
            'title' => 'Casa adosada en Monterrey',
            'price' => 1312,
            'location' => 'Contry, Monterrey',
            'description' => 'Espaciosa casa adosada ideal para familias, con jardín privado, cochera para dos autos y zona de parrillada.',
            'bedrooms' => 3,
            'bathrooms' => 2,
            'area' => 120,
            'property_type' => 'Casa',
            'featured' => true,
            'rating' => 4.78,
            'images' => array(
                'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=1200&h=900&fit=crop',
            ),
            'amenities' => array('WiFi', 'Jardín', 'Estacionamiento', 'Lavandería'),
            'coordinates' => array('lat' => 25.6753, 'lng' => -100.3446),
            'availability' => true,
        ),
        array(
            'id' => 'demo-5',
            'title' => 'Departamento con Terraza en San Pedro',
            'price' => 2986,
            'location' => 'San Pedro Garza García, NL',
            'description' => 'Residencia premium en San Pedro con terraza privada y amenidades de clase mundial. Perfecto para estancias ejecutivas.',
            'bedrooms' => 3,
            'bathrooms' => 2,
            'area' => 110,
            'property_type' => 'Departamento',
            'featured' => true,
            'rating' => 5.00,
            'images' => array(
                'https://images.unsplash.com/photo-1515263487990-61b07816b324?w=1200&h=900&fit=crop',
            ),
            'amenities' => array('WiFi', 'Alberca Infinity', 'Gimnasio', 'Business Center', 'Seguridad 24/7'),
            'coordinates' => array('lat' => 25.6613, 'lng' => -100.3589),
            'availability' => true,
        ),
        array(
            'id' => 'demo-6',
            'title' => 'Sky Loft Valle Oriente',
            'price' => 3410,
            'location' => 'Valle Oriente, Monterrey',
            'description' => 'Loft en piso alto con ventanales de piso a techo, acabados de diseñador y amenidades completas.',
            'bedrooms' => 1,
            'bathrooms' => 1,
            'area' => 70,
            'property_type' => 'Loft',
            'featured' => false,
            'rating' => 4.95,
            'images' => array(
                'https://images.unsplash.com/photo-1554995207-c18c203602cb?w=1200&h=900&fit=crop',
            ),
            'amenities' => array('WiFi', 'Sky Lounge', 'Gimnasio', 'Seguridad'),
            'coordinates' => array('lat' => 25.6542, 'lng' => -100.3014),
            'availability' => true,
        ),
        array(
            'id' => 'demo-7',
            'title' => 'Habitación en Cuauhtémoc',
            'price' => 1141,
            'location' => 'Cuauhtémoc, Ciudad de México',
            'description' => 'Cómoda habitación en departamento compartido. Incluye servicios y ambiente amigable. Cerca de metro y comercios.',
            'bedrooms' => 1,
            'bathrooms' => 1,
            'area' => 25,
            'property_type' => 'Habitación',
            'featured' => false,
            'rating' => 4.88,
            'images' => array(
                'https://images.unsplash.com/photo-1616594039964-ae9021a400a0?w=1200&h=900&fit=crop',
            ),
            'amenities' => array('WiFi', 'Cocina Compartida'),
            'coordinates' => array('lat' => 19.4326, 'lng' => -99.1332),
            'availability' => true,
        ),
        array(
            'id' => 'demo-8',
            'title' => 'Departamento en Roma Norte',
            'price' => 1524,
            'location' => 'Roma Norte, Ciudad de México',
            'description' => 'Elegante departamento en la Roma Norte. Rodeado de cafés, galerías y restaurantes. Ideal para vida urbana.',
            'bedrooms' => 2,
            'bathrooms' => 1,
            'area' => 75,
            'property_type' => 'Departamento',
            'featured' => true,
            'rating' => 5.00,
            'images' => array(
                'https://images.unsplash.com/photo-1502672023488-70e25813eb80?w=1200&h=900&fit=crop',
            ),
            'amenities' => array('WiFi', 'Balcón', 'Cocina Equipada', 'Pet Friendly'),
            'coordinates' => array('lat' => 19.4145, 'lng' => -99.1635),
            'availability' => true,
        ),
        array(
            'id' => 'demo-9',
            'title' => 'Alojamiento en El Rosedal',
            'price' => 3765,
            'location' => 'El Rosedal, Monterrey',
            'description' => 'Lujoso alojamiento en zona exclusiva con vistas espectaculares y acabados de primera calidad.',
            'bedrooms' => 2,
            'bathrooms' => 2,
            'area' => 95,
            'property_type' => 'Departamento',
            'featured' => true,
            'rating' => 4.92,
            'images' => array(
                'https://images.unsplash.com/photo-1512918728675-ed5a9ecdebfd?w=1200&h=900&fit=crop',
            ),
            'amenities' => array('WiFi', 'Gimnasio', 'Alberca', 'Terraza', 'Seguridad'),
            'coordinates' => array('lat' => 25.6524, 'lng' => -100.2836),
            'availability' => true,
        ),
        array(
            'id' => 'demo-10',
            'title' => 'Condominio en Cuauhtémoc',
            'price' => 3350,
            'location' => 'Cuauhtémoc, Ciudad de México',
            'description' => 'Elegante condominio con amenidades completas. Diseño moderno y funcional para ejecutivos.',
            'bedrooms' => 2,
            'bathrooms' => 2,
            'area' => 88,
            'property_type' => 'Condominio',
            'featured' => false,
            'rating' => 4.98,
            'images' => array(
                'https://images.unsplash.com/photo-1574643156929-51fa098b0394?w=1200&h=900&fit=crop',
            ),
            'amenities' => array('WiFi', 'Alberca', 'Gimnasio', 'Estacionamiento', 'Roof Garden'),
            'coordinates' => array('lat' => 19.4340, 'lng' => -99.1438),
            'availability' => true,
        ),
        array(
            'id' => 'demo-11',
            'title' => 'Hotel boutique en Santa María la Ribera',
            'price' => 1322,
            'location' => 'Santa María la Ribera, Ciudad de México',
            'description' => 'Encantador espacio tipo boutique hotel con decoración vintage y comodidades modernas.',
            'bedrooms' => 1,
            'bathrooms' => 1,
            'area' => 45,
            'property_type' => 'Suite',
            'featured' => false,
            'rating' => 4.90,
            'images' => array(
                'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1200&h=900&fit=crop',
            ),
            'amenities' => array('WiFi', 'Desayuno Incluido', 'Servicio de Limpieza'),
            'coordinates' => array('lat' => 19.4469, 'lng' => -99.1553),
            'availability' => true,
        ),
        array(
            'id' => 'demo-12',
            'title' => 'Departamento en Ciudad de México',
            'price' => 2582,
            'location' => 'Polanco, Ciudad de México',
            'description' => 'Sofisticado departamento en la exclusiva zona de Polanco. Cerca de museos y restaurantes gourmet.',
            'bedrooms' => 2,
            'bathrooms' => 2,
            'area' => 92,
            'property_type' => 'Departamento',
            'featured' => true,
            'rating' => 5.00,
            'images' => array(
                'https://images.unsplash.com/photo-1567767292278-a4f21aa2d36e?w=1200&h=900&fit=crop',
            ),
            'amenities' => array('WiFi', 'Concierge', 'Valet Parking', 'Gimnasio', 'Spa'),
            'coordinates' => array('lat' => 19.4338, 'lng' => -99.1945),
            'availability' => true,
        ),
        array(
            'id' => 'demo-13',
            'title' => 'Cabaña Boutique en Parras',
            'price' => 986,
            'location' => 'Parras, Coahuila',
            'description' => 'Cabaña boutique entre viñedos con fogatero y diseño contemporáneo. Ideal para escapadas de fin de semana.',
            'bedrooms' => 2,
            'bathrooms' => 1,
            'area' => 70,
            'property_type' => 'Cabaña',
            'featured' => true,
            'rating' => 4.97,
            'images' => array(
                'https://images.unsplash.com/photo-1432297984334-707d2b3c3002?w=1200&h=900&fit=crop',
            ),
            'amenities' => array('WiFi', 'Fogatero', 'Vista a Viñedo'),
            'coordinates' => array('lat' => 25.4380, 'lng' => -102.1729),
            'availability' => true,
        ),
    );
}
