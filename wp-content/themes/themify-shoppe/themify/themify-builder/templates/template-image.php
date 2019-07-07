<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Template Image
 * 
 * Access original fields: $mod_settings
 * @author Themify
 */
if (TFCache::start_cache($mod_name, self::$post_id, array('ID' => $module_ID))):

    $fields_default = array(
        'mod_title_image' => '',
        'style_image' => '',
        'url_image' => '',
        'appearance_image' => '',
        'caption_on_overlay' => '',
        'image_size_image' => '',
        'width_image' => '',
        'auto_fullwidth' => false,
        'height_image' => '',
        'title_image' => '',
        'link_image' => '',
        'param_image' => '',
        'image_zoom_icon' => '',
        'lightbox_width' => '',
        'lightbox_height' => '',
	    'lightbox_width_unit' => 'px',
	    'lightbox_height_unit' => 'px',
        'alt_image' => '',
        'caption_image' => '',
        'css_image' => '',
        'animation_effect' => ''
    );

    if (isset($mod_settings['appearance_image'])) {
        $mod_settings['appearance_image'] = self::get_checkbox_data($mod_settings['appearance_image']);
    }
    $fields_args = wp_parse_args($mod_settings, $fields_default);
    unset($mod_settings);
    $animation_effect = self::parse_animation_effect($fields_args['animation_effect'], $fields_args);

    $lightbox_size_unit_width = $fields_args['lightbox_width_unit']?$fields_args['lightbox_width_unit']:'px';
    $lightbox_size_unit_height = $fields_args['lightbox_height_unit']?$fields_args['lightbox_height_unit']:'px';

    $container_class = implode(' ', apply_filters('themify_builder_module_classes', array(
        'module', 'module-' . $mod_name, $module_ID, $fields_args['appearance_image'], $fields_args['style_image'], $fields_args['css_image'], $animation_effect
                    ), $mod_name, $module_ID, $fields_args)
    );
    if ( ('' === $fields_args['style_image'] || $fields_args['style_image']) && isset( $fields_args['caption_on_overlay'] ) && 'yes' == $fields_args['caption_on_overlay'] ){
		$container_class .= ' active-caption-hover';
    }
    if ($fields_args['auto_fullwidth']=='1') {
        $container_class.=' auto_fullwidth';
    }
    $lightbox = $fields_args['param_image'] === 'lightbox';
    $zoom = $fields_args['image_zoom_icon'] === 'zoom';
    $zoom_icon = $fields_args['param_image'] === 'lightbox' ? 'fa-search' : 'fa-external-link';
    $newtab = !$lightbox && $fields_args['param_image'] === 'newtab';
    $lightbox_data = !empty($fields_args['lightbox_width']) || !empty($fields_args['lightbox_height']) ? sprintf(' data-zoom-config="%s|%s"'
                    , $fields_args['lightbox_width'] . $lightbox_size_unit_width, $fields_args['lightbox_height'] . $lightbox_size_unit_height) : false;
    $image_alt = '' !== $fields_args['alt_image'] ? $fields_args['alt_image'] : wp_strip_all_tags($fields_args['caption_image']);
    $image_title = $fields_args['title_image'];
    if ($image_alt === '') {
        $image_alt = $image_title;
    }
    if (Themify_Builder_Model::is_img_php_disabled()) {
        // get image preset
        global $_wp_additional_image_sizes;
        $preset = $fields_args['image_size_image'] !== '' ? $fields_args['image_size_image'] : themify_builder_get('setting-global_feature_size', 'image_global_size_field');
        if (isset($_wp_additional_image_sizes[$preset]) && $fields_args['image_size_image'] !== '') {
            $width_image = (int) $_wp_additional_image_sizes[$preset]['width'];
            $height_image = (int) $_wp_additional_image_sizes[$preset]['height'];
        } else {
            $width_image = $fields_args['width_image'] !== '' ? $fields_args['width_image'] : get_option($preset . '_size_w');
            $height_image = $fields_args['height_image'] !== '' ? $fields_args['height_image'] : get_option($preset . '_size_h');
        }
        $upload_dir = wp_upload_dir();
        $base_url = $upload_dir['baseurl'];
        $attachment_id = themify_get_attachment_id_from_url($fields_args['url_image'], $base_url);
        $class = $attachment_id ? 'wp-image-' . $attachment_id : '';
        $image = '<img src="' . esc_url($fields_args['url_image']) . '" alt="' . esc_attr($image_alt) . (!empty($image_title) ? ( '" title="' . esc_attr($image_title) ) : '' ) . '" width="' . $fields_args['width_image'] . '" height="' . $fields_args['height_image'] . '" class="' . $class . '">';
        if (!empty($attachment_id)) {
            $image = wp_get_attachment_image($attachment_id, $preset);
        }
    } else {
        $image = themify_get_image('src=' . esc_url($fields_args['url_image']) . '&w=' . $fields_args['width_image'] . '&h=' . $fields_args['height_image'] . '&alt=' . $image_alt . (!empty($image_title) ? ( '&title=' . $image_title ) : '' ) . '&ignore=true');
    }
    $image = apply_filters('themify_image_make_responsive_image', $image);

    $container_props = apply_filters('themify_builder_module_container_props', array(
        'id' => $module_ID,
        'class' => $container_class
            ), $fields_args, $mod_name, $module_ID);
    ?>
    <!-- module image -->
    <div <?php echo self::get_element_attributes($container_props); ?>>
        <!--insert-->
        <?php if ($fields_args['mod_title_image'] !== ''): ?>
            <?php echo $fields_args['before_title'] . apply_filters('themify_builder_module_title', $fields_args['mod_title_image'], $fields_args). $fields_args['after_title']; ?>
        <?php endif; ?>

        <div class="image-wrap">
            <?php if ($fields_args['link_image'] !== ''): ?>
                <a href="<?php echo esc_url($fields_args['link_image']); ?>"
                   <?php if ($lightbox) : ?>class="lightbox-builder themify_lightbox"<?php echo $lightbox_data; ?><?php endif; ?>
                   <?php if ($newtab): ?> rel="noopener" target="_blank"<?php endif; ?>>
                       <?php if ($zoom): ?>
                        <span class="zoom fa <?php echo $zoom_icon; ?>"></span>
                    <?php endif; ?>
                    <?php echo $image; ?>
                </a>
            <?php else: ?>
                <?php echo $image; ?>
            <?php endif; ?>

            <?php if ('image-overlay' !== $fields_args['style_image']): ?>
            </div>
            <!-- /image-wrap -->
        <?php endif; ?>

        <?php if ($image_title !== '' || $fields_args['caption_image'] !== ''): ?>
            <div class="image-content">
                <?php if ($image_title !== ''): ?>
                    <h3 class="image-title">
                        <?php if ($fields_args['link_image'] !== ''): ?>
                            <a href="<?php echo esc_url($fields_args['link_image']); ?>" 
                               <?php if ($lightbox) : ?> class="lightbox-builder themify_lightbox"<?php echo $lightbox_data; ?><?php endif; ?>
                               <?php if ($newtab): ?> rel="noopener" target="_blank"<?php endif; ?>>
                                   <?php echo $image_title; ?>
                            </a>
                        <?php else: ?>
                            <?php echo $image_title; ?>
                        <?php endif; ?>
                    </h3>
                <?php endif; ?>

                    <?php if ($fields_args['caption_image'] !== ''): ?>
                    <div class="image-caption">
                        <?php echo apply_filters('themify_builder_module_content', $fields_args['caption_image']); ?>
                </div>
                <!-- /image-caption -->
                <?php endif; ?>
            </div>
            <!-- /image-content -->
        <?php endif; ?>

        <?php if ('image-overlay' === $fields_args['style_image']): ?>
        </div>
        <!-- /image-wrap -->
    <?php endif; ?>
    </div>
    <!-- /module image -->
<?php endif; ?>
<?php TFCache::end_cache(); ?>