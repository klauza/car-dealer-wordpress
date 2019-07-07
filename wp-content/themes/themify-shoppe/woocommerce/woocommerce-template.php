<?php
if ( ! function_exists( 'themify_theme_shop_loop_item_title' ) ) {
	/**
	 * Render product title.
	 *
	 * @since 1.5.8
	 */
	function themify_theme_shop_loop_item_title() {
		global $themify,$product;
		$title_class = 'product_title';
		if ( $themify->is_related_loop || ( ( is_woocommerce() || themify_theme_is_product_query() ) && 'list-post' != $themify->post_layout ) ) {
			$title_class = '';
		}
		$price = '';
		if (is_object( $product ) && ($price_html = $product->get_price_html() )){
			$price='<span class="price">'.$price_html.'</span>';
		}
		if ( 'yes' != $themify->hide_product_title ) :
			if ( 'yes' != $themify->unlink_product_title ) : ?>
				<a href="<?php the_permalink(); ?>">
					<h3 class="<?php echo esc_attr( $title_class ); ?>"><?php the_title(); ?></h3>
					<?php echo $price?>
				</a>
			<?php else : ?>
				<h3 class="<?php echo esc_attr( $title_class ); ?>"><?php the_title(); ?></h3>
				<?php echo $price?>
			<?php endif;?>
		<?php else:?>
			<a href="<?php the_permalink(); ?>">
			<?php echo $price?>
			
		<?php endif;
		
	}
}



if ( ! function_exists( 'themify_theme_before_shop_content' ) ) {
	/**
	 * Add initial portion of wrapper
	 */
	function themify_theme_before_shop_content() {
		?>

		<!-- layout -->
		<div id="layout-wrap">
		<div id="layout" class="pagewidth clearfix">

	        <?php themify_content_before(); //hook ?>
			<!-- content -->
			<div id="content">
				
				<?php if ( ! themify_check( 'setting-hide_shop_breadcrumbs' ) && ! is_product() ) { ?>

					<?php themify_breadcrumb_before(); ?>

					<?php woocommerce_breadcrumb(); ?>

					<?php themify_breadcrumb_after(); ?>

				<?php } ?>

				<?php themify_content_start(); //hook ?>

				<?php
	}
}

if(!function_exists('themify_theme_after_shop_content')) {
	/**
	 * Add end portion of wrapper
	 */
	function themify_theme_after_shop_content() {
				if (is_search() && is_post_type_archive() ) {
					add_filter( 'woo_pagination_args', 'woocommerceframework_add_search_fragment', 10 );
				} ?>
				<?php themify_content_end(); //hook ?>
			</div>
			<!-- /#content -->
			 <?php themify_content_after() //hook; ?>
			
			<?php
			global $themify;
			if ($themify->layout !== 'sidebar-none') get_sidebar();
		?>
		</div>
		<!-- /#layout -->
		</div>
		<!-- /#layout-wrap -->
		<?php
	}
}

///////////////////////////////////////////////////////////////////////
// Single product
///////////////////////////////////////////////////////////////////////


if ( ! function_exists( 'themify_template_single_breadcrumb' ) ) {
	function themify_template_single_breadcrumb() { 
		if ( ! ( themify_check( 'setting-hide_shop_breadcrumbs' ) 
			|| themify_check( 'setting-hide_shop_single_breadcrumbs' ) ) && is_product() ) {

			themify_breadcrumb_before();
			if ( ! themify_theme_is_product_lightbox() ) woocommerce_breadcrumb(); 
			themify_breadcrumb_after(); 
		}
	}
}
if ( ! function_exists( 'themify_template_single_wishlist' ) ) {
	
	function themify_template_single_wishlist(){
		remove_action( 'woocommerce_after_add_to_cart_button', 'themify_template_single_wishlist' );
		?>
			<div class="product-share-wrap">
				<?php Themify_Wishlist::button()?>
				<?php if (themify_hide_social_share()): ?>
					<?php get_template_part('includes/social-share', 'product'); ?>
				<?php endif; ?>
			</div>
		<?php
	}
}
if ( ! function_exists( 'themify_template_single_onsale' ) ) {
	
	function themify_template_single_onsale(){
		global $product,$post;
		echo $product->is_on_sale()?apply_filters('woocommerce_sale_flash', '<span class="onsale">'.__( 'Sale!', 'woocommerce' ).'</span>', $post, $product):'';
	}
}
if(!function_exists('themify_theme_wc_compatibility_sidebar')) {
	/**
	 * Add sidebar if it's enabled in theme settings
	 * @since 1.4.6
	 */
	function themify_theme_wc_compatibility_sidebar(){
		// Check if WC is active and this is a WC-managed page
		if( !themify_is_woocommerce_active() || !is_woocommerce() ) return;

		$sidebar_layout = 'sidebar-none';

		if( is_product() ) {
			if( themify_check('setting-single_product_layout') ) {
				$sidebar_layout = themify_get('setting-single_product_layout');
			} elseif( themify_check('setting-default_page_post_layout') ) {
				$sidebar_layout = themify_get('setting-default_page_post_layout');
			}
		} else {
			$key = is_shop()?'setting-shop_layout':'setting-shop_archive_layout';
			if( themify_check($key) ) {
				$sidebar_layout = themify_get($key);
			} elseif( themify_check('setting-default_layout') ) {
				$sidebar_layout = themify_get('setting-default_layout');
			}
		}

		themify_ecommerce_sidebar_before(); // Hook

		if ( $sidebar_layout != 'sidebar-none' ) {
			get_sidebar();
		}

		themify_ecommerce_sidebar_after(); // Hook
	}
}

////////////////////////////////////////////////////////////////////////
// Loop products
////////////////////////////////////////////////////////////////////////

/**
 * Outputs product short description or full content depending on the setting.
 */
function themify_after_shop_loop_item() {
	global $themify;
	// Product Short Description or Full Content /////////////////////////
	if ( '' != $themify->product_archive_show_short ) {
		$product_archive_show_short = $themify->product_archive_show_short;
	} elseif ( themify_check( 'setting-product_archive_show_short' ) ) {
		$product_archive_show_short = themify_get( 'setting-product_archive_show_short' );
	} else {
		$product_archive_show_short = '';
	}
	if ( $product_archive_show_short == 'short' ) {
		$product_archive_show_short = 'excerpt';
	}
	?>
		<div class="product-description">
			<?php
			if ( 'excerpt' == $product_archive_show_short ) {
				the_excerpt();
			} elseif ( 'content' == $product_archive_show_short ) {
				the_content();
			}
			?>
		</div>
	<?php
}

if ( ! function_exists( 'themify_theme_product_class' ) ) {
	function themify_theme_product_class( $classes, $class, $post_id ) {
		global $themify;
		if ( 'product' == get_post_type( $post_id ) ) {
			if ( 'list-post' == $themify->post_layout && ! is_product() ) {
				$classes[] = themify_get( 'product_image_layout' );
			}
			if ( themify_theme_is_product_lightbox() ) {
				$classes[] = 'image-left';
			}
			$classes[] = themify_product_fullcover( $post_id );
		}
		return $classes;
	}
}

if ( ! function_exists( 'themify_theme_product_description_wrap' ) ) {
	function themify_theme_product_description_wrap( $description ) {
		return '<div class="product-description">' . $description . '</div><!-- /.product-description -->';
	}
}

if (!function_exists('woocommerce_single_product_content_ajax')) {
	/**
	 * WooCommerce Single Product Content with AJAX
	 * @param object|bool $wc_query
	 */
	function woocommerce_single_product_content_ajax( $wc_query = false ) {

		// Override the query used
		if (!$wc_query) {
			global $wp_query;
			$wc_query = $wp_query;
		}

		if ( $wc_query->have_posts() ) while ( $wc_query->have_posts() ) : $wc_query->the_post(); ?>
			<div id="product_single_wrapper" class="product product-<?php the_ID(); ?> single product-single-ajax">
				<div class="product-imagewrap">
					<?php do_action('themify_single_product_image_ajax'); ?>
				</div>
				<div class="product-content product-single-entry">
					<h3 class="product-title"><?php the_title(); ?></h3>
					<div class="product-price">
						<?php do_action('themify_single_product_price'); ?>
					</div>
					<?php do_action('themify_single_product_ajax_content'); ?>
				</div>
			</div>
			<!-- /.product -->
		<?php endwhile;
	}
}

if(!function_exists('themify_product_image_ajax')){
	/**
	 * Filter image of product loaded in lightbox to remove link and wrap in figure.product-image. Implements filter themify_product_image_ajax for external usage
	 * @param string $html Original markup
	 * @param int $post_id Post ID
	 * @return string Image markup without link
	 */
	function themify_product_image_ajax($html, $post_id) {
		$image = get_the_post_thumbnail( $post_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );
		return apply_filters( 'themify_product_image_ajax', sprintf( '<figure class="product-image">%s<span class="loading-product"></span></figure>', $image ) );
	};
}

if(!function_exists('themify_product_image_single')){
	/**
	 * Filter image of product loaded in lightbox to remove link and wrap in figure.product-image. Implements filter themify_product_image_single for external usage
	 * @param string $html Original markup
	 * @param int $post_id Product ID
	 * @return string Image markup without link
	 */
	function themify_product_image_single( $html, $post_id ) {
		global $post;

		query_posts( 'p=' . $post_id . '&post_type=product' );
		$post = get_post( $post_id );
		setup_postdata( $post );
		$replace = themify_get_product_image(true);
		wp_reset_postdata();
		wp_reset_query();

		if(!empty($replace['width']) || !empty($replace['height'])){
			$size =  get_option('shop_single_image_size');
			$url = get_the_post_thumbnail_url( $post_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ));
			$replace['width'] = !empty($replace['width'])?'width="'.$replace['width'].'"':'';
			$replace['height'] = !empty($replace['height'])?'height="'.$replace['height'].'"':'';
			$html = str_replace(array($url,'width="'.$size['width'].'"','height="'.$size['height'].'"'), array($replace['html'],$replace['width'],$replace['height']), $html );
		}
		return apply_filters( 'themify_product_image_single', $html );
	};
}

if(!function_exists('themify_theme_product_image')){
	
	function themify_theme_product_image($url) {
		$replace = themify_get_product_image();
		echo $replace['html'];
	};
}

if(!function_exists('themify_loop_add_to_cart_link')) {
	/**
	 * Filter link to setup lightbox capabilities
	 * @param string $format Original markup
	 * @param object $product WC Product Object
	 * @param array $link Array of link parameters
	 * @return string Markup for link
	 */
	function themify_loop_add_to_cart_link( $format = '', $product = null ) {

		if ( function_exists( 'themify_is_touch' ) ) {
			$isPhone = themify_is_touch( 'phone' );
		} else {
			if ( ! class_exists( 'Themify_Mobile_Detect' ) ) {
				require_once THEMIFY_DIR . '/class-themify-mobile-detect.php';
			}
			$detect = new Themify_Mobile_Detect;
			$isPhone = $detect->isMobile() && !$detect->isTablet();
		}
		if( ( 'variable' == $product->get_type() || 'grouped' == $product->get_type() ) && !$isPhone
			&& !( themify_check( 'setting-disable_product_lightbox' ) ) ) {

			$url = add_query_arg( array('post_in_lightbox' => '1'), $product->add_to_cart_url() );
			$replacement = 'class="variable-link themify-lightbox '; // add space at the end
			$format = preg_replace( '/(class=")/', $replacement, $format, 1 );
			$format = preg_replace( '/href="(.*?)"/', 'href="'.$url.'"', $format, 1 );
		}
		if ( $product->is_purchasable() ) {
			$format = preg_replace( '/add_to_cart_button/', 'add_to_cart_button theme_add_to_cart_button', $format, 1 );
		}
		return $format;
	}
}

if(!function_exists('themify_product_description')){
	/**
	 * WooCommerce Single Product description
	 */
	function themify_product_description(){
		the_content();
	}
}

if ( ! function_exists( 'themify_theme_sale_flash' ) ) {
	function themify_theme_sale_flash( $html ) {
		return '<span class="onsale">'.__( 'Sale', 'themify' ).'</span>';
	}
}


