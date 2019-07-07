<?php
if (!defined('ABSPATH'))
	exit; // Exit if accessed directly
/**
 * Template Widget
 * 
 * Access original fields: $mod_settings
 * @author Themify
 */
if (TFCache::start_cache($mod_name, self::$post_id, array('ID' => $module_ID))):

	$fields_default = array(
		'mod_title_widget' => '',
		'class_widget' => '',
		'instance_widget' => array(),
		'custom_css_widget' => '',
		'background_repeat' => '',
		'animation_effect' => ''
	);
	$fields_args = wp_parse_args( $mod_settings, $fields_default );
	unset( $mod_settings );
	$animation_effect = self::parse_animation_effect( $fields_args['animation_effect'], $fields_args );

	$container_class = implode( ' ', apply_filters( 'themify_builder_module_classes', array(
		'module', 'module-' . $mod_name, $module_ID, $fields_args['custom_css_widget'], $fields_args['background_repeat'], $animation_effect
		), $mod_name, $module_ID, $fields_args )
	);
	$container_props = apply_filters('themify_builder_module_container_props', array(
		'id' => $module_ID,
		'class' => $container_class
	), $fields_args, $mod_name, $module_ID);

	// Backward compatibility with how widget data used to be saved.
	$fields_args['instance_widget'] = TB_Widget_Module::sanitize_widget_instance( $fields_args['instance_widget'] );
	?>

	<!-- module widget -->
	<div <?php echo self::get_element_attributes($container_props); ?>>
		<!--insert-->
		<?php
		if ( $fields_args['mod_title_widget'] !== '' ) {
			echo $fields_args['before_title'] . apply_filters( 'themify_builder_module_title', $fields_args['mod_title_widget'], $fields_args ). $fields_args['after_title'];
		}

		do_action( 'themify_builder_before_template_content_render' );
		if ( $fields_args['class_widget'] !== '' && class_exists( $fields_args['class_widget'] ) ) {
			the_widget( $fields_args['class_widget'], $fields_args['instance_widget'] );
		}
		do_action('themify_builder_after_template_content_render');
		?>
	</div><!-- /module widget -->
<?php endif; ?>
<?php TFCache::end_cache(); ?>