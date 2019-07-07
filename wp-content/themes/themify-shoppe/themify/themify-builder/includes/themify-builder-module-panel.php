<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Builder Frontend Panel HTML
 */
global $post;
if (!is_object($post)) {
    return;
}
?>
<div id="tb_toolbar">
    <div class="tb_toolbar_add_modules_wrap" tabindex="-1">
        <span class="tb_toolbar_add_modules"></span>
        <div id="tb_module_panel" class="tb_modules_panel_wrap">
            <div class="tb_module_panel_search">
                <input type="text" class="tb_module_panel_search_text"/>
            </div>
            <a href="#" class="tb_module_panel_lock"><i class="ti-lock"></i></a>
            <ul class="tb_module_types">
                <li class="active"><a href="#" data-hide="tb_module_panel_tab" data-target="tb_module_panel_modules_wrap"><?php _e('Modules', 'themify') ?></a></li>
                <li><a href="#" data-hide="tb_module_panel_tab" data-target="tb_module_panel_rows_wrap"><?php _e('Rows', 'themify') ?></a></li>
                <li><a href="#" data-hide="tb_module_panel_tab" data-target="tb_module_panel_library_wrap"><?php _e('Library', 'themify') ?></a></li>
            </ul>
            <div class="tb_module_panel_tab tb_module_panel_modules_wrap"></div>
            <!-- /tb_module_panel_modules_wrap -->
            <div class="tb_module_panel_tab tb_module_panel_rows_wrap">
                <ul class="tb_rows_grid">
                    <li>
                        <div class="tb_row_grid tb_row_grid_1" data-slug="1">
                            <div class="tb_row_grid_title"><?php _e('One Column Row', 'themify'); ?></div>
                        </div>
                    </li>
                    <li>
                        <div class="tb_row_grid tb_row_grid_2" data-slug="2">
                            <div class="tb_row_grid_title"><?php _e('Two Columns Row', 'themify'); ?></div>
                        </div>
                    </li>
                    <li>
                        <div class="tb_row_grid tb_row_grid_3" data-slug="3">
                            <div class="tb_row_grid_title"><?php _e('Three Columns Row', 'themify'); ?></div>
                        </div>
                    </li>
                    <li>
                        <div class="tb_row_grid tb_row_grid_4" data-slug="4">
                            <div class="tb_row_grid_title"><?php _e('Four Columns Row', 'themify'); ?></div>
                        </div>
                    </li>
                    <li>
                        <div class="tb_row_grid tb_row_grid_5" data-slug="5">
                            <div class="tb_row_grid_title"><?php _e('Five Columns Row', 'themify'); ?></div>
                        </div>
                    </li>
                    <li>
                        <div class="tb_row_grid tb_row_grid_6" data-slug="6">
                            <div class="tb_row_grid_title"><?php _e('Six Columns Row', 'themify'); ?></div>
                        </div>
                    </li>
                </ul>
                <div class="tb_page_break_module">
                    <div class="tb_page_break_title"><?php _e('Page Break', 'themify'); ?></div>
                </div>
                <div class="tb_ui_dropdown">
                    <span class="tb_ui_dropdown_label"><?php _e('All', 'themify') ?></span>
                    <!-- /tb_row_cat_filter_active -->
                    <ul class="tb_row_filter tb_ui_dropdown_items">
                        <li><a href="#"><?php _e('All', 'themify') ?></a></li>
                    </ul>
                    <!-- /tb_row_cat_filter -->
                </div>
                <!-- /tb_row_cat_filter_wrap -->
                <div class="tb_predesigned_rows_list">
                    <div class="tb_predesigned_rows_container"></div>
                </div>
                <!-- /tb_predesigned_rows_list -->
            </div>
            <div class="tb_module_panel_tab tb_module_panel_library_wrap">
                <ul class="tb_module_types tb_library_types">
                    <li class="active"><a href="#" data-type="row" data-hide="tb_library_item" data-target="tb_item_row"><?php _e('Rows', 'themify') ?></a></li>
                    <li><a href="#" data-type="module" data-hide="tb_library_item" data-target="tb_item_module"><?php _e('Modules', 'themify') ?></a></li>
                    <li><a href="#" data-type="part" data-hide="tb_library_item" data-target="tb_item_part"><?php _e('Parts', 'themify') ?></a></li>
                </ul>
                <!-- /tb_library_types -->
                <div class="tb_library_item_list"></div>
                <!-- /tb_library_item_list -->
            </div>
            <!-- /tb_module_panel_library_wrap -->
        </div>
    </div>

    <!-- /tb_module_panel -->

    <ul class="tb_toolbar_menu">
        <li class="tb_toolbar_zoom_menu"><a href="#" class="tb_toolbar_zoom_menu_toggle" data-zoom="100"><i class="ti-zoom-in"></i></a>
            <ul>
                <li><a href="#" class="tb_zoom" data-zoom="50"><?php _e('50%', 'themify'); ?></a></li>
                <li><a href="#" class="tb_zoom" data-zoom="75"><?php _e('75%', 'themify'); ?></a></li>
                <li><a href="#" class="tb_zoom" data-zoom="100"><?php _e('100%', 'themify'); ?></a></li>
            </ul>
        </li>
        <li class="tb_toolbar_divider hide-if-backend"></li>
        <li class="hide-if-backend"><a href="#" class="tb_tooltip tb_toolbar_builder_preview"><i class="ti-layout-media-center-alt"></i><span><?php _e('Preview', 'themify'); ?></span></a></li>
        <li class="tb_toolbar_divider hide-if-backend"></li>
        <?php
        $breakpoints = themify_get_breakpoints();
        $is_premium = Themify_Builder_Model::is_premium();
        $breakpoints = array_merge(array('desktop'=>''),$breakpoints);
        ?>
        <li class="tb_toolbar_desktop_switcher">
            <a href="javascript:void(0);" class="tb_tooltip tb_compact tb_compact_switcher breakpoint-desktop"><i class="ti-desktop"></i>
                <span><?php _e('Desktop',  'themify'); ?></span>
            </a>
            <ul class="tb_toolbar_down">
                <?php foreach ($breakpoints as $b => $v): ?>
                    <li class="tb_toolbar_<?php echo strtolower( $b ); ?>_switcher <?php if(!$is_premium && $b!=='desktop'):?>tb_lite<?php endif;?>">
                        <?php if(!$is_premium && $b!=='desktop'):?><span class="themify_lite_tooltip"></span><?php endif;?>
                        <a href="#" class="tb_tooltip tb_breakpoint_switcher breakpoint-<?php echo $b?>"><i class="<?php if($b==='tablet_landscape'):?>ti-tablet <?php endif;?>ti-<?php echo $b?>"></i>
                        <?php $b = $b==='tablet_landscape' ? __( 'Tablet Landscape', 'themify' ) : ucfirst($b);?>
                        <span><?php printf('%s', $b);?></span></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </li>
        <li class="tb_toolbar_divider"></li>
        <li class="tb_toolbar_undo">
            <a href="javascript:void(0);" class="tb_tooltip tb_compact tb_compact_undo tb_disabled"><i class="ti-back-left"></i><span><?php _e('Undo (CTRL+Z)', 'themify'); ?></span></a>
            <ul class="tb_toolbar_down">
                <li><a href="#" class="tb_tooltip tb_undo_redo tb_undo_btn tb_disabled"><i class="ti-back-left"></i><span><?php _e('Undo (CTRL+Z)', 'themify'); ?></span></a></li>
                <li><a href="#" class="tb_tooltip tb_undo_redo tb_redo_btn tb_disabled"><i class="ti-back-right"></i><span><?php _e('Redo (CTRL+SHIFT+Z)', 'themify'); ?></span></a></li>
            </ul>
        </li>
        <li class="tb_toolbar_divider"></li>
        <li class="tb_toolbar_import">
            <a href="javascript:void(0);" class="tb_compact"><i class="ti-import"></i></a>
            <ul class="tb_toolbar_down">
                <li>
                    <a href="javascript:void(0);" class="tb_import_btn"><i class="ti-import"></i><span><?php _e('Import', 'themify'); ?></span></a>
                    <ul>
                        <li><a href="#" data-component="file" class="tb_import"><?php _e('Import From File', 'themify'); ?></a></li>
                        <li><a href="#" data-component="page" class="tb_import"><?php _e('Import From Page', 'themify'); ?></a></li>
                        <li><a href="#" data-component="post" class="tb_import"><?php _e('Import From Post', 'themify'); ?></a></li>
                    </ul>
                </li>
                <li class="tb_toolbar_export"><a href="<?php echo wp_nonce_url('?themify_builder_export_file=true&postid=' . $post->ID, 'themify_builder_export_nonce') ?>" class="tb_tooltip tb_export_link"><i class="ti-export"></i><span><?php _e('Export', 'themify'); ?></span></a></li>
            </ul>
        </li>
        <li class="tb_toolbar_divider"></li>
        <li class="tb_toolbar_layout"><a href="javascript:void(0);"><i class="ti-layout"></i></a>
            <ul>
                <li<?php if (!$is_premium): ?> class="tb_lite"<?php endif; ?>><?php if (!$is_premium): ?><span class="themify_lite_tooltip"></span><?php endif; ?><a href="#" class="tb_load_layout"><?php _e('Load Layout', 'themify'); ?></a></li>
                <li<?php if (!$is_premium): ?> class="tb_lite"<?php endif; ?>><?php if (!$is_premium): ?><span class="themify_lite_tooltip"></span><?php endif; ?><a href="#" class="tb_save_layout"><?php _e('Save as Layout', 'themify'); ?></a></li>
            </ul>
        </li>
        <li class="tb_toolbar_divider"></li>
        <li><a href="#" class="tb_tooltip tb_dup_link"><i class="ti-layers"></i><span><?php _e('Duplicate this page', 'themify'); ?></span></a></li>
        <li class="tb_toolbar_divider"></li>
        <li><a href="javascript:void(0);" class="tb_tooltip tb_help_btn"><i class="ti-help"></i><span><?php _e('Help', 'themify'); ?></span></a></li>
    </ul>

    <div class="tb_toolbar_save_wrap">
        <?php if ($post->post_status !== 'auto-draft'): ?>
            <div class="tb_toolbar_backend_edit">
                <?php if (is_admin()): ?>
                    <a href="<?php echo get_permalink() ?>#builder_active" id="tb_switch_frontend" class="tb_switch_frontend"><i class="ti-arrow-right"></i><span><?php _e('Frontend', 'themify'); ?></span></a>
                <?php else: ?>
                    <a href="<?php echo get_edit_post_link(); ?>" id="tb_switch_backend"><i class="ti-arrow-left"></i><span><?php esc_html_e('Backend', 'themify'); ?></span></a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <div class="tb_toolbar_close">
            <a href="#"  class="tb_tooltip tb_toolbar_close_btn" title="<?php _e('ESC', 'themify') ?>"><i class="ti-close"></i><span><?php _e('Close', 'themify'); ?></span></a>
        </div>
        <!-- /tb_toolbar_close -->
        <div class="tb_toolbar_save_btn">
            <a href="#" class="tb_toolbar_save" title="<?php _e('Ctrl + S', 'themify') ?>"><?php _e('Save', 'themify'); ?></a>
            <div tabindex="1" class="tb_toolbar_revision_btn">
                <span class="ti-angle-down"></span>
                <ul>
                    <li<?php if (!$is_premium): ?> class="tb_lite"<?php endif; ?>><?php if (!$is_premium): ?><span class="themify_lite_tooltip"></span><?php endif; ?><a href="#" class="tb_revision tb_save_revision"><?php _e('Save as Revision', 'themify'); ?></a></li>
                    <li<?php if (!$is_premium): ?> class="tb_lite"<?php endif; ?>><?php if (!$is_premium): ?><span class="themify_lite_tooltip"></span><?php endif; ?><a href="#" class="tb_revision tb_load_revision"><?php _e('Load Revision', 'themify'); ?></a></li>
                </ul>
            </div>
        </div>
        <!-- /tb_toolbar_save_btn -->
    </div>
    <!-- /tb_toolbar_save_wrap -->
</div>
<!-- /tb_toolbar -->
<?php if(!isset($_COOKIE['tb_first_on']) && !is_admin()):?>
    <div id="tb_first_help" class="tb_help_lightbox">
        <div class="tb_help_video_wrapper tb_first_help_video">
            <div class="tb_player_wrapper">
                <span class="tb_player_btn"></span>
                <img src="<?php echo THEMIFY_BUILDER_URI?>/img/quick-tour.jpg"/>
            </div>
        </div>
        <div class="tb_first_help_message">
            <h2><?php _e('Welcome','themify')?></h2>
            <p>
                New to Themify Builder? Watch our brief tutorial videos for a quick crash course. Or watch them later by clicking the ‘?’ button in the top toolbar.
            </p>
            <div class="tb_first_button_wrap">
                <button class="tb_watch"><?php _e('Watch Videos','themify')?></button>
                <a class="tb_start_building" href="javascript:void(0);"><?php _e('Start Building','themify')?></a>
            </div>
        </div>
        <div class="tb_close_lightbox"><i class="ti-close"></i></div>
    </div>
<?php endif; ?>
