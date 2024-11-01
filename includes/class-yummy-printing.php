<?php
/**
 * Class-yummy-printing.php
 *
 * @package yummy-recipes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Yummy_Printing.
 */
class Yummy_Printing {

	/**
	 * Print URL slug.
	 *
	 * @var string
	 */
	private static $print_url_slug;

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'add_print_url_endpoint' ) );
		add_action( 'template_redirect', array( __CLASS__, 'render_print_template' ) );
		add_action( 'yummy_action_print_template_head', array( __CLASS__, 'enqueue_print_css' ) );

		$permalink_print_endpoint = get_option( 'yummy_permalink_print_endpoint' );

		self::$print_url_slug = ( ! empty( $permalink_print_endpoint ) ? $permalink_print_endpoint : 'print' );
	}

	/**
	 * Returns print URL slug.
	 */
	public static function get_print_url_slug() {
		return self::$print_url_slug;
	}

	/**
	 * Adds URL endpoint for print. Also registers a query var.
	 */
	public static function add_print_url_endpoint() {
		add_rewrite_endpoint( self::$print_url_slug, EP_PERMALINK | EP_PAGES );
	}

	/**
	 * Redirects to the custom print template when going to the print page.
	 *
	 * @return void
	 */
	public static function render_print_template() {

		global $wp_query;

		if ( array_key_exists( self::$print_url_slug, $wp_query->query_vars ) ) {

			$recipe = yummy_get_recipe_object( get_the_ID() );

			// Make sure that the recipe object was set up correctly.
			if ( false === $recipe ) {
				return;
			}

			// Turn on output buffering.
			ob_start();

			yummy_get_template_part( 'template-print', array( 'recipe' => $recipe ) );
			exit();
		}
	}

	/**
	 * Adds print CSS file to the print template head.
	 */
	public static function enqueue_print_css() {

		$css_url = yummy_get_css_url( 'print.css' );

		if ( $css_url ) {
			echo '<link rel="stylesheet" href="' . esc_url( $css_url ) . '" type="text/css" media="screen, print" />'; // @codingStandardsIgnoreLine
		}
	}
}

Yummy_Printing::init();
