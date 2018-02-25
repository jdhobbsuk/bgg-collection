<?php
/**
 * This file should contain all of your functions that you need to fire off when in
 * the WordPress back-end. Please ensure you remember to sanitize your variables if
 * handling $_POST or $_GET data.
 * @see https://developer.wordpress.org/plugins/security/data-validation/
 * @see https://developer.wordpress.org/plugins/security/securing-input/
 */



/**
 * Set up permissions for Administrators and Editors to access the configuration Page
 * of this plugin
 *
 * @since 1.0.0
 */
function bgg_collection_add_caps() {
    
    $role = get_role('administrator');
    $role->add_cap('bgg_collection');
    
    $role = get_role('editor');
    $role->add_cap('bgg_collection');
}

add_action( 'admin_init', 'bgg_collection_add_caps' );



/**
 * Add a sub menu page underneath the existing Mixd Plugins Page
 *
 * @since 1.0.0
 */
function bgg_collection_options_page() {

    add_options_page( 'Boardgamegeek Collection', 'BGG Collection', 'manage_options', 'bgg-collection', 'bgg_collection_options' );

}
add_action( 'admin_menu', 'bgg_collection_options_page' );

function bgg_collection_settings() {
	$bgg_prefix = wp_cache_get('bgg_prefix');

	// data import choices
	register_setting( "{$bgg_prefix}_settings", "{$bgg_prefix}_data_rating_avg", array( 'default', 'on' ) );
	register_setting( "{$bgg_prefix}_settings", "{$bgg_prefix}_data_rating_per", array( 'default', 'on' ) );
	register_setting( "{$bgg_prefix}_settings", "{$bgg_prefix}_data_rank", array( 'default', 'on' ) );
	register_setting( "{$bgg_prefix}_settings", "{$bgg_prefix}_data_length", array( 'default', 'on' ) );
	register_setting( "{$bgg_prefix}_settings", "{$bgg_prefix}_data_player", array( 'default', 'on' ) );
	register_setting( "{$bgg_prefix}_settings", "{$bgg_prefix}_data_year", array( 'default', 'on' ) );

	// data filter choices
	register_setting( "{$bgg_prefix}_settings", "{$bgg_prefix}_filter_rating_avg", array( 'default', 'on' ) );
	register_setting( "{$bgg_prefix}_settings", "{$bgg_prefix}_filter_length", array( 'default', 'on' ) );
	register_setting( "{$bgg_prefix}_settings", "{$bgg_prefix}_filter_player", array( 'default', 'on' ) );

	// data layout choices
	register_setting( "{$bgg_prefix}_settings", "{$bgg_prefix}_root" );
    register_setting( "{$bgg_prefix}_settings", "{$bgg_prefix}_ppp", array( 'default', 0 ) );
    register_setting( "{$bgg_prefix}_settings", "{$bgg_prefix}_css", array( 'default', 'on' ) );

    // BGG settings
    register_setting( "{$bgg_prefix}_settings", "{$bgg_prefix}_username" );

}
add_action( 'admin_init', 'bgg_collection_settings' );



/**
 * Outputs information on the 'Demo Plugin' Page in WordPress Admin
 *
 * @since 1.0.0
 */
function bgg_collection_options() {
    require_once( plugin_dir_path( __FILE__ ) . 'options.php' );
}
