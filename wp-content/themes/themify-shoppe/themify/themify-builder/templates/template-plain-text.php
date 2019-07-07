<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Template Plain Text
 * 
 * Access original fields: $mod_settings
 * @author Themify
 */
if (TFCache::start_cache($mod_name, self::$post_id, array('ID' => $module_ID))):
    $fields_default = array(
        'plain_text' => '',
        'add_css_text' => '',
        'animation_effect' => ''
    );

    $fields_args = wp_parse_args($mod_settings, $fields_default);
    unset($mod_settings);
    $animation_effect = self::parse_animation_effect($fields_args['animation_effect'], $fields_args);

    $container_class = implode(' ', apply_filters('themify_builder_module_classes', array(
        'module', 'module-' . $mod_name, $module_ID, $fields_args['add_css_text'], $animation_effect
                    ), $mod_name, $module_ID, $fields_args)
    );
    $container_props = apply_filters('themify_builder_module_container_props', array(
    'id' => $module_ID,
    'class' => $container_class
        ), $fields_args, $mod_name, $module_ID);
    ?>
    <!-- module plain text -->
    <div <?php echo self::get_element_attributes($container_props); ?>>
        <!--insert-->
        <?php echo $fields_args['plain_text'] !== '' ? do_shortcode($fields_args['plain_text']) : ''; ?>
    </div>
    <!-- /module plain text -->
<?php endif; ?>
<?php TFCache::end_cache(); ?>