<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Template Map
 * 
 * Access original fields: $mod_settings
 * @author Themify
 */
if (TFCache::start_cache($mod_name, self::$post_id, array('ID' => $module_ID))):

    $fields_default = array(
        'mod_title_map' => '',
        'address_map' => '',
        'latlong_map' => '',
        'zoom_map' => 15,
        'w_map' => '100',
        'w_map_static' => 500,
        'w_map_unit' => 'px',
        'h_map' => '300',
        'h_map_unit' => 'px',
        'b_style_map' => 'solid',
        'b_width_map' => '',
        'b_color_map' => '',
        'type_map' => 'ROADMAP',
        'scrollwheel_map' => 'disable',
        'draggable_map' => 'enable',
        'draggable_disable_mobile_map' => 'yes',
        'info_window_map' => '',
        'map_display_type' => 'dynamic',
        'css_map' => '',
        'animation_effect' => ''
    );

    if (!empty($mod_settings['address_map'])) {
        $mod_settings['address_map'] = preg_replace('/\s+/', ' ', trim($mod_settings['address_map']));
    }
    $fields_args = wp_parse_args($mod_settings, $fields_default);
    $fields_args['w_map_unit'] = isset($fields_args['unit_w']) && $fields_args['unit_w']!=-1?$fields_args['unit_w']:$fields_args['w_map_unit'];
    $fields_args['h_map_unit'] = isset($fields_args['unit_h']) && $fields_args['unit_h']!=-1?$fields_args['unit_h']:$fields_args['h_map_unit'];
    unset($mod_settings);
    $animation_effect = self::parse_animation_effect($fields_args['animation_effect'], $fields_args);
    $info_window_map = $fields_args['info_window_map'] === '' ? sprintf('<b>%s</b><br/><p>%s</p>', __('Address', 'themify'), $fields_args['address_map']) : $fields_args['info_window_map'];

// Check if draggable should be disabled on mobile devices
    if ('enable' === $fields_args['draggable_map'] && 'yes' === $fields_args['draggable_disable_mobile_map'] && wp_is_mobile()) {
        $fields_args['draggable_map'] = 'disable';
    }

    $container_class = implode(' ', apply_filters('themify_builder_module_classes', array(
        'module', 'module-' . $mod_name, $module_ID, $fields_args['css_map'], $animation_effect
                    ), $mod_name, $module_ID, $fields_args)
    );

    $container_props = apply_filters('themify_builder_module_container_props', array(
        'id' => $module_ID,
        'class' => $container_class
            ), $fields_args, $mod_name, $module_ID);

    $style = '';

// specify border
    if ($fields_args['b_width_map'] !== '') {
        $style .= 'border: ' . $fields_args['b_style_map'] . ' ' . $fields_args['b_width_map'] . 'px';
        if ($fields_args['b_color_map'] !== '') {
            $style.=' ' . Themify_Builder_Stylesheet::get_rgba_color($fields_args['b_color_map']);
        }
        $style .= ';';
    }
    ?>
    <!-- module map -->
    <div <?php echo self::get_element_attributes($container_props); ?>>
        <!--insert-->
        <?php if ($fields_args['mod_title_map'] !== ''): ?>
            <?php echo $fields_args['before_title'] . apply_filters('themify_builder_module_title', $fields_args['mod_title_map'], $fields_args). $fields_args['after_title']; ?>
        <?php endif; ?>

        <?php if ($fields_args['map_display_type'] === 'static') : ?>
            <?php
            $args = 'key='.Themify_Builder_Model::getMapKey();
            if ($fields_args['address_map'] !== '') {
                $args .= '&center=' . $fields_args['address_map'];
            } elseif ($fields_args['latlong_map'] !== '') {
                $args .= '&center=' . $fields_args['latlong_map'];
            }
            $args .= '&zoom=' . $fields_args['zoom_map'];
            $args .= '&maptype=' . strtolower($fields_args['type_map']);
            $args .= '&size=' . preg_replace('/[^0-9]/', '', $fields_args['w_map_static']) . 'x' . preg_replace('/[^0-9]/', '', $fields_args['h_map']);
            ?>
            <img style="<?php echo esc_attr($style); ?>" src="//maps.googleapis.com/maps/api/staticmap?<?php echo $args; ?>" />

        <?php elseif ($fields_args['address_map'] !== '' || $fields_args['latlong_map'] !== ''):
                if($fields_args['w_map_unit']==-1){
                    $fields_args['w_map_unit'] = 'px';
                }
                $style .= 'width:' . $fields_args['w_map'] . $fields_args['w_map_unit'] . ';';
                if($fields_args['h_map_unit']==-1){
                    $fields_args['h_map_unit'] = 'px';
                }
                $style .= 'height:' . $fields_args['h_map'] . $fields_args['h_map_unit'] . ';';
            ?>
            <div
				data-address="<?php echo esc_attr( $fields_args['address_map'] !== '' ? $fields_args['address_map'] : $fields_args['latlong_map'] ) ?>"
				data-zoom="<?php echo $fields_args['zoom_map']; ?>"
				data-type="<?php echo $fields_args['type_map']; ?>"
				data-scroll="<?php echo $fields_args['scrollwheel_map'] === 'enable'; ?>"
				data-drag="<?php echo $fields_args['draggable_map'] === 'enable'; ?>"
				class="themify_map map-container"
				style="<?php echo esc_attr($style); ?>"
				data-info-window="<?php echo esc_attr($info_window_map); ?>"
				data-reverse-geocoding="<?php echo empty($fields_args['address_map']) && !empty($fields_args['latlong_map']) ?>">
			</div>
        <?php endif; ?>
    </div>
    <!-- /module map -->
<?php endif; ?>
<?php TFCache::end_cache(); ?>