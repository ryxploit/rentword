<?php
/**
 * Gutenberg Blocks Initialization
 * 
 * @package RentWord
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Gutenberg blocks
 */
function rentword_register_blocks() {
    // Register block category
    add_filter('block_categories_all', 'rentword_block_category', 10, 2);
    
    // Register blocks
    register_block_type('rentword/properties-grid', array(
        'editor_script' => 'rentword-blocks',
        'render_callback' => 'rentword_properties_grid_block_render',
        'attributes' => array(
            'limit' => array(
                'type' => 'number',
                'default' => 6,
            ),
            'columns' => array(
                'type' => 'number',
                'default' => 3,
            ),
            'featured' => array(
                'type' => 'boolean',
                'default' => false,
            ),
        ),
    ));
    
    register_block_type('rentword/featured-slider', array(
        'editor_script' => 'rentword-blocks',
        'render_callback' => 'rentword_featured_slider_block_render',
        'attributes' => array(
            'limit' => array(
                'type' => 'number',
                'default' => 10,
            ),
        ),
    ));
    
    register_block_type('rentword/property-search', array(
        'editor_script' => 'rentword-blocks',
        'render_callback' => 'rentword_search_block_render',
    ));
    
    register_block_type('rentword/properties-map', array(
        'editor_script' => 'rentword-blocks',
        'render_callback' => 'rentword_map_block_render',
        'attributes' => array(
            'height' => array(
                'type' => 'string',
                'default' => '500px',
            ),
            'zoom' => array(
                'type' => 'number',
                'default' => 12,
            ),
        ),
    ));
}
add_action('init', 'rentword_register_blocks');

/**
 * Add RentWord block category
 */
function rentword_block_category($categories, $post) {
    return array_merge(
        $categories,
        array(
            array(
                'slug' => 'rentword',
                'title' => __('RentWord', 'rentword'),
                'icon' => 'admin-home',
            ),
        )
    );
}

/**
 * Enqueue block editor assets
 */
function rentword_block_editor_assets() {
    wp_enqueue_script(
        'rentword-blocks',
        RENTWORD_ASSETS_URI . '/js/blocks.js',
        array('wp-blocks', 'wp-element', 'wp-components', 'wp-editor'),
        RENTWORD_VERSION,
        true
    );
    
    wp_localize_script('rentword-blocks', 'rentwordBlockData', array(
        'apiUrl' => rest_url('rentword/v1'),
        'nonce' => wp_create_nonce('wp_rest'),
    ));
}
add_action('enqueue_block_editor_assets', 'rentword_block_editor_assets');

/**
 * Render properties grid block
 */
function rentword_properties_grid_block_render($attributes) {
    $limit = isset($attributes['limit']) ? $attributes['limit'] : 6;
    $columns = isset($attributes['columns']) ? $attributes['columns'] : 3;
    $featured = isset($attributes['featured']) ? $attributes['featured'] : false;
    
    $shortcode = '[rentword_properties limit="' . $limit . '" columns="' . $columns . '"';
    
    if ($featured) {
        $shortcode .= ' featured="1"';
    }
    
    $shortcode .= ']';
    
    return do_shortcode($shortcode);
}

/**
 * Render featured slider block
 */
function rentword_featured_slider_block_render($attributes) {
    $limit = isset($attributes['limit']) ? $attributes['limit'] : 10;
    
    return do_shortcode('[rentword_featured_slider limit="' . $limit . '"]');
}

/**
 * Render search block
 */
function rentword_search_block_render($attributes) {
    return do_shortcode('[rentword_search]');
}

/**
 * Render map block
 */
function rentword_map_block_render($attributes) {
    $height = isset($attributes['height']) ? $attributes['height'] : '500px';
    $zoom = isset($attributes['zoom']) ? $attributes['zoom'] : 12;
    
    return do_shortcode('[rentword_map height="' . $height . '" zoom="' . $zoom . '"]');
}
