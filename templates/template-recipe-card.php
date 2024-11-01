<?php
/**
 * Template-recipe-card.php
 *
 * This template can be overridden by copying it to yourtheme/yummy/template-recipe-card.php.
 *
 * @package yummy-recipes
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<div <?php yummy_card_class( 'big_card', 'yummy-big-card' ); ?> id="yummy-recipe-anchor">
	<?php do_action( 'yummy_action_big_card_after_opening_tag' ); ?>

	<?php do_action( 'yummy_action_big_card_top', $recipe ); ?>

	<?php do_action( 'yummy_action_big_card_middle', $recipe ); ?>

	<?php do_action( 'yummy_action_big_card_bottom', $recipe ); ?>

	<?php do_action( 'yummy_action_big_card_before_closing_tag' ); ?>
</div>
