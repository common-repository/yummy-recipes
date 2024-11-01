<?php
/**
 * Yummy-recipe.php
 *
 * @package yummy-recipes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Returns Yummy_Recipe object.
 *
 * @param  int   $post_id    Post ID.
 * @param  array $attributes Block attributes.
 *
 * @return array
 */
function yummy_get_recipe_object( $post_id = null, $attributes = null ) {

	if ( empty( $post_id ) ) {
		return false;
	}

	$get_post = get_post( $post_id );

	// Parse blocks in the post content to get block attributes.
	// If called from the recipe card, the attributes are already set.
	if ( empty( $attributes ) ) {
		$attributes = array();

		if ( has_blocks( $get_post->post_content ) ) {
			$blocks = parse_blocks( $get_post->post_content );

			if ( ! empty( $blocks ) ) {
				foreach ( $blocks as $key => $block ) {
					if ( 'yummy/recipe-card' === $block['blockName'] ) {
						$attributes = $block['attrs'];

						if ( ! empty( $block['innerBlocks'] ) ) {
							$attributes['instructions'] = '';
							foreach ( $block['innerBlocks'] as $key => $value ) {
								if ( 'core/list' === $value['blockName'] || 'core/heading' === $value['blockName'] ) {
									$attributes['instructions'] .= $value['innerHTML'];
								}
							}
						}
					}
				}
			}
		}
	}

	// Remove instructions if it has only the placeholder content.
	// Otherwise the placeholder content is shown on the recipe card, structured data etc.
	if ( ! empty( $attributes ) && ! empty( $attributes['instructions'] ) && strlen( trim( wp_strip_all_tags( $attributes['instructions'] ) ) ) === 0 ) {
		$attributes['instructions'] = '';
	}

	// Check that the post was found, and the post type is the right one.
	if ( is_a( $get_post, 'WP_Post' ) && 'yummy_recipe' === $get_post->post_type ) {

		// Create a new Yummy_Recipe.
		$object = new Yummy_Recipe( $get_post, $attributes );

		// Allow filtering the returned object.
		$object = apply_filters( 'yummy_filter_get_recipe_object', $object, $post_id );

		return $object;
	}

	return false;
}

/**
 * Returns nutrition facts for the recipe.
 *
 * @param WP_Post $attributes Block attributes.
 * @param boolean $all        Set to true if serving size and calories per serving should be included.
 *
 * @return array
 */
function yummy_get_nutrition_facts( $attributes, $all = false ) {

	if ( empty( $attributes ) || empty( $attributes['nutrition'] ) || ! is_array( $attributes['nutrition'] ) ) {
		return false;
	}

	$exclude = array( 'servingSize', 'calories' );

	$return = array();

	$nutrition_options = yummy_get_nutrition_options();

	if ( empty( $nutrition_options ) || ! is_array( $nutrition_options ) ) {
		return false;
	}

	foreach ( $nutrition_options as $key => $fields ) {

		// If the field is excluded.
		if ( false === $all && in_array( $fields['structured_data_name'], $exclude, true ) ) {
			continue;
		}

		$meta_value = $attributes['nutrition'][ $fields['structured_data_name'] ];

		if ( ! empty( $meta_value ) ) {

			$object = new \stdClass();

			$object->name   = $fields['name'];
			$object->amount = $meta_value;

			// Structured data name.
			if ( ! empty( $fields['structured_data_name'] ) ) {
				$object->structured_data_name = $fields['structured_data_name'];
			}

			// Unit.
			if ( ! empty( $fields['unit'] ) ) {
				$object->unit = ( ! empty( $fields['unit'] ) ? $fields['unit'] : false );
			}

			// Daily value.
			if ( ! empty( $fields['daily_value'] ) ) {
				$object->daily_value = apply_filters( 'yummy_filter_nutrition_daily_value', $fields['daily_value'], $fields['structured_data_name'] );

				// Calculate percentage of the daily value.
				$object->daily_value_percent = round( 100 * ( floatval( $meta_value ) / $object->daily_value ), 0 ) . '%';
			}

			// If has parent field.
			$object->has_parent = ( ! empty( $fields['parent_field'] ) ? true : false );

			$return[ $fields['structured_data_name'] ] = $object;
		}
	}

	return $return;
}
