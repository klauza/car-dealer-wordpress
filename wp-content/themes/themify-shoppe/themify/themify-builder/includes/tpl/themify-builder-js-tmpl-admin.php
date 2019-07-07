<?php
global $post;
if (!is_object($post))
    return;
?>
<script type="text/html" id="tmpl-builder_module_item">
    <div class="tb_action_wrap tb_module_actions">
        <div class="tb_grid_icon themify_module_options ti-pencil"></div>
        <ul class="tb_dropdown">
            <li>
                <div class="tb_module_styling ti-brush themify-tooltip-bottom">
                    <div class="themify_tooltip"><?php _e('Styling', 'themify') ?></div>
                </div>
            </li>
            <li>
                <div class="tb_duplicate ti-layers themify-tooltip-bottom">
                    <div class="themify_tooltip"><?php _e('Duplicate', 'themify') ?></div>
                </div>
            </li>
            <li>
                <div class="tb_save_component ti-save themify-tooltip-bottom" data-component="module">
                    <div class="themify_tooltip"><?php _e('Save', 'themify') ?></div>
                </div>
            </li>
            <li>
                <div class="tb_delete ti-close themify-tooltip-bottom">
                    <div class="themify_tooltip"><?php _e('Delete', 'themify') ?></div>
                </div>
            </li>
            <li class="tb_action_more">
                <div class="ti-more"></div>
                <ul>
                    <li><div class="tb_export_component ti-export" data-component="module">
                            <?php _e('Export', 'themify') ?>
                        </div></li>
                    <li>
                        <div class="tb_import_component ti-import" data-component="module">
                            <?php _e('Import', 'themify') ?>
                        </div>
                    </li>
                    <li class="tb_inner_action_more">
                        <div class="ti-files" > <?php _e('Copy', 'themify') ?> </div>
                        <ul>
                            <li><div class="tb_copy_component" data-component="{{ data.component_name }}"> <?php _e('Content & Styling', 'themify') ?> </div></li>
                            <li><div class="tb_copy_style"> <?php _e('Styling Only', 'themify') ?> </div></li>
                        </ul>
                        
                    </li>
                    <li  class="tb_inner_action_more">
                        <div class="ti-clipboard"> <?php _e('Paste', 'themify') ?></div>
                        <ul>
                            <li><div class="tb_paste_component" data-component="{{ data.component_name }}"> <?php _e('Content & Styling', 'themify') ?> </div></li>
                            <li><div class="tb_paste_style"> <?php _e('Styling Only', 'themify') ?> </div></li>
                        </ul>
                    </li>
                    <li>
                        <div class="tb_visibility_component ti-eye themify-tooltip-bottom">
                            <?php _e('Visibility', 'themify') ?>
                        </div>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
    <div class="module_label">
        <i class="tb_icon tb-module-type-{{ data.slug }}"></i>
        <strong class="module_name">{{ data.name }}</strong>
        <em class="module_excerpt">{{ data.excerpt }}</em>
    </div>
</script>

<script type="text/html" id="tmpl-builder_admin_canvas_block">
    <div class="themify_builder themify_builder_admin clearfix">
        <?php include_once THEMIFY_BUILDER_INCLUDES_DIR . '/themify-builder-module-panel.php'; ?>
        <div id="tb_scroll_anchor"></div>
        <div id="tb_module_tmp"></div>
        <div class="tb_row_panel clearfix">
            <div id="tb_row_wrapper" class="tb_row_js_wrapper tb_editor_wrapper" data-postid="<?php echo $post->ID; ?>"></div>
        </div>
    </div>
</script>
