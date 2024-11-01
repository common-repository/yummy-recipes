<?php

/**
 * Recipe-card/template-ingredients.php.
 *
 * This template can be overridden by copying it to yourtheme/yummy/recipe-card/template-ingredients.php.
 *
 * @package yummy-recipes
 * @version 1.0.0
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="yummy-big-card-ingredients">
	<div class="yummy-big-card-ingredients-top">
		<h3 class="yummy-big-card-subtitle"><?php 
echo  esc_html( apply_filters( 'yummy_filter_card_ingredients_title', __( 'Ingredients', 'yummy-recipes' ) ) ) ;
?></h3>

		<?php 

if ( yummy_get_option( 'show_servings_on_big_card' ) ) {
    ?>
			<?php 
    
    if ( $recipe->get_servings() ) {
        ?>
				<div class="yummy-big-card-servings">
					<?php 
        // Translators: %d: Number of servings.
        ?>
					<span class="yummy-servings"><?php 
        printf( esc_html__( '%s servings', 'yummy-recipes' ), '<span class="yummy-servings-counter">' . absint( $recipe->get_servings() ) . '</span>' );
        ?></span>

					<?php 
        ?>
				</div>
			<?php 
    }
    
    ?>
		<?php 
}

?>
	</div>

	<?php 
do_action( 'yummy_action_card_before_ingredient_lists' );
?>

	<?php 
foreach ( $recipe->get_ingredient_lists() as $yummy_ingredient_list ) {
    ?>
		<?php 
    yummy_get_template_part( 'recipe-card/template-ingredient-list', array(
        'ingredient_list' => $yummy_ingredient_list,
    ) );
    ?>
	<?php 
}
?>

	<?php 
do_action( 'yummy_action_card_after_ingredient_lists' );
?>
</div>
