<?php
    $bgg_prefix     = wp_cache_get('bgg_prefix');
    $data_choices   = wp_cache_get('data_choices');
    $filter_choices = wp_cache_get('filter_choices');
    $layout_choices = wp_cache_get('layout_choices');

    $username = (get_option("{$bgg_prefix}_username")) ? sanitize_text_field( get_option("{$bgg_prefix}_username") ) : '';

    $sync     = ( isset($_POST["{$bgg_prefix}_sync"]) ) ? $_POST["{$bgg_prefix}_sync"] : false ;

    $bgg_url  = 'https://www.boardgamegeek.com/xmlapi2/collection?username='.$username.'&stats=1';
    $page     = get_current_screen();
    $sync_url = admin_url($page->parent_file.'?page=bgg-collection&sync=1' );

    if( $username && $sync ):
        require_once( plugin_dir_path(__FILE__) . 'sync.php' );
    endif;

    // flush permalinks anytime settings are changed
    if( isset( $_GET['settings-updated'] ) ):
        flush_rewrite_rules( true );
    endif;


?>

<section id="introduction" class="wrap about-description">

    <h1><?php echo bgg_collection_title(); ?></h1>
    <!-- <p><?php //echo bgg_collection_description(); ?></p> -->

    <form method="post" action="options.php">
        <?php settings_fields( "{$bgg_prefix}_settings" ); ?>
        <?php do_settings_sections( "{$bgg_prefix}_settings" ); ?>

        <h3>Data Settings</h3>
        <p>Choose the data to be pulled in from BoardGameGeek.</p>

        <table>
            <tr valign="top">
                <td>
                    <?php foreach( $data_choices as $choice => $value ): ?>
                        <?php $is_selected = ($value['value'] == 'on') ? 'checked="checked"' : '' ; ?>
                        <div style="margin: 0 0 0.5em">
                            <label for="<?php echo $choice; ?>">
                                <input id="<?php echo $choice; ?>" name="<?php echo $choice; ?>" type="checkbox" <?php echo $is_selected; ?> />
                                <?php echo $value['label']; ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </td>
            </tr>
        </table>

        <hr />

        <h3>Filter Settings</h3>
        <p>Choose the options to use for filtering.</p>

        <table>
            <tr valign="top">
                <td>
                    <?php foreach( $filter_choices as $choice => $value ): ?>
                        <?php $is_selected = ($value['value'] == 'on') ? 'checked="checked"' : '' ; ?>
                        <div style="margin: 0 0 0.5em">
                            <label for="<?php echo $choice; ?>">
                                <input id="<?php echo $choice; ?>" name="<?php echo $choice; ?>" type="checkbox" <?php echo $is_selected; ?> />
                                <?php echo $value['label']; ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </td>
            </tr>
        </table>

        <hr />

        <h3>Layout Settings</h3>
        <table class="form-table">
            <tr valign="top">
                <th scope="row" style="min-width: 300px;">
                    <strong>List page</strong>
                    <small style="display: block; font-weight: normal;">Choose the page you want your collection to appear</small>
                </th>
                <td>
                    <?php
                        $args = array(
                            'id'       => 'bgg_collection_root',
                            'name'     => 'bgg_collection_root',
                            'selected' => $layout_choices['bgg_collection_root'],
                            'exclude'  => array( get_option( 'page_on_front' ) )
                        );

                        wp_dropdown_pages( $args );
                    ?>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" style="min-width: 300px;">
                    <strong>Items per page</strong>
                    <small style="display: block; font-weight: normal;">Choose 0 if you don't want any pagination</small>
                </th>
                <td>
                    <input id="bgg_collection_ppp" name="bgg_collection_ppp" type="number" step="10" min="0" max="50" value="<?php echo $layout_choices['bgg_collection_ppp']; ?>">
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" style="min-width: 300px;">
                    <strong>Include CSS</strong>
                    <small style="display: block; font-weight: normal;">Include to supply basic styling to the filtering and loop</small>
                </th>
                <td>
                    <div style="margin: 0 0 0.5em">
                        <?php $is_selected = ($layout_choices['bgg_collection_css'] == 'on') ? 'checked="checked"' : '' ; ?>
                        <label for="bgg_collection_css">
                            <input id="bgg_collection_css" name="bgg_collection_css" type="checkbox" <?php echo $is_selected; ?> />
                        </label>
                    </div>
                </td>
            </tr>
        </table>

        <h3>BoardGameGeek Settings</h3>
        <table class="form-table">        
            <tr valign="top">
                <th scope="row">Username</th>
                <td>
                    <input type="text" name="<?php echo $bgg_prefix.'_username' ?>" id="<?php echo $bgg_prefix.'_username' ?>" value="<?php echo esc_attr( $username ); ?>" />
                </td>
            </tr>
        </table>
    
        <?php submit_button(); ?>
    </form>

    <?php if( $username ): ?>
        <hr />

        <?php $view_xml = '<a href="'.$bgg_url.'" target="_blank" style="margin-left: 7px;" class="button button-secondary">View XML</a>'; ?>

        <div style="display: table; margin-top: 1.5em;">
            <h2 style="display: table-cell; vertical-align: middle;">Sync Data</h2>
            <div style="display: table-cell;"><?php echo $view_xml; ?></div>
        </div>
        <p style="max-width: 800px;">Now you've chosen your settings and given a BGG username, check whether your data is ready to be synced by clicking <strong>View XML</strong>. If it doesn't say <em>"Please try again later for access"</em>, then the sync will work.</p>

        <form method="post" action="options-general.php?page=bgg-collection">

            <input type="hidden" name="<?php echo $bgg_prefix.'_sync' ?>" id="<?php echo $bgg_prefix.'_sync' ?>" value="true" />

            <?php submit_button('Sync Data', array('primary', 'large') ); ?>
        </form>
    <?php endif; ?>

    

</section>
