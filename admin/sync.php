<?php
/**
 * BGG Collection – Sync
 *
 */

$data_choices   = wp_cache_get('data_choices');
$filter_choices = wp_cache_get('filter_choices');
$layout_choices = wp_cache_get('layout_choices');

$username = (get_option('bgg_collection_username')) ? sanitize_text_field( get_option('bgg_collection_username') ) : '';
$bgg_url  = 'https://www.boardgamegeek.com/xmlapi2/collection?username='.$username.'&stats=1';
$xml = simplexml_load_file( $bgg_url );

// set defaults
$avg_rating  = '';
$per_rating  = '';
$min_player  = '';
$max_player  = '';
$playingtime = '';

// pending message from BGG
$bgg_pending = '/Please try again later for access./';

//counters
$games_added   = 0;
$games_updated = 0;

if( !empty( $xml ) ):

    $check_message = $xml[0];

    // check XML output for basics
    if( preg_match( $bgg_pending, $check_message ) ):
        $bgg_status = 'pending';
    else:
        if( $xml['totalitems'] > 0 ):
            $bgg_status = 'success';
        else:
            $bgg_status = 'failed';
        endif;
    endif;


    if( $bgg_status == 'success' ):

        foreach( $xml as $item ):
            global $wpdb;

            // basics
            $bgg_id         = intval( $item['objectid']->__toString() );
            $title          = $item->name[0]->__toString();
            $image          = $item->image[0]->__toString();
            $year_published = $item->yearpublished[0]->__toString();
            
            // if stats are accessible
            if( !empty($item->stats) ):
                $avg_rating = number_format( $item->stats->rating->average[0]['value']->__toString(), 5 );

                $per_rating = $item->stats->rating['value'][0]->__toString();

                $min_player = intval( $item->stats['minplayers'][0]->__toString() );
                $max_player = intval( $item->stats['maxplayers'][0]->__toString() );

                if( $item->stats['playingtime'] ):
                    $playingtime = intval( $item->stats['playingtime']->__toString() );
                endif;

                $ranks = $item->stats->rating->ranks;

                // get BGG rank from array
                if( !empty($ranks) ):
                    foreach($ranks as $rank):
                        if($rank->rank['id']->__toString() == 1):
                            $rank = intval( $rank->rank['value']->__toString() );
                        endif;
                    endforeach;
                endif;
            endif;

            // check for existing posts using BGG ID
            $exist_check = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE meta_key = 'bgg_id' AND meta_value = ".$bgg_id." LIMIT 1", ARRAY_A);

            // generate player terms
            if( $data_choices['bgg_collection_data_player']['value'] == 'on' ):
                $players = array();

                if( $min_player && $max_player ):
                    for($i = $min_player; $i <= $max_player; $i++):
                        $term_check = term_exists( $i, 'players' );

                        if( $term_check === 0 || $term_check === NULL ):
                            $term_title = $i.' Players';
                            if( $i == 1 ):
                                $term_title = 'Solo'; 
                            endif;

                            $args = array(
                                'slug' => $i
                            );
                            wp_insert_term( $term_title, 'players', $args );
                        endif;

                        $term = get_term_by('slug', $i, 'players', ARRAY_A);
                        $players[] = intval( $term['term_id'] );
                    endfor;
                endif;
            endif;

            // generate years published
            if( $year_published && $data_choices['bgg_collection_data_year']['value'] == 'on' ):
                $year_check = term_exists( $year_published, 'published' );
                if( $year_check === 0 || $year_check === NULL ):
                    $args = array(
                        'slug' => $year_published
                    );
                    wp_insert_term( $year_published, 'published', $args );
                endif;
            endif;

            // create post, or if exists then update instead
            if( empty($exist_check) ):
                $new_post = array(
                    'post_title' => $title,
                    'post_status' => 'publish',
                    'post_type' => 'collection',
                );

                $post_id = wp_insert_post($new_post);
                add_post_meta($post_id, 'bgg_id', $bgg_id);

                // meta
                    // average rating
                    if( $avg_rating && $data_choices['bgg_collection_data_rating_avg']['value'] == 'on' ):
                        add_post_meta($post_id, 'avg_rating', $avg_rating);
                    elseif( $data_choices['bgg_collection_data_rating_avg']['value'] != 'on' ):
                        delete_post_meta($post_id, 'avg_rating');
                    endif;

                    // personal rating
                    if( ($per_rating && $per_rating != 'N/A') && $data_choices['bgg_collection_data_rating_per']['value'] == 'on' ):
                        add_post_meta($post_id, 'per_rating', number_format($per_rating, 1) );
                    elseif( $data_choices['bgg_collection_data_rating_per']['value'] != 'on' ):
                        delete_post_meta($post_id, 'per_rating' );
                    endif;

                    // playing time
                    if( $playingtime && $data_choices['bgg_collection_data_rating_per']['value'] == 'on' ):
                        add_post_meta($post_id, 'playingtime', $playingtime);
                    endif;

                    // BGG rank
                    if( $rank ):
                        add_post_meta($post_id, 'rank', $rank);
                    endif;

                // increase count
                $games_added++;
            else:
                $post_id = $exist_check[0]['post_id'];

                $update_details = array(
                    'ID' => $post_id,
                    'post_title' => $title,
                );

                //meta
                    // average rating
                    if( $avg_rating && $data_choices['bgg_collection_data_rating_avg']['value'] == 'on' ):
                        update_post_meta($post_id, 'avg_rating', $avg_rating);
                    elseif( $data_choices['bgg_collection_data_rating_avg']['value'] != 'on' ):
                        update_post_meta($post_id, 'avg_rating', null);
                    endif;

                    // personal rating
                    if( ($per_rating && $per_rating != 'N/A') && $data_choices['bgg_collection_data_rating_per']['value'] == 'on' ):
                        update_post_meta($post_id, 'per_rating', number_format($per_rating, 1) );
                    elseif( $data_choices['bgg_collection_data_rating_per']['value'] != 'on' ):
                        update_post_meta($post_id, 'per_rating', null);
                    endif;

                    // playing time
                    if( $playingtime ):
                        update_post_meta($post_id, 'playingtime', $playingtime);
                    endif;

                    // BGG rank
                    if( $rank ):
                        update_post_meta($post_id, 'rank', $rank);
                    endif;

                // increase updated
                $games_updated++;
            endif;


            // add player choices
            if( !empty($players) && $data_choices['bgg_collection_data_player']['value'] == 'on' ):
                wp_set_object_terms( $post_id, $players, 'players' );
            endif;

            // add year published
            if( $year_published && $data_choices['bgg_collection_data_year']['value'] == 'on' ):
                wp_set_object_terms( $post_id, $year_published, 'published' );
            endif;

        endforeach;

        $players = get_terms( 'players', array( 'hide_empty' => true) );
        if( !empty($players) && $data_choices['bgg_collection_data_player']['value'] == 'on' ):
            foreach($players as $player):
                $term_id   = $player->term_id;
                $term_slug = $player->slug;

                add_term_meta($term_id, 'player_count', intval($term_slug), true);
            endforeach;
        endif;

        // success message
        if($games_added > 0 || $games_updated > 0):
            echo '<div class="notice notice-success is-dismissible">';
                echo '<p><strong>Success!</strong> '.$games_added.' games added, '.$games_updated.' games updated.</p>';
            echo '</div>';
        endif;

    elseif( $bgg_status == 'pending' ):
        echo '<div class="notice notice-warning is-dismissible">';
            echo '<p><strong>Fail:</strong> Data still pending with BoardGameGeek. Please click "View XML" and refresh until you see a change.</p>';
        echo '</div>';
    else:
        echo '<div class="notice notice-error is-dismissible">';
            echo '<p><strong>Fail:</strong> Please check your username.</p>';
        echo '</div>';
    endif;
endif;



?>