<?php
/**
 * Recipe-card/template-ingredient-list.php
 *
 * This template can be overridden by copying it to yourtheme/yummy/recipe-card/template-ingredient-list.php.
 *
 * @package yummy-recipes
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="yummy-ingredient-list">
	<?php if ( ! empty( $ingredient_list->title ) ) : ?>
		<h4 class="yummy-big-card-subtitle"><?php echo esc_html( $ingredient_list->title ); ?></h4>
	<?php endif; ?>

	<ul class="yummy-ingredient-list-items">
		<?php foreach ( $ingredient_list->ingredients as $yummy_ingredient ) : ?>
			<li class="yummy-ingredient" data-yummy-ingredient-amount="<?php echo esc_attr( $yummy_ingredient->amount_numeric ); ?>" data-yummy-ingredient-amount-type="<?php echo esc_attr( $yummy_ingredient->amount_type ); ?>">
				<span class="yummy-ingredient-amount"><?php echo wp_kses_post( $yummy_ingredient->amount ); ?></span>
				<span class="yummy-ingredient-unit"><?php echo wp_kses_post( $yummy_ingredient->unit ); ?></span>
				<span class="yummy-ingredient-name"><?php echo wp_kses_post( $yummy_ingredient->name ); ?></span>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
