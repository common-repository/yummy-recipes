<?php
/**
 * Blocks-render.php
 *
 * @package yummy-recipes
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'yummy_render_block_recipe_card' ) ) {
	/**
	 * Renders 'yummy/recipe-card' block.
	 *
	 * @param  array  $atts    Attributes.
	 * @param  string $content Content.
	 *
	 * @return string          HTML.
	 */
	function yummy_render_block_recipe_card( $atts, $content ) {

		if ( ! is_singular( 'yummy_recipe' ) ) {
			return false;
		}

		$post_id = get_the_ID();

		// Instructions are saved as InnerBlocks. Get the InnerBlocks content with the 'content' parameter.
		if ( ! empty( $content ) ) {
			$atts['instructions'] = $content;
		}

		$recipe = yummy_get_recipe_object( $post_id, $atts );

		$template_args['recipe'] = $recipe;

		ob_start();

		yummy_get_template_part( 'template-recipe-card', $template_args );

		yummy_output_recipe_card_structured_data_json( $recipe );

		wp_reset_postdata();

		// Return output.
		return ob_get_clean();
	}
}

if ( ! function_exists( 'yummy_render_block_recipe_collection' ) ) {
	/**
	 * Renders 'yummy/recipe-collection' block.
	 *
	 * @param  array $atts Block attributes.
	 *
	 * @return string      HTML.
	 */
	function yummy_render_block_recipe_collection( $atts ) {

		if ( empty( $atts['term_id'] ) ) {
			return;
		}

		$wp_query_args = array(
			'post_type'              => 'yummy_recipe',
			'posts_per_page'         => -1,
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
		);

		$wp_query_args['tax_query'] = array( // phpcs:ignore slow query ok.
			array(
				'taxonomy' => 'yummy_collection',
				'field'    => 'term_id',
				'terms'    => absint( $atts['term_id'] ),
			),
		);

		$wp_query_recipes = new WP_Query( $wp_query_args );

		ob_start();

		if ( $wp_query_recipes->have_posts() ) {
			yummy_get_template_part(
				'template-recipe-grid',
				array(
					'wp_query_recipes' => $wp_query_recipes,
				)
			);

			yummy_output_recipe_collection_structured_data_json( $wp_query_recipes );
		}

		wp_reset_postdata();

		return ob_get_clean();
	}
}

if ( ! function_exists( 'yummy_render_block_recipe_index' ) ) {
	/**
	 * Renders 'yummy/recipe-index' block.
	 *
	 * @param  array $atts Block attributes.
	 *
	 * @return string      HTML.
	 */
	function yummy_render_block_recipe_index( $atts ) {

		if ( 'az' === $atts['type'] ) {
			return yummy_get_recipe_index_az( $atts );
		} elseif ( 'taxonomies' === $atts['type'] ) {
			return yummy_get_recipe_index_taxonomies( $atts );
		}
	}
}

if ( ! function_exists( 'yummy_render_block_term_index' ) ) {
	/**
	 * Renders 'yummy/recipe-term-index' block.
	 *
	 * @param  array $atts Block attributes.
	 *
	 * @return string      HTML.
	 */
	function yummy_render_block_term_index( $atts ) {

		$style      = $atts['style'];
		$show_count = $atts['show_count'];

		$get_object_taxonomies = get_object_taxonomies( 'yummy_recipe', 'objects' );

		$taxonomies = array();

		// If taxonomy attribute is set.
		if ( ! empty( $get_object_taxonomies ) ) {
			if ( ! empty( $atts['taxonomies'] ) && is_array( $atts['taxonomies'] ) ) {
				foreach ( $atts['taxonomies'] as $taxonomy_name ) {
					if ( array_key_exists( $taxonomy_name, $get_object_taxonomies ) ) {
						$taxonomies[ $taxonomy_name ] = $get_object_taxonomies[ $taxonomy_name ];
					}
				}
			} else {
				$taxonomies = $get_object_taxonomies;
			}
		}

		ob_start();

		foreach ( $taxonomies as $taxonomy ) {

			$terms = get_terms(
				array(
					'taxonomy'   => $taxonomy->name,
					'hide_empty' => true,
				)
			);

			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
				yummy_get_template_part(
					'template-term-index.php',
					array(
						'taxonomy'   => $taxonomy,
						'terms'      => $terms,
						'style'      => $style,
						'show_count' => $show_count,
					)
				);
			}
		}

		return ob_get_clean();
	}
}
