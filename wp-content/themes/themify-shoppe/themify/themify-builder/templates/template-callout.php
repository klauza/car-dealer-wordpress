<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Template Callout
 *
 * Access original fields: $mod_settings
 * @author Themify
 */
if (TFCache::start_cache($mod_name, self::$post_id, array('ID' => $module_ID))):

    $fields_default = array(
        'mod_title_callout' => '',
        'appearance_callout' => '',
        'layout_callout' => '',
        'color_callout' => '',
        'heading_callout' => '',
        'text_callout' => '',
        'action_btn_link_callout' => '#',
        'open_link_new_tab_callout' => '',
        'action_btn_text_callout' => false,
        'action_btn_color_callout' => '',
        'action_btn_appearance_callout' => '',
        'css_callout' => '',
        'background_repeat' => '',
        'animation_effect' => ''
    );

    if (isset($mod_settings['appearance_callout'])) {
        $mod_settings['appearance_callout'] = self::get_checkbox_data($mod_settings['appearance_callout']);
    }
    if (isset($mod_settings['action_btn_appearance_callout'])) {
        $mod_settings['action_btn_appearance_callout'] = self::get_checkbox_data($mod_settings['action_btn_appearance_callout']);
    }
    $fields_args = wp_parse_args($mod_settings, $fields_default);
    unset($mod_settings);
    $animation_effect = self::parse_animation_effect($fields_args['animation_effect'], $fields_args);

    $container_class = implode(' ', apply_filters('themify_builder_module_classes', array(
        'module', 'module-' . $mod_name, $module_ID, 'ui', $fields_args['layout_callout'], $fields_args['color_callout'], $fields_args['css_callout'], $fields_args['appearance_callout'], $fields_args['background_repeat'], $animation_effect
                    ), $mod_name, $module_ID, $fields_args)
    );
    $container_props = apply_filters('themify_builder_module_container_props', array(
        'id' => $module_ID,
        'class' => $container_class
            ), $fields_args, $mod_name, $module_ID);

    $ui_class = implode(' ', array('ui', 'builder_button', $fields_args['action_btn_color_callout'], $fields_args['action_btn_appearance_callout']));
    ?>
    <!-- module callout -->
    <div <?php echo self::get_element_attributes($container_props); ?>>
        <!--insert-->
        <?php if ($fields_args['mod_title_callout'] !== ''): ?>
            <?php echo $fields_args['before_title'] . apply_filters('themify_builder_module_title', $fields_args['mod_title_callout'], $fields_args). $fields_args['after_title']; ?>
        <?php endif; ?>

        <div class="callout-inner">
            <div class="callout-content">
                <h3 class="callout-heading"><?php echo $fields_args['heading_callout'] ?></h3>
                <p>
                <?php
                echo apply_filters('themify_builder_module_content', $fields_args['text_callout']);
                ?>
                </p>
            </div>
            <!-- /callout-content -->

            <?php if ($fields_args['action_btn_text_callout']) : ?>
                <div class="callout-button">
                        <a href="<?php echo esc_url($fields_args['action_btn_link_callout']); ?>" class="<?php echo $ui_class; ?>"<?php echo 'yes' === $fields_args['open_link_new_tab_callout'] ? ' rel="noopener" target="_blank"' : ''; ?>>
                            <?php echo $fields_args['action_btn_text_callout'] ?>
                        </a>
                    </div>
                <?php endif; ?>
        </div>
        <!-- /callout-content -->
    </div>
    <!-- /module callout -->
<?php endif; ?>
<?php TFCache::end_cache(); ?>
