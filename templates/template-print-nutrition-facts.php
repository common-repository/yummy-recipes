<?php
/**
 * Template-print-nutrition-facts.php
 *
 * This template can be overridden by copying it to yourtheme/yummy/template-print-nutrition-facts.php.
 *
 * @package yummy-recipes
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="yummy-nutrition-facts">
	<div class="yummy-nutrition-facts-header">
		<h3><?php esc_html_e( 'Nutrition Facts', 'yummy-recipes' ); ?></h3>

		<?php if ( $recipe->get_servings() ) : ?>
			<?php // Translators: %s: Number of servings. ?>
			<p><?php printf( esc_html__( 'Serving Size: %s', 'yummy-recipes' ), absint( $recipe->get_servings() ) ); ?></p>
		<?php endif; ?>

		<?php if ( $recipe->get_calories_per_serving() ) : ?>
			<?php // Translators: %s: Calories per serving. ?>
			<p><?php printf( esc_html__( 'Calories Per Serving: %s', 'yummy-recipes' ), floatval( $recipe->get_calories_per_serving() ) ); ?></p>
		<?php endif; ?>
	</div>

	<?php if ( ! empty( $recipe->get_nutrition_facts() ) ) : ?>
		<?php if ( yummy_get_option( 'display_daily_values' ) ) : ?>
			<div class="yummy-nutrition-facts-row-daily-value-title">
				<?php esc_html_e( '% Daily Value', 'yummy-recipes' ); ?>
			</div>
		<?php endif; ?>

		<div class="yummy-nutrition-facts-rows">
			<?php foreach ( $recipe->get_nutrition_facts() as $yummy_nutrient ) : ?>
				<div class="yummy-nutrition-facts-row<?php echo ( $yummy_nutrient->has_parent ? ' yummy-nutrition-facts-row-child' : '' ); ?>">
					<span class="yummy-nutrition-facts-item"><?php echo esc_html( $yummy_nutrient->name ); ?></span>
					<span class="yummy-nutrition-facts-item-amount <?php echo( empty( yummy_get_option( 'display_daily_values' ) ) ? 'yummy-nutrition-facts-item-right' : '' ); ?>">
						<?php // Translators: %1$s: Nutrient name %2$s: Nutrient amount. ?>
						&#8207;<?php printf( esc_html_x( '%1$s%2$s', 'Nutrient amount and unit in the nutrient facts.', 'yummy-recipes' ), esc_html( $yummy_nutrient->amount ), esc_html( $yummy_nutrient->unit ) ); ?>
					</span>
					<?php if ( yummy_get_option( 'display_daily_values' ) && ! empty( $yummy_nutrient->daily_value_percent ) ) : ?>
						<span class="yummy-nutrition-facts-item-right"><?php echo esc_html( $yummy_nutrient->daily_value_percent ); ?></span>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>
