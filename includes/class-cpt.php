<?php
/**
 * Registers the Gallery Project custom post type and Gallery Category taxonomy.
 *
 * @package GalleryFilter
 */

namespace GalleryFilter;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CPT {

	public function __construct() {
		add_action( 'init',             [ $this, 'register_post_type' ] );
		add_action( 'init',             [ $this, 'register_taxonomy' ] );
		add_action( 'add_meta_boxes',   [ $this, 'add_meta_boxes' ] );
		add_action( 'save_post',        [ $this, 'save_meta' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
		add_action( 'admin_menu',           [ $this, 'add_settings_page' ] );
		add_action( 'admin_init',           [ $this, 'register_settings' ] );
		add_filter( 'manage_gf_project_posts_columns',       [ $this, 'add_admin_columns' ] );
		add_action( 'manage_gf_project_posts_custom_column', [ $this, 'render_admin_columns' ], 10, 2 );
	}

	// ── Settings (tag vocabulary) ───────────────────────────────────────────────

	public function add_settings_page() {
		add_submenu_page(
			'edit.php?post_type=gf_project',
			'Gallery Filter Settings',
			'Settings',
			'manage_options',
			'gf-settings',
			[ $this, 'render_settings_page' ]
		);
	}

	public function register_settings() {
		register_setting( 'gf_settings_group', 'gf_gallery_tags', [
			'type'              => 'string',
			'sanitize_callback' => [ $this, 'sanitize_tags_option' ],
			'default'           => '',
		] );
	}

	public function sanitize_tags_option( $value ) {
		$lines = preg_split( '/[\r\n]+/', (string) $value );
		$clean = [];
		foreach ( $lines as $line ) {
			$line = sanitize_text_field( trim( $line ) );
			if ( $line !== '' && ! in_array( $line, $clean, true ) ) {
				$clean[] = $line;
			}
		}
		return implode( "\n", $clean );
	}

	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Pre-fill the box with the current list (defaults until a custom list is saved).
		$stored = get_option( 'gf_gallery_tags', '' );
		$value  = ( is_string( $stored ) && trim( $stored ) !== '' )
			? $stored
			: implode( "\n", gf_default_tag_options() );
		?>
		<div class="wrap">
			<h1>Gallery Filter — Settings</h1>
			<form method="post" action="options.php">
				<?php settings_fields( 'gf_settings_group' ); ?>
				<h2>Tags</h2>
				<p>These tags appear as checkboxes on each project and as the multi-select in the Elementor widget. <strong>One tag per line.</strong> Reorder them here to change the order they appear in.</p>
				<textarea name="gf_gallery_tags" rows="16" cols="50" style="width:420px;max-width:100%;font-family:monospace;"><?php echo esc_textarea( $value ); ?></textarea>
				<p class="description">Leave empty and save to restore the built-in default list.</p>
				<?php submit_button( 'Save Tags' ); ?>
			</form>
		</div>
		<?php
	}

	// ── Post Type ─────────────────────────────────────────────────────────────

	public function register_post_type() {
		$labels = [
			'name'               => 'Gallery Projects',
			'singular_name'      => 'Gallery Project',
			'add_new'            => 'Add New Project',
			'add_new_item'       => 'Add New Gallery Project',
			'edit_item'          => 'Edit Gallery Project',
			'new_item'           => 'New Gallery Project',
			'view_item'          => 'View Gallery Project',
			'search_items'       => 'Search Gallery Projects',
			'not_found'          => 'No projects found.',
			'not_found_in_trash' => 'No projects found in Trash.',
			'menu_name'          => 'Gallery Filter',
			'all_items'          => 'All Projects',
		];

		register_post_type( 'gf_project', [
			'labels'            => $labels,
			'public'            => false,
			'show_ui'           => true,
			'publicly_queryable'=> false,
			'exclude_from_search' => true,
			'show_in_menu'      => true,
			'menu_icon'         => 'dashicons-format-gallery',
			'menu_position'     => 25,
			'supports'          => [ 'title', 'editor', 'thumbnail', 'page-attributes' ],
			'has_archive'       => false,
			'rewrite'           => false,
			'show_in_rest'      => true, // Gutenberg / REST compatibility
		] );
	}

	// ── Taxonomy ──────────────────────────────────────────────────────────────

	public function register_taxonomy() {
		$labels = [
			'name'              => 'Gallery Categories',
			'singular_name'     => 'Gallery Category',
			'search_items'      => 'Search Categories',
			'all_items'         => 'All Categories',
			'edit_item'         => 'Edit Category',
			'update_item'       => 'Update Category',
			'add_new_item'      => 'Add New Category',
			'new_item_name'     => 'New Category Name',
			'menu_name'         => 'Categories',
		];

		register_taxonomy( 'gf_category', 'gf_project', [
			'labels'            => $labels,
			'hierarchical'      => false,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'publicly_queryable'=> false,
			'rewrite'           => false,
			'show_in_rest'      => true,
		] );
	}

	// ── Admin Assets ──────────────────────────────────────────────────────────

	public function enqueue_admin_assets( $hook ) {
		if ( $hook !== 'post.php' && $hook !== 'post-new.php' ) {
			return;
		}
		$screen = get_current_screen();
		if ( ! $screen || $screen->post_type !== 'gf_project' ) {
			return;
		}
		wp_enqueue_media();
		wp_enqueue_script(
			'gf-admin',
			GF_PLUGIN_URL . 'assets/js/admin.js',
			[ 'jquery' ],
			GF_VERSION,
			true
		);
	}

	// ── Meta Boxes ────────────────────────────────────────────────────────────

	public function add_meta_boxes() {
		add_meta_box(
			'gf_project_details',
			'Project Details',
			[ $this, 'render_meta_box' ],
			'gf_project',
			'normal',
			'high'
		);
	}

	public function render_meta_box( $post ) {
		wp_nonce_field( 'gf_save_meta', 'gf_meta_nonce' );

		$tags        = get_post_meta( $post->ID, '_gf_tags', true );
		$location    = get_post_meta( $post->ID, '_gf_location', true );
		$link        = get_post_meta( $post->ID, '_gf_link', true );
		$link_target = get_post_meta( $post->ID, '_gf_link_target', true );

		$gallery = get_post_meta( $post->ID, '_gf_gallery', true );
		$gallery = is_array( $gallery ) ? array_filter( array_map( 'intval', $gallery ) ) : [];

		$before_id = (int) get_post_meta( $post->ID, '_gf_before', true );
		$after_id  = (int) get_post_meta( $post->ID, '_gf_after', true );
		?>
		<style>
			.gf-meta-table { width: 100%; border-collapse: collapse; }
			.gf-meta-table th { width: 160px; text-align: left; padding: 10px 10px 10px 0; font-weight: 600; vertical-align: top; padding-top: 14px; }
			.gf-meta-table td { padding: 8px 0; }
			.gf-meta-table small { color: #777; font-weight: normal; display: block; margin-top: 2px; }
			.gf-meta-table input[type="text"],
			.gf-meta-table input[type="url"] { width: 100%; }
			.gf-gallery-field { margin: 4px 0 18px; }
			.gf-gallery-preview { display: flex; flex-wrap: wrap; gap: 8px; margin: 0 0 10px; padding: 0; list-style: none; }
			.gf-gallery-preview:empty { display: none; }
			.gf-gallery-preview li { position: relative; width: 80px; height: 80px; border-radius: 4px; overflow: hidden; box-shadow: 0 0 0 1px rgba(0,0,0,0.1); }
			.gf-gallery-preview li img { width: 100%; height: 100%; object-fit: cover; display: block; }
			.gf-gallery-remove { position: absolute; top: 2px; right: 2px; width: 20px; height: 20px; line-height: 18px; text-align: center; padding: 0; border: none; border-radius: 50%; background: rgba(0,0,0,0.65); color: #fff; font-size: 15px; cursor: pointer; }
			.gf-gallery-remove:hover { background: #b32d2e; }
			.gf-gallery-empty { color: #777; margin: 0 0 10px; font-style: italic; }
			.gf-tag-checklist { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 6px 16px; margin: 0 0 8px; }
			.gf-tag-checklist label { display: flex; align-items: center; gap: 7px; padding: 4px 0; cursor: pointer; }
			.gf-tag-checklist label span { line-height: 1.2; }
			.gf-ba-fields { display: flex; flex-wrap: wrap; gap: 20px; margin: 4px 0 18px; }
			.gf-single-field { flex: 1; min-width: 200px; }
			.gf-single-field > strong { display: block; margin-bottom: 6px; }
			.gf-single-preview { width: 120px; height: 90px; border-radius: 4px; overflow: hidden; background: #f0f0f1; box-shadow: 0 0 0 1px rgba(0,0,0,0.1); margin-bottom: 8px; display: none; }
			.gf-single-preview img { width: 100%; height: 100%; object-fit: cover; display: block; }
			.gf-single-preview.has-image { display: block; }
		</style>

		<p style="margin:0 0 4px;font-weight:600;">Before / After Slider
			<small style="color:#777;font-weight:normal;display:block;">Set <strong>both</strong> a Before and an After image to show a draggable comparison slider on the card. Any Gallery Images above still open in the lightbox after the comparison. Leave both empty to use only the gallery.</small>
		</p>
		<div class="gf-ba-fields">
			<div class="gf-single-field" data-meta="gf_before">
				<strong>Before Image</strong>
				<div class="gf-single-preview<?php echo $before_id ? ' has-image' : ''; ?>">
					<?php if ( $before_id ) echo wp_get_attachment_image( $before_id, [ 120, 90 ] ); ?>
				</div>
				<input type="hidden" name="gf_before" class="gf-single-id" value="<?php echo esc_attr( $before_id ?: '' ); ?>" />
				<button type="button" class="button gf-single-add">Select Before</button>
				<button type="button" class="button-link gf-single-remove" style="margin-left:6px;color:#b32d2e;<?php echo $before_id ? '' : 'display:none;'; ?>">Remove</button>
			</div>
			<div class="gf-single-field" data-meta="gf_after">
				<strong>After Image</strong>
				<div class="gf-single-preview<?php echo $after_id ? ' has-image' : ''; ?>">
					<?php if ( $after_id ) echo wp_get_attachment_image( $after_id, [ 120, 90 ] ); ?>
				</div>
				<input type="hidden" name="gf_after" class="gf-single-id" value="<?php echo esc_attr( $after_id ?: '' ); ?>" />
				<button type="button" class="button gf-single-add">Select After</button>
				<button type="button" class="button-link gf-single-remove" style="margin-left:6px;color:#b32d2e;<?php echo $after_id ? '' : 'display:none;'; ?>">Remove</button>
			</div>
		</div>

		<p style="margin:0 0 4px;font-weight:600;">Gallery Images
			<small style="color:#777;font-weight:normal;display:block;">Before / after and any extra photos. Shown in the lightbox. Set a <strong>Featured Image</strong> (right sidebar) to use as the card cover — it is added first in the lightbox automatically.</small>
		</p>
		<div class="gf-gallery-field">
			<ul class="gf-gallery-preview">
				<?php foreach ( $gallery as $att_id ) :
					$thumb = wp_get_attachment_image( $att_id, [ 80, 80 ] );
					if ( ! $thumb ) continue;
					?>
					<li data-id="<?php echo esc_attr( $att_id ); ?>">
						<?php echo $thumb; ?>
						<button type="button" class="gf-gallery-remove" aria-label="Remove image">&times;</button>
					</li>
				<?php endforeach; ?>
			</ul>
			<input type="hidden" name="gf_gallery" class="gf-gallery-ids" value="<?php echo esc_attr( implode( ',', $gallery ) ); ?>" />
			<button type="button" class="button gf-gallery-add">Add / Edit Images</button>
			<button type="button" class="button-link gf-gallery-clear" style="margin-left:8px;color:#b32d2e;">Clear all</button>
		</div>

		<?php $selected_tags = $tags !== '' ? array_map( 'trim', explode( ',', $tags ) ) : []; ?>
		<p style="margin:16px 0 4px;font-weight:600;">Tags
			<small style="color:#777;font-weight:normal;display:block;">Select the labels to show on the card (the first three appear, with a “+N” for the rest).</small>
		</p>
		<div class="gf-tag-checklist">
			<?php foreach ( gf_tag_options() as $tag ) : ?>
			<label>
				<input type="checkbox" name="gf_tags[]" value="<?php echo esc_attr( $tag ); ?>" <?php checked( in_array( $tag, $selected_tags, true ) ); ?> />
				<span><?php echo esc_html( $tag ); ?></span>
			</label>
			<?php endforeach; ?>
		</div>

		<table class="gf-meta-table">
			<tr>
				<th>
					<label for="gf_location">Location</label>
					<small>Optional — e.g. Lebanon County</small>
				</th>
				<td>
					<input
						type="text"
						id="gf_location"
						name="gf_location"
						value="<?php echo esc_attr( $location ); ?>"
						placeholder="Lebanon County"
					/>
				</td>
			</tr>
			<tr>
				<th>
					<label for="gf_link">Link URL</label>
					<small>Optional — makes arrow clickable</small>
				</th>
				<td>
					<input
						type="url"
						id="gf_link"
						name="gf_link"
						value="<?php echo esc_url( $link ); ?>"
						placeholder="https://example.com/project"
					/>
				</td>
			</tr>
			<tr>
				<th><label>Open Link In</label></th>
				<td>
					<label style="margin-right:16px;">
						<input type="radio" name="gf_link_target" value="_self" <?php checked( $link_target, '_self' ); ?> />
						Same tab
					</label>
					<label>
						<input type="radio" name="gf_link_target" value="_blank" <?php checked( $link_target !== '_self' ); ?> />
						New tab
					</label>
				</td>
			</tr>
		</table>
		<p style="margin-top:14px;color:#777;font-size:12px;">
			<strong>Tip:</strong> The main editor above is the project <strong>Description</strong> — it appears in the lightbox.
			Assign a <a href="edit-tags.php?taxonomy=gf_category&post_type=gf_project">Gallery Category</a> to enable filtering.
		</p>
		<?php
	}

	public function save_meta( $post_id ) {
		if ( ! isset( $_POST['gf_meta_nonce'] ) || ! wp_verify_nonce( $_POST['gf_meta_nonce'], 'gf_save_meta' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Tags: a checkbox list, kept only if in the allowed vocabulary, stored
		// as a comma-separated string for the widget renderer.
		$allowed  = gf_tag_options();
		$selected = isset( $_POST['gf_tags'] ) ? (array) wp_unslash( $_POST['gf_tags'] ) : [];
		$selected = array_values( array_intersect(
			array_map( 'sanitize_text_field', $selected ),
			$allowed
		) );
		update_post_meta( $post_id, '_gf_tags', implode( ', ', $selected ) );
		if ( isset( $_POST['gf_location'] ) ) {
			update_post_meta( $post_id, '_gf_location', sanitize_text_field( wp_unslash( $_POST['gf_location'] ) ) );
		}
		if ( isset( $_POST['gf_link'] ) ) {
			update_post_meta( $post_id, '_gf_link', esc_url_raw( wp_unslash( $_POST['gf_link'] ) ) );
		}
		$target = ( isset( $_POST['gf_link_target'] ) && $_POST['gf_link_target'] === '_self' ) ? '_self' : '_blank';
		update_post_meta( $post_id, '_gf_link_target', $target );

		if ( isset( $_POST['gf_gallery'] ) ) {
			$ids = array_values( array_filter( array_map(
				'intval',
				explode( ',', sanitize_text_field( wp_unslash( $_POST['gf_gallery'] ) ) )
			) ) );
			update_post_meta( $post_id, '_gf_gallery', $ids );
		}

		foreach ( [ 'gf_before' => '_gf_before', 'gf_after' => '_gf_after' ] as $field => $meta ) {
			if ( isset( $_POST[ $field ] ) ) {
				$id = intval( $_POST[ $field ] );
				if ( $id > 0 ) {
					update_post_meta( $post_id, $meta, $id );
				} else {
					delete_post_meta( $post_id, $meta );
				}
			}
		}
	}

	// ── Admin Columns ─────────────────────────────────────────────────────────

	public function add_admin_columns( $columns ) {
		$new = [];
		foreach ( $columns as $key => $label ) {
			$new[ $key ] = $label;
			if ( $key === 'title' ) {
				$new['gf_thumb'] = 'Photo';
			}
		}
		return $new;
	}

	public function render_admin_columns( $column, $post_id ) {
		if ( $column === 'gf_thumb' ) {
			$thumb = get_the_post_thumbnail( $post_id, [ 60, 60 ] );
			echo $thumb ? '<div style="width:60px;height:60px;overflow:hidden;border-radius:4px;">' . $thumb . '</div>' : '—';
		}
	}
}

new CPT();
