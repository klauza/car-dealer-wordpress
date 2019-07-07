<div class="lightbox_inner tb_import_file_inner">
    <?php printf('<h3>%s</h3>', __('Select a file to import', 'themify'));?>
    <?php if (is_multisite() && !is_upload_space_available()):?>
        <?php printf(__('<p>Sorry, you have filled your %s MB storage quota so uploading has been disabled.</p>', 'themify'), get_space_allowed());?>
    <?php else:?>
            <div class="tb_plupload_upload_uic tb_upload_btn" id="tb_import_filestb_plupload_upload_ui">
                    <input id="tb_import_filestb_plupload_browse_button" type="button" value="<?php _e('Upload', 'themify')?>" class="builder_button" />
                    <span class="ajaxnonceplu" id="ajaxnonceplu<?php echo wp_create_nonce('themify_builder_import_filethemify-builder-plupload')?>"></span>
            </div>
        <?php
            $max_upload_size = (int) wp_max_upload_size() / ( 1024 * 1024 );
            printf(__('<p>Maximum upload file size: %d MB.</p>', 'themify'), $max_upload_size)
        ?>
    <?php endif;?>
</div>