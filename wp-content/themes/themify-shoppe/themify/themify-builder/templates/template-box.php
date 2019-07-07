<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Template Box
 * 
 * Access original fields: $mod_settings
 * @author Themify
 */
if (TFCache::start_cache($mod_name, self::$post_id, array('ID' => $module_ID))):

    $fields_default = array(
        'mod_title_box' => '',
        'content_box' => '',
        'appearance_box' => '',
        'color_box' => '',
        'add_css_box' => '',
        'background_repeat' => '',
        'animation_effect' => ''
    );

    if (isset($mod_settings['appearance_box'])) {
        $mod_settings['appearance_box'] = self::get_checkbox_data($mod_settings['appearance_box']);
    }
    $fields_args = wp_parse_args($mod_settings, $fields_default);
    unset($mod_settings);
    $animation_effect = self::parse_animation_effect($fields_args['animation_effect'], $fields_args);

    $container_class = implode(' ', apply_filters('themify_builder_module_classes', array(
        'module', 'module-' . $mod_name, $module_ID, $animation_effect, $fields_args['add_css_box']
                    ), $mod_name, $module_ID, $fields_args)
    );
    $inner_container_classes = implode(' ', apply_filters('themify_builder_module_inner_classes', array(
        'module-' . $mod_name . '-content', 'ui', $fields_args['appearance_box'], $fields_args['color_box'], $fields_args['background_repeat']
            ))
    );
    $container_props = apply_filters('themify_builder_module_container_props', array(
        'id' => $module_ID,
        'class' => $container_class
            ), $fields_args, $mod_name, $module_ID);
    ?>
    <!-- module box -->
    <div <?php echo self::get_element_attributes($container_props); ?>>
        <!--insert-->
        <?php if ($fields_args['mod_title_box'] !== ''): ?>
            <?php echo $fields_args['before_title'] . apply_filters('themify_builder_module_title', $fields_args['mod_title_box'], $fields_args). $fields_args['after_title']; ?>
        <?php endif; ?>
        <div class="<?php echo $inner_container_classes; ?>">
                <?php echo apply_filters('themify_builder_module_content', $fields_args['content_box']); ?>
        </div>
    </div>
    <!-- /module box -->
<?php endif; ?>
<?php TFCache::end_cache(); ?>