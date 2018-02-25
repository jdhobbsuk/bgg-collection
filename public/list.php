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

$collection = bgg_collection_get_games();

?>

<?php if( $collection->have_posts() ): ?>
	<div class="">
        	<?php while ( $collection->have_posts() ): $collection->the_post(); ?>
        		<?php
        			$id = get_the_ID();

        			$bgg_id = get_post_meta($id, 'bgg_id', true);
		            if($bgg_id):
		                $url   = 'https://boardgamegeek.com/boardgame/';
		                $href  = 'href="'.$url.$bgg_id.'"';
		                $title = '<a '.$href.' target="_blank">'.get_the_title().'</a>';
		            else:
		            	$title = get_the_title();
		            endif;

		            // meta
					$meta_arr    = array();

					$avg_rating  = get_post_meta($id, 'avg_rating', true);
					if( $avg_rating ):
						$meta_arr[] = array( 'label' => 'Rating (Average)', 'value' => number_format($avg_rating, 1) );
					endif;

					$per_rating  = get_post_meta($id, 'per_rating', true);
					if( !$per_rating ):
						$per_rating = 'N/A';
					endif;
					$meta_arr[] = array( 'label' => 'Rating (Personal)', 'value' => $per_rating );

					$rank  = get_post_meta($id, 'rank', true);
					if( $rank ):
						$meta_arr[] = array( 'label' => 'BGG Rank', 'value' => $rank );
					endif;

					$playingtime = get_post_meta($id, 'playingtime', true);
					if( $playingtime ):
						$meta_arr[] = array( 'label' => 'Length (mins)', 'value' => $playingtime );
					endif;
        		?>

        		<article>
        			<header>
	            		<h2><?php echo $title; ?></h2>
	            	</header>
	            	<?php
	            		if( !empty($meta_arr) ):
	            			echo '<dl>';
	            				foreach($meta_arr as $meta):
	            					echo '<div>';
	            						echo '<dt>'.$meta['label'].'</dt>';
	            						echo '<dd>'.$meta['value'].'</dd>';
	            					echo '</div>';
	            				endforeach;
	            			echo '</dl>';
	            		endif;
	            	?>

	            	<hr />
	            </article>
            <?php endwhile; ?>
        </div>
	</div>
<?php endif; wp_reset_query(); ?>