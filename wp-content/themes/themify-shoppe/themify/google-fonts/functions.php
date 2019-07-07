<?php

if( ! function_exists( 'themify_get_google_font_lists' ) ) :

/**
 * Get google font lists
 * @return array
 */
function themify_get_google_font_lists() {
	if( defined( 'THEMIFY_GOOGLE_FONTS' ) && THEMIFY_GOOGLE_FONTS != true ) {
		return array();
	}

	/* cache */
	static $fonts = null;
	if( null == $fonts ) {
		$fonts = themify_grab_remote_google_fonts();
	}

	return $fonts;
}

/**
 * Return file to use depending if user selected Recommended or Full list in theme settings.
 *
 * @since 2.1.7
 *
 * @return string
 */
function themify_get_google_fonts_file() {
	if( apply_filters( 'themify_google_fonts_full_list', false ) ) {
		$fonts = dirname( __FILE__ ) . '/google-fonts.php';
	} else {
		$fonts = dirname( __FILE__ ) . '/google-fonts-recommended.php';
	}

	/**
	 * Filters the file loaded.
	 * Useful for recovery in case user loaded Full List and their server can't manage it.
	 * @param string $fonts
	 */
	return apply_filters( 'themify_google_fonts_file', $fonts );
}

/**
 * Grab google fonts lists from api
 * @return array
 */
function themify_grab_remote_google_fonts() {
	$fonts_file_path = themify_get_google_fonts_file();
	$subsets = themify_get_font_subsets();
	$subsets_count = count( $subsets );

	$fonts = array();
	$results = include( $fonts_file_path );
	if ( $results !== false ) {
		foreach ( $results as $font ) {
			// If user specified additional subsets
			if ( $subsets_count > 1) {
				$font_subsets = $font['subsets'];
				$subsets_match = true;
				// Check that all specified subsets are available in this font
				foreach ( $subsets as $subset ) {
					if ( ! in_array( $subset, $font_subsets ) ) {
						$subsets_match = false;
					}
				}
				// Ok, this font supports all subsets requested by user, add it to the list
				if( $subsets_match ) {
					$fonts[] = array(
						'family' => $font['label'],
						'variant' => $font['variants']
					);
				}
			} else {
				$fonts[] = array(
					'family' => $font['label'],
					'variant' => $font['variants']
				);
			}
		}
	}

	return $fonts;
}

/**
 * Returns a list of font subsets enabled
 *
 * @return array
 */
function themify_get_font_subsets() {
	return apply_filters( 'themify_google_fonts_subsets', array( 'latin' ) );
}

/**
 * Check if given value is google fonts or web safe fonts
 * @param string $value
 * @return boolean
 */
function themify_is_google_fonts( $value ) {
	$found = false;
	$themify_gfonts = themify_get_google_font_lists();
	if ( sizeof( $themify_gfonts ) > 0 ) {
		foreach ( $themify_gfonts as $font ) {
			if ( $found ) break;
			if ( $font['family'] == $value ) $found = true;
		}
	}
	return $found;
}

/**
 * Get font default variant
 * @param $family
 * @return string
 */
function themify_get_gfont_variant( $family ) {
	$variant = 400;
	$themify_gfonts = themify_get_google_font_lists();
	if ( isset( $themify_gfonts ) && is_array( $themify_gfonts ) ) {
		foreach ($themify_gfonts as $v) {
			if ( $v['family'] == $family ) {
				$variant = $v['variant'];
				break;
			}
		}
	}

	return $variant;
}

/**
 * Returns a list of Google Web Fonts
 * @return array
 * @since 1.5.6
 */
function themify_get_google_web_fonts_list() {
	static $google_fonts_list;
	if ( ! $google_fonts_list ) {
		$google_fonts_list = array(
			array( 'value' => '', 'name' => '' ),
			array(
				'value' => '',
				'name' => '--- ' . __( 'Google Fonts', 'themify' ) . ' ---'
			)
		);
		$fonts = themify_get_google_font_lists();
		foreach ( $fonts as $font ) {
			if ( ! empty( $font['family'] ) ) {
				$google_fonts_list[] = array(
					'value' => $font['family'],
					'name' => $font['family'],
					'variant' => $font['variant']
				);
			}
		}
		$google_fonts_list = apply_filters( 'themify_get_google_web_fonts_list', $google_fonts_list );
	}

	return $google_fonts_list;
}

/**
 * Returns a list of web safe fonts
 * @param bool $only_names Whether to return only the array keys or the values as well
 * @return mixed|void
 * @since 1.0.0
 */
function themify_get_web_safe_font_list( $only_names = false ) {
	$web_safe_font_names = array(
		"Arial, Helvetica, sans-serif",
		"Verdana, Geneva, sans-serif",
		"Georgia, 'Times New Roman', Times, serif",
		"'Times New Roman', Times, serif",
		"Tahoma, Geneva, sans-serif",
		"'Trebuchet MS', Arial, Helvetica, sans-serif",
		"Palatino, 'Palatino Linotype', 'Book Antiqua', serif",
		"'Lucida Sans Unicode', 'Lucida Grande', sans-serif"
	);

	if( ! $only_names ) {
		$web_safe_fonts = array(
			array( 'value' => 'default', 'name' => '', 'selected' => true ),
			array( 'value' => '', 'name' => '--- '.__( 'Web Safe Fonts', 'themify' ) . ' ---' )
		);

		foreach( $web_safe_font_names as $font ) {
			$web_safe_fonts[] = array(
				'value' => $font,
				'name' => $font
			);
		}
	} else {
		$web_safe_fonts = $web_safe_font_names;
	}

	return apply_filters( 'themify_get_web_safe_font_list', $web_safe_fonts );
}

/**
 * Enqueue Google fonts (if any) on the page
 *
 * @uses themify_google_fonts filter
 */
function themify_enqueue_google_fonts() {
	/* do not enqueue if google fonts are disabled */
	if( defined( 'THEMIFY_GOOGLE_FONTS' ) && THEMIFY_GOOGLE_FONTS != true ) {
		return;
	}

	$fonts = apply_filters( 'themify_google_fonts', array() );
	$fonts = array_filter( $fonts, 'themify_validate_font_enqueue' );
	if ( ! empty( $fonts ) ) {

		$path = ( is_ssl() ? 'https' : 'http' ) . '://fonts.googleapis.com/css?family=' . join( '|', $fonts );
		if( $subsets = themify_get_font_subsets() ) {
			$subsets = join( ',', $subsets );
			$subsets = str_replace( ' ', '', $subsets );
			$path .= '&subset=' . $subsets;
		}
		wp_enqueue_style( 'themify-google-fonts', $path );
	}
}
add_action( 'wp_enqueue_scripts', 'themify_enqueue_google_fonts', 30 );

/**
 * Filters the font list in themify_enqueue_google_fonts()
 * to ensure all font names are valid.
 *
 * @return bool
 */
function themify_validate_font_enqueue( $font ) {
	if ( $font[0] === ':' ) {
	    return false;
	}
	$font = preg_replace( '/[\s:|]/', '', $font );
	if ( empty( $font ) ) {
		return false;
	}

	return true;
}

endif;