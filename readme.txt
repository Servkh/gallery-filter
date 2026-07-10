=== Gallery Filter ===
Contributors: servkh
Tags: gallery, filter, portfolio, elementor, before-after
Requires at least: 5.8
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 1.4.0
License: GPL-2.0+
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A lightweight, filterable portfolio gallery with an Elementor widget, category filtering, a lightbox, and a before/after comparison slider.

== Description ==

Gallery Filter lets you build a filterable portfolio of projects and drop it anywhere with an Elementor widget. Each project can have a cover photo, a gallery, a category (used for the filter buttons), tags, a description, an optional link, and a before/after comparison slider.

You can manage projects two ways, switchable per widget:

* **Gallery Projects (WP Admin)** — a dedicated post type under the "Gallery Filter" menu, with a media-picker gallery, a WYSIWYG description, tags, categories, and before/after images. Best for many reusable projects and non-Elementor editors.
* **Manual items** — enter everything directly in the Elementor widget with a live preview. Best for one-off pages.

The same Elementor Style controls apply to both.

**Highlights**

* Category filter bar (no page reload) with an "All" button.
* Responsive grid — per-device columns, card height, and spacing.
* Accessible lightbox — keyboard, touch swipe, and focus support; shows the project description.
* Before/After comparison slider on the card and enlarged in the lightbox (drag, touch, and arrow-key support). Gallery images appear after the comparison in the lightbox.
* Fully designable in Elementor: filter buttons (normal/hover/active, border), cards (border, radius, shadow, overlay), badge, title, tags, arrow, lightbox (backdrop, text, buttons), and the before/after slider (divider, handle, labels).
* No jQuery or external libraries on the front end.

== Installation ==

1. Upload the `gallery-filter` folder to `/wp-content/plugins/`, or install the ZIP via **Plugins → Add New → Upload Plugin**.
2. Activate the plugin through the **Plugins** screen.
3. Make sure **Elementor** is installed and active (the widget requires it).
4. Add projects under **Gallery Filter → Add New Project**, or choose **Manual items** in the widget.
5. Edit a page with Elementor, search for the **Gallery Filter** widget, and drop it in.

== Frequently Asked Questions ==

= Does it require Elementor? =
Yes. The gallery is rendered by an Elementor widget. An admin notice appears if Elementor is not active.

= How do I create a before/after slider? =
On a project (or a manual item), set both a **Before Image** and an **After Image**. When both are set, the card becomes a draggable comparison slider and the lightbox shows the comparison first, followed by any gallery images.

= Will updating the plugin delete my projects? =
No. Projects are stored in the WordPress database, not in the plugin folder. Updating or reinstalling the plugin leaves them untouched.

= The Elementor editor preview looks unstyled. =
Reload the editor after updating so it loads the current CSS/JS (browsers cache assets aggressively). Fixed in 1.3.1.

== Changelog ==

= 1.4.0 =
* Improve: card design polish — titles are capped to 2 lines at a sensible size (no more oversized, overrunning headings), a taller/softer overlay keeps them readable without darkening the whole photo, tags are limited to 3 with a "+N" pill so rows line up, and clickable cards get a subtle hover lift.

= 1.3.4 =
* Improve: much larger, higher-contrast lightbox controls — bigger close, prev/next, and comparison-handle buttons with a white ring and larger icons so they're clearly visible on any image.

= 1.3.3 =
* Fix: the lightbox image and before/after comparison could blow up to full-screen (and hide the gallery) on themes that force `img { max-width:100% }`. Lightbox and card image sizes are now enforced so they stay within the viewport regardless of theme CSS.

= 1.3.2 =
* Fix: lightbox close and prev/next buttons now use a dark background so their icons stay visible over the full-bleed before/after comparison (they previously disappeared against bright images).

= 1.3.1 =
* Fix: plugin CSS/JS now load inside the Elementor editor preview (grid/card layout no longer appears broken in the editor).
* Fix: a Before/After pair no longer hides the gallery — the lightbox shows the comparison first, then the gallery images, with shared prev/next navigation.

= 1.3.0 =
* New: Before/After comparison slider, shown on the card and enlarged in the lightbox (mouse drag, touch, and keyboard). Includes a "Before / After Slider" style section (divider, handle, and label styling) and Before/After image pickers in the backend and the manual repeater.

= 1.2.0 =
* New: full Elementor design controls — Lightbox style section (backdrop, title, description, buttons), responsive card height/gaps/filter spacing, filter button hover state and border, card border, and an optional "Show Description on Card" toggle with styling.

= 1.1.0 =
* New: the Gallery Project post type is now displayed by the widget (previously unused). Added project descriptions (editor), a gallery-images meta box with a media picker, and a Source control to choose Gallery Projects or manual items.
* New: the lightbox shows the project description.
* Change: the post type is admin-only (no public front-end pages).
* Fix: cached lightbox images could stay invisible; only image-bearing cards are focusable.

= 1.0.0 =
* Initial release: filterable gallery, Elementor widget, and the Gallery Project post type with categories.

== Upgrade Notice ==

= 1.3.1 =
Fixes the Elementor editor preview and lets the gallery work alongside a before/after pair. Reload the editor after updating.
