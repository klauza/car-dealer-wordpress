<?php
/*-----------------------------------------------------------------------------------*/
/* Any WooCommerce overrides and functions can be found here
/*-----------------------------------------------------------------------------------*/

/**
 * Use shop_medium size instead of shop_catalog
 */
function themify_get_product_image($urlonly=false,$src=false,$is_singular=false) {
	global $post,$product, $themify,$woocommerce_loop;
	$is_singular = $is_singular || is_singular('product');
	$post_image = '';
	$is_loopname_singular = $is_singular && ! empty( $woocommerce_loop['name'] );
	$related = $is_loopname_singular && $woocommerce_loop['name'] === 'related';
	$up_sells = $is_loopname_singular && $woocommerce_loop['name'] === 'up-sells';
	if($is_singular && !$related && !$up_sells){
		$size = 'shop_single';
		$cl = $slider = '';
	}
	else{
		$size = 'shop_catalog';
		$slider = themify_get_product_slider($product);
		if(!empty($slider)){
			$cl = ' product-slider';
			$slider = 'data-product-slider="'.esc_attr($slider).'"';
			if((!$is_singular || $related || $up_sells) && 'yes' != $themify->unlink_product_image ){
				$slider.=' data-product-link="'.get_the_permalink().'"';
			}
		}
		else{
			$cl = $slider = '';
		
		}
	}
	$html = '';
	if(!$urlonly && (!$is_singular || $related || $up_sells)){
		$onsale = $product->is_on_sale()?apply_filters('woocommerce_sale_flash', '<span class="onsale">'.__( 'Sale!', 'woocommerce' ).'</span>', $post, $product):'';
		$html = '<figure '.$slider.' class="post-image product-image'.$cl.'">'.$onsale;
		if( 'yes' != $themify->unlink_product_image ){
			$html.='<a href="'.get_the_permalink().'">';
		}
	}
	$width = $height = '';
	if(!themify_is_image_script_disabled()){
		
		static $default_width = null,$default_height = null;
		if(!empty($themify->builder_args['is_product'])){
			$width = $themify->width;
			$height = $themify->height;
			$image_size = $themify->image_setting;
		}
		elseif($related){
			$width = themify_get( 'setting-product_related_image_width' );
			$height = themify_get( 'setting-product_related_image_height' );
		}
		if(!$width && !$height){

			if(is_null($default_width) || $related){
				$default_width = $is_singular && !$related?themify_theme_get('image_width','', 'setting-default_product_single_image_post_width' ):themify_get( 'setting-default_product_index_image_post_width' );
			}
			if(is_null($default_height) || $related){
				$default_height = $is_singular && !$related?themify_theme_get('image_height','','setting-default_product_single_image_post_height' ):themify_get( 'setting-default_product_index_image_post_height' );
			}
			$width = $default_width;
			$height = $default_height;
			if(empty($image_size)){
				$image_size = 'image_size='.$size.'&';
			}
		}
		if($width || $height){
			$attr =  'ignore=true&'.$image_size.'w=' . $width . '&h=' . $height;
			if($src){
				$attr.= '&src='.$src;
			}
			elseif(!has_post_thumbnail()){
				$attr.= '&class=woocommerce-placeholder wp-post-image&src='.wc_placeholder_img_src();
			}
			else{
				$attr.= '&class=wp-post-image';
			}
			if($urlonly){
				$attr.='&urlonly=1';
			}

			$post_image = apply_filters( 'post_thumbnail_html', themify_get_image($attr), get_the_ID(), get_post_thumbnail_id( get_the_ID() ), array($width, $height), '' );
		}
	}
	if(empty($post_image)){
		if($urlonly){
			$url = wc_get_product_attachment_props( get_post_thumbnail_id(), $post );
			$post_image=$url['url'];
		}
		else{
			$post_image=woocommerce_get_product_thumbnail( $size );
		}
		
	}
	$html.=$post_image;
	if(!$urlonly && (!$is_singular || $related || $up_sells)){
		if( 'yes' !== $themify->unlink_product_image ){
			$html.='</a>';
		}
		$html .= '</figure>';
	}
	return array('html'=>$html,'width'=>$width,'height'=>$height);
}


/**
 * Replace link to rebuild images to Themify's own rebuild page
 * @param $args
 * @return array
 */
function themify_theme_replace_rebuild_link( $args ) {
	foreach ( $args as $arg ) {
		if ( stripos( $arg['desc'], 'http://wordpress.org/extend/plugins/regenerate-thumbnails/' ) ) {
			$arg['desc'] = str_replace( 'http://wordpress.org/extend/plugins/regenerate-thumbnails/', admin_url( 'https://wordpress.org/plugins/regenerate-thumbnails/' ), $arg['desc'] );
		}
		$new_args[] = $arg;
	}
	return $new_args;
}

/**
 * Hide certain shop features based on user choice
 */
function themify_hide_shop_features() {
	if ( themify_check( 'setting-hide_shop_count' ) ) {
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
	}
	else{
		get_template_part( 'loop/result-count');
	}
}
 
/**
 * Display sorting bar only in shop and category pages
 * @since 1.0.0
 */
function themify_catalog_ordering() {
	if ( !is_search() ) {
		// Get user choice
		if ( ! themify_check( 'setting-hide_shop_sorting' ) )
			woocommerce_catalog_ordering();
	}
}

/**
 * Show Themify welcome message in home shop
 */
function themify_show_welcome_message() {
	if ( is_front_page() && !is_paged() )
		get_template_part( 'includes/welcome-message');
}

/**
 * Hide related products based in user choice
 */
function themify_single_product_related_products() {
	if ( is_product() ) {
		if ( themify_get_gallery_type() === 'zoom' || themify_get_gallery_type()==='disable-zoom' ) {
			add_filter( 'woocommerce_single_product_image_thumbnail_html','themify_swipe_main_image_html', 10, 2 );
			add_filter( 'woocommerce_single_product_image_html','themify_swipe_main_image_html', 10, 2 ); //for below 3.0.0
			remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );
			add_action( 'woocommerce_product_thumbnails', 'themify_swipe_image_thumbnail_html', 20 );
			// Remove default gallery
			remove_theme_support( 'wc-product-gallery-zoom' );
			remove_theme_support( 'wc-product-gallery-lightbox' );
			remove_theme_support( 'wc-product-gallery-slider' );
		}
		
		if ( ! themify_check( 'setting-related_products' ) ) {
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
			add_action( 'woocommerce_after_single_product_summary', 'themify_related_products_limit', 20 );
		} else {
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
		}
	}
}

/**
 * Display a specific number of related products
 * @since 1.3.2
 */
function themify_related_products_limit() {
	$related_products_limit = themify_check('setting-related_products_limit')? themify_get('setting-related_products_limit'): 3;
	woocommerce_related_products(array(
		  'posts_per_page' => $related_products_limit,
		  'columns'        => 3,
	));
}

/**
 * Hide reviews based in user choice
 * @param array $tabs Default tabs shown
 * @return array Filtered tabs
 */
function themify_single_product_reviews($tabs){
	if(is_product()) {
		if(themify_check('setting-product_reviews')) {
			unset($tabs['reviews']);
		}
	}
	return $tabs;
}

/**
 * Get sidebar layout
 */
function themify_woocommerce_sidebar_layout(){
	/** Themify Default Variables
	 *  @var object */
	global $themify;
	if ( is_shop() || is_product_category() ) {
		$sidebar_layout = '';
		$queried_object = get_queried_object();
		$sidebar_in_page = '';
		$key = is_shop()?'setting-shop_layout':'setting-shop_archive_layout';
		if ( isset( $queried_object ) && isset( $queried_object->ID ) ) {
			$sidebar_in_page = get_post_meta( $queried_object->ID, 'page_layout', true );
		}
		if ( ! empty( $sidebar_in_page ) && 'default' != $sidebar_in_page ) {
			$sidebar_layout = $sidebar_in_page;
		} elseif( themify_check($key) ) {
			$sidebar_layout = themify_get($key);
		} elseif( themify_check('setting-default_layout') ) {
			$sidebar_layout = themify_get('setting-default_layout');
		}
		$themify->layout = empty( $sidebar_layout ) ? 'sidebar1' : $sidebar_layout;
	}
	elseif(is_product()) {
		$themify->layout = themify_check('setting-single_product_layout')? themify_get('setting-single_product_layout'): 'sidebar1';
	}
	if('sidebar-none' === $themify->layout)
		remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
}

/**
 * Disables shop page title output based on user choice
 */
function themify_hide_shop_title() {
	if('' != themify_get('setting-hide_shop_title') && is_shop()){
		add_filter('woocommerce_show_page_title', create_function('', 'return false;'));
	}
};

/**
 * Disables price output or not following the setting applied in shop settings panel
 * @param string $price
 * @return string
 */
function themify_no_price($price){
	global $themify;
	if( ( in_the_loop() && ( is_woocommerce() && ! $themify->is_single_product_main ) || themify_theme_is_product_query() ) )
		
	    return is_product() || themify_theme_get('hide_price','','setting-product_archive_hide_price') !== 'yes'?$price: '';
	else
		return $price;
}

/**
 * Disables title output or not following the setting applied in shop settings panel
 * @param $title String
 * @return String
 */
function themify_no_product_title($title){
	global $themify;
	if( ( in_the_loop() && ( is_woocommerce() && ! $themify->is_single_product_main ) || themify_theme_is_product_query() ) && themify_theme_get('hide_title','','setting-product_archive_hide_title') === 'yes' )
		return '';
	else
		return $title;
}

/**
 * Include post type product in WordPress' search
 * @param array
 * @return array
 * @since 1.0.0 
 */
function woocommerceframework_add_search_fragment ( $settings ) {
	$settings['add_fragment'] = '?post_type=product';
	return $settings;
}

/**
 * Set number of products shown in shop
 * @param int $products Default number of products shown
 * @return int Number of products based on user choice
 */
function themify_products_per_page($products){
	return themify_get('setting-shop_products_per_page');
}

//////////////////////////////////////////////////////////////
// Update catalog images
// Hooks:
// 		switch_theme - themify_theme_delete_image_sizes_flag
// 		wp_loaded - themify_set_wc_image_sizes
//////////////////////////////////////////////////////////////

/**
 * Delete flag option to set up new image sizes the next time
 */
function themify_theme_delete_image_sizes_flag() {
	delete_option( 'themify_set_wc_images' );
}

/**
 * Set up initial image sizes
 */
function themify_set_wc_image_sizes(){
	$sizes = get_option('themify_set_wc_images');
	if( ! isset( $sizes ) || ! $sizes ) {
		// update catalog images

		update_option( 'shop_catalog_image_size',  array(
					'width' 	=> '200',
					'height'	=> '160',
					'crop'		=> true
				) );

		update_option( 'shop_single_image_size',  array(
					'width' 	=> '600',
					'height'	=> '380',
					'crop'		=> 1
				) );

		update_option( 'shop_thumbnail_image_size',  array(
					'width' 	=> '58',
					'height'	=> '58',
					'crop'		=> 1
				) );

		update_option('themify_set_wc_images', true);
	}
}
add_action('wp_loaded', 'themify_set_wc_image_sizes');

/** gets the url to remove an item from dock cart */
function themify_get_remove_url( $cart_item_key ) {
	global $woocommerce;

	$cart_page_id =  version_compare( WOOCOMMERCE_VERSION, '3.0.0', '>=' )
		? wc_get_page_id( 'cart' )
		: woocommerce_get_page_id( 'cart' );
		
	if ($cart_page_id)
		return apply_filters('woocommerce_get_remove_url', $woocommerce->nonce_url( 'cart', add_query_arg('update_cart', $cart_item_key, get_permalink($cart_page_id))));
}

/**
 * Remove from cart/update
 **/
function themify_update_cart_action() {
	global $woocommerce;
	
	// Update Cart
	if (!empty($_GET['update_cart']) && $woocommerce->verify_nonce('cart')) :
		
		$cart_totals = $_GET['update_cart'];
		
		if (sizeof($woocommerce->cart->get_cart())>0) : 
			foreach ($woocommerce->cart->get_cart() as $cart_item_key => $values) :
				
        $update = $values['quantity'] - 1;
        
				if ($cart_totals == $cart_item_key) 
          $woocommerce->cart->set_quantity( $cart_item_key, $update);
				
			endforeach;
		endif;
		
		echo json_encode(array('deleted' => 'deleted'));
    die();
		
	endif;
}

/**
 * Single post lightbox
 **/
function themify_single_post_lightbox() {

	// locate template single page in lightbox
	if (!empty( $_GET['post_in_lightbox'] )  && is_product() ) {
		if(themify_get_gallery_type()==='zoom' || themify_get_gallery_type()==='disable-zoom' ){
			add_filter('the_title','themif_link_to_post',10,1);
			add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
			add_filter('woocommerce_single_product_image_thumbnail_html','themify_swipe_main_image_html',10,2);
			add_filter('woocommerce_single_product_image_html','themify_swipe_main_image_html',10,2);//for below 3.0.0
			remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );
			add_action( 'woocommerce_product_thumbnails', 'themify_swipe_image_thumbnail_html', 20 );
			
		}
		add_filter('woocommerce_product_tabs', '__return_false',100);
		$return_template = locate_template( 'single-lightbox.php' );
                wc_get_template( 'single-product/add-to-cart/variation.php' );
		themify_do_theme_redirect($return_template);
	}
}

/**
 * Add product variation value to callback lightbox
 **/
function themify_product_variation_vars(){
  global $available_variations;
  echo '<div class="hide" id="themify_product_vars">'.json_encode($available_variations).'</div>';
}

/**
 * Add cart total and shopdock cart to the WC Fragments
 * @param array $fragments 
 * @return array
 */
function themify_theme_add_to_cart_fragments( $fragments ) {
	// cart list
	ob_start();
	get_template_part( 'includes/shopdock' );
	$shopdock = ob_get_clean();
	$fragments['#shopdock'] = $shopdock;
	$total = WC()->cart->get_cart_contents_count();
	$cl= $total>0?'icon-menu-count':'icon-menu-count cart_empty';
	$fragments['#cart-icon-count .icon-menu-count, #cart-link-mobile .icon-menu-count'] = '<span class="'.$cl.'">' . $total. '</span>';
	return $fragments;
}

/**
 * Delete cart
 * @return json
 */
function themify_theme_woocommerce_delete_cart() {
	global $woocommerce;

	if ( !empty($_POST['remove_item'])) {
		$woocommerce->cart->set_quantity( $_POST['remove_item'], 0 );
		WC_AJAX::get_refreshed_fragments();
		die();
	}
}

/**
 * Add to cart ajax on single product page
 * @return json
 */
function themify_theme_woocommerce_add_to_cart() {
	ob_start();
	WC_AJAX::get_refreshed_fragments();
	die();	
}

/**
 * Remove (unnecessary) success message after a product was added to cart through theme's AJAX method.
 * 
 * @since 1.5.5
 * 
 * @param string $message
 *
 * @return string
 */
 
function themify_theme_wc_add_to_cart_message( $message ) {
	if ( isset( $_REQUEST['action'] ) && 'theme_add_to_cart' === $_REQUEST['action'] ) {
		$message = '';
	}
	return $message;
}


function themif_link_to_post($title){
	return '<a href="'.get_the_permalink().'">'.$title.'</a>';
}

// Get Gallery Type 
function themify_get_gallery_type() {
	$type = themify_get( 'setting-product_gallery_type' );

	if( empty( $type ) ) $type = 'default';
	if( $type === 'default' && version_compare( WOOCOMMERCE_VERSION,'3.0.0','<' ) )
		$type = false;

	return $type;
}


/**
 * Add swipe slider wrapper for main image
 */
function themify_swipe_main_image_html( $img, $attachment_id ) {
	global $product, $post;

	$attachment_ids = themify_get_gallery( $product );

	$html = '<div class="themify_spinner"></div><div class="swiper-container product-images-carousel"><div class="swiper-wrapper">';
	if ( has_post_thumbnail() ) {
		$props = wc_get_product_attachment_props( $attachment_id, $post );
		$image = themify_get_product_image( false, $props['url'] );
		if(themify_get_gallery_type()==='disable-zoom' ) :
		$html .= '<div class="swiper-slide woocommerce-main-image woocommerce-product-gallery__image post-image">';
		else :
			$html .= '<div data-zoom-image="'. $props['url'] . '" class="swiper-slide woocommerce-main-image woocommerce-product-gallery__image zoom post-image">';
		endif;
		$html .= empty( $image[ 'width' ] ) && empty( $image[ 'height' ] )
			? get_the_post_thumbnail( $post->ID, 'shop_single' ) : $image[ 'html' ];
	} else {
		if(themify_get_gallery_type()==='disable-zoom' ) :
			$html .= '<div class="swiper-slide woocommerce-main-image woocommerce-product-gallery__image post-image">';
		else :
			$html .= '<div class="swiper-slide woocommerce-main-image woocommerce-product-gallery__image zoom post-image">';
		endif;
		$html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />'
			, esc_url( wc_placeholder_img_src() )
			, esc_html__( 'Awaiting product image', 'themify' ) );
	}

	$html .= '</div>';
	
	foreach ( $attachment_ids as $attach ) {
		$props = wc_get_product_attachment_props( $attach, $post );

		if ( ! $props['url'] ) {
			continue;
		}

		$thumb = themify_get_product_image( true, $props['url'] );

		if ( empty( $thumb['width'] ) && empty( $thumb['height'] ) ) {
			$size  = wc_get_image_size( 'shop_single' );
			$thumb['width'] = $size['width'];
			$thumb['height'] = $size['height'];
			$thumb['html'] = wp_get_attachment_image_url( $attach, 'shop_single' );
		}

		if(themify_get_gallery_type()==='disable-zoom' ) :
			$html.='<div class="swiper-slide woocommerce-main-image woocommerce-product-gallery__image post-image"><div class="default_img" style="width:'.$thumb['width'].'px; " data-width="'.$thumb['width'].'" data-height="'.$thumb['height'].'" data-src="'.$thumb['html'].'" data-title="'.$props['title'].'" data-alt="'.$props['alt'].'"></div></div>';
		else :
			$html.='<div data-zoom-image="'.$props['url'].'" class="swiper-slide woocommerce-main-image woocommerce-product-gallery__image zoom post-image"><div class="default_img" style="width:'.$thumb['width'].'px; " data-width="'.$thumb['width'].'" data-height="'.$thumb['height'].'" data-src="'.$thumb['html'].'" data-title="'.$props['title'].'" data-alt="'.$props['alt'].'"></div></div>';
		endif;

	}

	$html .= '</div></div>';

	return $html;
}

function themify_swipe_image_thumbnail_html(){
	global $post, $product;

	$attachment_ids = themify_get_gallery($product);
	$class = (themify_get_gallery_type()==='disable-zoom' ) ? 'swiper-slide post-image' : 'zoom swiper-slide post-image';
	if ( $attachment_ids) {
		$html='<div class="swiper-container product-thumbnails-carousel"><ul class="swiper-wrapper flex-control-nav">';
		if(has_post_thumbnail()){
			$html.='<li class="'.$class.'">';
			$html.= str_replace( 'srcset="#"', '', wp_get_attachment_image(get_post_thumbnail_id( $post ), 'shop_thumbnail', false, array( 'srcset' => '#' ) ) );
			$html.='</li>';
		}
		else {
			$html.='<li class="'.$class.' woocommerce-main-image ">';
			$html.= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src() ), esc_html__( 'Awaiting product image', 'themify' ) );
			$html.='</li>';
		}
		foreach ( $attachment_ids as $attachment_id ) {
			$html.='<li class="'.$class.'">';
			$html.= wp_get_attachment_image( $attachment_id, 'shop_thumbnail' );
			$html.='</li>';
		}
		$html.='</ul></div>';
		echo $html;
	}
}

//get Cart Style
function themify_get_cart_style() {
    static $cart_style = null;
    if (is_null($cart_style) && themify_is_woocommerce_active()) {
		$cart_style = is_shop()?get_post_meta( wc_get_page_id('shop'), 'cart_style', true ):(is_singular()? themify_get('cart_style'):false);
        if (!$cart_style) {
            $cart_style = themify_get('setting-cart_style');
        }
        if (!$cart_style) {
            $cart_style = 'dropdown';
        }
    }
    return $cart_style;
}

//Check Quick Look
function themify_hide_quick_look() {
	static $quick_look = null;

	if( is_null( $quick_look ) ) {
		if( themify_theme_is_product_query() ) {
			$quick_look = themify_theme_get( 'quick_look' );
			$quick_look = $quick_look === 'yes' ? false : ( $quick_look === 'no' ? true : null );
		}
		if( is_null( $quick_look ) ) {
			$quick_look = ! themify_check( 'setting-hide_shop_more_info' );
		}
	}

	return $quick_look;
}

//Check Social Share
function themify_hide_social_share() {
	$social = null;

	if( is_null( $social ) ) {
		if( is_product() ){
			$social = ! themify_check('setting-single_hide_shop_share');
		} elseif( themify_theme_is_product_query() ) {
			$social = themify_theme_get( 'social_share' );
			$social = $social === 'yes' ? false : ( $social ==='no' ? true : null );
		}
		if( is_null( $social ) ) {
			$social = ! themify_check( 'setting-hide_shop_share' );
		}
	}

	return $social;
}

// Variation Custom Image Size
function themify_variation_image_size( $data ) {
	if( ! empty( $data[ 'image' ] ) ) {
		$image = themify_get_product_image( true, $data[ 'image' ][ 'full_src' ] );

		if( ! empty( $image[ 'width' ] ) && ! empty( $image[ 'height' ] ) ) {
			$data[ 'image' ][ 'src' ] = $image[ 'html' ];
			$data[ 'image' ][ 'src_w' ] = $image[ 'width' ];
			$data[ 'image' ][ 'src_h' ] = $image[ 'height' ];
		}
	}

	return $data;
}

// Set AJAX variation limit
function themify_ajax_variation_threshold( $qty, $product ) { return 500; }

// Render Builder content in description tab
function themify_builder_description_tab( $content ) {
	if( is_singular( array( 'product' ) ) ) {
		global $themify_builder_plugin_compat;

		if( isset( $themify_builder_plugin_compat ) ) {
			$builder_output = '';

			global $post, $ThemifyBuilder;
			if ( Themify_Builder_Model::is_front_builder_activate() ) {
				$builder_output = $ThemifyBuilder->get_active_builder_data( $post->ID ) . $ThemifyBuilder->get_builder_stylesheet('');
			} else {
				$builder_data = $ThemifyBuilder->get_builder_data( $post->ID );
				$output = Themify_Builder_Component_Base::retrieve_template( 'builder-output.php', array( 'builder_output' => $builder_data, 'builder_id' => $post->ID ), '', '', false );
				$builder_output = $ThemifyBuilder->get_builder_stylesheet( $output ) . $output;
			}

			remove_action( 'woocommerce_after_single_product_summary', array( $themify_builder_plugin_compat, 'show_builder_below_tabs' ), 12 );

			$content .= $builder_output;
		}
	}

	return $content;
}