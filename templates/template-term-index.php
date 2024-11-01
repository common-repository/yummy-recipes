<?php
/**
 * Template-term-index.php
 *
 * This template can be overridden by copying it to yourtheme/yummy/template-term-index.php.
 *
 * @package yummy-recipes
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="yummy-term-index">
	<h3><?php echo esc_html( $taxonomy->label ); ?></h3>

	<?php echo ( 'list' === $style ? '<ul>' : '<div class="yummy-card-grid yummy-card-grid-terms">' ); ?>

	<?php foreach ( $terms as $yummy_term ) : ?>

		<?php if ( 'list' === $style ) : ?>
			<li><a href="<?php echo esc_url( get_term_link( $yummy_term ) ); ?>"><?php echo esc_html( $yummy_term->name ); ?></a> <?php echo esc_html( ( $show_count ? '(' . absint( $yummy_term->count ) . ')' : '' ) ); ?></li>

		<?php elseif ( 'cards' === $style ) : ?>
			<?php
			$yummy_template_args = array(
				'term'           => $yummy_term,
				'found_posts'    => $yummy_term->count,
				'term_image_url' => yummy_get_term_image_url( $yummy_term->term_id ),
				'show_count'     => $show_count,
			);
			?>
			<?php yummy_get_template_part( 'template-term-card', $yummy_template_args ); ?>
		<?php endif; ?>

	<?php endforeach; ?>

	<?php echo ( 'list' === $style ? '</ul>' : '</div>' ); ?>
</div><!-- .yummy-term-index -->
