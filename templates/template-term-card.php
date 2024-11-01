<?php
/**
 * Template-term-card.php
 *
 * This template can be overridden by copying it to yourtheme/yummy/template-term-card.php.
 *
 * @package yummy-recipes
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<div <?php yummy_card_class( 'small_card', 'yummy-small-card yummy-small-card-term' ); ?>>
	<?php do_action( 'yummy_action_term_card_after_opening_tag' ); ?>

	<a href="<?php echo esc_url( get_term_link( $term ) ); ?>">
		<div class="yummy-small-card-image">
			<img src="<?php echo esc_url( $term_image_url ); ?>" alt="" width="160" height="160" loading="lazy">
		</div>

		<div class="yummy-small-card-content">
			<div class="yummy-small-card-content-title"><?php echo esc_html( $term->name ); ?></div>

			<?php if ( $show_count ) : ?>
				<div class="yummy-small-card-content-count">
					<?php // Translators: %d: Number of recipes. ?>
					<?php printf( esc_html( _n( '%d recipe', '%d recipes', $found_posts, 'yummy-recipes' ) ), absint( $found_posts ) ); ?>
				</div>
			<?php endif; ?>
		</div>
	</a>

	<?php do_action( 'yummy_action_term_card_before_closing_tag' ); ?>
</div>
