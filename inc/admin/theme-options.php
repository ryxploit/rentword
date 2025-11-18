<?php
/**
 * Theme Options Panel
 * 
 * Admin panel for configuring Rentinno API and field mappings
 *
 * @package RentWord
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add Theme Options Menu
 */
function rentword_add_admin_menu() {
    add_menu_page(
        __('RentWord Settings', 'rentword'),
        __('RentWord', 'rentword'),
        'manage_options',
        'rentword-settings',
        'rentword_options_page',
        'dashicons-admin-home',
        61
    );
    
    add_submenu_page(
        'rentword-settings',
        __('API Settings', 'rentword'),
        __('API Settings', 'rentword'),
        'manage_options',
        'rentword-settings',
        'rentword_options_page'
    );
    
    add_submenu_page(
        'rentword-settings',
        __('Field Mapping', 'rentword'),
        __('Field Mapping', 'rentword'),
        'manage_options',
        'rentword-field-mapping',
        'rentword_field_mapping_page'
    );
}
add_action('admin_menu', 'rentword_add_admin_menu');

/**
 * Register Settings
 */
function rentword_settings_init() {
    // API Settings
    register_setting('rentword_api_settings', 'rentword_api_url');
    register_setting('rentword_api_settings', 'rentword_api_key');
    register_setting('rentword_api_settings', 'rentword_cache_duration');
    register_setting('rentword_api_settings', 'rentword_per_page');
    
    // Field Mapping Settings
    register_setting('rentword_field_mapping', 'rentword_field_id');
    register_setting('rentword_field_mapping', 'rentword_field_title');
    register_setting('rentword_field_mapping', 'rentword_field_price');
    register_setting('rentword_field_mapping', 'rentword_field_images');
    register_setting('rentword_field_mapping', 'rentword_field_location');
    register_setting('rentword_field_mapping', 'rentword_field_description');
    register_setting('rentword_field_mapping', 'rentword_field_amenities');
    register_setting('rentword_field_mapping', 'rentword_field_coordinates');
    register_setting('rentword_field_mapping', 'rentword_field_bedrooms');
    register_setting('rentword_field_mapping', 'rentword_field_bathrooms');
    register_setting('rentword_field_mapping', 'rentword_field_property_type');
    register_setting('rentword_field_mapping', 'rentword_field_area');
    register_setting('rentword_field_mapping', 'rentword_field_availability');
    register_setting('rentword_field_mapping', 'rentword_field_featured');
    register_setting('rentword_field_mapping', 'rentword_field_rating');
    
    // Display Settings
    register_setting('rentword_display_settings', 'rentword_currency_symbol');
    register_setting('rentword_display_settings', 'rentword_currency_position');
    register_setting('rentword_display_settings', 'rentword_date_format');
}
add_action('admin_init', 'rentword_settings_init');

/**
 * API Settings Page
 */
function rentword_options_page() {
    $api_url = get_option('rentword_api_url');
    $api_configured = !empty($api_url);
    ?>
    <div class="wrap rentword-admin-wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <?php if (!$api_configured): ?>
        <div class="notice notice-warning">
            <p><strong><?php esc_html_e('¡Importante!', 'rentword'); ?></strong> <?php esc_html_e('Para mostrar propiedades en tu sitio, debes configurar la URL de la API de Rentinno a continuación.', 'rentword'); ?></p>
        </div>
        <?php endif; ?>
        
        <div class="rentword-admin-intro">
            <h2><?php esc_html_e('🏠 Configuración de la API de Propiedades', 'rentword'); ?></h2>
            <p><?php esc_html_e('Conecta tu sitio con la API de Rentinno para mostrar propiedades automáticamente. Sigue los pasos a continuación:', 'rentword'); ?></p>
        </div>
        
        <form action="options.php" method="post">
            <?php
            settings_fields('rentword_api_settings');
            ?>
            
            <div class="rentword-admin-section">
                <h3 class="rentword-section-title"><?php esc_html_e('🔌 Conexión API', 'rentword'); ?></h3>
                
                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="rentword_api_url">
                                    <?php esc_html_e('URL de la API', 'rentword'); ?>
                                    <span class="required">*</span>
                                </label>
                            </th>
                            <td>
                                <input type="url" 
                                       id="rentword_api_url" 
                                       name="rentword_api_url" 
                                       value="<?php echo esc_attr(get_option('rentword_api_url')); ?>" 
                                       class="large-text rentword-api-input" 
                                       placeholder="https://api.rentinno.com/v1/properties"
                                       required>
                                <p class="description">
                                    <strong><?php esc_html_e('Paso 1:', 'rentword'); ?></strong> 
                                    <?php esc_html_e('Ingresa la URL completa del endpoint de la API de Rentinno que devuelve las propiedades.', 'rentword'); ?>
                                    <br>
                                    <em><?php esc_html_e('Ejemplo: https://api.rentinno.com/v1/properties o https://tu-servidor.com/api/properties', 'rentword'); ?></em>
                                </p>
                            </td>
                            </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="rentword_api_key"><?php esc_html_e('Clave API (Opcional)', 'rentword'); ?></label>
                            </th>
                            <td>
                                <input type="password" 
                                       id="rentword_api_key" 
                                       name="rentword_api_key" 
                                       value="<?php echo esc_attr(get_option('rentword_api_key')); ?>" 
                                       class="large-text"
                                       placeholder="Tu clave API (si es requerida)">
                                <p class="description">
                                    <strong><?php esc_html_e('Paso 2:', 'rentword'); ?></strong> 
                                    <?php esc_html_e('Si tu API requiere autenticación, ingresa la clave aquí. De lo contrario, déjalo en blanco.', 'rentword'); ?>
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="rentword-admin-section">
                <h3 class="rentword-section-title"><?php esc_html_e('⚙️ Configuración Avanzada', 'rentword'); ?></h3>
                
                <table class="form-table" role="presentation">
                    <tbody>
                    <tbody>
                    
                    <tr>
                        <th scope="row">
                            <label for="rentword_cache_duration"><?php esc_html_e('Duración del Caché (horas)', 'rentword'); ?></label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="rentword_cache_duration" 
                                   name="rentword_cache_duration" 
                                   value="<?php echo esc_attr(get_option('rentword_cache_duration', 1)); ?>" 
                                   min="0" 
                                   max="168" 
                                   class="small-text">
                            <p class="description">
                                <?php esc_html_e('Tiempo para almacenar en caché las respuestas de la API (0 para desactivar). Recomendado: 1-24 horas.', 'rentword'); ?>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="rentword_per_page"><?php esc_html_e('Propiedades por Página', 'rentword'); ?></label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="rentword_per_page" 
                                   name="rentword_per_page" 
                                   value="<?php echo esc_attr(get_option('rentword_per_page', 12)); ?>" 
                                   min="1" 
                                   max="100" 
                                   class="small-text">
                            <p class="description">
                                <?php esc_html_e('Número de propiedades a mostrar por página. Recomendado: 12 o 24.', 'rentword'); ?>
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
            
            <div class="rentword-admin-section">
                <h3 class="rentword-section-title"><?php esc_html_e('💰 Configuración de Moneda', 'rentword'); ?></h3>
            
            <table class="form-table" role="presentation">
                <tbody>
                <tbody>
                    <tr>
                        <th scope="row">
                            <label for="rentword_currency_symbol"><?php esc_html_e('Símbolo de Moneda', 'rentword'); ?></label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="rentword_currency_symbol" 
                                   name="rentword_currency_symbol" 
                                   value="<?php echo esc_attr(get_option('rentword_currency_symbol', '$')); ?>" 
                                   class="small-text">
                            <p class="description">
                                <?php esc_html_e('Ejemplos: $, €, £, MXN, USD', 'rentword'); ?>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="rentword_currency_position"><?php esc_html_e('Posición de la Moneda', 'rentword'); ?></label>
                        </th>
                        <td>
                            <select id="rentword_currency_position" name="rentword_currency_position">
                                <option value="before" <?php selected(get_option('rentword_currency_position', 'before'), 'before'); ?>>
                                    <?php esc_html_e('Antes ($100)', 'rentword'); ?>
                                </option>
                                <option value="after" <?php selected(get_option('rentword_currency_position', 'before'), 'after'); ?>>
                                    <?php esc_html_e('Después (100$)', 'rentword'); ?>
                                </option>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
            
            <div class="rentword-test-section">
                <h3><?php esc_html_e('🔍 Probar Conexión API', 'rentword'); ?></h3>
                <p><?php esc_html_e('Antes de guardar, prueba que tu API esté funcionando correctamente:', 'rentword'); ?></p>
                <button type="button" class="button button-large button-secondary" id="rentword-test-api">
                    <?php esc_html_e('🚀 Probar Conexión Ahora', 'rentword'); ?>
                </button>
                <button type="button" class="button button-large" id="rentword-clear-cache" style="margin-left: 10px;">
                    <?php esc_html_e('🗑️ Limpiar Caché', 'rentword'); ?>
                </button>
                <div id="rentword-api-test-result" style="margin-top: 15px;"></div>
                <div id="rentword-cache-result" style="margin-top: 15px;"></div>
            </div>
            
            <?php submit_button(__('💾 Guardar Configuración', 'rentword'), 'primary large'); ?>
        </form>
    </div>
    
    <style>
    .rentword-admin-wrap {
        background: #f9f9f9;
        margin: 20px 20px 20px 0;
        padding: 30px;
    }
    .rentword-admin-intro {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 8px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .rentword-admin-intro h2 {
        margin-top: 0;
        color: white;
        font-size: 24px;
    }
    .rentword-admin-intro p {
        margin-bottom: 0;
        opacity: 0.95;
        font-size: 16px;
    }
    .rentword-admin-section {
        background: white;
        padding: 25px;
        margin-bottom: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .rentword-section-title {
        margin-top: 0;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f0f0;
        font-size: 18px;
        color: #333;
    }
    .rentword-api-input {
        border: 2px solid #667eea !important;
        padding: 12px !important;
        font-size: 14px !important;
    }
    .rentword-api-input:focus {
        border-color: #764ba2 !important;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1) !important;
    }
    .required {
        color: #dc3545;
        font-weight: bold;
    }
    .rentword-test-section {
        background: #e3f2fd;
        padding: 25px;
        border-radius: 8px;
        border: 2px solid #2196f3;
        margin-bottom: 20px;
    }
    .rentword-test-section h3 {
        margin-top: 0;
        color: #1976d2;
    }
    #rentword-api-test-result .notice {
        margin: 0;
        padding: 15px;
        border-radius: 6px;
    }
    </style>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const testButton = document.getElementById('rentword-test-api');
        if (testButton) {
            testButton.addEventListener('click', function() {
                const button = this;
                const resultDiv = document.getElementById('rentword-api-test-result');
                
                button.disabled = true;
                button.textContent = '⏳ <?php esc_html_e('Probando conexión...', 'rentword'); ?>';
                resultDiv.innerHTML = '<p style="color: #666;"><?php esc_html_e('Conectando con la API...', 'rentword'); ?></p>';
                
                const params = new URLSearchParams({
                    action: 'rentword_test_api',
                    nonce: '<?php echo wp_create_nonce('rentword_test_api'); ?>',
                    api_url: document.getElementById('rentword_api_url').value,
                    api_key: document.getElementById('rentword_api_key').value
                });
                
                fetch(ajaxurl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: params.toString()
                })
                .then(response => response.json())
                .then(response => {
                    if (response.success) {
                        let html = '<div class="notice notice-success inline"><p><strong>✅ ' + response.data.message + '</strong></p>';
                        if (response.data.debug) {
                            html += '<details style="margin-top: 10px;"><summary style="cursor: pointer; font-weight: bold;">🔍 Ver detalles técnicos</summary>';
                            html += '<pre style="background: #f5f5f5; padding: 10px; margin-top: 10px; overflow-x: auto;">';
                            html += 'Llaves encontradas en la respuesta:\n' + JSON.stringify(response.data.debug.response_keys, null, 2);
                            html += '\n\nNúmero de propiedades: ' + response.data.debug.property_count;
                            html += '</pre></details>';
                            html += '</div>';
                        }
                        resultDiv.innerHTML = html;
                    } else {
                        resultDiv.innerHTML = '<div class="notice notice-error inline"><p><strong>❌ ' + response.data.message + '</strong></p></div>';
                    }
                })
                .catch(() => {
                    resultDiv.innerHTML = '<div class="notice notice-error inline"><p><strong>❌ <?php esc_html_e('Error de conexión. Verifica la URL y tu conexión a internet.', 'rentword'); ?></strong></p></div>';
                })
                .finally(() => {
                    button.disabled = false;
                    button.textContent = '🚀 <?php esc_html_e('Probar Conexión Ahora', 'rentword'); ?>';
                });
            });
        }
        
        const clearCacheButton = document.getElementById('rentword-clear-cache');
        if (clearCacheButton) {
            clearCacheButton.addEventListener('click', function() {
                const button = this;
                const resultDiv = document.getElementById('rentword-cache-result');
                
                button.disabled = true;
                button.textContent = '⏳ <?php esc_html_e('Limpiando caché...', 'rentword'); ?>';
                
                const params = new URLSearchParams({
                    action: 'rentword_clear_cache',
                    nonce: '<?php echo wp_create_nonce('rentword_clear_cache'); ?>'
                });
                
                fetch(ajaxurl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: params.toString()
                })
                .then(response => response.json())
                .then(response => {
                    if (response.success) {
                        resultDiv.innerHTML = '<div class="notice notice-success inline"><p><strong>✅ ' + response.data.message + '</strong></p></div>';
                        setTimeout(() => {
                            resultDiv.innerHTML = '';
                        }, 3000);
                    } else {
                        resultDiv.innerHTML = '<div class="notice notice-error inline"><p><strong>❌ ' + response.data.message + '</strong></p></div>';
                    }
                })
                .catch(() => {
                    resultDiv.innerHTML = '<div class="notice notice-error inline"><p><strong>❌ <?php esc_html_e('Error al limpiar el caché.', 'rentword'); ?></strong></p></div>';
                })
                .finally(() => {
                    button.disabled = false;
                    button.textContent = '🗑️ <?php esc_html_e('Limpiar Caché', 'rentword'); ?>';
                });
            });
        }
    });
    </script>
    <?php
}

/**
 * Field Mapping Page
 */
function rentword_field_mapping_page() {
    // Check if auto-detection has been done
    $auto_detected = get_option('rentword_auto_detection_fields', array());
    $detection_time = get_option('rentword_auto_detection_done', '');
    
    // Get current saved values (these might be user overrides or detected values)
    $current_mapping = rentword_get_field_mapping();
    
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Field Mapping', 'rentword'); ?></h1>
        
        <?php if (!empty($auto_detected)): ?>
        <div class="notice notice-success" style="padding: 15px; margin: 20px 0;">
            <h3 style="margin-top: 0;">
                <span class="dashicons dashicons-yes-alt" style="color: #46b450;"></span>
                <?php esc_html_e('Auto-Detection Active', 'rentword'); ?>
            </h3>
            <p><strong><?php esc_html_e('RentWord automatically detected your API structure!', 'rentword'); ?></strong></p>
            <p><?php printf(esc_html__('Last scan: %s', 'rentword'), esc_html($detection_time)); ?></p>
            
            <div style="background: #fff; padding: 15px; border-left: 4px solid #46b450; margin: 15px 0;">
                <h4 style="margin-top: 0;"><?php esc_html_e('Detected Fields:', 'rentword'); ?></h4>
                <ul style="list-style: disc; margin-left: 20px;">
                    <?php foreach ($auto_detected as $internal => $api_field): ?>
                        <li>
                            <strong><?php echo esc_html(ucwords(str_replace('_', ' ', $internal))); ?>:</strong>
                            <code style="background: #f0f0f0; padding: 2px 6px; border-radius: 3px;"><?php echo esc_html($api_field); ?></code>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <p>
                <button type="button" class="button button-secondary" onclick="if(confirm('¿Resetear auto-detección y volver a escanear tu API?')) { document.getElementById('rescan-form').submit(); }">
                    <span class="dashicons dashicons-update" style="vertical-align: middle;"></span>
                    <?php esc_html_e('Re-Scan API', 'rentword'); ?>
                </button>
            </p>
        </div>
        
        <form id="rescan-form" method="post" style="display:none;">
            <?php wp_nonce_field('rentword_rescan_api', 'rentword_rescan_nonce'); ?>
            <input type="hidden" name="rentword_rescan_api" value="1">
        </form>
        
        <div class="notice notice-info">
            <p><strong><?php esc_html_e('Note:', 'rentword'); ?></strong> <?php esc_html_e('The fields below are optional. Leave them empty to use auto-detection, or fill them to override specific fields.', 'rentword'); ?></p>
        </div>
        <?php else: ?>
        <div class="notice notice-info" style="padding: 15px;">
            <h3 style="margin-top: 0;">
                <span class="dashicons dashicons-info" style="color: #72aee6;"></span>
                <?php esc_html_e('Smart Auto-Detection', 'rentword'); ?>
            </h3>
            <p><?php esc_html_e('RentWord will automatically detect field names from your API the first time it fetches properties. You can leave these fields empty and let the system do the work!', 'rentword'); ?></p>
            <p><?php esc_html_e('The system recognizes common field variations in English and Spanish:', 'rentword'); ?></p>
            <ul style="list-style: disc; margin-left: 20px;">
                <li><strong>Price:</strong> price, precio, cost, costo, nightly_rate, rate</li>
                <li><strong>Images:</strong> images, imagenes, photos, fotos, gallery, galeria</li>
                <li><strong>Title:</strong> title, name, titulo, nombre, property_name</li>
                <li><strong>Location:</strong> location, ubicacion, address, direccion, city, ciudad</li>
                <li><?php esc_html_e('...and many more!', 'rentword'); ?></li>
            </ul>
        </div>
        <?php endif; ?>
        
        <p><?php esc_html_e('Map the fields from your API JSON response to RentWord theme fields. Enter the exact key name as it appears in the API response, or leave empty for auto-detection.', 'rentword'); ?></p>
        
        <form action="options.php" method="post"><?php
    
    // Handle rescan request
    if (isset($_POST['rentword_rescan_api']) && check_admin_referer('rentword_rescan_api', 'rentword_rescan_nonce')) {
        delete_option('rentword_auto_detection_done');
        delete_option('rentword_auto_detection_fields');
        
        // Clear cache to force new API call
        global $wpdb;
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_rentword_%'");
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_rentword_%'");
        
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Auto-detection reset! The system will re-scan your API on the next property fetch.', 'rentword') . '</p></div>';
    }
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Field Mapping', 'rentword'); ?></h1>
        
        <p><?php esc_html_e('Map the fields from your Rentinno API JSON response to RentWord theme fields. Enter the exact key name as it appears in the API response.', 'rentword'); ?></p>
        
        <form action="options.php" method="post">
            <?php
            settings_fields('rentword_field_mapping');
            ?>
            
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row">
                            <label for="rentword_field_id"><?php esc_html_e('Property ID', 'rentword'); ?></label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="rentword_field_id" 
                                   name="rentword_field_id" 
                                   value="<?php echo esc_attr(get_option('rentword_field_id', 'id')); ?>" 
                                   class="regular-text">
                            <p class="description"><?php esc_html_e('Example: id, property_id, listing_id', 'rentword'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="rentword_field_title"><?php esc_html_e('Title', 'rentword'); ?></label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="rentword_field_title" 
                                   name="rentword_field_title" 
                                   value="<?php echo esc_attr(get_option('rentword_field_title', 'title')); ?>" 
                                   class="regular-text">
                            <p class="description"><?php esc_html_e('Example: title, name, property_name', 'rentword'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="rentword_field_price"><?php esc_html_e('Price', 'rentword'); ?></label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="rentword_field_price" 
                                   name="rentword_field_price" 
                                   value="<?php echo esc_attr(get_option('rentword_field_price', 'price')); ?>" 
                                   class="regular-text">
                            <p class="description"><?php esc_html_e('Example: price, cost, nightly_rate', 'rentword'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="rentword_field_images"><?php esc_html_e('Images', 'rentword'); ?></label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="rentword_field_images" 
                                   name="rentword_field_images" 
                                   value="<?php echo esc_attr(get_option('rentword_field_images', 'images')); ?>" 
                                   class="regular-text">
                            <p class="description"><?php esc_html_e('Example: images, photos, gallery (expects array of URLs)', 'rentword'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="rentword_field_location"><?php esc_html_e('Location', 'rentword'); ?></label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="rentword_field_location" 
                                   name="rentword_field_location" 
                                   value="<?php echo esc_attr(get_option('rentword_field_location', 'location')); ?>" 
                                   class="regular-text">
                            <p class="description"><?php esc_html_e('Example: location, address, city', 'rentword'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="rentword_field_description"><?php esc_html_e('Description', 'rentword'); ?></label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="rentword_field_description" 
                                   name="rentword_field_description" 
                                   value="<?php echo esc_attr(get_option('rentword_field_description', 'description')); ?>" 
                                   class="regular-text">
                            <p class="description"><?php esc_html_e('Example: description, details, summary', 'rentword'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="rentword_field_amenities"><?php esc_html_e('Amenities', 'rentword'); ?></label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="rentword_field_amenities" 
                                   name="rentword_field_amenities" 
                                   value="<?php echo esc_attr(get_option('rentword_field_amenities', 'amenities')); ?>" 
                                   class="regular-text">
                            <p class="description"><?php esc_html_e('Example: amenities, features, facilities (expects array)', 'rentword'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="rentword_field_coordinates"><?php esc_html_e('Coordinates', 'rentword'); ?></label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="rentword_field_coordinates" 
                                   name="rentword_field_coordinates" 
                                   value="<?php echo esc_attr(get_option('rentword_field_coordinates', 'coordinates')); ?>" 
                                   class="regular-text">
                            <p class="description"><?php esc_html_e('Example: coordinates, geo, latlng (expects object with lat/lng or latitude/longitude)', 'rentword'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="rentword_field_bedrooms"><?php esc_html_e('Bedrooms', 'rentword'); ?></label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="rentword_field_bedrooms" 
                                   name="rentword_field_bedrooms" 
                                   value="<?php echo esc_attr(get_option('rentword_field_bedrooms', 'bedrooms')); ?>" 
                                   class="regular-text">
                            <p class="description"><?php esc_html_e('Example: bedrooms, beds, bedroom_count', 'rentword'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="rentword_field_bathrooms"><?php esc_html_e('Bathrooms', 'rentword'); ?></label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="rentword_field_bathrooms" 
                                   name="rentword_field_bathrooms" 
                                   value="<?php echo esc_attr(get_option('rentword_field_bathrooms', 'bathrooms')); ?>" 
                                   class="regular-text">
                            <p class="description"><?php esc_html_e('Example: bathrooms, baths, bathroom_count', 'rentword'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="rentword_field_property_type"><?php esc_html_e('Property Type', 'rentword'); ?></label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="rentword_field_property_type" 
                                   name="rentword_field_property_type" 
                                   value="<?php echo esc_attr(get_option('rentword_field_property_type', 'property_type')); ?>" 
                                   class="regular-text">
                            <p class="description"><?php esc_html_e('Example: property_type, type, category', 'rentword'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="rentword_field_area"><?php esc_html_e('Area/Size', 'rentword'); ?></label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="rentword_field_area" 
                                   name="rentword_field_area" 
                                   value="<?php echo esc_attr(get_option('rentword_field_area', 'area')); ?>" 
                                   class="regular-text">
                            <p class="description"><?php esc_html_e('Example: area, size, square_feet', 'rentword'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="rentword_field_availability"><?php esc_html_e('Availability', 'rentword'); ?></label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="rentword_field_availability" 
                                   name="rentword_field_availability" 
                                   value="<?php echo esc_attr(get_option('rentword_field_availability', 'availability')); ?>" 
                                   class="regular-text">
                            <p class="description"><?php esc_html_e('Example: availability, available_dates, calendar', 'rentword'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="rentword_field_featured"><?php esc_html_e('Featured', 'rentword'); ?></label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="rentword_field_featured" 
                                   name="rentword_field_featured" 
                                   value="<?php echo esc_attr(get_option('rentword_field_featured', 'featured')); ?>" 
                                   class="regular-text">
                            <p class="description"><?php esc_html_e('Example: featured, is_featured, highlighted (expects boolean)', 'rentword'); ?></p>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/**
 * AJAX handler for testing API connection
 */
function rentword_test_api_connection() {
    check_ajax_referer('rentword_test_api', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => __('Unauthorized', 'rentword')));
    }
    
    $api_url = isset($_POST['api_url']) ? esc_url_raw($_POST['api_url']) : '';
    $api_key = isset($_POST['api_key']) ? sanitize_text_field($_POST['api_key']) : '';
    
    if (empty($api_url)) {
        wp_send_json_error(array('message' => __('Please enter an API URL.', 'rentword')));
    }
    
    $args = array(
        'timeout' => 15,
        'headers' => array(
            'Accept' => 'application/json',
        ),
    );
    
    if (!empty($api_key)) {
        $args['headers']['Authorization'] = 'Bearer ' . $api_key;
    }
    
    $response = wp_remote_get($api_url, $args);
    
    if (is_wp_error($response)) {
        wp_send_json_error(array('message' => $response->get_error_message()));
    }
    
    $code = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);
    
    if ($code === 200) {
        $data = json_decode($body, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            // Check different response structures
            $count = 0;
            $structure_info = '';
            
            if (isset($data['data']) && is_array($data['data'])) {
                $count = count($data['data']);
                $structure_info = sprintf(__('Found properties in data.data array. Top-level keys: %s', 'rentword'), implode(', ', array_keys($data)));
            } elseif (isset($data['properties']) && is_array($data['properties'])) {
                $count = count($data['properties']);
                $structure_info = sprintf(__('Found properties in data.properties array. Top-level keys: %s', 'rentword'), implode(', ', array_keys($data)));
            } elseif (is_array($data)) {
                // Check if it's an array of properties or a paginated response
                if (isset($data[0]) && is_array($data[0])) {
                    $count = count($data);
                    $structure_info = sprintf(__('Found properties in direct array. First property keys: %s', 'rentword'), implode(', ', array_keys($data[0])));
                } else {
                    // Might be paginated response
                    $count = is_array($data) ? count($data) : 0;
                    $structure_info = sprintf(__('Response top-level keys: %s', 'rentword'), implode(', ', array_keys($data)));
                    
                    // Check for common pagination patterns
                    if (isset($data['items']) && is_array($data['items'])) {
                        $count = count($data['items']);
                        $structure_info .= sprintf(__(' | Found %d items in data.items', 'rentword'), $count);
                    } elseif (isset($data['results']) && is_array($data['results'])) {
                        $count = count($data['results']);
                        $structure_info .= sprintf(__(' | Found %d items in data.results', 'rentword'), $count);
                    }
                }
            }
            
            wp_send_json_success(array(
                'message' => sprintf(
                    __('Connection successful! Found %d properties. %s', 'rentword'),
                    $count,
                    $structure_info
                ),
                'debug' => array(
                    'response_keys' => array_keys($data),
                    'property_count' => $count
                )
            ));
        } else {
            wp_send_json_error(array('message' => __('API returned invalid JSON.', 'rentword')));
        }
    } else {
        wp_send_json_error(array(
            'message' => sprintf(__('API returned status code %d', 'rentword'), $code)
        ));
    }
}
add_action('wp_ajax_rentword_test_api', 'rentword_test_api_connection');

/**
 * AJAX handler for clearing API cache
 */
function rentword_clear_api_cache() {
    check_ajax_referer('rentword_clear_cache', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => __('Unauthorized', 'rentword')));
    }
    
    // Clear all rentword API transients
    global $wpdb;
    $deleted = $wpdb->query(
        "DELETE FROM {$wpdb->options} 
         WHERE option_name LIKE '_transient_rentword_properties_%' 
         OR option_name LIKE '_transient_timeout_rentword_properties_%'"
    );
    
    wp_send_json_success(array(
        'message' => sprintf(
            __('Caché limpiado correctamente. Se eliminaron %d registros. Las propiedades se cargarán de nuevo desde la API.', 'rentword'),
            $deleted
        )
    ));
}
add_action('wp_ajax_rentword_clear_cache', 'rentword_clear_api_cache');
