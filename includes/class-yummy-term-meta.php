<?php
/**
 * Class-yummy-term-meta.php
 *
 * @package yummy-recipes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Yummy_Term_Meta.
 */
class Yummy_Term_Meta {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_term_meta' ) );
		add_action( 'saved_term', array( __CLASS__, 'save_term_meta' ), 100, 4 );
		add_action( 'admin_init', array( __CLASS__, 'add_hooks_for_taxonomies' ) );
		add_action( 'wp_ajax_yummy_get_meta_image_preview', array( __CLASS__, 'ajax_get_meta_image_preview' ) );
		add_action( 'saved_term', array( __CLASS__, 'save_additional_term_meta' ), 100, 4 );
		add_action( 'save_post_yummy_recipe', array( __CLASS__, 'save_post_update_term_image_meta' ) );
	}

	/**
	 * Registers term meta.
	 */
	public static function register_term_meta() {
		register_meta(
			'term',
			'_yummy_term_meta_image_id',
			array(
				'sanitize_callback' => 'absint',
			)
		);
	}

	/**
	 * Adds fields to the taxonomy page's add new term form.
	 */
	public static function add_form_field_term_meta_text() {
		wp_nonce_field( basename( __FILE__ ), 'yummy_nonce_term_meta' );
		?>

		<h2>Yummy</h2>

		<div class="form-field term-meta-text-wrap">
			<label for="yummy-id-term-image"><?php esc_html_e( 'Card Image', 'yummy-recipes' ); ?></label>
			<?php self::yummy_term_meta_output_field_image(); ?>
		</div>
		<?php
	}

	/**
	 * Adds fields to the taxonomy term edit page.
	 *
	 * @param WP_Term $term Term.
	 */
	public static function edit_form_field_term_meta_text( $term ) {

		$image_id = get_term_meta( $term->term_id, '_yummy_term_meta_image_id', true );
		if ( ! $image_id ) {
			$image_id = '';
		}

		wp_nonce_field( basename( __FILE__ ), 'yummy_nonce_term_meta' );
		?>

		<tr class="form-field">
			<th scope="row" colspan="2">
				<h3>Yummy</h3>
			</th>
		</tr>

		<tr class="form-field term-meta-text-wrap">
			<th scope="row"><label for="yummy-id-term-image-id"><?php esc_html_e( 'Card Image', 'yummy-recipes' ); ?></label></th>

			<td>
				<?php self::yummy_term_meta_output_field_image( $image_id ); ?>
			</td>
		</tr>
		<?php
	}

	/**
	 * Outputs term meta field for the add new term and edit term pages.
	 *
	 * @param int $image_id Image attachment ID.
	 */
	public static function yummy_term_meta_output_field_image( $image_id = null ) {

		if ( empty( $image_id ) ) {
			$image_id = '';
		}
		?>
		<input type="hidden" name="yummy_term_custom_image_id" id="yummy-id-term-image-id" class="yummy_image_id" value="<?php echo esc_attr( $image_id ); ?>">
		<a href="#" class="yummy_js_upload_image_button button button-secondary"><?php esc_html_e( 'Add or Upload Image', 'yummy-recipes' ); ?></a>
		<p class="description"><?php esc_html_e( 'Image is used for example in recipe term cards. Optional.', 'yummy-recipes' ); ?></p>

		<div>
			<?php if ( ! empty( $image_id ) ) : ?>
				<?php
				echo wp_get_attachment_image(
					$image_id,
					'medium',
					false,
					array(
						'id'    => 'yummy-term-meta-preview-image',
						'class' => 'yummy_term_meta_preview_image',
					)
				);

				echo wp_kses_post( self::get_remove_file_button( '_yummy_term_meta_image_id' ) );
				?>
			<?php else : ?>
				<?php echo '<img src="" id="yummy-term-meta-preview-image" class="yummy_term_meta_preview_image">'; ?>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Outputs remove file button for meta form field.
	 *
	 * @param string $data_target Data-target.
	 */
	public static function get_remove_file_button( $data_target ) {

		$html = '<p><a href="#" class="yummy-remove-file" data-target="' . esc_attr( $data_target ) . '">' . esc_html__( 'Remove Image', 'yummy-recipes' ) . '</a></p>';

		return $html;
	}

	/**
	 * Saves custom term meta.
	 *
	 * @param int     $term_id  Term ID.
	 * @param int     $tt_id    Term taxonomy ID.
	 * @param string  $taxonomy Taxonomy slug.
	 * @param boolean $update   Whether this is an existing term being updated.
	 */
	public static function save_term_meta( $term_id, $tt_id, $taxonomy, $update ) {

		// Verify the nonce.
		if ( ! isset( $_POST['yummy_nonce_term_meta'] ) || ! wp_verify_nonce( $_POST['yummy_nonce_term_meta'], basename( __FILE__ ) ) ) {
			return;
		}

		$old_value = get_term_meta( $term_id, '_yummy_term_meta_image_id', true );
		$new_value = isset( $_POST['yummy_term_custom_image_id'] ) ? absint( $_POST['yummy_term_custom_image_id'] ) : '';

		if ( $old_value && '' === $new_value ) {
			delete_term_meta( $term_id, '_yummy_term_meta_image_id' );
		} elseif ( $old_value !== $new_value ) {
			update_term_meta( $term_id, '_yummy_term_meta_image_id', $new_value );
		}
	}

	/**
	 * Adds term meta column to the term listing.
	 *
	 * @param array $columns The column header labels keyed by column ID.
	 *
	 * @return array
	 */
	public static function edit_columns( $columns ) {
		$columns['yummy_term_column_image'] = __( 'Card Image', 'yummy-recipes' );

		return $columns;
	}

	/**
	 * Displays term meta in the term listing column.
	 *
	 * @param string $string      Blank string.
	 * @param string $column      Name of the column.
	 * @param int    $term_id     Term ID.
	 */
	public static function manage_custom_column( $string, $column, $term_id ) {
		if ( 'yummy_term_column_image' === $column ) {
			$image_id = get_term_meta( $term_id, '_yummy_term_meta_image_id', true );

			if ( ! empty( $image_id ) ) {
				echo wp_get_attachment_image( $image_id, array( 50, 50 ), false );
			}
		}
	}

	/**
	 * Loops taxonomies to register needed filters and actions.
	 */
	public static function add_hooks_for_taxonomies() {
		$taxonomies = get_object_taxonomies( 'yummy_recipe', 'names' );

		if ( ! empty( $taxonomies ) ) {
			foreach ( $taxonomies as $taxonomy ) {
				add_action( $taxonomy . '_add_form_fields', array( __CLASS__, 'add_form_field_term_meta_text' ) );
				add_action( $taxonomy . '_edit_form_fields', array( __CLASS__, 'edit_form_field_term_meta_text' ) );
				add_filter( 'manage_edit-' . $taxonomy . '_columns', array( __CLASS__, 'edit_columns' ) );
				add_filter( 'manage_' . $taxonomy . '_custom_column', array( __CLASS__, 'manage_custom_column' ), 10, 3 );
			}
		}
	}

	/**
	 * AJAX function to show the selected image after the term meta field.
	 */
	public static function ajax_get_meta_image_preview() {

		$attachment_id    = filter_input( INPUT_GET, 'attachmentId', FILTER_VALIDATE_INT );
		$image_element_id = filter_input( INPUT_GET, 'previewImageElementId', FILTER_SANITIZE_STRING );

		if ( isset( $attachment_id ) && isset( $image_element_id ) ) {
			$image = wp_get_attachment_image(
				$attachment_id,
				'medium',
				false,
				array(
					'id'    => esc_attr( $image_element_id ),
					'class' => 'yummy_term_meta_preview_image',
				)
			);

			$image .= self::get_remove_file_button( '_yummy_term_meta_image_id' );

			$data = array(
				'image' => $image,
			);
			wp_send_json_success( $data );
		} else {
			wp_send_json_error();
		}
	}

	/**
	 * Updates additional term meta fields when saving terms.
	 *
	 * @param int     $term_id  Term ID.
	 * @param int     $tt_id    Term taxonomy ID.
	 * @param string  $taxonomy Taxonomy slug.
	 * @param boolean $update   Whether this is an existing term being updated.
	 */
	public static function save_additional_term_meta( $term_id, $tt_id, $taxonomy, $update ) {

		delete_term_meta( $term_id, '_yummy_term_meta_image_url' );

		$term_image_id = get_term_meta( $term_id, '_yummy_term_meta_image_id', true );

		// If term image meta is not set, get the ID from one of the posts using the term.
		if ( empty( $term_image_id ) ) {
			$term_image_id = self::get_term_image_id_from_post( $term_id, $taxonomy );
		}

		// Check if meta value or image from one of the posts is found.
		if ( is_numeric( $term_image_id ) ) {
			// Get the URL for the image thumbnail, and update the meta field.
			$get_attachment_image = wp_get_attachment_image_src( $term_image_id, 'yummy-320x320' );

			if ( is_array( $get_attachment_image ) ) {
				update_term_meta( $term_id, '_yummy_term_meta_image_url', $get_attachment_image[0] );
			}
		}
	}

	/**
	 * Updates meta fields for term images on post save.
	 *
	 * @param  int $post_id Post ID.
	 */
	public static function save_post_update_term_image_meta( $post_id ) {

		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		$post_taxonomies = get_post_taxonomies( $post_id );

		// Get all terms of the post and check if term has image meta set. If not use the post's image.
		if ( is_array( $post_taxonomies ) ) {
			foreach ( $post_taxonomies as $taxonomy_slug ) {
				$terms = get_the_terms( $post_id, $taxonomy_slug );

				if ( is_array( $terms ) ) {
					foreach ( $terms as $term ) {
						$term_image_id  = get_term_meta( $term->term_id, '_yummy_term_meta_image_id', true );
						$term_image_url = get_term_meta( $term->term_id, '_yummy_term_meta_image_url', true );

						if ( ! is_numeric( $term_image_id ) || empty( $term_image_url ) ) {
							if ( ! has_post_thumbnail( $post_id ) ) {
								delete_term_meta( $term->term_id, '_yummy_term_meta_image_id' );
								delete_term_meta( $term->term_id, '_yummy_term_meta_image_url' );
							} elseif ( has_post_thumbnail( $post_id ) ) {
								$thumbnail_id = get_post_thumbnail_id( $post_id );
								update_term_meta( $term->term_id, '_yummy_term_meta_image_id', $term_image_id );

								$get_attachment_image = wp_get_attachment_image_src( $thumbnail_id, 'yummy-320x320' );

								// Update also term image URL.
								if ( is_array( $get_attachment_image ) ) {
									update_term_meta( $term->term_id, '_yummy_term_meta_image_url', $get_attachment_image[0] );
								}
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Returns the ID of the term image or the thumbnail ID from one of the posts using the term.
	 *
	 * @param int    $term_id       Term ID.
	 * @param string $taxonomy_slug Taxonomy slug.
	 *
	 * @return int Image ID.
	 */
	public static function get_term_image_id_from_post( $term_id, $taxonomy_slug ) {

		if ( empty( $term_id ) || empty( $taxonomy_slug ) ) {
			return false;
		}

		// Get the thumbnail image from one of the recipes.
		$args_recipes = array(
			'post_type'      => 'yummy_recipe',
			'orderby'        => 'date',
			'order'          => 'DESC',
			'fields'         => 'ids',
			'posts_per_page' => 1,
			'tax_query'      => array( // phpcs:ignore slow query ok.
				array(
					'taxonomy' => $taxonomy_slug,
					'terms'    => $term_id,
				),
			),
			'meta_query'     => array( // phpcs:ignore slow query ok.
				array(
					'key' => '_thumbnail_id',
				),
			),
		);

		$args_recipes = apply_filters( 'yummy_filter_get_term_image_id_wp_query_args', $args_recipes );

		$wp_query_recipes = new WP_Query( $args_recipes );

		// If a post is found, save the thumbnail id as term meta.
		if ( $wp_query_recipes->have_posts() ) {
			$term_image_id = get_post_thumbnail_id( $wp_query_recipes->posts[0] );
			update_term_meta( $term->term_id, '_yummy_term_meta_image_id', $term_image_id );

			$get_attachment_image = wp_get_attachment_image_src( $term_image_id, 'yummy-320x320' );

			// Update also term image URL.
			if ( is_array( $get_attachment_image ) ) {
				update_term_meta( $term->term_id, '_yummy_term_meta_image_url', $get_attachment_image[0] );
			}

			wp_reset_postdata();
			return $term_image_id;
		}

		return false;
	}

}

Yummy_Term_Meta::init();
