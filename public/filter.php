<?php
/**
 * Filter functionality for collection page
 *
 * @since 1.0.0
 */

$root     = (get_option('bgg_collection_root')) ? sanitize_text_field( get_option('bgg_collection_root') ) : '';

// filter choices
$filters_player = (get_option('bgg_collection_filter_player')) ? sanitize_text_field( get_option('bgg_collection_filter_player') ) : 'off';
$filters_year = (get_option('bgg_collection_filter_year')) ? sanitize_text_field( get_option('bgg_collection_filter_year') ) : 'off';

$filters = array();

if($filters_player == 'on'):
	$filters[] = array(
	    'label'    => 'Number of players',
	    'name'     => 'filter_players',
	    'taxonomy' => 'players',
	    'type'     => 'checkbox'
	);
endif;

if($filters_year == 'on'):
	$filters[] = array(
	    'label'    => 'Year published',
	    'name'     => 'filter_year',
	    'taxonomy' => 'published',
	    'type'     => 'select'
	);
endif;


?>
<?php if( !empty($filters) ): ?>
	<form action="<?php the_permalink( $root ); ?>#filter" method="GET" role="search" id="filter">
		<fieldset>
			<ul>
				<?php foreach( $filters as $filter ): ?>
	                <?php
	                    $current = (isset($_GET[ $filter['name'] ])) ? $_GET[ $filter['name'] ] : '';

	                    if( isset($filter['taxonomy']) ):
	                    	$args = array(
							    'taxonomy' => $filter['taxonomy'],
							);
	                    	if( $filter['taxonomy'] == 'players' ):
	                    		$args['meta_query'][] = array(
									'key'  => 'player_count',
									'type' => 'NUMERIC'
								);

								$args['orderby'] = 'player_count';
								$args['order'] = 'ASC';
	                    	endif;

	                        $terms = get_terms( $args );
	                    endif;
	                ?>
	                <li>
	                    <label for="<?php echo $filter['name']; ?>"><?php echo $filter['label']; ?></label>
	                        <?php
	                            if( !empty($terms) ):

	                                foreach( $terms as $term ):
	                                    if($term->term_id == $current):
	                                        $is_current = 'checked="checked"';
	                                    else:
	                                        $is_current = '';
	                                    endif;
	                                    echo '<label for="filter_'.$filter['taxonomy'].'_'.$term->term_id.'">';
	                                    echo '<input type="checkbox" class="" name="filter_'.$filter['taxonomy'].'[]" id="filter_'.$filter['taxonomy'].'_'.$term->term_id.'" value="'.$term->term_id.'">';
	                                    echo $term->name;
	                                    echo '</label>';
	                                endforeach;
	                            endif;
	                        ?>
	                </li>
	            <?php endforeach; ?>

	            <li>
	            	<button type="submit" class="btn btn--primary">Filter</button>
	            </li>
			</ul>
		</fieldset>
	</form>
<?php endif; ?>