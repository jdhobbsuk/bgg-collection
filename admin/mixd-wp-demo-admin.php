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

add_action( 'admin_init', 'bgg_collection_settings' );

function bgg_collection_settings() {
    register_setting( 'bgg_collection_settings', 'bgg_username' );
}



/**
 * Outputs information on the 'Demo Plugin' Page in WordPress Admin
 *
 * @since 1.0.0
 */
function bgg_collection_options() {
    require_once( plugin_dir_path( __FILE__ ) . 'options.php' );
}
