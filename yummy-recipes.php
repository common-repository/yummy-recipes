<?php

/**
 * Plugin Name: Yummy Recipes
 * Plugin URI: https://nordwp.com/plugins/yummy-recipes
 * Description: Yummy Recipes is a recipe plugin for WordPress. It helps you to share your tasty recipes to everyone.
 * Version: 1.2.0
 * Author: NordWP
 * Author URI: https://nordwp.com/
 * Text Domain: yummy-recipes
 * Domain Path: /languages
 * Requires at least: 5.6
 * Requires PHP: 5.6
 * License: GPLv2 or later
 *
 * @package yummy-recipes
 */
defined( 'ABSPATH' ) || exit;

if ( !function_exists( 'yummy_fs' ) ) {
    /**
     * Create a helper function for easy SDK access.
     */
    function yummy_fs()
    {
        global  $yummy_fs ;
        
        if ( !isset( $yummy_fs ) ) {
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            $yummy_fs = fs_dynamic_init( array(
                'id'             => '10488',
                'slug'           => 'yummy-recipes',
                'type'           => 'plugin',
                'public_key'     => 'pk_d927e8ed58ff86c312057c1cd55a3',
                'is_premium'     => false,
                'premium_suffix' => 'Premium',
                'has_addons'     => false,
                'has_paid_plans' => true,
                'trial'          => array(
                'days'               => 14,
                'is_require_payment' => false,
            ),
                'menu'           => array(
                'slug'    => 'yummy-options-page',
                'contact' => false,
                'support' => false,
            ),
                'is_live'        => true,
            ) );
        }
        
        return $yummy_fs;
    }
    
    // Init Freemius.
    yummy_fs();
    // Signal that SDK was initiated.
    do_action( 'yummy_fs_loaded' );
}

/**
 * Defines the plugin version.
 */
define( 'YUMMY_PLUGIN_VERSION', '1.2.0' );
define( 'YUMMY_DIR_PATH', plugin_dir_path( __FILE__ ) );
/**
 * Load plugin textdomain.
 */
function yummy_load_plugin_textdomain()
{
    load_plugin_textdomain( 'yummy-recipes', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

add_action( 'init', 'yummy_load_plugin_textdomain' );
/**
 * Sets a constant for the plugin URL.
 */
add_action( 'init', function () {
    define( 'YUMMY_BASE_PLUGIN_URL', plugins_url( '', __FILE__ ) );
} );
if ( !function_exists( 'yummy_get_option' ) ) {
    /**
     * Wrapper function around get_option().
     *
     * @param  string $key     Options array key.
     * @param  mixed  $default Optional default value.
     *
     * @return mixed           Option value.
     */
    function yummy_get_option( $key = '', $default = false )
    {
        // Get all options.
        $opts = get_option( 'yummy_options' );
        
        if ( is_array( $opts ) ) {
            if ( 'all' === $key ) {
                return $opts;
            }
            if ( array_key_exists( $key, $opts ) && false !== $opts[$key] ) {
                return $opts[$key];
            }
        }
        
        return $default;
    }

}
/**
 * Requires files from /includes folder.
 */

if ( is_admin() ) {
    require YUMMY_DIR_PATH . 'includes/class-yummy-permalink-settings.php';
    require YUMMY_DIR_PATH . 'includes/class-yummy-admin-page.php';
    require YUMMY_DIR_PATH . 'includes/class-yummy-term-meta.php';
}

require YUMMY_DIR_PATH . 'includes/class-yummy-post-types-taxonomies.php';
require YUMMY_DIR_PATH . 'includes/class-yummy-customizer.php';
require YUMMY_DIR_PATH . 'includes/class-yummy-blocks.php';
require YUMMY_DIR_PATH . 'includes/class-yummy-printing.php';
require YUMMY_DIR_PATH . 'includes/class-yummy-recipe.php';
require YUMMY_DIR_PATH . 'includes/yummy-recipe-helpers.php';
require YUMMY_DIR_PATH . 'includes/blocks-render.php';
require YUMMY_DIR_PATH . 'includes/front-end-functions.php';
require YUMMY_DIR_PATH . 'includes/icons.php';
require YUMMY_DIR_PATH . 'includes/styles-scripts.php';
require YUMMY_DIR_PATH . 'includes/structured-data.php';
require YUMMY_DIR_PATH . 'includes/template-functions.php';
require YUMMY_DIR_PATH . 'includes/template-hooks.php';
require YUMMY_DIR_PATH . 'includes/ingredient-helpers.php';
require YUMMY_DIR_PATH . 'includes/helpers.php';
/**
 * Registers new image sizes.
 */
add_image_size(
    'yummy-320x320',
    320,
    320,
    true
);
// 1x1.
add_image_size(
    'yummy-400x300',
    400,
    300,
    true
);
// 4x3
add_image_size(
    'yummy-400x225',
    400,
    225,
    true
);
// 16x9
add_image_size(
    'yummy-800x600',
    800,
    600,
    true
);
/**
 * Registers plugin functions to be run when the plugin is activated.
 */
register_activation_hook( YUMMY_DIR_PATH, 'yummy_plugin_activate_flush_rewrite_rules' );
register_activation_hook( __FILE__, 'yummy_plugin_activate_set_default_options' );
/**
 * Registers a plugin function to be run when the plugin is deactivated.
 */
register_deactivation_hook( YUMMY_DIR_PATH, 'yummy_plugin_deactivate' );
/**
 * Flush rewrite rules on theme activation.
 */
function yummy_plugin_activate_flush_rewrite_rules()
{
    flush_rewrite_rules();
}

/**
 * Set default plugin options on theme activation.
 */
function yummy_plugin_activate_set_default_options()
{
    Yummy_Admin_Page::set_default_options_values();
}

/**
 * Run on theme deactivation.
 */
function yummy_plugin_deactivate()
{
    // Flush rewrite rules.
    flush_rewrite_rules();
}

/**
 * Uploads the default image if it's not already uploaded.
 *
 * @return string|boolean
 */
function yummy_upload_default_image()
{
    $image_id = get_option( 'yummy_default_thumbnail_image_id' );
    $get_attachment = wp_get_attachment_image_src( $image_id );
    // If the image file is not found or the option is not set.
    
    if ( false === $get_attachment || !is_numeric( $image_id ) ) {
        // Upload default featured image.
        $uploaded_image_id = media_sideload_image(
            trailingslashit( YUMMY_BASE_PLUGIN_URL ) . 'assets/images/yummy-pattern.png',
            0,
            null,
            'id'
        );
        update_option( 'yummy_default_thumbnail_image_id', $uploaded_image_id );
        
        if ( is_numeric( $uploaded_image_id ) ) {
            $get_attachment = wp_get_attachment_image_src( $uploaded_image_id, 'yummy-320x320' );
            
            if ( is_array( $get_attachment ) ) {
                update_option( 'yummy_default_thumbnail_image_url', $get_attachment[0] );
                return $get_attachment[0];
            }
        
        }
    
    }
    
    return false;
}

add_action( 'admin_init', 'yummy_upload_default_image' );