<?php
/**
 * Template-functions.php
 *
 * @package yummy-recipes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Returns template part.
 *
 * @param  string $template_name Template name.
 * @param  array  $args          Arguments. (default: array).
 * @param  string $template_path Template path. (default: '').
 * @param  string $default_path  [description].
 */
function yummy_get_template_part( $template_name, $args = array(), $template_path = '', $default_path = '' ) {

	// Setup possible parts.
	if ( false === strpos( $template_name, '.php' ) ) {
		$template_name = $template_name . '.php';
	}

	$template = yummy_locate_template( $template_name );

	if ( ! empty( $args ) && is_array( $args ) ) {
		extract( $args ); // @codingStandardsIgnoreLine
	}

	do_action( 'yummy_action_before_template_part', $template_name, $template, $args );

	// Return the part that is found.
	include $template;

	do_action( 'yummy_action_after_template_part', $template_name, $template, $args );
}

/**
 * Like yummy_get_template_part, but returns the HTML instead of outputting.
 *
 * @param string $template_name Template name.
 * @param array  $args          Arguments. (default: array).
 * @param string $template_path Template path. (default: '').
 * @param string $default_path  Default path. (default: '').
 *
 * @return string
 */
function yummy_get_template_part_html( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	ob_start();
	yummy_get_template_part( $template_name, $args, $template_path, $default_path );
	return ob_get_clean();
}

/**
 * Locate a template and return the path for inclusion.
 *
 * @param  string $template_name  Template name.
 * @param  string $template_path  Template path. (default: '').
 * @param  string $default_path   Default path. (default: '').
 *
 * @return string
 */
function yummy_locate_template( $template_name, $template_path = '', $default_path = '' ) {

	// No file found yet.
	$template = false;

	// Continue if template name is empty.
	if ( empty( $template_name ) ) {
		return;
	}

	// Trim off any slashes from the template name.
	$template_name = ltrim( $template_name, '/' );

	// 1. Check child theme.
	// 2. Check parent theme.
	// 3. Check plugin.
	if ( file_exists( trailingslashit( get_stylesheet_directory() ) . 'yummy/' . $template_name ) ) {
		$template = trailingslashit( get_stylesheet_directory() ) . 'yummy/' . $template_name;

	} elseif ( file_exists( trailingslashit( get_template_directory() ) . 'yummy/' . $template_name ) ) {
		$template = trailingslashit( get_template_directory() ) . 'yummy/' . $template_name;

	} elseif ( file_exists( trailingslashit( YUMMY_DIR_PATH ) . 'templates/' . $template_name ) ) {
		$template = trailingslashit( YUMMY_DIR_PATH ) . 'templates/' . $template_name;
	}

	return apply_filters( 'yummy_filter_locate_template', $template, $template_name, $template_path );
}
