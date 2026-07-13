# Changelog

All notable changes to **Gallery Filter** are documented here.
The format is based on [Keep a Changelog](https://keepachangelog.com/),
and this project adheres to [Semantic Versioning](https://semver.org/).

## [1.12.1] — 2026-07-13
### Fixed
- Lightbox close and prev/next controls could appear as themed squares with no visible icon on themes that style all buttons aggressively. They are now reset (with `!important` and higher specificity) to the intended dark circular buttons regardless of theme; the Elementor Lightbox color controls still override them.

## [1.12.0] — 2026-07-13
### Added
- "Filter Bar Layout" option (Filter Bar → Filter Bar Layout). "Single line (scroll)" keeps many category buttons on one horizontally-scrollable row — responsive and tidy on mobile — instead of wrapping to multiple lines.

## [1.11.1] — 2026-07-13
### Changed
- Categories now come from the existing Gallery Filter → Categories taxonomy (no separate list). The project Category dropdown and the Elementor Category dropdown both read those terms; the redundant Categories box on the Settings page was removed. Tags are still managed on the Settings page.

## [1.11.0] — 2026-07-13
### Added
- Categories are a selectable dropdown on the project screen (which sets the backend Gallery Category term) and in the Elementor widget.

## [1.10.0] — 2026-07-13
### Added
- Settings page (Gallery Filter → Settings) to edit the tag list from the dashboard — one tag per line, reorderable. Falls back to the built-in defaults when empty.

## [1.9.0] — 2026-07-13
### Added
- Explicit filter buttons: Filter Bar → "Filter Buttons From" can be set to a Custom list (an ordered repeater of category names) so you control exactly which buttons appear and in what order — or leave it on Automatic to build them from your items.

## [1.8.3] — 2026-07-13
### Changed
- The widget now starts empty (no demo items), so a freshly dropped Gallery Filter has no items and no filter buttons until you add your own — better for reuse across sites.

## [1.8.2] — 2026-07-13
### Changed
- Plugin author set to Servkh (https://servkh.com/).

## [1.8.1] — 2026-07-13
### Added
- Two more filter button styles: "Underline" (text buttons, active one underlined) and "Minimal (text)" (plain text, active is a filled pill).

## [1.8.0] — 2026-07-13
### Added
- Filter Button Style control (Filter Bar → Button Style) with an "Outline" option — bordered, transparent buttons with the active one filled — alongside the default filled "Pills". Colors come from the Filter Buttons style section.

## [1.7.3] — 2026-07-13
### Changed
- Masonry shows the title, location, and tags at all times, exactly like the Grid layout — only the image heights differ. (Reverts the hover-only behavior from 1.7.2.)

## [1.7.2] — 2026-07-13
### Changed
- Masonry rendered as a clean image mosaic with info revealed on hover (matching a reference). Superseded by 1.7.3.

## [1.7.1] — 2026-07-12
### Changed
- In Masonry, the title, location, tags, badge, and arrow stay visible just like Grid — only the layout (natural image heights) differs.

## [1.7.0] — 2026-07-12
### Added
- "Masonry" layout style (Layout → Layout Style) that keeps each image's natural proportions — alongside the existing uniform Grid, which stays the default so nothing changes unless you choose Masonry.

## [1.6.0] — 2026-07-10
### Added
- Location field per project (e.g. "Lebanon County") — shown under the title on the card (with a pin icon) and in the lightbox. Available on both the project screen and the Elementor widget.

## [1.5.0] — 2026-07-10
### Added
- Tags are chosen from a fixed, selectable list instead of free text — a checkbox list on the project screen and a multi-select in the Elementor widget, both drawing from one shared vocabulary (filterable via `gf_tag_options`).

## [1.4.0] — 2026-07-10
### Changed
- Card design polish: titles capped to 2 lines at a sensible size, a taller/softer overlay keeps them readable without darkening the whole photo, tags limited to 3 with a "+N" pill so rows line up, and clickable cards get a subtle hover lift.

## [1.3.4] — 2026-07-10
### Changed
- Much larger, higher-contrast lightbox controls — bigger close, prev/next, and comparison-handle buttons with a white ring and larger icons so they're clearly visible on any image.

## [1.3.3] — 2026-07-10
### Fixed
- The lightbox image and before/after comparison could blow up to full-screen (and hide the gallery) on themes that force `img { max-width:100% }`. Lightbox and card image sizes are now enforced so they stay within the viewport regardless of theme CSS.

## [1.3.2] — 2026-07-10
### Fixed
- Lightbox close and prev/next buttons now use a dark background so their icons stay visible over the full-bleed before/after comparison (they previously disappeared against bright images).

## [1.3.1] — 2026-07-10
### Added
- WordPress `readme.txt` with metadata and changelog.
### Fixed
- Plugin CSS/JS now load inside the Elementor editor preview (the grid/card layout no longer appears broken in the editor).
- A Before/After pair no longer hides the gallery — the lightbox shows the comparison first, then the gallery images, with shared prev/next navigation.

## [1.3.0] — 2026-07-10
### Added
- Before/After comparison slider, shown on the card and enlarged in the lightbox (mouse drag, touch, and keyboard). Includes a "Before / After Slider" style section (divider, handle, and label styling) and Before/After image pickers in the backend and the manual repeater.

## [1.2.0] — 2026-07-10
### Added
- Full Elementor design controls — Lightbox style section (backdrop, title, description, buttons), responsive card height/gaps/filter spacing, filter button hover state and border, card border, and an optional "Show Description on Card" toggle with styling.

## [1.1.0] — 2026-07-10
### Added
- The Gallery Project post type is now displayed by the widget (previously unused). Added project descriptions (editor), a gallery-images meta box with a media picker, and a Source control to choose Gallery Projects or manual items.
- The lightbox shows the project description.
- A release workflow that publishes an installable, correctly-foldered plugin ZIP on each `v*` tag.
### Changed
- The post type is admin-only (no public front-end pages).
### Fixed
- Cached lightbox images could stay invisible; only image-bearing cards are focusable.

## [1.0.0] — 2026-03-27
### Added
- Initial release: filterable gallery, Elementor widget, and the Gallery Project post type with categories.

[1.12.1]: https://github.com/Servkh/gallery-filter/releases/tag/v1.12.1
[1.12.0]: https://github.com/Servkh/gallery-filter/releases/tag/v1.12.0
[1.11.1]: https://github.com/Servkh/gallery-filter/releases/tag/v1.11.1
[1.11.0]: https://github.com/Servkh/gallery-filter/releases/tag/v1.11.0
[1.10.0]: https://github.com/Servkh/gallery-filter/releases/tag/v1.10.0
[1.9.0]: https://github.com/Servkh/gallery-filter/releases/tag/v1.9.0
[1.8.3]: https://github.com/Servkh/gallery-filter/releases/tag/v1.8.3
[1.8.2]: https://github.com/Servkh/gallery-filter/releases/tag/v1.8.2
[1.8.1]: https://github.com/Servkh/gallery-filter/releases/tag/v1.8.1
[1.8.0]: https://github.com/Servkh/gallery-filter/releases/tag/v1.8.0
[1.7.3]: https://github.com/Servkh/gallery-filter/releases/tag/v1.7.3
[1.7.2]: https://github.com/Servkh/gallery-filter/releases/tag/v1.7.2
[1.7.1]: https://github.com/Servkh/gallery-filter/releases/tag/v1.7.1
[1.7.0]: https://github.com/Servkh/gallery-filter/releases/tag/v1.7.0
[1.6.0]: https://github.com/Servkh/gallery-filter/releases/tag/v1.6.0
[1.5.0]: https://github.com/Servkh/gallery-filter/releases/tag/v1.5.0
[1.4.0]: https://github.com/Servkh/gallery-filter/releases/tag/v1.4.0
[1.3.4]: https://github.com/Servkh/gallery-filter/releases/tag/v1.3.4
[1.3.3]: https://github.com/Servkh/gallery-filter/releases/tag/v1.3.3
[1.3.2]: https://github.com/Servkh/gallery-filter/releases/tag/v1.3.2
[1.3.1]: https://github.com/Servkh/gallery-filter/releases/tag/v1.3.1
[1.3.0]: https://github.com/Servkh/gallery-filter/releases/tag/v1.3.0
[1.2.0]: https://github.com/Servkh/gallery-filter/releases/tag/v1.2.0
[1.1.0]: https://github.com/Servkh/gallery-filter/releases/tag/v1.1.0
[1.0.0]: https://github.com/Servkh/gallery-filter/releases/tag/v1.0.0
