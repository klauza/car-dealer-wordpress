<?php
/**
 * Post Meta Box Options
 * @param array $args
 * @return array
 * @since 1.0.0
 */
function themify_theme_product_meta_box( $args = array() ) {
	extract( $args );
	return array(
		// Layout
		array(
			'name' 		=> 'layout',
			'title' 		=> __('Sidebar Option', 'themify'),
			'description' => '',
			'type' 		=> 'layout',
			'show_title' => true,
			'meta'		=> array(
				array('value' => 'default', 'img' => 'images/layout-icons/default.png', 'selected' => true, 'title' => __('Default', 'themify')),
				array('value' => 'sidebar1', 'img' => 'images/layout-icons/sidebar1.png', 'title' => __('Sidebar Right', 'themify')),
				array('value' => 'sidebar1 sidebar-left', 'img' => 'images/layout-icons/sidebar1-left.png', 'title' => __('Sidebar Left', 'themify')),
				array('value' => 'sidebar-none', 'img' => 'images/layout-icons/sidebar-none.png', 'title' => __('No Sidebar ', 'themify'))
			),
			'default' => 'default',
		),
		// Content Width
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
			)
		),
		// Product Image Layout
		array(
			'name' 			=> 'image_layout',
			'title'			=> __('Product Image Layout', 'themify'),
			'description'	=> '',
			'type'			=> 'layout',
			'show_title' 	=> true,
			'meta'			=>  array(
				array('value' => '', 'img' => 'images/layout-icons/default.png', 'title' => __('Default', 'themify'), 'selected' => true),
				array('value' => 'img-left', 'img' => 'images/layout-icons/image-left.png', 'title' => __('Product Image Left', 'themify')),
				array('value' => 'img-center', 'img' => 'images/layout-icons/image-center.png', 'title' => __('Product Image Center', 'themify')),
				array('value' => 'img-right', 'img' => 'images/layout-icons/image-right.png', 'title' => __('Product Image Right', 'themify'))
			)
		),
		// Multi field: Image Dimension
		themify_image_dimensions_field(array('title'=>__('Product Image Dimension','themify')))
	);
}

/**************************************************************************************************
 * Themify Theme Settings Module
 **************************************************************************************************/

/**
 * Creates module for general shop layout and settings
 * @param array
 * @return string
 * @since 1.0.0
 */
function themify_shop_layout($data=array()){
	$data = themify_get_data();

	$sidebar_options = array(
		array('value' => 'sidebar1', 'img' => 'images/layout-icons/sidebar1.png', 'title' => __('Sidebar Right', 'themify')),
		array('value' => 'sidebar1 sidebar-left', 'img' => 'images/layout-icons/sidebar1-left.png', 'title' => __('Sidebar Left', 'themify')),
		array('value' => 'sidebar-none', 'img' => 'images/layout-icons/sidebar-none.png', 'title' => __('No Sidebar', 'themify'), 'selected' => true)
	);
	/**
	 * Sidebar placement options
	 * @var array
	 */
	$sidebar_location_options = array(
		array('value' => 'sidebar1', 'img' => 'images/layout-icons/sidebar1.png', 'selected' => true, 'title' => __('Sidebar Right', 'themify')),
		array('value' => 'sidebar1 sidebar-left', 'img' => 'images/layout-icons/sidebar1-left.png', 'title' => __('Sidebar Left', 'themify')),
		array('value' => 'sidebar2', 'img' => 'images/layout-icons/sidebar2.png', 'title' => __('Left and Right', 'themify')),
		array('value' => 'sidebar2 content-left', 	'img' => 'images/layout-icons/sidebar2-content-left.png', 'title' => __('2 Right Sidebars', 'themify')),
		array('value' => 'sidebar2 content-right', 	'img' => 'images/layout-icons/sidebar2-content-right.png', 'title' => __('2 Left Sidebars', 'themify')),
		array('value' => 'sidebar-none', 'img' => 'images/layout-icons/sidebar-none.png', 'title' => __('No Sidebar', 'themify'))
	);
	/**
	 * Entries layout options
	 * @var array
	 */
	$default_entry_layout_options = array(
		array('value' => 'list-post', 'img' => 'images/layout-icons/list-post.png', 'title' => __('List Post', 'themify')),
		array('value' => 'grid4', 'img' => 'images/layout-icons/grid4.png', 'title' => __('Grid 4', 'themify'), 'selected' => true),
		array('value' => 'grid3', 'img' => 'images/layout-icons/grid3.png', 'title' => __('Grid 3', 'themify')),
		array('value' => 'grid2', 'img' => 'images/layout-icons/grid2.png', 'title' => __('Grid 2', 'themify')),
		array('value' => 'auto_tiles', 'img' => 'images/layout-icons/auto-tiles.png', 'title' => __('Auto Tiles', 'themify'))
	);
	$default_options = array(
		array('name'=>'','value'=>''),
		array('name'=>__('Yes', 'themify'),'value'=>'yes'),
		array('name'=>__('No', 'themify'),'value'=>'no')
	);
	$content_options = array(
		array('name'=>__('Short Description', 'themify'),'value'=>'excerpt'),
		array('name'=>__('Full Content', 'themify'),'value'=>'content'),
		array('name'=>__('None', 'themify'),'value'=>''),
	);

	$val = isset( $data['setting-shop_layout'] ) ? $data['setting-shop_layout'] : '';

	/**
	 * Modules output
	 * @var String
	 * @since 1.0.0
	 */
	$output = '';
	
	/**
	 * Sidebar option
	 */
	$output .= '<p><span class="label">' . __('Shop Page Sidebar', 'themify') . '</span>';
	foreach($sidebar_options as $option){
		if ( ( '' == $val || ! $val || ! isset( $val ) ) && ( isset( $option['selected'] ) && $option['selected'] ) ) {
			$val = $option['value'];
		}
		if ( $val == $option['value'] ) {
			$class = "selected";
		} else {
			$class = "";
		}
		$output .= '<a href="#" class="preview-icon '.$class.'" title="'.$option['title'].'"><img src="'.THEME_URI.'/'.$option['img'].'" alt="'.$option['value'].'"  /></a>';
	}
	$output .= '<input type="hidden" name="setting-shop_layout" class="val" value="'.$val.'" /></p>';
	
	$output .= themify_shop_archive_layout();
	/**
	 * Entries Layout
	 */
	$output .= '<p>
					<span class="label">' . __('Product Layout', 'themify') . '</span>';
	$val = isset( $data['setting-products_layout'] ) ? $data['setting-products_layout'] : '';
	foreach($default_entry_layout_options as $option){
		if ( ( '' == $val || ! $val || ! isset( $val ) ) && ( isset( $option['selected'] ) && $option['selected'] ) ) {
			$val = $option['value'];
		}
		if ( $val == $option['value'] ) {
			$class = 'selected';
		} else {
			$class = '';
		}
		$output .= '<a href="#" class="preview-icon '.$class.'" title="'.$option['title'].'"><img src="'.THEME_URI.'/'.$option['img'].'" alt="'.$option['value'].'"  /></a>';
	}

	$output .= '	<input type="hidden" name="setting-products_layout" class="val" value="'.$val.'" />
				</p>';
		
	
	$output .= '<p data-show-if-element="[name=setting-products_layout]" data-show-if-value=' . '["grid2","grid3","grid4"]' . '><span class="label">' . __('Masonry Layout', 'themify') . '</span>
				<label for="setting-shop_masonry_disabled"><input type="checkbox" id="setting-shop_masonry_disabled" name="setting-shop_masonry_disabled" '.checked( themify_get( 'setting-shop_masonry_disabled' ), 'on', false ).' /> ' . __('Disable masonry layout in product archive grid view.', 'themify') . '</label></p>';
	
	
		/**
	 * Product Content Style
	 */
	$output .= '<p data-show-if-element="[name=setting-products_layout]" data-show-if-value=' . '["grid2","grid3","grid4","list-post"]' . '>
					<span class="label">' . __( 'Product Content Style', 'themify' ) . '</span>
					<select name="setting-product_content_layout">'.
						themify_options_module( array(
							array( 'name' => __( 'Default', 'themify' ), 'value' => '' ),
							array( 'name' => __( 'Overlay', 'themify' ), 'value' => 'overlay' ),
							array( 'name' => __( 'Polaroid', 'themify' ), 'value' => 'polaroid' ),
							array( 'name' => __( 'Boxed', 'themify' ), 'value' => 'boxed' ),
							array( 'name' => __( 'Flip', 'themify' ), 'value' => 'flip' )
						), 'setting-product_content_layout' ) . '
					</select>
				</p>';
	/**
	 * Product Gutter
	 */
	$output .= '<p><span class="label">' . __( 'Product Gutter', 'themify' ) . '</span>
					<select name="setting-product_post_gutter">'.
						themify_options_module( array(
							array( 'name' => __( 'Default', 'themify' ), 'value' => 'gutter' ),
							array( 'name' => __( 'No gutter', 'themify' ), 'value' => 'no-gutter' )
						), 'setting-product_post_gutter' ) . '
					</select>
				</p>';
	/**
	 * Product Slider
	 */
	$output .= '<p><span class="label">' . __( 'Product Hover Gallery', 'themify' ) . '</span>
					<select name="setting-products_slider">'.
						themify_options_module( array(
							array( 'name' => __( 'Enable', 'themify' ), 'value' => 'enable' ),
							array( 'name' => __( 'Disable', 'themify' ), 'value' => 'disable' )
						), 'setting-products_slider' ) . '
					</select>
				</p>';
	/**
	 * Products Per Page
	 */
	$output .= '<p><span class="label">' . __('Products Per Page', 'themify') . '</span>
				<input type="text" name="setting-shop_products_per_page" value="' . themify_get( 'setting-shop_products_per_page' ) . '" class="width2" /></p>';

	/**
	 * Hide Title Options
	 * @var String
	 * @since 1.0.0
	 */
	$output .= '<p class="feature_box_posts">
					<span class="label">' . __('Hide Product Title', 'themify') . '</span>
					<select name="setting-product_archive_hide_title">
						'.themify_options_module( $default_options, 'setting-product_archive_hide_title' ).'
					</select>
				</p>';

	/**
	 * Hide Price Options
	 * @var String
	 * @since 1.0.0
	 */
	$output .= '<p class="feature_box_posts">
					<span class="label">' . __('Hide Product Price', 'themify') . '</span>
					<select name="setting-product_archive_hide_price">
						'.themify_options_module( $default_options, 'setting-product_archive_hide_price' ).'
					</select>
				</p>';
	
	/**
	 * Hide Add to Cart Button
	 * @var String
	 */
	$output .= '<p class="feature_box_posts">
					<span class="label">' . __('Hide Add to Cart Button', 'themify') . '</span>
					<select name="setting-product_archive_hide_cart_button">
						'.themify_options_module( $default_options, 'setting-product_archive_hide_cart_button' ).'
					</select>
				</p>';
				
	/**
	 * 
	 Disable Product Lightbox
	 * @var String
	 */
	$output .= '<p><span class="label">' . __('Product Lightbox', 'themify') . '</span>
				<label for="setting-disable_product_lightbox"><input type="checkbox" id="setting-disable_product_lightbox" name="setting-disable_product_lightbox" '.checked( themify_get( 'setting-disable_product_lightbox' ), 'on', false ).' /> ' . __('Disable Product Lightbox', 'themify') . '</label></p>';


	/**
	 * Hide Breadcrumbs
	 * @var String
	 */
	$output .= '<p><span class="label">' . __('Shop Breadcrumbs', 'themify') . '</span>
				<label for="setting-hide_shop_breadcrumbs"><input type="checkbox" id="setting-hide_shop_breadcrumbs" name="setting-hide_shop_breadcrumbs" '.checked( themify_get( 'setting-hide_shop_breadcrumbs' ), 'on', false ).' /> ' . __('Hide shop breadcrumb navigation', 'themify') . '</label></p>';

	/**
	 * Hide Product Count
	 * @var String
	 */
	$output .= '<p><span class="label">' . __('Product Count', 'themify') . '</span>
				<label for="setting-hide_shop_count"><input type="checkbox" id="setting-hide_shop_count" name="setting-hide_shop_count" '.checked( themify_get( 'setting-hide_shop_count' ), 'on', false ).' /> ' . __('Hide product count', 'themify') . '</label></p>';

	/**
	 * Hide Sorting Bar
	 * @var String
	 */
	$output .= '<p><span class="label">' . __('Product Sorting', 'themify') . '</span>
				<label for="setting-hide_shop_sorting"><input type="checkbox" id="setting-hide_shop_sorting" name="setting-hide_shop_sorting" '.checked( themify_get( 'setting-hide_shop_sorting' ), 'on', false ).' /> ' . __('Hide product sorting select', 'themify') . '</label></p>';

	/**
	 * Hide Shop Page Title
	 * @var String
	 */
	$output .= '<p><span class="label">' . __('Shop Page Title', 'themify') . '</span>
				<label for="setting-hide_shop_title"><input type="checkbox" id="setting-hide_shop_title" name="setting-hide_shop_title" '.checked( themify_get( 'setting-hide_shop_title' ), 'on', false ).' /> ' . __('Hide shop page title', 'themify') . '</label></p>';
			
	/**
	 * Hide More Info Button
	 * @var String
	 */
	$output .= '<p><span class="label">' . __('Quick Look', 'themify') . '</span>
				<label for="setting-hide_shop_more_info"><input type="checkbox" id="setting-hide_shop_more_info" name="setting-hide_shop_more_info" '.checked( themify_get( 'setting-hide_shop_more_info' ), 'on', false ).' /> ' . __('Hide product quick look button', 'themify') . '</label></p>';
	
	/**
	 * Hide Social Share
	 * @var String
	 */
	$output .= '<p><span class="label">' . __('Product Share', 'themify') . '</span>
				<label for="setting-hide_shop_share"><input type="checkbox" id="setting-hide_shop_share" name="setting-hide_shop_share" '.checked( themify_get( 'setting-hide_shop_share' ), 'on', false ).' /> ' . __('Hide product share button', 'themify') . '</label></p>';

	/**
	 * Show Short Description Options
	 * @var String
	 * @since 1.0.0
	 */
	$output .= '<p class="feature_box_posts">
					<span class="label">' . __('Product Description', 'themify') . '</span>
					<select name="setting-product_archive_show_short">'.
						themify_options_module( $content_options, 'setting-product_archive_show_short' ) . '
					</select>
				</p>';
				
	/**
	 * Image Dimensions
	 */	
	$output .= '<p class="show_if_enabled_img_php">
					<span class="label">' . __('Image Size', 'themify') . '</span>  
					<input type="text" class="width2" name="setting-default_product_index_image_post_width" value="' . themify_get( 'setting-default_product_index_image_post_width' ) . '" /> ' . __('width', 'themify') . ' <small>(px)</small>
					<input type="text" class="width2" name="setting-default_product_index_image_post_height" value="' . themify_get( 'setting-default_product_index_image_post_height' ) . '" /> <span>' . __('height', 'themify') . ' <small>(px)</small></span>
					<br /><span class="pushlabel"><small>' . __('Enter height = 0 to disable vertical cropping with img.php enabled', 'themify') . '</small></span>
				</p>';
				
	return $output;
}

/**
 * Creates module for general archive layout
 * @param array $data
 * @return string
 * @since 1.5.1
 */
function themify_shop_archive_layout( $data = array() ) {

	$data = themify_get_data();
	/**
	 * Sidebar option
	 */
	$val = isset( $data['setting-shop_archive_layout'] ) ? $data['setting-shop_archive_layout'] : '';
	$options = array(
		array('value' => 'sidebar1', 'img' => 'images/layout-icons/sidebar1.png', 'title' => __('Sidebar Right', 'themify')),
		array('value' => 'sidebar1 sidebar-left', 'img' => 'images/layout-icons/sidebar1-left.png', 'title' => __('Sidebar Left', 'themify')),
		array('value' => 'sidebar-none', 'img' => 'images/layout-icons/sidebar-none.png','selected' => true, 'title' => __('No Sidebar', 'themify'))
	);

	$html= '<p><span class="label">' . __('Product Archive Sidebar', 'themify') . '</span>';
	foreach ( $options as $option ) {
		if ( ( '' == $val || ! $val || ! isset( $val ) ) && ( isset( $option['selected'] ) && $option['selected'] ) ) {
			$val = $option['value'];
		}
		$class = $val == $option['value'] ?"selected":"";
		$html.= '<a href="#" class="preview-icon '.$class.'" title="'.$option['title'].'"><img src="'.THEME_URI.'/'.$option['img'].'" alt="'.$option['value'].'"  /></a>';
	}
	$html.= '<input type="hidden" name="setting-shop_archive_layout" class="val" value="'.$val.'" /></p>';

	return $html;
}

/**
 * Creates module for single product settings
 * @param array
 * @return string
 * @since 1.0.0
 */
function themify_single_product($data=array()){
	$data = themify_get_data();

	$options = array(
		array('value' => 'sidebar1', 'img' => 'images/layout-icons/sidebar1.png', 'title' => __('Sidebar Right', 'themify')),
		array('value' => 'sidebar1 sidebar-left', 'img' => 'images/layout-icons/sidebar1-left.png', 'title' => __('Sidebar Left', 'themify')),
		array('value' => 'sidebar-none', 'img' => 'images/layout-icons/sidebar-none.png', 'title' => __('No Sidebar', 'themify'), 'selected' => true)
	);
	$defaul_image_layout =array(
		array('value' => 'img-left', 'img' => 'images/layout-icons/image-left.png', 'title' => __('Product Image Left', 'themify')),
		array('value' => 'img-center', 'img' => 'images/layout-icons/image-center.png', 'title' => __('Product Image Center', 'themify')),
		array('value' => 'img-right', 'img' => 'images/layout-icons/image-right.png', 'title' => __('Product Image Right', 'themify'))
	);

	$default_options = array(
		array('name' => '', 'value' => ''),
		array('name' => __('Yes', 'themify'), 'value' => 'yes'),
		array('name' => __('No', 'themify'), 'value' => 'no')
	);

	/**
	 * Product Sidebar
	 */
	$val = isset( $data['setting-single_product_layout'] ) ? $data['setting-single_product_layout'] : '';
	$output = '<p><span class="label">' . __('Product Sidebar Option', 'themify') . '</span>';
	foreach ( $options as $option ) {
		if ( ( '' == $val || ! $val || ! isset( $val ) ) && ( isset( $option['selected'] ) && $option['selected'] ) ) {
			$val = $option['value'];
		}
		if ( $val == $option['value'] ) {
			$class = 'selected';
		} else {
			$class = '';
		}
		$output .= '<a href="#" class="preview-icon '.$class.'" title="'.$option['title'].'"><img src="'.THEME_URI.'/'.$option['img'].'" alt="'.$option['value'].'"  /></a>';
	}
	$output .= '<input type="hidden" name="setting-single_product_layout" class="val" value="'.$val.'" /></p>';
        
        /**
	 * Product Image Layout
	 */
	$val = isset($data['setting-product_image_layout']) ? $data['setting-product_image_layout'] : '';
        $output.= '<p><span class="label">' . __('Product Image Layout', 'themify') . '</span>';
        foreach ($defaul_image_layout as $option) {
            if (( '' == $val || !$val || !isset($val) ) && ( isset($option['selected']) && $option['selected'] )) {
                $val = $option['value'];
            }
            if ($val == $option['value']) {
                $class = 'selected';
            } else {
                $class = '';
            }
            $output .= '<a href="#" class="preview-icon ' . $class . '" title="' . $option['title'] . '"><img src="' . THEME_URI . '/' . $option['img'] . '" alt="' . $option['value'] . '"  /></a>';
        }
	$output .= '<input type="hidden" name="setting-product_image_layout" class="val" value="'.$val.'" /></p>';
        			
	/**
	 * Image Dimensions
	 */	
	$output .= '<p class="show_if_enabled_img_php">
					<span class="label">' . __('Image Size', 'themify') . '</span>  
					<input type="text" class="width2" name="setting-default_product_single_image_post_width" value="' . themify_get( 'setting-default_product_single_image_post_width' ) . '" /> ' . __('width', 'themify') . ' <small>(px)</small>
					<input type="text" class="width2" name="setting-default_product_single_image_post_height" value="' . themify_get( 'setting-default_product_single_image_post_height' ) . '" /> <span>' . __('height', 'themify') . ' <small>(px)</small></span>
					<br /><span class="pushlabel"><small>' . __('Enter height = 0 to disable vertical cropping with img.php enabled', 'themify') . '</small></span>
				</p>';
	
	/**
	 * Gallery Type
	 */	
	$gallery_type = themify_get('setting-product_gallery_type');
	if( ! $gallery_type ) $gallery_type = 'default';

	$output .= '<p>
					<span class="label">' . __('Product Gallery', 'themify') . '</span>  
					<label><input type="radio" name="setting-product_gallery_type" value="zoom" '.checked($gallery_type,'zoom',false).'/> ' . __('Zoom Image', 'themify') . '</label>
					<label><input type="radio" name="setting-product_gallery_type" value="default" '.checked($gallery_type,'default',false).' />' . __('Default WooCommerce', 'themify') . '</label>
					<label><input type="radio" name="setting-product_gallery_type" value="disable-zoom" '.checked($gallery_type,'disable-zoom',false).'/> ' . __('Disable Zoom', 'themify') . '</label>
				</p>';
	
	/**
	 * Hide Social Share
	 * @var String
	 */
	$output .= '<p><span class="label">' . __('Product Share', 'themify') . '</span>
				<label for="setting-single_hide_shop_share"><input type="checkbox" id="setting-single_hide_shop_share" name="setting-single_hide_shop_share" '.checked( themify_get( 'setting-single_hide_shop_share' ), 'on', false ).' /> ' . __('Hide product share button', 'themify') . '</label></p>';

	/**
	 * Hide Breadcrumbs
	 * @var String
	 */
	$output .= '<p><span class="label">' . __('Hide Shop Breadcrumbs', 'themify') . '</span>
				<label for="setting-hide_shop_single_breadcrumbs"><input type="checkbox" id="setting-hide_shop_single_breadcrumbs" name="setting-hide_shop_single_breadcrumbs" '.checked( themify_get( 'setting-hide_shop_single_breadcrumbs' ), 'on', false ).' /> ' . __('Check to hide shop breadcrumbs', 'themify') . '</label></p>';

	/**
	 * Hide Product SKU
	 * @var String
	 */
	$output .= '<p><span class="label">' . __('Product SKU', 'themify') . '</span>
				<label for="setting-hide_shop_single_sku"><input type="checkbox" id="setting-hide_shop_single_sku" name="setting-hide_shop_single_sku" '.checked( themify_get( 'setting-hide_shop_single_sku' ), 'on', false ).' /> ' . __('Hide product SKU', 'themify') . '</label></p>';

	/**
	 * Hide Product tags
	 * @var String
	 */
	$output .= '<p><span class="label">' . __('Product Tags', 'themify') . '</span>
				<label for="setting-hide_shop_single_tags"><input type="checkbox" id="setting-hide_shop_single_tags" name="setting-hide_shop_single_tags" '.checked( themify_get( 'setting-hide_shop_single_tags' ), 'on', false ).' /> ' . __('Hide product tags', 'themify') . '</label></p>';

	/**
	 * Product Reviews
	 */
	$output .= '<p><span class="label">' . __('Product Reviews', 'themify') . '</span>
				<label for="setting-product_reviews"><input type="checkbox" id="setting-product_reviews" name="setting-product_reviews" '.checked( themify_get( 'setting-product_reviews' ), 'on', false ).' /> ' . __('Disable product reviews', 'themify') . '</label></p>';

	/**
	 * Related Products
	 */
	$output .= '<p><span class="label">' . __('Related Products', 'themify') . '</span>
				<label for="setting-related_products"><input type="checkbox" id="setting-related_products" name="setting-related_products" '.checked( themify_get( 'setting-related_products' ), 'on', false ).' /> ' . __('Do not display related products', 'themify') . '</label></p>';

	$related_products_limit = themify_check( 'setting-related_products_limit' ) ? themify_get( 'setting-related_products_limit' ) : 3;
	$output .= '<p data-show-if-element="[name=setting-related_products]" data-show-if-value=' . '["false"]' . '><span class="label">' . __('Related Products Limit', 'themify') . '</span>
					<input type="text" name="setting-related_products_limit" value="' . $related_products_limit . '" class="width2" /></p>';

		
	/**
	 * Related Image Dimensions
	 */	
	$output .= '<p class="show_if_enabled_img_php" data-show-if-element="[name=setting-related_products]" data-show-if-value=' . '["false"]' . '>
					<span class="label">' . __('Related Products Image Size', 'themify') . '</span>  
					<input type="text" class="width2" name="setting-product_related_image_width" value="' . themify_get( 'setting-product_related_image_width' ) . '" /> ' . __('width', 'themify') . ' <small>(px)</small>
					<input type="text" class="width2" name="setting-product_related_image_height" value="' . themify_get( 'setting-product_related_image_height' ) . '" /> <span>' . __('height', 'themify') . ' <small>(px)</small></span>
					<br /><span class="pushlabel"><small>' . __('Enter height = 0 to disable vertical cropping with img.php enabled', 'themify') . '</small></span>
				</p>';
				
	return $output;
}

/**
 * Creates module for shop sidebar
 * @param array
 * @return string
 * @since 1.0.0
 */
function themify_shop_sidebar($data=array()){
	$data = themify_get_data();

	$key = 'setting-disable_shop_sidebar';
	
	$output = '<p><span class="label">' . __('Shop Sidebar', 'themify') . '</span>
			<label for="'. $key.'"><input type="checkbox" id="'. $key.'" name="'. $key.'" '.checked( themify_get( $key ), 'on', false ).' /> ' . __('Disable shop sidebar', 'themify') . '</label>';
	$output.='<span class="pushlabel"><small>'.__('Shop sidebar is used in all WooCommerce pages such as shop, products, product categories, cart, checkout page, etc. If disabled, the main sidebar will be used.','themify').'</small></span></p>';

	return $output;
}

/**
 * Creates module for ajax cart style
 * @param array
 * @return string
 * @since 1.0.0
 */
function themify_ajax_cart_style($data=array()){
	$data = themify_get_data();

	$key = 'setting-cart_style';
	$value = themify_get( $key );
	if(!$value){
		$value = 'dropdown';
	}
	$output = '<p><span class="label">' . __('Cart Style', 'themify') . '</span>
			<label><input type="radio" value="dropdown" name="'. $key.'" '.checked( $value, 'dropdown', false ).' /> ' . __('Dropdown cart', 'themify') . '</label>';
	$output.='<label><input type="radio" value="slide-out" name="'. $key.'" '.checked( $value, 'slide-out', false ).' /> ' . __('Slide-out cart', 'themify') . '</label>';
	$output.='<label><input type="radio" value="link_to_cart" name="'. $key.'" '.checked( $value, 'link_to_cart', false ).' /> ' . __('Link to cart page', 'themify') . '</label></p>';

	$key = 'setting-cart_show_seconds';

	$output .= '<p><span class="label">' . __( 'Show cart', 'themify' ) . '</span>
					<select name="' . $key . '">'.
						themify_options_module( array(
							array( 'name' => 1, 'value' => 1000 ),
							array( 'name' => 2, 'value' => 2000 ),
							array( 'name' => 3, 'value' => 3000 ),
							array( 'name' => 4, 'value' => 4000 ),
							array( 'name' => 5, 'value' => 5000 )
						), $key ) . '
					</select> ' . esc_html__( 'seconds', 'themify' ) . '<br>
					<small class="pushlabel">' . esc_html__( 'When an item is added, show cart for n second(s)', 'themify' ) . '</small>
				</p>';

	/**
	 * Disable AJAX add to cart
	 * @var String
	 */
	$output .= '<p>
				<label for="setting-single_ajax_cart" class="pushlabel"><input type="checkbox" id="setting-single_ajax_cart" name="setting-single_ajax_cart" '.checked( themify_get( 'setting-single_ajax_cart' ), 'on', false ).' /> ' . __('Disable AJAX cart on single product page', 'themify') . '</label></p>';

	return $output;
}



if (!function_exists('themify_spark_animation')) {

	/**
	 * Spark Animation Settings
	 * @param array $data
	 * @return string
	 */
	function themify_spark_animation($data = array()) {
        $pre_color = 'setting-spark_color';
		$pre = 'setting-spark_animation';
        $sparkling_color = themify_get( $pre_color );
		$output = '<p><span class="label">' . __('Spark Animation', 'themify') . '</span><label for="'.$pre.'"><input type="checkbox" id="'.$pre.'" name="'.$pre.'" ' . checked( themify_get( $pre ), 'on', false ) . ' /> ' . __('Disable add to cart and wishlist spark animation', 'themify');
		$output.='<br/><small>'.__('Spark animation is the animation effect occurs when user clicks on the add to cart or wishlist button.','themify').'</small></label></p>';
		$output .='<div class="themify_field_row" data-show-if-element="[name=' . $pre . ']" data-show-if-value="false">
					<span class="label">' . __('Spark Icons Color', 'themify') . '</span>
					<div class="themify_field-color">
						<span class="colorSelect" style="' . esc_attr( 'background:#' . $sparkling_color . ';' ). '">
							<span></span>
						</span>
						<input type="text" class="colorSelectInput width4" value="' . esc_attr( $sparkling_color ) . '" name="' . esc_attr( $pre_color ) . '" />
					</div>
				</div>';
		return $output;
	}

}




/**************************************************************************************************
 * Start Woocommerce Functions
 **************************************************************************************************/

// Declare Woocommerce support
add_theme_support( 'woocommerce' );

add_action( 'template_redirect', 'themify_redirect_product_ajax_content', 20 );
add_action( 'admin_notices', 'themify_check_ecommerce_environment_admin' );
add_action( 'themify_body_end', 'themify_theme_lightbox_added' );

add_filter( defined( 'WOOCOMMERCE_VERSION' ) && version_compare( WOOCOMMERCE_VERSION, '3.3.0', '>=' )
	? 'woocommerce_get_script_data' : 'woocommerce_params', 'themify_woocommerce_params' );
add_filter( 'themify_body_classes', 'themify_woocommerce_site_notice_class' );
add_filter( 'themify_post_types', 'themify_theme_add_product_type' );

if ( ! function_exists( 'themify_theme_add_product_type' ) ) {
	/**
	 * Add 'product' to list of post types managed by Themify
	 * @param $types
	 * @return array
	 */
	function themify_theme_add_product_type( $types ) {
		$extended = array_merge( array( 'product' ), $types	);
		return array_unique( $extended );
	}
}

if ( ! function_exists( 'themify_edit_link' ) ) {
	/**
	 * Displays a link to edit the entry
	 */
	function themify_edit_link() {
		edit_post_link(__('Edit', 'themify'), '<span class="edit-button">[', ']</span>');
	}
}

////////////////////////////////////////////////////////////
// Sidebar Layout Classes Filter
////////////////////////////////////////////////////////////
if ( ! function_exists( 'themify_theme_sidebar_layout_condition' ) ) {
	/**
	 * Alters condition to filter layout class
	 * @param bool
	 * @return bool
	 */
	function themify_theme_sidebar_layout_condition($condition){
		return $condition || themify_is_function('is_shop') || themify_is_function('is_product_category') || themify_is_function('is_product_tag') || themify_is_function('is_product') || themify_theme_is_product_query();
	}
	add_filter('themify_default_layout_condition', 'themify_theme_sidebar_layout_condition');
}

if ( ! function_exists( 'themify_theme_sidebar_layout' ) ) {
	/**
	 * Returns default shop layout
	 * @param string $class
	 * @return string
	 */
	function themify_theme_sidebar_layout($class) {
		global $themify;
		$class = $themify->layout;
		if ( themify_is_function('is_shop') || themify_is_function('is_product_category') || themify_is_function('is_product_tag') ) {
			$class = themify_get('setting-shop_layout')? themify_get('setting-shop_layout') : 'sidebar-none';
		} elseif ( themify_is_function('is_product') ){
			$class = themify_get('setting-single_product_layout')? themify_get('setting-single_product_layout') : 'sidebar-none';
		} elseif ( themify_theme_is_product_query() ) {
			$class .= ' query-product';
		}
		return $class;
	}
	add_filter('themify_default_layout', 'themify_theme_sidebar_layout');
}

////////////////////////////////////////////////////////////
// Post Layout Classes Filter
////////////////////////////////////////////////////////////
if ( ! function_exists( 'themify_theme_default_post_layout_condition' ) ) {
	/**
	 * Alters condition to filter post layout class
	 * @param bool
	 * @return bool
	 */
	function themify_theme_default_post_layout_condition($condition) {
		return $condition || themify_is_function('is_shop') || themify_is_function('is_product_category') || themify_is_function('is_product_tag') || themify_theme_is_product_query();
	}
	add_filter('themify_default_post_layout_condition', 'themify_theme_default_post_layout_condition');
}

if ( ! function_exists( 'themify_theme_default_post_layout' ) ) {
	/**
	 * Returns default shop layout
	 * @param string $class
	 * @return string
	 */
	function themify_theme_default_post_layout( $class ) {
		global $themify;
		$class = $themify->post_layout;
		if( themify_is_function('is_shop') || themify_is_function('is_product_category') || themify_is_function('is_product_tag') ) {
			$class = '' != themify_get('setting-products_layout')? themify_get('setting-products_layout') : 'list-post';
		}
		return $class;
	}
	add_filter('themify_default_post_layout', 'themify_theme_default_post_layout');
}

/**
 * Check if the current view must be replaced with a query product loop
 * @param $context
 * @return bool
 */
function themify_theme_is_product_query() {
	static $is_product_query = null;
	if(is_null($is_product_query)){
		global $themify;
		$is_product_query = '' != $themify->product_category && themify_is_woocommerce_active();
	}
	return $is_product_query;
}

if ( ! function_exists( 'themify_is_function' ) ) {
	/**
	 * Checks if it's the function name passed exists and in that case, it calls the function
	 * @param string $context
	 * @return bool|mixed
	 * @since 1.0.0
	 */
	function themify_is_function( $context = '' ) {
		if( function_exists( $context ) )
			return call_user_func( $context );
		else
			return false;
	}
}

if ( ! function_exists( 'themify_woocommerce_site_notice_class' ) ) {
	/**
	 * Add additional class when Woocommerce site wide notice is enabled.
	 * @param array $classes
	 * @return array
	 * @since 1.0.0
	 */
	function themify_woocommerce_site_notice_class( $classes ) {
		$notice = get_option( 'woocommerce_demo_store' );
		if ( ! empty( $notice ) && 'no' != $notice ) {
			$classes[] = 'site-wide-notice';
		}
		return $classes;
	}
}

if ( ! function_exists( 'themify_get_ecommerce_template' ) ) {
	/**
	 * Checks if Woocommerce is active and loads the requested template
	 * @param string $template
	 * @since 1.0.0
	 */
	function themify_get_ecommerce_template( $template = '' ) {
		if ( themify_is_woocommerce_active() )
			get_template_part( $template );
	}
}

/**
 * Add woocommerce_enable_ajax_add_to_cart option to JS
 * @param Array
 * @return Array
 */
function themify_woocommerce_params( $params ) {
	if( is_array( $params ) ) {
		$params = array_merge( $params, array(
			'option_ajax_add_to_cart' => ( 'yes' == get_option( 'woocommerce_enable_ajax_add_to_cart' ) ) ? 'yes' : 'no'
		) );
	}
	return $params;
}

/**
 * Single product lightbox
 */
function themify_redirect_product_ajax_content() {
	global $post, $wp_query;
	// locate template single page in lightbox
	if (is_single() && isset($_GET['ajax']) && $_GET['ajax']) {
		// remove admin bar inside iframe
		add_filter( 'show_admin_bar', '__return_false' );
		if (have_posts()) {
			woocommerce_single_product_content_ajax();
			die();
		} else {
			$wp_query->is_404 = true;
		}
	}
}

if ( ! function_exists( 'themify_check_ecommerce_environment_admin' ) ) {
	/**
	 * Check in admin if Woocommerce is enabled and show a notice otherwise.
	 * @since 1.3.0
	 */
	function themify_check_ecommerce_environment_admin() {
		if ( ! themify_is_woocommerce_active() ) {
			$warning = 'installwoocommerce9';
			if ( ! get_option( 'themify_warning_' . $warning ) ) {
				wp_enqueue_script( 'themify-admin-warning' );
				echo '<div class="update-nag">'.__('Remember to install and activate WooCommerce plugin to enable the shop.', 'themify'). ' <a href="#" class="themify-close-warning" data-warning="' . $warning . '" data-nonce="' . wp_create_nonce( 'themify-warning' ) . '">' . __("Got it, don't remind me again.", 'themify') . '</a></div>';
			}
		}
	}
}

if ( ! function_exists( 'themify_check_ecommerce_scripts' ) ) {
	function themify_check_ecommerce_scripts() {
		wp_register_script( 'themify-admin-warning', themify_enque(THEME_URI . '/js/themify.admin.warning.js'), array('jquery'), false, true );
	}
	add_action( 'admin_enqueue_scripts', 'themify_check_ecommerce_scripts' );
}

if ( ! function_exists( 'themify_dismiss_warning' ) ) {
	function themify_dismiss_warning() {
		check_ajax_referer( 'themify-warning', 'nonce' );
		$result = false;
		if ( isset( $_POST['warning'] ) ) {
			$result = update_option( 'themify_warning_' . $_POST['warning'], true );
		}
		if ( $result ) {
			echo 'true';
		} else {
			echo 'false';
		}
		die;
	}
	add_action( 'wp_ajax_themify_dismiss_warning', 'themify_dismiss_warning' );
}

/**
 * Checks if it's a product in lightbox
 * @return bool
 */
function themify_theme_is_product_lightbox() {
	return isset( $_GET['post_in_lightbox'] ) && '1' == $_GET['post_in_lightbox'];
}

function themify_theme_lightbox_added() {
	global $woocommerce;
	if ( themify_is_woocommerce_active() ) : ?>
		<div class="lightbox-added" style="display:none;">
			<h2><?php _e('Added to Cart', 'themify'); ?></h2>
			<a href="#" rel="nofollow" class="button outline close-themibox"><?php _e('Keep Shopping', 'themify'); ?></a>
			<button type="submit" class="button checkout" onClick="document.location.href='<?php echo esc_url( wc_get_checkout_url() ); ?>'; return false;"><?php _e('Checkout', 'themify')?></button>
		</div>
	<?php endif;

}

/**
 * Output settings for product background image
 * @param int $post_id
 * @return string
 */
function themify_product_fullcover( $post_id = 0 ) {
	if ( 0 == $post_id ) $post_id =  get_the_ID();
	$fullcover = get_post_meta( $post_id, 'background_repeat', true );
	return '' == $fullcover || 'default' == $fullcover? 'fullcover' : $fullcover;
}

// Load required files
if ( themify_is_woocommerce_active() ) {
	require_once(TEMPLATEPATH . '/woocommerce/theme-woocommerce.php'); // WooCommerce overrides
	require_once(TEMPLATEPATH . '/woocommerce/woocommerce-hooks.php'); // WooCommerce hook overrides
	require_once(TEMPLATEPATH . '/woocommerce/woocommerce-template.php'); // WooCommerce template overrides
}