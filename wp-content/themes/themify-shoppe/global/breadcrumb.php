<?php
/**
 * Shop breadcrumb
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/global/breadcrumb.php.
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
 * @version     2.3.0
 * @see         woocommerce_breadcrumb()
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! empty( $breadcrumb ) ) {

	echo $wrap_before;
	$title = is_singular()?get_the_title():false;
	$shop = !is_shop()?wc_get_page_id( 'shop' ):false;

	$crumbs = array();
	foreach($breadcrumb as $k=>$b){
		if($shop && $k===1){
			$crumbs[get_the_permalink($shop)] = get_the_title($shop);
			$crumbs[$b[1]] = $b[0];
		}
		elseif($title!==$b[0]){
			$crumbs[$b[1]] = $b[0];
		}
		
	}
	$i=0;
	foreach ( $crumbs as $key => $crumb ) {
                
		echo $before;

		echo !empty( $key )?'<a href="' . esc_url( $key ) . '">' . esc_html( $crumb ) . '</a>':esc_html( $crumb);

		echo $after;

		if ($shop || (sizeof( $breadcrumb ) !== ($i + 1))) {
			echo $delimiter;
		}
                ++$i;

	}

	echo $wrap_after;

}
