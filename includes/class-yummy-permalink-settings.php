<?php
/**
 * Class-yummy-permalink-settings.php
 *
 * @package yummy-recipes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Yummy_Permalink_Settings.
 */
class Yummy_Permalink_Settings {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'add_permalinks_section' ) );
		add_action( 'load-options-permalink.php', array( __CLASS__, 'add_permalink_fields' ) );
	}

	/**
	 * Adds a section for custom permalinks.
	 */
	public static function add_permalinks_section() {
		add_settings_section(
			'permalink_settings_section',
			__( 'Yummy', 'yummy-recipes' ),
			array( __CLASS__, 'permalink_settings_section_callback' ),
			'permalink'
		);
	}

	/**
	 * Adds instructions for the custom permalink section.
	 */
	public static function permalink_settings_section_callback() {
		echo '<p>' . esc_html__( 'Change the URL structure used in the plugin. If you leave these blank the defaults will be used.', 'yummy-recipes' ) . '</p>';
	}

	/**
	 * Adds form fields to the custom permalink section.
	 */
	public static function add_permalink_fields() {

		// Add post type permalink settings manually.
		$custom_permalinks = array(
			'yummy_permalink_base_recipe'         => 'Recipe base',
			'yummy_permalink_base_recipe_archive' => 'Recipe archive base',
		);

		// Add taxonomy settings.
		$taxonomies = get_object_taxonomies( 'yummy_recipe', 'objects' );

		foreach ( $taxonomies as $taxonomy ) {
			$slug = 'yummy_permalink_base_' . $taxonomy->name;

			// Translators: %s: Permalink setting name for a taxonomy.
			$custom_permalinks[ $slug ] = sprintf( esc_html__( '%s base', 'yummy-recipes' ), $taxonomy->label );
		}

		// Add print setting.
		$custom_permalinks['yummy_permalink_print_endpoint'] = 'Print endpoint';

		foreach ( $custom_permalinks as $slug => $title ) {
			add_settings_field(
				$slug,
				$title,
				array( __CLASS__, 'permalink_settings_field_callback' ),
				'permalink',
				'permalink_settings_section',
				array(
					'name'      => $slug,
					'label_for' => 'id-' . $slug,
				)
			);

			$value = filter_input( INPUT_POST, $slug );
			if ( isset( $value ) ) {
				update_option( $slug, sanitize_option( 'category_base', $value ) );
			}
		}
	}

	/**
	 * Callback function for displaying the permalink settings form fields.
	 *
	 * @param  array $args Arguments passed from add_settings_field().
	 */
	public static function permalink_settings_field_callback( $args ) {
		echo '<input type="text" value="' . esc_attr( get_option( $args['name'] ) ) . '" name="' . esc_attr( $args['name'] ) . '" id="' . esc_attr( $args['label_for'] ) . '" class="regular-text code">';
	}
}

Yummy_Permalink_Settings::init();
