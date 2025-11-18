<?php
/**
 * Theme Customizer
 * 
 * Personalización completa del tema sin código
 *
 * @package RentWord
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register customizer settings
 */
function rentword_customize_register($wp_customize) {
    
    // ========================================
    // SECCIÓN: COLORES DEL TEMA
    // ========================================
    
    $wp_customize->add_section('rentword_colors', array(
        'title' => __('Colores del Tema', 'rentword'),
        'priority' => 30,
    ));
    
    // Color Primario (Turquesa)
    $wp_customize->add_setting('rentword_primary_color', array(
        'default' => '#5EBFB3',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'rentword_primary_color', array(
        'label' => __('Color Primario (Turquesa)', 'rentword'),
        'description' => __('Color principal: fondo hero, elementos destacados', 'rentword'),
        'section' => 'rentword_colors',
        'settings' => 'rentword_primary_color',
    )));
    
    // Color Secundario (Naranja)
    $wp_customize->add_setting('rentword_secondary_color', array(
        'default' => '#E89B6D',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'rentword_secondary_color', array(
        'label' => __('Color Secundario (Naranja)', 'rentword'),
        'description' => __('Color de acento: tarjetas, botones, sidebar', 'rentword'),
        'section' => 'rentword_colors',
        'settings' => 'rentword_secondary_color',
    )));
    
    // Color Overlay (Oscuro)
    $wp_customize->add_setting('rentword_overlay_color', array(
        'default' => '#2D6A6A',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'rentword_overlay_color', array(
        'label' => __('Color Overlay (Turquesa Oscuro)', 'rentword'),
        'description' => __('Para overlays y gradientes', 'rentword'),
        'section' => 'rentword_colors',
        'settings' => 'rentword_overlay_color',
    )));
    
    // Color de Texto
    $wp_customize->add_setting('rentword_text_color', array(
        'default' => '#222222',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'rentword_text_color', array(
        'label' => __('Color de Texto', 'rentword'),
        'description' => __('Color del texto principal', 'rentword'),
        'section' => 'rentword_colors',
        'settings' => 'rentword_text_color',
    )));
    
    // Color de Fondo
    $wp_customize->add_setting('rentword_background_color', array(
        'default' => '#FFFFFF',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'rentword_background_color', array(
        'label' => __('Color de Fondo', 'rentword'),
        'description' => __('Color de fondo del sitio', 'rentword'),
        'section' => 'rentword_colors',
        'settings' => 'rentword_background_color',
    )));
    
    // Habilitar Gradientes
    $wp_customize->add_setting('rentword_enable_gradients', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('rentword_enable_gradients', array(
        'label' => __('Habilitar Gradientes', 'rentword'),
        'description' => __('Gradientes suaves en fondos y elementos', 'rentword'),
        'section' => 'rentword_colors',
        'type' => 'checkbox',
    ));
    
    // Glassmorphism
    $wp_customize->add_setting('rentword_glassmorphism', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('rentword_glassmorphism', array(
        'label' => __('Efecto Glassmorphism', 'rentword'),
        'description' => __('Tarjetas con efecto cristal/blur', 'rentword'),
        'section' => 'rentword_colors',
        'type' => 'checkbox',
    ));
    
    // Radio de Bordes
    $wp_customize->add_setting('rentword_border_radius', array(
        'default' => '24',
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control('rentword_border_radius', array(
        'label' => __('Radio de Bordes (px)', 'rentword'),
        'description' => __('Redondez de tarjetas y botones', 'rentword'),
        'section' => 'rentword_colors',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 0,
            'max' => 50,
            'step' => 2,
        ),
    ));
    
    // ========================================
    // SECCIÓN: AJUSTES DE PÁGINA DE INICIO
    // ========================================
    
    $wp_customize->add_section('rentword_homepage', array(
        'title' => __('Ajustes de la página de inicio', 'rentword'),
        'priority' => 35,
    ));
    
    // Texto Hero
    $wp_customize->add_setting('rentword_hero_title', array(
        'default' => __('Encuentra tu Hogar Perfecto', 'rentword'),
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));
    
    $wp_customize->add_control('rentword_hero_title', array(
        'label' => __('Título Hero (Portada)', 'rentword'),
        'description' => __('Título grande que aparece en la página de inicio', 'rentword'),
        'section' => 'rentword_homepage',
        'type' => 'text',
    ));
    
    // Subtítulo Hero
    $wp_customize->add_setting('rentword_hero_subtitle', array(
        'default' => __('Miles de propiedades vacacionales en los mejores destinos', 'rentword'),
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport' => 'refresh',
    ));
    
    $wp_customize->add_control('rentword_hero_subtitle', array(
        'label' => __('Subtítulo Hero', 'rentword'),
        'description' => __('Texto descriptivo debajo del título principal', 'rentword'),
        'section' => 'rentword_homepage',
        'type' => 'textarea',
    ));
    
    // Imagen Hero Background
    $wp_customize->add_setting('rentword_hero_image', array(
        'default' => '',
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'rentword_hero_image', array(
        'label' => __('Imagen de Fondo (Hero)', 'rentword'),
        'description' => __('Imagen grande para la sección principal de inicio', 'rentword'),
        'section' => 'rentword_homepage',
        'mime_type' => 'image',
    )));
    
    // Texto del Botón CTA
    $wp_customize->add_setting('rentword_cta_text', array(
        'default' => __('Explorar Propiedades', 'rentword'),
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));
    
    $wp_customize->add_control('rentword_cta_text', array(
        'label' => __('Texto Botón Principal', 'rentword'),
        'section' => 'rentword_homepage',
        'type' => 'text',
    ));
    
    // ========================================
    // SECCIÓN: IMÁGENES Y LOGO
    // ========================================
    
    $wp_customize->add_section('rentword_images', array(
        'title' => __('Imágenes y Multimedia', 'rentword'),
        'priority' => 40,
    ));
    
    // Favicon personalizado
    $wp_customize->add_setting('rentword_favicon', array(
        'default' => '',
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'rentword_favicon', array(
        'label' => __('Favicon (32x32px)', 'rentword'),
        'description' => __('Ícono que aparece en la pestaña del navegador', 'rentword'),
        'section' => 'rentword_images',
        'mime_type' => 'image',
    )));
    
    // ========================================
    // SECCIÓN: CONFIGURACIÓN DE PROPIEDADES
    // ========================================
    
    $wp_customize->add_section('rentword_properties', array(
        'title' => __('Configuración de Propiedades', 'rentword'),
        'priority' => 45,
    ));
    
    // Propiedades por página
    $wp_customize->add_setting('rentword_properties_per_page', array(
        'default' => 12,
        'sanitize_callback' => 'absint',
        'transport' => 'refresh',
    ));
    
    $wp_customize->add_control('rentword_properties_per_page', array(
        'label' => __('Propiedades por Página', 'rentword'),
        'description' => __('Número de propiedades que se muestran en el listado', 'rentword'),
        'section' => 'rentword_properties',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 1,
            'max' => 100,
            'step' => 1,
        ),
    ));
    
    // Mostrar precio
    $wp_customize->add_setting('rentword_show_price', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport' => 'refresh',
    ));
    
    $wp_customize->add_control('rentword_show_price', array(
        'label' => __('Mostrar Precios', 'rentword'),
        'description' => __('Mostrar precios en las tarjetas de propiedades', 'rentword'),
        'section' => 'rentword_properties',
        'type' => 'checkbox',
    ));
    
    // Símbolo de moneda
    $wp_customize->add_setting('rentword_currency_symbol', array(
        'default' => '$',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));
    
    $wp_customize->add_control('rentword_currency_symbol', array(
        'label' => __('Símbolo de Moneda', 'rentword'),
        'description' => __('Ejemplo: $, €, £, MXN', 'rentword'),
        'section' => 'rentword_properties',
        'type' => 'text',
    ));
    
    // Posición del símbolo
    $wp_customize->add_setting('rentword_currency_position', array(
        'default' => 'before',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));
    
    $wp_customize->add_control('rentword_currency_position', array(
        'label' => __('Posición del Símbolo', 'rentword'),
        'section' => 'rentword_properties',
        'type' => 'select',
        'choices' => array(
            'before' => __('Antes ($1,500)', 'rentword'),
            'after' => __('Después (1,500 MXN)', 'rentword'),
        ),
    ));
    
    // ========================================
    // SECCIÓN: REDES SOCIALES
    // ========================================
    
    $wp_customize->add_section('rentword_social', array(
        'title' => __('Redes Sociales', 'rentword'),
        'priority' => 50,
    ));
    
    // Facebook
    $wp_customize->add_setting('rentword_facebook', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('rentword_facebook', array(
        'label' => __('Facebook URL', 'rentword'),
        'section' => 'rentword_social',
        'type' => 'url',
    ));
    
    // Instagram
    $wp_customize->add_setting('rentword_instagram', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('rentword_instagram', array(
        'label' => __('Instagram URL', 'rentword'),
        'section' => 'rentword_social',
        'type' => 'url',
    ));
    
    // Twitter
    $wp_customize->add_setting('rentword_twitter', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('rentword_twitter', array(
        'label' => __('Twitter URL', 'rentword'),
        'section' => 'rentword_social',
        'type' => 'url',
    ));
    
    // WhatsApp
    $wp_customize->add_setting('rentword_whatsapp', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('rentword_whatsapp', array(
        'label' => __('WhatsApp (con código de país)', 'rentword'),
        'description' => __('Ejemplo: 52XXXXXXXXXX', 'rentword'),
        'section' => 'rentword_social',
        'type' => 'text',
    ));
    
    // ========================================
    // SECCIÓN: INFORMACIÓN DEL HOST
    // ========================================
    
    $wp_customize->add_section('rentword_host_info', array(
        'title' => __('Información del Anfitrión/Host', 'rentword'),
        'priority' => 52,
    ));
    
    // Nombre del Host
    $wp_customize->add_setting('rentword_host_name', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));
    
    $wp_customize->add_control('rentword_host_name', array(
        'label' => __('Nombre del Anfitrión', 'rentword'),
        'description' => __('Nombre completo del host/propietario', 'rentword'),
        'section' => 'rentword_host_info',
        'type' => 'text',
    ));
    
    // Email del Host
    $wp_customize->add_setting('rentword_host_email', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_email',
        'transport' => 'refresh',
    ));
    
    $wp_customize->add_control('rentword_host_email', array(
        'label' => __('Email del Anfitrión', 'rentword'),
        'section' => 'rentword_host_info',
        'type' => 'email',
    ));
    
    // Teléfono del Host
    $wp_customize->add_setting('rentword_host_phone', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));
    
    $wp_customize->add_control('rentword_host_phone', array(
        'label' => __('Teléfono del Anfitrión', 'rentword'),
        'description' => __('Ejemplo: +52 811 234 5678', 'rentword'),
        'section' => 'rentword_host_info',
        'type' => 'tel',
    ));
    
    // WhatsApp del Host
    $wp_customize->add_setting('rentword_host_whatsapp', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));
    
    $wp_customize->add_control('rentword_host_whatsapp', array(
        'label' => __('WhatsApp del Anfitrión (con código de país)', 'rentword'),
        'description' => __('Ejemplo: 521234567890', 'rentword'),
        'section' => 'rentword_host_info',
        'type' => 'text',
    ));
    
    // Bio del Host
    $wp_customize->add_setting('rentword_host_bio', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport' => 'refresh',
    ));
    
    $wp_customize->add_control('rentword_host_bio', array(
        'label' => __('Biografía del Anfitrión', 'rentword'),
        'description' => __('Breve descripción del host', 'rentword'),
        'section' => 'rentword_host_info',
        'type' => 'textarea',
    ));
    
    // Imagen del Host
    $wp_customize->add_setting('rentword_host_image', array(
        'default' => '',
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'rentword_host_image', array(
        'label' => __('Foto del Anfitrión', 'rentword'),
        'description' => __('Imagen de perfil del host (recomendado: 200x200px)', 'rentword'),
        'section' => 'rentword_host_info',
        'mime_type' => 'image',
    )));
    
    // ========================================
    // SECCIÓN: FOOTER
    // ========================================
    
    $wp_customize->add_section('rentword_footer', array(
        'title' => __('Footer / Pie de Página', 'rentword'),
        'priority' => 55,
    ));
    
    // Texto Copyright
    $wp_customize->add_setting('rentword_copyright', array(
        'default' => sprintf(__('© %s %s. Todos los derechos reservados.', 'rentword'), date('Y'), get_bloginfo('name')),
        'sanitize_callback' => 'wp_kses_post',
        'transport' => 'refresh',
    ));
    
    $wp_customize->add_control('rentword_copyright', array(
        'label' => __('Texto de Copyright', 'rentword'),
        'section' => 'rentword_footer',
        'type' => 'textarea',
    ));
    
    // Mostrar créditos
    $wp_customize->add_setting('rentword_show_credits', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    
    $wp_customize->add_control('rentword_show_credits', array(
        'label' => __('Mostrar Créditos del Tema', 'rentword'),
        'section' => 'rentword_footer',
        'type' => 'checkbox',
    ));
}
add_action('customize_register', 'rentword_customize_register');

/**
 * Output custom CSS from Customizer
 */
function rentword_customizer_css() {
    $primary = get_theme_mod('rentword_primary_color', '#5EBFB3');
    $secondary = get_theme_mod('rentword_secondary_color', '#E89B6D');
    $overlay = get_theme_mod('rentword_overlay_color', '#2D6A6A');
    $text = get_theme_mod('rentword_text_color', '#222222');
    $background = get_theme_mod('rentword_background_color', '#FFFFFF');
    $gradients = get_theme_mod('rentword_enable_gradients', true);
    $glass = get_theme_mod('rentword_glassmorphism', true);
    $radius = get_theme_mod('rentword_border_radius', '24');
    
    ?>
    <style type="text/css">
        :root {
            --rw-primary: <?php echo esc_attr($primary); ?>;
            --rw-primary-dark: <?php echo esc_attr(rentword_darken_color($primary, 15)); ?>;
            --rw-secondary: <?php echo esc_attr($secondary); ?>;
            --rw-secondary-light: <?php echo esc_attr(rentword_lighten_color($secondary, 20)); ?>;
            --rw-overlay: <?php echo esc_attr($overlay); ?>;
            --rw-dark: <?php echo esc_attr($text); ?>;
            --rw-white: <?php echo esc_attr($background); ?>;
            --rw-radius: <?php echo esc_attr($radius); ?>px;
            --rw-radius-sm: <?php echo esc_attr($radius / 2); ?>px;
            --rw-radius-lg: <?php echo esc_attr($radius * 1.5); ?>px;
        }
        
        /* Modern Gradient Background */
        <?php if ($gradients): ?>
        .hero-section,
        .modern-hero {
            background: linear-gradient(135deg, 
                <?php echo esc_attr($primary); ?> 0%, 
                <?php echo esc_attr($overlay); ?> 100%);
        }
        
        .gradient-overlay {
            background: linear-gradient(180deg, 
                rgba(0,0,0,0.3) 0%, 
                rgba(0,0,0,0.6) 100%);
        }
        <?php endif; ?>
        
        /* Glassmorphism Effect */
        <?php if ($glass): ?>
        .glass-card,
        .property-card-modern,
        .search-box-glass {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .glass-sidebar {
            background: rgba(<?php echo esc_attr(rentword_hex_to_rgb($secondary)); ?>, 0.9);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
        }
        <?php endif; ?>
        
        /* Border Radius */
        .card,
        .property-card,
        .btn,
        .form-control,
        .modal-content {
            border-radius: var(--rw-radius) !important;
        }
        
        .badge,
        .tag {
            border-radius: var(--rw-radius-sm) !important;
        }
        
        img.rounded,
        .property-image {
            border-radius: var(--rw-radius) !important;
            overflow: hidden;
        }
        
        /* Color Scheme */
        body {
            color: <?php echo esc_attr($text); ?>;
            background-color: <?php echo esc_attr($background); ?>;
        }
        
        .btn-primary {
            background: <?php echo esc_attr($secondary); ?>;
            border-color: <?php echo esc_attr($secondary); ?>;
            box-shadow: 0 4px 15px rgba(<?php echo esc_attr(rentword_hex_to_rgb($secondary)); ?>, 0.3);
        }
        
        .btn-primary:hover {
            background: <?php echo esc_attr(rentword_darken_color($secondary, 10)); ?>;
            border-color: <?php echo esc_attr(rentword_darken_color($secondary, 10)); ?>;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(<?php echo esc_attr(rentword_hex_to_rgb($secondary)); ?>, 0.4);
        }
        
        .btn-outline-primary {
            color: <?php echo esc_attr($primary); ?>;
            border-color: <?php echo esc_attr($primary); ?>;
        }
        
        .btn-outline-primary:hover {
            background: <?php echo esc_attr($primary); ?>;
            border-color: <?php echo esc_attr($primary); ?>;
        }
        
        a {
            color: <?php echo esc_attr($primary); ?>;
            transition: all 0.3s ease;
        }
        
        a:hover {
            color: <?php echo esc_attr(rentword_darken_color($primary, 15)); ?>;
        }
        
        .property-price {
            color: <?php echo esc_attr($secondary); ?>;
            font-weight: 700;
        }
        
        .badge-primary {
            background: <?php echo esc_attr($secondary); ?>;
        }
        
        /* Header Modern */
        .site-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        
        /* Modern Card Hover */
        .property-card-modern:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }
        
        /* Smooth Transitions */
        .btn,
        .card,
        .property-card,
        a {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
    <?php
}
add_action('wp_head', 'rentword_customizer_css');

/**
 * Darken color helper
 */
function rentword_darken_color($hex, $percent) {
    $hex = str_replace('#', '', $hex);
    
    if (strlen($hex) == 3) {
        $hex = str_repeat(substr($hex, 0, 1), 2) . str_repeat(substr($hex, 1, 1), 2) . str_repeat(substr($hex, 2, 1), 2);
    }
    
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    $r = max(0, min(255, $r - ($r * $percent / 100)));
    $g = max(0, min(255, $g - ($g * $percent / 100)));
    $b = max(0, min(255, $b - ($b * $percent / 100)));
    
    return '#' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT) . str_pad(dechex($g), 2, '0', STR_PAD_LEFT) . str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
}

/**
 * Lighten color helper
 */
function rentword_lighten_color($hex, $percent) {
    $hex = str_replace('#', '', $hex);
    
    if (strlen($hex) == 3) {
        $hex = str_repeat(substr($hex, 0, 1), 2) . str_repeat(substr($hex, 1, 1), 2) . str_repeat(substr($hex, 2, 1), 2);
    }
    
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    $r = max(0, min(255, $r + ((255 - $r) * $percent / 100)));
    $g = max(0, min(255, $g + ((255 - $g) * $percent / 100)));
    $b = max(0, min(255, $b + ((255 - $b) * $percent / 100)));
    
    return '#' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT) . str_pad(dechex($g), 2, '0', STR_PAD_LEFT) . str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
}

/**
 * Hex to RGB helper
 */
function rentword_hex_to_rgb($hex) {
    $hex = str_replace('#', '', $hex);
    
    if (strlen($hex) == 3) {
        $hex = str_repeat(substr($hex, 0, 1), 2) . str_repeat(substr($hex, 1, 1), 2) . str_repeat(substr($hex, 2, 1), 2);
    }
    
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    return "$r, $g, $b";
}
