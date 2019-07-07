<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.4.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $woocommerce_loop, $themify;

// Store loop count we're currently on
if (empty($woocommerce_loop['loop'])) {
    $woocommerce_loop['loop'] = 0;
}

// Store column count for displaying the grid
if (empty($woocommerce_loop['columns'])) {
    $woocommerce_loop['columns'] = apply_filters('loop_shop_columns', 4);
}

// Ensure visibility
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}


////////////////////////////////////
// Themify Specific
////////////////////////////////////

$full_width = '';
if ('sidebar-none' == $themify->layout) {
    $full_width = 'pagewidth';
}
if ('list-post' != $themify->post_layout) {
    $full_width = '';
}
?>
<li data-product-id="<?php the_ID(); ?>" <?php post_class(array('post')); ?>>
    <?php do_action('woocommerce_before_shop_loop_item'); ?>
    <?php if ('yes' != $themify->hide_product_image) : ?>
        <?php
        /**
         * woocommerce_before_shop_loop_item_title hook
         *
         * @hooked woocommerce_show_product_loop_sale_flash - 10
         * @hooked woocommerce_template_loop_product_thumbnail - 10
         */
        do_action('woocommerce_before_shop_loop_item_title');
        ?>
    <?php endif; ?>
    <div class="product-content">
        <div class="product-content-inner-wrapper">
            <div class="product-content-inner">
                <?php
                /**
                 * woocommerce_shop_loop_item_title hook
                 *
                 * @hooked woocommerce_template_loop_product_title - 10
                 */
                do_action('woocommerce_shop_loop_item_title');

                /**
                 * woocommerce_after_shop_loop_item hook
                 *
                 * @hooked woocommerce_template_loop_add_to_cart - 10
                 */
                do_action('woocommerce_after_shop_loop_item');
                ?>
                <div class="product-share-wrap">
                    <?php Themify_Wishlist::button() ?>
                    <?php if (themify_hide_quick_look()): ?>
                        <a onclick="return false;" data-image="<?php echo wc_placeholder_img_src() ?>" class="quick-look themify-lightbox" href="<?php echo add_query_arg(array('post_in_lightbox' => '1'), get_permalink()) ?>"><span class="tooltip"><?php _e('Quick Look', 'themify'); ?></span></a>
                    <?php endif; ?>
                    <?php if (themify_hide_social_share()): ?>
                        <?php get_template_part('includes/social-share', 'product'); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- /.summary -->
</li>