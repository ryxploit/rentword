# 🏖️ RentWord Pro v3.1.0 - Theme Profesional de Rentas Vacacionales

El **MEJOR** theme de WordPress para rentas vacacionales con diseño MODERNO estilo Nickelfox.

![Version](https://img.shields.io/badge/version-3.1.0-blue.svg)
![WordPress](https://img.shields.io/badge/wordpress-6.0%2B-blue.svg)
![PHP](https://img.shields.io/badge/php-7.4%2B-purple.svg)
![License](https://img.shields.io/badge/license-GPL--2.0-green.svg)

---

## ✨ LO NUEVO EN v3.1.0 - DISEÑO NICKELFOX

### 🎨 Diseño Moderno Profesional
- **Esquema Turquesa/Naranja** (#5EBFB3 / #E89B6D)
- **Glassmorphism Effects** con backdrop-filter
- **Gradientes Suaves** en hero y fondos
- **Border Radius Personalizables** (0-50px)
- **Animaciones Smooth** cubic-bezier
- **Tarjetas Modernas** con hover 3D
- **Footer Gradiente** naranja

### 🛠️ Personalización Total (25+ Opciones)
✅ **Colores**: Primary, Secondary, Overlay, Texto, Fondo  
✅ **Diseño**: Gradientes, Glassmorphism, Border Radius  
✅ **Textos**: Hero, CTA, Copyright, Títulos  
✅ **Imágenes**: Logo, Favicon, Hero Background  
✅ **Propiedades**: Cantidad, Precios, Moneda  
✅ **Social Media**: Facebook, Instagram, Twitter, WhatsApp  

---

## 🚀 Características Principales

### 🔌 Auto-Detección Inteligente
✅ **100+ Variaciones** de campos (ES/EN)  
✅ **property_images** → `image_url`  
✅ **price_per_night** → `price`  
✅ **city** → `location`  
✅ **Zero configuración manual**  
✅ **Cache inteligente** (refresh cada hora)  

### 🎨 UI/UX Profesional
✨ Glassmorphism en componentes  
🌈 Gradientes modernos  
🎯 Hover effects suaves  
📱 100% Responsive  
⚡ Animaciones fluidas  
🎭 CSS Variables dinámicas  

### 🔍 Búsqueda Avanzada
- Ubicación
- Precio (min/max)
- Habitaciones
- Baños
- Tipo de propiedad
- Amenidades
- **AJAX en tiempo real**

### 🗺️ Mapas + Galerías
- **Leaflet.js** interactivo
- **Swiper.js** sliders
- Lazy loading
- Touch-friendly

---

## 📦 Instalación Rápida

1. **Subir Theme**
   - Apariencia → Temas → Añadir Nuevo → Subir
   - O vía FTP a `/wp-content/themes/`

2. **Configurar API**
   - Apariencia → RentWord Settings
   - Ingresa URL de API
   - Clic en "Probar Conexión"
   - ✅ Auto-detección se ejecuta automáticamente

3. **Personalizar Diseño**
   - Apariencia → Personalizar
   - Ajusta colores, textos, logo
   - Activa glassmorphism/gradientes
   - Publica cambios

4. **Crear Páginas**
   - Página "Home" → Template "Home Page"
   - Página "Properties" → Template "Properties Listing"
   - Ajustes → Lectura → Establece "Home" como inicio

---

## 🎯 Estructuras de API Soportadas

### Tu Caso (Supabase)
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

### Estructura Simple
```json
{
  "id": 1,
  "name": "Casa",
  "price": 1500,
  "location": "Monterrey",
  "images": ["url.jpg"]
}
```

### Estructura Anidada
```json
{
  "property": {
    "title": "Villa",
    "pricing": {"nightly": 2500},
    "photos": [{"url": "...jpg"}]
  }
}
```

**✨ El theme detecta TODAS estas estructuras automáticamente**

---

## 🎨 Personalización Avanzada

### CSS Variables
```css
:root {
  --rw-primary: #5EBFB3;
  --rw-secondary: #E89B6D;
  --rw-radius: 24px;
}
```

### Utility Classes
```html
<div class="glass-effect">Glassmorphism</div>
<div class="gradient-primary">Gradiente turquesa</div>
<h1 class="text-gradient">Texto con gradiente</h1>
<button class="btn btn-modern btn-modern-primary">Botón</button>
```

### Funciones PHP
```php
// Obtener campos
$title = rentword_get_property_field($property, 'title');

// Formatear precio
echo rentword_format_price($price); // $799/noche

// Tarjeta moderna
rentword_property_card_modern($property);
```

---

## 🔧 Solución de Problemas

| Problema | Solución |
|----------|----------|
| No aparecen propiedades | Verifica API, limpia cache, reactiva theme |
| Imágenes no se ven | Verifica URLs absolutas, revisa Field Mapping |
| Precios no aparecen | Revisa mapeo de campo `price` |
| Diseño no moderno | Activa glassmorphism/gradientes en Customizer |

---

## 📚 Stack Tecnológico

| Tech | Versión | Uso |
|------|---------|-----|
| WordPress | 6.0+ | CMS |
| PHP | 7.4+ | Backend |
| Bootstrap | 5.3.2 | CSS |
| jQuery | 3.7.1 | JS |
| Leaflet | 1.9.4 | Mapas |
| Swiper | 11.0.0 | Sliders |

---

## 📁 Estructura

```
rentword/
├── assets/css/modern.css      ⭐ Diseño Nickelfox
├── inc/customizer.php         ⭐ 25+ opciones
├── inc/template-functions.php ⭐ Auto-detección
├── inc/api/rentinno-api.php   ⭐ API handler
├── page-templates/home.php    ⭐ Home moderno
├── functions.php              ⭐ Core
├── header.php                 ⭐ Logo customizer
└── footer.php                 ⭐ Footer gradiente
```

---

## 🎉 Changelog

### v3.1.0 (Actual) - NICKELFOX DESIGN
- ✨ Diseño completo Nickelfox (turquesa/naranja)
- ✨ Glassmorphism + Gradientes
- ✨ modern.css (450+ líneas)
- ✨ property_card_modern()
- 🎨 Home/Listing rediseñados
- 🎨 Footer gradiente naranja
- 🛠️ Customizer: gradientes/glass toggles

### v3.0.0 - CUSTOMIZER PRO
- ✨ WordPress Customizer (25+ opciones)
- ✨ 6 secciones personalización
- ✨ Logo/Favicon support

### v2.0.3 - AUTO-RESET
- 🐛 Auto-detección primera vez
- 🐛 Cache hourly refresh

### v2.0.2 - SUPABASE
- ✨ property_images support
- ✨ image_url extraction

### v2.0.1 - AUTO-DETECCIÓN
- ✨ 100+ field variations
- ✨ 4-level search

---

## 🤝 Soporte

📧 support@rentword.com  
📖 https://rentword.com/docs  
💬 https://rentword.com/chat  

---

## 📄 Licencia

GPL-2.0 - Libre para uso personal y comercial

---

## 💖 Créditos

Desarrollado con ❤️ por **Equipo RentWord Pro**  
Diseño inspirado por **Nickelfox**

---

**⭐ RentWord Pro v3.1.0** - El MEJOR theme de rentas con diseño PROFESIONAL 🏆
