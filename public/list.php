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
	<div class="bgg-list" id="list">
    	<?php while ( $collection->have_posts() ): $collection->the_post(); ?>
    		<?php
    			$id = get_the_ID();
    			$content = get_the_content();

    			// logic to choose appropriate link
    			if( $content != '' ):
    				$title = '<a href="'.get_permalink().'">'.get_the_title().'</a>';
    			else:
        			$bgg_id = get_post_meta($id, 'bgg_id', true);
		            if($bgg_id):

		                $url   = 'https://boardgamegeek.com/boardgame/';
		                $href  = 'href="'.$url.$bgg_id.'"';
		                $title = '<a '.$href.' target="_blank">'.get_the_title().'</a>';
		            else:
		            	$title = get_the_title();
		            endif;
		        endif;

	            // meta
	            $meta_arr = bgg_collection_create_meta( $id );
    		?>

    		<article class="bgg-item">
    			<header class="bgg-item__header">
            		<h2 class="bgg-item__title"><?php echo $title; ?></h2>
            	</header>
            	<?php
            		if( !empty($meta_arr) ):
            			echo '<dl class="bgg-item__meta">';
            				foreach($meta_arr as $meta):
            					echo '<div>';
            						echo '<dt>'.$meta['label'].'</dt>';
            						echo '<dd>'.$meta['value'].'</dd>';
            					echo '</div>';
            				endforeach;
            			echo '</dl>';
            		endif;
            	?>
            </article>
        <?php endwhile; ?>
	</div>
<?php endif; wp_reset_query(); ?>

<?php
    $found_posts = $collection->found_posts;
    $ppp = $layout_choices["{$bgg_prefix}_ppp"];

    $total_pages = ceil($found_posts / $ppp);
    $page = (isset($_GET['filter_page'])) ? $_GET['filter_page'] : '1';
?>

<?php if ( $total_pages > 1 ) : ?>

    <nav class="pagination">

        <?php // Previous Page Results ?>
        <div class="pagination__prev">
            <?php if ($page == 1): ?>
                <span>Previous</span>
            <?php else: ?>
                <a href="<?php echo esc_url( add_query_arg( 'filter_page', $page-1 ) ); ?>#list">Previous</a>
            <?php endif; ?>
        </div>

        <?php // Pagination ?>
        <?php if ( $total_pages > 1 ) : ?>
            <ul class="pagination__pages">
                <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                    <?php if($page == $i): ?>
                        <li><span class="btn btn--white btn--flush"><?php echo $i; ?></span></li>
                    <?php else: ?>
                        <li><a class="btn btn--white btn--flush" href="<?php echo esc_url( add_query_arg( 'filter_page', $i ) ); ?>#list"><?php echo $i; ?></a></li>
                    <?php endif; ?>
                <?php endfor; ?>
            </ul>
        <?php endif; ?>

        <?php // Next Results Page ?>
        <div class="pagination__next">
            <?php if ($page < $total_pages): ?>
                <a href="<?php echo esc_url( add_query_arg( 'filter_page', $page+1 ) ); ?>#list">Next</a>
            <?php else: ?>
                <span>Next</span>
            <?php endif; ?>
        </div>
    </nav>

<?php endif; ?>