<div class="lightbox_inner">
    <ul id="tb_lightbox_options_tab_items">
        <li class="current"><a href="#tb_options_setting"><?php _e( 'Revisions', 'themify' )?></a></li>
    </ul>
    <?php if ( ! empty( $revisions )):?>
        <div class="tb_options_tab_wrapper">
            <ul class="tb_revision_lists">
                <?php foreach( $revisions as $revision ):?>
                <?php 
                    $date = date_i18n( __( 'd/m/Y @ h:i:s a', 'themify' ), strtotime( $revision->post_modified ) );
                    $revision_is_current = $post_id === $revision->ID;
                    $has_builder = $this->check_has_builder( $revision->ID );
                    $rev_comment = get_metadata( 'post', $revision->ID, '_builder_custom_rev_comment', true );
                    $is_deleteable = !$revision_is_current && $can_edit_post && $has_builder && ! wp_is_post_autosave( $revision )  ;
                ?>
                <li>
                    <?php if($is_deleteable):?>
                        <a href="#" title="<?php esc_attr_e( 'Click to restore this revision', 'themify' )?>" class="builder-restore-revision-btn js-builder-restore-revision-btn" data-rev-id="<?php echo $revision->ID ?>"><?php echo $date?></a>
                    <?php else:?>
                        <?php echo $data?>
                    <?php endif;?>

                    <?php if(! empty( $rev_comment )):?>
                        <small>(<?php echo $rev_comment?>)</small>
                    <?php endif;?>
                    <?php if($is_deleteable):?>
                        <a href="#" title="<?php esc_attr_e( 'Delete this revision', 'themify' )?>" class="builder-delete-revision-btn js-builder-delete-revision-btn ti-close" data-rev-id="<?php echo $revision->ID ?>"></a>
                    <?php endif;?>
                  </li>
                <?php endforeach?>
            </ul>
        </div>
    <?php else:?>
        <p><?php _e( 'No Revision found.', 'themify' ) ?></p>
    <?php endif;?>
</div>