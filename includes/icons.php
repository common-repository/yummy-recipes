<?php
/**
 * Icons.php
 *
 * @package yummy-recipes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Outputs SVG icon inline.
 * If the icons does not exist, falls back to a default icon.
 *
 * @param string $icon     Icon slug.
 * @param string $classes  CSS classes.
 */
function yummy_inline_svg( $icon = 'fallback', $classes = null ) {

	$classes = 'yummy-icon yummy-icon-' . sanitize_html_class( $icon ) . ' ' . $classes;

	$icon_path = YUMMY_DIR_PATH . '/assets/images/' . $icon . '.svg';

	// Use the fallback icon, if the icon is not found.
	if ( ! file_exists( $icon_path ) ) {
		$icon_path = YUMMY_DIR_PATH . '/assets/images/fallback.svg';
	}

	if ( file_exists( $icon_path ) ) {
		ob_start();
		include $icon_path;
		$file_contents = ob_get_clean();

		$file_contents = str_replace( '<svg ', '<svg class="' . $classes . '" ', $file_contents );

		echo wp_kses( apply_filters( 'yummy_filter_inline_svg', $file_contents ), yummy_get_wp_kses_allowed_html( 'svg_icon' ) );
	}
}

/**
 * Returns HTML for inline SVG icon.
 *
 * @param string $icon     Icon slug.
 * @param string $classes  CSS classes.
 */
function yummy_get_inline_svg( $icon = 'fallback', $classes = 'yummy-icon' ) {
	ob_start();
	yummy_inline_svg( $icon, $classes );
	return ob_get_clean();
}
