<?php
/**
 * Structured-data.php
 *
 * @package yummy-recipes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Outputs 'Recipe' structured data for yummy/recipe-card block.
 *
 * @param  Yummy_Recipe $recipe Recipe.
 */
function yummy_output_recipe_card_structured_data_json( $recipe ) {

	// Allow only on single recipes.
	if ( ! is_singular( 'yummy_recipe' ) ) {
		return;
	}

	// Get the global post object.
	global $post;

	$structured_data = array();

	$structured_data = array(
		'@context'      => 'https://schema.org/',
		'@type'         => 'Recipe',
		'name'          => $recipe->get_title(),
		'url'           => get_permalink(),
		'datePublished' => get_the_date( 'c' ),
		'author'        => array(
			'@type' => 'Person',
			'name'  => $recipe->get_author_name(),
		),
	);

	// Set recipe description.
	if ( ! empty( $recipe->get_description() ) ) {
		$description = $recipe->get_description();
	}

	// If recipe description attribute is not set, a short piece of post content is used.
	if ( empty( $description ) && ! empty( $post->post_content ) ) {
		$description = wp_kses_post( strip_shortcodes( wp_strip_all_tags( $post->post_content ) ) );

		// Shorten the post content to be used as description.
		if ( strlen( $description ) >= 160 ) {
			$description = substr( $description, 0, 150 ) . '...';
		}

		$description = str_replace( '....', '...', $description );
	}

	$structured_data['description'] = $description;

	if ( is_numeric( $recipe->get_image_id() ) ) {
		$structured_data['image'][] = wp_get_attachment_image_url( $recipe->get_image_id(), 'yummy-320x320' ); // 1x1
		$structured_data['image'][] = wp_get_attachment_image_url( $recipe->get_image_id(), 'yummy-400x300' ); // 4x3
		$structured_data['image'][] = wp_get_attachment_image_url( $recipe->get_image_id(), 'yummy-400x225' ); // 16x9
	}

	// recipeYield.
	if ( ! empty( $recipe->get_yield() ) ) {
		$structured_data['recipeYield'] = esc_html( $recipe->get_yield() );
	}

	// prepTime.
	if ( ! empty( $recipe->get_prep_time() ) ) {
		$structured_data['prepTime'] = 'PT' . absint( $recipe->get_prep_time() ) . 'M';
	}

	// cookTime.
	if ( ! empty( $recipe->get_cook_time() ) ) {
		$structured_data['cookTime'] = 'PT' . absint( $recipe->get_cook_time() ) . 'M';
	}

	// totalTime.
	if ( ! empty( $recipe->get_prep_time() ) || ! empty( $recipe->get_cook_time() ) ) {
		$prep_time = ( ! empty( absint( $recipe->get_prep_time() ) ) ? absint( $recipe->get_prep_time() ) : 0 );
		$cook_time = ( ! empty( absint( $recipe->get_cook_time() ) ) ? absint( $recipe->get_cook_time() ) : 0 );

		$total_time = $prep_time + $cook_time;
		if ( $total_time > 0 ) {
			$structured_data['totalTime'] = 'PT' . absint( $total_time ) . 'M';
		}
	}

	// recipeCategory.
	$courses = get_the_terms( $post->ID, 'yummy_course' );
	if ( $courses && ! is_wp_error( $courses ) ) {
		foreach ( $courses as $course ) {
			$structured_data['recipeCategory'][] = $course->name;
		}
	}

	// recipeCuisine.
	$cuisines = get_the_terms( $post->ID, 'yummy_cuisine' );
	if ( $cuisines && ! is_wp_error( $cuisines ) ) {
		foreach ( $cuisines as $cuisine ) {
			$structured_data['recipeCuisine'][] = $cuisine->name;
		}
	}

	// keywords.
	$get_post_terms = wp_get_post_terms(
		$post->ID,
		array( 'yummy_recipe_tag', 'yummy_difficulty', 'yummy_special_diet' ),
		array( 'fields' => 'names' )
	);

	if ( ! empty( $get_post_terms ) && ! is_wp_error( $get_post_terms ) ) {
		$structured_data['keywords'] = implode( ', ', $get_post_terms );
	}

	if ( $recipe->get_ingredient_lists() ) {
		foreach ( $recipe->get_ingredient_lists() as $ingredient_list ) {
			foreach ( $ingredient_list->ingredients as $ingredient ) {
				if ( ! empty( $ingredient->name ) ) {
					$amount = '';
					if ( ! empty( $ingredient->amount ) ) {
						$amount = $ingredient->amount . ' ';
					}
					$unit = '';
					if ( ! empty( $ingredient->unit ) ) {
						$unit = $ingredient->unit . ' ';
					}
					$structured_data['recipeIngredient'][] = $amount . $unit . $ingredient->name;
				}
			}
		}
	}

	// aggregateRating.
	if ( $post->_yummy_meta_average_rating > 0 && $post->_yummy_meta_ratings_count > 0 ) {
		$structured_data['aggregateRating'] = array(
			'@type'       => 'AggregateRating',
			'ratingValue' => esc_html( round( $post->_yummy_meta_average_rating, 1 ) ),
			'ratingCount' => absint( $post->_yummy_meta_ratings_count ),
		);
	}

	// recipeInstructions.
	$instructions = ( ! empty( $recipe->get_instructions() ) ? $recipe->get_instructions() : '' );
	if ( ! empty( $instructions ) ) {
		$doc = new DOMDocument();
		libxml_use_internal_errors( true );

		// Remove other tags than lists.
		$html = strip_tags( $instructions, '<ol><ul><li>' );
		$doc->loadHTML( mb_convert_encoding( $html, 'HTML-ENTITIES', 'UTF-8' ) );
		libxml_clear_errors();

		$li_elements = $doc->getElementsByTagName( 'li' );

		if ( ! empty( $li_elements ) ) {
			$structured_data['recipeInstructions'] = array();

			foreach ( $li_elements as $li ) {
				$text = strip_shortcodes( wp_strip_all_tags( $li->nodeValue ) ); // phpcs:ignore

				$structured_data['recipeInstructions'][] = array(
					'@type' => 'HowToStep',
					'text'  => $text,
				);
			}
		}
	}

	// Nutrition.
	$nutrition_facts = $recipe->get_nutrition_facts( true );

	if ( ! empty( $nutrition_facts ) || ! empty( $calories_per_serving ) || ! empty( $servings ) ) {
		$structured_data['nutrition']['@type'] = 'NutritionInformation';

		foreach ( $nutrition_facts as $meta_key => $values ) {
			if ( ! empty( $values->name ) && ! empty( $values->amount ) && ! empty( $values->structured_data_name ) ) {
				if ( ! empty( $values->unit ) ) {
					$value = esc_html( $values->amount ) . ' ' . esc_html( $values->unit );
				} else {
					$value = absint( $values->amount );
				}
				$structured_data['nutrition'][ $values->structured_data_name ] = $value;
			}
		}
	}

	// Apply filters to allow filtering structured data.
	$structured_data = apply_filters( 'yummy_filter_recipe_structured_data', $structured_data, $post );

	if ( ! empty( $structured_data ) ) {
		?>
		<script type="application/ld+json">
			<?php echo wp_json_encode( $structured_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ); ?>
		</script>
		<?php
	}
}

/**
 * Outputs 'ItemList' structured data for yummy/recipe-collection block.
 *
 * @param  WP_Query $wp_query WP_Query.
 */
function yummy_output_recipe_collection_structured_data_json( $wp_query ) {

	$structured_data = array();

	// Add ItemList if post or page has a list of recipes.
	// https://developers.google.com/search/docs/data-types/recipe#carousel-example.
	if ( $wp_query->have_posts() ) {
		$structured_data = array(
			'@context' => 'https://schema.org/',
			'@type'    => 'ItemList',
		);

		$posts = array();
		$x     = 1;

		while ( $wp_query->have_posts() ) {
			$wp_query->the_post();
			$posts[] = array(
				'@type'    => 'ListItem',
				'position' => $x,
				'url'      => get_permalink(),
			);

			$x++;
		}

		$structured_data['itemListElement'] = $posts;
	}

	// Apply filters to allow filtering structured data.
	$structured_data = apply_filters( 'yummy_filter_list_structured_data', $structured_data );

	if ( ! empty( $structured_data ) ) {
		?>
		<script type="application/ld+json">
			<?php echo wp_json_encode( $structured_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ); ?>
		</script>
		<?php
	}
}
