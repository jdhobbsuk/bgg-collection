<?php
/**
 * Filter functionality for collection page
 *
 * @since 1.0.0
 */

$bgg_prefix     = wp_cache_get('bgg_prefix');
$data_choices   = wp_cache_get('data_choices');
$filter_choices = wp_cache_get('filter_choices');
$layout_choices = wp_cache_get('layout_choices');

$filters = array();

if($filter_choices["{$bgg_prefix}_filter_player"]['value'] == 'on'):
	// $filters[] = array(
	//     'label'    => 'Number of players',
	//     'name'     => 'filter_players',
	//     'taxonomy' => 'players',
	//     'type'     => 'slider'
	// );
endif;


?>
<?php if( !empty($filters) ): ?>
	<form action="<?php the_permalink( $root ); ?>#filter" method="GET" role="search" id="filter">
		<fieldset>
			<ul>
				<li>
					
				</li>
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