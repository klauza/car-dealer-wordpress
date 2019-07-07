<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Template Products
 * 
 * Access original fields: $mod_settings
 */
global $paged;
if (TFCache::start_cache($mod_name, self::$post_id, array('ID' => $module_ID))):
$fields_default = array(
	'mod_title_products' => '',
	'query_products' => 'all',
	'template_products' => 'list',
	'hide_free_products' => 'no',
	'hide_outofstock_products' => 'no',
	'layout_products' => 'grid3',
	'category_products' => '',
	'hide_child_products'=>false,
	'post_per_page_products' => 6,
	'offset_products' => 0,
	'order_products' => 'ASC',
	'orderby_products' => 'title',
	'description_products' => 'none',
	'hide_feat_img_products' => 'no',
	'image_size_products' => '',
	'img_width_products' => '',
	'img_height_products' => '',
	'unlink_feat_img_products' => 'no',
	'hide_post_title_products' => 'no',
	'unlink_post_title_products' => 'no',
	'hide_price_products' => 'no',
	'hide_add_to_cart_products' => 'no',
	'hide_rating_products' => 'no',
	'hide_sales_badge' => 'no',
	// slider settings
	'layout_slider' => '',
	'visible_opt_slider' => '',
	'mob_visible_opt_slider' => '',
	'auto_scroll_opt_slider' => 0,
	'scroll_opt_slider' => '',
	'speed_opt_slider' => '',
	'effect_slider' => 'scroll',
	'pause_on_hover_slider' => 'resume',
	'pagination'=>'yes',
	'wrap_slider' => 'yes',
	'show_nav_slider' => 'yes',
	'show_arrow_slider' => 'yes',
	'left_margin_slider' => '',
	'right_margin_slider' => '',
	'height_slider' => 'variable',
	'hide_page_nav_products' => 'yes',
	'animation_effect' => '',
	'css_products' => '',
);

if ( isset( $mod_settings['category_products'] ) ){	
	$mod_settings['category_products'] = self::get_param_value( $mod_settings['category_products'] );
}
$fields_args = wp_parse_args( $mod_settings, $fields_default );
unset($mod_settings);
$temp_terms = explode( ',', $fields_args['category_products'] );
$terms = array();
$terms_exclude = array();
$is_string = false;
foreach ( $temp_terms as $t ) {
	$is_string = ! is_numeric( $t );
	$t = trim( $t );

	if ( '' !== $t ) {
		if( ! $is_string && $t < 0 ) {
			$terms_exclude[] = abs( $t );
		} else {
			$terms[] = $t;
		}
	}
}
$tax_field = $is_string ? 'slug' : 'id';

$query_args = array(
	'post_type' => 'product',
	'posts_per_page' => $fields_args['post_per_page_products'],
	'order' => $fields_args['order_products'],
);
$paged = self::get_paged_query();
$query_args['offset'] = ( ( $paged - 1 ) * $fields_args['post_per_page_products'] ) + $fields_args['offset_products'];

$query_args['meta_query'][] = WC()->query->stock_status_meta_query();
$query_args['meta_query']   = array_filter( $query_args['meta_query'] );

if( ! empty( $terms_exclude ) ) {
	$query_args['tax_query'] = array(
		array(
			'taxonomy' => 'product_cat',
			'field' => $tax_field,
			'terms' => $terms_exclude,
			'include_children' => $fields_args['hide_child_products'] !=='yes',
			'operator' => 'NOT IN'
		)
	);

} else if( ! empty( $terms ) && ! in_array( '0', $terms ) ) {
	$query_args['tax_query'] = array(
		array(
			'taxonomy' => 'product_cat',
			'field' => $tax_field,
			'terms' => $terms,
			'include_children'=> $fields_args['hide_child_products'] !=='yes'
		)
	);
}

if( $fields_args['query_products'] === 'onsale' ) {
	$product_ids_on_sale = wc_get_product_ids_on_sale();
	$product_ids_on_sale[] = 0;
	$query_args['post__in'] = $product_ids_on_sale;
} elseif( $fields_args['query_products'] === 'featured' ) {
	if( version_compare( WOOCOMMERCE_VERSION, '3.0.0', '>=' ) ) {
		$query_args['tax_query'][] = array(
			'taxonomy'	=> 'product_visibility',
			'field'		=> 'name',
			'terms'		=> 'featured',
			'operator'	=> 'IN'
		);
	} else {
		$query_args['meta_query'][] = array(
			'key'	=> '_featured',
			'value' => 'yes'
		);
	}
}

switch ( $fields_args['orderby_products'] ) {
	case 'price' :
		$query_args['meta_key'] = '_price';
		$query_args['orderby']  = 'meta_value_num';
		break;
	case 'sales' :
		$query_args['meta_key'] = 'total_sales';
		$query_args['orderby']  = 'meta_value_num';
		break;
	default :
		$query_args['orderby']  = $fields_args['orderby_products'];
}

if ( $fields_args['hide_free_products'] === 'yes' ) {
	$query_args['meta_query'][] = array(
		'key'     => '_price',
		'value'   => 0,
		'compare' => '>',
		'type'    => 'DECIMAL',
	);
}
if( $fields_args['hide_outofstock_products'] === 'yes' ) {
 	if( version_compare( WOOCOMMERCE_VERSION, '3.0.0', '>=' ) ) {
 		$query_args['tax_query'][] = array(
			'taxonomy'	=> 'product_visibility',
			'field'		=> 'name',
			'terms'		=> array( 'exclude-from-catalog', 'outofstock' ),
			'operator'	=> 'NOT IN'
		);
 	} else {
 		$query_args['meta_query'][] = array(
 			'key'     => '_stock_status',
 			'value'   => 'outofstock',
 			'compare' => 'NOT IN'
 		);
 	}
 }
$is_theme_template = false;
if( $fields_args['template_products'] === 'list' && Themify_Builder_Model::is_loop_template_exist( 'query-product.php', 'includes' ) ) {
	$theme_layouts = apply_filters( 'builder_woocommerce_theme_layouts', array() );
        // check if the chosen layout is supported by the theme
        $is_theme_template = in_array( $fields_args['layout_products'], $theme_layouts,true );
}

if( $is_theme_template ) {
	global $themify;
	$themify_save = clone $themify;

	// $themify->page_navigation = $hide_page_nav_products;
	$themify->page_navigation = $fields_args['hide_page_nav_products']; // hide navigation links
	$themify->query_products = $query_args;
	$themify->post_layout = $fields_args['layout_products'];
	$themify->product_archive_show_short = $fields_args['description_products'];
	$themify->unlink_product_title = $fields_args['unlink_post_title_products'];
	$themify->hide_product_title = $fields_args['hide_post_title_products'];
	$themify->hide_product_image = $fields_args['hide_feat_img_products'];
	$themify->unlink_product_image = $fields_args['unlink_feat_img_products'];
	$themify->width = $fields_args['img_width_products'];
	$themify->height = $fields_args['img_height_products'];
	if (Themify_Builder_Model::is_img_php_disabled() && $fields_args['image_size_products'] !== ''){
            $themify->image_setting .= 'image_size=' . $fields_args['image_size_products'] . '&';
	}
	

	if( 'yes' === $fields_args['hide_add_to_cart_products'] ) {
		add_filter( 'woocommerce_loop_add_to_cart_link', '__return_empty_string' );
	}
	if( 'yes' ===$fields_args['hide_rating_products']  ) {
		add_filter( 'option_woocommerce_enable_review_rating', 'builder_woocommerce_return_no' );
	} else {
		// enable ratings despite the option configured in WooCommerce > Settings
		add_filter( 'option_woocommerce_enable_review_rating', 'builder_woocommerce_return_yes' );
	}
	if( 'yes' === $fields_args['hide_sales_badge'] ) {
		add_filter( 'woocommerce_sale_flash', '__return_empty_string' );
	}
	if( 'yes' ===$fields_args['hide_price_products']  ) {
		add_filter( 'woocommerce_get_price_html', '__return_empty_string' );
	}
	$animation_effect = self::parse_animation_effect( $fields_args['animation_effect'], $fields_args );
	$container_class = implode(' ', 
		apply_filters( 'themify_builder_module_classes', array(
			'module', 'module-' . $mod_name, $module_ID, $fields_args['css_products']
		), $mod_name, $module_ID, $fields_args )
	);

	$container_props = apply_filters( 'themify_builder_module_container_props', array(
		'id' => $module_ID,
		'class' => $container_class
	), $fields_args, $mod_name, $module_ID );
	if($animation_effect!==''){
            self::add_post_class( $animation_effect );
        }
	?>
	<div <?php echo self::get_element_attributes( $container_props ); ?>>
            <!--insert-->
		<?php if ( $fields_args['mod_title_products'] !== '' ): ?>
			<?php echo $fields_args['before_title'] . apply_filters( 'themify_builder_module_title', $fields_args['mod_title_products'], $fields_args )  . $fields_args['after_title']; ?>
		<?php endif; ?>

		<?php do_action( 'themify_builder_before_template_content_render' ); ?>

		<?php get_template_part( 'includes/query-product' ); ?>
		
		<?php do_action( 'themify_builder_after_template_content_render' ); ?>
	</div>
	<?php
	// reset config
	$themify = clone $themify_save;

	remove_filter( 'woocommerce_loop_add_to_cart_link', '__return_empty_string' );
	remove_filter( 'option_woocommerce_enable_review_rating', 'builder_woocommerce_return_no' );
	remove_filter( 'option_woocommerce_enable_review_rating', 'builder_woocommerce_return_yes' );
	remove_filter( 'woocommerce_sale_flash', '__return_empty_string' );
	remove_filter( 'woocommerce_get_price_html', '__return_empty_string' );

} else {
	// render the template
	self::retrieve_template( 'template-'.$mod_name.'-'.$fields_args['template_products'].'.php', array(
		'module_ID' => $module_ID,
		'mod_name' => $mod_name,
		'query_args' => $query_args,
		'settings' => $fields_args
	), '', '', true );
}
 endif; ?>
<?php TFCache::end_cache(); ?>