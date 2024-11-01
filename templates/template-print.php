<?php
/**
 * Template-print.php
 *
 * This template can be overridden by copying it to yourtheme/yummy/template-print.php.
 *
 * @package yummy-recipes
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<title><?php the_title(); ?></title>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex,follow">
	<link rel="canonical" href="<?php the_permalink(); ?>">
	<?php wp_site_icon(); ?>

	<?php do_action( 'yummy_action_print_template_head' ); ?>
</head>
<body>

<div class="yummy-print">
	<?php do_action( 'yummy_action_print_template_after_opening_tag' ); ?>

	<button type="button" class="yummy-hide-on-print" onclick="window.print();event.preventDefault();"><?php esc_html_e( 'Print', 'yummy-recipes' ); ?></button>

	<h1><?php echo esc_html( $recipe->get_title() ); ?></h1>

	<?php if ( yummy_show_author_in( 'print' ) ) : ?>
		<div class="yummy-print-author">
			<?php echo esc_html( $recipe->get_author_name() ); ?>
		</div>
	<?php endif; ?>

	<?php if ( $recipe->get_prep_time_formatted() ) : ?>
		<?php yummy_inline_svg( 'clock' ); ?>
		<?php esc_html_e( 'Prep', 'yummy-recipes' ); ?>
		<?php echo esc_html( $recipe->get_prep_time_formatted() ); ?>
	<?php endif; ?>

	<?php if ( $recipe->get_cook_time_formatted() ) : ?>
		<?php yummy_inline_svg( 'clock' ); ?>
		<?php esc_html_e( 'Cook', 'yummy-recipes' ); ?>
		<?php echo esc_html( $recipe->get_cook_time_formatted() ); ?>
	<?php endif; ?>

	<?php if ( $recipe->get_yield() ) : ?>
		<?php yummy_inline_svg( 'yield' ); ?>
		<?php esc_html_e( 'Yield', 'yummy-recipes' ); ?>
		<?php echo esc_html( $recipe->get_yield() ); ?>
	<?php endif; ?>

	<?php if ( $recipe->get_description() ) : ?>
		<div class="yummy-print-description">
			<?php echo wp_kses_post( wpautop( $recipe->get_description() ) ); ?>
		</div>
	<?php endif; ?>

	<?php if ( $recipe->get_taxonomies() ) : ?>
		<div class="yummy-print-taxonomies">
			<?php foreach ( $recipe->get_taxonomies() as $yummy_taxonomy ) : ?>
				<?php echo get_the_term_list( get_the_ID(), $yummy_taxonomy->name, '<b class="yummy-print-taxonomy-title">' . $yummy_taxonomy->labels->singular_name . '</b> ', ', ', '' ); ?>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<hr>

	<?php if ( $recipe->get_servings() ) : ?>
		<?php // Translators: %d: Number of servings. ?>
		<p><?php printf( esc_html__( '%d servings', 'yummy-recipes' ), absint( $recipe->get_servings() ) ); ?></p>
	<?php endif; ?>

	<?php if ( $recipe->get_ingredient_lists() ) : ?>
		<div class="yummy-print-ingredients">
			<h3 class="yummy-print-subtitle"><?php esc_html_e( 'Ingredients', 'yummy-recipes' ); ?></h3>

			<?php foreach ( $recipe->get_ingredient_lists() as $yummy_ingredient_list ) : ?>

				<div class="yummy-ingredient-lists">
					<h4 class="yummy-print-subtitle"><?php echo esc_html( $yummy_ingredient_list->title ); ?></h4>

					<ul class="yummy-print-ingredients-list">
						<?php foreach ( $yummy_ingredient_list->ingredients as $yummy_ingredient ) : ?>
							<li class="yummy-ingredient">
								<span class="yummy-print-ingredient-amount"><?php echo wp_kses_post( $yummy_ingredient->amount ); ?></span>
								<span class="yummy-print-ingredient-unit"><?php echo wp_kses_post( $yummy_ingredient->unit ); ?></span>
								<span class="yummy-print-ingredient-name"><?php echo wp_kses_post( $yummy_ingredient->name ); ?></span>
							</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $recipe->get_instructions() ) ) : ?>
		<div class="yummy-print-instructions">
			<h3 class="yummy-print-subtitle"><?php esc_html_e( 'Instructions', 'yummy-recipes' ); ?></h3>

			<?php echo wp_kses_post( $recipe->get_instructions() ); ?>
		</div>
	<?php endif; ?>

	<hr>

	<?php if ( yummy_get_option( 'display_nutrition_facts_on_print' ) && $recipe->has_nutrition_facts() ) : ?>
		<div class="yummy-print-nutrition-facts">
			<?php yummy_get_template_part( 'template-print-nutrition-facts', array( 'recipe' => $recipe ) ); ?>
		</div>
	<?php endif; ?>

	<?php do_action( 'yummy_action_print_template_before_closing_tag' ); ?>
</div>

</body>
</html>
