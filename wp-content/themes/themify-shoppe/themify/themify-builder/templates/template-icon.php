<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Template Icon
 * 
 * Access original fields: $mod_settings
 * @author Themify
 */
if (TFCache::start_cache($mod_name, self::$post_id, array('ID' => $module_ID))):

    $fields_default = array(
        'mod_title_icon' => '',
        'icon_size' => '',
        'icon_style' => '',
        'icon_arrangement' => 'icon_horizontal',
        'icon_position' => 'icon_position_left',
        'content_icon' => array(),
        'animation_effect' => '',
        'css_icon' => ''
    );

    $fields_args = wp_parse_args($mod_settings, $fields_default);
    unset($mod_settings);
    $animation_effect = self::parse_animation_effect($fields_args['animation_effect'], $fields_args);
    $container_class = implode(' ', apply_filters('themify_builder_module_classes', array(
        'module', $module_ID, $fields_args['css_icon'], $animation_effect
                    ), $mod_name, $module_ID, $fields_args)
    );
    $ui_class = implode(' ', array('module-' . $mod_name, $fields_args['icon_size'], $fields_args['icon_style'], $fields_args['icon_arrangement'],$fields_args['icon_position']));
    ?>
    <!-- module icon -->
    <div id="<?php echo $module_ID; ?>" class="<?php echo $container_class; ?>">
        <!--insert-->
        <?php if ($fields_args['mod_title_icon'] !== ''): ?>
            <?php echo $fields_args['before_title'] . apply_filters('themify_builder_module_title', $fields_args['mod_title_icon'], $fields_args). $fields_args['after_title']; ?>
        <?php endif; ?>

        <div class="<?php echo $ui_class; ?>">
            <?php
            $content_icon = array_filter($fields_args['content_icon']);
            foreach ($content_icon as $content):
                $content = wp_parse_args($content, array(
                    'label' => '',
                    'link' => '',
                    'icon' => '',
                    'new_window' => false,
                    'icon_color_bg' => false,
                    'link_options' => '',
                    'lightbox_width' => '',
                    'lightbox_height' => '',
                    'lightbox_width_unit' => 'px',
                    'lightbox_height_unit' => 'px'
                ));
	            $content['lightbox_width_unit'] = $content['lightbox_width_unit'] ? $content['lightbox_width_unit'] : 'px';
	            $content['lightbox_height_unit'] = $content['lightbox_height_unit'] ? $content['lightbox_height_unit'] : 'px';
                $link_target = $content['link_options'] === 'newtab' ? ' rel="noopener" target="_blank"' : '';
                $link_lightbox_class = $content['link_options'] === 'lightbox' ? ' class="lightbox-builder themify_lightbox"' : '';
                $lightbox_data = !empty($content['lightbox_width']) || !empty($content['lightbox_height']) ? sprintf(' data-zoom-config="%s|%s"'
                                , $content['lightbox_width'] . $content['lightbox_width_unit']
                                , $content['lightbox_height'] . $content['lightbox_height_unit']) : false;
                ?>
                <div class="module-icon-item">
                    <?php if ($content['link']): ?>
                        <a href="<?php echo esc_attr($content['link']) ?>"<?php echo $link_target, $lightbox_data, $link_lightbox_class ?>>
                        <?php endif; ?>
                        <?php if ($content['icon']): ?>
                            <i class="<?php echo themify_get_icon($content['icon']); ?> ui <?php echo $content['icon_color_bg'] ?>"></i>
                        <?php endif; ?>
                        <?php if ($content['label']): ?>
                            <span><?php echo $content['label'] ?></span>
                        <?php endif; ?>
                        <?php if ($content['link']): ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <!-- /module icon -->
<?php endif; ?>
<?php TFCache::end_cache(); ?>