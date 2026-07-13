<?php
/**
 * Plugin Name:       Gallery Filter
 * Plugin URI:        https://servkh.com/
 * Description:       A lightweight filterable gallery with Elementor widget support. Add projects, assign categories, and drop the widget anywhere on your page.
 * Version:           1.10.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Servkh
 * Author URI:        https://servkh.com/
 * License:           GPL-2.0+
 * Text Domain:       gallery-filter
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GF_VERSION',    '1.10.0' );
define( 'GF_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'GF_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * The default tag vocabulary, used until a custom list is saved under
 * Gallery Filter → Settings.
 *
 * @return string[]
 */
function gf_default_tag_options() {
	return [
		'Residential',
		'Commercial',
		'Agricultural',
		'Municipal',
		'New Installation',
		'Resurfacing',
		'Repair',
		'Parking Lot',
		'Driveway',
		'Roadway',
		'Stone Base',
		'Heavy Duty',
		'Custom Design',
		'Weekend/Off-Hours Work',
		'ADA Compliant',
		'Drainage Solutions',
	];
}

/**
 * The selectable tag vocabulary, shared by the admin project screen and the
 * Elementor widget. Reads the list saved under Gallery Filter → Settings
 * (one tag per line); falls back to the defaults. Filter `gf_tag_options`
 * to adjust programmatically.
 *
 * @return string[]
 */
function gf_tag_options() {
	$stored = get_option( 'gf_gallery_tags', '' );

	if ( is_string( $stored ) && trim( $stored ) !== '' ) {
		$tags = array_values( array_filter( array_map( 'trim', preg_split( '/[\r\n]+/', $stored ) ) ) );
	} else {
		$tags = gf_default_tag_options();
	}

	return apply_filters( 'gf_tag_options', $tags );
}

// ── CPT & Taxonomy ────────────────────────────────────────────────────────────
require_once GF_PLUGIN_DIR . 'includes/class-cpt.php';

// ── Elementor Widget ──────────────────────────────────────────────────────────
add_action( 'elementor/widgets/register', function ( $manager ) {
	require_once GF_PLUGIN_DIR . 'includes/class-elementor-widget.php';
	$manager->register( new \GalleryFilter\Elementor_Widget() );
} );

// Warn if Elementor is not active (admin only)
add_action( 'admin_notices', function () {
	if ( ! did_action( 'elementor/loaded' ) ) {
		echo '<div class="notice notice-warning is-dismissible"><p>';
		echo '<strong>Gallery Filter:</strong> Elementor is not active. The Gallery Filter widget requires Elementor to be installed and activated.';
		echo '</p></div>';
	}
} );

// ── Front-end Assets ──────────────────────────────────────────────────────────
add_action( 'wp_enqueue_scripts', function () {
	wp_register_style(
		'gallery-filter',
		GF_PLUGIN_URL . 'assets/css/gallery-filter.css',
		[],
		GF_VERSION
	);
	wp_register_script(
		'gallery-filter',
		GF_PLUGIN_URL . 'assets/js/gallery-filter.js',
		[],
		GF_VERSION,
		true
	);
} );

// ── Elementor Editor Assets ───────────────────────────────────────────────────
add_action( 'elementor/editor/after_enqueue_styles', function () {
	wp_enqueue_style(
		'gallery-filter',
		GF_PLUGIN_URL . 'assets/css/gallery-filter.css',
		[],
		GF_VERSION
	);
} );

// ── Elementor Preview (editor canvas iframe) ──────────────────────────────────
// Ensures the grid/card layout renders correctly inside the editor preview.
add_action( 'elementor/preview/enqueue_styles', function () {
	wp_enqueue_style( 'gallery-filter' );
} );
