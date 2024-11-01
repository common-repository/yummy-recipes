<?php
/**
 * Recipe-card/template-instructions.php
 *
 * This template can be overridden by copying it to yourtheme/yummy/recipe-card/template-instructions.php.
 *
 * @package yummy-recipes
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="yummy-big-card-instructions yummy-instructions">
	<h3 class="yummy-big-card-subtitle"><?php echo esc_html( apply_filters( 'yummy_filter_card_instructions_title', __( 'Instructions', 'yummy-recipes' ) ) ); ?></h3>

	<?php echo wp_kses_post( $recipe->get_instructions() ); ?>
</div>
