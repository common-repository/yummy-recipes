<?php
/**
 * Recipe-card/template-description.php
 *
 * This template can be overridden by copying it to yourtheme/yummy/recipe-card/template-description.php.
 *
 * @package yummy-recipes
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="yummy-big-card-description">
	<?php echo wp_kses_post( wpautop( $recipe->get_description() ) ); ?>
</div>
