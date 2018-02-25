<?php
/**
 * BGG Collection: Shortcode
 *
 * @author      thecodezombie
 * @version     1.0.0
 *
 */

function bgg_collection_shortcode_output( $atts, $content ) {
    // extract and set defaults
    extract( shortcode_atts( array(
        'filters' => true,
        'list'    => 'filtered'
    ), $atts ) );

    if($filters):
    	require_once( plugin_dir_path(__FILE__) . 'filter.php' );
    endif;

    if( $list == 'filtered' ):
    	require_once( plugin_dir_path(__FILE__) . 'list.php' );
    else:
    	//require_once( plugin_dir_path(__FILE__) . 'alphabetical.php' );
    endif;



}
add_shortcode( 'bgg_collection', 'bgg_collection_shortcode_output' );