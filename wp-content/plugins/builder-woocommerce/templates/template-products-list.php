<?php
/**
 * @var $query_args the query parameters set by the module
 * @var $settings module config
 */

$animation_effect = self::parse_animation_effect( $settings['animation_effect'], $settings );

$container_class = implode(' ', 
	apply_filters( 'themify_builder_module_classes', array(
		'module', 'module-' . $mod_name, $module_ID,$settings['css_products'] 
	), $mod_name, $module_ID, $settings )
);

$container_props = apply_filters( 'themify_builder_module_container_props', array(
	'id' => $module_ID,
	'class' => $container_class
), $settings, $mod_name, $module_ID );

if($animation_effect!==''){
    self::add_post_class( $animation_effect );
}
?>

<!-- module products -->
<div <?php echo self::get_element_attributes( $container_props ); ?>>
	<div class="woocommerce">

		<?php if ( $settings['mod_title_products'] !== '' ): ?>
			<?php echo $settings['before_title'] . apply_filters( 'themify_builder_module_title', $settings['mod_title_products'], $settings ) . $settings['after_title']; ?>
		<?php endif; ?>

		<?php do_action( 'themify_builder_before_template_content_render' ); ?>

		<?php
		global $post;
		$builder_wc_temp_post = $post;
		$query = new WP_Query( $query_args );
		if( $query->have_posts() ) : ?>
			<?php
				$param_image = 'w='.$settings['img_width_products'] .'&h='.$settings['img_height_products'].'&ignore=true';
				if( Themify_Builder_Model::is_img_php_disabled() && $settings['image_size_products']!=='' ) {
					$param_image .= '&image_size=' . $settings['image_size_products'];
				}
			?>
			<div class="wc-products <?php echo $settings['layout_products']; ?>">

			<?php while( $query->have_posts() ) : $query->the_post(); ?>

				<div id="product-<?php the_ID(); ?>" <?php post_class('post product clearfix'); ?>>

					<?php if ( $settings['hide_feat_img_products'] !== 'yes'  && ($post_image = themify_get_image( $param_image )) ):?>
						<figure class="post-image">
							<?php if ( $settings['unlink_feat_img_products']  === 'yes' ): ?>
									<?php echo $post_image; ?>
							<?php else: ?>
									<a href="<?php echo the_permalink(); ?>"><?php echo $post_image; ?></a>
							<?php endif; ?>
						</figure>
					<?php endif;?>

					<div class="post-content">

						<?php if($settings['hide_sales_badge']!== 'yes' ) : ?>
							<?php woocommerce_show_product_loop_sale_flash(); ?>
						<?php endif; ?>

						<?php if ($settings['hide_post_title_products']  !== 'yes' ) : ?>
							<?php if ($settings['unlink_post_title_products']  === 'yes' ) : ?>
								<h3><?php the_title(); ?></h3>
							<?php else: ?>
								<h3><a href="<?php echo the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
							<?php endif; //unlink product title ?>
						<?php endif; //product title ?>    

						<?php
						if( $settings['hide_rating_products'] !== 'yes' ) {
							woocommerce_template_loop_rating();
						} // product rating

						if( $settings['hide_price_products'] !== 'yes' ) {
							woocommerce_template_loop_price();
						} // product price

						
						if( $settings['description_products'] === 'short' ) {
								woocommerce_template_single_excerpt();
						} elseif('none' !== $settings['description_products'] ) {
								the_content();
						}
						 // product description

						if( $settings['hide_add_to_cart_products'] !== 'yes' ) {
							echo '<p class="add-to-cart-button">';
							woocommerce_template_loop_add_to_cart();
							echo '</p>';
						} // product add to cart
						?>

						<?php edit_post_link(__('Edit', 'themify'), '<span class="edit-button">[', ']</span>'); ?>

					</div><!-- /.post-content -->
					
				</div><!-- product-<?php the_ID(); ?> -->

			<?php endwhile; wp_reset_postdata(); $post = $builder_wc_temp_post; unset( $builder_wc_temp_post ); ?>

			</div>

		<?php endif; ?>

		<?php if( 'no' === $settings['hide_page_nav_products']  ) {
			echo self::get_pagenav( '', '', $query, $settings['offset_products'] );
		} ?>

		<?php do_action( 'themify_builder_after_template_content_render' );
		if($animation_effect!==''){
			self::remove_post_class( $animation_effect );
		}
		?>
	</div><!-- .woocommerce -->
</div>
<!-- /module products -->