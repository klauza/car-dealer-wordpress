<?php
/**
 * Products loop query
 * @since 1.0.0
 */

$pq = 'setting-product_query_';

$product_category = themify_check($pq.'category')? themify_get($pq.'category') : '0';

$args = array(
	'post_type' => 'product',
	'posts_per_page' => -1,
	'post_status' => 'publish',
);
if( '0' == $product_category || !$product_category ){
	$args['meta_query'] = array(
		array(
			'key' => '_featured',
			'value' => 'yes',
		)
	);
} elseif($product_category && '0' != $product_category ){
	$args['tax_query'] = array(
		array(
			'taxonomy' => 'product_cat',
			'field' => 'id',
			'terms' => array( $product_category )
		)
	);
}
$loop = new WP_Query( $args );

if ($loop->have_posts()) : ?>

	<?php while ($loop->have_posts()) : $loop->the_post(); ?>

		<div class="product product-<?php the_ID(); ?>">
			<div class="product-imagewrap">
				<?php
				if(class_exists('WC_Product_Factory')){
					$wcpc = new WC_Product_Factory();
					$_product = $wcpc->get_product( $loop->post->ID );
				} else {
					$_product = &new WC_Product( $loop->post->ID );
				}
				woocommerce_show_product_sale_flash(); ?>

				<figure class="product-image">
					<?php themify_product_slider_image_start(); //hook ?>
					<a href="<?php the_permalink(); ?>">
						<?php if (has_post_thumbnail($loop->post->ID)): ?>
								<?php echo get_the_post_thumbnail( $loop->post->ID, 'shop_catalog' ); ?>
						<?php else: ?>
							<img src="http://placehold.it/200x160">
						<?php endif; ?>
					</a><span class="loading-product"></span>
					<?php themify_product_slider_image_end(); //hook ?>
				</figure>

			</div>
			<?php if(themify_get('setting-product_slider_hide_title') != 'yes'): ?>
			<h3 class="product-title">
				 <?php themify_product_slider_title_start(); //hook ?>
				<a href="<?php the_permalink(); ?>">
					<?php the_title(); ?>
				</a>
				 <?php themify_product_slider_title_end(); //hook ?>
			</h3>
			<?php endif; ?>

			<?php
			if( themify_get('setting-product_slider_hide_price' ) != 'yes' && is_object( $_product ) ) :
				if ( $price_html = $_product->get_price_html() ) : ?>
					<p class="price">
						<?php themify_product_slider_price_start(); //hook ?>
						<?php echo $price_html; ?>
						<?php themify_product_slider_price_end(); //hook ?>
					</p>
				<?php
				endif; // $price_html
			endif; // product slider hide price and is_object $_product?>

			<?php themify_product_slider_add_to_cart_before(); //hook  ?>
			<?php woocommerce_template_loop_add_to_cart( $loop->post, $_product ); ?>
			<?php themify_product_slider_add_to_cart_after(); //hook  ?>

			<?php edit_post_link(__('Edit', 'themify'), '<span class="edit-button">[', ']</span>'); ?>
		</div>

	<?php endwhile; ?>

<?php endif; // have posts ?>