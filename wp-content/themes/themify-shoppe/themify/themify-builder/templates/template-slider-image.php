<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Template Slider Image
 * 
 * Access original fields: $settings
 * @author Themify
 */
if (!empty($settings['img_content_slider'])):
    $image_w = $settings['img_w_slider'];
    $image_h = $settings['img_h_slider'];
    $is_img_disabled = Themify_Builder_Model::is_img_php_disabled();
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
    ?>
    <!-- module slider image -->

    <?php foreach ($settings['img_content_slider'] as $content): ?>
        <?php $image_title = isset($content['img_title_slider']) ? $content['img_title_slider'] : '';?>
        <li>
            <div class="slide-inner-wrap"<?php if ($settings['margin'] !== ''): ?> style="<?php echo $settings['margin']; ?>"<?php endif; ?>>
                <?php if (!empty($content['img_url_slider'])): ?>
                    <div class="slide-image">
                        <?php
                        $image_url = isset($content['img_url_slider']) ? esc_url($content['img_url_slider']) : '';
                        
                        if ($alt_by_url = Themify_Builder_Model::get_alt_by_url($image_url)) {
                            $image_alt = $alt_by_url;
                        } else {
                            $image_alt = $image_title;
                        }
                        if ($is_img_disabled) {
                            $image = '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($image_alt) . '" width="' . $image_w . '" height="' . $image_h . '"/>';
                        } else {
                            $param_image_src['src'] = $image_url;
                            $param_image_src['alt'] = $image_alt;
                            $image = themify_get_image($param_image_src);
                        }
                        ?>
                        <?php if (!empty($content['img_link_slider'])): ?>
                            <?php
                            $attr = '';
                            if (isset($content['img_link_params'])) {
                                $attr = $content['img_link_params'] === 'lightbox' ? ' data-rel="' . $module_ID . '" class="themify_lightbox"' : ($content['img_link_params'] === 'newtab' ? ' target="_blank" rel="noopener"' : '');
                            }
                            ?>
                            <a href="<?php echo esc_url(trim($content['img_link_slider'])); ?>" alt="<?php echo esc_attr($image_alt); ?>"<?php echo $attr; ?>>
                                <?php echo $image; ?>
                            </a>
                        <?php else: ?>
                            <?php echo $image; ?>
                        <?php endif; ?>
                    </div><!-- /slide-image -->
                <?php endif; ?>

                <?php if ($image_title !== '' || isset($content['img_caption_slider'])): ?>
                    <div class="slide-content">

                        <?php if ($image_title !== ''): ?>
                            <h3 class="slide-title">
                                <?php if (!empty($content['img_link_slider'])): ?>
                                    <a href="<?php echo esc_url($content['img_link_slider']); ?>"<?php echo $attr; ?>><?php echo wp_kses_post($image_title); ?></a>
                                <?php else: ?>
                                    <?php echo $image_title; ?>
                                <?php endif; ?>
                            </h3>
                        <?php endif; ?>

                        <?php
                        if (isset($content['img_caption_slider'])) {
                            echo apply_filters('themify_builder_module_content', $content['img_caption_slider']);
                        }
                        ?>
                    </div><!-- /slide-content -->
                <?php endif; ?>
            </div>
        </li>
    <?php endforeach; ?>
<?php endif; ?>