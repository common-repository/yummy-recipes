<?php
/**
 * Class-Yummy-Customizer.php
 *
 * @package yummy-recipes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Yummy_Customizer.
 */
class Yummy_Customizer {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'customize_register', array( __CLASS__, 'register' ) );
		add_action( 'customize_preview_init', array( __CLASS__, 'live_preview' ) );
		add_action( 'customize_controls_print_styles', array( __CLASS__, 'inline_styles' ) );
		add_action( 'customize_save_after', array( __CLASS__, 'update_default_thumbnail_url_on_save' ) );
	}

	/**
	 * Adds customizer sections, settings, and controls.
	 *
	 * @param WP_Customize_Manager $wp_customize  Instance of the WP_Customize_Manager class.
	 */
	public static function register( $wp_customize ) {

		// Allow multicheck settings.
		require_once trailingslashit( YUMMY_DIR_PATH ) . 'includes/class-yummy-customize-control-checkbox-multiple.php';

		$wp_customize->add_section(
			'yummy_section',
			array(
				'title'    => 'Yummy Recipes',
				'priority' => 1000,
			)
		);

		$wp_customize->add_setting(
			'yummy_big_card_style',
			array(
				'default'           => 'classic',
				'type'              => 'option',
				'transport'         => 'refresh',
				'sanitize_callback' => 'esc_attr',
			)
		);

		$wp_customize->add_control(
			'yummy_big_card_style',
			array(
				'label'    => __( 'Big recipe card style', 'yummy-recipes' ),
				'section'  => 'yummy_section',
				'settings' => 'yummy_big_card_style',
				'type'     => 'select',
				'choices'  => apply_filters(
					'yummy_filter_customizer_options_big_card_style',
					array(
						'classic' => 'Classic',
						'modern'  => 'Modern',
						'hero'    => 'Hero',
					)
				),
			)
		);

		$wp_customize->add_setting(
			'yummy_small_card_style',
			array(
				'default'           => 'classic',
				'type'              => 'option',
				'transport'         => 'refresh',
				'sanitize_callback' => 'esc_attr',
			)
		);

		$wp_customize->add_control(
			'yummy_small_card_style',
			array(
				'label'    => __( 'Small recipe card style', 'yummy-recipes' ),
				'section'  => 'yummy_section',
				'settings' => 'yummy_small_card_style',
				'type'     => 'select',
				'choices'  => apply_filters(
					'yummy_filter_customizer_options_small_card_style',
					array(
						'classic' => 'Classic',
						'modern'  => 'Modern',
						'overlay' => 'Overlay',
					)
				),
			)
		);

		$wp_customize->add_setting(
			'yummy_taxonomies_on_card',
			array(
				'default'           => array( 'yummy_course', 'yummy_difficulty', 'yummy_special_diet' ),
				'type'              => 'option',
				'transport'         => 'refresh',
				'sanitize_callback' => 'yummy_customizer_sanitize_array',
			)
		);

		$wp_customize->add_control(
			new Yummy_Customize_Control_Checkbox_Multiple(
				$wp_customize,
				'yummy_taxonomies_on_card',
				array(
					'section'     => 'yummy_section',
					'label'       => __( 'Taxonomies shown on the recipe card', 'yummy-recipes' ),
					'description' => __( 'Choose which taxonomies are shown on the recipe card.', 'yummy-recipes' ),
					'choices'     => self::get_customizer_taxonomy_choices(),
				)
			)
		);

		$wp_customize->add_setting(
			'yummy_display_author',
			array(
				'default'           => array( 'big_card', 'small_card', 'print' ),
				'type'              => 'option',
				'transport'         => 'refresh',
				'sanitize_callback' => 'yummy_customizer_sanitize_array',
			)
		);

		$wp_customize->add_control(
			new Yummy_Customize_Control_Checkbox_Multiple(
				$wp_customize,
				'yummy_display_author',
				array(
					'section' => 'yummy_section',
					'label'   => __( 'Show recipe author in', 'yummy-recipes' ),
					'choices' => array(
						'big_card'   => 'Big card',
						'small_card' => 'Small card',
						'print'      => 'Print',
					),
				)
			)
		);

		$wp_customize->add_setting(
			'yummy_share_links',
			array(
				'default'           => array( 'facebook', 'pinterest', 'twitter' ),
				'type'              => 'option',
				'transport'         => 'refresh',
				'sanitize_callback' => 'yummy_customizer_sanitize_array',
			)
		);

		$wp_customize->add_control(
			new Yummy_Customize_Control_Checkbox_Multiple(
				$wp_customize,
				'yummy_share_links',
				array(
					'section'     => 'yummy_section',
					'label'       => __( 'Share', 'yummy-recipes' ),
					'description' => __( 'Choose share links to be displayed on the recipe card.', 'yummy-recipes' ),
					'choices'     => array(
						'facebook'  => 'Facebook',
						'pinterest' => 'Pinterest',
						'twitter'   => 'Twitter',
					),
				)
			)
		);

		// Colors.
		$colors[] = array(
			'slug'      => 'yummy_color_big_card_background',
			'default'   => '#ffffff',
			'label'     => __( 'Recipe card background color', 'yummy-recipes' ),
			'transport' => 'postMessage',
		);

		$colors[] = array(
			'slug'            => 'yummy_color_card_border',
			'default'         => '#222',
			'label'           => __( 'Recipe card border color (only for Classic)', 'yummy-recipes' ),
			'transport'       => 'postMessage',
			'active_callback' => function ( $control ) {
				$big_card_style = $control->manager->get_setting( 'yummy_big_card_style' );
				$small_card_style = $control->manager->get_setting( 'yummy_small_card_style' );

				if ( 'classic' === $big_card_style->value() || 'classic' === $small_card_style->value() ) {
					return true;
				}

				return false;
			},
		);

		$colors[] = array(
			'slug'      => 'yummy_color_big_card_top',
			'default'   => '#fafafa',
			'label'     => __( 'Recipe card top background color', 'yummy-recipes' ),
			'transport' => 'postMessage',
		);

		$colors[] = array(
			'slug'      => 'yummy_color_big_card_top_link',
			'default'   => '#333',
			'label'     => __( 'Recipe card top link color', 'yummy-recipes' ),
			'transport' => 'postMessage',
		);

		$colors[] = array(
			'slug'            => 'yummy_color_big_card_top_image_border',
			'default'         => '#e2e2e2',
			'label'           => __( 'Image border on the recipe card', 'yummy-recipes' ),
			'transport'       => 'postMessage',
			'active_callback' => function ( $control ) {
				$setting = $control->manager->get_setting( 'yummy_big_card_style' );

				if ( 'classic' === $setting->value() ) {
					return false;
				}

				return true;
			},
		);

		$colors[] = array(
			'slug'            => 'yummy_color_small_card_background',
			'default'         => '#ffffff',
			'label'           => __( 'Small card background color', 'yummy-recipes' ),
			'transport'       => 'postMessage',
			'active_callback' => function ( $control ) {
				$setting = $control->manager->get_setting( 'yummy_small_card_style' );

				if ( 'classic' === $setting->value() ) {
					return false;
				}

				return true;
			},
		);

		$colors[] = array(
			'slug'      => 'yummy_color_stars',
			'default'   => '#e6b200',
			'label'     => __( 'Rating stars color', 'yummy-recipes' ),
			'transport' => 'postMessage',
		);

		$colors[] = array(
			'slug'      => 'yummy_color_instructions_step_background',
			'default'   => '#333333',
			'label'     => __( 'Instructions step number background', 'yummy-recipes' ),
			'transport' => 'postMessage',
		);

		$colors[] = array(
			'slug'        => 'yummy_color_social_icons',
			'default'     => '',
			'label'       => __( 'Social icons color', 'yummy-recipes' ),
			'description' => __( 'Leave blank to use the default colors.', 'yummy-recipes' ),
			'transport'   => 'postMessage',
		);

		// Add settings and controls for each color.
		foreach ( $colors as $color ) {

			$wp_customize->add_setting(
				$color['slug'],
				array(
					'default'           => $color['default'],
					'type'              => 'option',
					'transport'         => $color['transport'],
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					$color['slug'],
					array(
						'label'           => $color['label'],
						'description'     => ( ! empty( $color['description'] ) ? $color['description'] : '' ),
						'section'         => 'yummy_section',
						'settings'        => $color['slug'],
						'active_callback' => ( ! empty( $color['active_callback'] ) ? $color['active_callback'] : '' ),
					)
				)
			);
		}

		$wp_customize->add_setting(
			'yummy_default_thumbnail_image_id',
			array(
				'type'              => 'option',
				'transport'         => 'refresh',
				'sanitize_callback' => 'absint',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Media_Control(
				$wp_customize,
				'yummy_default_thumbnail',
				array(
					'label'       => __( 'Default thumbnail image', 'yummy-recipes' ),
					'description' => __( 'Used when a recipe has no image.', 'yummy-recipes' ),
					'section'     => 'yummy_section',
					'settings'    => 'yummy_default_thumbnail_image_id',
					'mime_type'   => 'image',
				)
			)
		);
	}

	/**
	 * Enqueues script used by the customizer.
	 */
	public static function live_preview() {
		wp_enqueue_script( 'yummy-theme-customize', YUMMY_BASE_PLUGIN_URL . '/assets/js/theme-customize.js', array( 'jquery', 'customize-preview' ), YUMMY_PLUGIN_VERSION, true );
	}

	/**
	 * Hides the remove button from the default image setting.
	 */
	public static function inline_styles() { ?>
		<style>
			#customize-control-yummy_default_thumbnail button.remove-button {
				display: none;
			}
			</style>
		<?php
	}

	/**
	 * Returns options for taxonomy displayed on recipe cards.
	 *
	 * @return array  Array of taxonomies
	 */
	public static function get_customizer_taxonomy_choices() {

		$options = array();

		$taxonomies = get_object_taxonomies( 'yummy_recipe', 'objects' );

		foreach ( $taxonomies as $taxonomy ) {
			$options[ $taxonomy->name ] = $taxonomy->labels->singular_name;
		}

		return $options;
	}

	/**
	 * Saves option for default thumbnail URL when saving the options in the customizer.
	 *
	 * @return void
	 */
	public static function update_default_thumbnail_url_on_save() {
		delete_option( 'yummy_default_thumbnail_image_url' );

		$image_id = get_option( 'yummy_default_thumbnail_image_id' );

		if ( is_numeric( $image_id ) ) {
			$get_attachment = wp_get_attachment_image_src( $image_id, 'yummy-320x320' );

			if ( is_array( $get_attachment ) ) {
				update_option( 'yummy_default_thumbnail_image_url', $get_attachment[0] );
			}
		}
	}
}

Yummy_Customizer::init();
