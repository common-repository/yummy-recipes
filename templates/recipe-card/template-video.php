<?php
/**
 * Recipe-card/template-video.php
 *
 * This template can be overridden by copying it to yourtheme/yummy/recipe-card/template-video.php.
 *
 * @package yummy-recipes
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="yummy-big-card-video-embed">
	<?php echo wp_oembed_get( $recipe->get_video_url() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</div>
