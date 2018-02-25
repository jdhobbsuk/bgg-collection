<?php
/**
 * This file should contain all of your functions that you need to fire off when in the
 * WordPress front-end. These usually consists of getting information from the backend
 * or firing off a request to a Class or vendor package.
 *
 * @since 1.0.0
 */

function bgg_collection_get_games(){
	$bgg_prefix     = wp_cache_get('bgg_prefix');
	$layout_choices = wp_cache_get('layout_choices');
	$filter_choices = wp_cache_get('filter_choices');

	//$filter_tag = (isset($_GET['filter_tag'])) ? $_GET['filter_tag'] : '';

    // Define arguments for query.
    $args = array(
        'post_type'      => 'collection',
        'posts_per_page' => $layout_choices["{$bgg_prefix}_ppp"],
        'post_status'    => 'publish',
        'orderby'        => 'title',
        'order'          => 'ASC'
    );

    // Create new instance of WP_Query class.
    $output = new WP_Query( $args );

    // Return the results
    return $output;
}


function bgg_collection_force_redirect_empty() {
	// only action when on front-end
    if ( !is_admin() ):
    	// only action when looking at collection singular page
    	if( is_singular('collection') ):

    		// get globals
    		$bgg_prefix     = wp_cache_get('bgg_prefix');
    		$layout_choices = wp_cache_get('layout_choices');

    		// check for content
    		global $post;
			$content = $post->post_content;

			// check if content is empty
    		if( $content == '' ):

    			// set URL to fallback
	    		if( $layout_choices["{$bgg_prefix}_root"] && $layout_choices["{$bgg_prefix}_root"] == get_option( 'page_on_front' ) ):
					$url = get_permalink( $layout_choices["{$bgg_prefix}_root"] );
				else:
					$url = '/collection/';
				endif;

				// redirect
				wp_safe_redirect( $url );
				exit;
			endif;
    	endif;
    endif;
}
add_action( 'template_redirect', 'bgg_collection_force_redirect_empty');