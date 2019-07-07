<script type="text/html" id="tmpl-builder_visual_module_item">
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
                        <div class="tb_visibility_component ti-eye">
                            <?php _e('Visibility', 'themify') ?>
                        </div>
                    </li>
                </ul>
            </li>

        </ul>
        <div class="tb_data_mod_name"></div>
    </div>
</script>
<script type="text/html" id="tmpl-small_toolbar">
<?php $is_premium = Themify_Builder_Model::is_premium(); ?>
    <div class="tb_disable_sorting" id="tb_small_toolbar">
        <ul class="tb_toolbar_menu">
            <li class="tb_toolbar_undo"><a href="#" class="tb_tooltip tb_undo_redo tb_undo_btn tb_disabled"><i class="ti-back-left"></i><span><?php _e('Undo (CTRL+Z)', 'themify'); ?></span></a></li>
            <li class="tb_toolbar_redo"><a href="#" class="tb_tooltip tb_undo_redo tb_redo_btn tb_disabled"><i class="ti-back-right"></i><span><?php _e('Redo (CTRL+SHIFT+Z)', 'themify'); ?></span></a></li>
            <li class="tb_toolbar_divider"></li>
            <li class="tb_toolbar_import"><a href="javascript:void(0);"><i class="ti-import"></i><span></a>
                <ul>
                    <li><a href="#" data-component="file"><?php _e('Import From File', 'themify'); ?></a></li>
                    <li><a href="#" data-component="page"><?php _e('Import From Page', 'themify'); ?></a></li>
                    <li><a href="#" data-component="post"><?php _e('Import From Post', 'themify'); ?></a></li>
                </ul>
            </li>
            <li class="tb_toolbar_export"><a href="<?php echo wp_nonce_url('?themify_builder_export_file=true', 'themify_builder_export_nonce') ?>&postid=#postID#" class="tb_tooltip tb_export_link"><i class="ti-export"></i><span><?php _e('Export', 'themify'); ?></span></a></li>
            <li class="tb_toolbar_divider"></li>
            <li><a href="javascript:void(0);"><i class="ti-layout"></i></a>
                <ul>
                    <li<?php if (!$is_premium): ?> class="tb_lite"<?php endif; ?>><?php if (!$is_premium): ?><span class="themify_lite_tooltip"></span><?php endif; ?><a href="#" class="tb_load_layout"><?php _e('Load Layout', 'themify'); ?></a></li>
                    <li<?php if (!$is_premium): ?> class="tb_lite"<?php endif; ?>><?php if (!$is_premium): ?><span class="themify_lite_tooltip"></span><?php endif; ?><a href="#" class="tb_save_layout"><?php _e('Save as Layout', 'themify'); ?></a></li>
                </ul>
            </li>
        </ul>
        <div class="tb_toolbar_save_wrap">
            <div class="tb_toolbar_close">
                <a href="#"  class="tb_tooltip tb_toolbar_close_btn" title="<?php _e('ESC', 'themify') ?>"><i class="ti-close"></i><span><?php _e('Close', 'themify'); ?></span></a>
            </div>
            <div class="tb_toolbar_save_btn">
                <a href="#" class="tb_toolbar_save" title="<?php _e('Ctrl + S', 'themify') ?>"><?php _e('Save', 'themify'); ?></a>
            </div>
        </div>
    </div>
</script>
<script type="text/html" id="tmpl-builder_inline_editor">
    <ul id="tb_editor">
        <li class="themify-tooltip-top ti-paragraph tb_editor_paragraph">
            <span class="themify_tooltip"><?php _e('Paragprah', 'themify') ?></span>
            <ul data-action="p">
                <li>P</li>
                <li>H1</li>
                <li>H2</li>
                <li>H3</li>
                <li>H4</li>
                <li>H5</li>
                <li>H6</li>
            </ul>
        </li>
        <li class="themify-tooltip-top ti-align-left tb_editor_text_align">
            <span class="themify_tooltip"><?php _e('Text Align', 'themify') ?></span>
            <ul data-action="text_align">
                <li data-action="l" class="themify-tooltip-top ti-align-left"><span class="themify_tooltip"><?php _e('Left', 'themify') ?></span></li>
                <li data-action="c" class="themify-tooltip-top ti-align-center"><span class="themify_tooltip"><?php _e('Center', 'themify') ?></span></li>
                <li data-action="r" class="themify-tooltip-top ti-align-right"><span class="themify_tooltip"><?php _e('Right', 'themify') ?></span></li>
                <li data-action="j" class="themify-tooltip-top ti-align-justify"><span class="themify_tooltip"><?php _e('Justify', 'themify') ?></span></li>
            </ul>
        </li>
        <li class="themify-tooltip-top ti-link tb_editor_link" data-action="link">
            <span class="themify_tooltip"><?php _e('Link', 'themify') ?></span>
        </li>
        <li class="themify-tooltip-top tb_editor_bold" data-action="bold">
            <span class="themify_tooltip"><?php _e('Bold', 'themify') ?></span>
        </li>
        <li class="themify-tooltip-top ti-Italic tb_editor_italic" data-action="i">
            <span class="themify_tooltip"><?php _e('Italic', 'themify') ?></span>
        </li>
        <li class="themify-tooltip-top ti-underline tb_editor_text_decoration">
            <span class="themify_tooltip"><?php _e('Text Decoration', 'themify') ?></span>
            <ul data-action="text_decoration">
                <li class="themify-tooltip-top"><span class="themify_tooltip"><?php _e('Underline', 'themify') ?></span></li>
                <li class="themify-tooltip-top"><span class="themify_tooltip"><?php _e('Center', 'themify') ?></span></li>
                <li class="themify-tooltip-top"><span class="themify_tooltip"><?php _e('Right', 'themify') ?></span></li>
                <li class="themify-tooltip-top"><span class="themify_tooltip"><?php _e('Justify', 'themify') ?></span></li>
            </ul>
        </li>
        <li class="themify-tooltip-top ti-list tb_editor_list">
            <span class="themify_tooltip"><?php _e('List Settings', 'themify') ?></span>
            <ul data-action="list">
                <li class="themify-tooltip-top ti-list" data-action="underscore"><span class="themify_tooltip"><?php _e('Underscore List', 'themify') ?></span></li>
                <li class="themify-tooltip-top ti-list-ol" data-action="ordered"><span class="themify_tooltip"><?php _e('Ordered List', 'themify') ?></span></li>
                <li class="themify-tooltip-top" data-action="indent"><span class="themify_tooltip"><?php _e('Indent List', 'themify') ?></span></li>
                <li class="themify-tooltip-top" data-action="undent"><span class="themify_tooltip"><?php _e('Undent List', 'themify') ?></span></li>
            </ul>
        </li>
        <li class="themify-tooltip-top ti-paint-bucket tb_editor_color" data-action="color">
            <span class="themify_tooltip"><?php _e('Text Color', 'themify') ?></span>
        </li>
        <li class="themify-tooltip-top tb_editor_fonts" data-action="fonts">
            <span class="themify_tooltip"><?php _e('Fonts', 'themify') ?></span>
        </li>
        <li class="themify-tooltip-top ti-new-window tb_editor_expand" data-action="expand">
            <span class="themify_tooltip"><?php _e('Expand', 'themify') ?></span>
        </li>
    </ul>
</script>