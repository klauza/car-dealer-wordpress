<?php
/**
 * Fontello icon font
 * @link http://fontello.com/
 */
class Themify_Icon_Picker_Fontello extends Themify_Icon_Picker_Font {

	function get_id() {
		return 'fontello';
	}

	function get_label() {
		return __( 'Fontello', 'themify' );
	}

	/**
	 * Check if the icon name belongs to the Fontello icon font
	 *
	 * @return bool
	 */
	function is_valid_icon( $name ) {
		if( substr( $name, 0, 5 ) === 'icon-' ) {
			return true;
		}

		return false;
	}

	function get_classname( $icon ) {
		return $icon;
	}

	/**
	 * Get a list of available icons from the config.json file provided by Fontello
	 *
	 * @return array
	 */
	function get_icons() {
		$icons = array();
		$config = themify_fontello_get_config();
		if( $config ) {
			if( ! empty( $config['glyphs'] ) ) {
				foreach( $config['glyphs'] as $glyph ) {

					/* custom icons uploaded but not selected are still included in the list; skip over those. */
					if( isset( $glyph['src'] ) && $glyph['src'] === 'custom_icons' && $glyph['selected'] == false ) {
						continue;
					}

					$icons[ 'icon-' . $glyph['css'] ] = $glyph['css'];
				}
			}
		}
		return array(
			array(
				'key' => 'custom',
				'label' => __( 'Icons', 'themify' ),
				'icons' => $icons
			),
		);
	}

	function picker_template() {
		if( themify_check( 'setting-fontello' ) ) {
			parent::picker_template();
		} else {
			?>
			<div class="tf-font-group" data-group="<?php echo $this->get_id(); ?>">
				<?php printf( __( 'To add icons here: go to <a href="http://fontello.com" target="_blank">fontello.com</a> and create a package. Then go to <a href="%s">Themify > Settings > Custom Icon Font</a> to upload the icon font package.', 'themify' ), admin_url( 'admin.php?page=themify#setting-custom-icon-font' ) ); ?>
			</div><!-- .tf-font-group -->
			<?php
		}
	}
}