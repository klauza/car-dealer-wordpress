<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Template Divider
 *
 * Access original fields: $mod_settings
 * @author Themify
 */
if (TFCache::start_cache($mod_name, self::$post_id, array('ID' => $module_ID))):

    $fields_default = array(
        'mod_title_divider' => '',
        'style_divider' => 'solid',
        'stroke_w_divider' => 1,
        'color_divider' => '',
        'top_margin_divider' => '',
        'bottom_margin_divider' => '',
        'css_divider' => '',
        'divider_type' => 'fullwidth',
        'divider_width' => 200,
        'divider_align' => 'left',
        'animation_effect' => ''
    );
    
    $fields_args = wp_parse_args( $mod_settings, $fields_default );
    unset( $mod_settings );
    if ($fields_args['divider_type'] === 'custom') {
        $fields_args['divider_align'] = 'divider-' . $fields_args['divider_align'];
        $divider_type = 'divider-' . $fields_args['divider_type'];
    } else {
        $divider_type = $fields_args['divider_align'] = $fields_args['divider_width'] = '';
    }
    $animation_effect = self::parse_animation_effect($fields_args['animation_effect'], $fields_args);

	$styles = array(
		'border-width'	=> 'stroke_w_divider',
		'border-color'	=> 'color_divider',
		'margin-top'	=> 'top_margin_divider',
		'margin-bottom'	=> 'bottom_margin_divider',
		'width'			=> 'divider_width'
	);
	
	$style = '';

	foreach( $styles as $prop => $val ) {
		if( isset( $fields_args[ $val ] ) && $fields_args[ $val ]!=='') {
			$style .= $prop . ': ';
			$style .= $prop === 'border-color'
				? Themify_Builder_Stylesheet::get_rgba_color( $fields_args[ $val ] ) . ';'
				: $fields_args[ $val ] . 'px;';
		}
	}

    $container_class = implode(' ', apply_filters('themify_builder_module_classes', array(
        'module', 'module-' . $mod_name, $module_ID, $fields_args['style_divider'], $fields_args['css_divider'], $animation_effect, $divider_type, $fields_args['divider_align']
                    ), $mod_name, $module_ID, $fields_args)
    );
    $container_props = apply_filters('themify_builder_module_container_props', array(
        'id' => $module_ID,
        'class' => $container_class,
            ), $fields_args, $mod_name, $module_ID);
    if ($style) {
        $container_props['style'] = esc_attr($style);
    }
    ?>
    <!-- module divider -->
    <div <?php echo self::get_element_attributes($container_props); ?>>
        <!--insert-->
        <?php if ($fields_args['mod_title_divider'] !== ''): ?>
            <?php echo $fields_args['before_title'] . apply_filters('themify_builder_module_title', $fields_args['mod_title_divider'], $fields_args). $fields_args['after_title']; ?>
        <?php endif; ?>
    </div>
    <!-- /module divider -->
<?php endif; ?>
<?php TFCache::end_cache(); ?>
