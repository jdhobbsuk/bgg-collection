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
 * Set 'Administrator' and 'Editor' roles to have access to Plugin config by default
 *
 * @since 1.0.0
 */
if ( !function_exists( 'bgg_collection_add_global_vars' ) ) {
    function bgg_collection_add_global_vars() {
        $bgg_prefix = wp_cache_get('bgg_prefix');

        // data import params
        $data_choices = array(
            "{$bgg_prefix}_data_rating_avg" => array(
                'label' => 'Rating (average)',
                'value' =>  sanitize_text_field( get_option( "{$bgg_prefix}_data_rating_avg" ) )
            ),
            "{$bgg_prefix}_data_rating_per" => array(
                'label' => 'Rating (personal)',
                'value' =>  sanitize_text_field( get_option( "{$bgg_prefix}_data_rating_per" ) ),
            ),
            "{$bgg_prefix}_data_rank" => array(
                'label' => 'BGG Rank',
                'value' =>  sanitize_text_field( get_option( "{$bgg_prefix}_data_rank" ) ),
            ),
            "{$bgg_prefix}_data_length" => array(
                'label' => 'Length (mins)',
                'value' =>  sanitize_text_field( get_option( "{$bgg_prefix}_data_length" ) ),
            ),
            "{$bgg_prefix}_data_player" => array(
                'label' => 'Player Count',
                'value' =>  sanitize_text_field( get_option( "{$bgg_prefix}_data_player" ) ),
            ),
            "{$bgg_prefix}_data_year" => array(
                'label' => 'Year Published',
                'value' =>  sanitize_text_field( get_option( "{$bgg_prefix}_data_year" ) ),
            ),
        );
        wp_cache_set( 'data_choices', $data_choices );

        // data filter params
        $filter_choices = array(
            "{$bgg_prefix}_filter_rating_avg" => array(
                'label' => 'Rating (average)',
                'value' =>  sanitize_text_field( get_option( "{$bgg_prefix}_filter_rating_avg" ) )
            ),
            "{$bgg_prefix}_filter_length" => array(
                'label' => 'Length (mins)',
                'value' =>  sanitize_text_field( get_option( "{$bgg_prefix}_filter_length" ) )
            ),
            "{$bgg_prefix}_filter_player" => array(
                'label' => 'Player Count',
                'value' =>  sanitize_text_field( get_option( "{$bgg_prefix}_filter_player" ) )
            )
        );
        wp_cache_set( 'filter_choices', $filter_choices );

        // data layout params
        $layout_choices = array(
            "{$bgg_prefix}_root" => sanitize_text_field( get_option( "{$bgg_prefix}_root" ) ),
            "{$bgg_prefix}_ppp"  => sanitize_text_field( get_option( "{$bgg_prefix}_ppp" ) ),
            "{$bgg_prefix}_css"  => sanitize_text_field( get_option( "{$bgg_prefix}_css" ) ),
        );
        wp_cache_set( 'layout_choices', $layout_choices );
    }

    add_action('init', 'bgg_collection_add_global_vars');
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
