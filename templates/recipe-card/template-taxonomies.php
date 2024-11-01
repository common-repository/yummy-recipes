<?php
/**
 * Recipe-card/template-taxonomies.php
 *
 * This template can be overridden by copying it to yourtheme/yummy/recipe-card/template-taxonomies.php.
 *
 * @package yummy-recipes
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="yummy-big-card-taxonomies">
	<?php foreach ( $recipe->get_taxonomies() as $yummy_taxonomy ) : ?>
		<?php echo get_the_term_list( get_the_ID(), $yummy_taxonomy->name, '<div class="yummy-big-card-taxonomies-title">' . $yummy_taxonomy->labels->singular_name . ': ', ', ', '</div> ' ); ?>
	<?php endforeach; ?>
</div>
