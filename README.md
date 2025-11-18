# RentWord Theme

WordPress theme para rentas vacacionales.

## Características

### Auto-Detección Inteligente
- 100+ variaciones de campos (ES/EN)
- property_images → image_url
- price_per_night → price
- city → location
- Zero configuración manual
- Cache inteligente (refresh cada hora)

### UI/UX
- Glassmorphism en componentes
- Gradientes modernos
- Hover effects suaves
- 100% Responsive
- Animaciones fluidas
- CSS Variables dinámicas

### Búsqueda Avanzada
- Ubicación
- Precio (min/max)
- Habitaciones
- Baños
- Tipo de propiedad
- Amenidades
- AJAX en tiempo real

### Mapas y Galerías
- Leaflet.js interactivo
- Swiper.js sliders
- Lazy loading
- Touch-friendly  

### Mapas y Galerías
- Leaflet.js interactivo
- Swiper.js sliders
- Lazy loading
- Touch-friendly

## Instalación

1. Subir theme a `/wp-content/themes/`
2. Activar desde Apariencia → Temas
3. Configurar API en Apariencia → RentWord Settings
4. Ajustar colores en Apariencia → Personalizar

## Estructuras de API Soportadas

### Supabase
```json
{
  "id": "uuid",
  "title": "Departamento playa",
  "price_per_night": 799,
  "city": "Mazatlán",
  "property_images": [
    {"image_url": "https://...jpg"}
  ]
}
```

### Simple
```json
{
  "id": 1,
  "name": "Casa",
  "price": 1500,
  "location": "Monterrey",
  "images": ["url.jpg"]
}
```

El theme detecta estas estructuras automáticamente.

## Personalización

### CSS Variables
```css
:root {
  --rw-primary: #5EBFB3;
  --rw-secondary: #E89B6D;
  --rw-radius: 24px;
}
```

### Funciones PHP
```php
$title = rentword_get_property_field($property, 'title');
echo rentword_format_price($price);
rentword_property_card_modern($property);
```

## Stack Tecnológico

- WordPress 6.0+
- PHP 7.4+
- Bootstrap 5.3.2
- jQuery 3.7.1
- Leaflet 1.9.4
- Swiper 11.0.0

## Licencia

GPL-2.0
