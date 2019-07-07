<?php ! defined( 'ABSPATH' ) && exit;
/**
 * Template Gallery Slider
 * 
 * Access original fields: $settings
 * @author Themify
 */

if( $settings['layout_gallery'] === 'slider' ) :
	$is_img_disabled = Themify_Builder_Model::is_img_php_disabled();

	$margins = '';

	if( $settings['left_margin_slider'] !== '' ) {
		$margins .= 'margin-left:' . $settings['left_margin_slider'] . 'px;';
	}

	if ($settings['right_margin_slider'] !== '') {
		$margins .= 'margin-right:' . $settings['right_margin_slider'] . 'px;';
	}

	foreach( array( 'slider', 'thumbs' ) as $mode ) :
		$is_slider = $mode === 'slider';
?>

<ul id="<?php echo $module_ID . '-' . $mode; ?>" class="themify_builder_slider"
	data-id="<?php echo $module_ID; ?>" 
	data-visible="<?php echo ! $is_slider ? $settings['visible_opt_slider'] : 1; ?>" 
	data-mob-visible="<?php echo ! $is_slider ? $settings['mob_visible_opt_slider'] : 1;?>"
	data-scroll="1" 
	data-auto-scroll="<?php echo $is_slider ? $settings['auto_scroll_opt_slider'] : ''; ?>"
	data-speed="<?php echo $settings['speed_opt_slider'] === 'slow' ? 4 : ($settings['speed_opt_slider'] === 'fast' ? '.5' : 1) ?>"
	data-wrap="<?php echo $settings['wrap_slider']; ?>"
	data-arrow="<?php echo $mode === ( $settings['show_arrow_buttons_vertical'] ? 'slider' : 'thumbs' ) ? $settings['show_arrow_slider'] : ''; ?>"
	data-pagination="0"
	data-effect="<?php echo $is_slider ? $settings['effect_slider'] : 'scroll'; ?>" 
	data-height="variable"
	<?php $is_slider && printf( 'data-sync="#%s-thumbs"', $module_ID ); ?>
	data-pause-on-hover="<?php echo $settings['pause_on_hover_slider'] ?>">

<?php foreach( $settings['gallery_images'] as $image ) : ?>
	<li><div class="slide-inner-wrap"<?php $margins && printf( ' style="%s"', $margins ) ?>>
		<div class="slide-image gallery-icon"><?php

			$image_html = $is_img_disabled 
				? wp_get_attachment_image( $image->ID, 'full' )
				: themify_get_image( array(
					'w' => ! $is_slider ? $settings['thumb_w_gallery'] : $settings['s_image_w_gallery'],
					'h' => ! $is_slider ? $settings['thumb_h_gallery'] : $settings['s_image_h_gallery'],
					'ignore' => true,
					'alt' => get_post_meta( $image->ID, '_wp_attachment_image_alt', true ),
					'src' => wp_get_attachment_image_url( $image->ID, 'full' )
				) );

			$lightbox = '';

			if( $settings['link_opt'] === 'file' ) {
				$link = wp_get_attachment_image_src( $image->ID, $settings['link_image_size'] );
				$link = $link[0];
				$lightbox = ' class="themify_lightbox"';
			} elseif( 'none' === $settings['link_opt'] ) {
				$link = '';
			} else {
				$link = get_attachment_link( $image->ID );
			}

			if( ! empty( $link ) && $is_slider ) {
				printf( '<a href="%s"%s>%s</a>'
					, esc_url( $link ), $lightbox, $image_html );
			} else {
				echo $image_html;
			}
		?>
		</div>

		<?php if( ( $settings['gallery_image_title'] && $image->post_title || ! $settings['gallery_exclude_caption'] && $image->post_excerpt ) && $is_slider ) : ?>
			<div class="slide-content">
				<?php 
					$settings['gallery_image_title'] && ! empty( $image->post_title )
						&& printf( '<h3 class="slide-title">%s</h3>', wp_kses_post( $image->post_title ) );

					! $settings['gallery_exclude_caption'] && ! empty( $image->post_excerpt )
						&& printf( '<p>%s</p>', apply_filters( 'themify_builder_module_content', $image->post_excerpt ) );
				?>
			</div><!-- /slide-content -->
		<?php endif; ?>
	</div></li>
<?php endforeach; ?>
</ul>
<?php endforeach; ?>
<?php endif; ?>