<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Template Menu
 * 
 * Access original fields: $mod_settings
 * @author Themify
 */
if (TFCache::start_cache($mod_name, self::$post_id, array('ID' => $module_ID))):

    $fields_default = array(
        'mod_title_menu' => '',
        'layout_menu' => '',
        'custom_menu' => '',
        'color_menu' => '',
        'according_style_menu' => '',
        'css_menu' => '',
        'animation_effect' => '',
        'menu_breakpoint' => '',
        'menu_slide_direction' => '',
		'allow_menu_fallback' => '',
        'allow_menu_breakpoint' => ''
    );

    if (isset($mod_settings['according_style_menu'])) {
        $mod_settings['according_style_menu'] = self::get_checkbox_data($mod_settings['according_style_menu']);
    }
    $fields_args = wp_parse_args($mod_settings, $fields_default);
    unset($mod_settings);
    $animation_effect = self::parse_animation_effect($fields_args['animation_effect'], $fields_args);

    $container_class = implode(' ', apply_filters('themify_builder_module_classes', array(
        'module', 'module-' . $mod_name, $module_ID, $fields_args['css_menu'], $animation_effect
                    ), $mod_name, $module_ID, $fields_args)
    );

    $container_props = array('id' => $module_ID, 'class' => $container_class);

    if ($fields_args['allow_menu_breakpoint'] !== '') {
		$container_props['data-menu-breakpoint'] = $fields_args['menu_breakpoint'];
		$container_props['data-menu-direction'] = $fields_args['menu_slide_direction'];
    }

    $container_props = apply_filters('themify_builder_module_container_props', $container_props, $fields_args, $mod_name, $module_ID);
    ?>

    <!-- module menu -->
    <div <?php echo self::get_element_attributes($container_props); ?>>
        <!--insert-->
        <?php if ($fields_args['mod_title_menu'] !== ''): ?>
            <?php echo $fields_args['before_title'] . apply_filters('themify_builder_module_title', $fields_args['mod_title_menu'], $fields_args). $fields_args['after_title']; ?>
        <?php endif; ?>

        <?php
        if ($fields_args['custom_menu'] !== '') {
			$args = array(
				'menu' => $fields_args['custom_menu'],
				'menu_class' => 'ui nav ' . $fields_args['layout_menu'] . ' ' . $fields_args['color_menu'] . ' ' . $fields_args['according_style_menu']
			);
			
			wp_nav_menu( $args );
        } else if ( ! empty( $fields_args['allow_menu_fallback'] ) ) {
			$args = array(
				'title_li'	=> '',
				'echo'		=> 0,
			);

			printf('<ul class="%1$s">%2$s</ul>'
				, 'ui nav ' . $fields_args['layout_menu'] . ' ' . $fields_args['color_menu'] . ' ' . $fields_args['according_style_menu']
				, wp_list_pages( $args ) );
		}
        ?>
    </div>
    <!-- /module menu -->
<?php endif; ?>
<?php TFCache::end_cache(); ?>