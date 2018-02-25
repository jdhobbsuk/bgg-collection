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

	// filters
	$filter_players = (isset($_GET['filter_players'])) ? sanitize_key( $_GET['filter_players'] ) : '';
	$filter_search  = (isset($_GET['filter_search'])) ? sanitize_text_field( $_GET['filter_search'] ) : '';

	// order
	$filter_order = (isset($_GET['filter_orderby'])) ? sanitize_text_field( $_GET['filter_orderby'] ) : 'title';

	$filter_page   = (isset($_GET['filter_page'])) ? $_GET['filter_page'] : '1';
	
	$ppp = $layout_choices["{$bgg_prefix}_ppp"];
	if( $ppp == 0 ):
		$ppp = -1;
	endif;

    // Define arguments for query
    $args = array (
        'post_type'      => 'collection',
        'posts_per_page' => $ppp,
        'paged'          => $filter_page,
        'post_status'    => 'publish',
    );

    if( $filter_search ):
    	$args['s']   = $filter_search;
    endif;

    // ordering settings
    switch( $filter_order ):
    	case 'title':
    		$args['order']   = 'ASC';
    		$args['orderby'] = 'title';
    	break;
    	case 'rating':
    		$args['order']    = 'DESC';
    		$args['orderby']  = 'meta_value_num';
    		$args['meta_key'] = 'avg_rating';
    	break;
    	case 'rank':
    		$args['order']    = 'DESC';
    		$args['orderby']  = 'meta_value_num';
    		$args['meta_key'] = 'rank';
    	break;
    	case 'length':
    		$args['order']    = 'ASC';
    		$args['orderby']  = 'meta_value_num';
    		$args['meta_key'] = 'playingtime';
    	break;
   	endswitch;

   	// player search
    if( $filter_choices["{$bgg_prefix}_filter_player"]['value'] == 'on' && $filter_players ):
    	$args['tax_query'][] = array(
			'taxonomy' => 'players',
			'field'    => 'slug',
			'terms'    => $filter_players,
		);
    endif;

    // Create new instance of WP_Query class.
    $output = new WP_Query( $args );

    // Return the results
    return $output;
}

function bgg_collection_create_meta( $game_id ){

	$meta_arr    = array();

	// rating (average)
	$avg_rating  = get_post_meta($game_id, 'avg_rating', true);
	if( $avg_rating ):
		$meta_arr[] = array( 'label' => 'Rating (Avg)', 'value' => number_format($avg_rating, 1) );
	endif;

	// rating (personal)
	$per_rating  = get_post_meta($game_id, 'per_rating', true);
	if( !$per_rating ):
		$per_rating = 'N/A';
	endif;
	$meta_arr[] = array( 'label' => 'Rating (Personal)', 'value' => $per_rating );

	// BGG rank
	$rank  = get_post_meta($game_id, 'rank', true);
	if( $rank ):
		$meta_arr[] = array( 'label' => 'BGG Rank', 'value' => $rank );
	endif;

	// length (mins)
	$playingtime = get_post_meta($game_id, 'playingtime', true);
	if( $playingtime ):
		$meta_arr[] = array( 'label' => 'Length', 'value' => $playingtime.' mins' );
	endif;

	// players
	$args = array(
		'orderby'  => 'meta_value_num',
		'meta_key' => 'player_count'
	);
	$players = wp_get_object_terms($game_id, 'players', $args );
	$player_count = count($players);
	if( !empty($players) ):
		if( $player_count == 1 ):
			$count = $players[0]->slug;

			$player_val = $count.' Player';
			if($count != 1):
				$player_val .= 's';
			endif;
			
		else:
			$first = $players[0]->slug;
			$last  = $players[$player_count-1]->slug;

			$player_val = $first.' &ndash; '.$last.' Players';
		endif;

		$meta_arr[] = array( 'label' => 'Player Count', 'value' => $player_val );
	endif;

	// published
	$published = wp_get_object_terms($game_id, 'published' );
	$published_count = count($published);
	if( !empty($published) && $published_count == 1 ):

		$meta_arr[] = array( 'label' => 'Published', 'value' => $published[0]->slug );
	endif;

	return $meta_arr;
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