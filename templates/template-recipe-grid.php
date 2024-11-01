<?php
/**
 * Template-recipe-grid.php
 *
 * This template can be overridden by copying it to yourtheme/yummy/template-recipe-grid.php.
 *
 * @package yummy-recipes
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="yummy-card-grid yummy-card-grid-recipes">
	<?php do_action( 'yummy_action_recipe_grid_after_opening_tag' ); ?>

	<?php while ( $wp_query_recipes->have_posts() ) : ?>
		<?php $wp_query_recipes->the_post(); ?>
		<?php yummy_get_template_part( 'template-recipe-card-small', array( 'recipe' => yummy_get_recipe_object( get_the_ID() ) ) ); ?>
	<?php endwhile; ?>

	<?php do_action( 'yummy_action_recipe_grid_before_closing_tag' ); ?>
</div>
