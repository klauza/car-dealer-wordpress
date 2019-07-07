<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Template Service Menu
 * 
 * Access original fields: $mod_settings
 * @author Themify
 */
if (TFCache::start_cache($mod_name, self::$post_id, array('ID' => $module_ID))):

    $fields_default = array(
        'image_size_image' => '',
        'title_service_menu' => '',
        'style_service_menu' => 'image-left',
        'description_service_menu' => '',
        'price_service_menu' => '',
        'image_service_menu' => '',
        'appearance_image_service_menu' => '',
        'image_size_service_menu' => '',
        'width_service_menu' => '',
        'height_service_menu' => '',
        'link_service_menu' => '',
        'link_options' => '',
        'image_zoom_icon' => '',
        'lightbox_width' => '',
        'lightbox_height' => '',
        'lightbox_size_unit_width' => 'pixels',
        'lightbox_size_unit_height' => 'pixels',
        'param_service_menu' => array(),
        'highlight_service_menu' => array(),
        'highlight_text_service_menu' => '',
        'highlight_color_service_menu' => '',
        'css_service_menu' => '',
        'animation_effect' => ''
    );

    if (isset($mod_settings['appearance_image_service_menu'])) {
        $mod_settings['appearance_image_service_menu'] = self::get_checkbox_data($mod_settings['appearance_image_service_menu']);
    }
    if (isset($mod_settings['param_service_menu'])) {
        $mod_settings['param_service_menu'] = explode('|', $mod_settings['param_service_menu']);
    }
    $highlight = false;
    if (isset($mod_settings['highlight_service_menu'])) {
        $mod_settings['highlight_service_menu'] = explode('|', $mod_settings['highlight_service_menu']);
        if (in_array('highlight', $mod_settings['highlight_service_menu'])) {
            $highlight = true;
        }
    }
    $fields_args = wp_parse_args($mod_settings, $fields_default);
    unset($mod_settings);
    $animation_effect = self::parse_animation_effect($fields_args['animation_effect'], $fields_args);

    $container_class = array('module', 'module-' . $mod_name, $module_ID, $fields_args['appearance_image_service_menu'], $fields_args['style_service_menu'], $fields_args['css_service_menu'], $animation_effect);
    if ($highlight === true) {
        $container_class[] = 'has-highlight';
        $container_class[] = $fields_args['highlight_color_service_menu'];
    } else {
        $container_class[] = 'no-highlight';
    }
    $container_class = implode(' ', apply_filters('themify_builder_module_classes', $container_class, $mod_name, $module_ID, $fields_args));

    $lightbox = false;
    $link_attr = '';
    if ($fields_args['link_options'] === 'lightbox') {
        $lightbox = true;
        $units = array(
            'pixels' => 'px',
            'percents' => '%'
        );

        if ($fields_args['lightbox_width'] !== '' || $fields_args['lightbox_height'] !== '') {
            $lightbox_settings = array();
            $lightbox_settings[] = $fields_args['lightbox_width'] !== '' ? $fields_args['lightbox_width'] . $units[$fields_args['lightbox_size_unit_width']] : '';
            $lightbox_settings[] = $fields_args['lightbox_height'] !== '' ? $fields_args['lightbox_height'] . $units[$fields_args['lightbox_size_unit_height']] : '';
            $link_attr = sprintf('data-zoom-config="%s"', implode('|', $lightbox_settings));
        }
    }

    $zoom = $fields_args['image_zoom_icon'] === 'zoom';
    $newtab = $fields_args['link_options'] === 'newtab';
    $image = '';
    $image_alt = '' !== $fields_args['title_service_menu'] ? esc_attr($fields_args['title_service_menu']) : wp_strip_all_tags($fields_args['description_service_menu']);
    if(!empty($fields_args['image_service_menu'])){
        if (Themify_Builder_Model::is_img_php_disabled()) {
            // get image preset
            global $_wp_additional_image_sizes;
            $preset = $fields_args['image_size_image'] !== '' ? $fields_args['image_size_image'] : themify_builder_get('setting-global_feature_size', 'image_global_size_field');
            if (isset($_wp_additional_image_sizes[$preset]) && $fields_args['image_size_image'] !== '') {
                $width_service_menu = (int) $_wp_additional_image_sizes[$preset]['width'];
                $height_service_menu = (int) $_wp_additional_image_sizes[$preset]['height'];
            } else {
                $width_service_menu = $fields_args['width_service_menu'] !== '' ? $fields_args['width_service_menu'] : get_option($preset . '_size_w');
                $height_service_menu = $fields_args['height_service_menu'] !== '' ? $fields_args['height_service_menu'] : get_option($preset . '_size_h');
            }
            $upload_dir = wp_upload_dir();
            $base_url = $upload_dir['baseurl'];
            $attachment_id = themify_get_attachment_id_from_url($fields_args['image_service_menu'], $base_url);
            $class = $attachment_id ? 'wp-image-' . $attachment_id : '';
            $image = $fields_args['image_service_menu'] ? '<img src="' . esc_url($fields_args['image_service_menu']) . '" alt="' . esc_attr($image_alt) . '" width="' . $width_service_menu . '" height="' . $height_service_menu . '" class="tb_menu_image ' . $class . '">' : '';
        } else {
            $image = themify_get_image('src=' . esc_url($fields_args['image_service_menu']) . '&w=' . $fields_args['width_service_menu'] . '&h=' . $fields_args['height_service_menu'] . '&alt=' . $image_alt . '&ignore=true&class=tb_menu_image');
        }
    }
    $image = apply_filters('themify_image_make_responsive_image', $image);

    $container_props = apply_filters('themify_builder_module_container_props', array(
        'id' => $module_ID,
        'class' => $container_class
            ), $fields_args, $mod_name, $module_ID);
    ?>
    <!-- module service menu -->
    <div <?php echo self::get_element_attributes($container_props); ?>>
        <!--insert-->
        <?php if ($highlight === true && $fields_args['highlight_text_service_menu'] !== '') : ?>
            <div class="tb-highlight-text">
                <?php echo $fields_args['highlight_text_service_menu']; ?>
            </div>
        <?php endif; ?>
        <?php if ($image): ?>
            <div class="tb-image-wrap">
                <?php if ($fields_args['link_service_menu'] !== '') : ?>
                    <a href="<?php echo esc_url($fields_args['link_service_menu']); ?>" <?php
                    if ($lightbox) : echo 'class="lightbox-builder themify_lightbox"';
                    endif;
                    ?> <?php
                    if ($newtab) : echo 'rel="noopener" target="_blank"';
                    endif;
                    ?> <?php echo $link_attr; ?>>
                           <?php if ($zoom && $fields_args['link_options'] !== 'regular') : ?>
                               <?php $zoom_icon = $newtab ? 'fa-external-link' : 'fa-search'; ?>
                            <span class="zoom fa <?php echo $zoom_icon; ?>"></span>
                        <?php endif; ?>
                        <?php echo $image; ?>
                    </a>
                <?php else : ?>
                    <?php echo $image; ?>
                <?php endif; ?>
            </div><!-- .tb-image-wrap -->
        <?php endif; ?>

        <div class="tb-image-content">
            <?php if ($fields_args['title_service_menu'] !== '') : ?>
                <h4 class="tb-menu-title"><?php echo $fields_args['title_service_menu']; ?></h4>
            <?php endif; ?>

            <?php if ($fields_args['price_service_menu'] !== '') : ?>
                <div class="tb-menu-price">
                    <?php echo $fields_args['price_service_menu']; ?>
                    
                    <?php if (isset( $fields_args['_render_plain_content'] ) && true === $fields_args['_render_plain_content'] ): ?>
                <br />
                <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($fields_args['description_service_menu'] !== '') : ?>
                <div class="tb-menu-description">
                    <?php echo $fields_args['description_service_menu']; ?>
            </div>
            <?php endif; ?>
        </div><!-- .tb-image-content -->
    </div>
    <!-- /module service menu -->

<?php endif; ?>
<?php TFCache::end_cache(); ?>