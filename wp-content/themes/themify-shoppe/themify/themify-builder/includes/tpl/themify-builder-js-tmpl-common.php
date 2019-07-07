<script type="text/html" id="tmpl-builder_lightbox">
<?php //create fix overlay on top iframe,mouse position will be always on top iframe on resizing  ?>
    <div class="tb_resizable_overlay"></div>
    <div id="tb_lightbox_parent" class="themify_builder themify_builder_admin builder-lightbox {{ data.is_themify_theme }}">
        <div class="tb_lightbox_top_bar clearfix">
            <ul class="tb_options_tab clearfix"></ul>
            <div class="tb_lightbox_actions">
                <a class="builder_cancel_docked_mode"><i class="ti-new-window"></i></a>
                <div class="tb_close_lightbox"><?php _e('Cancel', 'themify') ?><i class="ti-close"></i></div>
                <span class="tb_lightbox_actions_wrap"></span>	
            </div>
        </div>
        <div id="tb_lightbox_container"></div>
        <div class="tb_resizable tb_resizable-e"></div>
        <div class="tb_resizable tb_resizable-s"></div>
        <div class="tb_resizable tb_resizable-w"></div>
        <div class="tb_resizable tb_resizable-se"></div>
    </div>
</script>
<script type="text/html" id="tmpl-builder_lite_lightbox_confirm">
    <p>{{ data.message }}</p>
    <p>
        <# _.each(data.buttons, function(value, key) { #> 
        <button data-type="{{ key }}">{{ value.label }}</button> 
        <# }); #>
    </p>
</script>
<script type="text/html" id="tmpl-builder_lite_lightbox_prompt">
    <p>{{ data.message }}</p>
    <p><input type="text" class="tb_litelightbox_prompt_input"></p>
    <p>
        <# _.each(data.buttons, function(value, key) { #> 
        <button data-type="{{ key }}">{{ value.label }}</button> 
        <# }); #>
    </p>
</script>
<script type="text/html" id="tmpl-builder_row_item">
    <div class="page-break-overlay"></div>
    <div class="tb_action_wrap tb_row_actions">
        <div class="tb_grid_icon ti-view-list"></div>
        <?php Themify_Builder_Model::grid('row'); ?>
        <ul class="tb_dropdown">
            <li>
				<div class="tb_option_row ti-settings themify-tooltip-bottom">
					<div class="themify_tooltip"><?php _e('Options', 'themify') ?></div>
				</div>
				<div class="tb_options_row_hover">
					<a href="#" class="tb_row_hover__expand"><i class="ti-new-window"></i></a>
					<ul>
						<li>
							<div class="tb_row_hover__label"><?php esc_html_e( 'Width', 'themify' ) ?></div>
							<div class="tb_row_hover__option">
								<ul class="tb_row_hover__icon tb_row_hover__input" data-option="row_width">
									<li class="tb_row_hover__icon--default themify-tooltip-top selected" data-value>
										<div class="themify_tooltip"><?php _e('Default', 'themify') ?></div>
									</li>
									<li class="tb_row_hover__icon--container themify-tooltip-top" data-value="fullwidth">
										<div class="themify_tooltip"><?php _e('Full width with container', 'themify') ?></div>
									</li>
									<li class="tb_row_hover__icon--fullwidth themify-tooltip-top" data-value="fullwidth-content">
										<div class="themify_tooltip"><?php _e('Full width', 'themify') ?></div>
									</li>
								</ul>
							</div>
						</li>
						<li>
							<div class="tb_row_hover__label"><?php esc_html_e( 'Height', 'themify' ) ?></div>
							<div class="tb_row_hover__option">
								<ul class="tb_row_hover__icon tb_row_hover__input" data-option="row_height">
									<li class="tb_row_hover__icon--default themify-tooltip-top selected" data-value>
										<div class="themify_tooltip"><?php _e('Default', 'themify') ?></div>
									</li>
									<li class="tb_row_hover__icon--fullheight themify-tooltip-top" data-value="fullheight">
										<div class="themify_tooltip"><?php _e('Full height', 'themify') ?></div>
									</li>
								</ul>
							</div>
						</li>
						<li>
							<div class="tb_row_hover__label"><?php esc_html_e( 'CSS Class', 'themify' ) ?></div>
							<div class="tb_row_hover__option"><input type="text" class="tb_row_hover__input" data-option="custom_css_row"></div>
						</li>
						<li>
							<div class="tb_row_hover__label"><?php esc_html_e( 'Anchor', 'themify' ) ?></div>
							<div class="tb_row_hover__option"><input type="text" class="tb_row_hover__input" data-option="row_anchor"></div>
						</li>
					</ul>
				</div>
			</li>
            <li><div class="tb_style_row ti-brush themify-tooltip-bottom">
                    <div class="themify_tooltip"><?php _e('Styling', 'themify') ?></div>
                </div></li>
            <li><div class="tb_duplicate ti-layers themify-tooltip-bottom">
                    <div class="themify_tooltip"><?php _e('Duplicate', 'themify') ?></div>
                </div></li>
            <li><div class="tb_save_component ti-save themify-tooltip-bottom"  data-component="row">
                    <div class="themify_tooltip"><?php _e('Save', 'themify') ?></div>
                </div></li>

            <li class="tb_delete_row_container">
                <div class="tb_delete ti-close themify-tooltip-bottom">
                    <div class="themify_tooltip"><?php _e('Delete', 'themify') ?></div>
                </div>
            </li>
            <li class="tb_action_more">
                <div class="ti-more"></div>
                <ul>
                    <li>
                        <div class="tb_export_component ti-export" data-component="row">
                            <?php _e('Export', 'themify') ?>
                        </div>
                    </li>
                    <li>
                        <div class="tb_import_component ti-import" data-component="row">
                            <?php _e('Import', 'themify') ?>
                        </div>
                    </li>
                    <li class="tb_inner_action_more">
                        <div class="ti-files"> <?php _e('Copy', 'themify') ?> </div>
                        <ul>
                            <li><div class="tb_copy_component"> <?php _e('Content & Styling', 'themify') ?> </div></li>
                            <li><div class="tb_copy_style"> <?php _e('Styling Only', 'themify') ?> </div></li>
                        </ul>
                        
                    </li>
                    <li  class="tb_inner_action_more">
                        <div class="ti-clipboard"> <?php _e('Paste', 'themify') ?></div>
                        <ul>
                            <li><div class="tb_paste_component"> <?php _e('Content & Styling', 'themify') ?> </div></li>
                            <li><div class="tb_paste_style"> <?php _e('Styling Only', 'themify') ?> </div></li>
                        </ul>
                    </li>
                    <li>
                        <div  class="tb_visibility_component ti-eye">
                            <?php _e('Visibility', 'themify') ?>
                        </div>
                    </li>
                </ul>
            </li>
        </ul>
        <span class="tb_row_anchor"></span>
    </div>
    <div class="row_inner"></div>
    <div class="tb_row_btn_plus"></div>
</script>
<script type="text/html" id="tmpl-builder_subrow_item">
    <div class="tb_action_wrap tb_subrow_actions">
        <div class="tb_grid_icon ti-view-list"></div>
        <?php Themify_Builder_Model::grid('subrow'); ?>
        <ul class="tb_dropdown">
            <li>
                <div class="tb_style_subrow ti-brush themify-tooltip-bottom">
                    <div class="themify_tooltip"><?php _e('Styling', 'themify') ?></div>
                </div>
            </li>
            <li><div class="tb_duplicate ti-layers themify-tooltip-bottom">
                    <div class="themify_tooltip"><?php _e('Duplicate', 'themify') ?></div>
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
                    <li>
                        <div class="tb_export_component ti-export" data-component="subrow">
                            <?php _e('Export', 'themify') ?>
                        </div>
                    </li>
                    <li>
                        <div class="tb_import_component ti-import" data-component="subrow">
                            <?php _e('Import', 'themify') ?>
                        </div>
                    </li>
                    <li class="tb_inner_action_more">
                        <div class="ti-files"> <?php _e('Copy', 'themify') ?> </div>
                        <ul>
                            <li><div class="tb_copy_component"> <?php _e('Content & Styling', 'themify') ?> </div></li>
                            <li><div class="tb_copy_style"> <?php _e('Styling Only', 'themify') ?> </div></li>
                        </ul>
                        
                    </li>
                    <li  class="tb_inner_action_more">
                        <div class="ti-clipboard"> <?php _e('Paste', 'themify') ?></div>
                        <ul>
                            <li><div class="tb_paste_component"> <?php _e('Content & Styling', 'themify') ?> </div></li>
                            <li><div class="tb_paste_style"> <?php _e('Styling Only', 'themify') ?> </div></li>
                        </ul>
                    </li>
                    <li>
                        <div  class="tb_visibility_subrow ti-eye">
							<?php _e('Visibility', 'themify') ?>
                        </div>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
    <div class="subrow_inner"></div>
</script>
<script type="text/html" id="tmpl-builder_column_item">
    <div class="tb_grid_drag tb_drag_right tb_disable_sorting"></div>
    <div class="tb_grid_drag tb_drag_left tb_disable_sorting"></div>
    <div class="tb_action_wrap tb_col_actions">
        <div class="tb_grid_icon ti-layout-column3"></div>
        <ul class="tb_dropdown">
            <li>
                <div  class="tb_option_column ti-brush themify-tooltip-bottom">
                    <div class="themify_tooltip"><?php _e('Styling', 'themify') ?></div>
                </div>
            </li>
            <li class="tb_action_more">
                <div class="ti-more"></div>
                <ul>
                    <li>
                        <div  class="tb_export_component ti-export" data-component="{{ data.component_name }}">
                            <?php _e('Export', 'themify') ?>
                        </div>
                    </li>
                    <li>
                        <div  class="tb_import_component ti-import" data-component="{{ data.component_name }}">
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
                </ul>
            </li>
        </ul>
    </div>
    <div class="tb_holder"></div>
</script>
<script type="text/html" id="tmpl-builder_module_item_draggable">
    <div class="tb_module_outer<# if(data.favorite){ #> favorited<# } #> tb-module-{{data.slug}}">
        <div class="tb_module tb-module-type-{{data.slug}}" data-type="{{data.type}}" data-module-slug="{{data.slug}}">
            <span class="tb_favorite ti-star tb_disable_sorting"></span>
            <strong class="module_name">{{data.name}}</strong> <a href="#" class="add_module_btn tb_disable_sorting" title="<?php esc_attr_e('Add module', 'themify'); ?>"></a>
        </div>
    </div>
</script>