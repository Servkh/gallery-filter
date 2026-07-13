<?php
/**
 * Elementor Widget: Gallery Filter
 *
 * @package GalleryFilter
 */

namespace GalleryFilter;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Elementor_Widget extends Widget_Base {

	public function get_name()       { return 'gallery_filter'; }
	public function get_title()      { return 'Gallery Filter'; }
	public function get_icon()       { return 'eicon-gallery-masonry'; }
	public function get_categories() { return [ 'general' ]; }
	public function get_keywords()   { return [ 'gallery', 'filter', 'portfolio', 'projects', 'grid', 'images' ]; }

	// Tell Elementor which assets this widget needs, so they load on the
	// front end AND inside the editor preview (where render()-time enqueues
	// don't reach the iframe).
	public function get_style_depends()  { return [ 'gallery-filter' ]; }
	public function get_script_depends() { return [ 'gallery-filter' ]; }

	// ── Controls ──────────────────────────────────────────────────────────────

	protected function register_controls() {

		/* ────────── CONTENT TAB ────────── */

		/* ── Source ── */

		$this->start_controls_section( 'section_source', [
			'label' => 'Source',
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control( 'source', [
			'label'       => 'Content Source',
			'type'        => Controls_Manager::SELECT,
			'default'     => 'cpt',
			'options'     => [
				'cpt'    => 'Gallery Projects (managed in WP Admin)',
				'manual' => 'Manual items (entered below)',
			],
			'description' => 'Pull projects from the Gallery Filter post type, or enter items by hand in this widget.',
		] );

		$this->add_control( 'cpt_limit', [
			'label'       => 'Number of Projects',
			'type'        => Controls_Manager::NUMBER,
			'default'     => 0,
			'min'         => 0,
			'description' => '0 = show all published projects.',
			'condition'   => [ 'source' => 'cpt' ],
		] );

		$this->add_control( 'cpt_orderby', [
			'label'     => 'Order By',
			'type'      => Controls_Manager::SELECT,
			'default'   => 'date',
			'options'   => [
				'date'       => 'Newest first',
				'menu_order' => 'Manual order (Page Attributes → Order)',
				'title'      => 'Title (A–Z)',
			],
			'condition' => [ 'source' => 'cpt' ],
		] );

		$this->end_controls_section();

		/* ── Gallery Items (Repeater) ── */

		$this->start_controls_section( 'section_items', [
			'label'     => 'Gallery Items',
			'tab'       => Controls_Manager::TAB_CONTENT,
			'condition' => [ 'source' => 'manual' ],
		] );

		$repeater = new Repeater();

		// Shared, selectable tag vocabulary (from gf_tag_options()).
		$tag_options = [];
		if ( function_exists( 'gf_tag_options' ) ) {
			foreach ( gf_tag_options() as $gf_tag ) {
				$tag_options[ $gf_tag ] = $gf_tag;
			}
		}

		$repeater->add_control( 'images', [
			'label'       => 'Images',
			'type'        => Controls_Manager::GALLERY,
			'description' => 'First image is the card cover; all images open in the lightbox. If a Before/After pair is also set, the comparison shows first and these images follow it.',
			'default'     => [],
		] );

		$repeater->add_control( 'before_image', [
			'label'       => 'Before Image',
			'type'        => Controls_Manager::MEDIA,
			'description' => 'Set both Before and After to show a draggable comparison slider on the card. Gallery images above still open in the lightbox after it.',
			'default'     => [ 'url' => '' ],
		] );

		$repeater->add_control( 'after_image', [
			'label'   => 'After Image',
			'type'    => Controls_Manager::MEDIA,
			'default' => [ 'url' => '' ],
		] );

		$repeater->add_control( 'title', [
			'label'       => 'Title',
			'type'        => Controls_Manager::TEXT,
			'default'     => 'Project Title',
			'label_block' => true,
		] );

		$repeater->add_control( 'category', [
			'label'       => 'Category',
			'type'        => Controls_Manager::TEXT,
			'default'     => '',
			'placeholder' => 'e.g. Residential',
			'description' => 'Used for the filter buttons. Items with the same category name are grouped together.',
			'label_block' => true,
		] );

		$repeater->add_control( 'location', [
			'label'       => 'Location',
			'type'        => Controls_Manager::TEXT,
			'default'     => '',
			'placeholder' => 'e.g. Lebanon County',
			'label_block' => true,
		] );

		$repeater->add_control( 'tags', [
			'label'       => 'Tags',
			'type'        => Controls_Manager::SELECT2,
			'multiple'    => true,
			'options'     => $tag_options,
			'label_block' => true,
			'description' => 'Select the labels to show on the card (first 3 appear, with a “+N” for the rest).',
		] );

		$repeater->add_control( 'description', [
			'label'       => 'Description',
			'type'        => Controls_Manager::TEXTAREA,
			'rows'        => 4,
			'default'     => '',
			'description' => 'Shown in the lightbox when the project is opened.',
		] );

		$repeater->add_control( 'link', [
			'label'         => 'Link',
			'type'          => Controls_Manager::URL,
			'placeholder'   => 'https://example.com/project',
			'show_external' => true,
			'default'       => [ 'url' => '', 'is_external' => true, 'nofollow' => false ],
		] );

		$this->add_control( 'gallery_items', [
			'label'       => 'Items',
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'default'     => [
				[
					'images'   => [],
					'title'    => 'Country Lane Driveway Restoration',
					'category' => 'Residential',
					'location' => 'Lebanon County',
					'tags'     => [ 'Residential', 'New Installation', 'Stone Base', 'Drainage Solutions' ],
					'description' => 'Complete driveway replacement for a rural property including excavation, stone base installation, and 2.5" of Superpave asphalt. We graded the surface for proper water runoff and installed a new stone base before paving.',
				],
				[
					'images'   => [],
					'title'    => 'Medical Office Parking Lot',
					'category' => 'Commercial',
					'tags'     => [ 'Commercial', 'Resurfacing', 'Parking Lot' ],
					'description' => 'Full-depth reclamation and resurfacing of a busy medical office parking lot, completed in phases to keep the facility open throughout the project.',
				],
				[
					'images'   => [],
					'title'    => 'Historic Home Driveway',
					'category' => 'Residential',
					'tags'     => [ 'Residential', 'Custom Design' ],
					'description' => 'A custom driveway design sympathetic to a historic property, balancing modern durability with a look appropriate to the home\'s period character.',
				],
			],
			'title_field' => '{{{ title }}}',
		] );

		$this->end_controls_section();

		/* ── Layout ── */

		$this->start_controls_section( 'section_layout', [
			'label' => 'Layout',
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control( 'layout_style', [
			'label'       => 'Layout Style',
			'type'        => Controls_Manager::SELECT,
			'default'     => 'grid',
			'options'     => [
				'grid'    => 'Grid (uniform cards)',
				'masonry' => 'Masonry (natural heights)',
			],
			'description' => 'Grid crops every card to the same height. Masonry keeps each image’s natural proportions (Pinterest-style). Everything else — title, location, tags, badge, arrow — stays the same.',
		] );

		$this->add_control( 'columns', [
			'label'   => 'Columns (Desktop)',
			'type'    => Controls_Manager::SELECT,
			'default' => '3',
			'options' => [ '1' => '1', '2' => '2', '3' => '3', '4' => '4' ],
		] );

		$this->add_control( 'columns_tablet', [
			'label'   => 'Columns (Tablet)',
			'type'    => Controls_Manager::SELECT,
			'default' => '2',
			'options' => [ '1' => '1', '2' => '2', '3' => '3' ],
		] );

		$this->add_control( 'columns_mobile', [
			'label'   => 'Columns (Mobile)',
			'type'    => Controls_Manager::SELECT,
			'default' => '1',
			'options' => [ '1' => '1', '2' => '2' ],
		] );

		$this->add_responsive_control( 'card_height', [
			'label'      => 'Card Height',
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'vh' ],
			'range'      => [ 'px' => [ 'min' => 150, 'max' => 800 ], 'vh' => [ 'min' => 10, 'max' => 80 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 450 ],
			'selectors'  => [ '{{WRAPPER}} .gf-grid:not(.gf-grid--masonry) .gf-card' => 'height: {{SIZE}}{{UNIT}};' ],
			'condition'  => [ 'layout_style' => 'grid' ],
		] );

		$this->add_control( 'show_desc_on_card', [
			'label'        => 'Show Description on Card',
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => 'Yes',
			'label_off'    => 'No',
			'return_value' => 'yes',
			'default'      => '',
			'separator'    => 'before',
			'description'  => 'Display the project description directly on each card (it always shows in the lightbox too).',
		] );

		$this->add_control( 'desc_card_lines', [
			'label'       => 'Description Lines (before trimming)',
			'type'        => Controls_Manager::NUMBER,
			'default'     => 3,
			'min'         => 1,
			'max'         => 10,
			'selectors'   => [ '{{WRAPPER}} .gf-card-desc' => '-webkit-line-clamp: {{VALUE}};' ],
			'condition'   => [ 'show_desc_on_card' => 'yes' ],
		] );

		$this->end_controls_section();

		/* ── Filter Bar ── */

		$this->start_controls_section( 'section_filter_content', [
			'label' => 'Filter Bar',
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control( 'show_filter', [
			'label'        => 'Show Filter Buttons',
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => 'Yes',
			'label_off'    => 'No',
			'return_value' => 'yes',
			'default'      => 'yes',
		] );

		$this->add_control( 'all_label', [
			'label'     => '"All" Button Label',
			'type'      => Controls_Manager::TEXT,
			'default'   => 'All Projects',
			'condition' => [ 'show_filter' => 'yes' ],
		] );

		$this->add_control( 'filter_align', [
			'label'     => 'Alignment',
			'type'      => Controls_Manager::CHOOSE,
			'options'   => [
				'flex-start' => [ 'title' => 'Left',   'icon' => 'eicon-text-align-left' ],
				'center'     => [ 'title' => 'Center', 'icon' => 'eicon-text-align-center' ],
				'flex-end'   => [ 'title' => 'Right',  'icon' => 'eicon-text-align-right' ],
			],
			'default'   => 'center',
			'selectors' => [ '{{WRAPPER}} .gf-filter' => 'justify-content: {{VALUE}};' ],
			'condition' => [ 'show_filter' => 'yes' ],
		] );

		$this->add_control( 'filter_style', [
			'label'       => 'Button Style',
			'type'        => Controls_Manager::SELECT,
			'default'     => 'pills',
			'options'     => [
				'pills'     => 'Pills (filled)',
				'outline'   => 'Outline',
				'underline' => 'Underline',
				'minimal'   => 'Minimal (text)',
			],
			'description' => 'Pills: solid filled buttons. Outline: bordered, transparent (active stays filled). Underline: text buttons with an underline on the active one. Minimal: plain text, active is a filled pill. Colors come from the Filter Buttons style section.',
			'condition'   => [ 'show_filter' => 'yes' ],
		] );

		$this->end_controls_section();

		/* ────────── STYLE TAB ────────── */

		/* ── Filter Button Style ── */

		$this->start_controls_section( 'section_filter_style', [
			'label'     => 'Filter Buttons',
			'tab'       => Controls_Manager::TAB_STYLE,
			'condition' => [ 'show_filter' => 'yes' ],
		] );

		$this->add_responsive_control( 'filter_gap', [
			'label'     => 'Gap Between Buttons',
			'type'      => Controls_Manager::SLIDER,
			'size_units'=> [ 'px' ],
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
			'default'   => [ 'unit' => 'px', 'size' => 10 ],
			'selectors' => [ '{{WRAPPER}} .gf-filter' => 'gap: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'filter_margin_bottom', [
			'label'     => 'Space Below Filter Bar',
			'type'      => Controls_Manager::SLIDER,
			'size_units'=> [ 'px' ],
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 80 ] ],
			'default'   => [ 'unit' => 'px', 'size' => 32 ],
			'selectors' => [ '{{WRAPPER}} .gf-filter' => 'margin-bottom: {{SIZE}}{{UNIT}};' ],
		] );

		$this->start_controls_tabs( 'filter_tabs' );

		$this->start_controls_tab( 'filter_tab_normal', [ 'label' => 'Normal' ] );

		$this->add_control( 'filter_btn_bg', [
			'label'     => 'Background',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [ '{{WRAPPER}} .gf-filter-btn' => 'background-color: {{VALUE}};' ],
		] );

		$this->add_control( 'filter_btn_color', [
			'label'     => 'Text Color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#1a1a1a',
			'selectors' => [ '{{WRAPPER}} .gf-filter-btn' => 'color: {{VALUE}};' ],
		] );

		$this->end_controls_tab();

		$this->start_controls_tab( 'filter_tab_hover', [ 'label' => 'Hover' ] );

		$this->add_control( 'filter_btn_hover_bg', [
			'label'     => 'Background',
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .gf-filter-btn:hover' => 'background-color: {{VALUE}};' ],
		] );

		$this->add_control( 'filter_btn_hover_color', [
			'label'     => 'Text Color',
			'type'      => Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .gf-filter-btn:hover' => 'color: {{VALUE}};' ],
		] );

		$this->end_controls_tab();

		$this->start_controls_tab( 'filter_tab_active', [ 'label' => 'Active' ] );

		$this->add_control( 'filter_btn_active_bg', [
			'label'     => 'Background',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#8B1A1A',
			'selectors' => [ '{{WRAPPER}} .gf-filter-btn.is-active' => 'background-color: {{VALUE}};' ],
		] );

		$this->add_control( 'filter_btn_active_color', [
			'label'     => 'Text Color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [ '{{WRAPPER}} .gf-filter-btn.is-active' => 'color: {{VALUE}};' ],
		] );

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control( 'filter_btn_radius', [
			'label'     => 'Border Radius',
			'type'      => Controls_Manager::SLIDER,
			'size_units'=> [ 'px' ],
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
			'default'   => [ 'unit' => 'px', 'size' => 50 ],
			'selectors' => [ '{{WRAPPER}} .gf-filter-btn' => 'border-radius: {{SIZE}}{{UNIT}};' ],
			'separator' => 'before',
		] );

		$this->add_responsive_control( 'filter_btn_padding', [
			'label'      => 'Padding',
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px' ],
			'default'    => [ 'top' => '10', 'right' => '22', 'bottom' => '10', 'left' => '22', 'unit' => 'px', 'isLinked' => false ],
			'selectors'  => [ '{{WRAPPER}} .gf-filter-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'filter_typography',
			'selector' => '{{WRAPPER}} .gf-filter-btn',
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'      => 'filter_border',
			'selector'  => '{{WRAPPER}} .gf-filter-btn',
			'separator' => 'before',
		] );

		$this->end_controls_section();

		/* ── Card Style ── */

		$this->start_controls_section( 'section_card_style', [
			'label' => 'Cards',
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_responsive_control( 'card_gap', [
			'label'     => 'Gap Between Cards',
			'type'      => Controls_Manager::SLIDER,
			'size_units'=> [ 'px' ],
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
			'default'   => [ 'unit' => 'px', 'size' => 20 ],
			// gap for the grid layout; column-gap + --gf-gap drive the masonry layout.
			'selectors' => [ '{{WRAPPER}} .gf-grid' => 'gap: {{SIZE}}{{UNIT}}; column-gap: {{SIZE}}{{UNIT}}; --gf-gap: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( 'card_radius', [
			'label'     => 'Border Radius',
			'type'      => Controls_Manager::SLIDER,
			'size_units'=> [ 'px' ],
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
			'default'   => [ 'unit' => 'px', 'size' => 12 ],
			'selectors' => [ '{{WRAPPER}} .gf-card' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'card_border',
			'selector' => '{{WRAPPER}} .gf-card',
		] );

		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [
			'name'     => 'card_shadow',
			'selector' => '{{WRAPPER}} .gf-card',
		] );

		$this->add_control( 'overlay_heading', [
			'label'     => 'Overlay',
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$this->add_control( 'overlay_color', [
			'label'     => 'Overlay Color',
			'type'      => Controls_Manager::COLOR,
			'default'   => 'rgba(0,0,0,0.72)',
			'selectors' => [ '{{WRAPPER}} .gf-card-overlay' => 'background: linear-gradient(to top, {{VALUE}} 0%, transparent 72%);' ],
		] );

		$this->add_control( 'hover_zoom', [
			'label'        => 'Zoom Image on Hover',
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => 'Yes',
			'label_off'    => 'No',
			'return_value' => 'yes',
			'default'      => 'yes',
		] );

		$this->end_controls_section();

		/* ── Badge Style ── */

		$this->start_controls_section( 'section_badge_style', [
			'label' => 'Category Badge',
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'badge_bg', [
			'label'     => 'Background',
			'type'      => Controls_Manager::COLOR,
			'default'   => 'rgba(255,255,255,0.92)',
			'selectors' => [ '{{WRAPPER}} .gf-badge' => 'background-color: {{VALUE}};' ],
		] );

		$this->add_control( 'badge_color', [
			'label'     => 'Text Color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#222222',
			'selectors' => [ '{{WRAPPER}} .gf-badge' => 'color: {{VALUE}};' ],
		] );

		$this->add_control( 'badge_radius', [
			'label'     => 'Border Radius',
			'type'      => Controls_Manager::SLIDER,
			'size_units'=> [ 'px' ],
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 30 ] ],
			'default'   => [ 'unit' => 'px', 'size' => 20 ],
			'selectors' => [ '{{WRAPPER}} .gf-badge' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'badge_typography',
			'selector' => '{{WRAPPER}} .gf-badge',
		] );

		$this->end_controls_section();

		/* ── Title Style ── */

		$this->start_controls_section( 'section_title_style', [
			'label' => 'Title',
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'title_color', [
			'label'     => 'Color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [ '{{WRAPPER}} .gf-title' => 'color: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'title_typography',
			'selector' => '{{WRAPPER}} .gf-title',
		] );

		$this->add_control( 'title_margin', [
			'label'     => 'Bottom Margin',
			'type'      => Controls_Manager::SLIDER,
			'size_units'=> [ 'px' ],
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 30 ] ],
			'default'   => [ 'unit' => 'px', 'size' => 10 ],
			'selectors' => [ '{{WRAPPER}} .gf-title' => 'margin-bottom: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();

		/* ── Tag Style ── */

		$this->start_controls_section( 'section_tag_style', [
			'label' => 'Tags',
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'tag_bg', [
			'label'     => 'Background',
			'type'      => Controls_Manager::COLOR,
			'default'   => 'rgba(255,255,255,0.18)',
			'selectors' => [ '{{WRAPPER}} .gf-tag' => 'background-color: {{VALUE}};' ],
		] );

		$this->add_control( 'tag_color', [
			'label'     => 'Text Color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [ '{{WRAPPER}} .gf-tag' => 'color: {{VALUE}};' ],
		] );

		$this->add_control( 'tag_radius', [
			'label'     => 'Border Radius',
			'type'      => Controls_Manager::SLIDER,
			'size_units'=> [ 'px' ],
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 30 ] ],
			'default'   => [ 'unit' => 'px', 'size' => 20 ],
			'selectors' => [ '{{WRAPPER}} .gf-tag' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'tag_typography',
			'selector' => '{{WRAPPER}} .gf-tag',
		] );

		$this->end_controls_section();

		/* ── Arrow Style ── */

		$this->start_controls_section( 'section_arrow_style', [
			'label' => 'Arrow Button',
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'arrow_bg', [
			'label'     => 'Background',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#8B1A1A',
			'selectors' => [ '{{WRAPPER}} .gf-arrow' => 'background-color: {{VALUE}};' ],
		] );

		$this->add_control( 'arrow_color', [
			'label'     => 'Icon Color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [ '{{WRAPPER}} .gf-arrow' => 'color: {{VALUE}};' ],
		] );

		$this->add_control( 'arrow_size', [
			'label'     => 'Button Size',
			'type'      => Controls_Manager::SLIDER,
			'size_units'=> [ 'px' ],
			'range'     => [ 'px' => [ 'min' => 24, 'max' => 80 ] ],
			'default'   => [ 'unit' => 'px', 'size' => 42 ],
			'selectors' => [ '{{WRAPPER}} .gf-arrow' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( 'arrow_radius', [
			'label'     => 'Border Radius',
			'type'      => Controls_Manager::SLIDER,
			'size_units'=> [ 'px' ],
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
			'default'   => [ 'unit' => 'px', 'size' => 8 ],
			'selectors' => [ '{{WRAPPER}} .gf-arrow' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();

		/* ── Card Description Style ── */

		$this->start_controls_section( 'section_card_desc_style', [
			'label'     => 'Card Description',
			'tab'       => Controls_Manager::TAB_STYLE,
			'condition' => [ 'show_desc_on_card' => 'yes' ],
		] );

		$this->add_control( 'card_desc_color', [
			'label'     => 'Text Color',
			'type'      => Controls_Manager::COLOR,
			'default'   => 'rgba(255,255,255,0.85)',
			'selectors' => [ '{{WRAPPER}} .gf-card-desc' => 'color: {{VALUE}};' ],
		] );

		$this->add_control( 'card_desc_spacing', [
			'label'     => 'Space Above',
			'type'      => Controls_Manager::SLIDER,
			'size_units'=> [ 'px' ],
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 30 ] ],
			'default'   => [ 'unit' => 'px', 'size' => 8 ],
			'selectors' => [ '{{WRAPPER}} .gf-card-desc' => 'margin-top: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'card_desc_typography',
			'selector' => '{{WRAPPER}} .gf-card-desc',
		] );

		$this->end_controls_section();

		/* ── Lightbox Style ── */

		$this->start_controls_section( 'section_lightbox_style', [
			'label' => 'Lightbox',
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'lb_backdrop', [
			'label'     => 'Backdrop Color',
			'type'      => Controls_Manager::COLOR,
			'default'   => 'rgba(0,0,0,0.92)',
			'selectors' => [ '{{WRAPPER}} .gf-lb-backdrop' => 'background: {{VALUE}};' ],
		] );

		$this->add_control( 'lb_title_heading', [
			'label'     => 'Title',
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$this->add_control( 'lb_title_color', [
			'label'     => 'Title Color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [ '{{WRAPPER}} .gf-lb-title' => 'color: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'lb_title_typography',
			'selector' => '{{WRAPPER}} .gf-lb-title',
		] );

		$this->add_control( 'lb_desc_heading', [
			'label'     => 'Description',
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$this->add_control( 'lb_desc_color', [
			'label'     => 'Description Color',
			'type'      => Controls_Manager::COLOR,
			'default'   => 'rgba(255,255,255,0.85)',
			'selectors' => [ '{{WRAPPER}} .gf-lb-desc' => 'color: {{VALUE}}; opacity: 1;' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'lb_desc_typography',
			'selector' => '{{WRAPPER}} .gf-lb-desc',
		] );

		$this->add_control( 'lb_controls_heading', [
			'label'     => 'Navigation & Close Buttons',
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$this->add_control( 'lb_btn_bg', [
			'label'     => 'Button Background',
			'type'      => Controls_Manager::COLOR,
			'default'   => 'rgba(0,0,0,0.55)',
			'selectors' => [ '{{WRAPPER}} .gf-lb-close, {{WRAPPER}} .gf-lb-prev, {{WRAPPER}} .gf-lb-next' => 'background: {{VALUE}};' ],
		] );

		$this->add_control( 'lb_btn_color', [
			'label'     => 'Icon Color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [ '{{WRAPPER}} .gf-lb-close, {{WRAPPER}} .gf-lb-prev, {{WRAPPER}} .gf-lb-next' => 'color: {{VALUE}};' ],
		] );

		$this->end_controls_section();

		/* ── Before / After Slider Style ── */

		$this->start_controls_section( 'section_ba_style', [
			'label' => 'Before / After Slider',
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'ba_divider_color', [
			'label'     => 'Divider Color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [ '{{WRAPPER}} .gf-ba-handle, {{WRAPPER}} .gf-lb-ba-handle' => 'background: {{VALUE}};' ],
		] );

		$this->add_control( 'ba_handle_color', [
			'label'     => 'Handle Color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [ '{{WRAPPER}} .gf-ba-grip, {{WRAPPER}} .gf-lb-ba-grip' => 'background: {{VALUE}};' ],
		] );

		$this->add_control( 'ba_handle_icon_color', [
			'label'     => 'Handle Icon Color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#1a1a1a',
			'selectors' => [ '{{WRAPPER}} .gf-ba-grip, {{WRAPPER}} .gf-lb-ba-grip' => 'color: {{VALUE}};' ],
		] );

		$this->add_control( 'show_ba_labels', [
			'label'        => 'Show Before / After Labels',
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => 'Yes',
			'label_off'    => 'No',
			'return_value' => 'yes',
			'default'      => 'yes',
			'separator'    => 'before',
		] );

		$this->add_control( 'before_label', [
			'label'     => 'Before Label',
			'type'      => Controls_Manager::TEXT,
			'default'   => 'Before',
			'condition' => [ 'show_ba_labels' => 'yes' ],
		] );

		$this->add_control( 'after_label', [
			'label'     => 'After Label',
			'type'      => Controls_Manager::TEXT,
			'default'   => 'After',
			'condition' => [ 'show_ba_labels' => 'yes' ],
		] );

		$this->add_control( 'ba_label_bg', [
			'label'     => 'Label Background',
			'type'      => Controls_Manager::COLOR,
			'default'   => 'rgba(0,0,0,0.6)',
			'selectors' => [ '{{WRAPPER}} .gf-ba-label, {{WRAPPER}} .gf-lb-ba-label' => 'background: {{VALUE}};' ],
			'condition' => [ 'show_ba_labels' => 'yes' ],
		] );

		$this->add_control( 'ba_label_color', [
			'label'     => 'Label Text Color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#ffffff',
			'selectors' => [ '{{WRAPPER}} .gf-ba-label, {{WRAPPER}} .gf-lb-ba-label' => 'color: {{VALUE}};' ],
			'condition' => [ 'show_ba_labels' => 'yes' ],
		] );

		$this->end_controls_section();
	}

	// ── Render ────────────────────────────────────────────────────────────────

	protected function render() {
		$settings = $this->get_settings_for_display();
		$source   = ! empty( $settings['source'] ) ? $settings['source'] : 'cpt';
		$items    = $source === 'manual'
			? $this->get_manual_items( $settings )
			: $this->get_cpt_items( $settings );

		if ( empty( $items ) ) {
			if ( $source === 'cpt' ) {
				echo '<p class="gf-no-results">No projects yet — add some under <strong>Gallery Filter → Add New Project</strong> in your dashboard.</p>';
			} else {
				echo '<p class="gf-no-results">No items yet — add some in the widget panel.</p>';
			}
			return;
		}

		// Build ordered unique category list (preserving first-seen order)
		$categories = [];
		foreach ( $items as $item ) {
			if ( $item['cat_slug'] === '' ) continue;
			if ( ! isset( $categories[ $item['cat_slug'] ] ) ) {
				$categories[ $item['cat_slug'] ] = $item['category'];
			}
		}

		wp_enqueue_style( 'gallery-filter' );
		wp_enqueue_script( 'gallery-filter' );

		$columns        = intval( $settings['columns'] );
		$columns_tablet = intval( $settings['columns_tablet'] );
		$columns_mobile = intval( $settings['columns_mobile'] );
		$hover_zoom     = $settings['hover_zoom'] === 'yes' ? 'gf-zoom' : '';
		$layout_style   = ! empty( $settings['layout_style'] ) ? $settings['layout_style'] : 'grid';
		$grid_class     = 'gf-grid' . ( $layout_style === 'masonry' ? ' gf-grid--masonry' : '' );
		$filter_style   = ! empty( $settings['filter_style'] ) ? $settings['filter_style'] : 'pills';
		$filter_class   = 'gf-filter' . ( in_array( $filter_style, [ 'outline', 'underline', 'minimal' ], true ) ? ' gf-filter--' . $filter_style : '' );
		$show_desc_card = ! empty( $settings['show_desc_on_card'] ) && $settings['show_desc_on_card'] === 'yes';
		$show_ba_labels = ! isset( $settings['show_ba_labels'] ) || $settings['show_ba_labels'] === 'yes';
		$before_label   = ! empty( $settings['before_label'] ) ? $settings['before_label'] : 'Before';
		$after_label    = ! empty( $settings['after_label'] ) ? $settings['after_label'] : 'After';
		$widget_id      = $this->get_id();
		?>
		<style>
			#gf-<?php echo esc_attr( $widget_id ); ?> .gf-grid {
				--gf-cols-desktop: <?php echo $columns; ?>;
				--gf-cols-tablet:  <?php echo $columns_tablet; ?>;
				--gf-cols-mobile:  <?php echo $columns_mobile; ?>;
			}
		</style>

		<div class="gf-wrapper" id="gf-<?php echo esc_attr( $widget_id ); ?>">

			<?php if ( $settings['show_filter'] === 'yes' && ! empty( $categories ) ) : ?>
			<div class="<?php echo esc_attr( $filter_class ); ?>" role="group" aria-label="Filter gallery">
				<button class="gf-filter-btn is-active" data-filter="*" aria-pressed="true">
					<?php echo esc_html( $settings['all_label'] ); ?>
				</button>
				<?php foreach ( $categories as $slug => $name ) : ?>
				<button class="gf-filter-btn" data-filter="<?php echo esc_attr( $slug ); ?>" aria-pressed="false">
					<?php echo esc_html( $name ); ?>
				</button>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>

			<div class="<?php echo esc_attr( $grid_class ); ?>">
				<?php foreach ( $items as $item ) :
					$images       = $item['images'];
					$img_url      = ! empty( $images[0]['url'] ) ? $images[0]['url'] : '';
					$img_alt      = ! empty( $images[0]['alt'] ) ? $images[0]['alt'] : $item['title'];
					$title        = $item['title'];
					$cat_name     = $item['category'];
					$cat_slug     = $item['cat_slug'];
					$tags         = $item['tags'];
					$location     = $item['location'];
					$description  = $item['description'];
					$link         = $item['link'];
					$is_ba        = ! empty( $item['is_ba'] );
					$before       = $item['before'];
					$after        = $item['after'];
					$img_count    = count( $images );
					$has_gallery  = $is_ba || $img_count > 0;
					$gallery_json = wp_json_encode( $images );
				?>
				<div
					class="gf-card <?php echo esc_attr( $hover_zoom ); ?><?php echo $has_gallery ? ' gf-has-gallery' : ''; ?><?php echo $is_ba ? ' gf-card--ba' : ''; ?>"
					data-categories="<?php echo esc_attr( $cat_slug ); ?>"
					data-title="<?php echo esc_attr( $title ); ?>"
					data-location="<?php echo esc_attr( $location ); ?>"
					data-description="<?php echo esc_attr( $description ); ?>"
					data-gallery="<?php echo esc_attr( $gallery_json ); ?>"
					<?php if ( $is_ba ) : ?>data-ba="1" data-before="<?php echo esc_url( $before ); ?>" data-after="<?php echo esc_url( $after ); ?>"<?php endif; ?>
					<?php if ( $has_gallery ) : ?>role="button" tabindex="0" aria-label="Open <?php echo esc_attr( $title ); ?><?php echo $is_ba ? ' before and after comparison' : ' gallery'; ?>"<?php endif; ?>
				>
					<?php if ( $is_ba ) : ?>
					<div class="gf-ba" aria-hidden="true">
						<img class="gf-ba-img gf-ba-after" src="<?php echo esc_url( $after ); ?>" alt="" loading="lazy" />
						<img class="gf-ba-img gf-ba-before" src="<?php echo esc_url( $before ); ?>" alt="" loading="lazy" />
					</div>
					<?php elseif ( $img_url ) : ?>
					<img
						src="<?php echo esc_url( $img_url ); ?>"
						alt="<?php echo esc_attr( $img_alt ); ?>"
						class="gf-card-img"
						loading="lazy"
					/>
					<?php else : ?>
					<div class="gf-card-no-img"></div>
					<?php endif; ?>

					<div class="gf-card-overlay" aria-hidden="true"></div>

					<?php if ( $cat_name ) : ?>
					<span class="gf-badge"><?php echo esc_html( $cat_name ); ?></span>
					<?php endif; ?>

					<?php if ( $img_count > 1 ) : ?>
					<span class="gf-photo-count" aria-hidden="true">
						<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
						<?php echo $img_count; ?>
					</span>
					<?php endif; ?>

					<div class="gf-card-body">
						<?php if ( $title ) : ?>
						<h3 class="gf-title"><?php echo esc_html( $title ); ?></h3>
						<?php endif; ?>
						<?php if ( $location ) : ?>
						<div class="gf-location"><?php echo $this->get_pin_svg(); ?><span><?php echo esc_html( $location ); ?></span></div>
						<?php endif; ?>
						<?php if ( ! empty( $tags ) ) :
							$shown_tags = array_slice( $tags, 0, 3 );
							$extra_tags = count( $tags ) - count( $shown_tags );
						?>
						<div class="gf-tags" aria-label="Tags">
							<?php foreach ( $shown_tags as $tag ) : ?>
							<span class="gf-tag"><?php echo esc_html( $tag ); ?></span>
							<?php endforeach; ?>
							<?php if ( $extra_tags > 0 ) : ?>
							<span class="gf-tag gf-tag--more">+<?php echo (int) $extra_tags; ?></span>
							<?php endif; ?>
						</div>
						<?php endif; ?>
						<?php if ( $show_desc_card && $description !== '' ) : ?>
						<p class="gf-card-desc"><?php echo esc_html( $description ); ?></p>
						<?php endif; ?>
					</div>

					<?php if ( $link['url'] ) : ?>
					<a
						href="<?php echo esc_url( $link['url'] ); ?>"
						class="gf-arrow"
						target="<?php echo esc_attr( $link['target'] ); ?>"
						<?php if ( $link['rel'] ) echo 'rel="' . esc_attr( $link['rel'] ) . '"'; ?>
						aria-label="Visit <?php echo esc_attr( $title ); ?>"
					><?php echo $this->get_arrow_svg(); ?></a>
					<?php else : ?>
					<span class="gf-arrow" aria-hidden="true"><?php echo $this->get_arrow_svg(); ?></span>
					<?php endif; ?>

					<?php if ( $is_ba ) : ?>
					<?php if ( $show_ba_labels ) : ?>
					<span class="gf-ba-label gf-ba-label--before"><?php echo esc_html( $before_label ); ?></span>
					<span class="gf-ba-label gf-ba-label--after"><?php echo esc_html( $after_label ); ?></span>
					<?php endif; ?>
					<div class="gf-ba-handle" aria-hidden="true"><span class="gf-ba-grip"><?php echo $this->get_ba_grip_svg(); ?></span></div>
					<?php endif; ?>

				</div>
				<?php endforeach; ?>
			</div><!-- .gf-grid -->

			<!-- Lightbox (one per widget) -->
			<div class="gf-lightbox" role="dialog" aria-modal="true" aria-label="Image gallery" hidden>
				<div class="gf-lb-backdrop"></div>
				<button class="gf-lb-close" aria-label="Close gallery">
					<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
				</button>
				<button class="gf-lb-prev" aria-label="Previous image">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
				</button>
				<button class="gf-lb-next" aria-label="Next image">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
				</button>
				<div class="gf-lb-stage">
					<img class="gf-lb-img" src="" alt="" />
					<div class="gf-lb-ba" hidden>
						<img class="gf-lb-ba-img gf-lb-ba-after" src="" alt="" />
						<img class="gf-lb-ba-img gf-lb-ba-before" src="" alt="" />
						<?php if ( $show_ba_labels ) : ?>
						<span class="gf-ba-label gf-lb-ba-label gf-lb-ba-label--before"><?php echo esc_html( $before_label ); ?></span>
						<span class="gf-ba-label gf-lb-ba-label gf-lb-ba-label--after"><?php echo esc_html( $after_label ); ?></span>
						<?php endif; ?>
						<div class="gf-lb-ba-handle" tabindex="0" role="slider" aria-label="Before and after comparison" aria-valuemin="0" aria-valuemax="100" aria-valuenow="50"><span class="gf-ba-grip gf-lb-ba-grip"><?php echo $this->get_ba_grip_svg(); ?></span></div>
					</div>
				</div>
				<div class="gf-lb-footer">
					<div class="gf-lb-text">
						<span class="gf-lb-title"></span>
						<span class="gf-lb-location"></span>
						<p class="gf-lb-desc"></p>
					</div>
					<span class="gf-lb-counter"></span>
				</div>
			</div><!-- .gf-lightbox -->

		</div><!-- .gf-wrapper -->
		<?php
	}

	// ── Item Sources ────────────────────────────────────────────────────────────

	/**
	 * Normalize a link into url / target / rel.
	 */
	private function normalize_link( $url, $is_external, $nofollow = false ) {
		$url = ! empty( $url ) ? $url : '';
		if ( ! $url ) {
			return [ 'url' => '', 'target' => '_self', 'rel' => '' ];
		}
		$target = $is_external ? '_blank' : '_self';
		$rel    = $nofollow ? 'nofollow' : '';
		if ( $is_external ) {
			$rel = trim( 'noopener noreferrer ' . $rel );
		}
		return [ 'url' => $url, 'target' => $target, 'rel' => $rel ];
	}

	/**
	 * Normalized items from the manual repeater.
	 */
	private function get_manual_items( $settings ) {
		$raw   = ! empty( $settings['gallery_items'] ) ? $settings['gallery_items'] : [];
		$items = [];

		foreach ( $raw as $item ) {
			$title = isset( $item['title'] ) ? $item['title'] : '';

			$images = [];
			if ( ! empty( $item['images'] ) ) {
				foreach ( $item['images'] as $img ) {
					if ( empty( $img['url'] ) ) continue;
					$images[] = [
						'url' => $img['url'],
						'alt' => ! empty( $img['alt'] ) ? $img['alt'] : $title,
					];
				}
			}

			$cat      = isset( $item['category'] ) ? trim( $item['category'] ) : '';
			$tags_raw = isset( $item['tags'] ) ? $item['tags'] : [];
			// SELECT2 returns an array; older saved items may be a comma string.
			if ( is_array( $tags_raw ) ) {
				$tags = array_filter( array_map( 'trim', $tags_raw ) );
			} else {
				$tags = $tags_raw !== '' ? array_filter( array_map( 'trim', explode( ',', $tags_raw ) ) ) : [];
			}

			$link = $this->normalize_link(
				! empty( $item['link']['url'] ) ? $item['link']['url'] : '',
				! empty( $item['link']['is_external'] ),
				! empty( $item['link']['nofollow'] )
			);

			$before_url = ! empty( $item['before_image']['url'] ) ? $item['before_image']['url'] : '';
			$after_url  = ! empty( $item['after_image']['url'] ) ? $item['after_image']['url'] : '';

			$items[] = [
				'title'       => $title,
				'category'    => $cat,
				'cat_slug'    => sanitize_title( $cat ),
				'location'    => isset( $item['location'] ) ? $item['location'] : '',
				'tags'        => array_values( $tags ),
				'description' => isset( $item['description'] ) ? $item['description'] : '',
				'link'        => $link,
				'images'      => $images,
				'before'      => $before_url,
				'after'       => $after_url,
				'is_ba'       => ( $before_url !== '' && $after_url !== '' ),
			];
		}

		return $items;
	}

	/**
	 * Normalized items from the gf_project post type.
	 */
	private function get_cpt_items( $settings ) {
		$limit   = isset( $settings['cpt_limit'] ) ? intval( $settings['cpt_limit'] ) : 0;
		$orderby = isset( $settings['cpt_orderby'] ) ? $settings['cpt_orderby'] : 'date';

		$args = [
			'post_type'      => 'gf_project',
			'post_status'    => 'publish',
			'posts_per_page' => $limit > 0 ? $limit : -1,
			'no_found_rows'  => true,
		];

		if ( $orderby === 'title' ) {
			$args['orderby'] = 'title';
			$args['order']   = 'ASC';
		} elseif ( $orderby === 'menu_order' ) {
			$args['orderby'] = [ 'menu_order' => 'ASC', 'date' => 'DESC' ];
		} else {
			$args['orderby'] = 'date';
			$args['order']   = 'DESC';
		}

		$query = new \WP_Query( $args );
		$items = [];

		foreach ( $query->posts as $post ) {
			$pid   = $post->ID;
			$title = get_the_title( $pid );

			// Images: featured image first, then the gallery meta (deduped).
			$ids  = [];
			$feat = get_post_thumbnail_id( $pid );
			if ( $feat ) {
				$ids[] = (int) $feat;
			}
			$gallery = get_post_meta( $pid, '_gf_gallery', true );
			if ( is_array( $gallery ) ) {
				$ids = array_merge( $ids, array_map( 'intval', $gallery ) );
			}

			$images = [];
			$seen   = [];
			foreach ( $ids as $aid ) {
				if ( ! $aid || isset( $seen[ $aid ] ) ) continue;
				$seen[ $aid ] = true;
				$url = wp_get_attachment_image_url( $aid, 'large' );
				if ( ! $url ) continue;
				$alt      = get_post_meta( $aid, '_wp_attachment_image_alt', true );
				$images[] = [
					'url' => $url,
					'alt' => $alt !== '' ? $alt : $title,
				];
			}

			// Category — first assigned term.
			$cat      = '';
			$cat_slug = '';
			$terms    = get_the_terms( $pid, 'gf_category' );
			if ( $terms && ! is_wp_error( $terms ) ) {
				$cat      = $terms[0]->name;
				$cat_slug = $terms[0]->slug;
			}

			$tags_raw = get_post_meta( $pid, '_gf_tags', true );
			$tags     = $tags_raw ? array_filter( array_map( 'trim', explode( ',', $tags_raw ) ) ) : [];

			$link = $this->normalize_link(
				get_post_meta( $pid, '_gf_link', true ),
				get_post_meta( $pid, '_gf_link_target', true ) !== '_self'
			);

			$before_id  = (int) get_post_meta( $pid, '_gf_before', true );
			$after_id   = (int) get_post_meta( $pid, '_gf_after', true );
			$before_url = $before_id ? wp_get_attachment_image_url( $before_id, 'large' ) : '';
			$after_url  = $after_id ? wp_get_attachment_image_url( $after_id, 'large' ) : '';

			$items[] = [
				'title'       => $title,
				'category'    => $cat,
				'cat_slug'    => $cat_slug,
				'location'    => get_post_meta( $pid, '_gf_location', true ),
				'tags'        => array_values( $tags ),
				'description' => trim( wp_strip_all_tags( $post->post_content ) ),
				'link'        => $link,
				'images'      => $images,
				'before'      => $before_url ? $before_url : '',
				'after'       => $after_url ? $after_url : '',
				'is_ba'       => ( $before_url && $after_url ),
			];
		}

		wp_reset_postdata();

		return $items;
	}

	private function get_arrow_svg() {
		return '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>';
	}

	private function get_pin_svg() {
		return '<svg class="gf-pin" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>';
	}

	private function get_ba_grip_svg() {
		return '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 6 4 12 9 18"/><polyline points="15 6 20 12 15 18"/></svg>';
	}
}
