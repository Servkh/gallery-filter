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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Elementor_Widget extends Widget_Base {

	public function get_name()       { return 'gallery_filter'; }
	public function get_title()      { return 'Gallery Filter'; }
	public function get_icon()       { return 'eicon-gallery-masonry'; }
	public function get_categories() { return [ 'general' ]; }
	public function get_keywords()   { return [ 'gallery', 'filter', 'portfolio', 'projects', 'grid', 'images' ]; }

	// ── Controls ──────────────────────────────────────────────────────────────

	protected function register_controls() {

		/* ────────── CONTENT TAB ────────── */

		/* ── Gallery Items (Repeater) ── */

		$this->start_controls_section( 'section_items', [
			'label' => 'Gallery Items',
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );

		$repeater = new Repeater();

		$repeater->add_control( 'images', [
			'label'       => 'Images',
			'type'        => Controls_Manager::GALLERY,
			'description' => 'First image is used as the card background. Add more for future lightbox support.',
			'default'     => [],
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

		$repeater->add_control( 'tags', [
			'label'       => 'Tags',
			'type'        => Controls_Manager::TEXT,
			'default'     => '',
			'placeholder' => 'e.g. Drainage, Driveway',
			'description' => 'Comma-separated labels shown on the card.',
			'label_block' => true,
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
					'tags'     => 'Drainage, Driveway',
				],
				[
					'images'   => [],
					'title'    => 'Medical Office Parking Lot',
					'category' => 'Commercial',
					'tags'     => 'Resurfacing, Parking Lot',
				],
				[
					'images'   => [],
					'title'    => 'Historic Home Driveway',
					'category' => 'Residential',
					'tags'     => 'Custom Design, Historic Property',
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

		$this->add_control( 'card_height', [
			'label'      => 'Card Height',
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'vh' ],
			'range'      => [ 'px' => [ 'min' => 150, 'max' => 800 ], 'vh' => [ 'min' => 10, 'max' => 80 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 450 ],
			'selectors'  => [ '{{WRAPPER}} .gf-card' => 'height: {{SIZE}}{{UNIT}};' ],
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

		$this->end_controls_section();

		/* ────────── STYLE TAB ────────── */

		/* ── Filter Button Style ── */

		$this->start_controls_section( 'section_filter_style', [
			'label'     => 'Filter Buttons',
			'tab'       => Controls_Manager::TAB_STYLE,
			'condition' => [ 'show_filter' => 'yes' ],
		] );

		$this->add_control( 'filter_gap', [
			'label'     => 'Gap Between Buttons',
			'type'      => Controls_Manager::SLIDER,
			'size_units'=> [ 'px' ],
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
			'default'   => [ 'unit' => 'px', 'size' => 10 ],
			'selectors' => [ '{{WRAPPER}} .gf-filter' => 'gap: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( 'filter_margin_bottom', [
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

		$this->end_controls_section();

		/* ── Card Style ── */

		$this->start_controls_section( 'section_card_style', [
			'label' => 'Cards',
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'card_gap', [
			'label'     => 'Gap Between Cards',
			'type'      => Controls_Manager::SLIDER,
			'size_units'=> [ 'px' ],
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
			'default'   => [ 'unit' => 'px', 'size' => 20 ],
			'selectors' => [ '{{WRAPPER}} .gf-grid' => 'gap: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( 'card_radius', [
			'label'     => 'Border Radius',
			'type'      => Controls_Manager::SLIDER,
			'size_units'=> [ 'px' ],
			'range'     => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
			'default'   => [ 'unit' => 'px', 'size' => 12 ],
			'selectors' => [ '{{WRAPPER}} .gf-card' => 'border-radius: {{SIZE}}{{UNIT}};' ],
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
			'default'   => 'rgba(0,0,0,0.55)',
			'selectors' => [ '{{WRAPPER}} .gf-card-overlay' => 'background: linear-gradient(to top, {{VALUE}} 0%, transparent 60%);' ],
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
	}

	// ── Render ────────────────────────────────────────────────────────────────

	protected function render() {
		$settings = $this->get_settings_for_display();
		$items    = $settings['gallery_items'];

		if ( empty( $items ) ) {
			echo '<p class="gf-no-results">No items yet — add some in the widget panel.</p>';
			return;
		}

		// Build ordered unique category list (preserving first-seen order)
		$categories = [];
		foreach ( $items as $item ) {
			$cat = trim( $item['category'] );
			if ( $cat === '' ) continue;
			$slug = sanitize_title( $cat );
			if ( ! isset( $categories[ $slug ] ) ) {
				$categories[ $slug ] = $cat;
			}
		}

		wp_enqueue_style( 'gallery-filter' );
		wp_enqueue_script( 'gallery-filter' );

		$columns        = intval( $settings['columns'] );
		$columns_tablet = intval( $settings['columns_tablet'] );
		$columns_mobile = intval( $settings['columns_mobile'] );
		$hover_zoom     = $settings['hover_zoom'] === 'yes' ? 'gf-zoom' : '';
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
			<div class="gf-filter" role="group" aria-label="Filter gallery">
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

			<div class="gf-grid">
				<?php foreach ( $items as $item ) :
					$images      = ! empty( $item['images'] ) ? $item['images'] : [];
					$first_img   = ! empty( $images[0] ) ? $images[0] : [];
					$img_url     = ! empty( $first_img['url'] ) ? $first_img['url'] : '';
					$img_alt     = ! empty( $first_img['alt'] ) ? $first_img['alt'] : $item['title'];
					$title       = $item['title'];
					$cat_name    = trim( $item['category'] );
					$cat_slug    = sanitize_title( $cat_name );
					$tags_raw    = $item['tags'];
					$tags        = ! empty( $tags_raw ) ? array_filter( array_map( 'trim', explode( ',', $tags_raw ) ) ) : [];
					$link_url    = ! empty( $item['link']['url'] ) ? $item['link']['url'] : '';
					$is_external = ! empty( $item['link']['is_external'] );
					$nofollow    = ! empty( $item['link']['nofollow'] );
					$img_count   = count( $images );

					// Build gallery JSON for the lightbox
					$gallery_data = [];
					foreach ( $images as $img ) {
						if ( empty( $img['url'] ) ) continue;
						$gallery_data[] = [
							'url' => $img['url'],
							'alt' => ! empty( $img['alt'] ) ? $img['alt'] : $title,
						];
					}
					$gallery_json = wp_json_encode( $gallery_data );
				?>
				<div
					class="gf-card <?php echo esc_attr( $hover_zoom ); ?><?php echo $img_count > 0 ? ' gf-has-gallery' : ''; ?>"
					data-categories="<?php echo esc_attr( $cat_slug ); ?>"
					data-title="<?php echo esc_attr( $title ); ?>"
					data-gallery="<?php echo esc_attr( $gallery_json ); ?>"
					role="button"
					tabindex="0"
					aria-label="Open <?php echo esc_attr( $title ); ?> gallery"
				>
					<?php if ( $img_url ) : ?>
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
						<?php if ( ! empty( $tags ) ) : ?>
						<div class="gf-tags" aria-label="Tags">
							<?php foreach ( $tags as $tag ) : ?>
							<span class="gf-tag"><?php echo esc_html( $tag ); ?></span>
							<?php endforeach; ?>
						</div>
						<?php endif; ?>
					</div>

					<?php if ( $link_url ) :
						$target = $is_external ? '_blank' : '_self';
						$rel    = $nofollow ? 'nofollow' : '';
						if ( $is_external ) $rel = trim( 'noopener noreferrer ' . $rel );
					?>
					<a
						href="<?php echo esc_url( $link_url ); ?>"
						class="gf-arrow"
						target="<?php echo esc_attr( $target ); ?>"
						<?php if ( $rel ) echo 'rel="' . esc_attr( $rel ) . '"'; ?>
						aria-label="Visit <?php echo esc_attr( $title ); ?>"
					><?php echo $this->get_arrow_svg(); ?></a>
					<?php else : ?>
					<span class="gf-arrow" aria-hidden="true"><?php echo $this->get_arrow_svg(); ?></span>
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
				</div>
				<div class="gf-lb-footer">
					<span class="gf-lb-title"></span>
					<span class="gf-lb-counter"></span>
				</div>
			</div><!-- .gf-lightbox -->

		</div><!-- .gf-wrapper -->
		<?php
	}

	private function get_arrow_svg() {
		return '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>';
	}
}
