<form id="tb_library_item_form">
	<div id="tb_lightbox_options_tab_items">
            <li class="title"><?php _e($type==='module'?'Save Module':'Save Row', 'themify'); ?></li>
	</div>
	<div id="tb_lightbox_actions_items">
		<button id="builder_submit_library_item_form" class="builder_button"><?php _e('Save', 'themify') ?></button>
	</div>
	<div id="tb_layout_part_form" class="tb_options_tab_wrapper">
		<div class="tb_options_tab_content"><?php themify_builder_module_settings_field( $fields,'' ); ?></div>
		<input type="hidden" name="postid" value="<?php echo $postid; ?>" />
                <input type="hidden" name="nonce" value=""/>
		<input type="hidden" name="item" />
                <input type="hidden" name="action" value="tb_save_custom_item" />
		<input type="hidden" name="type" value="<?php echo $type; ?>" />
		<input type="hidden" name="model" value="<?php  esc_attr_e( $model ); ?>" />
	</div>
</form>