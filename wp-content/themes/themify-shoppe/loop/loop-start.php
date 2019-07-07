<?php
/**
 * Product Loop Start
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/loop-start.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.3.0
 */
 global $woocommerce_loop,$themify;
 $width = $height = '';
 $is_related = !empty($woocommerce_loop['name']) && $woocommerce_loop['name']==='related';
 if(!empty($themify->builder_args['is_product']) && ($themify->width || $themify->height)){
	 $width = $themify->width;
	 $height = $themify->height;
 }
 elseif($is_related){
	$width = themify_get( 'setting-product_related_image_width' );
	$height = themify_get( 'setting-product_related_image_height' );
 }
?>
<ul <?php echo  $width || $height?'data-width="'.$width.'" data-height="'.$height.'"':'';?> class="products loops-wrapper<?php echo !$is_related?' '.esc_attr( themify_theme_query_classes() ):''; ?>">
