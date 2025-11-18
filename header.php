<?php
/**
 * The header template file - Clean Modern Design
 *
 * @package RentWord
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php 
    // Custom favicon from customizer
    $favicon_id = get_theme_mod('rentword_favicon');
    if ($favicon_id) {
        $favicon = wp_get_attachment_image_src($favicon_id, 'full');
        if ($favicon) {
            echo '<link rel="icon" type="image/x-icon" href="' . esc_url($favicon[0]) . '">';
        }
    }
    ?>
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
    <!-- RentWord Header -->
    <header id="masthead" class="rw-header" data-header>
        <!-- Navbar -->
        <div class="rw-navbar">
            <div class="container">
                <div class="row align-items-center">
                    <!-- Logo -->
                    <div class="col-4 col-md-3">
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="rw-logo-text">
                            <?php
                            $custom_logo_id = get_theme_mod('custom_logo');
                            if ($custom_logo_id) {
                                $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
                                echo '<img src="' . esc_url($logo[0]) . '" alt="' . get_bloginfo('name') . '" class="rw-logo">';
                            } else {
                                echo esc_html(get_theme_mod('rentword_site_title', get_bloginfo('name')));
                            }
                            ?>
                        </a>
                    </div>

                    <!-- Center Links (Desktop Only) -->
                    <div class="col-md-6 d-none d-md-flex justify-content-center">
                    </div>

                    <!-- Right Menu -->
                    <div class="col-8 col-md-3 d-flex justify-content-end align-items-center gap-2">
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="rw-search-container">
            <div class="container">
                <form method="GET" action="<?php echo esc_url(home_url('/properties')); ?>" class="rw-search-bar">
                    <!-- Destino -->
                    <div class="rw-search-section">
                        <label class="rw-search-label"><?php esc_html_e('Destino', 'rentword'); ?></label>
                        <input type="text" name="location" class="rw-search-input" placeholder="<?php esc_attr_e('Buscar destinos', 'rentword'); ?>" value="<?php echo esc_attr(isset($_GET['location']) ? $_GET['location'] : ''); ?>">
                    </div>

                    <!-- Fechas -->
                    <div class="rw-search-section">
                        <label class="rw-search-label"><?php esc_html_e('Fechas', 'rentword'); ?></label>
                        <input type="text" id="rw-daterange" class="rw-search-input rw-daterange-input" placeholder="<?php esc_attr_e('Agregar fechas', 'rentword'); ?>" readonly>
                        <input type="hidden" id="rw-checkin" name="checkin" value="<?php echo esc_attr(isset($_GET['checkin']) ? $_GET['checkin'] : ''); ?>">
                        <input type="hidden" id="rw-checkout" name="checkout" value="<?php echo esc_attr(isset($_GET['checkout']) ? $_GET['checkout'] : ''); ?>">
                    </div>

                    <!-- Huéspedes -->
                    <div class="rw-search-section">
                        <label class="rw-search-label"><?php esc_html_e('Huéspedes', 'rentword'); ?></label>
                        <div class="rw-guest-counter">
                            <button type="button" class="rw-counter-btn" onclick="rwChangeGuests(-1)" aria-label="<?php esc_attr_e('Disminuir huéspedes', 'rentword'); ?>">−</button>
                            <input type="number" id="rw-guests" name="guests" class="rw-counter-display" value="<?php echo esc_attr(isset($_GET['guests']) && $_GET['guests'] > 0 ? $_GET['guests'] : '1'); ?>" readonly>
                            <button type="button" class="rw-counter-btn" onclick="rwChangeGuests(1)" aria-label="<?php esc_attr_e('Aumentar huéspedes', 'rentword'); ?>">+</button>
                        </div>
                    </div>

                    <!-- Search Button -->
                    <button type="submit" class="rw-search-btn" aria-label="<?php esc_attr_e('Buscar', 'rentword'); ?>">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <div id="content" class="site-content">
