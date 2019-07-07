<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Module Name: Widgetized
 * Description: Display any registered sidebar
 */
class TB_Widgetized_Module extends Themify_Builder_Component_Module {
	public function __construct() {
		parent::__construct(array(
			'name' => __('Widgetized', 'themify'),
			'slug' => 'widgetized'
		));

		add_action( 'themify_builder_lightbox_fields', array( $this, 'widgetized_fields' ), 10, 2 );
	}

	public function get_options() {
		return array(
			array(
				'id' => 'mod_title_widgetized',
				'type' => 'text',
				'label' => __('Module Title', 'themify'),
				'class' => 'large',
                                'render_callback' => array(
                                    'live-selector'=>'.module-title'
				)
			),
			array(
				'id' => 'sidebar_widgetized',
				'type' => 'widgetized_select',
				'label' => __('Widgetized Area', 'themify'),
				'class' => 'large'
			),
			// Additional CSS
			array(
				'type' => 'separator',
				'meta' => array( 'html' => '<hr/>' )
			),
			array(
				'id' => 'custom_css_widgetized',
				'type' => 'text',
				'label' => __('Additional CSS Class', 'themify'),
				'help' => sprintf( '<br/><small>%s</small>', __('Add additional CSS class(es) for custom styling (<a href="https://themify.me/docs/builder#additional-css-class" target="_blank">learn more</a>).', 'themify') ),
				'class' => 'large exclude-from-reset-field'
			)
		);
	}
        
        public function get_visual_type() {
            return 'ajax';            
        }
        
	public function get_styling() {
		$general = array(
			// Background
                        self::get_seperator('image_bacground',__( 'Background', 'themify' ),false),
                        self::get_image('.module-widgetized'),
                        self::get_color('.module-widgetized', 'background_color',__( 'Background Color', 'themify' ),'background-color'),
						self::get_repeat('.module-widgetized'),
						self::get_position('.module-widgetized'),
			// Font
                        self::get_seperator('font',__('Font', 'themify')),
                        self::get_font_family('.module-widgetized'),
                        self::get_element_font_weight('.module-widgetized'),
                        self::get_color('.module-widgetized','font_color',__('Font Color', 'themify')),
                        self::get_font_size('.module-widgetized'),
                        self::get_line_height('.module-widgetized'),
                        self::get_letter_spacing('.module-widgetized'),
                        self::get_text_align('.module-widgetized'),
                        self::get_text_transform('.module-widgetized'),
                        self::get_font_style('.module-widgetized'),
                        self::get_text_decoration('.module-widgetized','text_decoration_regular'),
			// Link
                        self::get_seperator('link',__('Link', 'themify')),
                        self::get_color( '.module-widgetized a','link_color'),
                        self::get_color('.module-widgetized a:hover','link_color_hover',__('Color Hover', 'themify')),
                        self::get_text_decoration('.module-widgetized a'),
			// Padding
                        self::get_seperator('padding',__('Padding', 'themify')),
                        self::get_padding('.module-widgetized'),
			// Margin
                        self::get_seperator('margin',__('Margin', 'themify')),
                        self::get_margin('.module-widgetized'),
			// Border
                        self::get_seperator('border',__('Border', 'themify')),
                        self::get_border('.module-widgetized')
		);

		$widgetized_container = array(
			// Background
                        self::get_seperator('image_bacground',__( 'Background', 'themify' ),false),
                        self::get_color('.module-widgetized .widget', 'b_c_c',__( 'Background Color', 'themify' ),'background-color'),
			// Padding
                        self::get_seperator('padding',__('Padding', 'themify')),
                        self::get_padding('.module-widgetized .widget','p_c'),
			// Margin
                        self::get_seperator('margin',__('Margin', 'themify')),
                        self::get_margin('.module-widgetized .widget','m_c'),
			// Border
                        self::get_seperator('border',__('Border', 'themify')),
                        self::get_border('.module-widgetized .widget','b_c')
		);

		$widgetized_title = array(
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
						'fields' =>  $this->module_title_custom_style()
					),
					'widgetized_title' => array(
						'label' => __('Widgetized Title', 'themify'),
						'fields' => $widgetized_title
					),
					'widgetized_container' => array(
						'label' => __('Widgetized Container', 'themify'),
						'fields' => $widgetized_container
					)
				)
			)
		);
	}

	function widgetized_fields($field, $mod_name) {
                if ( $mod_name !== 'widgetized' ){
                    return false;
                }
		global $wp_registered_sidebars;
		$output = '';
                if($field['type']==='widgetized_select'){
                    $output= '<div class="selectwrapper"><select name="'. esc_attr( $field['id'] ) .'" id="'. esc_attr( $field['id'] ) .'" class="tb_lb_option"'. themify_builder_get_control_binding_data( $field ) .'>';
                    $output .= '<option></option>';
                    foreach ( $wp_registered_sidebars as $v ) {
                            $output .= '<option value="'.esc_attr( $v['id'] ).'">'.esc_html( $v['name'] ).'</option>';
                    }
                    $output .= '</select></div>';
                }
		echo $output;
	}
}

///////////////////////////////////////
// Module Options
///////////////////////////////////////
Themify_Builder_Model::register_module( 'TB_Widgetized_Module' );
