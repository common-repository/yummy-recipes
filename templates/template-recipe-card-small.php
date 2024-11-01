<?php

/**
 * Template-recipe-card-small.php
 *
 * This template can be overridden by copying it to yourtheme/yummy/template-recipe-card-small.php.
 *
 * @package yummy-recipes
 * @version 1.0.0
 */
defined( 'ABSPATH' ) || exit;
?>
<div <?php 
yummy_card_class( 'small_card', 'yummy-small-card yummy-small-card-recipe' );
?>>
	<?php 
do_action( 'yummy_action_small_card_after_opening_tag' );
?>

	<a href="<?php 
the_permalink();
?>">
		<div class="yummy-small-card-image">
			<?php 
echo  wp_get_attachment_image( $recipe->get_image_id(), 'yummy-320x320' ) ;
?>
		</div>

		<div class="yummy-small-card-content">
			<?php 
?>

			<div class="yummy-small-card-content-title"><?php 
echo  esc_html( $recipe->get_title() ) ;
?></div>

			<?php 

if ( yummy_show_author_in( 'small_card' ) ) {
    ?>
				<div class="yummy-small-card-author">
					<?php 
    echo  get_avatar(
        get_the_author_meta( 'ID' ),
        24,
        '',
        'Avatar',
        array(
        'class' => 'yummy-avatar',
    )
    ) ;
    echo  esc_html( $recipe->get_author_name() ) ;
    ?>
				</div>
			<?php 
}

?>
		</div>
	</a>

	<?php 
do_action( 'yummy_action_small_card_before_closing_tag' );
?>
</div>
