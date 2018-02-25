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
	$filters[] = array(
	    'label'    => 'Number of players',
	    'name'     => 'filter_players',
	    'taxonomy' => 'players',
	    'type'     => 'select'
	);
endif;

$order_arr = array(
	array('label' => 'Title (A-Z)', 'value' => 'title'),
	array('label' => 'Highest Rated', 'value' => 'rating'),
	array('label' => 'Highest Ranked', 'value' => 'ranking'),
	array('label' => 'Length', 'value' => 'length'),
);

$filter_order  = (isset($_GET['filter_orderby'])) ? sanitize_text_field( $_GET['filter_orderby'] ) : 'title';
$filter_search = (isset($_GET['filter_search'])) ? sanitize_text_field( $_GET['filter_search'] ) : '';


?>
<?php if( !empty($filters) ): ?>
	<form action="<?php the_permalink( $layout_choices["{$bgg_prefix}_root"] ); ?>#list" method="GET" role="search" id="filter" class="bgg-filter">
		<fieldset>
			<ul>
				<li>
					<label class="bgg-filter__label bgg-filter__label--faux">Search</label>
					<input class="bgg-filter__search" type="text" name="filter_search" id="filter_search" value="<?php echo $filter_search; ?>" placeholder="eg. Ticket to Ride" />
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
	                    <h3 class="bgg-filter__label bgg-filter__label--faux"><?php echo $filter['label']; ?></h3>
	                        <?php
	                            if( !empty($terms) ):

	                            	if( $filter['type'] == 'checkbox' ):
		                                foreach( $terms as $term ):
		                                    if($term->slug == $current):
		                                        $is_current = 'checked="checked"';
		                                    else:
		                                        $is_current = '';
		                                    endif;
		                                    echo '<label for="filter_'.$filter['taxonomy'].'_'.$term->slug.'" class="bgg-filter__label">';
		                                    echo '<input type="checkbox" class="" name="filter_'.$filter['taxonomy'].'[]" id="filter_'.$filter['taxonomy'].'_'.$term->slug.'" value="'.$term->slug.'">';
		                                    echo $term->name;
		                                    echo '</label>';
		                                endforeach;
		                            elseif( $filter['type'] == 'select' ):
		                            	echo '<select name="'.$filter['name'].'" id="'.$filter['name'].'">';
			                            	echo '<option value="">All '.ucfirst($filter['taxonomy']).'</option>';
			                            	foreach( $terms as $term ):
			                                    if($term->slug == $current):
			                                        $is_current = 'selected';
			                                    else:
			                                        $is_current = '';
			                                    endif;
			                                    echo '<option value="'.$term->slug.'" '.$is_current.'>'.$term->name.'</option>';
			                                endforeach;
		                                echo '</select>';
		                            endif;
	                            endif;
	                        ?>
	                </li>
	            <?php endforeach; ?>
	            <?php if( !empty($order_arr) ): ?>
		            <li>
		            	<h3 class="bgg-filter__label bgg-filter__label--faux">Order by</h3>
		            	<?php
		            		foreach( $order_arr as $order ):
		            			if( $order['value'] == $filter_order ):
                                    $is_current = 'checked="checked"';
                                else:
                                    $is_current = '';
                                endif;

		            			echo '<label for="filter_orderby_'.$order['value'].'" class="bgg-filter__label">';
	                                echo '<input type="radio" class="" name="filter_orderby" id="filter_orderby_'.$order['value'].'" value="'.$order['value'].'" '.$is_current.'>';
	                                echo $order['label'];
                                echo '</label>';
		            		endforeach;
		            	?>
		            </li>
		        <?php endif; ?>
	            <li>
	            	<button type="submit" class="bgg-filter__button">Filter</button>
	            </li>
			</ul>
		</fieldset>
	</form>
<?php endif; ?>