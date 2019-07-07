<form id="tb_import_form" method="POST">

	<div id="tb_lightbox_options_tab_items">
		<li class="title"><?php _e( 'Import From', 'themify' ); ?></li>
	</div>

	<div id="tb_lightbox_actions_items"></div>

	<div class="tb_options_tab_wrapper">
		<div class="tb_options_tab_content">
			<?php foreach( $data as $field ): ?>
			<div class="tb_field">
				<div class="tb_label"><?php echo esc_html( $field['label'] ); ?></div>
				<div class="tb_input">
                                    <div class="selectwrapper">
					<select name="<?php  echo esc_attr( $field['post_type'] ); ?>">
						<?php foreach( $field['items'] as $option ): ?>
                                                    <option value="<?php  echo esc_attr( $option->ID ); ?>"><?php echo esc_html( $option->post_title ); ?></option>
						<?php endforeach; ?>
					</select>
                                    </div>
				</div>
			</div>
			<?php endforeach; ?>

			<button id="tb_submit_import_form" class="builder_button"><?php _e('Import', 'themify') ?></button>
		</div>
	</div>

</form>
