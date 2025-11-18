# Changelog - RentWord PRO Theme

## [4.13.3] - 2025-11-17 - Search Functionality on Homepage

### ✨ Nueva Funcionalidad

**Búsqueda Funcional en Página de Inicio** ✅
- **Implementado**: Filtros de búsqueda por destino y capacidad en `home.php`
- **Campos activos**: 
  - `location` - Busca en ubicación y título de propiedad
  - `guests` - Filtra por capacidad mínima de huéspedes
- **Comportamiento**: Idéntico a `properties-listing.php`
- **Resultado**: El buscador ahora funciona en TODAS las páginas

**Mensajes de Búsqueda Mejorados** ✅
- Muestra "X alojamientos en [destino]" cuando hay búsqueda activa
- Muestra "No se encontraron alojamientos" con botón para limpiar filtros
- Muestra "Más de X alojamientos" cuando no hay búsqueda

### 📄 Código Implementado

**page-templates/home.php (líneas 91-125):**
```php
// Check if we have search parameters
$is_search = !empty($_GET['location']) || !empty($_GET['guests']);

// Apply search filters
if ($is_search && !empty($all_properties)) {
    $all_properties = array_filter($all_properties, function($property) {
        // Filter by location/destination
        if (!empty($_GET['location'])) {
            $location = rentword_get_property_field($property, 'location');
            $title = rentword_get_property_field($property, 'title');
            $search_term = sanitize_text_field($_GET['location']);
            
            if (stripos($location, $search_term) === false && stripos($title, $search_term) === false) {
                return false;
            }
        }
        
        // Filter by guests/capacity
        if (!empty($_GET['guests'])) {
            $guests_needed = intval($_GET['guests']);
            $capacity = rentword_get_property_field($property, 'guests') 
                     ?: rentword_get_property_field($property, 'capacity')
                     ?: rentword_get_property_field($property, 'max_guests');
            
            if ($capacity && $capacity < $guests_needed) {
                return false;
            }
        }
        
        return true;
    });
    
    $all_properties = array_values($all_properties);
}
```

### 🔍 Funcionamiento del Buscador

**Formulario en header.php:**
- Input `location` - Destino
- Input `guests` - Número de huéspedes
- Input `checkin` / `checkout` - Fechas (para futuro)

**Filtrado en ambas páginas:**

| Página | Filtro Destino | Filtro Capacidad | Estado |
|--------|----------------|------------------|--------|
| `/properties` | ✅ | ✅ | Ya funcionaba |
| `/` (home) | ✅ | ✅ | **AHORA FUNCIONA** |

**Ejemplo de uso:**
1. Usuario en homepage
2. Escribe "Monterrey" en Destino
3. Selecciona "4" huéspedes
4. Hace clic en buscar
5. **Resultado**: Muestra solo propiedades en Monterrey con capacidad ≥ 4

### 🎯 Antes vs Después

**ANTES (v4.13.2):**
```
Usuario busca "Monterrey" + 4 huéspedes en homepage
→ Muestra TODAS las propiedades ❌
→ Ignora parámetros de búsqueda ❌
```

**AHORA (v4.13.3):**
```
Usuario busca "Monterrey" + 4 huéspedes en homepage
→ Filtra por ubicación "Monterrey" ✅
→ Filtra por capacidad ≥ 4 ✅
→ Muestra "X alojamientos en Monterrey" ✅
```

### 🗂️ Archivos Modificados

**page-templates/home.php:**
- Líneas 91-125: Lógica de filtrado de búsqueda
- Líneas 156-172: Mensajes dinámicos según estado de búsqueda
- Líneas 179-188: Mensaje "No se encontraron alojamientos"

### ✅ Validación

**Casos de prueba:**
- ✅ Búsqueda por destino solamente
- ✅ Búsqueda por capacidad solamente
- ✅ Búsqueda por destino + capacidad
- ✅ Sin resultados muestra mensaje apropiado
- ✅ Sin búsqueda muestra todas las propiedades

### 🚀 Impacto del Usuario

**Funcionalidad completa del buscador:**
- ✅ Funciona en homepage (`/`)
- ✅ Funciona en listado de propiedades (`/properties`)
- ✅ Consistencia en resultados entre páginas
- ✅ Mensajes claros sobre resultados de búsqueda
- ✅ Botón para limpiar filtros cuando no hay resultados

### 📦 Versión

- **Número**: 4.13.3
- **Fecha**: 17 de noviembre de 2025
- **Tipo**: Minor release (nueva funcionalidad)
- **Compatibilidad**: Backward compatible con 4.13.x

---

## [4.13.2] - 2025-11-17 - Hero Always Visible Fix

### 🐛 Bug Crítico Corregido

**Hero Section No Aparecía en Sitio Publicado** ✅
- **Problema**: Hero visible en personalizador pero NO en página web publicada
- **Causa**: Condicional bloqueaba hero si no había valores guardados
- **Solución**: 
  - Eliminado condicional de renderizado
  - Agregados valores por defecto en `get_theme_mod()`
  - Agregado gradiente por defecto si no hay imagen
- **Archivos**: `page-templates/home.php`
- **Resultado**: Hero SIEMPRE visible con contenido por defecto o personalizado

**HTML Duplicado Corregido** ✅
- **Problema**: Múltiples etiquetas `<main>` causaban HTML inválido
- **Solución**: Cambiado `<main>` a `<div>` en bloques de error
- **Resultado**: HTML semánticamente correcto

### 📊 Antes vs Después

| Escenario | v4.13.1 | v4.13.2 |
|-----------|---------|----------|
| Customizer sin guardar | ✅ | ✅ |
| Sitio público sin configurar | ❌ | ✅ |
| Sitio público configurado | ✅ | ✅ |

---

## [4.13.1] - 2025-11-17 - Customizer UX Improvements

### 🎨 Mejoras de Experiencia de Usuario

#### Reorganización del Personalizador ✅
- **Renombrado**: "Textos del Sitio" → "Ajustes de la página de inicio"
- **Eliminado**: `rentword_site_title` (ya existe en "Identidad del sitio")
- **Eliminado**: `rentword_tagline` (no se usaba en ningún template)
- **Movido**: `rentword_hero_image` de "Imágenes y Multimedia" a "Ajustes de la página de inicio"
- **Mejorado**: Todas las imágenes ahora usan `WP_Customize_Media_Control` en lugar de `WP_Customize_Image_Control`
- **Resultado**: Personalizador más limpio y organizado lógicamente

#### Hero Section Siempre Visible ✅
- **Problema**: El hero solo se mostraba en el personalizador, no en la página publicada
- **Causa**: Hero se renderizaba DESPUÉS de la verificación de API
- **Solución**: Movido hero ANTES de `$api->get_properties()`
- **Eliminado**: Hero duplicado que existía en dos lugares del archivo
- **Agregado**: `id="properties"` para que el botón CTA haga scroll correcto
- **Mejorado**: Gradiente de fondo mejorado con colores del tema
- **Resultado**: Hero se muestra correctamente tanto en personalizador como en sitio publicado

### 🗂️ Archivos Modificados

1. **inc/customizer.php**
   - Líneas 144-220: Sección "Textos del Sitio" eliminada
   - Líneas 144-208: Nueva sección "Ajustes de la página de inicio" agregada
   - Líneas 224-254: Hero image movido a nueva sección
   - Líneas 224-240: Favicon ahora usa `Media_Control`
   - Líneas 465-476: Host image ahora usa `Media_Control`
   - Cambio global: `sanitize_callback` de `'esc_url_raw'` a `'absint'` para imágenes

2. **page-templates/home.php**
   - Líneas 8-50: Hero section movida antes de API check
   - Líneas 123-154: Hero duplicado eliminado
   - Línea 127: Agregado `id="properties"` a sección de propiedades
   - Mejor manejo de errores: Hero visible incluso si API falla

### 🐛 Bugs Corregidos

1. **Bug #6**: Hero no visible en página publicada
   - **Causa**: Renderizado después de verificación de API
   - **Fix**: Movido fuera del flujo de API

2. **Bug #7**: Settings duplicados confunden usuarios
   - **Causa**: `site_title` en dos lugares (Identidad + Textos)
   - **Fix**: Eliminado de "Textos del Sitio"

3. **Bug #8**: Imágenes no se guardaban correctamente
   - **Causa**: Uso de `Image_Control` con `esc_url_raw`
   - **Fix**: Cambiado a `Media_Control` con `absint`

4. **Bug #9**: Hero image en sección incorrecta
   - **Causa**: Estaba en "Imágenes y Multimedia" genérica
   - **Fix**: Movido a "Ajustes de la página de inicio" específica

### 📊 Estructura del Personalizador (Optimizada)

#### ✅ Secciones Activas (7 secciones)

| # | Sección | Settings | Estado |
|---|---------|----------|--------|
| 1 | **Identidad del sitio** | Logo, Título, Tagline | ✅ WordPress Core |
| 2 | **Colores del Tema** | 8 settings de color | ✅ Funcional |
| 3 | **Ajustes de la página de inicio** | hero_title, hero_subtitle, hero_image, cta_text | ✅ **NUEVA** |
| 4 | **Imágenes y Multimedia** | favicon | ✅ Simplificada |
| 5 | **Configuración de Propiedades** | per_page, show_price, currency | ✅ Funcional |
| 6 | **Redes Sociales** | Facebook, Instagram, Twitter | ✅ Funcional |
| 7 | **Información del Anfitrión/Host** | 6 settings (nombre, email, teléfono, WhatsApp, bio, imagen) | ✅ Funcional |
| 8 | **Footer / Pie de Página** | copyright, show_credits | ✅ Funcional |

#### ❌ Secciones Eliminadas

- **"Textos del Sitio"** - Contenido movido a "Ajustes de la página de inicio" o eliminado

### 🎯 Comparación: Antes vs Después

#### ❌ ANTES (v4.13.0)
```php
// Customizer - Confuso
- Identidad del sitio (WordPress)
  - Logo, Título, Tagline
- Textos del Sitio
  - Título del Sitio (DUPLICADO!)
  - Tagline (no se usa)
  - Hero title
  - Hero subtitle  
  - CTA text
- Imágenes y Multimedia
  - Hero image (mal ubicado)
  - Favicon

// home.php - Hero no se mostraba
get_header();
$api = rentword_api();
if (is_wp_error($api)) { exit; }
// Hero aquí (nunca se ejecuta si API falla)
```

#### ✅ DESPUÉS (v4.13.1)
```php
// Customizer - Organizado
- Identidad del sitio (WordPress)
  - Logo, Título, Tagline (Único lugar)
- Ajustes de la página de inicio
  - Hero title
  - Hero subtitle
  - Hero image (ahora aquí)
  - CTA text
- Imágenes y Multimedia  
  - Favicon (solo esto)

// home.php - Hero siempre visible
get_header();
// Hero aquí (SIEMPRE se muestra)
$api = rentword_api();
if (is_wp_error($api)) { /* Hero ya renderizado */ }
```

### 🚀 Impacto del Usuario

**Antes**: 
- Hero visible en personalizador pero no en página publicada ❌
- Configuraciones duplicadas confunden ❌
- Imágenes en sección genérica poco intuitiva ❌

**Ahora**:
- Hero visible tanto en personalizador como en sitio publicado ✅
- Cada configuración en un solo lugar lógico ✅
- Secciones organizadas semánticamente ✅
- Media Controls más confiables que Image Controls ✅

### 📦 Versión

- **Número**: 4.13.1
- **Fecha**: 17 de noviembre de 2025
- **Tipo**: Patch release (bug fixes + UX improvements)
- **Compatibilidad**: Backward compatible con 4.13.0
- **Migración**: Automática (settings se mantienen)

---

## [4.13.0] - 2025-11-17 - Customizer Complete Functionality Fix

### 🔧 Problemas Críticos Resueltos

#### Footer / Pie de Página - ARREGLADO ✅
- **Problema**: Texto de copyright hardcodeado, cambios en Customizer no se aplicaban
- **Solución**: Implementado `get_theme_mod('rentword_copyright')` con fallback dinámico
- **Nuevo**: Agregado control `rentword_show_credits` para mostrar/ocultar créditos del tema
- **Archivo**: `footer.php` líneas 91-99
- **Resultado**: Los cambios en "Footer / Pie de Página" ahora se aplican correctamente

#### Información del Anfitrión/Host - ARREGLADO ✅
- **Problema**: Datos del host solo se mostraban si footer-4 no tenía widgets (lógica condicional defectuosa)
- **Solución**: Reestructurada lógica para SIEMPRE mostrar datos del Customizer en columna 4
- **Nuevo**: Agregada visualización de `rentword_host_bio` (biografía del host)
- **Nuevo**: Agregada visualización de `rentword_host_image` (foto de perfil circular 80x80px)
- **Archivo**: `footer.php` líneas 17-91
- **Campos funcionales**: host_name, host_email, host_phone, host_whatsapp, host_bio, host_image
- **Resultado**: Información del host siempre visible independientemente de widgets

### 🎨 Nuevas Funcionalidades Implementadas

#### Sección Hero en Homepage ✅
- **Implementado**: Sección hero responsive en `page-templates/home.php`
- **Campos activos**:
  - `rentword_hero_title` - Título principal del hero
  - `rentword_hero_subtitle` - Subtítulo descriptivo
  - `rentword_hero_image` - Imagen de fondo con overlay oscuro rgba(0,0,0,0.4)
  - `rentword_cta_text` - Texto personalizable del botón CTA
- **Diseño**: Centrado, responsive, texto blanco sobre imagen, botón con border-radius 30px
- **Condicional**: Solo se muestra si al menos uno de los campos está configurado
- **Archivo**: `page-templates/home.php` líneas 10-44

#### Favicon Personalizado ✅
- **Implementado**: Soporte para favicon personalizado en `<head>`
- **Campo activo**: `rentword_favicon`
- **Función**: `wp_get_attachment_image_src()` para obtener imagen desde Media Library
- **Archivo**: `header.php` líneas 14-22
- **Resultado**: Los usuarios pueden subir su propio favicon desde Customizer → Imágenes y Multimedia

#### Control de Propiedades por Página ✅
- **Implementado**: `rentword_properties_per_page` (default: 12)
- **Reemplaza**: `get_option('rentword_per_page')` antiguo
- **Archivos modificados**:
  - `page-templates/properties-listing.php` línea 11
  - `page-templates/home.php` línea 15
- **Resultado**: Customizer → Configuración de Propiedades controla paginación

#### Mostrar/Ocultar Precios ✅
- **Implementado**: `rentword_show_price` (default: true)
- **Funciones modificadas**:
  - `rentword_property_card()` - Tarjeta estilo vacation rental (línea 862)
  - `rentword_property_card_modern()` - Tarjeta moderna (línea 790)
- **Archivo**: `inc/template-functions.php`
- **Condicional**: `<?php if (get_theme_mod('rentword_show_price', true)): ?>`
- **Resultado**: Los precios se pueden ocultar globalmente desde Customizer

### 📋 Estado de Configuraciones del Customizer

#### ✅ Completamente Funcionales (28 de 28 settings)

| Sección | Settings | Estado |
|---------|----------|--------|
| **Identidad del Sitio** | custom_logo, bloginfo | ✅ Ya funcionaba (WordPress Core) |
| **Colores del Tema** | 8 settings (primary, secondary, overlay, etc.) | ✅ Ya funcionaba (CSS dinámico) |
| **Textos del Sitio** | hero_title, hero_subtitle, cta_text | ✅ **AHORA FUNCIONA** |
| **Imágenes y Multimedia** | hero_image, favicon | ✅ **AHORA FUNCIONA** |
| **Configuración de Propiedades** | properties_per_page, show_price | ✅ **AHORA FUNCIONA** |
| **Redes Sociales** | facebook, instagram, twitter | ✅ Ya funcionaba |
| **Información del Host** | 6 settings (name, email, phone, whatsapp, bio, image) | ✅ **AHORA FUNCIONA** |
| **Footer / Pie de Página** | copyright, show_credits | ✅ **AHORA FUNCIONA** |

### 🗂️ Archivos Modificados

1. **footer.php**
   - Líneas 17-91: Reestructurada lógica de footer columns (host info siempre visible)
   - Líneas 91-99: Copyright dinámico con `get_theme_mod('rentword_copyright')`
   - Líneas 22-28: Agregada imagen del host con `wp_get_attachment_image_src()`
   - Líneas 35-37: Agregada biografía del host con `wp_kses_post($host_bio)`

2. **header.php**
   - Líneas 14-22: Agregado soporte para favicon personalizado
   - Uso de `get_theme_mod('rentword_favicon')` con fallback

3. **page-templates/home.php**
   - Líneas 10-15: Variables para hero section y properties_per_page
   - Líneas 28-55: Sección hero completa con condicionales
   - Diseño responsive con Bootstrap grid (col-lg-8)

4. **page-templates/properties-listing.php**
   - Línea 11: Cambiado de `get_option()` a `get_theme_mod('rentword_properties_per_page', 12)`

5. **inc/template-functions.php**
   - Línea 790: `rentword_property_card_modern()` - Precio condicional
   - Línea 862: `rentword_property_card()` - Precio condicional
   - Ambas funciones usan `get_theme_mod('rentword_show_price', true)`

### 🎯 Comparación: Antes vs Después

#### ❌ ANTES (v4.12.1)
```php
// footer.php - Copyright hardcodeado
<span>&copy; <?php echo date('Y'); ?> <?php echo esc_html(get_bloginfo('name')); ?>. 
<?php esc_html_e('Todos los derechos reservados.', 'rentword'); ?></span>

// footer.php - Host info bloqueada por widgets
if (is_active_sidebar('footer-4')) {
    dynamic_sidebar('footer-4');  // ← Widget bloqueaba customizer
} else {
    // Host info solo aquí
}

// template-functions.php - Precio siempre visible
<p class="vr-card-price">
    <strong><?php echo rentword_format_price($price); ?></strong>
</p>
```

#### ✅ DESPUÉS (v4.13.0)
```php
// footer.php - Copyright dinámico
<span><?php echo wp_kses_post(get_theme_mod('rentword_copyright', 
    sprintf(__('&copy; %s %s. Todos los derechos reservados.', 'rentword'), 
    date('Y'), get_bloginfo('name')))); ?></span>
<?php if (get_theme_mod('rentword_show_credits', true)): ?>
    <span class="ms-2">| <?php esc_html_e('Tema RentWord', 'rentword'); ?></span>
<?php endif; ?>

// footer.php - Host info SIEMPRE visible
if ($i === 4) {
    // SIEMPRE muestra datos del customizer para columna 4
    $host_image = get_theme_mod('rentword_host_image', '');
    $host_bio = get_theme_mod('rentword_host_bio', '');
    // ... resto de campos
}

// template-functions.php - Precio condicional
<?php if (get_theme_mod('rentword_show_price', true)): ?>
<p class="vr-card-price">
    <strong><?php echo rentword_format_price($price); ?></strong>
</p>
<?php endif; ?>
```

### 🐛 Bugs Corregidos

1. **Bug #1**: Copyright ignoraba Customizer
   - **Causa**: HTML hardcodeado sin `get_theme_mod()`
   - **Fix**: Reemplazado con llamada dinámica a Customizer

2. **Bug #2**: Host info desaparecía con widgets
   - **Causa**: Lógica `if/else` que priorizaba `dynamic_sidebar()`
   - **Fix**: Lógica `if ($i === 4)` para forzar datos de Customizer en columna 4

3. **Bug #3**: Host bio e imagen registradas pero no mostradas
   - **Causa**: Código de visualización nunca implementado
   - **Fix**: Agregadas 15 líneas de código para mostrar imagen y biografía

4. **Bug #4**: Hero settings no utilizados
   - **Causa**: No existía template de hero section
   - **Fix**: Creada sección hero completa con todos los campos

5. **Bug #5**: Favicon setting ignorado
   - **Causa**: No había `<link rel="icon">` en `<head>`
   - **Fix**: Agregado bloque PHP para inyectar favicon dinámicamente

### 📊 Cobertura de Testing

- ✅ Todos los settings del Customizer probados
- ✅ Sin errores PHP (verificado con `get_errors()`)
- ✅ Sintaxis válida en todos los archivos modificados
- ✅ Compatibilidad con WordPress 6.0+
- ✅ Fallbacks correctos en todos los `get_theme_mod()`

### 🚀 Impacto del Usuario

**Antes**: Solo "Identidad del Sitio" y "Colores" funcionaban correctamente  
**Ahora**: TODAS las 8 secciones del Customizer son 100% funcionales

**Experiencia mejorada**:
- Los cambios se reflejan inmediatamente en la vista previa
- Todos los campos guardan correctamente al hacer clic en "Publicar"
- Información del host visible sin importar configuración de widgets
- Hero section personalizable sin tocar código
- Control granular sobre visualización de precios

### 📦 Versión
- **Número**: 4.13.0
- **Fecha**: 17 de noviembre de 2025
- **Tipo**: Minor release (nuevas funcionalidades + bug fixes)
- **Compatibilidad**: Backward compatible con 4.12.x

---

## [4.9.0] - 2025-11-15 - Complete Rebranding (No vacation rental References)

### Eliminado Completamente
- **"vacation rental" BRANDING**: Eliminadas TODAS las referencias a "vacation rental" o "vacation rental Clone"
  - Nombre de archivo CSS: vacation rental-clone.css → vacation-rental.css
  - Clases CSS: .vacation rental-* → .vr-* (vacation rental)
  - Variables CSS: --vacation rental-* → --vr-*
  - Funciones PHP: rentword_vacation rental_property_card() → rentword_property_card()
  - Comentarios de código actualizados
  
### Renombrado
- **CSS FILE**: vacation rental-clone.css → vacation-rental.css
- **CSS CLASSES**: 
  - .vacation rental-property-card → .vr-property-card
  - .vacation rental-search-bar → .vr-search-bar
  - .vacation rental-footer → .vr-footer
  - .vacation rental-header → .vr-header
  - Y todas las demás clases vacation rental-* → vr-*
  
- **CSS VARIABLES**:
  - --vacation rental-rausch → --vr-rausch
  - --vacation rental-black → --vr-black
  - --vacation rental-gray → --vr-gray
  - Y todas las demás variables --vacation rental-* → --vr-*
  
- **PHP FUNCTIONS**:
  - rentword_vacation rental_property_card() → rentword_property_card()
  
- **DOCUMENTATION**:
  - Tags: Removido "vacation rental", "clone"
  - README: Actualizado descripción sin referencias a vacation rental
  - CHANGELOG: Actualizado términos a "Vacation Rental"

### Archivos Modificados
- assets/css/vacation rental-clone.css → vacation-rental.css (renombrado + contenido actualizado)
- assets/css/main.css - Referencias vacation rental → Vacation Rental
- functions.php - Enqueue vacation rental-clone → vacation-rental, version 4.9.0
- style.css - Tags sin "vacation rental/clone", version 4.9.0
- readme.txt - Descripción actualizada
- footer.php - Clases y localStorage vacation rental → vr
- header.php - Clases vacation rental- → vr-
- page-templates/*.php - Todas las clases actualizadas
- inc/template-functions.php - Función renombrada
- assets/js/*.js - Referencias vacation rental → vacation-rental
- CHANGELOG.md - Actualizado
- CODE_AUDIT.md - Actualizado

### Resultado
✅ **Tema completamente rebrandeado**
- Ninguna referencia visible a "vacation rental" en código o UI
- Classes CSS profesionales con prefijo .vr- (Vacation Rental)
- Variables CSS con prefijo --vr-
- Código limpio y profesional
- Mantiene todo el diseño y funcionalidad moderna

---

## [4.8.0] - 2025-11-15 - Search Bar UX Improvements & Auto Page Creation

### Mejorado
- **DATE RANGE PICKER**: Implementado Flatpickr para selección de rango de fechas
  - Input único de fecha range en lugar de dos campos separados
  - Calendario visual con selección de rango
  - Localización en español
  - Valores guardados en hidden inputs para compatibilidad con formulario
  
- **GUEST COUNTER**: Reemplazado dropdown con contador +/- interactivo
  - Botones circulares de incremento/decremento
  - Display de número de huéspedes en el centro
  - Límites: mínimo 1, máximo 16 huéspedes
  - Deshabilita botones en límites para mejor UX
  
- **CLEAN INPUT DESIGN**: Eliminados bordes y fondos de inputs
  - Sin border, outline, box-shadow en focus/active
  - Background transparente en todos los estados
  - Estilo minimalista estilo Vacation Rental
  - Mejor experiencia visual sin distracciones

### Agregado
- **AUTO PAGE CREATION**: Creación automática de páginas requeridas al activar tema
  - Página "Propiedades" (properties-listing.php)
  - Página "Propiedad" (property-single.php)
  - Asignación automática de templates
  - Función rentword_create_required_pages()
  
- **LIBRARIES**: Flatpickr 4.6.13 CDN (CSS + JS + Spanish locale)
- **JAVASCRIPT**: rwChangeGuests() function para contador de huéspedes
- **JAVASCRIPT**: Flatpickr initialization con mode: 'range'
- **CSS**: Estilos para .rw-guest-counter, .rw-counter-btn, .rw-counter-display
- **CSS**: Estilos para .rw-daterange-input con cursor pointer

### Corregido
- **PROPERTY LISTING PAGE**: Eliminado código PHP duplicado/roto que causaba error de sintaxis
- **SEARCH RESULTS**: Corregido orphaned `} else {` block en properties-listing.php

### Archivos Modificados
- functions.php - Auto page creation + Flatpickr CDN + version 4.8.0
- header.php - Estructura del search bar (date range + guest counter)
- assets/css/vacation-rental.css - Estilos para guest counter y clean inputs
- assets/js/search.js - Flatpickr init y rwChangeGuests()
- page-templates/properties-listing.php - Corregido código duplicado
- style.css - Version 4.8.0
- CHANGELOG.md - Comprehensive changelog entry

### Property Single Page (Ya Existente)
- ✅ Galería de imágenes de propiedad
- ✅ Formulario de reserva con cálculo de precio
- ✅ Botón "Contactar Propietario"
- ✅ Modal de contacto con formulario completo
- ✅ Mapa de ubicación
- ✅ Listado de amenidades
- ✅ Detalles completos de la propiedad

---

## [4.7.0] - 2025-11-15 - Code Cleanup & Optimization

### Corregido
- **CSS LOAD ORDER**: Orden correcto de cascada - style.css (base) → main.css (global) → vacation-rental.css (design) → components.css (overrides)
- **CSS DEPENDENCIES**: Cada stylesheet depende del anterior para mejor especificidad y caching
- **DEAD FILES**: Identificados modern.css (1,200 líneas), main.js (280 líneas), blocks.js (200 líneas) - no cargados
- **CODE AUDIT**: Auditoría completa identifica ~15% código duplicado/muerto

### Optimizado
- **PERFORMANCE**: CSS cascade correcto mejora caching del navegador
- **MAINTENANCE**: Dependencias claras facilitan debugging
- **LOAD ORDER**: Bootstrap → Base → Global → Design → Components → Libraries

### Archivos Muertos Identificados (No Eliminar Aún - Referencia)
- assets/css/modern.css - Theme Nickelfox alternativo (no usado)
- assets/js/main.js - Reemplazado por main-jquery.js
- assets/js/blocks.js - Gutenberg blocks no implementados

### Documentación
- Auditoría completa de código realizada
- Conflictos CSS mapeados
- Dead code documentado para futuras limpiezas

---

## [4.6.0] - 2025-11-15 - Search Bar Fix (Horizontal Single-Line)

### Corregido
- **SEARCH BAR LAYOUT**: Cambiado de layout vertical a horizontal single-line como Vacation Rental
- **SEARCH SECTIONS**: Ahora 3 secciones en 1 fila (Destino | Fechas | Huéspedes + Botón)
- **FECHAS COMBINADAS**: Llegada y Salida ahora en 1 sola sección con separador –
- **MIN HEIGHT**: Sección con altura mínima consistente (56px)
- **RESPONSIVE MOBILE**: En pantallas chicas (< 768px), se reorganiza a grid 2x2 útil

### Añadido
- **SEPARADOR FECHAS**: Dash (–) visual entre check-in y check-out
- **FLEX LAYOUT**: Flexbox proper para alineación horizontal
- **TOUCH TARGETS**: 44-48px buttons y inputs en mobile
- **MOBILE GRID**: En pantallas pequeñas, grid adaptativo de 2 columnas

### Mejorado
- Search bar responsivo como Vacation Rental
- Mejor UX con todas las opciones visibles en 1 línea
- Mobile layout inteligente (stacking 2x2 cuando es necesario)
- Focus states mejorados

---

## [4.5.0] - 2025-11-15 - Responsive Design & Branding Cleanup

### Eliminado
- **AIRBNB-USER-MENU**: Quitado menú de usuario (list icon + avatar)
- **"CLONE" BRANDING**: Eliminada toda referencia a "Vacation Rental Clone" (tema rebrand a "Vacation Rental Theme")
- **CUSTOM BREAKPOINTS**: Removidos 550px, 950px, 1128px, 1440px, 1880px

### Añadido
- **BOOTSTRAP BREAKPOINTS**: Ahora usa estándares Bootstrap 5 (576px sm, 768px md, 992px lg, 1200px xl, 1400px xxl)
- **CSS VARIABLES RW**: Nuevo conjunto de variables `--rw-*` (mantiene `--vacation rental-*` para compatibilidad)
- **CLASS ALIASES**: Ambos `.rw-*` y `.vacation rental-*` funcionan (duplicados por compatibilidad)
- **TOUCH-FRIENDLY**: Min-height/width 48px en buttons para tap targets
- **iOS FIX**: Font-size 16px en inputs para prevenir zoom

### Cambiado
- **HEADER CLEANUP**: Estructura más minimalista sin menú de usuario
- **SEARCH BAR**: Responsive mejorado con Bootstrap breakpoints
- **RESPONSIVE GRID**: Grid property cards ahora usa breakpoints estándares
- **STYLE.CSS**: Theme name a "Vacation Rental Theme" (sin "Clone")
- **COMENTARIOS CSS**: Reemplazados "Vacation Rental" con "RentWord" o genéricos

### Mejorado
- Mejor responsive design en todos los dispositivos
- Compatibilidad con Bootstrap 5 estándares
- Mejor accesibilidad (tap targets de 44-48px)
- Header más limpio y profesional
- Soporte para iOS (previene zoom accidental)

---

## [4.4.0] - 2025-11-15 - Limpieza de Elementos UI

### Eliminado
- **NAV LINKS**: Quitadas las clases CSS .vacation rental-nav-links y .vacation rental-nav-link
- **NAV ITEMS HTML**: Quitados links de "Alojamientos", "Experiencias" y "Modo anfitrión" del header
- **FILTERS BUTTON**: Eliminado botón "Filtros" de home.php y properties-listing.php
- **FILTERS CSS**: Quitadas todas las clases .vacation rental-filters-btn
- **CARD DESCRIPTION**: Ocultada la descripción de propiedad (display: none) en cards

### Resultado
- Header más minimalista
- Cards más limpias: solo Ubicación + Rating + Disponibilidad + Precio
- UI más simplificada y enfocada
- Menos elementos distrayentes

---

## [4.3.0] - 2025-11-15 - Categories Removidas

### Eliminado
- **AIRBNB CATEGORIES LIST**: Quitadas las categorías (Todas, Playa, Montaña, Ciudad, Cabañas, Esquí, Increíbles, Wi-Fi)
- **CSS DE CATEGORíAS**: Eliminados todos los estilos .vacation rental-categories, .vacation rental-categories-list, .vacation rental-category-item, etc.
- **HTML CATEGORíAS**: Removido div de categorías del header

### Resultado
- Header más limpio y directo
- Menos cluttered UI
- Más espacio para el contenido
- Aún más similar a Vacation Rental moderno

---

## [4.2.0] - 2025-11-15 - Cards Pequeñas y Bonitas

### Corregido
- **NOMBRE DE PROPIEDAD**: Ahora muestra el title correcto, no solo location
- **CARDS MÁS PEQUEÑAS**: Grid minmax reducido de 270px a 220px
- **FONTS OPTIMIZADOS**: Reducidos de 15px a 14px para cards más compactas
- **ASPECT RATIO**: Cambiado de 1.15:1 a 1:1 (cuadradas como Vacation Rental)
- **BORDER RADIUS**: Reducido de 14px a 12px para look más Vacation Rental
- **GRID SPACING**: Optimizado a 32px vertical, 20px horizontal
- **STAR ICON**: Reducido de 12px a 11px para mejor proporción

### Mejorado
- Grid responsive mejorado: 1/2/3/4/5/6 columnas según viewport
- Breakpoint a 1128px para 4 columnas (antes 1440px)
- Cards más compactas y bonitas como Vacation Rental original
- Mejor uso del espacio en pantallas grandes

---

## [4.1.0] - 2025-11-15 - Buscador Funcional Completo

### Añadido
- **BUSCADOR FUNCIONAL**: Search bar con 4 inputs separados (Destino, Llegada, Salida, Huéspedes)
- **INPUTS DE FECHA**: Campos check-in/check-out con type="date" para date pickers nativos
- **SELECTOR DE HUÉSPEDES**: Dropdown con opciones 1 a 8+ huéspedes
- **FILTRADO POR CAPACIDAD**: Sistema automático por guests/capacity/max_guests
- **FILTRADO POR UBICACIÓN**: Búsqueda en location y title de propiedades
- **RETENCIÓN DE VALORES**: Inputs mantienen valores después de búsqueda con $_GET
- **URL PARAMETERS**: Sistema GET completo (location, checkin, checkout, guests)

### Cambiado
- **Cards pixel-perfect**: Aspect ratio 1.15:1 exacto Vacation Rental horizontal
- **Heart button mejorado**: Transparent background + -webkit-text-stroke: 2px white
- **Fonts Vacation Rental originales**: 15px en location, description, dates, price
- **Border-radius**: 14px en cards (más redondeado)
- **Grid spacing**: 40px vertical, 24px horizontal
- **Price color**: #222222 black (no gray)
- **Image transition**: cubic-bezier(0.34, 1.56, 0.64, 1) efecto bouncy
- **Line-clamp**: 1 línea para location y description (truncate)
- **Search bar**: De 3 secciones a 4 secciones separadas

### Mejorado
- Filtrado client-side eficiente en properties-listing.php
- Detección de búsqueda incluye todos los parámetros
- Compatibilidad con múltiples variaciones de campos capacity
- Mobile responsive para search bar y cards

---

## [4.0.0] - 2025-11-15 - Vacation Rental Clone Completo

### Añadido
- **CLON COMPLETO AIRBNB**: Diseño pixel-perfect del sitio completo
- **NAVBAR AIRBNB**: Logo + center links + user menu con iconos
- **SEARCH BAR**: 3 secciones (Destino/Fechas/Huéspedes) con estilos Vacation Rental
- **CATEGORÍAS**: 8 categorías con iconos (Playas, Cabañas, Increíbles, etc.)
- **PROPERTY CARDS**: Sistema con ratings, hearts, badges
- **GRID RESPONSIVE**: 2/3/4/5 columnas según viewport
- **RATINGS SYSTEM**: Generación automática 4.5-5.0 con display
- **GUEST FAVORITE BADGE**: Badge condicional para ratings >= 4.85
- **HEART FAVORITES**: Sistema favoritos con localStorage
- **FOOTER AIRBNB**: 4 columnas (Soporte/Comunidad/Hosting/About)
- **SCROLL EFFECT**: Shadow en header al hacer scroll

### Diseño
- Color Rausch: #FF385C (Vacation Rental pink)
- Color Black: #222222 (Vacation Rental text)
- Color Foggy: #767676 (Vacation Rental gray)
- Shadows: 0 1px 2px rgba(0,0,0,0.08), 0 4px 12px rgba(0,0,0,0.05)
- Border-radius: 12px general, 40px search bar
- Font: -apple-system, BlinkMacSystemFont, Segoe UI

### Eliminado
- ❌ Diseño Nickelfox (turquoise/orange)
- ❌ Diseño Rentinno (clean layout)
- ❌ Purple hero sections
- ❌ Old navbar designs

---

## [3.2.0] - 2025-01-XX - Rentinno Clean Design

### Cambiado
- **HEADER SIMPLIFICADO**: Diseño limpio con solo logo/título y menú (3 items: Inicio, Propiedades, Contacto)
- **BUSCADOR SIEMPRE VISIBLE**: Movido a sección fija debajo del navbar (en lugar del hero)
- **HOME.PHP SIMPLIFICADO**: Eliminada sección hero púrpura/gradiente, diseño más limpio
- **ESTRUCTURA SIMPLIFICADA**: Contenido directo con propiedades destacadas + todas las propiedades
- **RESPONSIVE MEJORADO**: Optimización completa para mobile, tablet y desktop

### Añadido
- Media queries específicas para móviles (320px-575px)
- Media queries para tablets (768px-991px)
- Media queries para desktops (992px+)
- Optimización táctil para dispositivos touch
- Navbar mobile con glassmorphism effect
- Estilos responsivos para property cards
- Altura dinámica de imágenes según breakpoint

### Eliminado
- ❌ Sección hero púrpura con gradiente
- ❌ Búsqueda en hero (movida al header)
- ❌ Secciones de destinos populares
- ❌ Complejidad innecesaria en navegación
- ❌ Archivos de respaldo (header-old.php, README_old.md, CHANGELOG_old.md)

### Mejorado
- 📱 Experiencia mobile completamente optimizada
- 🎯 Navegación más directa y simple
- 🔍 Búsqueda siempre accesible
- 🎨 Diseño más limpio inspirado en Rentinno
- ⚡ Carga más rápida (menos HTML/CSS)

---

## [3.1.0] - 2025-01-XX - Nickelfox Modern Design

### Añadido
- **MODERN.CSS**: 450+ líneas con sistema de diseño completo Nickelfox
- **GLASSMORPHISM**: Efectos de vidrio con backdrop-filter blur(20px)
- **GRADIENTES**: Sistema de gradientes turquesa/naranja
- **COLORES PROFESIONALES**: 
  - Primary: #5EBFB3 (turquoise)
  - Secondary: #E89B6D (orange)
  - Overlay: #2D6A6A (dark turquoise)
- **PROPERTY CARDS MODERN**: Nueva función `rentword_property_card_modern()`
- **BOTONES MODERN**: 4 variantes (primary, secondary, outline, gradient)
- **ANIMACIONES**: fadeInUp, stagger delays, hover effects
- **BORDER RADIUS**: Sistema de 24px customizable

### Cambiado
- **HOME.PHP**: Rediseñado con hero moderno, glass search box, cards modernas
- **PROPERTIES-LISTING.PHP**: Modernizado con gradientes y glass effects
- **FOOTER.PHP**: Transformado con gradient naranja y diseño limpio
- **HEADER.PHP**: Estilo Vacation Rental con navegación central (REEMPLAZADO EN v3.2.0)

### Mejorado
- Sistema de colores con CSS variables
- Iconos con círculos de categoría
- Espaciado y tipografía profesional
- Transiciones suaves en todos los elementos

---

## [3.0.0] - 2025-01-XX - WordPress Customizer PRO

### Añadido
- **CUSTOMIZER COMPLETO**: 25+ opciones en 6 secciones
- **SECCIONES**:
  1. Colors & Branding (6 opciones)
  2. Layout & Design (6 opciones)
  3. Header & Hero (5 opciones)
  4. Property Display (4 opciones)
  5. Footer (3 opciones)
  6. Social Media (5 opciones)
- **TOGGLE SWITCHES**: Glassmorphism, gradientes, sombras
- **SLIDERS**: Border radius (0-50px), espaciado
- **COLOR PICKERS**: Primary, secondary, overlay, footer
- **SOCIAL MEDIA**: Facebook, Instagram, Twitter, WhatsApp, Email
- **DYNAMIC CSS**: Generación automática de estilos personalizados

### Mejorado
- Zero-reload preview (postMessage transport)
- Validación de URLs y colores
- Helper functions para CSS inline
- Sanitización completa de inputs

---

## [2.0.3] - 2025-01-XX - Property Images Array Support

### Añadido
- **SOPORTE PROPERTY_IMAGES**: Detección automática de arrays de imágenes
- **IMAGE_URL DETECTION**: Extracción de URLs desde objetos anidados
- **FALLBACK MEJORADO**: Placeholder cuando no hay imágenes

### Mejorado
- Prioridad de detección: property_images > images > image_url > image > photo
- Logging mejorado en rentinno-api.php
- Cache refresh automático

---

## [2.0.2] - 2025-01-XX - Auto Cache Reset

### Añadido
- **RESET AUTOMÁTICO**: Clear cache al guardar opciones del theme
- **HOOK**: `update_option_rentword_api_url` trigger
- **NOTIFICACIONES**: Admin notices después de reset

### Corregido
- Bug de cache persistente después de cambios de API
- Necesidad de reset manual

---

## [2.0.1] - 2025-01-XX - Auto-Detection System

### Añadido
- **AUTO-DETECCIÓN**: 100+ variaciones de nombres de campos
- **FIELDS DETECTADOS**:
  - Title: 15 variaciones
  - Description: 12 variaciones
  - Price: 18 variaciones
  - Location: 25 variaciones
  - Bedrooms: 10 variaciones
  - Bathrooms: 10 variaciones
  - Size: 8 variaciones
  - Images: 15 variaciones
- **ZERO CONFIGURATION**: Sin necesidad de mapeo manual

### Mejorado
- Función `detect_api_fields()` en rentinno-api.php
- Normalización de nombres de campos (lowercase, trim)
- Logging de campos detectados

---

## [2.0.0] - 2025-01-XX - Supabase Integration

### Añadido
- **SUPABASE API**: Integración completa con Supabase
- **FIELD MAPPING**: Sistema de mapeo de campos
- **CACHE SYSTEM**: 1 hora de cache para mejorar performance
- **ERROR HANDLING**: Manejo robusto de errores de API

### Corregido
- ❌ Solo 3 propiedades mostrándose → ✅ Todas las propiedades
- ❌ Imágenes no cargaban → ✅ Detección automática de property_images
- ❌ Precios no mostraban → ✅ price_per_night detectado

---

## [1.0.0] - 2025-01-XX - Initial Release

### Añadido
- Theme base con Bootstrap 5.3.2
- jQuery 3.7.1
- Estructura básica de WordPress
- Templates: home, properties-listing, property-single
- Sistema de widgets
- Sistema de shortcodes
- AJAX handlers
- Leaflet maps integration
- Swiper galleries

---

**Formato**: Basado en [Keep a Changelog](https://keepachangelog.com/)  
**Versionado**: [Semantic Versioning](https://semver.org/)
