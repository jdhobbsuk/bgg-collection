<?php
/**
 * BGG Collection – Settings
 * - registers the 'collection' custom post type
 * - registers the 'players' taxonomy
 * - sets 'collection' to be ordered alphabetically
 *
 */

/**
 * Registers the 'collection' custom post type
 *
 * @since 1.0.0
 */

function bgg_collection_games_cpt () {
    $bgg_prefix     = wp_cache_get('bgg_prefix');
    $layout_choices = wp_cache_get('layout_choices');

    if( $layout_choices["{$bgg_prefix}_root"] && $layout_choices["{$bgg_prefix}_root"] == get_option( 'page_on_front' ) ):
        $url = rtrim( str_replace( home_url(), '', get_permalink( $layout_choices["{$bgg_prefix}_root"] ) ), '/');
    else:
        $url = 'collection';
    endif;

    $singular = 'Game';
    $plural   = 'Games';

    register_post_type(
        'collection',
        array(
            'public' => true,
            'show_ui' => true,
            'capability_type' => 'page',
            'hierarchical' => false,
            'publicly_queryable' => true,
            'exclude_from_search' => false,
            'menu_position' => 4,
            'menu_icon' => 'dashicons-layout',
            'query_var' => true,
            'rewrite' => array( 'slug' => $url, 'with_front' => false ),
            'supports' => array( 'title', 'page-attributes', 'revisions', 'editor' ),
            'labels' => array(
                'name' => __( $plural ),
                'singular_name' => __( $singular ),
                'add_new' => __( 'Add New' ),
                'add_new_item' => __( 'Add New '.$singular ),
                'edit' => __( 'Edit' ),
                'edit_item' => __( 'Edit '.$singular ),
                'new_item' => __( 'New '.$singular ),
                'view' => __( 'View' ),
                'view_item' => __( 'View '.$singular ),
                'search_items' => __( 'Search '.$plural ),
                'not_found' => __( 'No '.$plural.' found' ),
                'not_found_in_trash' => __( 'No '.$plural.' found in Trash' )
            )
        )
    );
}

add_action('init', 'bgg_collection_games_cpt');


/**
 * Registers the 'players' taxonomy
 *
 * @since 1.0.0
 */
function bgg_collection_players_taxonomy () {

    $bgg_prefix = wp_cache_get('bgg_prefix');
    $data_choices = wp_cache_get('data_choices');

    if( $data_choices["{$bgg_prefix}_data_player"]['value'] == 'on' ):

        $singular = 'Player';
        $plural   = 'Players';

        register_taxonomy(
            'players',
            array( 'collection' ),
            array(
                'labels' => array(
                    'name' => __( 'Player Count' ),
                    'singular_name' => __( $singular ),
                    'search_items' => __( 'Search '.$plural ),
                    'popular_items' => __( 'Popular '.$plural ),
                    'all_items' => __( 'All '.$plural ),
                    'parent_item' => __( 'Parent '.$singular ),
                    'parent_item_colon' => __( 'Parent '.$singular.':' ),
                    'edit_item' => __( 'Edit '.$singular ),
                    'update_item' => __( 'Update '.$singular ),
                    'add_new_item' => __( 'Add New '.$singular ),
                    'new_item_name' => __( 'New '.$singular ),
                ),
                'public' => true,
                'show_in_nav_menus' => true,
                'show_ui' => true,
                'hierarchical' => true,
                'query_var' => true,
                'rewrite' => array( 'slug' => 'players', 'with_front' => false ),
            )
        );
    endif;
}

add_action('init', 'bgg_collection_players_taxonomy');

/**
 * Registers the 'published' taxonomy
 *
 * @since 1.0.0
 */
function bgg_collection_published_taxonomy () {

    $bgg_prefix = wp_cache_get('bgg_prefix');
    $data_choices = wp_cache_get('data_choices');

    if( $data_choices["{$bgg_prefix}_data_year"]['value'] == 'on' ):
        $singular = 'Year';
        $plural   = 'Years';

        register_taxonomy(
            'published',
            array( 'collection' ),
            array(
                'labels' => array(
                    'name' => __( 'Year Published' ),
                    'singular_name' => __( $singular ),
                    'search_items' => __( 'Search '.$plural ),
                    'popular_items' => __( 'Popular '.$plural ),
                    'all_items' => __( 'All '.$plural ),
                    'parent_item' => __( 'Parent '.$singular ),
                    'parent_item_colon' => __( 'Parent '.$singular.':' ),
                    'edit_item' => __( 'Edit '.$singular ),
                    'update_item' => __( 'Update '.$singular ),
                    'add_new_item' => __( 'Add New '.$singular ),
                    'new_item_name' => __( 'New '.$singular ),
                ),
                'public' => true,
                'show_in_nav_menus' => true,
                'show_ui' => true,
                'hierarchical' => true,
                'query_var' => true,
                'rewrite' => array( 'slug' => 'published', 'with_front' => false ),
            )
        );
    endif;
}

add_action('init', 'bgg_collection_published_taxonomy');


/**
 * Orders games alphabetically
 *
 * @since 1.0.0
 */
function bgg_collection_admin_order( $wp_query ) {

    if ( is_admin() ) :
        $post_type = $wp_query->query['post_type'];

        if ( $post_type == 'collection' ):
            $wp_query->set( 'orderby', 'title' );
            $wp_query->set( 'order', 'ASC' );
        endif;
    endif;
}

add_filter( 'pre_get_posts', 'bgg_collection_admin_order' );


/**
 * Setup custom columns for admin
 *
 * @since 1.0.0
 */
function bgg_collection_define_columns( $columns ) {

    $bgg_prefix   = wp_cache_get('bgg_prefix');
    $data_choices = wp_cache_get('data_choices');

    $columns['cb'] = '<input type="checkbox" />';

    if( $data_choices["{$bgg_prefix}_data_rating_avg"]['value'] == 'on' ):
        $columns['avg_rating']  = __('<span style="font-size: 85%">Rating (Avg)</span>');
    endif;
    if( $data_choices["{$bgg_prefix}_data_rating_per"]['value'] == 'on' ):
        $columns['per_rating']  = __('<span style="font-size: 85%">Rating (Personal)</span>');
    endif;
    if( $data_choices["{$bgg_prefix}_data_rank"]['value'] == 'on' ):
        $columns['rank']  = __('<span style="font-size: 85%"><i class="dashicons dashicons-awards"></i> Rank</span>');
    endif;
    if( $data_choices["{$bgg_prefix}_data_length"]['value'] == 'on' ):
        $columns['playingtime']  = __('<span style="font-size: 85%"><i class="dashicons dashicons-clock"></i> Length (mins)</span>');
    endif;
    if( $data_choices["{$bgg_prefix}_data_year"]['value'] == 'on' ):
        $columns['year'] = __('<span style="font-size: 85%"><i class="dashicons dashicons-calendar-alt"></i> Published</span>');
    endif;
    if( $data_choices["{$bgg_prefix}_data_player"]['value'] == 'on' ):
        $columns['players'] = __('<span style="font-size: 85%"><i class="dashicons dashicons-admin-users"></i> Players</span>');
    endif;

    $columns['bgg_link']    = __('<span style="font-size: 85%">BGG Link</span>');
    unset($columns['date']);

    return $columns;
}

add_filter( 'manage_edit-collection_columns', 'bgg_collection_define_columns' );


/**
 * Populate custom columns for admin
 *
 * @since 1.0.0
 */
function bgg_collection_fill_columns( $column_name, $id ) {

    global $post;
    switch ( $column_name ) {
        case 'avg_rating':

            $avg_rating = get_post_meta($post->ID, 'avg_rating', true);

            if( $avg_rating ):
                echo number_format($avg_rating, 1);
            endif;
        break;
        case 'per_rating':

            $per_rating = get_post_meta($post->ID, 'per_rating', true);
            if( !$per_rating ):
                $per_rating = '&ndash;';
            endif;

            echo $per_rating;
        break;
        case 'playingtime':

            $playingtime = get_post_meta($post->ID, 'playingtime', true);

            echo $playingtime;
        break;
        case 'rank':

            $rank = get_post_meta($post->ID, 'rank', true);

            echo $rank;
        break;
        case 'bgg_link':

            $bgg_id = get_post_meta($post->ID, 'bgg_id', true);
            if($bgg_id):
                $url  = 'https://boardgamegeek.com/boardgame/';
                $href = 'href="'.$url.$bgg_id.'"';
                echo '<a '.$href.' target="_blank"><i class="dashicons dashicons-admin-links">&nbsp;</i></a>';
            endif;
        break;
        case 'year':

            $published = get_the_term_list( $post->ID, 'published', '<ul style="margin: 0;"><li>', '</li><li>', '</li></ul>' );
            echo strip_tags( $published, '<ul> <li>' );
        break;
        case 'players':

            $players = get_the_term_list( $post->ID, 'players', '<ul style="margin: 0; font-size: 75%"><li>', '</li><li>', '</li></ul>' );
            echo strip_tags( $players, '<ul> <li>' );
        break;
        default:
            break;
    }
}

add_action( 'manage_collection_posts_custom_column', 'bgg_collection_fill_columns', 10, 2 );

?>