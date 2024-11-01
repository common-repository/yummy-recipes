<?php

/**
 * Recipe-card/template-buttons.php
 *
 * This template can be overridden by copying it to yourtheme/yummy/recipe-card/template-buttons.php.
 *
 * @package yummy-recipes
 * @version 1.0.0
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="yummy-big-card-buttons">
	<?php 
do_action( 'yummy_action_big_card_buttons_after_opening_tag' );
?>

	<?php 

if ( !empty($share_links) ) {
    ?>
		<?php 
    foreach ( $share_links as $yummy_share_link_slug => $yummy_share_link ) {
        ?>
			<a href="<?php 
        echo  esc_url( $yummy_share_link->url ) ;
        ?>" class="yummy-icon-button yummy-icon-button-social yummy-icon-button-social-<?php 
        echo  esc_attr( $yummy_share_link_slug ) ;
        ?>" target="_blank" rel="noopener"><?php 
        yummy_inline_svg( $yummy_share_link_slug );
        ?><span class="screen-reader-text"><?php 
        echo  esc_html( $yummy_share_link->title ) ;
        ?></span></a>
		<?php 
    }
    ?>
	<?php 
}

?>

	<?php 

if ( $display_print_button || $bookmarks_enabled ) {
    ?>
		<div class="yummy-big-card-buttons-print-bookmark">
			<?php 
    
    if ( $display_print_button ) {
        ?>
				<a href="<?php 
        echo  esc_url( yummy_get_print_url( $recipe->get_post_id() ) ) ;
        ?>" class="yummy-icon-button yummy-icon-button-print" target="_blank" rel="nofollow"><?php 
        yummy_inline_svg( 'print' );
        ?><span class="screen-reader-text"><?php 
        esc_html_e( 'Print', 'yummy-recipes' );
        ?></span></a>
			<?php 
    }
    
    ?>

			<?php 
    ?>
		</div>
	<?php 
}

?>

	<?php 
do_action( 'yummy_action_big_card_buttons_before_closing_tag' );
?>
</div>
