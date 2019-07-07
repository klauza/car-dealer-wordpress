<?php
/**
 * Changes to WordPress behavior and interface applied by Themify framework
 *
 * @package Themify
 */

/**
 * Add Themify Settings link to admin bar
 * @since 1.1.2
 */
function themify_admin_bar() {
	global $wp_admin_bar;
	if ( !is_super_admin() || !is_admin_bar_showing() )
		return;
	$wp_admin_bar->add_menu( array(
		'id' => 'themify-settings',
		'parent' => 'appearance',
		'title' => __( 'Themify Settings', 'themify' ),
		'href' => admin_url( 'admin.php?page=themify' )
	));
}
add_action( 'wp_before_admin_bar_render', 'themify_admin_bar' );

/**
 * Generate CSS code from Styling panel
 */
function themify_get_css() {
	$data = themify_get_data();
	$output = '';
	/**
	 * Stores CSS rules
	 * @var string
	 */
	$module_styling = '';
	if( is_array( $data ) ) {
		$new_arr = array();
		foreach( $data as $name => $value ) {
			$array = explode( '-', $name );
			$path = '';
			foreach($array as $part){
				$path .= "[$part]";
			}
			$new_arr[ $path ] = $value;
		}
		$themify_config = themify_convert_brackets_string_to_arrays( $new_arr );

		if( isset( $themify_config['styling'] ) && is_array( $themify_config['styling'] ) ) {
			foreach( $themify_config['styling'] as $nav => $value ) {
				foreach( $value as $element => $val ) {
					$temp = '';
					foreach( $val as $attribute => $v ) {
						$attribute = str_replace('_', '-', $attribute);
						if( !empty( $v['value'] )) {
							switch( $attribute ) {
								case 'border':
									foreach( $v['value'] as $key => $val ) {
										if( '' == $val ) {
											if( strpos( $key, 'style' ) === false ) {
												if ( strpos( $key, 'color' ) === false ) {
													$v['value'][$key] = 0;
												} else {
													$v['value'][$key] = '000000';
												}
											} else {
												$v['value'][$key] = 'solid';
											}
										}
									}
									if( !empty( $v['value']['checkbox'] )) {
										$temp .= 'border: '.$v['value']['same'].'px '.$v['value']['same_style'].' #'.$v['value']['same_color'].";\n";
									} else {
										if( !empty( $v['value']['top'] ) && !empty( $v['value']['top_style'] ) && !empty( $v['value']['top_color'] )) {
											$temp .= 'border-top: '.$v['value']['top'].'px '.$v['value']['top_style'].' ' . themify_sanitize_hex_color( $v['value']['top_color'] ) .";\n";
										}
										if ( !empty( $v['value']['right'] ) && !empty( $v['value']['right_style'] ) && !empty( $v['value']['right_color'] )) {
											$temp .= 'border-right: '.$v['value']['right'].'px '.$v['value']['right_style'].' ' . themify_sanitize_hex_color( $v['value']['right_color'] ) .";\n";
										}
										if ( !empty( $v['value']['bottom'] ) && !empty( $v['value']['bottom_style'] ) && !empty( $v['value']['bottom_color'] )) {

											$temp .= 'border-bottom: '.$v['value']['bottom'].'px '.$v['value']['bottom_style'].' ' . themify_sanitize_hex_color( $v['value']['bottom_color'] ) . ";\n";
										}
										if ( !empty( $v['value']['left'] ) && !empty( $v['value']['left_style'] ) && !empty( $v['value']['left_color'] )) {
											$temp .= 'border-left: '.$v['value']['left'].'px '.$v['value']['left_style'].' ' . themify_sanitize_hex_color( $v['value']['left_color'] ) .";\n";
										}
									}
								break;
								case 'background-position':
									if ( !empty( $v['value']['x'] ) && !empty( $v['value']['y'] ) ) {
										foreach ( $v['value'] as $key => $val ) {
											if ( $val == '' ) {
												$v['value'][$key] = 0;
											}
										}
										$temp .= $attribute.': ';
										$temp .= $v['value']['x'].' '.$v['value']['y'].";\n";
									}
								break;
								case 'padding':
									if ( !empty( $v['value']['checkbox'] )) {
										$temp .= $attribute.': ';
										$temp .= $v['value']['same'].'px'.";\n";
									} else {
										if ( !empty( $v['value']['top'] )) {
											$temp .= 'padding-top: '.$v['value']['top']."px;\n";
										}
										if ( !empty( $v['value']['right'] )) {
											$temp .= 'padding-right: '.$v['value']['right']."px;\n";
										}
										if ( !empty( $v['value']['bottom'] )) {
											$temp .= 'padding-bottom: '.$v['value']['bottom']."px;\n";
										}
										if ( !empty( $v['value']['left'] )) {
											$temp .= 'padding-left: '.$v['value']['left']."px;\n";
										}
									}
								break;
								case 'margin':
									if ( !empty( $v['value']['checkbox'] )) {
										$temp .= $attribute.': ';
										$temp .= $v['value']['same'].'px'.";\n";
									} else {
										if ( !empty( $v['value']['top'] ) ) {
											$temp .= 'margin-top: '.$v['value']['top']."px;\n";
										}
										if ( !empty( $v['value']['right'] )) {
											$temp .= 'margin-right: '.$v['value']['right']."px;\n";
										}
										if ( !empty( $v['value']['bottom'] )  ) {
											$temp .= 'margin-bottom: '.$v['value']['bottom']."px;\n";
										}
										if ( !empty( $v['value']['left'] ) ) {
											$temp .= 'margin-left: '.$v['value']['left']."px;\n";
										}
									}
								break;
								case 'color':
									if ( !empty( $v['value']['value'] )&& $v['value']['value'] != ' ' ) {
										$temp .= $attribute.': ';
										$temp .= themify_sanitize_hex_color( $v['value']['value'] ) . ";\n";
									}
								break;
								case 'background-color':
									if ( !empty( $v['value']['transparent'] ) ) {
										$temp .= $attribute.": transparent;\n";
									} elseif ( !empty( $v['value']['value'] ) && $v['value']['value'] != ' ' ) {
										$temp .= $attribute.': ';
										$temp .= themify_sanitize_hex_color( $v['value']['value'] ) .";\n";
									}
								break;
								case 'background-image':
									if ( !empty( $v['value']['value'] ) && $v['value']['value'] != ' ' ) {
										$temp .= $attribute.': ';
										$temp .= 'url('.$v['value']['value'].')'.";\n";
									} elseif ( isset( $v['value']['none'] ) && 'on' === $v['value']['none'] ) {
										$temp .= $attribute.': ';
										$temp .= "none;\n";
									}
								break;
								case 'background-repeat':
									if ( !empty( $v['value']['value'] ) && $v['value']['value'] != ' ' ) {
										$temp .= $attribute.': ';
										$temp .= $v['value']['value'].";\n";
									}
								break;
								case 'font-family':
									if ( !empty( $v['value']['value'] ) &&  $v['value']['value'] != ' ' ) {
										$temp .= $attribute.': ';
                                                                                $temp .= '"' . $v['value']['value'] . '"' .";\n";
                                                                        }
								break;
								case 'line-height':
									if ( !empty( $v['value']['value'] ) && $v['value']['value'] != ' ' ) {
										$temp .= $attribute.': ';
										$temp .= $v['value']['value'].$v['value']['unit'].";\n";
									}
								break;
								case 'position':
									if ( !empty( $v['value']['value'] ) && $v['value']['value'] != ' ' ) {
										$temp .= $attribute.': ';
										$temp .= $v['value']['value'].";\n";
										if($v['value']['value'] === 'absolute' || $v['value']['value'] === 'fixed'){
											if($v['value']['x_value'] != '' && $v['value']['x_value'] != ' '){
												$temp .= $v['value']['x'].': '.$v['value']['x_value']."px;\n";
											}
											if($v['value']['y_value'] != '' && $v['value']['y_value'] != ' '){
												$temp .= $v['value']['y'].': '.$v['value']['y_value']."px;\n";
											}
										}
									}
								break;
								default:
									if ( !empty( $v['value']['value'] )&& $v['value']['value'] != ' ' ) {
										$temp .= $attribute.': ';
										$temp .= $v['value']['value'];
										if(isset($v['value']['unit'])){
											$temp .= $v['value']['unit'];
										}
										$temp .= ";\n";
									}
								break;
							}
						}
					}
					if($temp != '' && $temp != ' '){

						$style_selector = themify_get_styling_selector('id', $element, $nav, true);
						if ( $style_selector != '' ) {
							$module_styling .= $style_selector." {\n";
							$module_styling .= $temp;
							$module_styling .= "}\n\n";
						}
					}
				}
			}
		}
	} else {
		$output = '<style type="text/css">/* ' . __('No Values in the Database', 'themify') . ' */</style>';
	}
	$module_styling_before = "<!-- modules styling -->\n<style type='text/css'>\n";
	$module_styling_after = "</style>";
	if( '' != $module_styling ){
		$output .= $module_styling_before . $module_styling . $module_styling_after;
	}
	echo "\n\n".$output;
}

/**
 * Outputs the module styling and then the Custom CSS module content.
 * @since 1.7.4
 */
function themify_output_framework_styling() {
	// Module Styling
	themify_get_css();

	// Custom CSS
	if ( $custom_css = themify_get( 'setting-custom_css' ) ) {
		echo "\n\n<!-- custom css -->\n\n<style type='text/css'>$custom_css</style>";
	}
	add_filter( 'style_loader_tag', 'themify_add_style_property', 15, 4 );
}
add_action( 'wp_head', 'themify_output_framework_styling' );

/**
 * Add property stylesheet attribute for footer styles
 */ 
function themify_add_style_property($html,$handle,$href,$media){
	global $wp_styles;
	$wp_styles->add_data( $handle, 'property', 'stylesheet' );
	return $html;
}

/**
 * Add different CSS classes to body tag.
 * Outputs:
 * 		skin name
 * 		layout
 * @param Array
 * @return Array
 * @since 1.2.2
 */
function themify_body_classes( $classes ) {
	global $themify;

	$template = get_template();
	$classes[] = 'themify-fw-' . str_replace( '.', '-', THEMIFY_VERSION );
	$classes[] = $template . '-' . str_replace( '.', '-', wp_get_theme( $template )->version );

	// Add skin name
	if( $skin = themify_is_theme_skin() ) {
		$skin_dir = explode( '/', $skin );
		$classes[] = 'skin-' . $skin_dir[sizeof( $skin_dir ) - 2];
	} else {
		$classes[] = 'skin-default';
	}

	// Browser classes
	global $is_gecko, $is_opera, $is_iphone, $is_IE, $is_winIE, $is_macIE;

	$is_android = $is_webkit = $is_edge = $is_ie10 = $is_ie9 = $is_ie8 = false;

	if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
		if ( stripos( $_SERVER['HTTP_USER_AGENT'], 'android' ) ) {
			$is_android = true;
		}
		if ( stripos( $_SERVER['HTTP_USER_AGENT'], 'webkit' ) ) {
			$is_webkit = true;
		}
		if( stripos( $_SERVER['HTTP_USER_AGENT'], 'Edge' ) ) {
			$is_edge = true;
		}
		if ( stripos( $_SERVER['HTTP_USER_AGENT'], 'MSIE 10' ) ) {
			$is_ie10 = true;
		}
		if ( stripos( $_SERVER['HTTP_USER_AGENT'], 'MSIE 9' ) ) {
			$is_ie9 = true;
		}
		if ( stripos( $_SERVER['HTTP_USER_AGENT'], 'MSIE 8' ) ) {
			$is_ie8 = true;
		}
	}

	$browsers = array(
		'gecko'   => $is_gecko,
		'opera'   => $is_opera,
		'iphone'  => $is_iphone,
		'android' => $is_android,
		'webkit'  => $is_webkit,
		'edge'	  => $is_edge,
		'ie' 	  => $is_IE,
		'iewin'   => $is_winIE,
		'iemac'   => $is_macIE,
		'ie10' 	  => $is_ie10,
		'ie9' 	  => $is_ie9,
		'ie8' 	  => $is_ie8
	);

	$is_not_ie = true;

	foreach( $browsers as $browser => $state ) {
		if ( $state ) {
			$classes[] = $browser;
			if ( stripos( $browser, 'ie' ) !== false ) {
				$is_not_ie = false;
			}
		}
	}
	if ( $is_not_ie ) {
		$classes[] = 'not-ie';
	}

	// Add default layout and post layout
	$post_layout = themify_get('setting-default_post_layout');

	// Set content width
	if ( is_search() ) {
		$classes[] = 'default_width';
	} elseif ( is_singular() || themify_is_shop() ) {
		$classes[] = themify_check( 'content_width' ) ? themify_get( 'content_width' ) : 'default_width';
	}

	if( themify_is_query_page() ) {
		$classes[] = 'query-page';
		$classes[] = isset($themify->query_post_type) ? 'query-'.$themify->query_post_type: 'query-post';
	}

	// It's a singular view (post, page, portfolio, any custom post type)
	if ( is_singular() ) {
		// Post requires password
		if ( post_password_required( get_the_ID() ) ) {
			$classes[] = 'entry-password-required';
		}
	}

	// If still empty, set default
	if( apply_filters('themify_default_layout_condition', '' == $themify->layout) ){
		$themify->layout = apply_filters('themify_default_layout', 'sidebar1');
	}
	$classes[] = $themify->layout;

	// non-homepage pages
	if( ! ( is_home() || is_front_page() ) ) {
		$classes[] = 'no-home';
	}

	// if the page is being displayed in lightbox
	if( isset( $_GET['iframe'] ) && $_GET['iframe'] === 'true' ) {
		$classes[] = 'lightboxed';
	}

	// Set post layout for blog, archive or a query category page
	$post_query_category = isset($themify->query_category)? $themify->query_category : themify_get('query_category');
	if( is_home() || is_archive() || '' != $post_query_category || is_search() ){
		$post_layout = $themify->post_layout;
		if(apply_filters('themify_default_post_layout_condition', '' == $post_layout)){
			$post_layout = apply_filters('themify_default_post_layout', 'list-post');
		}
		$classes[] = $post_layout;
	}

	$classes[] = themify_is_touch() ? 'touch' : 'no-touch';
		
	if(themify_get('setting-lightbox_content_images')){
		$classes[] = 'themify_lightboxed_images';
	}
		
	return apply_filters('themify_body_classes', $classes);
}
add_filter( 'body_class', 'themify_body_classes' );

/**
 * Adds classes to .post based on elements enabled for the currenty entry.
 *
 * @since 2.0.4
 *
 * @param $classes
 *
 * @return array
 */
function themify_post_class( $classes ) {
	global $themify;

	$classes[] = ( ! isset($themify->hide_title) || ( $themify->hide_title !== 'yes' ) ) ? 'has-post-title' : 'no-post-title';
	$classes[] = ( ! isset( $themify->hide_date ) || (  $themify->hide_date !== 'yes' ) ) ? 'has-post-date' : 'no-post-date';
	$classes[] = ( ! isset( $themify->hide_meta_category ) || (  $themify->hide_meta_category !== 'yes' ) ) ? 'has-post-category' : 'no-post-category';
	$classes[] = ( ! isset( $themify->hide_meta_tag ) || (  $themify->hide_meta_tag !== 'yes' ) ) ? 'has-post-tag' : 'no-post-tag';
	$classes[] = ( ! isset( $themify->hide_meta_comment ) || (  $themify->hide_meta_comment !== 'yes' ) ) ? 'has-post-comment' : 'no-post-comment';
	$classes[] = ( ! isset( $themify->hide_meta_author ) || (  $themify->hide_meta_author !== 'yes' ) ) ? 'has-post-author' : 'no-post-author';
	$classes[] = ( is_admin() && get_post_type() === 'product' ) ? 'product' : '';

	return apply_filters( 'themify_post_classes', $classes );
}
add_filter( 'post_class', 'themify_post_class' );

if ( ! function_exists( 'themify_disable_responsive_design' ) ) :
/**
 * Disables the responsive design by removing media-queries.css file and changing viewport tag
 *
 * @since 2.1.5
 */
function themify_disable_responsive_design() {
	// Remove media-queries.css
	add_action( 'wp_enqueue_scripts', create_function( '', "wp_deregister_style( 'themify-media-queries' );" ), 20 );

	// Remove JS for IE
	remove_action( 'wp_head', 'themify_ie_enhancements' );

	// Remove meta viewport tag
	remove_action( 'wp_head', 'themify_viewport_tag' );
}
endif;
if ( 'on' === themify_get( 'setting-disable_responsive_design' ) ) {
	add_action( 'init', 'themify_disable_responsive_design' );
}

if ( ! function_exists( 'themify_wp_video_shortcode' ) ) :
/**
 * Removes height in video to replicate this fix https://github.com/markjaquith/WordPress/commit/3d8e31fb82cc1485176c89d27b736bcd9d2444ba#diff-297bf46a572d5f80513d3fed476cd2a2R1862
 *
 * @param $out
 * @param $atts
 *
 * @return mixed
 */
function themify_wp_video_shortcode( $out, $atts ) {
	$width_rule = '';
	if ( ! empty( $atts['width'] ) ) {
		$width_rule = sprintf( 'width: %dpx; ', $atts['width'] );
	}
	return preg_replace( '/<div style="(.*?)" class="wp-video">/i', '<div style="' . esc_attr( $width_rule ) . '" class="wp-video">', $out );
}
endif;
add_filter( 'wp_video_shortcode', 'themify_wp_video_shortcode', 10, 2 );

add_filter( 'embed_oembed_html', 'themify_parse_video_embed_vars', 10, 2 );

/**
 * Add extra protocols like skype: to list of allowed protocols.
 *
 * @since 2.1.8
 *
 * @param array $protocols List of protocols allowed by default by WordPress.
 *
 * @return array $protocols Updated list including extra protocols added.
 */
function themify_allow_extra_protocols( $protocols ){
	$protocols[] = 'skype';
	$protocols[] = 'sms';
	$protocols[] = 'comgooglemaps';
	$protocols[] = 'comgooglemapsurl';
	$protocols[] = 'comgooglemaps-x-callback';
	$protocols[] = 'viber';
	$protocols[] = 'facetime';
	$protocols[] = 'facetime-audio';
	$protocols[] = 'tg';
	$protocols[] = 'whatsapp';
	$protocols[] = 'ymsgr';
	$protocols[] = 'gtalk';

	return $protocols;
}
add_filter( 'kses_allowed_protocols' , 'themify_allow_extra_protocols' );

if( ! function_exists( 'themify_upload_mime_types' ) ) :
/**
 * Adds .svg and .svgz to list of mime file types supported by WordPress
 * @param array $existing_mime_types WordPress supported mime types
 * @return array Array extended with svg/svgz support
 * @since 1.3.9
 */
function themify_upload_mime_types( $existing_mime_types = array() ) {
	$existing_mime_types['svg'] = 'image/svg+xml';
	$existing_mime_types['svgz'] = 'image/svg+xml';
	$existing_mime_types['zip'] = 'application/zip';
	$existing_mime_types['json'] = 'application/json';
	return $existing_mime_types;
}
endif;
add_filter( 'upload_mimes', 'themify_upload_mime_types' );

/**
 * Display an additional column in categories list
 * @since 1.1.8
 */
function themify_custom_category_header( $cat_columns ) {
    $cat_columns['cat_id'] = __( 'ID', 'themify' );
    return $cat_columns;
}
add_filter( 'manage_edit-category_columns', 'themify_custom_category_header', 10, 2 );

/**
 * Display ID in additional column in categories list
 * @since 1.1.8
 */
function themify_custom_category( $null, $column, $termid ) {
	return $termid;
}
add_filter( 'manage_category_custom_column', 'themify_custom_category', 10, 3 );

/**
 * Set a default title for the front page
 *
 * @return string
 * @since 1.7.6
 */
function themify_filter_wp_title( $title, $sep ) {
	global $aioseop_options;

	if( empty( $title ) && ( is_home() || is_front_page() ) ) {
		if( class_exists( 'All_in_One_SEO_Pack' ) && '' != $aioseop_options['aiosp_home_title'] ) {
			return $aioseop_options['aiosp_home_title'];
		}
		return get_bloginfo( 'name' );
	}

	return str_replace( $sep , '', $title );
}
add_filter( 'wp_title', 'themify_filter_wp_title', 10, 2 );

/**
 * Filters the title. Removes the default separator.
 *
 * @since 2.0.2
 *
 * @param string $title Page title to be output.
 * @param string $sep Separator to search and replace.
 *
 * @return mixed
 */
function themify_wp_title( $title, $sep ) {
	return str_replace( $sep, '', $title );
}
add_filter( 'wp_title', 'themify_wp_title', 10, 2 );

/**
 * Hijacks themes passed for upgrade checking and remove those from Themify
 * @param Bool
 * @param Array $r List of themes
 * @param String $url URL of upgrade check
 * @return Array
 * @since 1.1.8
 */
function themify_hide_themes( $r, $url ){
	if ( false !== stripos( $url, 'api.wordpress.org/themes/update-check' ) ) {
		$themes = json_decode( $r['body']['themes'] );
		$themes_list = themify_get_theme_names();
		if ( is_array( $themes_list ) ) {
			foreach( $themes_list as $theme_name ){
				unset( $themes->themes->{$theme_name} );
			}
			$r['body']['themes'] = json_encode( $themes );
		}
	}
	return $r;
}
if( is_admin() )
	add_filter( 'http_request_args', 'themify_hide_themes', 5, 2);

/**
 * Filters wp_nav_menu_args to set custom menu on pages that have one.
 * Also adds menu name as a classname to menus when "container" is missing.
 *
 * @since 2.8.9
 * @return array
 */
function themify_wp_nav_menu_args_filter( $args ) {

	if ( isset( $args['menu_id'] ) && $args['menu_id'] === 'main-nav' ) {
		if ( is_singular() || themify_is_shop() ) {
			// See if the page has a menu assigned
			$custom_menu = themify_get( 'custom_menu' );
			if ( ! empty( $custom_menu ) ) {
				$args['menu'] = $custom_menu;
			}
		}
	}

	if ( ! $args['container'] ) {
		if( ! empty( $args['menu'] ) ) {
			$menu = wp_get_nav_menu_object( $args['menu'] );
		} elseif ( $args['theme_location'] && ( $locations = get_nav_menu_locations() ) && isset( $locations[ $args['theme_location'] ] ) ) {
			$menu = wp_get_nav_menu_object( $locations[ $args['theme_location'] ] );
		}

		if ( isset( $menu ) && ! is_wp_error( $menu ) && $menu !== false ) {
			$args['menu_class'] .= ' menu-name-' . $menu->slug;
		}
	}

	return $args;
}
add_filter( 'wp_nav_menu_args', 'themify_wp_nav_menu_args_filter', 100 );

function themify_favicon_action( $data = array() ) {
	$data = themify_get_data();
	if ( !empty( $data['setting-favicon'] )) {
		$favurl = themify_https_esc($data['setting-favicon']);
		echo "\n\n".'<link href="' . esc_attr( $favurl ) . '" rel="shortcut icon" /> ';
	}
}
add_action( 'wp_head', 'themify_favicon_action' );

/**
 * Header HTML Module - Action
 * @param array $data
 */
function themify_header_html_action( $data = array() ) {
	if( ! Themify_Builder_Model::is_front_builder_activate() ) {
		echo "\n\n" . themify_get( 'setting-header_html' );
	}
}
add_action( 'wp_head','themify_header_html_action' );

/**
 * Footer HTML Module - Action
 * @param array $data
 */
function themify_footer_html_action( $data = array() ) {
	if( ! Themify_Builder_Model::is_front_builder_activate() ) {
		echo "\n\n" . themify_get( 'setting-footer_html' );
	}
}
add_action( 'wp_footer','themify_footer_html_action', 9999 );

if ( ! function_exists( 'themify_search_excludes_cpt' ) ) :
/**
 * Exclude Custom Post Types from Search - Filter
 *
 * @param $query
 * @return mixed
 */
function themify_search_excludes_cpt( $query ) {
	if ($query->is_search  && ! is_admin() && $query->is_main_query()) {

		/////////////////////////////////
		// Set category search settings
		/////////////////////////////////
		$cat_search = themify_get( 'setting-search_settings' );
		if ( isset( $cat_search ) && $cat_search != '' ) {
			$query->set( 'cat', $cat_search );
		}

		//////////////////////////////////
		// Exclude pages & post types ////
		//////////////////////////////////

		// If it's not a product search, proceed: retrieve the post types.
		$types = get_post_types( array( 'exclude_from_search' => false ) );

		// Exclude posts /////////////////
		$exclude_posts = themify_get( 'setting-search_exclude_post' );
		if ( !empty( $exclude_posts ) ) {
			unset( $types['post'] );
		}

		// Exclude pages /////////////////
		$exclude_pages = themify_get( 'setting-search_settings_exclude' );
		if ( !empty( $exclude_pages ) ) {
			unset( $types['page'] );
		}

		// Exclude custom post types /////
		$exclude_types = apply_filters( 'themify_types_excluded_in_search', get_post_types( array(
			'_builtin' => false,
			'public' => true,
			'exclude_from_search' => false
		)));

		foreach( array_keys( $exclude_types ) as $type ) {
			$exclude_type = themify_get( 'setting-search_exclude_' . $type );
			if ( !empty( $exclude_type ) ) {
				unset( $types[$type] );
			}
		}

		// Exclude Layout and Layout Part custom post types /////
		unset( $types['section'],$types['tbuilder_layout'],$types['tbuilder_layout_part'] );

		// Search for products
		if ( isset( $query->query_vars['post_type'] ) ) {
			if ( 'post' === $query->query_vars['post_type'] ) {
				unset( $query->query_vars['post_type'],$types['page'] );
				$types[] = 'post';
				if ( empty( $exclude_pages )) {
					$types[] = 'page';
				}
			} elseif( $query->query_vars['post_type'] === 'product' ) {
				// Check if only product post
				$types = array( $query->query_vars['post_type'] );
			}
		}

		// product post_type is excluded when the search is for post post_type
		if ( isset( $_GET['search-option'] ) && $_GET['search-option'] === 'post' && isset( $types['product'] )) {
			unset( $types['product'] );
		}
		
		// Set final query parameters ////
		$query->set('post_type', $types);
	}
	return $query;
}
endif;
add_filter( 'pre_get_posts', 'themify_search_excludes_cpt', 999 );

function themify_feed_settings_action($query){
	
	if( $query->is_feed ) {
            $data = themify_get_data();
            if( isset( $data['setting-feed_settings'] ) ) {
                $query->set( 'cat', $data['setting-feed_settings'] );	
            }
	}

	return $query;
}
add_filter('pre_get_posts','themify_feed_settings_action');

$themify_data = themify_get_data();
if( empty($themify_data['setting-exclude_img_rss'])) {
	add_filter('the_content', 'themify_custom_fields_for_feeds');

/* Firefox doesn't render images to feed when select full text from admin > Settings > Reading But IE does automatically for full text.
 * So this code below will be used by firefox only to render/fetch images in feed. If we use for all then it will show images 2 times. */

	$useragent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
	if(preg_match('|Firefox/([0-9\.]+)|',$useragent)) {
		add_filter('the_excerpt_rss', 'themify_custom_fields_for_feeds');
		add_filter('the_content_feed', 'themify_custom_fields_for_feeds');
	}

	function themify_custom_fields_for_feeds( $content ) {

		global  $themify_check;
		if(!is_feed() || $themify_check == true){
			return $content;
		}

		if(themify_check('post_image')) {
			$content = "<p><img src='" . esc_url( themify_get( 'post_image' ) ) . "'></p>" . $content;
		}
		$themify_check = false;
		return $content;
	}
}

// Custom Post Types in RSS
function themify_feed_custom_posts( $qv ) {
	$feed_custom_posts = themify_check( 'setting-feed_custom_post' )
		? explode( ',', trim( themify_get( 'setting-feed_custom_post' ) ) ) : '';

	if( ! empty( $feed_custom_posts ) && isset( $qv['feed'] ) && ! isset( $qv['post_type'] ) ) {
		if( in_array( 'all', $feed_custom_posts,true ) ) {
			$post_types = get_post_types( array('public' => true, 'publicly_queryable' => 'true' ) );
			$qv['post_type'] = array_diff( $post_types, array('attachment', 'tbuilder_layout', 'tbuilder_layout_part', 'section') );
		} else {
			$qv['post_type'] = $feed_custom_posts;
		}
	}

	return $qv;
}
add_filter( 'request', 'themify_feed_custom_posts' );

/**
 * Show custom 404 page (function)
 */
function themify_404_page_id() {
	$pageid = themify_get( 'setting-page_404' );

	if( ! empty( $pageid ) ) {
		if( defined( 'ICL_SITEPRESS_VERSION' ) ) {
			$pageid = apply_filters( 'wpml_object_id', $pageid, 'page', true );
		} else if( defined( 'POLYLANG_VERSION' ) ) {
			$translatedpageid = pll_get_post( $pageid );
			if ( !empty( $translatedpageid ) && 'publish' === get_post_status( $translatedpageid ) ) {
				$pageid = $translatedpageid;
			}
		}
	}

	return $pageid;
}

function themify_set_404_wp_query( $pageid ) {
	global $wp_query;

	$wp_query = null;
	$wp_query = new WP_Query();
	$wp_query->query( 'page_id=' . $pageid );
	$wp_query->the_post();

	return $wp_query;
}

function themify_404_init() {
	if(! is_admin() && ! is_customize_preview() && themify_404_page_id() != 0) {
		add_filter( 'the_posts', 'themify_404_display_static_page_result', 999, 2 );
		add_filter( '404_template', 'themify_404_static_template', 999 );
		add_filter( 'redirect_canonical' , '__return_false' );
	}
}
add_action( 'init', 'themify_404_init' );

function themify_404_display_static_page_result($posts, $query ) {
	if($query->is_main_query()){
		remove_filter( 'the_posts', 'themify_404_display_static_page_result', 999, 2);
		$pageid = themify_404_page_id();
		
		if( $pageid != 0 ) {
			if ( empty( $posts ) &&  !$query->is_robots() && !$query->is_home() && !$query->is_feed() && !$query->is_search() && !$query->is_post_type_archive() ) {
				
				$wp_query = themify_set_404_wp_query( $pageid );
				$posts = $wp_query->posts;
				$wp_query->rewind_posts();

				add_action( 'wp', 'themify_404_header' );
				add_filter( 'body_class', 'themify_404_body_class' );
				
			} elseif (isset($posts[0]) && 'page' === $posts[0]->post_type && 1 === count( $posts )) {
				
				$curpageid = $posts[0]->ID;
				
				if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
					// WPML page id
					global $sitepress;
					$curpageid = apply_filters( 'wpml_object_id', $curpageid, 'page', $sitepress->get_default_language() );
					$pageid = apply_filters( 'wpml_object_id', $pageid, 'page', $sitepress->get_default_language() );
				}
				
				if ( $pageid == $curpageid ) {
					add_action( 'wp', 'themify_404_header' );
					add_filter( 'body_class', 'themify_404_body_class' );
				}
			}
		}
	}
	return $posts;
}

function themify_404_static_template( $template ) {
	remove_filter( '404_template', 'themify_404_static_template', 999 );

	$pageid = themify_404_page_id();

	themify_set_404_wp_query( $pageid );
	$template = themify_404_template( $template );
	rewind_posts();
	add_filter( 'body_class', 'themify_404_body_class' );
	add_action( 'wp_head', 'themify_404_force_query' );
	
	return $template;
}

/**
 * Send a 404 HTTP header
 */
function themify_404_header() {
	remove_action( 'wp', 'themify_404_header' );
	status_header( 404 );
	nocache_headers();

	global $themify;
	$themify->is_custom_404 = true;
}

/**
 * Conditional tag to check if a custom 404 page is enabled
 *
 * @return bool
 */
function themify_is_custom_404() {
	global $themify;
	return !empty( $themify->is_custom_404 );
}

/**
 * Adds the error404 class to the body classes
 */
function themify_404_body_class( $classes ) {
	remove_action( 'body_class', 'themify_404_body_class' );
	
	if ( ! in_array( 'error404', $classes,true ) ) {
		$classes[] = 'error404';
	}

	return $classes;
}

/**
 * Set 404 page template
 */
function themify_404_template( $template ) {
	global $themify;

	$template = get_page_template();
	$pageid = themify_404_page_id();

	// PAGE LAYOUT (global $themify)
	$layout = ( themify_get( 'page_layout' ) !== 'default' && themify_check( 'page_layout' ) )
		? themify_get( 'page_layout' )
		: themify_get( 'setting-default_page_layout' );
	
	if ( $layout != '' ) {
		$themify->layout = $layout;
	}

	// PAGE TITLE VISIBILITY (global $themify)
	$hide_page_title = get_post_meta( $pageid, 'hide_page_title', true );

	if ( ! empty( $hide_page_title ) && 'default' !== $hide_page_title ) {
		$themify->page_title = $hide_page_title;
	} else {
		$themify->page_title = themify_check( 'setting-hide_page_title' ) ? themify_get( 'setting-hide_page_title' ) : 'no';
	}
	
	if ( 'yes' === $themify->page_title ) {
		add_filter( 'woocommerce_show_page_title', '__return_false' );
	}

	return $template;
}

function themify_404_force_query() {
	remove_action( 'wp_head', 'themify_404_force_query' );
	global $wp_query;

	$pageid = themify_404_page_id();

	if( empty( $wp_query->post->ID ) || $wp_query->post->ID != $pageid ) {
		themify_set_404_wp_query( $pageid );
		rewind_posts();
	}
}

/**
 * Handle Builder's JavaScript fullwidth rows, forces fullwidth rows if sidebar is disabled
 *
 * @return bool
 */
function themify_theme_fullwidth_layout_support( $support ) {
	global $themify;
	/* if Content Width option is set to Fullwidth, do not use JavaScript, using sidebar-none layout, force fullwidth rows using JavaScript */
	return themify_get( 'content_width' ) === 'full_width'?true:($themify->layout === 'sidebar-none'?false:true);
}
add_filter( 'themify_builder_fullwidth_layout_support', 'themify_theme_fullwidth_layout_support' );

/**
 * Load current skin's functions file if it exists
 *
 * @since 1.4.9
 */
function themify_theme_load_skin_functions() {
	$current_skin = themify_get( 'skin' );
	if( $current_skin ) {
		$parsed_skin = parse_url( $current_skin, PHP_URL_PATH );
		$basedir_skin = basename( dirname( $parsed_skin ) );
		if( is_file( THEME_DIR . '/skins/' . $basedir_skin . '/functions.php' ) ) {
			include THEME_DIR . '/skins/' . $basedir_skin . '/functions.php';
		}
	}
}
add_action( 'after_setup_theme', 'themify_theme_load_skin_functions', 1 );

/**
 * Adds the global variable in JS that controls the mobile menu breakpoint
 *
 * This is added separately from script in themify_mobile_menu_script() to ensure the
 * variable is always present in the page.
 */
function themify_set_global_menu_trigger_point() { ?>
	<script type="text/javascript">
		var tf_mobile_menu_trigger_point = <?php echo themify_get( 'setting-mobile_menu_trigger_point', 1200,true ); ?>;
	</script><?php
}
add_action( 'wp_head', 'themify_set_global_menu_trigger_point' );

/**
 * JavaScript code that toggles "mobile_menu_active" body class, based on browser window size
 *
 * @since 2.9.2
 */
function themify_mobile_menu_script() {
?>
<script type="text/javascript">
	function themifyMobileMenuTrigger(e) {
		if( document.body.clientWidth > 0 && document.body.clientWidth <= tf_mobile_menu_trigger_point ) {
			document.body.classList.add( 'mobile_menu_active' );
		} else {
			document.body.classList.remove( 'mobile_menu_active' );
		}
	}
	themifyMobileMenuTrigger();
	document.addEventListener( 'DOMContentLoaded', function () {
		jQuery( window ).on('tfsmartresize.tf_mobile_menu', themifyMobileMenuTrigger );
	}, false );
</script>
<?php
}
add_action( 'themify_body_start', 'themify_mobile_menu_script' );

/**
 * Change order and orderby parameters in the index loop, per options in Themify > Settings > Default Layouts
 *
 * @since 3.1.2
 */
function themify_archive_post_order( $query ) {
	if ( ( is_home() || is_archive() ) && $query->is_main_query() && ! is_admin() ) {
		if ( ! themify_is_woocommerce_active() || ( themify_is_woocommerce_active() && ! is_shop() && ! is_product_category() ) ) {
			global $themify;
			$query->set( 'order', $themify->order );
			$query->set( 'orderby', $themify->orderby );

			if( ! empty( $themify->order_meta_key ) ) {
				$query->set( 'meta_key', $themify->order_meta_key );
			}
		}
	}

	return $query;
}
add_filter( 'pre_get_posts', 'themify_archive_post_order' );


/**
 * Enable shortcodes in footer text areas
 */
add_filter( 'themify_the_footer_text_left', 'do_shortcode' );
add_filter( 'themify_the_footer_text_right', 'do_shortcode' );

/**
 * Enable shortcode in excerpt
 */
add_filter('the_excerpt', 'do_shortcode');	
add_filter('the_excerpt', 'shortcode_unautop');

function themify_filter_widget_text( $text, $instance = array( ) ) {
	global $wp_widget_factory;

	/* check for WP 4.8.1+ widget */
        /*
	 * if $instance['filter'] is set to "content", this is a WP 4.8 widget,
	 * leave it as is, since it's processed in the widget_text_content filter
	 */
	if( (isset( $instance['filter'] ) && 'content' === $instance['filter'])  || (isset( $wp_widget_factory->widgets['WP_Widget_Text'] ) && method_exists( $wp_widget_factory->widgets['WP_Widget_Text'], 'is_legacy_instance' ) && ! $wp_widget_factory->widgets['WP_Widget_Text']->is_legacy_instance( $instance ) )) {
		return $text;
	}
	$text = do_shortcode( $text );
	return shortcode_unautop( $text );
}
add_filter( 'widget_text', 'themify_filter_widget_text', 10, 2 );
/**
 * Enable shortcodes in Text widget for Wp 4.8+
 */
add_filter( 'widget_text_content', 'do_shortcode', 12 );

/**
 * Set the Twitter API keys, required for the [themify_twitter] shortcode
 *
 * @return array
 */
function themify_set_twitter_credentials() {
	$data = themify_get_data();
	$prefix = 'setting-twitter_settings_';

	return array(
		'consumer_key' => isset( $data[$prefix.'consumer_key'] )? $data[$prefix.'consumer_key'] : '',
		'consumer_secret' => isset( $data[$prefix.'consumer_secret'] )? $data[$prefix.'consumer_secret'] : '',
	);
}
add_filter( 'themify_twitter_credentials', 'themify_set_twitter_credentials' );

/**
 * Registers support for various WordPress features
 *
 * @since 3.2.1
 */
function themify_setup_wp_features() {

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );
}
add_filter( 'after_setup_theme', 'themify_setup_wp_features' );

/**
 * load all themify,plugins and theme js with attribute defer(without blocking page render)
 *
 * @since 3.2.3
 */
function themify_defer_js($tag,$handle,$src){
	$exclude_handles = apply_filters( 'themify_defer_js_exclude', array() );
    if(!Themify_Builder_Model::is_front_builder_activate() && TFCache::is_themify_file($src,$handle) && !in_array( $handle, $exclude_handles ) ){
        $tag = str_replace(' src', ' defer="defer" src', $tag);
    }
    return $tag;
}
add_filter('script_loader_tag', 'themify_defer_js', 11, 3);

/**
 * Adds Post Options in Themify Custom Panel to custom post types
 * that do not have any options set for it.
 *
 * @return array
 */
function themify_setup_cpt_post_options( $metaboxes ){
	global $typenow;

	/* list of post types that already have defined options */
	$exclude = false;
	foreach( $metaboxes as $metabox ) {
		if( $metabox[ 'id' ] === $typenow . '-options' ) {
			$exclude = true;
			break;
		}

		if( ! empty( $metabox['options'] ) ) {
			foreach( $metabox['options'] as $option ) {
				if( in_array( $typenow . '_layout', $option, true ) ) {
					$exclude = true;
					break 2;
				}
			}
		}
	}

	if( $exclude ) {
		return $metaboxes;
	}

	global $typenow;
	if (isset($typenow) && $typenow) {
		$name = 'custom_post_'. $typenow .'_single';
	} else {
		$name = 'page_layout';
	}

	$post_options =  array(
			array(
				'name' => $name,
				'title' => __('Sidebar Option', 'themify'),
				'description' => '',
				'type' => 'layout',
				'show_title' => true,
				'meta' => apply_filters('themify_post_type_theme_sidebars' , array(
							array('value' => 'default', 'img' => 'images/layout-icons/default.png', 'selected' => true, 'title' => __('Default', 'themify')),
							array('value' => 'sidebar1', 'img' => 'images/layout-icons/sidebar1.png', 'title' => __('Sidebar Right', 'themify')),
							array('value' => 'sidebar1 sidebar-left', 'img' => 'images/layout-icons/sidebar1-left.png', 'title' => __('Sidebar Left', 'themify')),
							array('value' => 'sidebar-none', 'img' => 'images/layout-icons/sidebar-none.png', 'title' => __('No Sidebar ', 'themify'))
						)
					),
				'default' => 'default'
			),
			array(
				'name'=> 'content_width',
				'title' => __('Content Width', 'themify'),
				'description' => '',
				'type' => 'layout',
				'show_title' => true,
				'meta' => array(
					array(
						'value' => 'default_width',
						'img' => 'themify/img/default.png',
						'selected' => true,
						'title' => __( 'Default', 'themify' )
					),
					array(
						'value' => 'full_width',
						'img' => 'themify/img/fullwidth.png',
						'title' => __( 'Fullwidth', 'themify' )
					)
				),
				'default' => 'default_width'
			),
		) ;

	$post_options = apply_filters( 'themify_post_type_default_options', $post_options);

	return array_merge( array(
		array(
			'name' => __( 'Post Options', 'themify' ),
			'id' => $typenow . '-options',
			'options' => $post_options,
			'pages' => $typenow
		),
	), $metaboxes );
}
add_filter( 'themify_metabox/fields/themify-meta-boxes', 'themify_setup_cpt_post_options', 98 );

/**
 * Set proper sidebar layout for post types' single post view
 *
 * @uses global $themify
 */
function themify_cpt_set_post_options() {
	if( is_single() ) {
		$exclude = apply_filters('themify_exclude_CPT_for_sidebar', array('post', 'attachment', 'tbuilder_layout', 'tbuilder_layout_part', 'section') );
		global $themify;

		if ( ! in_array( get_post_type(), $exclude ) ) {
			$cpt_sidebar = 'custom_post_'.get_post_type().'_single';
			if ( themify_check( $cpt_sidebar ) && themify_get( $cpt_sidebar ) != 'default' ) {
				$themify->layout = themify_get( $cpt_sidebar );
			} elseif ( themify_check( 'setting-'.$cpt_sidebar ) ) {
				$themify->layout = themify_get( 'setting-'.$cpt_sidebar );
			} else {
				$themify->layout = themify_get( 'page_layout' );
			}
		}
	}
}
add_action( 'template_redirect', 'themify_cpt_set_post_options', 100 );

/**
 * Set the base image size that img.php will resize thumbnails from
 *
 * @return string
 */
function themify_image_script_source_size( $size ) {
	return themify_get( 'setting-img_php_base_size', 'large' );
}
add_filter( 'themify_image_script_source_size', 'themify_image_script_source_size', 1 );

/**
 * Set default 'large' image size on attachment page
 */
function themify_prepend_attachment() {
	return '<p>' . wp_get_attachment_link( 0, 'large', false ) . '</p>';
}
add_filter( 'prepend_attachment', 'themify_prepend_attachment' );
