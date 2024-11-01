<?php
/**
 * Template-hooks.php
 *
 * @package yummy-recipes
 */

defined( 'ABSPATH' ) || exit;

add_action( 'yummy_action_big_card_top', 'yummy_template_recipe_card_top', 10 );
add_action( 'yummy_action_big_card_middle', 'yummy_template_recipe_card_description', 10 );
add_action( 'yummy_action_big_card_middle', 'yummy_template_recipe_card_taxonomies', 14 );
add_action( 'yummy_action_big_card_middle', 'yummy_template_recipe_card_ingredients', 12 );
add_action( 'yummy_action_big_card_middle', 'yummy_template_recipe_card_instructions', 13 );
add_action( 'yummy_action_big_card_middle', 'yummy_template_recipe_card_video_embed', 13 );
add_action( 'yummy_action_big_card_middle', 'yummy_template_recipe_card_nutrition_facts', 14 );
add_action( 'yummy_action_big_card_middle', 'yummy_template_recipe_card_buttons', 16 );

if ( ! function_exists( 'yummy_template_recipe_card_top' ) ) {
	/**
	 * Shows the recipe's top section.
	 *
	 * @param Yummy_Recipe $recipe Recipe.
	 */
	function yummy_template_recipe_card_top( $recipe ) {
		yummy_get_template_part( 'recipe-card/template-top.php', array( 'recipe' => $recipe ) );
	}
}

if ( ! function_exists( 'yummy_template_recipe_card_description' ) ) {
	/**
	 * Shows the recipe description.
	 *
	 * @param Yummy_Recipe $recipe Recipe.
	 */
	function yummy_template_recipe_card_description( $recipe ) {

		if ( $recipe->get_description() ) {
			yummy_get_template_part( 'recipe-card/template-description', array( 'recipe' => $recipe ) );
		}
	}
}

if ( ! function_exists( 'yummy_template_recipe_card_taxonomies' ) ) {
	/**
	 * Shows the recipe's taxonomies.
	 *
	 * @param Yummy_Recipe $recipe Recipe.
	 */
	function yummy_template_recipe_card_taxonomies( $recipe ) {

		if ( $recipe->get_taxonomies() ) {
			yummy_get_template_part( 'recipe-card/template-taxonomies', array( 'recipe' => $recipe ) );
		}
	}
}

if ( ! function_exists( 'yummy_template_recipe_card_ingredients' ) ) {
	/**
	 * Shows the recipe's ingredients.
	 *
	 * @param Yummy_Recipe $recipe Recipe.
	 */
	function yummy_template_recipe_card_ingredients( $recipe ) {

		if ( $recipe->get_ingredient_lists() ) {
			yummy_get_template_part( 'recipe-card/template-ingredients', array( 'recipe' => $recipe ) );
		}
	}
}

if ( ! function_exists( 'yummy_template_recipe_card_instructions' ) ) {
	/**
	 * Shows the recipe's instructions.
	 *
	 * @param Yummy_Recipe $recipe Recipe.
	 */
	function yummy_template_recipe_card_instructions( $recipe ) {

		if ( $recipe->get_instructions() ) {
			yummy_get_template_part( 'recipe-card/template-instructions', array( 'recipe' => $recipe ) );
		}
	}
}

if ( ! function_exists( 'yummy_template_recipe_card_video_embed' ) ) {
	/**
	 * Shows the recipe's video.
	 *
	 * @param Yummy_Recipe $recipe Recipe.
	 */
	function yummy_template_recipe_card_video_embed( $recipe ) {

		if ( ! empty( $recipe->get_video_url() ) ) {
			yummy_get_template_part( 'recipe-card/template-video', array( 'recipe' => $recipe ) );
		}
	}
}

if ( ! function_exists( 'yummy_template_recipe_card_nutrition_facts' ) ) {
	/**
	 * Shows the recipe's nutrition facts.
	 *
	 * @param Yummy_Recipe $recipe Recipe.
	 */
	function yummy_template_recipe_card_nutrition_facts( $recipe ) {

		if ( $recipe->has_nutrition_facts() ) {
			yummy_get_template_part( 'recipe-card/template-nutrition-facts', array( 'recipe' => $recipe ) );
		}
	}
}

if ( ! function_exists( 'yummy_template_recipe_card_buttons' ) ) {
	/**
	 * Shows the recipe's share links.
	 *
	 * @param Yummy_Recipe $recipe Recipe.
	 */
	function yummy_template_recipe_card_buttons( $recipe ) {

		$share_links          = yummy_get_share_links();
		$display_print_button = yummy_get_option( 'display_print_button_on_big_card' );
		$bookmarks_enabled    = yummy_get_option( 'bookmarks_enabled' );

		// Return if there is nothing so show.
		if ( empty( $share_links ) && false === $display_print_button && false === $bookmarks_enabled ) {
			return;
		}

		yummy_get_template_part(
			'recipe-card/template-buttons.php',
			array(
				'share_links'          => $share_links,
				'display_print_button' => $display_print_button,
				'bookmarks_enabled'    => $bookmarks_enabled,
				'recipe'               => $recipe,
			)
		);
	}
}
