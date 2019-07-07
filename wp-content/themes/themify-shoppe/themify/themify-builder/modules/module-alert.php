<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Module Name: Alert
 * Description: Display Alert content
 */

class TB_alert_Module extends Themify_Builder_Component_Module {

	/**
	 * Declared in components/base.php
	 * Redeclared here to prevent errors in case of downgrade
	 */
    protected static $texts =  array();

	function __construct() {
		self::$texts['heading_alert'] =__('Alert Heading', 'themify');
		self::$texts['text_alert'] =__('Alert Text', 'themify');
		self::$texts['action_btn_text_alert'] =__('Action Button Text', 'themify');
		parent::__construct(array(
			'name' => __('Alert', 'themify'),
			'slug' => 'alert'
		));
	}

	public function get_plain_text( $module ) {
		$text = isset( $module['heading_alert'] )?$module['heading_alert']:'';
		if( isset( $module['text_alert'] ) ){
                    $text .= $module['text_alert'];
                }
		return $text;
	}
        
	public function get_options() {
                $appearance = Themify_Builder_Model::get_appearance();
                $colors = Themify_Builder_Model::get_colors();
                $colors[] = array('img' => 'transparent', 'value' => 'transparent', 'label' => __('Transparent', 'themify'));
		return array(
			array(
				'id' => 'mod_title_alert',
				'type' => 'text',
				'label' => __('Module Title', 'themify'),
				'class' => 'large',
				'render_callback' => array(
					'binding' => 'live',
                                        'live-selector'=>'.module-title'
				)
			),
			array(
				'id' => 'layout_alert',
				'type' => 'layout',
                                'mode'=>'sprite',
				'label' => __('Alert Style', 'themify'),
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
				'id' => 'heading_alert',
				'type' => 'text',
				'label' =>self::$texts ['heading_alert'],
				'class' => 'xlarge',
				'render_callback' => array(
                                    'binding' => 'live',
                                    'live-selector'=>'.alert-heading'
				)
			),
			array(
				'id' => 'text_alert',
				'type' => 'textarea',
				'label' => self::$texts['text_alert'],
				'class' => 'fullwidth',
				'render_callback' => array(
                                    'binding' => 'live'
				)
			),
			array(
				'id' => 'color_alert',
				'type' => 'layout',
                                'mode'=>'sprite',
                                'class'=>'tb_colors',
				'label' => __('Alert Color', 'themify'),
				'options' =>$colors,
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'appearance_alert',
				'type' => 'checkbox',
				'label' => __('Alert Appearance', 'themify'),
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
				'id' => 'action_btn_text_alert',
				'type' => 'text',
				'label' => self::$texts ['action_btn_text_alert'],
				'class' => 'medium',
				'render_callback' => array(
					'binding' => 'live',
					'live-selector' => '.builder_button'
				)
			),
			array(
				'id' => 'alert_button_action',
				'type' => 'select',
				'label' => __( 'Button Click Action', 'themify' ),
				'options' => array(
					'close' => __('Close alert box', 'themify'),
					'message' => __('Display a message', 'themify'),
					'url' => __('Go to URL', 'themify'),
				),
				'binding' => array(
					'close' => array( 'hide' => array( 'alert_message_text', 'action_btn_link_alert', 'open_link_new_tab_alert' ) ),
					'message' => array( 'show' => array( 'alert_message_text' ), 'hide' => array( 'action_btn_link_alert', 'open_link_new_tab_alert' ) ),
					'url' => array( 'show' => array( 'action_btn_link_alert', 'open_link_new_tab_alert' ), 'hide' => array( 'alert_message_text' ) )
				),
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'alert_message_text',
				'type' => 'textarea',
				'label' => __('Message text', 'themify'),
				'class' => 'xlarge',
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'action_btn_link_alert',
				'type' => 'text',
				'label' => __('Action Button Link', 'themify'),
				'class' => 'xlarge',
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'open_link_new_tab_alert',
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
				'id' => 'action_btn_color_alert',
				'type' => 'layout',
				'class' => 'tb_colors',
				'mode' => 'sprite',
				'label' => __('Action Button Color', 'themify'),
				'options' => $colors,
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'action_btn_appearance_alert',
				'type' => 'checkbox',
				'label' => __('Action Button Appearance', 'themify'),
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
				'id' => 'alert_no_date_limit',
				'type' => 'checkbox',
				'label' => __('Schedule Alert', 'themify'),
				'options' => array(
					array( 'name' => 'alert_schedule', 'value' => __( 'Enable', 'themify' ) )
				),
				'binding' => array(
					'checked' => array(
						'show' => array( 'alert_start_at', 'alert_end_at' )
					),
					'not_checked' => array(
						'hide' => array( 'alert_start_at', 'alert_end_at' )
					)
				),
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'alert_start_at',
				'type' => 'text',
				'label' => __('Start at', 'themify'),
				'datepicker' => true,
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'alert_end_at',
				'type' => 'text',
				'label' => __('End at', 'themify'),
				'datepicker' => true,
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'alert_show_to',
				'type' => 'select',
				'label' => __( 'Guest/Logged Users', 'themify' ),
				'options' => array(
					'' => __( 'Show to both users and guest visitors', 'themify' ),
					'guest' => __( 'Show only to guest visitors', 'themify' ),
					'user' => __( 'Show only to logged-in users', 'themify' )
				),
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'alert_limit_count',
				'type' => 'text',
				'label' => __( 'Limit display', 'themify' ),
				'after' => __( ' times only show the alert.', 'themify' ),
				'class' => 'small',
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'alert_auto_close',
				'type' => 'checkbox',
				'label' => __('Auto Close', 'themify'),
				'options' => array(
					array( 'name' => 'alert_close_auto', 'value' => __( 'Enable', 'themify' ) )
				),
				'binding' => array(
					'checked' => array(
						'show' => array( 'alert_auto_close_delay' )
					),
					'not_checked' => array(
						'hide' => array( 'alert_auto_close_delay' )
					)
				),
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'alert_auto_close_delay',
				'type' => 'text',
				'label' => __('Close Alert After', 'themify'),
				'after' => __( ' Seconds', 'themify' ),
				'class' => 'small',
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
				'id' => 'css_alert',
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
			'heading_alert' => esc_html__( 'Alert Heading', 'themify' ),
			'text_alert' => esc_html__( 'Alert Text', 'themify' ),
			'action_btn_text_alert' => esc_html__( 'Action button', 'themify' ),
			'action_btn_link_alert' => 'https://themify.me/',
			'action_btn_color_alert' => 'blue',
			'alert_auto_close_delay' => 5
		);
	}


	public function get_styling() {
		$general = array(
			// Background
                        self::get_seperator('image_bacground',__( 'Background', 'themify' ),false),
                        self::get_image('.module-alert'),
                        self::get_color('.module-alert', 'background_color',__( 'Background Color', 'themify' ),'background-color'),
                        self::get_repeat('.module-alert'),
                        self::get_position('.module-alert'),
			// Font
                        self::get_seperator('font',__('Font', 'themify')),
                        self::get_font_family(array( '.module-alert', '.module-alert .alert-button' )),
                        self::get_element_font_weight(array( '.module-alert', '.module-alert .alert-button' )),
						self::get_color_type('font_color_type',__('Font Color Type', 'themify'),'font_color','font_gradient_color'),
						self::get_color(array( '.module-alert', '.module-alert h1', '.module-alert h2', '.module-alert h3', '.module-alert h4', '.module-alert h5', '.module-alert h6', '.module-alert .alert-button' ),'font_color',__('Font Color', 'themify'),'color',true),
						self::get_gradient_color(array( '.module-alert p', '.module-alert h1', '.module-alert h2', '.module-alert h3', '.module-alert h4', '.module-alert h5', '.module-alert h6' ),'font_gradient_color',__('Font Color', 'themify')),
                        self::get_font_size('.module-alert'),
                        self::get_line_height('.module-alert'),
                        self::get_letter_spacing('.module-alert'),
                        self::get_text_align('.module-alert'),
                        self::get_text_transform('.module-alert'),
                        self::get_font_style('.module-alert'),
                        self::get_text_decoration(array( '.module-alert .alert-content', '.module-alert .alert-button a' ),'text_decoration_regular'),
			// Link
                        self::get_seperator('link',__('Link', 'themify')),
                        self::get_color( '.module-alert .alert-button a','link_color'),
                        self::get_color('.module-alert .alert-button a:hover','link_color_hover',__('Color Hover', 'themify')),
                        self::get_text_decoration('.module-alert a'),
			// Padding
                        self::get_seperator('padding',__('Padding', 'themify')),
                        self::get_padding('.module-alert'),
                    
			// Margin
						self::get_seperator('margin',__('Margin', 'themify')),
                        self::get_margin('.module-alert'),
			// Border
                        self::get_seperator('border',__('Border', 'themify')),
                        self::get_border('.module-alert')
		);

		$alert_title = array(
			// Font
                        self::get_seperator('font',__('Font', 'themify')),
                        self::get_font_family('.module-alert .alert-heading','f_f_a_t'),
                        self::get_element_font_weight('.module-alert .alert-heading','f_w_a_t'),
                        self::get_color('.module-alert .alert-heading','f_c_a_t',__('Font Color', 'themify')),
                        self::get_font_size('.module-alert .alert-heading','f_s_a_t'),
                        self::get_line_height('.module-alert .alert-heading','l_h_a_t'),
                        self::get_letter_spacing('.module-alert .alert-heading', 'l_s_a_t'),
                        self::get_text_transform('.module-alert .alert-heading', 't_t_a_t'),
                        self::get_font_style('.module-alert .alert-heading', 'f_s_a_t','f_s_a_b'),
			// Margin
                        self::get_seperator('margin',__('Margin', 'themify')),
                        self::get_margin('.module-alert .alert-heading', 'm_a_t')
		);

		$alert_button = array(
			// Background
                        self::get_seperator('image_bacground',__( 'Background', 'themify' ),false),
                        self::get_color('.module-alert .alert-button a', 'background_color_button',__( 'Background Color', 'themify' ),'background-color'),
                        self::get_color('.module-alert .alert-button a:hover', 'b_c_b_h',__( 'HoverBackground Color', 'themify' ),'background-color'),
			// Font
                        self::get_seperator('font',__('Font', 'themify')),
                        self::get_font_family('.module-alert .alert-button a','font_family_button'),
                        self::get_element_font_weight('.module-alert .alert-button a','font_weight_button'),
                        self::get_color('.module-alert .alert-button a','font_color_button',__('Font Color', 'themify')),
                        self::get_color('.module-alert .alert-button a:hover','font_color_button_hover',__('Color Hover', 'themify')),
                        self::get_font_size('.module-alert .alert-button a','font_size_button'),
                        self::get_line_height('.module-alert .alert-button a','line_height_button'),
			// Padding
						self::get_seperator('padding',__('Padding', 'themify')),
                        self::get_padding('.module-alert .alert-button a', 'p_a_b'),
			// Margin
						self::get_seperator('margin',__('Margin', 'themify')),
                        self::get_margin('.module-alert .alert-button a', 'm_a_b'),
			// Border
						self::get_seperator('border',__('Border', 'themify')),
                        self::get_border('.module-alert .alert-button a', 'b_a_b')
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
					'alert_title' => array(
						'label' => __('Alert Title', 'themify'),
						'fields' => $alert_title
					),
					'button' => array(
						'label' => __('Alert Button', 'themify'),
						'fields' => $alert_button
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
		<div class="module module-<?php echo $this->slug ; ?> {{ font_color_type }} ui {{ data.layout_alert }} {{ data.color_alert }} {{ data.css_alert }} {{ data.background_repeat }} <# ! _.isUndefined( data.appearance_alert ) ? print( data.appearance_alert.split('|').join(' ') ) : ''; #>">
			<!--insert-->
                        <# if ( data.mod_title_alert ) { #>
			<?php echo $module_args['before_title']; ?>{{{ data.mod_title_alert }}}<?php echo $module_args['after_title']; ?>
			<# } #>
			
			<div class="alert-inner">
				<div class="alert-content">
					<h3 class="alert-heading">{{{ data.heading_alert }}}</h3>
					<p>{{{ data.text_alert }}}</p>
				</div>
				
				<# if ( data.action_btn_text_alert ) { #>
					<div class="alert-button">
						<a href="{{ data.action_btn_link_alert }}" class="ui builder_button {{ data.action_btn_color_alert }} <# ! _.isUndefined( data.action_btn_appearance_alert ) ? print( data.action_btn_appearance_alert.split('|').join(' ') ) : ''; #>">
							{{{ data.action_btn_text_alert }}}
						</a>
                                </div>
				<# } #>
			</div>
			<div class="alert-close ti-close"></div>
		</div>
	<?php
	}
}

///////////////////////////////////////
// Module Options
///////////////////////////////////////
Themify_Builder_Model::register_module( 'TB_alert_Module' );
