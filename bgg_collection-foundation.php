<?php
/**
 * Mixd WordPress Plugin Foundation
 * - Sets the defaults permissions for Admin & Editor
 * - Adds a Page into WordPress Admin sidebar
 * - Enqueues Mixd plugin stylesheet (sidebar icon)
 *
 * @author      thecodezombie
 * @version     1.0.0
 *
 */



/**
 * If we're not being loaded by WordPress, abort now
 */
if ( !defined( 'WPINC' ) ) { die; }



/**
 * Set 'Administrator' and 'Editor' roles to have access to Plugin config by default
 *
 * @since 1.0.0
 */
if ( !function_exists( 'bgg_collection_add_caps' ) ) {
    function bgg_collection_add_caps_init() {

        $role = get_role('administrator');
        $role->add_cap('bgg_collection');

        $role = get_role('editor');
        $role->add_cap('bgg_collection');

    }

    add_action('admin_init', 'bgg_collection_add_caps_init');
}


/**
 * Include admin stylesheet if it isn't already enqueued
 *
 * @since 1.0.0
 */
if ( !function_exists( 'bgg_collection_menu_styles' ) ) {
    function bgg_collection_menu_styles() {
        $css = plugins_url( 'assets/css/admin.css', __FILE__ );
        wp_register_style(
            'bgg-collection-admin-styles',
            $css,
            false,
            '1.0.0'
        );
        wp_enqueue_style( 'bgg-collection-admin-styles' );
    }

    add_action( 'admin_enqueue_scripts', 'bgg_collection_menu_styles' );
}

/**
 * Generates the custom post type and taxonomies
 *
 * @since 1.0.0
 */
require_once( plugin_dir_path(__FILE__) . 'settings.php' );
