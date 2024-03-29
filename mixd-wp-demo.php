<?php
/**
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * and registers the activation and deactivation functions.
 *
 * @link              http://github.com/mixd
 * @author            thecodezombie
 * @version           1.0.0
 * @package           bgg-collection
 *
 * @wordpress-plugin
 * Plugin Name:       BoardGameGeek Collection
 * Plugin URI:        https://github.com/mixd
 * Description:       Import a user's boardgame collection from boardgamegeek.com
 * Version:           1.0.0
 * Author:            thecodezombie
 * Author URI:        http://thecodezombie.co.uk
 * License:           GPLv3
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       http://thecodezombie.co.uk
*/



/**
 * If we're not being loaded by WordPress, abort now
 */
if ( !defined( 'WPINC' ) ) { die; }


/**
 * Define the naming prefix
 *
 * @since 1.0.0
 * @return string
 */
function bgg_collection_var_prefix() {
    wp_cache_set( 'bgg_prefix', 'bgg_collection' );
}
add_action('init', 'bgg_collection_var_prefix');

/**
 * Load the Mixd Plugin foundation
 *
 * @since 1.0.0
 */
require_once( 'bgg_collection-foundation.php' );



/**
 * Define the title to display in plugin's admin Page
 *
 * @since 1.0.0
 * @return string
 */
function bgg_collection_title() {
    return __("Boardgamegeek Collection", "bgg-collection");
}

/**
 * Define a short description to display in the plugin's admin Page
 *
 * @since 1.0.0
 * @return string
 */
function bgg_collection_description() {
    return __("This is a short description about how to use this plugin", "bgg-collection");
}



/**
 * Load the relevant scripts dependant on if the plugin is being loaded on the
 * frontend or the backend
 *
 * @since 1.0.0
 */
if( is_admin() ) {
    require_once( plugin_dir_path(__FILE__) . 'admin/mixd-wp-demo-admin.php' );
} else {
    require_once( plugin_dir_path(__FILE__) . 'public/mixd-wp-demo-public.php' );
}



/**
 * Do something when the plugin is activated from within WordPress
 * - Generally used to set up a new CPT and flush permalinks
 *
 * @since 1.0.0
 */
function mixd_wp_demo_activation() {
    // flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'mixd_wp_demo_activation' );



/**
 * Do something when the plugin is de-activated from within WordPress
 * - Generally used to remove a previously set up CPT and flush permalinks
 *
 * @since 1.0.0
 */
function mixd_wp_demo_deactivation() {
    // flush_rewrite_rules();
}

register_deactivation_hook( __FILE__, 'mixd_wp_demo_deactivation' );



/**
 * Do something when the plugin is uninstalled from within WordPress
 *
 * @since 1.0.0
 */
function mixd_wp_demo_uninstall() {
    //
}

register_uninstall_hook( __FILE__, 'mixd_wp_demo_uninstall' );
