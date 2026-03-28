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
		add_filter( 'manage_gf_project_posts_columns',       [ $this, 'add_admin_columns' ] );
		add_action( 'manage_gf_project_posts_custom_column', [ $this, 'render_admin_columns' ], 10, 2 );
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
			'labels'       => $labels,
			'public'       => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-format-gallery',
			'menu_position'=> 25,
			'supports'     => [ 'title', 'thumbnail' ],
			'has_archive'  => false,
			'rewrite'      => [ 'slug' => 'gallery-project' ],
			'show_in_rest' => true, // Gutenberg / REST compatibility
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
			'rewrite'           => [ 'slug' => 'gallery-category' ],
			'show_in_rest'      => true,
		] );
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
		$link        = get_post_meta( $post->ID, '_gf_link', true );
		$link_target = get_post_meta( $post->ID, '_gf_link_target', true );
		?>
		<style>
			.gf-meta-table { width: 100%; border-collapse: collapse; }
			.gf-meta-table th { width: 160px; text-align: left; padding: 10px 10px 10px 0; font-weight: 600; vertical-align: top; padding-top: 14px; }
			.gf-meta-table td { padding: 8px 0; }
			.gf-meta-table small { color: #777; font-weight: normal; display: block; margin-top: 2px; }
			.gf-meta-table input[type="text"],
			.gf-meta-table input[type="url"] { width: 100%; }
		</style>
		<table class="gf-meta-table">
			<tr>
				<th>
					<label for="gf_tags">Tags</label>
					<small>Shown on card (comma-separated)</small>
				</th>
				<td>
					<input
						type="text"
						id="gf_tags"
						name="gf_tags"
						value="<?php echo esc_attr( $tags ); ?>"
						placeholder="Drainage, Driveway, Residential"
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
			<strong>Tip:</strong> Set a Featured Image above — it becomes the card background photo.
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

		if ( isset( $_POST['gf_tags'] ) ) {
			update_post_meta( $post_id, '_gf_tags', sanitize_text_field( wp_unslash( $_POST['gf_tags'] ) ) );
		}
		if ( isset( $_POST['gf_link'] ) ) {
			update_post_meta( $post_id, '_gf_link', esc_url_raw( wp_unslash( $_POST['gf_link'] ) ) );
		}
		$target = ( isset( $_POST['gf_link_target'] ) && $_POST['gf_link_target'] === '_self' ) ? '_self' : '_blank';
		update_post_meta( $post_id, '_gf_link_target', $target );
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
			echo $thumb ? '<div style="width:60px;height:60px;overflow:hidden;border-radius:4px;">' . wp_kses_post( $thumb ) . '</div>' : '&mdash;';
		}
	}
}

new CPT();
