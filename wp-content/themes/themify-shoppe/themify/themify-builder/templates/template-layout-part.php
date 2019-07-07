<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Template Part
 * 
 * Access original fields: $mod_settings
 * @author Themify
 */
global $ThemifyBuilder;
$fields_default = array(
    'mod_title_layout_part' => '',
    'selected_layout_part' => '',
    'add_css_layout_part' => ''
);
$fields_args = wp_parse_args($mod_settings, $fields_default);
unset($mod_settings);
if (!self::$layout_part_id) {
    self::$layout_part_id = self::$post_id;
}
self::$post_id = $fields_args['selected_layout_part'];
$container_class = implode(' ', apply_filters('themify_builder_module_classes', array(
    'module', 'module-' . $mod_name, $module_ID, $fields_args['add_css_layout_part']
                ), $mod_name, $module_ID, $fields_args)
);
$container_props = apply_filters('themify_builder_module_container_props', array(
    'id' => $module_ID,
    'class' => $container_class
        ), $fields_args, $mod_name, $module_ID);
$ThemifyBuilder->in_the_loop = true;
?>
<!-- module template_part -->
<div <?php echo self::get_element_attributes($container_props); ?>>
    <!--insert-->
    <?php 
    if ($fields_args['mod_title_layout_part'] !== ''){
        echo $fields_args['before_title'] . apply_filters('themify_builder_module_title', $fields_args['mod_title_layout_part'], $fields_args). $fields_args['after_title']; 
    }
    if ($fields_args['selected_layout_part'] !== ''){
        echo do_shortcode('[themify_layout_part slug="' . $fields_args['selected_layout_part'] . '"]'); 
    }
    ?>
</div>
<!-- /module template_part -->
<?php
self::$post_id = self::$layout_part_id;
$ThemifyBuilder->in_the_loop = false;