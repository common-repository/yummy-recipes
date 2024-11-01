<?php
/**
 * Helpers.php
 *
 * @package yummy-recipes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Removes hentry class from recipes, as they are already using schema.org for the structured data.
 *
 * @param  array $class Current classes.
 *
 * @return array        Returned classes.
 */
function yummy_remove_hentry_post_class( $class ) {
	if ( array_search( 'type-yummy_recipe', $class, true ) ) {
		$class = array_diff( $class, array( 'hentry' ) );
	}

	return $class;
}

if ( ! function_exists( 'yummy_minutes_to_hr_min' ) ) {
	/**
	 * Displays time in hours and minutes.
	 *
	 * @param string $minutes Time in minutes.
	 */
	function yummy_minutes_to_hr_min( $minutes ) {

		$minutes = absint( $minutes );

		if ( ! empty( $minutes ) ) {
			if ( $minutes < 60 ) {
				$return = $minutes . ' ' . __( 'min', 'yummy-recipes' );
			} elseif ( $minutes >= 60 ) {
				if ( 0 === $minutes % 60 ) {
					$return = $minutes / 60 . ' ' . __( 'hr', 'yummy-recipes' );
				} else {
					$hours   = floor( $minutes / 60 );
					$minutes = ( $minutes % 60 );
					// Translators: 1: Hours 2: Minutes.
					$return = sprintf( esc_html__( '%1$s hr %2$s min', 'yummy-recipes' ), $hours, $minutes );
				}
			}

			return $return;
		}

		return false;
	}
}

if ( ! function_exists( 'yummy_hex2rgb' ) ) {
	/**
	 * Converts HEX color value to RGB.
	 *
	 * @param string $hex Hex color value.
	 */
	function yummy_hex2rgb( $hex ) {

		$hex = str_replace( '#', '', $hex );

		$r = hexdec( substr( $hex, 0, 2 ) );
		$g = hexdec( substr( $hex, 2, 2 ) );
		$b = hexdec( substr( $hex, 4, 2 ) );

		return $r . ', ' . $g . ', ' . $b;
	}
}

if ( ! function_exists( 'yummy_get_contrast' ) ) {
	/**
	 * Calculates color contrast.
	 * https://24ways.org/2010/calculating-color-contrast/
	 *
	 * @param  string $hex          Hex color value.
	 * @param  string $dark_return  String to return when color is dark.
	 * @param  string $light_return String to return when color is light.
	 * @return string
	 */
	function yummy_get_contrast( $hex, $dark_return, $light_return ) {

		$rgb = yummy_hex2rgb( $hex );

		list( $r, $g, $b ) = explode( ', ', $rgb );

		$yiq = ( ( $r * 299 ) + ( $g * 587 ) + ( $b * 114 ) ) / 1000;

		return ( $yiq >= 155 ) ? $light_return : $dark_return;
	}
}

/**
 * Multi checkbox sanitization callback for the customizer.
 *
 * @param  array $values Values.
 *
 * @return array
 */
function yummy_customizer_sanitize_array( $values ) {

	$multi_values = ! is_array( $values ) ? explode( ',', $values ) : $values;

	return ! empty( $multi_values ) ? array_map( 'sanitize_text_field', $multi_values ) : array();
}

if ( ! function_exists( 'yummy_get_print_url' ) ) {
	/**
	 * Returns URL for the recipe's print page.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return string
	 */
	function yummy_get_print_url( $post_id ) {
		$url  = trailingslashit( get_the_permalink( $post_id ) );
		$url .= Yummy_Printing::get_print_url_slug();

		return $url;
	}
}

if ( ! function_exists( 'yummy_remove_links_from_term_links' ) ) {
	/**
	 * Strips tags from links returned by the get_term_links function.
	 *
	 * @param  array $links Links.
	 * @return array
	 */
	function yummy_remove_links_from_term_links( $links ) {

		foreach ( $links as $key => $url ) {
			$return[ $key ] = wp_strip_all_tags( $url );
		}

		return $return;
	}
}

if ( ! function_exists( 'yummy_filter_term_links_output' ) ) {
	/**
	 * Adds filters to remove links from term links.
	 */
	function yummy_filter_term_links_output() {
		$taxonomies = get_object_taxonomies( 'yummy_recipe', 'names' );
		foreach ( $taxonomies as $taxonomy_name ) {
			add_filter( 'term_links-' . $taxonomy_name, 'yummy_remove_links_from_term_links', 100 );
		}
	}
}
if ( empty( yummy_get_option( 'link_card_taxonomy_terms_to_archives' ) ) ) {
	add_action( 'init', 'yummy_filter_term_links_output', 999 );
}

if ( ! function_exists( 'yummy_get_share_links' ) ) {
	/**
	 * Returns share links which are enabled in the options.
	 */
	function yummy_get_share_links() {

		$displayed_share_links = get_option( 'yummy_share_links', array( 'facebook', 'pinterest', 'twitter' ) );

		$image_url = '';

		if ( has_post_thumbnail() ) {
			$image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full', true )[0];
		}

		$share_urls = array(
			'facebook'  => array(
				'Facebook',
				'https://www.facebook.com/sharer.php?u=' . wp_get_shortlink(),
			),
			'twitter'   => array(
				'Twitter',
				'https://twitter.com/share?url=' . wp_get_shortlink() . '&text=' . rawurlencode( get_the_title() ),
			),
			'pinterest' => array(
				'Pinterest',
				'https://pinterest.com/pin/create/button/?url=' . wp_get_shortlink() . '&media=' . $image_url . '&description=' . rawurlencode( get_the_title() ),
			),
		);

		$return = array();

		if ( ! empty( $displayed_share_links ) ) {
			foreach ( $share_urls as $slug => $values ) {
				if ( in_array( $slug, $displayed_share_links, true ) ) {
					$item = new StdClass();

					$item->title = $values[0];
					$item->url   = $values[1];

					$return[ $slug ] = $item;
				}
			}
		}

		return $return;
	}
}

if ( ! function_exists( 'yummy_card_class' ) ) {
	/**
	 * Outputs class names for the cards.
	 *
	 * @param  string          $slug    Card slug.
	 * @param  string|string[] $classes Space-separated string or array of class names to add to the class list.
	 */
	function yummy_card_class( $slug, $classes ) {

		$return = array();

		if ( ! empty( $classes ) ) {
			if ( is_string( $classes ) ) {
				$return = array_merge( $return, explode( ' ', $classes ) );
			} elseif ( is_array( $classes ) ) {
				$return = array_merge( $return, $classes );
			}
		}

		if ( 'big_card' === $slug ) {
			$return[] = 'yummy-big-card-style-' . get_option( 'yummy_big_card_style', 'classic' );

			// Is the card's top background color light or dark?
			$color_background_card_top = get_option( 'yummy_color_big_card_top' );

			if ( ! empty( $color_background_card_top ) ) {
				$class_top = yummy_get_contrast( $color_background_card_top, 'yummy-big-card-top-is-dark', 'yummy-big-card-top-is-light' );
				$return[]  = $class_top;
			}

			// Is the instructions step background color light or dark?
			$color_instructions_step = get_option( 'yummy_color_instructions_step_background' );

			if ( ! empty( $color_instructions_step ) ) {
				$class_step = yummy_get_contrast( $color_instructions_step, 'yummy-instructions-step-is-dark', 'yummy-instructions-step-is-light' );
				$return[]   = $class_step;
			}
		} elseif ( 'small_card' === $slug ) {
			$return[] = 'yummy-small-card-style-' . get_option( 'yummy_small_card_style', 'classic' );

			if ( 'classic' === get_option( 'yummy_small_card_style', 'classic' ) ) {
				$return[] = 'yummy-small-card-is-light';
			} elseif ( 'overlay' === get_option( 'yummy_small_card_style', 'classic' ) ) {
				$return[] = 'yummy-small-card-is-dark';
			} else {
				// Is the small card background color light or dark?
				$color_background = get_option( 'yummy_color_small_card_background' );

				if ( ! empty( $color_background ) ) {
					$class_small_card = yummy_get_contrast( $color_background, 'yummy-small-card-is-dark', 'yummy-small-card-is-light' );
					$return[]         = $class_small_card;
				}
			}
		}

		// Apply filters to allow modifying classes with filters.
		$return = apply_filters( 'yummy_filter_card_classes_' . $slug, $return );

		echo 'class="' . esc_attr( implode( ' ', $return ) ) . '"';
	}
}

if ( ! function_exists( 'yummy_get_wp_kses_allowed_html' ) ) {
	/**
	 * Returns allowed HTML for wp_kses functions.
	 *
	 * @param  string $type Type.
	 *
	 * @return array
	 */
	function yummy_get_wp_kses_allowed_html( $type ) {

		$allowed_tags = array();

		$kses_defaults = wp_kses_allowed_html( 'post' );

		if ( 'svg_icon' === $type ) {

			$svg_args = array(
				'svg'   => array(
					'class'           => true,
					'aria-hidden'     => true,
					'aria-labelledby' => true,
					'role'            => true,
					'xmlns'           => true,
					'xmlns:xlink'     => true,
					'version'         => true,
					'width'           => true,
					'height'          => true,
					'viewbox'         => true,
					'viewBox'         => true,
				),
				'g'     => array( 'fill' => true ),
				'title' => array( 'title' => true ),
				'path'  => array(
					'd'       => true,
					'fill'    => true,
					'opacity' => true,
					'class'   => true,
				),
			);

			$allowed_tags = array_merge( $kses_defaults, $svg_args );
		}

		$allowed_tags = apply_filters( 'yummy_filter_get_wp_kses_allowed_html', $allowed_tags, $type );

		return $allowed_tags;
	}
}

if ( ! function_exists( 'yummy_include_recipes_in_blog_post_loop' ) ) {
	/**
	 * Includes recipes in selected loops an option is enabled.
	 *
	 * @param object $query WP_Query.
	 */
	function yummy_include_recipes_in_blog_post_loop( $query ) {

		$include = false;
		$show_in = yummy_get_option( 'show_recipes_in_loops' );

		if ( $query->is_main_query() && is_array( $show_in ) ) {
			if ( in_array( 'home', $show_in, true ) && is_home() ) {
				$include = true;
			} elseif ( in_array( 'archives', $show_in, true ) && is_archive() && ! is_author() ) {
				$include = true;
			} elseif ( in_array( 'author', $show_in, true ) && is_author() ) {
				$include = true;
			}
		}

		if ( $include ) {
			$query->set( 'post_type', array( 'post', 'yummy_recipe' ) );
		}
	}
}
if ( ! is_admin() && is_array( yummy_get_option( 'show_recipes_in_loops' ) ) ) {
	add_action( 'pre_get_posts', 'yummy_include_recipes_in_blog_post_loop', 999 );
}

/**
 * Returns array of sorting options for the recipe search results.
 *
 * @param string $orderby              Order by.
 * @param string $order                Order.
 * @param string $displayed_sorting    Selected sorting options from block attributes.
 */
function yummy_get_sorting_options( $orderby, $order, $displayed_sorting ) {

	$current_value = yummy_get_sorting_current_value( $orderby, $order );

	$return = array();

	$sorts = yummy_get_sorting_options_array();

	// Build all values here for cleaner template code. Use values also as array keys for easier filtering.
	if ( ! empty( $sorts ) && ! empty( $displayed_sorting ) ) {
		foreach ( $sorts as $key => $values ) {
			// Check if the sorting is enabled in block attributes (displayed_sorting).
			if ( in_array( $key, $displayed_sorting, true ) ) {
				$object = new StdClass();

				$object->value            = $key;
				$object->name             = $values[2];
				$object->id               = 'yummy-id-sort-radio-' . $key;
				$object->is_current_value = ( $current_value === $key ? true : false );

				$return[ $key ] = $object;
			}
		}
	}

	$return = apply_filters( 'yummy_filter_get_sorting_options', $return );

	return $return;
}

/**
 * Returns sorting options.
 */
function yummy_get_sorting_options_array() {

	// Note: Using an 'en' dash for A – Z and Z – A, as esc_html converts normal dashed to en dashes.
	$sorts = array(
		'date_desc'  => array( 'date', 'desc', __( 'Newest', 'yummy-recipes' ) ),
		'date_asc'   => array( 'date', 'asc', __( 'Oldest', 'yummy-recipes' ) ),
		'title_asc'  => array( 'title', 'asc', __( 'A – Z', 'yummy-recipes' ) ),
		'title_desc' => array( 'title', 'desc', __( 'Z – A', 'yummy-recipes' ) ),
	);

	// Add rating sort option if ratings are enabled.
	if ( yummy_get_option( 'ratings_enabled' ) ) {
		$rating = array( 'rating', 'desc', __( 'Best rated', 'yummy-recipes' ) );

		$sorts['rating_desc'] = $rating;
	}

	return $sorts;
}

/**
 * Returns current sorting value.
 *
 * @param string $orderby  Order by.
 * @param string $order    Order.
 */
function yummy_get_sorting_current_value( $orderby, $order ) {

	if ( is_array( $orderby ) || 'meta_value_num' === $orderby ) {
		$current_value = 'rating_desc';
	} else {
		$current_value = $orderby . '_' . $order;
	}

	return $current_value;
}

/**
 * Sets the post type for AJAX queries explicitly to make sure that only recipes are displayed.
 *
 * @param WP_Query $query WP_Query.
 */
function yummy_pre_get_posts_set_post_type( $query ) {
	$post_type = $query->get( 'post_type' );
	if ( wp_doing_ajax() && $query->is_search && ! $query->is_main_query() && 'yummy_recipe' === $post_type ) {
		$query->set( 'post_type', 'yummy_recipe' );
	}
}
add_filter( 'pre_get_posts', 'yummy_pre_get_posts_set_post_type', 999 );

/**
 * Returns URL for the term image.
 *
 * @param  int $term_id  Term ID.
 *
 * @return string        Image URL.
 */
function yummy_get_term_image_url( $term_id ) {

	$term_image_url = get_term_meta( $term_id, '_yummy_term_meta_image_url', true );
	if ( ! empty( $term_image_url ) ) {
		return $term_image_url;
	}

	$default_thumbnail_url = get_option( 'yummy_default_thumbnail_image_url' );
	if ( ! empty( $default_thumbnail_url ) ) {
		return $default_thumbnail_url;
	}

	$image_url = yummy_upload_default_image();
	if ( ! empty( $image_url ) ) {
		return $image_url;
	}
}

/**
 * Deletes transients for related recipes.
 *
 * @param int $post_id Post ID.
 */
function yummy_delete_related_recipes_transients( $post_id = null ) {

	if ( ! empty( $post_id ) ) {
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		$post_type = get_post_type( $post_id );

		if ( 'recipe' !== $post_type ) {
			return;
		}
	}

	global $wpdb;

	$wpdb->query(
		$wpdb->prepare(
			'DELETE FROM ' . $wpdb->options . ' WHERE (option_name LIKE %s OR option_name LIKE %s)',
			'_transient_yummy_related_recipes_to_post_id_%',
			'_transient_timeout_yummy_related_recipes_to_post_id_%'
		)
	);
}
add_action( 'save_post_yummy_recipe', 'yummy_delete_related_recipes_transients' );

/**
 * Builds an array of nutrition options.
 */
function yummy_get_nutrition_options() {

	$nutrition_options = array();

	$nutrition_options[] = array(
		'name'                 => __( 'Servings', 'yummy-recipes' ),
		'structured_data_name' => 'servingSize',
	);

	$nutrition_options[] = array(
		'name'                 => __( 'Calories in each serving', 'yummy-recipes' ),
		'structured_data_name' => 'calories',
	);

	$nutrition_options[] = array(
		'name'                 => __( 'Total Fat', 'yummy-recipes' ),
		'structured_data_name' => 'fatContent',
		'unit'                 => __( 'g', 'yummy-recipes' ),
		'daily_value'          => 78,
	);

	$nutrition_options[] = array(
		'name'                 => __( 'Saturated Fat', 'yummy-recipes' ),
		'structured_data_name' => 'saturatedFatContent',
		'unit'                 => __( 'g', 'yummy-recipes' ),
		'daily_value'          => 20,
		'parent_field'         => 'fatContent',
	);

	$nutrition_options[] = array(
		'name'                 => __( 'Unsaturated Fat', 'yummy-recipes' ),
		'structured_data_name' => 'unsaturatedFatContent',
		'unit'                 => __( 'g', 'yummy-recipes' ),
		'parent_field'         => 'fatContent',
	);

	$nutrition_options[] = array(
		'name'                 => __( 'Trans Fat', 'yummy-recipes' ),
		'structured_data_name' => 'transFatContent',
		'unit'                 => __( 'g', 'yummy-recipes' ),
		'parent_field'         => 'fatContent',
	);

	$nutrition_options[] = array(
		'name'                 => __( 'Cholesterol', 'yummy-recipes' ),
		'structured_data_name' => 'cholesterolContent',
		'unit'                 => _x( 'mg', 'The abbreviation for milligram', 'yummy-recipes' ),
		'daily_value'          => 300,
	);

	$nutrition_options[] = array(
		'name'                 => __( 'Sodium', 'yummy-recipes' ),
		'structured_data_name' => 'sodiumContent',
		'unit'                 => _x( 'mg', 'The abbreviation for milligram', 'yummy-recipes' ),
		'daily_value'          => 2300,
	);

	$nutrition_options[] = array(
		'name'                 => __( 'Total Carbohydrate', 'yummy-recipes' ),
		'structured_data_name' => 'carbohydrateContent',
		'unit'                 => __( 'g', 'yummy-recipes' ),
		'daily_value'          => 275,
	);

	$nutrition_options[] = array(
		'name'                 => __( 'Dietary Fiber', 'yummy-recipes' ),
		'structured_data_name' => 'fiberContent',
		'unit'                 => __( 'g', 'yummy-recipes' ),
		'parent_field'         => 'carbohydrateContent',
		'daily_value'          => 28,
	);

	$nutrition_options[] = array(
		'name'                 => __( 'Sugars', 'yummy-recipes' ),
		'structured_data_name' => 'sugarContent',
		'unit'                 => __( 'g', 'yummy-recipes' ),
		'parent_field'         => 'carbohydrateContent',
		'daily_value'          => 50,
	);

	$nutrition_options[] = array(
		'name'                 => __( 'Protein', 'yummy-recipes' ),
		'structured_data_name' => 'proteinContent',
		'unit'                 => __( 'g', 'yummy-recipes' ),
		'daily_value'          => 50,
	);

	$nutrition_options = apply_filters( 'yummy_filter_nutrition_options', $nutrition_options );

	return $nutrition_options;
}

/**
 * Checks if the recipe author should be displayed on a specified location.
 *
 * @param string $template Location.
 *
 * @return boolean
 */
function yummy_show_author_in( $template ) {
	if ( in_array( $template, get_option( 'yummy_display_author', array( 'big_card', 'small_card', 'print' ) ), true ) ) {
		return true;
	}

	return false;
}
