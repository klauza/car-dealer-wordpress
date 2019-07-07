<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
///////////////////////////////////////
// Switch Template Layout Types
///////////////////////////////////////
$template_name = isset($mod_settings['layout_display_slider']) ? $mod_settings['layout_display_slider'] : 'blog';
if (in_array($template_name, array('blog', 'portfolio', 'testimonial', 'slider'),true)) {
    $template_name = 'blog';
    $ThemifyBuilder->in_the_loop = true;
}
if (TFCache::start_cache($mod_name, self::$post_id, array('ID' => $module_ID))) {
    $slider_default = array(
        'layout_display_slider' => 'blog',
        'open_link_new_tab_slider' => 'no',
        'mod_title_slider' => '',
        'layout_slider' => '',
        'img_h_slider' => '',
        'img_w_slider' => '',
        'img_fullwidth_slider' => '',
        'image_size_slider' => '',
        'visible_opt_slider' => '',
        'mob_visible_opt_slider' => '',
        'auto_scroll_opt_slider' => 0,
        'scroll_opt_slider' => '',
        'speed_opt_slider' => '',
        'effect_slider' => 'scroll',
        'pause_on_hover_slider' => 'resume',
        'wrap_slider' => 'yes',
        'show_nav_slider' => 'yes',
        'show_arrow_slider' => 'yes',
        'show_arrow_buttons_vertical' => '',
        'unlink_feat_img_slider'=>'no',
        'unlink_post_title_slider'=>'no',
        'left_margin_slider' => '',
        'right_margin_slider' => '',
        'css_slider' => '',
        'animation_effect' => '',
        'height_slider' => 'variable'
    );

    $settings = wp_parse_args($mod_settings, $slider_default);
    unset($mod_settings);
    $animation_effect = self::parse_animation_effect($settings['animation_effect'], $settings);
    $arrow_vertical = $settings['show_arrow_slider'] === 'yes' && $settings['show_arrow_buttons_vertical'] === 'vertical' ? 'themify_builder_slider_vertical' : '';
    $fullwidth_image = $settings['img_fullwidth_slider'] === 'fullwidth' ? 'slide-image-fullwidth' : '';
    $container_class = implode(' ', apply_filters('themify_builder_module_classes', array(
        'module', 'module-' . $mod_name, $module_ID, 'themify_builder_slider_wrap', 'clearfix', $settings['css_slider'], $settings['layout_slider'], $animation_effect, $arrow_vertical, $fullwidth_image
                    ), $mod_name, $module_ID, $settings)
    );
    $container_props = apply_filters('themify_builder_module_container_props', array(
        'id' => $module_ID,
        'class' => $container_class
            ), $settings, $mod_name, $module_ID);

    $margins = '';
    if ($settings['left_margin_slider'] !== '') {
        $margins.='margin-left:' . $settings['left_margin_slider'] . 'px;';
    }
    if ($settings['right_margin_slider'] !== '') {
        $margins.='margin-right:' . $settings['right_margin_slider'] . 'px;';
    }
    $settings['margin'] = $margins;
    $speed = $settings['speed_opt_slider'] === 'slow' ? 4 : ($settings['speed_opt_slider'] === 'fast' ? '.5' : 1);
    ?>
    <div class="tb_slider_loader"></div>
    <div<?php echo self::get_element_attributes($container_props); ?>>
        <!--insert-->
        <?php if ($settings['mod_title_slider'] !== ''): ?>
            <?php echo $settings['before_title'] . apply_filters('themify_builder_module_title', $settings['mod_title_slider'], $settings). $settings['after_title']; ?>
        <?php endif; ?>
        <ul class="themify_builder_slider"
            data-id="<?php echo $module_ID; ?>" 
            data-visible="<?php echo $settings['visible_opt_slider'] ?>" 
            data-mob-visible="<?php echo $settings['mob_visible_opt_slider'] ?>"
            data-scroll="<?php echo $settings['scroll_opt_slider']; ?>" 
            data-auto-scroll="<?php echo $settings['auto_scroll_opt_slider'] ?>"
            data-speed="<?php echo $speed ?>"
            data-wrap="<?php echo $settings['wrap_slider']; ?>"
            data-arrow="<?php echo $settings['show_arrow_slider']; ?>"
            data-pagination="<?php echo $settings['show_nav_slider']; ?>"
            data-effect="<?php echo $settings['effect_slider'] ?>" 
            data-height="<?php echo $settings['height_slider'] ?>" 
            data-pause-on-hover="<?php echo $settings['pause_on_hover_slider'] ?>"
            <?php if ($template_name === 'video'): ?>data-type="video"<?php endif; ?>>
                <?php
                self::retrieve_template('template-' . $mod_name . '-' . $template_name . '.php', array(
                    'module_ID' => $module_ID,
                    'mod_name' => $mod_name,
                    'settings' => $settings
                        ), '', '', true);
                ?>
        </ul>
    </div>
        <?php
}
TFCache::end_cache();
$ThemifyBuilder->in_the_loop = false;
    