<form id="tb_save_layout_form">
	<div id="tb_lightbox_options_tab_items">
		<li class="title"><?php _e('Save as Layout', 'themify'); ?></li>
	</div>
	<div id="tb_lightbox_actions_items">
		<button id="builder_submit_layout_form" class="builder_button"><?php _e('Save', 'themify') ?></button>
	</div>
	<div class="tb_options_tab_wrapper">
		<div class="tb_options_tab_content">
			<?php themify_builder_module_settings_field( $fields ); ?>
		</div>
	</div>
	<input type="hidden" name="postid" value="<?php echo $postid; ?>">
</form>