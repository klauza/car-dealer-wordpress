<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Module Name: Widget
 * Description: Display any available widgets
 */
class TB_Widget_Module extends Themify_Builder_Component_Module {
	function __construct() {
		parent::__construct(array(
			'name' => __('Widget', 'themify'),
			'slug' => 'widget'
		));

		add_action( 'themify_builder_lightbox_fields', array( $this, 'widget_fields' ), 10, 2 );
		add_action( 'wp_ajax_tb_get_widget_items', array( $this, 'get_items' ) );
		add_action( 'wp_ajax_module_widget_get_form', array( $this, 'widget_get_form' ), 10 );
		add_action( 'themify_builder_data_before_construct', array( $this, 'themify_builder_data_before_construct' ), 10, 2 );
	}

	public function get_options() {
		return array(
			array(
				'id' => 'mod_title_widget',
				'type' => 'text',
				'label' => __('Module Title', 'themify'),
				'class' => 'large',
					'render_callback' => array(
					'live-selector'=>'.module-title'
				)
			),
			array(
				'id' => 'class_widget',
				'type' => 'widget_select',
				'label' => __('Select Widget', 'themify'),
				'render_callback'=>array('control_type'=>'widget_select')
			),
			array(
				'id' => 'instance_widget',
				'type' => 'widget_form',
				'label' => false
			),
			// Additional CSS
			array(
				'type' => 'separator',
				'meta' => array( 'html' => '<hr/>')
			),
			array(
				'id' => 'custom_css_widget',
				'type' => 'text',
				'label' => __('Additional CSS Class', 'themify'),
				'help' => sprintf( '<br/><small>%s</small>', __('Add additional CSS class(es) for custom styling (<a href="https://themify.me/docs/builder#additional-css-class" target="_blank">learn more</a>).', 'themify') ),
				'class' => 'large exclude-from-reset-field'
			)
		);
	}


	public function get_styling() {
		$general = array(
			// Background
			self::get_seperator('image_bacground',__( 'Background', 'themify' ),false),
			self::get_image('.module-widget'),
			self::get_color('.module-widget', 'background_color',__( 'Background Color', 'themify' ),'background-color'),
			self::get_repeat('.module-widget'),
			self::get_position('.module-widget'),
			// Font
			self::get_seperator('font',__('Font', 'themify')),
			self::get_font_family(array( '.module-widget', '.module-widget a' )),
			self::get_element_font_weight(array( '.module-widget', '.module-widget a' )),
			self::get_color(array( '.module-widget', '.module-widget a' ),'font_color',__('Font Color', 'themify')),
			self::get_font_size(array( '.module-widget', '.module-widget a' )),
			self::get_line_height(array( '.module-widget', '.module-widget a' )),
			self::get_letter_spacing('.module-widget'),
			self::get_text_align('.module-widget'),
			self::get_text_transform('.module-widget'),
			self::get_font_style('.module-widget'),
			self::get_text_decoration('.module-widget','text_decoration_regular'),
			// Link
			self::get_seperator('link',__('Link', 'themify')),
			self::get_color( '.module-widget a','link_color'),
			self::get_color('.module-widget a:hover','link_color_hover',__('Color Hover', 'themify')),
			self::get_text_decoration('.module-widget a'),
			// Padding
			self::get_seperator('padding',__('Padding', 'themify')),
			self::get_padding('.module-widget'),
			// Margin
			self::get_seperator('margin',__('Margin', 'themify')),
			self::get_margin('.module-widget'),
			// Border
			self::get_seperator('border',__('Border', 'themify')),
			self::get_border('.module-widget')
		);
		$widget_title = array(
			// Font
			self::get_seperator('font',__('Font', 'themify')),
			self::get_font_family( array(' .widgettitle'),'f_f_w_t'),
			self::get_element_font_weight( array(' .widgettitle'),'f_w_w_t'),
			self::get_color(array(' .widgettitle'),'f_c_w_t',__('Font Color', 'themify')),
			self::get_font_size(array(' .widgettitle'),'f_s_w_t'),
			self::get_line_height(array(' .widgettitle'),'l_h_w_t'),
			self::get_letter_spacing(' .widgettitle','l_s_w_t'),
			self::get_text_align(' .widgettitle','t_a_w_t'),
			self::get_text_transform(' .widgettitle','t_t_w_t'),
			self::get_font_style(' .widgettitle','f_sy_w_t','f_b_w_t'),
			self::get_text_decoration(' .widgettitle','t_d_w_t')
		);
		return array(
			array(
				'type' => 'tabs',
				'id' => 'module-styling',
				'tabs' => array(
					'general' => array(
						'label' => __( 'General', 'themify' ),
						'fields' => $general
					),
					'module-title' => array(
						'label' => __( 'Module Title', 'themify' ),
						'fields' => $this->module_title_custom_style()
					),
					'widget_title' => array(
						'label' => __( 'Widget Title', 'themify' ),
						'fields' => $widget_title
					)
				)
			)
		);
	}

	public function get_visual_type() {
		return 'ajax';            
	}

	public function get_items(){
		
		global $wp_widget_factory;
		$output = '';
		if(!empty($wp_widget_factory->widgets)){
			foreach ($wp_widget_factory->widgets as $class => $widget ) {
				$base = esc_attr( $widget->id_base );
				$output.='<div data-idbase="'.$base.'" data-value="'.esc_attr( $class ) .'" class="widget-tpl '.$base.'">';
				$output.='<div class="widget-title"><h3>'.$widget->name .'</h3></div>';
				if(!empty($widget->widget_options) && !empty($widget->widget_options['description'])){
					$output.='<div class="widget-description">'.$widget->widget_options['description'].'</div>';
				}
				$output.='</div>';
			}
		}
		die($output);
	}
		
	public function widget_fields( $field, $mod_name ) {
		if ( $mod_name !== 'widget' ){
			return;
		}
		switch ( $field['type'] ) {
			case 'widget_select':
				?>
				<div id="available-widgets-filter">
					<i class="fa fa-spin fa-spinner tb_loading_widgets"></i>
					<input type="text" id="widgets-search" placeholder="<?php esc_attr_e( 'Search widgets&hellip;' ) ?>"/>
				</div>
				<div id="available-widgets" tabindex="1" >
					<div id="<?php echo $field['id'] ?>" class="tb_lb_option tb_widget_select" data-validation="not_empty" data-error-msg="<?php esc_attr_e( 'Please select the Widget', 'themify' )?>" <?php echo themify_builder_get_control_binding_data( $field )?>></div>
				</div>
				<?php
			break;
			case 'widget_form':
			echo '<div id="'. $field['id'] .'" class="module-widget-form-container wp-core-ui tb_lb_option"'. themify_builder_get_control_binding_data( $field ) .'></div>';
			break;
		}
	}
		

	public function widget_get_form() {
		if ( ! wp_verify_nonce( $_POST['tb_load_nonce'], 'tb_load_nonce' ) ) die(-1);

		global $wp_widget_factory;
		require_once ABSPATH . 'wp-admin/includes/widgets.php';
		$widget_class = $_POST['load_class'];
		if ( $widget_class == '') die(-1);

		$instance = ! empty( $_POST['widget_instance'] ) && $_POST['widget_instance'] !== 'false' ? $_POST['widget_instance'] : array();
		$instance = TB_Widget_Module::sanitize_widget_instance( $instance );

		$widget = new $widget_class();
					   
		$widget->number = next_widget_id_number($_POST['id_base']);
		ob_start();
		$instance = stripslashes_deep($instance);
		$template = '';
		$src = array();

		if (empty($_POST['tpl_loaded']) && method_exists($widget, 'render_control_template_scripts')) {
			require_once ABSPATH . WPINC . '/media-template.php';
			ob_start();
			$widget->render_control_template_scripts();
			if ($widget->id_base !== 'text') {
				wp_print_media_templates();
			}

			$template = ob_get_contents();
			ob_end_clean();
			$widget->enqueue_admin_scripts();
			$type = str_replace('_', '-', $widget->id_base) . '-widget';
			if ($widget->id_base === 'text') {
					$type.= 's';
			}
			wp_enqueue_script($type);
			global $wp_scripts;
			if (isset($wp_scripts->registered[$type])) {
				$script = $wp_scripts->registered[$type];
				if ($widget->id_base !== 'text' && !empty($wp_scripts->registered[$type]->deps)) {
					foreach($wp_scripts->registered[$type]->deps as $deps) {
						$src[] = array('src'=>$this->resolve_script_path( $wp_scripts->registered[$deps]->src ));
					}
				}

				$src[] = array('src'=>$this->resolve_script_path( $script->src ),'extra'=>!empty($script->extra)?$script->extra:'');
			}
		}
		$widget->form($instance);
			   
		$form = ob_get_clean();
		$base_name = 'widget-' . $wp_widget_factory->widgets[$widget_class]->id_base . '\[' . $widget->number . '\]';
		$form = preg_replace("/{$base_name}/", '', $form); // remove extra names
		$form = str_replace(array(
			'[',
			']'
		) , '', $form); // remove extra [ & ] characters
		$widget->form = $form;
				
				/**
				 * The widget-id is not used to save widget data, it is however needed for compatibility
				 * with how core renders the module forms.
				 */
		$form = '<div class="widget open">
			<div class="widget-inside">
				<div class="form">
					<div class="widget-content">'
								.$form.
					'</div>
					<input type="hidden" class="id_base" name="id_base" value="' . esc_attr( $widget->id_base ) . '" />
					<input type="hidden" class="widget-id" name="widget-id" value="' . time() . '" />
					<input type="hidden" class="widget-class" name="widget-class" value="' . $widget_class . '" />
				</div>
			</div>
			<br/>
		</div>';
				
		global $wp_version;
		die( json_encode( array(
			'form' => $form,
			'template' => $template,
			'v' => $wp_version,
			'src' => $src
		) ));
	}

	public function resolve_script_path( $src ) {
		if ( ! ($guessurl = site_url() )) {
			$guessed_url = true;
			$guessurl = wp_guess_url();
		}
		$base_url = $guessurl;
		$content_url = defined( 'WP_CONTENT_URL' ) ? WP_CONTENT_URL : '';
		$default_dirs = array(
			'/wp-admin/js/',
			'/wp-includes/js/'
		);

		if ( ! preg_match( '|^(https?:)?//|', $src ) && ! ( $content_url && 0 === strpos( $src, $content_url ) ) ) {
			$src = $base_url . $src;
		}

		return $src;
	}

		
	/*
	 * Sanitize keys for widget fields
	 * This is required to provide backward compatibility with how widget data was saved.
	 *
	 * @return array
	 * @since 3.2.0
	 */
	public static function sanitize_widget_instance( $instance ) {
		if ( is_array( $instance ) ) {
			foreach ( $instance as $key => $val ) {
				preg_match( '/.*\[\d\]\[(.*)\]/', $key, $matches );
				if ( isset( $matches[1] ) ) {
					unset( $instance[ $key ] );
					$instance[ $matches[1] ] = $val;
				}
			}
		}

		return $instance;
	}
		
	/**
	 * Render plain content for static content.
	 * 
	 * @param array $module 
	 * @return string
	 */
	public function get_plain_content( $module ) {
		$mod_settings = wp_parse_args( $module['mod_settings'], array(
			'mod_title_widget' => '',
			'class_widget' => '',
			'instance_widget' => array(),
		) );
		$text = '';

		if ( '' !== $mod_settings['mod_title_widget'] ) 
			$text = sprintf( '<h3>%s</h3>', $mod_settings['mod_title_widget'] );

		if ( 'Themify_Twitter' === $mod_settings['class_widget'] ) {
			$mod_settings['instance_widget'] = self::sanitize_widget_instance( $mod_settings['instance_widget'] );
			$username = isset( $mod_settings['instance_widget']['username'] ) ? $mod_settings['instance_widget']['username'] : '';
			$text .= sprintf( '<p>https://twitter.com/%s</p>', $username );
			return $text;
		}

		if ( 'Themify_Social_Links' === $mod_settings['class_widget'] ) 
			return $this->_themify_social_links_plain_content();
		
		return parent::get_plain_content( $module );
	}

	private function _themify_social_links_plain_content() {
		if ( ! function_exists('themify_get_data') ) return;

		$data = themify_get_data();
		$pre = 'setting-link_';
		$out = '';

		$field_ids = isset( $data[$pre.'field_ids'] ) ? json_decode( $data[$pre.'field_ids'] ) : false;

		if ( is_array( $field_ids ) || is_object( $field_ids ) ) {
			$out .= '<ul>';
						$is_exist = function_exists( 'icl_t' );
			foreach($field_ids as $fid){

				$title_name = $pre.'title_'.$fid;

				if ( $is_exist ) {
					$title_val = icl_t('Themify', $title_name, $data[$title_name]);
				} else {
					$title_val = isset($data[$title_name])? $data[$title_name] : '';
				}

				$link_name = $pre.'link_'.$fid;
				$link_val = isset($data[$link_name])? trim( $data[$link_name] ) : '';
				if ( '' === $link_val ) {
					continue;
				}
								$out .= sprintf('<li><a href="%s">%s</a></li>',esc_url( $link_val ),$title_val);
			}
			$out .= '</ul>';
		}
		return $out;
	}

	/**
	 * Before Builder saves data, find all Widget modules and call
	 * WP_Widget::update() method on widget instance data.
	 *
	 * @return array
	 */
	public function themify_builder_data_before_construct( $builder_data, $post_id ) {
		if ( isset( $builder_data[0] ) && is_object( $builder_data[0] ) ) {
			$builder_data = json_decode( json_encode( $builder_data ), True );
		}
		foreach ( $builder_data as $row_index => $row ) {
			if ( ! empty( $row['cols'] ) ) {
				foreach ( $row['cols'] as $col_index => $column ) {
					if (  ! empty( $column['modules'] ) ) {
						foreach ( $column['modules'] as $module_index => $module ) {
							if (  ! empty( $module['cols'] ) ) {
								foreach ( $module['cols'] as $sub_column_index => $sub_column ) {
									if  (! empty( $sub_column['modules'] ) ) {
										foreach ( $sub_column['modules'] as $sub_module_index => $sub_module ) {
											$builder_data[ $row_index ]['cols'][ $col_index ]['modules'][ $module_index ]['cols'][ $sub_column_index ]['modules'][ $sub_module_index ] = $this->call_widget_update( $builder_data[ $row_index ]['cols'][ $col_index ]['modules'][ $module_index ]['cols'][ $sub_column_index ]['modules'][ $sub_module_index ] );
										}
									}
								}
							}
							$builder_data[ $row_index ]['cols'][ $col_index ]['modules'][ $module_index ] = $this->call_widget_update( $builder_data[ $row_index ]['cols'][ $col_index ]['modules'][ $module_index ] );
						}
					}
				}
			}
		}

		return $builder_data;
	}

	/**
	 * Takes a $module array, for "widget" modules will call WP_Widget::update() method
	 * on the widget instance data
	 *
	 * @return array
	 */
	private function call_widget_update( $module ) {
		if ( isset( $module['mod_name'] ) && $module['mod_name'] === 'widget' ) {
			$widget_class = $module['mod_settings']['class_widget'];
			global $wp_widget_factory;
			if ( isset( $wp_widget_factory->widgets[ $widget_class ] ) ) {
				$module['mod_settings']['instance_widget'] = $wp_widget_factory->widgets[ $widget_class ]->update( $module['mod_settings']['instance_widget'], array() );
			}
		}

		return $module;
	}
}

///////////////////////////////////////
// Module Options
///////////////////////////////////////
Themify_Builder_Model::register_module( 'TB_Widget_Module' );
