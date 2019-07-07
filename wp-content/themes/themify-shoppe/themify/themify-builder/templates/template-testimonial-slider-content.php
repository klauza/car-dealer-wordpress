<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Template Testimonial
 * 
 * Access original fields: $mod_settings
 * @author Themify
 */
if (!empty($settings['tab_content_testimonial'])):
    $is_img_disabled = Themify_Builder_Model::is_img_php_disabled(); 
    $image_w = $settings['img_w_slider'];
    $image_h = $settings['img_h_slider'];
    if ($is_img_disabled) {
        // get image preset
        global $_wp_additional_image_sizes;
        $preset = $settings['image_size_slider'] !== '' ? $settings['image_size_slider'] : themify_builder_get('setting-global_feature_size', 'image_global_size_field');
        if (isset($_wp_additional_image_sizes[$preset]) && $image_size_slider !== '') {
            $image_w = (int) $_wp_additional_image_sizes[$preset]['width'];
            $image_h = (int) $_wp_additional_image_sizes[$preset]['height'];
        } else {
            $image_w = $image_w !== '' ? $image_w : get_option($preset . '_size_w');
            $image_h = $image_h !== '' ? $image_h : get_option($preset . '_size_h');
        }
    } else {
        $param_image_src = array('w' => $image_w, 'h' => $image_h, 'ignore' => true);
    }
    foreach ($settings['tab_content_testimonial'] as $content):
        ?>
        <li>
            <div <?php if ($settings['margin'] !== ''): ?> style="<?php echo $settings['margin']; ?>"<?php endif; ?>>
                <?php
                $image = '';
                if (!empty($content['person_picture_testimonial'])) {
                    $image_url = esc_url($content['person_picture_testimonial']);
                    $image_title = isset($content['title_testimonial']) ? $content['title_testimonial'] : '';
                    if ($alt_by_url = Themify_Builder_Model::get_alt_by_url($image_url)) {
                        $image_alt = $alt_by_url;
                    } else {
                        $image_alt = $image_title;
                    }
                    if ($is_img_disabled) {
                        $image = '<img src="' . $image_url . '" alt="' . esc_attr($image_alt) . '" class="person-picture" width="' . $image_w . '" height="' . $image_h . '"/>';
                    } else {
                        $param_image_src['src'] = $image_url;
                        $param_image_src['alt'] = $image_alt;
                        $image = themify_get_image($param_image_src);
                    }
                }
                ?>	

                <?php if ($settings['layout_slider'] === 'image-top' && !empty($image)): ?>
                    <figure class="testimonial-image">
                        <?php echo $image; ?>
                    </figure>
                <?php endif; ?>

                <div class="testimonial-content">
                    <?php if (!empty($content['title_testimonial'])): ?>
                        <h3 class="testimonial-title"><?php echo $content['title_testimonial'] ?></h3>
                    <?php endif; ?>
                    <?php if (!empty($content['content_testimonial'])): ?>
						<div class="testimonial-entry-content">
							<?php
								$testimonial_content = get_extended( $content['content_testimonial'] );

								if( ! empty( $testimonial_content['main'] ) ) {
									printf( '<div class="testimonial-content-main">%s</div>', do_shortcode( $testimonial_content['main'] ) );
								}

								if( ! empty( $testimonial_content['extended'] ) ) {
									printf( '<label><input type="checkbox"><div class="testimonial-content-extended">%s</div><a href="#">%s</a></label>'
										, do_shortcode( $testimonial_content['extended'] )
										, ! empty( $testimonial_content['more_text'] ) ? $testimonial_content['more_text'] : esc_attr__( 'More', 'themify' ) );
								}
							?>
						</div>
                    <?php endif; ?>
                    <?php if ($settings['layout_slider'] !== 'image-top' && !empty($image)): ?>
                        <figure class="testimonial-image"><?php echo $image ?></figure>
                    <?php endif; ?>

                    <?php if (!empty($content['person_name_testimonial'])): ?>
                        <div class="testimonial-author">
                            <div class="person-name"><?php echo $content['person_name_testimonial'] ?></div>
                            <div class="person-company">
                                <?php if (!empty($content['person_position_testimonial'])): ?>
                                    <span class="person-position"><?php echo $content['person_position_testimonial'] ?></span>
                                <?php endif; ?>
                                <?php if (!empty($content['company_testimonial'])): ?>
                                    <div class="person-company">
                                        <?php if (!empty($content['company_website_testimonial'])): ?>
                                            <a href="<?php echo $content['company_website_testimonial'] ?>"><?php echo $content['company_testimonial'] ?></a>
                                        <?php else: ?>
                                            <?php echo $content['company_testimonial'] ?>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <!-- /testimonial-content -->
            </div>
        </li>
    <?php endforeach; ?>
<?php endif; ?>