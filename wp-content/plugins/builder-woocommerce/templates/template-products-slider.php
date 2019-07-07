<?php
/**
 * @var $query_args the query parameters set by the module
 * @var $settings module config
 */
$container_class = implode(' ', 
	apply_filters( 'themify_builder_module_classes', array(
		'module', 'module-' . $mod_name, 'module-slider', $module_ID, 'themify_builder_slider_wrap', 'clearfix', $settings['layout_slider'],$settings['css_products'] , $settings['animation_effect'] 
	), $mod_name, $module_ID, $settings )
);

$slide_margins = array();
$slide_margins[] = !empty($settings['left_margin_slider']  ) ? sprintf( 'margin-left:%spx;', $settings['left_margin_slider']  ) : '';
$slide_margins[] = !empty( $settings['right_margin_slider'] ) ? sprintf( 'margin-right:%spx;', $settings['right_margin_slider']  ) : '';
if(!empty($slide_margins)){
    $slide_margins = ' style="'.implode('',$slide_margins).'"';
}
$speed = $settings['speed_opt_slider']==='slow'?4:($settings['speed_opt_slider']==='fast'?'.5':1);
?>

<!-- module products slider -->
<div id="<?php echo $module_ID; ?>-loader" class="tb_slider_loader" style="<?php echo !empty($settings['img_height_products']) ? 'height:'.$settings['img_height_products'].'px;' : 'height:50px;'; ?>"></div>
<div id="<?php echo $module_ID; ?>" class="<?php echo esc_attr( $container_class ); ?>">
	<div class="woocommerce">

		<?php if ( $settings['mod_title_products'] !== '' ): ?>
			<?php echo $settings['before_title'] . apply_filters( 'themify_builder_module_title', $settings['mod_title_products'] , $settings )  . $settings['after_title']; ?>
		<?php endif; ?>

		<ul class="themify_builder_slider" 
			data-id="<?php echo $module_ID; ?>" 
			data-visible="<?php echo $settings['visible_opt_slider']; ?>"
			data-mob-visible="<?php echo $settings['mob_visible_opt_slider'] ?>"
			data-scroll="<?php echo $settings['scroll_opt_slider']; ?>" 
			data-auto-scroll="<?php echo $settings['auto_scroll_opt_slider']; ?>"
			data-speed="<?php echo $speed; ?>"
			data-wrapper="<?php echo $settings['wrap_slider']; ?>"
			data-arrow="<?php echo $settings['show_arrow_slider'] ; ?>"
			data-pagination="<?php echo $settings['pagination']; ?>"
			data-effect="<?php echo $settings['effect_slider']; ?>" 
			data-height="<?php echo $settings['height_slider'] ?>"
			data-pause-on-hover="<?php echo $settings['pause_on_hover_slider']; ?>" >

		<?php do_action( 'themify_builder_before_template_content_render' ); ?>

		<?php
		global $post;
		$builder_wc_temp_post = $post;
                $param_image = 'w='.$settings['img_width_products'] .'&h='.$settings['img_height_products'].'&ignore=true';
                if( Themify_Builder_Model::is_img_php_disabled() && $settings['image_size_products']!=='' ) {
                    $param_image .= '&image_size=' . $settings['image_size_products'];
                }
							
		$query = new WP_Query( $query_args );
		if( $query->have_posts() ) : while( $query->have_posts() ) : $query->the_post(); ?>

			<li>
				<div class="slide-inner-wrap"<?php echo $slide_margins; ?>>
					<?php
						if( $settings['hide_feat_img_products'] !== 'yes' && $post_image = themify_get_image( $param_image ) ) { ?>
							<figure class="slide-image">
								<?php if( $settings['unlink_feat_img_products'] === 'yes'): ?>
									<?php echo $post_image; ?>
								<?php else: ?>
									<a href="<?php the_permalink(); ?>" title="<?php echo the_title_attribute('echo=0'); ?>"><?php echo $post_image; ?></a>
								<?php endif; ?>
							</figure>
						<?php } // product image ?>

					<div class="slide-content">

						<?php if( $settings['hide_sales_badge'] !== 'yes' ) : ?>
							<?php woocommerce_show_product_loop_sale_flash(); ?>
						<?php endif; ?>

						<?php if( $settings['hide_post_title_products'] !== 'yes' ): ?>
							<?php if( $settings['unlink_post_title_products'] === 'yes' ): ?>
								<h3><?php the_title(); ?></h3>
							<?php else: ?>
								<h3><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
							<?php endif; //unlink post title ?>
						<?php endif; // hide post title ?>

						<?php
						if( $settings['hide_rating_products'] !== 'yes' ) {
							woocommerce_template_loop_rating();
						} // product rating

						if( $settings['hide_price_products'] !== 'yes' ) {
							woocommerce_template_loop_price();
						} // product price

						if( $settings['hide_add_to_cart_products'] !== 'yes' ) {
							echo '<p class="add-to-cart-button">';
							woocommerce_template_loop_add_to_cart();
							echo '</p>';
						} // product add to cart
						?>

						<?php if($settings['description_products']  === 'short' ) {
							woocommerce_template_single_excerpt();
						} elseif( $settings['description_products']  === 'full' ) {
							the_content();
						}
						?>
					</div><!-- /slide-content -->
				</div>
			</li>

		<?php endwhile; wp_reset_postdata(); $post = $builder_wc_temp_post; unset( $builder_wc_temp_post ); ?>
		<?php endif; ?>

		<?php do_action( 'themify_builder_after_template_content_render' ); ?>

		</ul>
	</div><!-- .woocommerce -->
</div>
<!-- /module products slider -->