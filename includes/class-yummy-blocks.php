<?php
/**
 * Class-yummy-blocks.php
 *
 * @package yummy-recipes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Yummy_Blocks.
 */
class Yummy_Blocks {

	/**
	 * Script handle.
	 *
	 * @var string
	 */
	private static $script_handle = 'yummy-blocks-script';

	/**
	 * Style handle.
	 *
	 * @var string
	 */
	private static $style_handle = 'yummy-frontend-style';

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_blocks' ), 100 );
		add_action( 'init', array( __CLASS__, 'set_script_translations' ), 101 );
		add_filter( 'block_categories_all', array( __CLASS__, 'add_block_category' ), 10, 2 );
	}

	/**
	 * Registers blocks.
	 */
	public static function register_blocks() {

		// Skip block registration if Gutenberg is not enabled/merged.
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		$asset_file = include YUMMY_DIR_PATH . 'build/index.asset.php';

		// Register block style.
		wp_register_style(
			self::$style_handle,
			yummy_get_css_url( 'yummy.css' ),
			array(),
			YUMMY_PLUGIN_VERSION
		);

		do_action( 'yummy_action_register_blocks', self::$style_handle );

		// Register block script.
		wp_register_script(
			self::$script_handle,
			YUMMY_BASE_PLUGIN_URL . '/build/index.js',
			$asset_file['dependencies'],
			$asset_file['version'],
			true
		);

		$localize = self::get_localized();

		if ( ! empty( $localize ) ) {
			wp_localize_script( self::$script_handle, 'yummyBlocks', $localize );
		}

		// Register blocks.
		register_block_type(
			'yummy/recipe-collection',
			array(
				'attributes'      => array(
					'term_id' => array(
						'type' => 'integer',
					),
				),
				'render_callback' => 'yummy_render_block_recipe_collection',
				'editor_script'   => self::$script_handle,
				'editor_style'    => self::$style_handle,
			)
		);

		register_block_type(
			'yummy/recipe-index',
			array(
				'attributes'      => array(
					'type'     => array(
						'type'    => 'string',
						'default' => 'az',
					),
					'style'    => array(
						'type'    => 'string',
						'default' => 'cards',
					),
					'links'    => array(
						'type'    => 'boolean',
						'default' => true,
					),
					'taxonomy' => array(
						'type'    => 'string',
						'default' => 'yummy_course',
					),
				),
				'render_callback' => 'yummy_render_block_recipe_index',
				'editor_script'   => self::$script_handle,
				'editor_style'    => self::$style_handle,
			)
		);

		register_block_type(
			'yummy/term-index',
			array(
				'attributes'      => array(
					'style'      => array(
						'type'    => 'string',
						'default' => 'cards',
					),
					'taxonomies' => array(
						'type'    => 'array',
						'default' => get_object_taxonomies( 'yummy_recipe', 'names' ),
						'items'   => array(
							'type' => 'string',
						),
					),
					'show_count' => array(
						'type'    => 'boolean',
						'default' => true,
					),
				),
				'render_callback' => 'yummy_render_block_term_index',
				'editor_script'   => self::$script_handle,
				'editor_style'    => self::$style_handle,
			)
		);

		register_block_type(
			'yummy/recipe-card',
			array(
				'attributes'      => array(
					'mediaID'     => array(
						'type' => 'integer',
					),
					'mediaURL'    => array(
						'type' => 'string',
					),
					'description' => array(
						'type' => 'string',
					),
					'prepTime'    => array(
						'type' => 'string',
					),
					'cookTime'    => array(
						'type' => 'string',
					),
					'yield'       => array(
						'type' => 'string',
					),
					'ingredients' => array(
						'type'    => 'string',
						'default' => self::get_ingredients_defaults(),
					),
					'nutrition'   => array(
						'type'    => 'object',
						'default' => self::get_nutrition_defaults(),
						'items'   => array(
							'type' => 'string',
						),
					),
					'video_url'   => array(
						'type' => 'string',
					),
				),
				'supports'        => array(
					'align'    => array( 'wide', 'full' ),
					'multiple' => false, // Only one block allowed.
				),
				'render_callback' => 'yummy_render_block_recipe_card',
				'editor_script'   => self::$script_handle,
				'editor_style'    => self::$style_handle,
			)
		);
	}

	/**
	 * Sets script translations.
	 */
	public static function set_script_translations() {
		wp_set_script_translations( 'yummy-blocks-script', 'yummy-recipes', YUMMY_DIR_PATH . 'languages/' );
	}

	/**
	 * Adds custom block category.
	 *
	 * @param array                   $block_categories     Array of categories for block types.
	 * @param WP_Block_Editor_Context $block_editor_context The current block editor context.
	 *
	 * @return array
	 */
	public static function add_block_category( $block_categories, $block_editor_context ) {

		return array_merge(
			$block_categories,
			array(
				array(
					'slug'  => 'yummy-recipes',
					'title' => 'Yummy Recipes',
				),
			)
		);
	}

	/**
	 * Returns default values for 'yummy/recipe-card' ingredient attribute.
	 */
	public static function get_ingredients_defaults() {
		$ingredients_defaults = array(
			array(
				'title' => 'Sauce',
			),
			array(
				'amount' => 1,
				'unit'   => 'tbsp',
				'name'   => 'Salt',
			),
			array(
				'amount' => 2,
				'unit'   => 'oz',
				'name'   => 'Sugar',
			),
			array(
				'amount' => 3,
				'unit'   => 'cups',
				'name'   => 'Soy sauce',
			),
		);

		$ingredients_defaults = apply_filters( 'yummy_filter_default_ingredients', $ingredients_defaults );
		$ingredients_defaults = wp_json_encode( $ingredients_defaults );

		return $ingredients_defaults;
	}

	/**
	 * Returns default values for 'yummy/recipe-card' ingredient attribute.
	 */
	public static function get_nutrition_defaults() {
		$nutrition_options = yummy_get_nutrition_options();

		if ( empty( $nutrition_options ) ) {
			return false;
		}

		$nutrition_default_filters = array();

		foreach ( $nutrition_options as $key => $value ) {
			$nutrition_default_filters[ $value['structured_data_name'] ] = '';
		}

		return $nutrition_default_filters;
	}

	/**
	 * Returns an array including options for blocks etc. Passed to the blocks via wp_localize_script().
	 *
	 * @return array
	 */
	public static function get_localized() {

		// Get object taxonomies, and set a variable. Used in block settings.
		$taxonomies = get_object_taxonomies( 'yummy_recipe', 'objects' );

		if ( ! empty( $taxonomies ) ) {
			$search_filters = array(
				'keyword' => esc_html__( 'Keyword', 'yummy-recipes' ),
			);

			foreach ( $taxonomies as $taxonomy ) {
				$search_filters[ $taxonomy->name ]   = $taxonomy->labels->singular_name;
				$taxonomy_filters[ $taxonomy->name ] = $taxonomy->labels->singular_name;
			}

			$localize['searchFilterOptions']   = $search_filters;
			$localize['taxonomyFilterOptions'] = $taxonomy_filters;
		}

		$collections = get_terms(
			array(
				'taxonomy'   => 'yummy_collection',
				'hide_empty' => true,
			)
		);

		if ( ! empty( $collections ) && ! is_wp_error( $collections ) ) {
			$localize['recipeListCollections'] = $collections;
		} else {
			$localize['recipeListCollections'] = array();
		}

		$nutrition_options = yummy_get_nutrition_options();

		if ( ! empty( $nutrition_options ) ) {
			$localize['recipeCardNutritionOptions'] = $nutrition_options;
		}

		$sorting_options = yummy_get_sorting_options_array();

		if ( ! empty( $sorting_options ) ) {
			$localize['searchResultsSortingOptions'] = $sorting_options;
		}

		return $localize;
	}
}

Yummy_Blocks::init();
