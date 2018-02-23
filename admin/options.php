<section id="introduction" class="wrap about-description">

    <h1><?php echo bgg_collection_title(); ?></h1>
    <!-- <p><?php //echo bgg_collection_description(); ?></p> -->

    <?php
    	$username = (get_option('bgg_username')) ? sanitize_text_field( get_option('bgg_username') ) : '';
    	$sync     = ( isset($_GET['sync']) ) ? true : false ;

    	$bgg_url  = 'https://www.boardgamegeek.com/xmlapi2/collection?username='.$username.'&stats=1';
    	$page     = get_current_screen();
    	$sync_url = admin_url($page->parent_file.'?page=bgg-collection&sync=1' );

    	if( $username && $sync ):
    		require_once( plugin_dir_path(__FILE__) . 'sync.php' );
    	endif;
    ?>

    <form method="post" action="options.php">
    <?php settings_fields( 'bgg_collection_settings' ); ?>
    <?php do_settings_sections( 'bgg_collection_settings' ); ?>
    <table class="form-table">        
        <tr valign="top">
        	<th scope="row">Boardgamegeek Username</th>
        	<td>
        		<input type="text" name="bgg_username" value="<?php echo esc_attr( $username ); ?>" />
        		<?php
        			if( $username ):
        				echo '<a href="'.$bgg_url.'" target="_blank" class="button button-secondary">';
        					echo 'View XML';
        				echo '</a>';

        				echo '<a style="margin-left:7px;" href="'.$sync_url.'" class="button button-primary">';
        					echo 'Sync Data';
        				echo '</a>';
        			endif;
        		?>
        	</td>
        </tr>
        <?php if( get_option('bgg_username') ): ?>
        <tr>
        	<td></td>
        </tr>
        <?php endif; ?>
    </table>
    
    <?php submit_button(); ?>

</section>
