<?php
/**
 * Template to display products in Themify cart
 * @package themify
 * @since 1.0.0
 */

global $woocommerce;
$carts = array_reverse( $woocommerce->cart->get_cart() );

foreach ( $carts as $cart_item_key => $cart_item ) :
	// Add support for MNM plugin
	if( isset( $cart_item['mnm_container'] ) ) continue;

	$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

	if ( $_product->exists() && $cart_item['quantity'] > 0 ): 
		$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->get_permalink( $cart_item ), $cart_item, $cart_item_key ); ?>

		<div class="product">

			<a href="<?php echo esc_url( version_compare( WOOCOMMERCE_VERSION, '3.3.0', '>=' )
					? wc_get_cart_remove_url( $cart_item_key ) : $woocommerce->cart->get_remove_url( $cart_item_key ) ); ?>" data-product-key="<?php echo $cart_item_key; ?>" class="remove-item remove-item-js">
				<i class="icon-flatshop-close"></i>
			</a>

			<figure class="product-image">
				<?php themify_product_cart_image_start(); // hook ?>
				<a href="<?php echo esc_url( $product_permalink ); ?>">
					<?php
						$product_thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
						if ( ! empty( $product_thumbnail ) ) {
							echo $product_thumbnail;
						} else {
							?>
							<img src="http://placehold.it/40x40">
							<?php
						}
					?>
				</a>
				<?php themify_product_cart_image_end(); // hook ?>
			</figure>

			<div class="product-details">
				<h3 class="product-title">
					<a href="<?php echo esc_url( $product_permalink );?>">
						<?php echo apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ); ?>
					</a>
				</h3>
				<p class="quantity-count"><?php printf(__('x %d', 'themify'), $cart_item['quantity']); ?></p>
			</div>

		</div>
		<!--/product -->

	<?php endif; ?>

<?php endforeach; ?>