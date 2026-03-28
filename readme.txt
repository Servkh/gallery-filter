=== Gallery Filter ===
Contributors: servkh
Tags: gallery, filter, elementor, portfolio, grid, images, lightbox
Requires at least: 5.8
Tested up to: 6.7
Stable tag: 1.1.2
Requires PHP: 7.4
License: GPL-2.0+
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A lightweight filterable gallery with Elementor widget support. Add projects, assign categories, and drop the widget anywhere on your page.

== Description ==

Gallery Filter is a lightweight, performance-focused filterable gallery plugin built for Elementor. Add gallery projects, assign categories, and let visitors filter through them instantly — no page reloads, no jQuery dependencies.

**Key Features**

* Filterable gallery grid with animated category switching
* Built-in lightbox with keyboard navigation (arrow keys, Escape) and touch swipe support
* Every image in a gallery item gets its own card — visitors see the full collection at a glance
* Responsive grid with independent column controls for desktop, tablet, and mobile
* Equal height or natural aspect-ratio card modes
* Full Elementor style controls: filter buttons, cards, badges, titles, tags, arrow button, and lightbox
* Custom Post Type (Gallery Projects) with Gallery Category taxonomy for content management
* Elementor icon picker for lightbox navigation buttons
* Compatible with all major caching plugins (SG Cache, WP Rocket, W3 Total Cache, LiteSpeed Cache)

**Elementor Style Controls**

* Filter bar: background, text color, active state, border radius, padding, typography, alignment
* Cards: gap, border radius, box shadow, overlay color, hover zoom toggle
* Category badge: background, text color, border radius, typography
* Title: color, typography
* Tags: background, text color, border radius, typography
* Arrow button: background, icon color, size, border radius
* Lightbox: backdrop color, image size, close button, prev/next buttons (independent), footer

== Installation ==

1. Upload the `gallery-filter` folder to `/wp-content/plugins/`
2. Activate the plugin through the **Plugins** menu in WordPress
3. Make sure **Elementor** is installed and activated
4. Go to **Gallery Filter → Add New** in the WordPress admin to create gallery projects
5. Assign a **Gallery Category** to each project for filtering
6. Edit any page with Elementor, search for **Gallery Filter** in the widget panel, and drag it onto your page

== Frequently Asked Questions ==

= Does this plugin require Elementor? =

Yes. The gallery widget is built on top of Elementor's widget system. A warning will appear in the WordPress admin if Elementor is not active.

= Can I use it without the Custom Post Type? =

Yes. The Elementor widget has a built-in repeater where you can add gallery items directly — no CPT required. The CPT is an optional workflow for managing larger collections.

= Does it work with caching plugins? =

Yes. Asset versions are set using the file modification timestamp (`filemtime`), so cached files are automatically invalidated whenever the plugin is updated. Dynamic styles are output as inline CSS, which is always current.

= How many images can I add per gallery item? =

There is no hard limit. Each image becomes its own card in the grid. All images from the same item share a lightbox gallery, so visitors can browse them with prev/next navigation.

= Is it mobile friendly? =

Yes. The grid is fully responsive with separate column controls for desktop, tablet, and mobile. The lightbox supports touch swipe gestures for navigating between images.

== Screenshots ==

1. Filterable gallery grid on the frontend
2. Lightbox with navigation controls
3. Elementor widget panel — Content tab
4. Elementor widget panel — Style tab (Filter Buttons)
5. Elementor widget panel — Style tab (Lightbox)
6. Gallery Project admin screen

== Changelog ==

= 1.1.2 =
* Added Equal Height Cards toggle with aspect ratio options (16:9, 4:3, 3:2, 1:1, 2:3, 3:4)
* Asset versioning now uses filemtime() for automatic cache busting with all caching plugins

= 1.1.1 =
* Lightbox image sizing moved to inline style for cache-independent rendering
* Added Image Width and Image Height controls to resize the lightbox stage

= 1.1.0 =
* Lightbox images now fill the available stage area using object-fit: contain

= 1.0.9 =
* Replaced separate prev/next icon size controls with one central nav icon size control
* Icon sizes applied via inline style with !important for reliable rendering

= 1.0.8 =
* Fixed icon size control not applying due to Elementor CSS cache and theme overrides

= 1.0.7 =
* Fixed icon size scaling with button size (decoupled em-based sizing to explicit px)

= 1.0.6 =
* Split Prev/Next button controls into fully independent sections (icon, bg, color, size, radius)
* Added Image Width and Image Height sliders to the Lightbox style section

= 1.0.5 =
* Fixed PHP parse error causing site crash after adding icon size controls

= 1.0.4 =
* Fixed fatal crash: guarded Icons_Manager calls with null checks for existing widgets

= 1.0.3 =
* Added icon size controls for lightbox close and nav buttons

= 1.0.2 =
* Replaced hardcoded SVG icons in lightbox with Elementor icon picker controls

= 1.0.1 =
* Fixed lightbox style controls not applying due to CSS specificity conflicts with theme
* Prefixed all lightbox CSS rules with .gf-lightbox for higher specificity

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.1.2 =
Adds equal height card toggle and improved cache busting. Upload all plugin files and purge your cache after upgrading.
