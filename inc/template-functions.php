<?php
/**
 * Template Functions
 * 
 * Utility functions for templates
 *
 * @package RentWord
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get property field value with smart auto-detection
 * 
 * @param array $property
 * @param string $field
 * @return mixed
 */
function rentword_get_property_field($property, $field) {
    if (!is_array($property)) {
        return '';
    }
    
    // Try exact match first
    if (isset($property[$field])) {
        return $property[$field];
    }
    
    // Try mapped field from admin
    $mapping = rentword_get_field_mapping();
    $api_field = isset($mapping[$field]) ? $mapping[$field] : '';
    if ($api_field && isset($property[$api_field])) {
        return $property[$api_field];
    }
    
    // Smart auto-detection for common field variations
    $variations = rentword_get_field_variations($field);
    foreach ($variations as $variation) {
        if (isset($property[$variation])) {
            return $property[$variation];
        }
    }
    
    // Case-insensitive search
    $lower_field = strtolower($field);
    foreach ($property as $key => $value) {
        if (strtolower($key) === $lower_field) {
            return $value;
        }
    }
    
    return '';
}

/**
 * Get possible field name variations for auto-detection
 * 
 * @param string $field
 * @return array
 */
function rentword_get_field_variations($field) {
    $variations = array();
    
    switch ($field) {
        case 'id':
            $variations = array('id', 'ID', 'property_id', 'propertyId', 'listing_id', 'listingId', '_id', 'code', 'codigo');
            break;
        case 'title':
            $variations = array('title', 'name', 'property_name', 'propertyName', 'listing_name', 'listingName', 'titulo', 'nombre', 'heading');
            break;
        case 'price':
            $variations = array('price', 'precio', 'cost', 'costo', 'nightly_rate', 'nightlyRate', 'rate', 'amount', 'value', 'precio_noche', 'price_per_night');
            break;
        case 'images':
            $variations = array('images', 'imagenes', 'photos', 'fotos', 'gallery', 'galeria', 'picture', 'imagen', 'photo', 'image', 'pictures', 'media', 'property_images', 'propertyImages');
            break;
        case 'location':
            $variations = array('location', 'ubicacion', 'address', 'direccion', 'city', 'ciudad', 'place', 'lugar', 'zone', 'zona');
            break;
        case 'description':
            $variations = array('description', 'descripcion', 'details', 'detalles', 'summary', 'resumen', 'about', 'content', 'contenido');
            break;
        case 'bedrooms':
            $variations = array('bedrooms', 'recamaras', 'beds', 'camas', 'bedroom_count', 'habitaciones', 'dormitorios', 'num_bedrooms');
            break;
        case 'bathrooms':
            $variations = array('bathrooms', 'banos', 'baths', 'bathroom_count', 'banios', 'num_bathrooms');
            break;
        case 'amenities':
            $variations = array('amenities', 'amenidades', 'features', 'caracteristicas', 'facilities', 'servicios', 'comodidades');
            break;
        case 'property_type':
            $variations = array('property_type', 'propertyType', 'type', 'tipo', 'category', 'categoria', 'kind');
            break;
        case 'area':
            $variations = array('area', 'size', 'tamano', 'square_feet', 'sqft', 'm2', 'meters', 'superficie');
            break;
        case 'rating':
            $variations = array('rating', 'calificacion', 'stars', 'estrellas', 'score', 'puntuacion', 'review_score');
            break;
        case 'featured':
            $variations = array('featured', 'destacado', 'is_featured', 'isFeatured', 'highlighted', 'destacada');
            break;
        case 'coordinates':
            $variations = array('coordinates', 'coordenadas', 'coords', 'geo', 'location_data', 'lat_lng', 'latlng');
            break;
        default:
            $variations = array($field);
    }
    
    return $variations;
}

/**
 * Get currency symbol from customizer
 * 
 * @return string
 */
function rentword_get_currency_symbol() {
    return get_theme_mod('rentword_currency_symbol', '$');
}

/**
 * Format price with smart number extraction
 * 
 * @param mixed $price
 * @return string
 */
function rentword_format_price($price) {
    // Extract numeric value from different formats
    if (is_array($price)) {
        // Handle {amount: 1500, currency: "MXN"}
        if (isset($price['amount'])) {
            $price = $price['amount'];
        } elseif (isset($price['value'])) {
            $price = $price['value'];
        } elseif (isset($price['price'])) {
            $price = $price['price'];
        } else {
            $price = reset($price);
        }
    }
    
    if (is_object($price)) {
        $price = isset($price->amount) ? $price->amount : (isset($price->value) ? $price->value : 0);
    }
    
    // Convert to float and remove any non-numeric characters except decimal point
    if (is_string($price)) {
        $price = preg_replace('/[^0-9.]/', '', $price);
    }
    
    $price = floatval($price);
    
    if ($price <= 0) {
        return '';
    }
    
    $symbol = get_theme_mod('rentword_currency_symbol', '$');
    $position = get_theme_mod('rentword_currency_position', 'before');
    
    $formatted_price = number_format($price, 0, '.', ',');
    
    if ($position === 'before') {
        return $symbol . $formatted_price;
    }
    
    return $formatted_price . ' ' . $symbol;
}

/**
 * Get property URL
 * 
 * @param string $property_id
 * @return string
 */
function rentword_get_property_url($property_id) {
    $page = get_page_by_path('property');
    
    if ($page) {
        return add_query_arg('id', $property_id, get_permalink($page->ID));
    }
    
    return home_url('/property/?id=' . $property_id);
}

/**
 * Get property thumbnail with smart image extraction
 * 
 * @param array $property
 * @param string $size
 * @return string
 */
function rentword_get_property_thumbnail($property, $size = 'rentword-property-medium') {
    $images = rentword_get_property_field($property, 'images');
    
    // Handle different image formats from API
    if (is_array($images) && !empty($images)) {
        $first_image = reset($images);
        
        // Format 1: Array of objects with 'url' property [{url: "..."}, ...]
        if (is_array($first_image) && isset($first_image['url'])) {
            return esc_url($first_image['url']);
        }
        
        // Format 1b: Array of objects with 'image_url' property [{image_url: "..."}, ...]
        if (is_array($first_image) && isset($first_image['image_url'])) {
            return esc_url($first_image['image_url']);
        }
        
        // Format 2: Object with url property (converted from stdClass)
        if (is_object($first_image) && isset($first_image->url)) {
            return esc_url($first_image->url);
        }
        
        // Format 2b: Object with image_url property
        if (is_object($first_image) && isset($first_image->image_url)) {
            return esc_url($first_image->image_url);
        }
        
        // Format 3: Array of objects with other url keys
        if (is_array($first_image)) {
            foreach (array('url', 'image_url', 'imageUrl', 'src', 'link', 'path', 'file', 'href', 'image') as $key) {
                if (isset($first_image[$key]) && is_string($first_image[$key])) {
                    if (filter_var($first_image[$key], FILTER_VALIDATE_URL)) {
                        return esc_url($first_image[$key]);
                    }
                }
            }
        }
        
        // Format 4: Direct URL strings ["url1", "url2", ...]
        if (is_string($first_image)) {
            if (filter_var($first_image, FILTER_VALIDATE_URL)) {
                return esc_url($first_image);
            }
            // Relative path - convert to absolute
            if (strpos($first_image, 'http') === false && strpos($first_image, '/') === 0) {
                $api_url = get_option('rentword_api_url', '');
                if ($api_url) {
                    $base_url = parse_url($api_url, PHP_URL_SCHEME) . '://' . parse_url($api_url, PHP_URL_HOST);
                    return esc_url($base_url . $first_image);
                }
            }
        }
    } 
    // Format 5: Single URL string (not array)
    elseif (is_string($images) && !empty($images)) {
        if (filter_var($images, FILTER_VALIDATE_URL)) {
            return esc_url($images);
        }
    }
    
    // Try alternative image field names directly from property
    foreach (array('image', 'imagen', 'photo', 'foto', 'picture', 'thumbnail', 'cover', 'main_image') as $alt_field) {
        if (isset($property[$alt_field])) {
            $alt_value = $property[$alt_field];
            
            if (is_string($alt_value) && filter_var($alt_value, FILTER_VALIDATE_URL)) {
                return esc_url($alt_value);
            }
            
            if (is_array($alt_value) && !empty($alt_value)) {
                $first = $alt_value[0];
                // Check for url key
                if (is_array($first) && isset($first['url'])) {
                    return esc_url($first['url']);
                }
                // Check for image_url key
                if (is_array($first) && isset($first['image_url'])) {
                    return esc_url($first['image_url']);
                }
                // Direct string URL
                if (is_string($first) && filter_var($first, FILTER_VALIDATE_URL)) {
                    return esc_url($first);
                }
            }
        }
    }
    
    // Fallback to placeholder
    if (defined('RENTWORD_ASSETS_URI')) {
        return RENTWORD_ASSETS_URI . '/images/placeholder.jpg';
    }
    
    return 'https://via.placeholder.com/400x300/FF5A5F/FFFFFF?text=Sin+Imagen';
}

/**
 * Spotlight card for horizontal collections
 */
function rentword_property_spotlight_card($property) {
    $id = rentword_get_property_field($property, 'id');
    $title = rentword_get_property_field($property, 'title');
    $price = rentword_get_property_field($property, 'price');
    $location = rentword_get_property_field($property, 'location');
    $featured = rentword_get_property_field($property, 'featured');
    $rating = rentword_get_property_field($property, 'rating');
    $image = rentword_get_property_thumbnail($property);
    $url = rentword_get_property_url($id);
    $rating_display = $rating ? number_format((float) $rating, 2) : '4.90';
    ?>
    <a href="<?php echo esc_url($url); ?>" class="text-decoration-none d-block" data-spotlight-card style="cursor: pointer;">
        <div class="position-relative" style="border-radius: 12px; overflow: hidden; margin-bottom: 12px;">
            <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>" class="w-100" loading="lazy" style="aspect-ratio: 1; object-fit: cover; transition: transform 0.3s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
            <?php if ($featured): ?>
            <span class="badge position-absolute" style="top: 12px; left: 12px; background: white; color: #222; font-weight: 600; font-size: 0.75rem; padding: 4px 10px; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.18);">
                Favorito entre huéspedes
            </span>
            <?php endif; ?>
            <button class="btn position-absolute" type="button" aria-label="<?php esc_attr_e('Guardar', 'rentword'); ?>" data-wishlist-toggle style="top: 12px; right: 12px; background: transparent; border: none; padding: 6px;">
                <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" style="width: 24px; height: 24px; fill: rgba(0,0,0,0.5); stroke: white; stroke-width: 2;">
                    <path d="M16 28s-10-7-10-14c0-5 3-7 6-7 2 0 4 1 4 4 0-3 2-4 4-4 3 0 6 2 6 7 0 7-10 14-10 14z"/>
                </svg>
            </button>
        </div>
        <div>
            <div class="d-flex justify-content-between align-items-start mb-1">
                <h6 class="mb-0 fw-bold" style="font-size: 0.9375rem; color: #222;"><?php echo esc_html($title); ?></h6>
                <div class="d-flex align-items-center ms-2">
                    <i class="bi bi-star-fill" style="font-size: 0.75rem; color: #222; margin-right: 2px;"></i>
                    <span style="font-size: 0.875rem; color: #222; font-weight: 500;"><?php echo esc_html($rating_display); ?></span>
                </div>
            </div>
            <?php if ($location): ?>
            <p class="text-muted mb-1" style="font-size: 0.875rem;"><?php echo esc_html($location); ?></p>
            <?php endif; ?>
            <p class="mb-0">
                <span class="fw-bold" style="color: #222; font-size: 0.9375rem;"><?php echo esc_html(rentword_format_price($price)); ?> MXN</span>
                <span class="text-muted" style="font-size: 0.875rem;"> noche</span>
            </p>
        </div>
    </a>
    <?php
}

/**
 * Render spotlight slider
 */
function rentword_render_spotlight_slider($properties, $limit = 8) {
    if (empty($properties)) {
        return;
    }
    $items = array_slice(array_values($properties), 0, $limit);
    ?>
    <div class="position-relative" data-rw-slider>
        <button class="btn btn-light position-absolute top-50 start-0 translate-middle-y shadow-sm d-none d-lg-flex align-items-center justify-content-center" type="button" aria-label="<?php esc_attr_e('Propiedades anteriores', 'rentword'); ?>" data-slider-prev style="left: -20px; width: 32px; height: 32px; border-radius: 50%; border: 1px solid #DDDDDD; z-index: 10; background: white;">
            <i class="bi bi-chevron-left" style="font-size: 0.875rem;"></i>
        </button>
        <div class="d-flex overflow-auto gap-3 pb-2" data-slider-track style="scroll-behavior: smooth; scrollbar-width: none; -ms-overflow-style: none;">
            <?php foreach ($items as $property) : ?>
                <div style="min-width: 300px; flex-shrink: 0;">
                    <?php rentword_property_spotlight_card($property); ?>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="btn btn-light position-absolute top-50 end-0 translate-middle-y shadow-sm d-none d-lg-flex align-items-center justify-content-center" type="button" aria-label="<?php esc_attr_e('Más propiedades', 'rentword'); ?>" data-slider-next style="right: -20px; width: 32px; height: 32px; border-radius: 50%; border: 1px solid #DDDDDD; z-index: 10; background: white;">
            <i class="bi bi-chevron-right" style="font-size: 0.875rem;"></i>
        </button>
    </div>
    <style>
        [data-slider-track]::-webkit-scrollbar { display: none; }
    </style>
    <?php
}

/**
 * Pagination
 * 
 * @param int $total
 * @param int $per_page
 * @param int $current_page
 */
function rentword_pagination($total, $per_page, $current_page = 1) {
    $total_pages = ceil($total / $per_page);
    
    if ($total_pages <= 1) {
        return;
    }
    
    ?>
    <nav aria-label="<?php esc_attr_e('Navegación', 'rentword'); ?>" class="rw-pagination d-flex justify-content-center my-5">
        <ul class="pagination">
            <?php if ($current_page > 1): ?>
            <li class="page-item">
                <a class="page-link" href="<?php echo esc_url(add_query_arg('paged', $current_page - 1)); ?>">
                    <?php esc_html_e('Anterior', 'rentword'); ?>
                </a>
            </li>
            <?php endif; ?>
            
            <?php 
            $start = max(1, $current_page - 2);
            $end = min($total_pages, $current_page + 2);
            
            if ($start > 1): ?>
                <li class="page-item"><a class="page-link" href="<?php echo esc_url(add_query_arg('paged', 1)); ?>">1</a></li>
                <?php if ($start > 2): ?>
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                <?php endif; ?>
            <?php endif; ?>
            
            <?php for ($i = $start; $i <= $end; $i++): ?>
                <li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                    <?php if ($i == $current_page): ?>
                        <span class="page-link"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a class="page-link" href="<?php echo esc_url(add_query_arg('paged', $i)); ?>"><?php echo $i; ?></a>
                    <?php endif; ?>
                </li>
            <?php endfor; ?>
            
            <?php if ($end < $total_pages): ?>
                <?php if ($end < $total_pages - 1): ?>
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                <?php endif; ?>
                <li class="page-item"><a class="page-link" href="<?php echo esc_url(add_query_arg('paged', $total_pages)); ?>"><?php echo $total_pages; ?></a></li>
            <?php endif; ?>
            
            <?php if ($current_page < $total_pages): ?>
            <li class="page-item">
                <a class="page-link" href="<?php echo esc_url(add_query_arg('paged', $current_page + 1)); ?>">
                    <?php esc_html_e('Siguiente', 'rentword'); ?>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>
    <?php
}

/**
 * Property search form
 */
function rentword_search_form() {
    $api = rentword_api();
    $property_types = $api->get_property_types();
    $amenities = $api->get_all_amenities();
    
    ?>
    <form class="card shadow-sm p-4 mb-4 rw-search-form" id="rw-search-form" method="get">
        <input type="hidden" name="sort_by" value="featured">
        <div class="row g-3 mb-3">
            <div class="col-md-12">
                <label for="rw-search-location" class="form-label"><?php esc_html_e('Ubicación', 'rentword'); ?></label>
                <input type="text" 
                       id="rw-search-location" 
                       name="location" 
                       class="form-control"
                       placeholder="<?php esc_attr_e('¿A dónde viajas?', 'rentword'); ?>"
                       value="<?php echo esc_attr(isset($_GET['location']) ? $_GET['location'] : ''); ?>">
            </div>
            
            <div class="col-md-3">
                <label for="rw-search-min-price" class="form-label"><?php esc_html_e('Precio mín.', 'rentword'); ?></label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" 
                           id="rw-search-min-price" 
                           name="min_price" 
                           class="form-control"
                           placeholder="0"
                           value="<?php echo esc_attr(isset($_GET['min_price']) ? $_GET['min_price'] : ''); ?>">
                </div>
            </div>
            
            <div class="col-md-3">
                <label for="rw-search-max-price" class="form-label"><?php esc_html_e('Precio máx.', 'rentword'); ?></label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" 
                           id="rw-search-max-price" 
                           name="max_price" 
                           class="form-control"
                           placeholder="10000"
                           value="<?php echo esc_attr(isset($_GET['max_price']) ? $_GET['max_price'] : ''); ?>">
                </div>
            </div>
            
            <div class="col-md-2">
                <label for="rw-search-bedrooms" class="form-label"><?php esc_html_e('Recámaras', 'rentword'); ?></label>
                <select id="rw-search-bedrooms" name="bedrooms" class="form-select">
                    <option value=""><?php esc_html_e('Cualquier', 'rentword'); ?></option>
                    <?php for ($i = 1; $i <= 10; $i++): ?>
                        <option value="<?php echo $i; ?>" <?php selected(isset($_GET['bedrooms']) ? $_GET['bedrooms'] : '', $i); ?>>
                            <?php echo $i; ?>+
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="rw-search-bathrooms" class="form-label"><?php esc_html_e('Baños', 'rentword'); ?></label>
                <select id="rw-search-bathrooms" name="bathrooms" class="form-select">
                    <option value=""><?php esc_html_e('Cualquier', 'rentword'); ?></option>
                    <?php for ($i = 1; $i <= 10; $i++): ?>
                        <option value="<?php echo $i; ?>" <?php selected(isset($_GET['bathrooms']) ? $_GET['bathrooms'] : '', $i); ?>>
                            <?php echo $i; ?>+
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="rw-search-property-type" class="form-label"><?php esc_html_e('Tipo', 'rentword'); ?></label>
                <select id="rw-search-property-type" name="property_type" class="form-select">
                    <option value=""><?php esc_html_e('Todos', 'rentword'); ?></option>
                    <?php foreach ($property_types as $type): ?>
                        <option value="<?php echo esc_attr($type); ?>" <?php selected(isset($_GET['property_type']) ? $_GET['property_type'] : '', $type); ?>>
                            <?php echo esc_html($type); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="mb-3">
            <button type="button" class="btn btn-link btn-sm p-0" id="rw-toggle-amenities">
                <i class="bi bi-funnel"></i> <?php esc_html_e('Más filtros', 'rentword'); ?>
            </button>
            
            <div class="rw-amenities-filter mt-3" id="rw-amenities-filter" style="display: none;">
                <h6><?php esc_html_e('Comodidades', 'rentword'); ?></h6>
                <div class="row g-2">
                    <?php foreach ($amenities as $amenity): ?>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="amenity-<?php echo esc_attr($amenity); ?>"
                                       name="amenities[]" 
                                       value="<?php echo esc_attr($amenity); ?>"
                                       <?php checked(in_array($amenity, isset($_GET['amenities']) ? (array)$_GET['amenities'] : array())); ?>>
                                <label class="form-check-label" for="amenity-<?php echo esc_attr($amenity); ?>">
                                    <?php echo esc_html($amenity); ?>
                                </label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-lg flex-grow-1">
                <i class="bi bi-search"></i> <?php esc_html_e('Buscar', 'rentword'); ?>
            </button>
            <a href="<?php echo esc_url(strtok($_SERVER['REQUEST_URI'], '?')); ?>" class="btn btn-outline-secondary btn-lg">
                <?php esc_html_e('Limpiar', 'rentword'); ?>
            </a>
        </div>
    </form>
    <?php
}

/**
 * Property gallery
 * 
 * @param array $property
 */
function rentword_property_gallery($property) {
    $images = rentword_get_property_field($property, 'images');
    
    // Process different image formats
    $processed_images = array();
    
    if (is_array($images) && !empty($images)) {
        foreach ($images as $image) {
            $url = '';
            
            // Format 1: Array with 'url' key
            if (is_array($image) && isset($image['url'])) {
                $url = $image['url'];
            }
            // Format 2: Array with 'image_url' key
            elseif (is_array($image) && isset($image['image_url'])) {
                $url = $image['image_url'];
            }
            // Format 3: Object with url property
            elseif (is_object($image) && isset($image->url)) {
                $url = $image->url;
            }
            // Format 4: Object with image_url property
            elseif (is_object($image) && isset($image->image_url)) {
                $url = $image->image_url;
            }
            // Format 5: Direct URL string
            elseif (is_string($image) && filter_var($image, FILTER_VALIDATE_URL)) {
                $url = $image;
            }
            
            if (!empty($url) && filter_var($url, FILTER_VALIDATE_URL)) {
                $processed_images[] = $url;
            }
        }
    }
    
    // If no images found, use thumbnail
    if (empty($processed_images)) {
        $thumbnail = rentword_get_property_thumbnail($property);
        if (!empty($thumbnail)) {
            $processed_images[] = $thumbnail;
        }
    }
    
    if (empty($processed_images)) {
        return;
    }
    
    ?>
    <div class="rw-property-gallery mb-4">
        <!-- Main Gallery Slider -->
        <div class="swiper rw-gallery-main">
            <div class="swiper-wrapper">
                <?php foreach ($processed_images as $index => $image_url): ?>
                    <div class="swiper-slide">
                        <div class="gallery-image" data-index="<?php echo $index; ?>">
                            <img src="<?php echo esc_url($image_url); ?>" 
                                 alt="<?php echo esc_attr(rentword_get_property_field($property, 'title')); ?>">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-pagination"></div>
        </div>
        
        <!-- Thumbnails -->
        <?php if (count($processed_images) > 1): ?>
        <div class="swiper rw-gallery-thumbs mt-3">
            <div class="swiper-wrapper">
                <?php foreach ($processed_images as $index => $image_url): ?>
                    <div class="swiper-slide">
                        <div class="gallery-thumb">
                            <img src="<?php echo esc_url($image_url); ?>" alt="">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Lightbox Modal -->
    <div class="rw-lightbox" id="gallery-lightbox" style="display: none;">
        <div class="lightbox-close">&times;</div>
        <div class="lightbox-prev">&#8249;</div>
        <div class="lightbox-next">&#8250;</div>
        <div class="lightbox-content">
            <img src="" alt="">
        </div>
        <div class="lightbox-counter"></div>
    </div>
    <?php
}

/**
 * Property map
 * 
 * @param array $property
 */
function rentword_property_map($property) {
    $coordinates = rentword_get_property_field($property, 'coordinates');
    
    if (empty($coordinates)) {
        return;
    }
    
    // Handle different coordinate formats
    $lat = '';
    $lng = '';
    
    if (is_array($coordinates)) {
        $lat = isset($coordinates['lat']) ? $coordinates['lat'] : (isset($coordinates['latitude']) ? $coordinates['latitude'] : '');
        $lng = isset($coordinates['lng']) ? $coordinates['lng'] : (isset($coordinates['longitude']) ? $coordinates['longitude'] : '');
    }
    
    if (empty($lat) || empty($lng)) {
        return;
    }
    
    ?>
    <div class="mb-4">
        <h5 class="mb-3"><?php esc_html_e('Ubicación', 'rentword'); ?></h5>
        <div id="rw-property-map" 
             class="rounded"
             style="height: 400px; border: 1px solid #ddd;"
             data-lat="<?php echo esc_attr($lat); ?>" 
             data-lng="<?php echo esc_attr($lng); ?>"
             data-title="<?php echo esc_attr(rentword_get_property_field($property, 'title')); ?>">
        </div>
    </div>
    <?php
}

/**
 * Property amenities list
 * 
 * @param array $property
 */
function rentword_property_amenities($property) {
    $amenities = rentword_get_property_field($property, 'amenities');
    
    if (empty($amenities) || !is_array($amenities)) {
        return;
    }
    
    ?>
    <div class="mb-4">
        <h5 class="mb-3"><?php esc_html_e('Comodidades', 'rentword'); ?></h5>
        <div class="row g-2">
            <?php foreach ($amenities as $amenity): ?>
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        <span><?php echo esc_html($amenity); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

/**
 * Modern Property Card (Nickelfox Design)
 * 
 * @param array $property Property data
 */
function rentword_property_card_modern($property) {
    $id = rentword_get_property_field($property, 'id');
    $title = rentword_get_property_field($property, 'title');
    $price = rentword_get_property_field($property, 'price');
    $location = rentword_get_property_field($property, 'location');
    $bedrooms = rentword_get_property_field($property, 'bedrooms');
    $bathrooms = rentword_get_property_field($property, 'bathrooms');
    $property_type = rentword_get_property_field($property, 'property_type');
    $image = rentword_get_property_thumbnail($property);
    $url = rentword_get_property_url($id);
    $featured = rentword_get_property_field($property, 'featured');
    
    ?>
    <div class="property-card-modern" data-property-id="<?php echo esc_attr($id); ?>">
        <div class="property-image-container">
            <a href="<?php echo esc_url($url); ?>">
                <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>" class="property-image" loading="lazy">
            </a>
            <?php if ($featured): ?>
            <span class="property-badge"><?php esc_html_e('Destacada', 'rentword'); ?></span>
            <?php elseif ($property_type): ?>
            <span class="property-badge"><?php echo esc_html($property_type); ?></span>
            <?php endif; ?>
        </div>
        
        <div class="property-body">
            <h3 class="property-title">
                <a href="<?php echo esc_url($url); ?>" class="text-decoration-none" style="color: inherit;">
                    <?php echo esc_html($title); ?>
                </a>
            </h3>
            
            <?php if ($location): ?>
            <div class="property-location">
                <i class="bi bi-geo-alt"></i>
                <span><?php echo esc_html($location); ?></span>
            </div>
            <?php endif; ?>
            
            <?php if ($bedrooms || $bathrooms): ?>
            <div class="property-features">
                <?php if ($bedrooms): ?>
                <div class="property-feature">
                    <i class="bi bi-door-closed"></i>
                    <span><?php echo esc_html($bedrooms); ?> <?php esc_html_e('Hab.', 'rentword'); ?></span>
                </div>
                <?php endif; ?>
                
                <?php if ($bathrooms): ?>
                <div class="property-feature">
                    <i class="bi bi-droplet"></i>
                    <span><?php echo esc_html($bathrooms); ?> <?php esc_html_e('Baños', 'rentword'); ?></span>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <div class="property-price-box">
                <?php if (get_theme_mod('rentword_show_price', true)): ?>
                <div>
                    <div class="property-price">
                        <?php echo rentword_format_price($price); ?>
                    </div>
                    <div class="property-price-label">
                        <?php esc_html_e('por noche', 'rentword'); ?>
                    </div>
                </div>
                <?php endif; ?>
                <a href="<?php echo esc_url($url); ?>" class="btn btn-modern btn-modern-outline">
                    <?php esc_html_e('Ver', 'rentword'); ?>
                </a>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Vacation Rental Property Card (Authentic Clone)
 * 
 * @param array $property Property data
 */
function rentword_property_card($property) {
    $id = rentword_get_property_field($property, 'id');
    $title = rentword_get_property_field($property, 'title');
    $price = rentword_get_property_field($property, 'price');
    $location = rentword_get_property_field($property, 'location');
    $bedrooms = rentword_get_property_field($property, 'bedrooms');
    $property_type = rentword_get_property_field($property, 'property_type');
    $image = rentword_get_property_thumbnail($property);
    $url = rentword_get_property_url($id);
    
    // Generate rating (4.5 - 5.0)
    $rating = number_format(4.5 + (mt_rand(0, 50) / 100), 2);
    $is_guest_favorite = $rating >= 4.85;
    
    // Property description
    $description = $property_type ?: 'Alojamiento completo';
    if ($bedrooms) {
        $description .= ' · ' . $bedrooms . ' habitaciones';
    }
    
    ?>
    <a href="<?php echo esc_url($url); ?>" class="vr-property-card animate-fade-in" data-property-id="<?php echo esc_attr($id); ?>">
        <div class="vr-card-image">
            <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy">
            
            <!-- Favorite Heart -->
            <button class="vr-favorite-btn" type="button" onclick="event.preventDefault(); toggleFavorite(<?php echo esc_attr($id); ?>);">
                <i class="bi bi-heart-fill"></i>
            </button>
            
            <?php if ($is_guest_favorite): ?>
            <!-- Guest Favorite Badge -->
            <div class="vr-guest-favorite">
                <i class="bi bi-award-fill"></i>
                <span><?php esc_html_e('Favorito entre huéspedes', 'rentword'); ?></span>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="vr-card-content">
            <div class="vr-card-header">
                <h3 class="vr-card-location"><?php echo esc_html($title); ?></h3>
                <div class="vr-card-rating">
                    <i class="bi bi-star-fill"></i>
                    <span class="vr-card-rating-value"><?php echo $rating; ?></span>
                </div>
            </div>
            
            <p class="vr-card-description"><?php echo esc_html($location ?: $description); ?></p>
            
            <p class="vr-card-dates"><?php esc_html_e('Disponible todo el año', 'rentword'); ?></p>
            
            <?php if (get_theme_mod('rentword_show_price', true)): ?>
            <p class="vr-card-price">
                <strong><?php echo rentword_format_price($price); ?></strong>
                <span><?php esc_html_e(' noche', 'rentword'); ?></span>
            </p>
            <?php endif; ?>
        </div>
    </a>
    <?php
}
