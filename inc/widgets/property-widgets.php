<?php
/**
 * Property Widgets
 * 
 * @package RentWord
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Recent Properties Widget
 */
class RentWord_Recent_Properties_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'rentword_recent_properties',
            __('RentWord: Recent Properties', 'rentword'),
            array('description' => __('Display recent properties', 'rentword'))
        );
    }
    
    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Recent Properties', 'rentword');
        $limit = !empty($instance['limit']) ? intval($instance['limit']) : 5;
        
        $api = rentword_api();
        $properties = $api->get_properties(array('per_page' => $limit));
        
        if (is_wp_error($properties) || empty($properties)) {
            return;
        }
        
        echo $args['before_widget'];
        echo $args['before_title'] . esc_html($title) . $args['after_title'];
        
        echo '<div class="rw-widget-properties">';
        foreach ($properties as $property) {
            $id = rentword_get_property_field($property, 'id');
            $property_title = rentword_get_property_field($property, 'title');
            $price = rentword_get_property_field($property, 'price');
            $image = rentword_get_property_thumbnail($property);
            $url = rentword_get_property_url($id);
            
            ?>
            <div class="rw-widget-property">
                <div class="rw-widget-property-image">
                    <a href="<?php echo esc_url($url); ?>">
                        <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($property_title); ?>">
                    </a>
                </div>
                <div class="rw-widget-property-content">
                    <h4><a href="<?php echo esc_url($url); ?>"><?php echo esc_html($property_title); ?></a></h4>
                    <span class="rw-widget-price"><?php echo rentword_format_price($price); ?></span>
                </div>
            </div>
            <?php
        }
        echo '</div>';
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Recent Properties', 'rentword');
        $limit = !empty($instance['limit']) ? $instance['limit'] : 5;
        
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php esc_html_e('Title:', 'rentword'); ?>
            </label>
            <input class="widefat" 
                   id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" 
                   type="text" 
                   value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('limit')); ?>">
                <?php esc_html_e('Number of properties:', 'rentword'); ?>
            </label>
            <input class="tiny-text" 
                   id="<?php echo esc_attr($this->get_field_id('limit')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('limit')); ?>" 
                   type="number" 
                   value="<?php echo esc_attr($limit); ?>" 
                   min="1" 
                   max="20">
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['limit'] = (!empty($new_instance['limit'])) ? intval($new_instance['limit']) : 5;
        return $instance;
    }
}

/**
 * Property Search Widget
 */
class RentWord_Search_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'rentword_search',
            __('RentWord: Property Search', 'rentword'),
            array('description' => __('Property search form', 'rentword'))
        );
    }
    
    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Search Properties', 'rentword');
        
        echo $args['before_widget'];
        echo $args['before_title'] . esc_html($title) . $args['after_title'];
        
        rentword_search_form();
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Search Properties', 'rentword');
        
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php esc_html_e('Title:', 'rentword'); ?>
            </label>
            <input class="widefat" 
                   id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" 
                   type="text" 
                   value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        return $instance;
    }
}

/**
 * Property Types Widget
 */
class RentWord_Property_Types_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'rentword_property_types',
            __('RentWord: Property Types', 'rentword'),
            array('description' => __('List of property types', 'rentword'))
        );
    }
    
    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Property Types', 'rentword');
        
        $api = rentword_api();
        $property_types = $api->get_property_types();
        
        if (empty($property_types)) {
            return;
        }
        
        echo $args['before_widget'];
        echo $args['before_title'] . esc_html($title) . $args['after_title'];
        
        echo '<ul class="rw-widget-property-types">';
        foreach ($property_types as $type) {
            $url = add_query_arg('property_type', $type, home_url('/properties'));
            echo '<li><a href="' . esc_url($url) . '">' . esc_html($type) . '</a></li>';
        }
        echo '</ul>';
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Property Types', 'rentword');
        
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php esc_html_e('Title:', 'rentword'); ?>
            </label>
            <input class="widefat" 
                   id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" 
                   type="text" 
                   value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        return $instance;
    }
}

/**
 * Register widgets
 */
function rentword_register_widgets() {
    register_widget('RentWord_Recent_Properties_Widget');
    register_widget('RentWord_Search_Widget');
    register_widget('RentWord_Property_Types_Widget');
}
add_action('widgets_init', 'rentword_register_widgets');
