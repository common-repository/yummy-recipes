<?php

/**
 * Recipe-card/template-top.php
 *
 * This template can be overridden by copying it to yourtheme/yummy/recipe-card/template-top.php.
 *
 * @package yummy-recipes
 * @version 1.0.0
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="yummy-big-card-top">
	<?php 
do_action( 'yummy_action_big_card_top_after_opening_tag' );
?>

	<div class="yummy-big-card-image">
		<?php 
echo  wp_get_attachment_image( $recipe->get_image_id(), ( 'hero' === get_option( 'yummy_big_card_style' ) ? 'yummy-800x600' : 'yummy-320x320' ) ) ;
?>
	</div>

	<div class="yummy-big-card-top-content">
		<h2 class="yummy-big-card-title"><?php 
echo  esc_html( $recipe->get_title() ) ;
?></h2>

		<?php 

if ( yummy_show_author_in( 'big_card' ) ) {
    ?>
			<div class="yummy-big-card-author">
				<a href="<?php 
    echo  esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) ;
    ?>">
					<?php 
    echo  get_avatar(
        get_the_author_meta( 'ID' ),
        32,
        '',
        'Avatar',
        array(
        'class' => 'yummy-avatar',
    )
    ) ;
    echo  esc_html( $recipe->get_author_name() ) ;
    ?>
				</a>
			</div>
		<?php 
}

?>

		<?php 
?>

		<div class="yummy-big-card-top-details">
			<?php 

if ( $recipe->get_prep_time_formatted() ) {
    ?>
				<div class="yummy-big-card-meta">
					<?php 
    yummy_inline_svg( 'clock' );
    ?>
					<span class="yummy-big-card-meta-title"><?php 
    esc_html_e( 'Prep', 'yummy-recipes' );
    ?></span>
					<?php 
    echo  esc_html( $recipe->get_prep_time_formatted() ) ;
    ?>
				</div>
			<?php 
}

?>

			<?php 

if ( $recipe->get_cook_time_formatted() ) {
    ?>
				<div class="yummy-big-card-meta">
					<?php 
    yummy_inline_svg( 'clock' );
    ?>
					<span class="yummy-big-card-meta-title"><?php 
    esc_html_e( 'Cook', 'yummy-recipes' );
    ?></span>
					<?php 
    echo  esc_html( $recipe->get_cook_time_formatted() ) ;
    ?>
				</div>
			<?php 
}

?>

			<?php 

if ( $recipe->get_yield() ) {
    ?>
				<div class="yummy-big-card-meta">
					<?php 
    yummy_inline_svg( 'yield' );
    ?>
					<span class="yummy-big-card-meta-title"><?php 
    esc_html_e( 'Yield', 'yummy-recipes' );
    ?></span>
					<?php 
    echo  esc_html( $recipe->get_yield() ) ;
    ?>
				</div>
			<?php 
}

?>
		</div>
	</div>

	<?php 
do_action( 'yummy_action_big_card_top_before_closing_tag' );
?>
</div><!-- /.yummy-big-card-top -->
