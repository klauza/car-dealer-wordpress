<?php 
WC_Frontend_Scripts::load_scripts();
global $themify,$wp_query;
$query = !empty($themify->query_products )?new WP_Query( $themify->query_products ):$wp_query;
?>
<?php if ( $query->have_posts() ) : ?>
	<?php $themify->query = $query;?>
	<?php do_action('woocommerce_before_shop_loop'); ?>
	<?php if($query->found_posts>1 && themify_get( 'product_show_sorting_bar' ) == 'yes' ):?>
		<?php get_template_part( 'loop/order');?>
	<?php endif; ?>
	<?php woocommerce_product_loop_start(); ?>

		<?php woocommerce_product_subcategories(); ?>

		<?php while ( $query->have_posts() ) : $query->the_post(); ?>

			<?php wc_get_template_part( 'content', 'product' ); ?>

		<?php endwhile; // end of the loop. ?>

	<?php woocommerce_product_loop_end(); ?>

	<?php do_action('woocommerce_after_shop_loop'); ?>
	<?php if ($themify->page_navigation != 'yes' && $query->found_posts>1): ?>
		<?php get_template_part( 'includes/pagination'); ?>
	<?php endif; ?>
<?php else: ?>

	<?php wc_get_template( 'loop/no-products-found.php' ); ?>

<?php endif;
if(!empty($themify->query_products )){
    wp_reset_postdata();
}