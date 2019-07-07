<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Module Name: Callout
 * Description: Display Callout content
 */

class TB_Callout_Module extends Themify_Builder_Component_Module {
	function __construct() {
                self::$texts['heading_callout'] =__('Callout Heading', 'themify');
                self::$texts['text_callout'] =__('Callout Text', 'themify');
                self::$texts['action_btn_text_callout'] =__('Action Button Text', 'themify');
		parent::__construct(array(
			'name' => __('Callout', 'themify'),
			'slug' => 'callout'
		));
	}

	public function get_plain_text( $module ) {
		$text = '';
		if( isset( $module['heading_callout'] ) ){
                    $text = $module['heading_callout'];
                }
		if( isset( $module['text_callout'] ) ){
                    $text .= $module['text_callout'];
                }
		return $text;
	}
        
	public function get_options() {
                $appearance = Themify_Builder_Model::get_appearance();
                $colors = Themify_Builder_Model::get_colors();
                $colors[] = array('img' => 'transparent', 'value' => 'transparent', 'label' => __('Transparent', 'themify'));
		return array(
			array(
				'id' => 'mod_title_callout',
				'type' => 'text',
				'label' => __('Module Title', 'themify'),
				'class' => 'large',
				'render_callback' => array(
					'binding' => 'live',
                                        'live-selector'=>'.module-title'
				)
			),
			array(
				'id' => 'layout_callout',
				'type' => 'layout',
                                'mode'=>'sprite',
				'label' => __('Callout Style', 'themify'),
				'options' => array(
					array('img' => 'callout_button_right', 'value' => 'button-right', 'label' => __('Button Right', 'themify')),
					array('img' => 'callout_button_left', 'value' => 'button-left', 'label' => __('Button Left', 'themify')),
					array('img' => 'callout_button_bottom', 'value' => 'button-bottom', 'label' => __('Button Bottom', 'themify')),
					array('img' => 'callout_button_bottom_center', 'value' => 'button-bottom-center', 'label' => __('Button Bottom Center', 'themify'))
				),
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'heading_callout',
				'type' => 'text',
				'label' =>self::$texts ['heading_callout'],
				'class' => 'xlarge',
				'render_callback' => array(
                                    'binding' => 'live',
                                    'live-selector'=>'.callout-heading'
				)
			),
			array(
				'id' => 'text_callout',
				'type' => 'textarea',
				'label' => self::$texts['text_callout'],
				'class' => 'fullwidth',
				'render_callback' => array(
                                    'binding' => 'live'
				)
			),
			array(
				'id' => 'color_callout',
				'type' => 'layout',
                                'mode'=>'sprite',
                                'class'=>'tb_colors',
				'label' => __('Callout Color', 'themify'),
				'options' =>$colors,
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'appearance_callout',
				'type' => 'checkbox',
				'label' => __('Callout Appearance', 'themify'),
				'options' => $appearance,
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'type' => 'separator',
				'meta' => array( 'html' => '<hr/>')
			),
			array(
				'id' => 'action_btn_link_callout',
				'type' => 'text',
				'label' => __('Action Button Link', 'themify'),
				'class' => 'xlarge',
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'open_link_new_tab_callout',
				'type' => 'radio',
				'label' => __('Open Link', 'themify'),
				'options' => array(
					'no' => __('Same Window', 'themify'),
					'yes' => __('New Window', 'themify')
				),
                                'default'=>'no',
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'action_btn_text_callout',
				'type' => 'text',
				'label' => self::$texts ['action_btn_text_callout'],
				'class' => 'medium',
				'render_callback' => array(
                                    'binding' => 'live',
                                    'live-selector'=>'.builder_button'
				)
			),
			array(
				'id' => 'action_btn_color_callout',
				'type' => 'layout',
                                'class'=>'tb_colors',
                                'mode'=>'sprite',
				'label' => __('Action Button Color', 'themify'),
				'options' => $colors,
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'action_btn_appearance_callout',
				'type' => 'checkbox',
				'label' => __('Action Button Appearance', 'themify'),
				'options' => $appearance,
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			// Additional CSS
			array(
				'type' => 'separator',
				'meta' => array( 'html' => '<hr/>')
			),
			array(
				'id' => 'css_callout',
				'type' => 'text',
				'label' => __('Additional CSS Class', 'themify'),
				'class' => 'large exclude-from-reset-field',
				'help' => sprintf( '<br/><small>%s</small>', __('Add additional CSS class(es) for custom styling (<a href="https://themify.me/docs/builder#additional-css-class" target="_blank">learn more</a>).', 'themify') ),
				'render_callback' => array(
					'binding' => 'live'
				)
			)
		);
	}

	public function get_default_settings() {
		return array(
			'heading_callout' => esc_html__( 'Callout Heading', 'themify' ),
			'text_callout' => esc_html__( 'Callout Text', 'themify' ),
			'action_btn_text_callout' => esc_html__( 'Action button', 'themify' ),
			'action_btn_link_callout' => 'https://themify.me/',
			'action_btn_color_callout' => 'blue'
		);
	}


	public function get_styling() {
		$general = array(
			// Background
                        self::get_seperator('image_bacground',__( 'Background', 'themify' ),false),
                        self::get_image('.module-callout'),
                        self::get_color('.module-callout', 'background_color',__( 'Background Color', 'themify' ),'background-color'),
						self::get_repeat('.module-callout'),
						self::get_position('.module-callout'),
			// Font
                        self::get_seperator('font',__('Font', 'themify')),
                        self::get_font_family(array( '.module-callout', '.module-callout .callout-button' )),
                        self::get_element_font_weight(array( '.module-callout', '.module-callout .callout-button' )),
						self::get_color_type('font_color_type',__('Font Color Type', 'themify'),'font_color','font_gradient_color'),
						self::get_color(array( '.module-callout', '.module-callout h1', '.module-callout h2', '.module-callout h3', '.module-callout h4', '.module-callout h5', '.module-callout h6', '.module-callout .callout-button' ),'font_color',__('Font Color', 'themify'),'color',true),
						self::get_gradient_color(array( '.module-callout p', '.module-callout h1', '.module-callout h2', '.module-callout h3', '.module-callout h4', '.module-callout h5', '.module-callout h6' ),'font_gradient_color',__('Font Color', 'themify')),
						self::get_font_size('.module-callout'),
                        self::get_line_height('.module-callout'),
                        self::get_letter_spacing('.module-callout'),
                        self::get_text_align('.module-callout'),
                        self::get_text_transform('.module-callout'),
                        self::get_font_style('.module-callout'),
                        self::get_text_decoration('.module-callout','text_decoration_regular'),
			// Link
                        self::get_seperator('link',__('Link', 'themify')),
                        self::get_color( '.module-callout .callout-button a','link_color'),
                        self::get_color('.module-callout .callout-button a:hover','link_color_hover',__('Color Hover', 'themify')),
                        self::get_text_decoration('.module-callout a'),
			// Padding
                        self::get_seperator('padding',__('Padding', 'themify')),
                        self::get_padding('.module-callout'),
                    
			// Margin
			self::get_seperator('margin',__('Margin', 'themify')),
                        self::get_margin('.module-callout'),
			// Border
                        self::get_seperator('border',__('Border', 'themify')),
                        self::get_border('.module-callout')
		);

		$callout_title = array(
			// Font
                        self::get_seperator('font',__('Font', 'themify')),
                        self::get_font_family('.module-callout .callout-heading','font_family_alert_title'),
                        self::get_element_font_weight('.module-callout .callout-heading','font_weight_alert_title'),
                        self::get_color('.module-callout .callout-heading','font_color_alert_title',__('Font Color', 'themify')),
                        self::get_color('.module-callout .callout-heading:hover','font_color_alert_title_hover',__('Color Hover', 'themify')),
                        self::get_font_size('.module-callout .callout-heading','font_size_alert_title'),
                        self::get_line_height('.module-callout .callout-heading','line_height_alert_title'),
                        self::get_letter_spacing('.module-callout .callout-heading', 'letter_spacing_alert_title'),
                        self::get_text_transform('.module-callout .callout-heading', 'text_transform_title'),
                        self::get_font_style('.module-callout .callout-heading', 'font_style_title','font_title_bold')
		);

		$callout_button = array(
			// Background
                        self::get_seperator('image_bacground',__( 'Background', 'themify' ),false),
                        self::get_color('.module-callout .callout-button a', 'background_color_button',__( 'Background Color', 'themify' ),'background-color'),
						self::get_color('.module-callout .callout-button a:hover', 'b_c_b_h',__( 'Hover Background Color', 'themify' ),'background-color'),
			// Font
                        self::get_seperator('font',__('Font', 'themify')),
                        self::get_font_family('.module-callout .callout-button a','font_family_button'),
                        self::get_element_font_weight('.module-callout .callout-button a','font_weight_button'),
                        self::get_color('.module-callout .callout-button a','font_color_button',__('Font Color', 'themify')),
                        self::get_color('.module-callout .callout-button a:hover','font_color_button_hover',__('Color Hover', 'themify')),
                        self::get_font_size('.module-callout .callout-button a','font_size_button'),
                        self::get_line_height('.module-callout .callout-button a','line_height_button')
		);

		return array(
			array(
				'type' => 'tabs',
				'id' => 'module-styling',
				'tabs' => array(
					'general' => array(
						'label' => __('General', 'themify'),
						'fields' => $general
					),
					'module-title' => array(
						'label' => __( 'Module Title', 'themify' ),
						'fields' => $this->module_title_custom_style()
					),
					'callout_title' => array(
						'label' => __('Callout Title', 'themify'),
						'fields' => $callout_title
					),
					'button' => array(
						'label' => __('Callout Button', 'themify'),
						'fields' => $callout_button
					)
				)
			)
		);

	}

	protected function _visual_template() {
		$module_args = $this->get_module_args(); ?>
		<#
		var font_color_type = '';
		if(themifybuilderapp.activeModel != null){
			var tempData = themifybuilderapp.Forms.serialize('tb_options_styling');
			font_color_type = ('font_color_type' in  tempData && tempData['font_color_type'].indexOf('gradient') !== -1)?'gradient':'solid';
			font_color_type = 'tb-font-color-' + font_color_type;
		}
		#>
		<div class="module module-<?php echo $this->slug ; ?> {{ font_color_type }} ui {{ data.layout_callout }} {{ data.color_callout }} {{ data.css_callout }} {{ data.background_repeat }} <# ! _.isUndefined( data.appearance_callout ) ? print( data.appearance_callout.split('|').join(' ') ) : ''; #>">
			<!--insert-->
                        <# if ( data.mod_title_callout ) { #>
			<?php echo $module_args['before_title']; ?>{{{ data.mod_title_callout }}}<?php echo $module_args['after_title']; ?>
			<# } #>
			
			<div class="callout-inner">
				<div class="callout-content">
					<h3 class="callout-heading">{{{ data.heading_callout }}}</h3>
					<p>{{{ data.text_callout }}}</p>
				</div>
				
				<# if ( data.action_btn_text_callout ) { #>
					<div class="callout-button">
						<a href="{{ data.action_btn_link_callout }}" class="ui builder_button {{ data.action_btn_color_callout }} <# ! _.isUndefined( data.action_btn_appearance_callout ) ? print( data.action_btn_appearance_callout.split('|').join(' ') ) : ''; #>">
							{{{ data.action_btn_text_callout }}}
						</a>
                                </div>
				<# } #>
			</div>			
		</div>
	<?php
	}
}

///////////////////////////////////////
// Module Options
///////////////////////////////////////
Themify_Builder_Model::register_module( 'TB_Callout_Module' );
