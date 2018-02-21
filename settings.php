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
    $singular = 'Game';
    $plural   = 'Games';

    register_post_type(
        'collection',
        array(
            'public' => false,
            'show_ui' => true,
            'capability_type' => 'page',
            'hierarchical' => false,
            'publicly_queryable' => false,
            'exclude_from_search' => false,
            'menu_position' => 5,
            'menu_icon' => 'dashicons-layout',
            'query_var' => true,
            'rewrite' => array( 'slug' => 'examples', 'with_front' => false ),
            'supports' => array( 'title', 'page-attributes', 'revisions' ),
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
    $singular = 'Player';
    $plural   = 'Players';

    register_taxonomy(
        'players',
        array( 'collection' ),
        array(
            'labels' => array(
                'name' => __( $plural ),
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
}

add_action('init', 'bgg_collection_players_taxonomy');


/**
 * Registers the 'players' taxonomy
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

    $columns['cb']            = '<input type="checkbox" />';
    $columns['avg_rating']    = __('Rating (Avg)');
    $columns['per_rating']    = __('Rating (Personal)');
    $columns['playingtime']    = __('Playing Time (mins)');
    $columns['players']       = __('Players');
    $columns['bgg_link']    = __('BGG Link');
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

            echo number_format($avg_rating, 1);
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
        case 'bgg_link':

            $bgg_id = get_post_meta($post->ID, 'bgg_id', true);
            if($bgg_id):
                $url  = 'https://boardgamegeek.com/boardgame/';
                $href = 'href="'.$url.$bgg_id.'"';
                echo '<a '.$href.' target="_blank"><i class="dashicons dashicons-admin-links">&nbsp;</i></a>';
            endif;
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