=== RentWord Pro ===
Contributors: RentWord Team
Tags: rental, property, real-estate, vacation-rental, booking, responsive, modern, premium
Requires at least: 6.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Premium WordPress theme for property rentals - Direct competitor to WP Rentals. Features advanced search, API integration, modern vacation rental UI.

== Description ==

RentWord Pro is a premium WordPress theme designed specifically for property rental businesses, vacation rentals, and real estate agencies. It's a direct competitor to WP Rentals with modern features and seamless API integration.

= Key Features =

* **API Integration**: Connect to Rentinno API or any REST API for dynamic property listings
* **Advanced Search**: Powerful search with filters for price, location, amenities, bedrooms, bathrooms, property type, and dates
* **Modern UI**: Vacation rental style components including cards, sliders, galleries, modals, grids, and interactive maps
* **Fully Responsive**: Mobile-first design that looks great on all devices
* **Theme Options Panel**: Easy-to-use admin panel for API configuration and field mapping
* **No Database Required**: All properties are fetched dynamically from the API
* **Shortcodes**: Multiple shortcodes for properties grid, featured slider, search form, and maps
* **Widgets**: Custom widgets for recent properties, search, and property types
* **Gutenberg Blocks**: Native blocks for the WordPress editor
* **SEO Optimized**: Clean code following WordPress best practices
* **Translation Ready**: Fully translatable with .pot file included

= Templates Included =

* Home Page Template
* Properties Listing Template
* Single Property Template
* Custom Page Templates

= Shortcodes =

* [rentword_properties] - Display property grid
* [rentword_search] - Display search form
* [rentword_featured_slider] - Featured properties slider
* [rentword_map] - Properties map
* [rentword_property_types] - Property types grid
* [rentword_property id="123"] - Single property

= Widgets =

* Recent Properties Widget
* Property Search Widget
* Property Types Widget

= Gutenberg Blocks =

* Properties Grid Block
* Featured Properties Slider Block
* Property Search Form Block
* Properties Map Block

== Installation ==

1. Download the theme ZIP file
2. Go to Appearance > Themes in your WordPress admin
3. Click "Add New" then "Upload Theme"
4. Select the downloaded ZIP file and click "Install Now"
5. Activate the theme
6. Go to RentWord > API Settings to configure your API

= Configuration =

1. Navigate to RentWord > API Settings in the WordPress admin
2. Enter your Rentinno API URL
3. (Optional) Enter your API key if required
4. Configure cache duration and properties per page
5. Go to RentWord > Field Mapping to map your API fields
6. Create pages using the included templates

= Creating Pages =

1. **Home Page**: Create a new page, select "Home Page" template, publish
2. **Properties Listing**: Create a page called "Properties", select "Properties Listing" template
3. **Single Property**: Create a page called "Property", select "Property Single" template

The theme will automatically handle property display using the configured API.

== Frequently Asked Questions ==

= What API does this theme support? =

The theme is designed to work with Rentinno API but can be configured to work with any REST API that returns property data in JSON format. You can map the API fields in the Field Mapping settings.

= Do I need a database for properties? =

No! All properties are fetched dynamically from the API. This keeps your WordPress installation lightweight and always synchronized with your property management system.

= Can I customize the field mapping? =

Yes! The theme includes a comprehensive field mapping system. You can map any API field to the theme's expected fields (title, price, images, location, description, amenities, coordinates, bedrooms, bathrooms, property type, area, availability, featured).

= Is the theme responsive? =

Absolutely! The theme is built with a mobile-first approach and looks great on all devices including desktops, tablets, and smartphones.

= Can I use this theme without an API? =

The theme is designed to work with an API. If you don't have an API, you would need to create one or use a property management system that provides a REST API.

= How do I clear the API cache? =

You can clear the cache from the admin bar (when logged in as administrator) by clicking "Clear RentWord Cache". You can also disable caching by setting cache duration to 0 in the API Settings.

= Can I customize the design? =

Yes! The theme includes CSS custom properties (CSS variables) for easy color and styling customization. You can add custom CSS in Appearance > Customize > Additional CSS.

= Does it work with page builders? =

The theme includes Gutenberg blocks and can work with most page builders. However, it's designed to work best with the native WordPress editor and the included templates.

== Changelog ==

= 1.0.0 =
* Initial release
* API integration with Rentinno
* Field mapping system
* Advanced property search
* Home, listing, and single property templates
* Shortcodes and widgets
* Gutenberg blocks
* Modern vacation rental UI
* Interactive maps with Leaflet
* Image galleries and sliders
* Responsive design
* Theme options panel

== Upgrade Notice ==

= 1.0.0 =
Initial release of RentWord Pro theme.

== Credits ==

* Swiper - https://swiperjs.com/ (MIT License)
* Leaflet - https://leafletjs.com/ (BSD 2-Clause License)
* Icons - CSS-based custom icons

== Support ==

For support, please visit: https://rentword.com/support
Documentation: https://rentword.com/docs

== Copyright ==

RentWord Pro WordPress Theme, Copyright 2024 RentWord Team
RentWord Pro is distributed under the terms of the GNU GPL v2 or later

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
