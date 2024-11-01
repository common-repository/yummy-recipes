<?php
/**
 * Front-end-functions.php
 *
 * @package yummy-recipes
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'yummy_the_content_filter' ) ) {
	/**
	 * Filters the content of single recipe.
	 *
	 * @param  string $content Post content.
	 * @return string          Post content.
	 */
	function yummy_the_content_filter( $content ) {

		global $wp_query;

		// Only for recipes. Not on the print page.
		if ( is_singular( 'yummy_recipe' ) && ! array_key_exists( 'print', $wp_query->query_vars ) ) {

			// Show list of related recipes after the post content.
			$related_recipes_number = yummy_get_option( 'related_recipes_number' );
			if ( absint( $related_recipes_number ) > 0 ) {
				$content = $content . yummy_get_related_recipes( get_the_ID(), absint( $related_recipes_number ) );
			}

			// Show the jump to recipe and print buttons before the post content.
			if ( yummy_get_option( 'jump_to_recipe_button_before_content' ) || yummy_get_option( 'print_recipe_button_before_content' ) ) {
				$link = '<p class="yummy-before-post-content">';
				if ( yummy_get_option( 'jump_to_recipe_button_before_content' ) ) {
					$link .= '<a href="#yummy-recipe-anchor" class="yummy-button">' . esc_html__( 'Jump to recipe', 'yummy-recipes' ) . '</a>';
				}
				if ( yummy_get_option( 'print_recipe_button_before_content' ) ) {
					$link .= ' <a href="' . esc_url( yummy_get_print_url( get_the_ID() ) ) . '" class="yummy-button" target="_blank" rel="nofollow">' . esc_html__( 'Print', 'yummy-recipes' ) . '</a>';
				}
				$link .= '</p>';

				$link = apply_filters( 'yummy_filter_jump_to_recipe_html', $link );

				$content = $link . $content;
			}
		}

		return $content;
	}
}
add_filter( 'the_content', 'yummy_the_content_filter' );

if ( ! function_exists( 'yummy_get_related_recipes' ) ) {
	/**
	 * Outputs related recipes.
	 *
	 * @param  int $post_id Post ID.
	 * @param  int $number  Number of recipes to show.
	 */
	function yummy_get_related_recipes( $post_id, $number = null ) {

		$transient_key = 'yummy_related_recipes_to_post_id_' . $post_id;

		$ids = get_transient( $transient_key );

		if ( false === $ids ) {

			$wp_query_args = array(
				'post_type'      => 'yummy_recipe',
				'posts_per_page' => $number,
				'no_found_rows'  => true,
				'fields'         => 'ids',
				'post__not_in'   => array( $post_id ),
			);

			$taxonomies = get_object_taxonomies( 'yummy_recipe' );

			foreach ( $taxonomies as $taxonomy ) {
				$terms = get_the_terms( $post_id, $taxonomy );

				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
					// Get term IDs.
					$term_ids = wp_list_pluck( $terms, 'term_id' );

					$wp_query_args['tax_query']['relation'] = 'OR';
					$wp_query_args['tax_query'][]           = array( // phpcs:ignore slow query ok.
						'taxonomy' => $taxonomy,
						'terms'    => $term_ids,
					);
				}
			}

			$wp_query_args = apply_filters( 'yummy_filter_related_recipes_query_args', $wp_query_args );

			$wp_query_recipes_ids = new WP_Query( $wp_query_args );

			$ids = $wp_query_recipes_ids->posts;

			set_transient( $transient_key, $ids, MONTH_IN_SECONDS );
		}

		// Return, if there are no recipes to show.
		if ( empty( $ids ) ) {
			return;
		}

		$wp_query_recipes = new WP_Query(
			array(
				'post_type'      => 'yummy_recipe',
				'post__in'       => $ids,
				'orderby'        => 'post__in',
				'posts_per_page' => count( $ids ), // phpcs:ignore
				'no_found_rows'  => true,
			)
		);

		$return = '';
		if ( $wp_query_recipes->have_posts() ) {
			$return .= yummy_get_template_part_html( 'template-related-recipes', array( 'wp_query_recipes' => $wp_query_recipes ) );
		}

		wp_reset_postdata();

		return $return;
	}
}

/**
 * Returns HTML for the recipe index A-Z.
 *
 * @param  array $args Arguments.
 *
 * @return string Recipe index.
 */
function yummy_get_recipe_index_az( $args ) {

	$style = 'list';

	if ( ! empty( $args['style'] ) ) {
		$style = $args['style'];
	}

	$wp_query_args = array(
		'post_type'              => 'yummy_recipe',
		'orderby'                => 'title',
		'order'                  => 'ASC',
		'posts_per_page'         => -1,
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
	);

	$wp_query_index = new WP_Query( $wp_query_args );

	ob_start();
	?>

	<?php if ( $wp_query_index->have_posts() ) : ?>
		<div class="yummy-recipe-index yummy-recipe-index-az">
			<?php if ( true === $args['links'] ) : ?>
				<nav class="yummy-recipe-index-navigation yummy-buttons">
					<?php
					$alphabet = array();
					while ( $wp_query_index->have_posts() ) {
						$wp_query_index->the_post();
						$recipe       = yummy_get_recipe_object( get_the_ID() );
						$first_letter = substr( $recipe->get_title(), 0, 1 );

						// Add the first letter to our alphabet list.
						if ( ! isset( $alphabet[ $first_letter ] ) ) {
							$alphabet[ $first_letter ] = $first_letter;
							printf( '<a href="#yummy-recipe-index-anchor-%s" class="yummy-button">%s</a>', esc_attr( $first_letter ), esc_html( $first_letter ) );
						}
					}
					?>
				</nav>
			<?php endif; ?>

			<?php $current = null; ?>
			<?php $in_list = false; ?>

			<?php while ( $wp_query_index->have_posts() ) : ?>
				<?php $wp_query_index->the_post(); ?>
				<?php
				// Get the first letter of the post title.
				$first_letter = substr( get_the_title(), 0, 1 );

				// If the current first letter is different, start a new section.
				if ( $current !== $first_letter ) {
					if ( $in_list ) {
						if ( 'list' === $style ) {
							echo '</ul>';
						} elseif ( 'cards' === $style ) {
							echo '</div>';
						}
					}
					printf( '<h3 id="yummy-recipe-index-anchor-%s">%s</h3>', esc_attr( $first_letter ), esc_html( $first_letter ) );
					// Start sub-section.
					if ( 'list' === $style ) {
						echo '<ul>';
					} elseif ( 'cards' === $style ) {
						echo '<div class="yummy-card-grid yummy-card-grid-recipes">';
					}
					$in_list = true;
					$current = $first_letter;
				}
				?>

				<?php if ( 'list' === $style ) : ?>
					<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
				<?php elseif ( 'cards' === $style ) : ?>
					<?php yummy_get_template_part( 'template-recipe-card-small', array( 'recipe' => yummy_get_recipe_object( get_the_ID() ) ) ); ?>
				<?php endif; ?>

			<?php endwhile; ?>
			<?php if ( $in_list ) : ?>
				<?php
				if ( 'list' === $style ) {
					echo '</ul>';
				} elseif ( 'cards' === $style ) {
					echo '</div>';
				}
				?>
			<?php endif; ?>
		</div><!-- .yummy-recipe-index -->
	<?php endif; ?>
	<?php wp_reset_postdata(); ?>

	<?php
	return ob_get_clean();
}

/**
 * Returns HTML for the recipe index by taxonomy.
 *
 * @param  string $args Arguments.
 *
 * @return string Recipe index.
 */
function yummy_get_recipe_index_taxonomies( $args ) {

	$taxonomy = $args['taxonomy'];
	$style    = 'list';

	if ( ! empty( $args['style'] ) ) {
		$style = $args['style'];
	}

	$terms = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => true,
		)
	);

	ob_start();

	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
		?>

		<div class="yummy-recipe-index yummy-recipe-index-categories">
			<?php foreach ( $terms as $term ) : ?>
				<h3><?php echo esc_html( $term->name ); ?></h3>

				<?php
				$wp_query_args = array(
					'post_type'              => 'yummy_recipe',
					'orderby'                => 'title',
					'order'                  => 'ASC',
					'posts_per_page'         => -1,
					'no_found_rows'          => true,
					'update_post_meta_cache' => false,
					'tax_query'              => array( // phpcs:ignore slow query ok.
						array(
							'taxonomy' => $taxonomy,
							'field'    => 'slug',
							'terms'    => $term->slug,
						),
					),
				);

				$wp_query_recipes = new WP_Query( $wp_query_args );
				?>

				<?php if ( $wp_query_recipes->have_posts() ) : ?>

					<?php if ( 'list' === $style ) : ?>
						<ul>
							<?php while ( $wp_query_recipes->have_posts() ) : ?>
								<?php $wp_query_recipes->the_post(); ?>
								<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
							<?php endwhile; ?>
						</ul>

					<?php elseif ( 'cards' === $style ) : ?>
						<?php yummy_get_template_part( 'template-recipe-grid', array( 'wp_query_recipes' => $wp_query_recipes ) ); ?>
					<?php endif; ?>

				<?php endif; ?>
				<?php wp_reset_postdata(); ?>

			<?php endforeach; ?>
		</div><!-- .yummy-recipe-index -->
		<?php
	}

	return ob_get_clean();
}

/**
 * Displays powered by text after the recipe card block if the option is enabled.
 *
 * @param string $template_name Template name.
 */
function yummy_display_powered_by_text( $template_name ) {
	if ( yummy_get_option( 'display_powered_by_text' ) && 'template-recipe-card.php' === $template_name ) {
		echo '<p class="yummy-powered-by">Powered by <a href="https://nordwp.com/plugins/yummy-recipes">Yummy - WordPress Recipe Plugin</a></p>';
	}
}
add_action( 'yummy_action_after_template_part', 'yummy_display_powered_by_text' );
