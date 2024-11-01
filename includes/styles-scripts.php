<?php
/**
 * Styles-scripts.php
 *
 * @package yummy-recipes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueues and registers front-end styles and scripts.
 */
function yummy_enqueue_styles_scripts() {

	wp_enqueue_style( 'yummy-style', yummy_get_css_url( 'yummy.css' ), '', YUMMY_PLUGIN_VERSION, 'screen' );
	$custom_css = yummy_get_inline_style();
	wp_add_inline_style( 'yummy-style', $custom_css );
}
add_action( 'wp_enqueue_scripts', 'yummy_enqueue_styles_scripts' );

/**
 * Enqueues admin styles and scripts.
 *
 * @param string $hook_suffix The current admin page.
 */
function yummy_admin_enqueue_styles_scripts( $hook_suffix ) {

	$screen = get_current_screen();

	// Return if the post type is not the right one.
	if ( null === $screen || 'yummy_recipe' !== $screen->post_type ) {
		return;
	}

	// If we are on the custom post edit or add new page.
	if ( 'post-new.php' === $hook_suffix || 'post.php' === $hook_suffix ) {
		wp_enqueue_style( 'yummy-admin-style', YUMMY_BASE_PLUGIN_URL . '/assets/css/admin.css', '', YUMMY_PLUGIN_VERSION );
	}

	// If we are on the term page or term edit page.
	if ( 'edit-tags.php' === $hook_suffix || 'term.php' === $hook_suffix ) {
		if ( ! did_action( 'wp_enqueue_media' ) ) {
			wp_enqueue_media();
		}

		// Enqueues script for the term image upload and preview.
		wp_enqueue_script( 'yummy-admin-meta-image-preview', YUMMY_BASE_PLUGIN_URL . '/assets/js/admin-meta-image-preview.js', array( 'jquery' ), YUMMY_PLUGIN_VERSION, false );
	}
}
add_action( 'admin_enqueue_scripts', 'yummy_admin_enqueue_styles_scripts', 10, 1 );

/**
 * Returns URL for a CSS file. Allows overriding default CSS files. Allows also using filter to change the URL.
 *
 * @param string $filename CSS filename.
 *
 * @return string
 */
function yummy_get_css_url( $filename ) {

	if ( file_exists( get_stylesheet_directory() . '/yummy/' . $filename ) ) {
		$url = get_stylesheet_directory_uri() . '/yummy/' . $filename;
	} elseif ( file_exists( get_template_directory() . '/yummy/' . $filename ) ) {
		$url = get_template_directory() . '/yummy/' . $filename;
	} else {
		$url = YUMMY_BASE_PLUGIN_URL . '/assets/css/' . $filename;
	}

	$url = apply_filters( 'yummy_filter_css_file_url', $url, $filename );

	return $url;
}

/**
 * Builds inline CSS style added as to the head.
 */
function yummy_get_inline_style() {

	$styles = array(
		'yummy_color_big_card_background'          => array(
			'.yummy-big-card' => 'background-color',
		),
		'yummy_color_card_border'                  => array(
			'.yummy-big-card-style-classic' => 'border-color',
			'.yummy-big-card-style-classic button.yummy-change-servings' => 'border-color',
			'.yummy-big-card-style-classic .yummy-nutrition-facts' => 'border-color',
			'.yummy-big-card-style-classic .yummy-big-card-top-content' => 'border-top-color',
			'.yummy-big-card-style-classic .yummy-big-card-top' => 'border-bottom-color',
			'.yummy-big-card-style-classic .yummy-big-card-description' => 'border-bottom-color',
			'.yummy-big-card-style-classic .yummy-big-card-ingredients' => 'border-bottom-color',
			'.yummy-big-card-style-classic .yummy-nutrition-facts-header' => 'border-bottom-color',
			'.yummy-big-card-style-classic .yummy-nutrition-facts-row' => 'border-bottom-color',
			'.yummy-big-card-style-classic .yummy-big-card-buttons' => 'border-top-color',
			'.yummy-big-card-style-classic .yummy-big-card-buttons-print-bookmark' => 'border-left-color',
			'.yummy-small-card-style-classic' => 'border-color',
			'.yummy-small-card-style-classic .yummy-small-card-content' => 'border-top-color',
			'.yummy-small-card-style-classic .yummy-small-card-author' => 'border-top-color',
		),
		'yummy_color_big_card_top'                 => array(
			'.yummy-big-card-top' => 'background-color',
			'.yummy-big-card-style-hero .yummy-big-card-top-content' => 'background-color',
			'.yummy-big-card-style-modern .yummy-big-card-top' => 'background-color',
		),
		'yummy_color_big_card_top_image_border'    => array(
			'.yummy-big-card-image img' => 'border-color',
		),
		'yummy_color_stars'                        => array(
			'svg.yummy-icon-stars'                 => 'fill',
			'input.yummy-input-radio-star + label' => 'color',
		),
		'yummy_color_instructions_step_background' => array(
			'.yummy-instructions ol li:before' => 'background-color',
		),
		'yummy_color_small_card_background'        => array(
			'.yummy-small-card-style-modern' => 'background-color',
		),
		'yummy_color_social_icons'                 => array(
			'.yummy-icon-button-social' => 'background-color',
		),
		'yummy_color_big_card_top_link'            => array(
			'.yummy-big-card-top a'       => 'color',
			'.yummy-big-card-top a:hover' => 'color',
		),
	);

	if ( 'hero' === get_option( 'yummy_big_card_style' ) ) {
		unset( $styles['yummy_color_big_card_top']['.yummy-big-card-top'] );
	}

	if ( 'classic' !== get_option( 'yummy_big_card_style' ) && 'classic' !== get_option( 'yummy_small_card_style' ) ) {
		unset( $styles['yummy_color_card_border'] );
	}

	ob_start();
	if ( 'classic' === get_option( 'yummy_big_card_style' ) ) {
		?>
		@media screen and (min-width:400px) {
			.yummy-big-card-style-classic .yummy-big-card-top-content {
				border-left-color: <?php echo esc_attr( get_option( 'yummy_color_card_border' ) ); ?>;
			}
		}
		<?php
	}

	foreach ( $styles as $option_name => $selectors ) {
		$value = get_option( $option_name );
		if ( ! empty( $value ) ) {
			foreach ( $styles[ $option_name ] as $selector => $property ) {
				echo esc_attr( $selector ) . '{' . esc_attr( $property ) . ':' . esc_attr( $value ) . '}';
			}
		}
	}
	$css = ob_get_clean();

	return preg_replace( '/\s+/S', ' ', $css );
}
