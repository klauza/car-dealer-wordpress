<?php
/**
 * Add support for Fontello packages to Themify framework
 *
 * @package Themify
 * @since 3.0.4
 */

function themify_register_fontello_font( $icon_picker ) {
	include_once( trailingslashit( THEMIFY_DIR ) . 'themify-fontello-icon-picker.php' );
	$icon_picker->register( 'Themify_Icon_Picker_Fontello' );
}
add_action( 'themify_icon_picker_register_types', 'themify_register_fontello_font' );

/**
 * Add separate tab for the Fontello settings, into Themify > Settings page
 *
 * @return array
 */
function themify_fontello_config_setup( $config ) {
	$config['panel']['settings']['tab']['custom-icon-font'] = array(
		'title' => __( 'Custom Icon Font', 'themify' ),
		'id' => 'custom-icon-font',
		'custom-module' => array(
			array(
				'title' => __( 'Custom Icon Font', 'themify' ),
				'function' => 'themify_fontello_input_callback',
			),
		)
	);
	return $config;
}
add_filter( 'themify_theme_config_setup', 'themify_fontello_config_setup', 30 );

/**
 * Render the input field to allow uploading font packages
 *
 * @return string
 */
function themify_fontello_input_callback( $data = array() ) {
	return '
	<div class="themify_field_row">
		<span class="label">'. __('Icon Font Package', 'themify') . '</span>
		<input id="setting-fontello" type="text" class="width10" name="setting-fontello" value="' . esc_attr( themify_get( 'setting-fontello' ) ) . '" /> <br />
		<div class="pushlabel" style="display:block;">
			<div class="themify_medialib_wrapper">
				<a href="#" class="themify-media-lib-browse" data-submit=\'' . json_encode( array( 'action' => 'themify_handle_fontello_upload', 'field_name' => 'setting-fontello' ) ) . '\' data-uploader-title="' . __( 'Upload package', 'themify' ) .'" data-uploader-button-text="'. __( 'Upload package', 'themify' ) .'" data-fields="setting-fontello" data-type="application/zip">'. __( 'Browse Library', 'themify' ) . '</a>
			</div>
			<br>
			<small>
			'. sprintf( __( 'Go to <a href="%s">fontello.com</a>, pick your icons, download the webfont zip, upload and insert the zip URL here. The icon package will be auto detected on Themify\'s icon library where you click "Insert Icon".', 'themify' ), 'http://fontello.com' ) .'
			</small>
		</div>
	</div>';
}

/**
 * Enqueue the font stylesheet on frontend
 *
 * This loads on both frontend, and everywhere the Icon Picker is called
 */
function themify_enqueue_fontello() {
	if( themify_check( 'setting-fontello' ) && $path = themify_fontello_path() ) {
		wp_enqueue_style( 'themify-fontello', $path['url'] . 'css/' . themify_fontello_get_config( 'name', 'fontello' ) . '-embedded.css' );
	}
}
add_action( 'themify_icon_picker_enqueue', 'themify_enqueue_fontello' );

function themify_fontello_main_script_vars( $vars ) {
	if( themify_check( 'setting-fontello' ) && $path = themify_fontello_path() ) {
		$vars['fontello_path' ] = $path['url'] . 'css/' . themify_fontello_get_config( 'name', 'fontello' ) . '-embedded.css';
	}

	return $vars;
}
add_filter( 'themify_main_script_vars', 'themify_fontello_main_script_vars' );

/**
 * Gets the path to the Fontello assets
 * Extracts the uploaded package file automatically if necessary
 *
 * @return array|bool
 */
function themify_fontello_path() {
	$upload_dir = wp_upload_dir();
	$dest = trailingslashit( $upload_dir['basedir'] ) . 'fontello/';
	$path = themify_get( 'setting-fontello' );
	// -n suffix in the filename is added by WP, if multiple files of the same names are uploaded
	$base = preg_replace( '/(-\d+)?\.zip$/', '', basename( $path ) );

	// directory exists
	if( is_dir( $dest . $base ) ) {
		$result = 1;
	} else { // attempt to extract the file
		// get the system path from URL
		$path = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $path );
		WP_Filesystem();
		$result = unzip_file( $path, $dest );
	}

	if( is_wp_error( $result ) ) {
		return false;
	} elseif( 1 == $result ) {
		return array(
			'url' => trailingslashit( $upload_dir['baseurl'] ) . 'fontello/' . trailingslashit( $base ),
			'dir' => trailingslashit( $dest . $base )
		);
	}
}

/**
 * Get Fontello configration from config.json file
 *
 * Can optionally provide $name to retrieve a specific key
 *
 * @return array|false
 */
function themify_fontello_get_config( $name = null, $default = null ) {
	$config = false;
	if( themify_check( 'setting-fontello' ) ) {
		$path = themify_fontello_path();
		if( $path ) {
			if( $config = themify_get_file_contents( $path['dir'] . 'config.json' ) ) {
				$config = json_decode( $config, true );
				if( isset( $name ) ) {
					if( ! empty( $config[ $name ] ) ) {
						$config = $config[ $name ];
					} else {
						$config = $default;
					}
				}
			}
		}
	}

	return $config;
}