<?php
/**
 * Class-yummy-post-types-taxonomies.php
 *
 * @package yummy-recipes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Yummy_Post_Types_Taxonomies.
 */
class Yummy_Post_Types_Taxonomies {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_taxonomies' ) );
		add_action( 'init', array( __CLASS__, 'register_post_types' ) );
		add_filter( 'register_taxonomy_args', array( __CLASS__, 'filter_taxonomy_rewrite_slug' ), 100, 2 );
	}

	/**
	 * Registers custom taxonomies.
	 */
	public static function register_taxonomies() {

		if ( ! taxonomy_exists( 'yummy_course' ) ) {
			$labels = array(
				'name'          => __( 'Courses', 'yummy-recipes' ),
				'singular_name' => __( 'Course', 'yummy-recipes' ),
				'menu_name'     => __( 'Courses', 'yummy-recipes' ),
				'edit_item'     => __( 'Edit Course', 'yummy-recipes' ),
				'search_items'  => __( 'Search Courses', 'yummy-recipes' ),
				'not_found'     => __( 'No courses found', 'yummy-recipes' ),
				'add_new_item'  => __( 'Add New Course', 'yummy-recipes' ),
				'new_item_name' => __( 'New Course Name', 'yummy-recipes' ),
				'back_to_items' => __( '&larr; Go to Courses', 'yummy-recipes' ),
			);

			$args = array(
				'label'             => __( 'Course', 'yummy-recipes' ),
				'labels'            => $labels,
				'hierarchical'      => true,
				'public'            => true,
				'show_ui'           => true,
				'show_in_nav_menus' => true,
				'query_var'         => 'course',
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array(
					'slug'       => 'course',
					'with_front' => false,
				),
			);

			register_taxonomy( 'yummy_course', 'yummy_recipe', $args );
		}

		if ( ! taxonomy_exists( 'yummy_cuisine' ) ) {
			$labels = array(
				'name'          => __( 'Cuisines', 'yummy-recipes' ),
				'singular_name' => __( 'Cuisine', 'yummy-recipes' ),
				'menu_name'     => __( 'Cuisines', 'yummy-recipes' ),
				'edit_item'     => __( 'Edit Cuisine', 'yummy-recipes' ),
				'search_items'  => __( 'Search Cuisines', 'yummy-recipes' ),
				'not_found'     => __( 'No cuisine found', 'yummy-recipes' ),
				'add_new_item'  => __( 'Add New Cuisine', 'yummy-recipes' ),
				'new_item_name' => __( 'New Cuisine Name', 'yummy-recipes' ),
				'back_to_items' => __( '&larr; Go to Cuisines', 'yummy-recipes' ),
			);

			$args = array(
				'label'             => __( 'Cuisine', 'yummy-recipes' ),
				'labels'            => $labels,
				'hierarchical'      => true,
				'public'            => true,
				'show_ui'           => true,
				'show_in_nav_menus' => true,
				'query_var'         => 'cuisine',
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array(
					'slug'       => 'cuisine',
					'with_front' => false,
				),
			);

			register_taxonomy( 'yummy_cuisine', 'yummy_recipe', $args );
		}

		if ( ! taxonomy_exists( 'yummy_special_diet' ) ) {
			$labels = array(
				'name'          => __( 'Special Diets', 'yummy-recipes' ),
				'singular_name' => __( 'Special Diet', 'yummy-recipes' ),
				'menu_name'     => __( 'Special Diets', 'yummy-recipes' ),
				'edit_item'     => __( 'Edit Special Diet', 'yummy-recipes' ),
				'search_items'  => __( 'Search Special Diets', 'yummy-recipes' ),
				'not_found'     => __( 'No special diet found', 'yummy-recipes' ),
				'add_new_item'  => __( 'Add New Special Diet', 'yummy-recipes' ),
				'new_item_name' => __( 'New Special Diet Name', 'yummy-recipes' ),
				'back_to_items' => __( '&larr; Go to Special Diets', 'yummy-recipes' ),
			);

			$args = array(
				'label'             => __( 'Special Diet', 'yummy-recipes' ),
				'labels'            => $labels,
				'hierarchical'      => true,
				'public'            => true,
				'show_ui'           => true,
				'show_in_nav_menus' => true,
				'query_var'         => 'special-diet',
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array(
					'slug'       => 'special-diet',
					'with_front' => false,
				),
			);

			register_taxonomy( 'yummy_special_diet', 'yummy_recipe', $args );
		}

		if ( ! taxonomy_exists( 'yummy_difficulty' ) ) {
			$labels = array(
				'name'          => _x( 'Difficulties', 'taxonomy general name', 'yummy-recipes' ),
				'singular_name' => __( 'Difficulty', 'yummy-recipes' ),
				'menu_name'     => __( 'Difficulties', 'yummy-recipes' ),
				'edit_item'     => __( 'Edit Difficulty', 'yummy-recipes' ),
				'search_items'  => __( 'Search Difficulties', 'yummy-recipes' ),
				'not_found'     => __( 'No difficulty found', 'yummy-recipes' ),
				'add_new_item'  => __( 'Add New Difficulty', 'yummy-recipes' ),
				'new_item_name' => __( 'New Difficulty Name', 'yummy-recipes' ),
				'back_to_items' => __( '&larr; Go to Difficulties', 'yummy-recipes' ),
			);

			$args = array(
				'label'             => __( 'Difficulty', 'yummy-recipes' ),
				'labels'            => $labels,
				'hierarchical'      => true,
				'public'            => true,
				'show_ui'           => true,
				'show_in_nav_menus' => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array(
					'slug'       => 'difficulty',
					'with_front' => false,
				),
			);

			register_taxonomy( 'yummy_difficulty', 'yummy_recipe', $args );
		}

		if ( ! taxonomy_exists( 'yummy_collection' ) ) {
			$labels = array(
				'name'          => __( 'Collections', 'yummy-recipes' ),
				'singular_name' => __( 'Collection', 'yummy-recipes' ),
				'menu_name'     => __( 'Collections', 'yummy-recipes' ),
				'edit_item'     => __( 'Edit Collection', 'yummy-recipes' ),
				'search_items'  => __( 'Search Collections', 'yummy-recipes' ),
				'not_found'     => __( 'No collection found', 'yummy-recipes' ),
				'add_new_item'  => __( 'Add New Collection', 'yummy-recipes' ),
				'new_item_name' => __( 'New Collection Name', 'yummy-recipes' ),
				'back_to_items' => __( '&larr; Go to Collections', 'yummy-recipes' ),
			);

			$args = array(
				'label'             => __( 'Collection', 'yummy-recipes' ),
				'labels'            => $labels,
				'hierarchical'      => true,
				'public'            => true,
				'show_ui'           => true,
				'show_in_nav_menus' => true,
				'query_var'         => 'collection',
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array(
					'slug'       => 'collection',
					'with_front' => false,
				),
			);

			register_taxonomy( 'yummy_collection', 'yummy_recipe', $args );
		}

		if ( ! taxonomy_exists( 'yummy_recipe_tag' ) ) {
			$labels = array(
				'name'          => __( 'Recipe Tags', 'yummy-recipes' ),
				'singular_name' => __( 'Recipe Tag', 'yummy-recipes' ),
				'menu_name'     => __( 'Tags', 'yummy-recipes' ),
				'edit_item'     => __( 'Edit Recipe Tag', 'yummy-recipes' ),
				'search_items'  => __( 'Search Recipe Tags', 'yummy-recipes' ),
				'not_found'     => __( 'No recipe tags found', 'yummy-recipes' ),
				'add_new_item'  => __( 'Add New Recipe Tag', 'yummy-recipes' ),
				'back_to_items' => __( '&larr; Go to Recipe Tags', 'yummy-recipes' ),
			);

			$args = array(
				'label'             => __( 'Recipe Tag', 'yummy-recipes' ),
				'labels'            => $labels,
				'singular_label'    => __( 'Recipe Tag', 'yummy-recipes' ),
				'hierarchical'      => false,
				'public'            => true,
				'show_ui'           => true,
				'query_var'         => 'recipe-tag',
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'show_tagcloud'     => true,
				'rewrite'           => array(
					'slug'       => 'recipe-tag',
					'with_front' => false,
				),
			);

			register_taxonomy( 'yummy_recipe_tag', 'yummy_recipe', $args );
		}
	}

	/**
	 * Registers custom post types.
	 */
	public static function register_post_types() {

		$slug = get_option( 'yummy_permalink_base_recipe' );
		if ( ! $slug ) {
			$slug = 'recipe';
		}

		$slug_archive = get_option( 'yummy_permalink_base_recipe_archive' );
		if ( ! $slug_archive ) {
			$slug_archive = _x( 'recipes', 'Recipe archive page URL slug.', 'yummy-recipes' );
		}

		$labels = array(
			'name'          => __( 'Recipes', 'yummy-recipes' ),
			'singular_name' => __( 'Recipe', 'yummy-recipes' ),
			'add_new'       => __( 'Add New Recipe', 'yummy-recipes' ),
			'edit_item'     => __( 'Edit Recipe', 'yummy-recipes' ),
			'search_items'  => __( 'Search Recipes', 'yummy-recipes' ),
			'not_found'     => __( 'No recipes found', 'yummy-recipes' ),
			'new_item'      => __( 'New Recipe', 'yummy-recipes' ),
			'add_new_item'  => __( 'Add New Recipe', 'yummy-recipes' ),
			'view_item'     => __( 'View Recipe', 'yummy-recipes' ),
		);

		$args = array(
			'labels'        => $labels,
			'menu_icon'     => 'dashicons-list-view',
			'menu_position' => 5,
			'public'        => true,
			'has_archive'   => $slug_archive,
			'supports'      => array( 'title', 'editor', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields' ),
			'show_in_rest'  => true,
			'rewrite'       => array(
				'slug'       => $slug,
				'with_front' => false,
			),
			'menu_icon'     => 'dashicons-food',
			'template'      => array(
				array(
					'yummy/recipe-card',
				),
			),
		);

		register_post_type( 'yummy_recipe', $args );
	}

	/**
	 * Filters taxonomy args, and changes the slug argument if permalink setting is set.
	 *
	 * @param array  $args     Taxonomy arguments.
	 * @param string $taxonomy Taxonomy.
	 *
	 * @return array
	 */
	public static function filter_taxonomy_rewrite_slug( $args, $taxonomy ) {

		$permalink_base = get_option( 'yummy_permalink_base_' . $taxonomy );

		if ( ! empty( $permalink_base ) ) {
			$args['rewrite']['slug'] = $permalink_base;
		}

		return $args;
	}
}

Yummy_Post_Types_Taxonomies::init();
