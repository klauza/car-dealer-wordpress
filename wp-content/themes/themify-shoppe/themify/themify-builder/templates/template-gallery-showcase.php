<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Template Gallery Showcase
 * 
 * Access original fields: $mod_settings
 * @author Themify
 */

$first_image = '';
$disable = Themify_Builder_Model::is_img_php_disabled();
if ( is_object( $settings['gallery_images'][0] ) ) :
	$alt = get_post_meta($settings['gallery_images'][0]->ID, '_wp_attachment_image_alt', true);
	$caption = $settings['gallery_images'][0]->post_excerpt;
	$title = $settings['gallery_images'][0]->post_title;
	
	if ( $disable ) {
		$first_image = wp_get_attachment_image_src($settings['gallery_images'][0]->ID, $settings['s_image_size_gallery']);
		$first_image = $first_image[0];
	} else {
		$first_image = themify_do_img($settings['gallery_images'][0]->ID, $settings['s_image_w_gallery'], $settings['s_image_h_gallery']);
		$first_image = $first_image['url'];
	} ?>

	<div class="gallery-showcase-image">
		<div class="image-wrapper">
			<img src="<?php echo esc_url($first_image); ?>" alt="<?php echo esc_attr($alt) ?>" />
			<?php if( ! empty( $settings['gallery_image_title'] ) || ( $settings['gallery_exclude_caption'] !== 'yes' ) ) : ?>
                <div class="gallery-showcase-title">
					<?php
					! empty( $settings['gallery_image_title'] )
					&& printf( '<strong class="gallery-showcase-title-text">%s</strong>'
								, esc_attr( $title ) );
					
						$settings['gallery_exclude_caption'] !== 'yes'
							&& printf( '<span class="gallery-showcase-caption">%s</span>'
								, esc_attr( $caption ) );
					?>
				</div>
			<?php endif; ?>
		</div>

    </div>
    <div class="gallery-images">
        <?php
        foreach ($settings['gallery_images'] as $image) :
            $alt = get_post_meta($image->ID, '_wp_attachment_image_alt', true);
            $title = $image->post_title;
            $caption = $image->post_excerpt;
            if ($disable) {
                $img = wp_get_attachment_image($image->ID, $settings['image_size_gallery']);
                $link = wp_get_attachment_image_src($image->ID, $settings['s_image_size_gallery']);
                $link = $link[0];
            } else {
                if ($settings['thumb_w_gallery'] !== '') {
                    $img = themify_do_img($image->ID, $settings['thumb_w_gallery'], $settings['thumb_h_gallery']);
                    $img = "<img src='{$img['url']}' width='{$img['width']}' height='{$img['height']}' alt='{$alt}' />";
                } else {
                    $img = wp_get_attachment_image($image->ID, $settings['image_size_gallery']);
                }
                $link = themify_do_img($image->ID, $settings['s_image_w_gallery'], $settings['s_image_h_gallery']);
                $link = $link['url'];
            }

			if ( ! empty( $link ) ) {
				echo '<a data-image="' . esc_url( $link ) . '" title="' . esc_attr( $title ) . '" data-caption="' . esc_attr( $caption ) . '" href="#">';
			}
			echo $img;
			if ( ! empty( $link ) ) echo '</a>';

        endforeach; // end loop 
        ?>
    </div>
<?php endif; ?>