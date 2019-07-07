<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Module Name: Fancy Heading
 * Description: Heading with fancy styles
 */
class TB_Fancy_Heading_Module extends Themify_Builder_Component_Module {
	function __construct() {
                self::$texts['sub_heading'] =__('Sub Heading', 'themify');
                self::$texts['heading'] =__('Heading', 'themify');
		parent::__construct(array(
			'name' => __('Fancy Heading', 'themify'),
			'slug' => 'fancy-heading'
		));
	}
	

	public function get_options() {
                $aligment = Themify_Builder_Model::get_text_aligment();
		return array(
			array(
				'id' => 'heading',
				'type' => 'text',
				'label' => self::$texts['heading'],
				'class' => 'fullwidth',
				'render_callback' => array(
                                    'binding' => 'live',
                                    'live-selector'=>'.main-head'
				)
			),
			array(
				'id' => 'sub_heading',
				'type' => 'text',
				'label' => self::$texts['sub_heading'],
				'class' => 'fullwidth',
				'render_callback' => array(
                                    'binding' => 'live',
                                    'live-selector'=>'.sub-head'
				)
			),
			array(
				'id' => 'heading_tag',
				'label' => __( 'HTML Tag', 'themify' ),
				'type' => 'select',
				'options' => array(
					'h1' => 'h1',
					'h2' => 'h2',
					'h3' => 'h3'
				),
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
                                'id' => 'text_alignment',
				'label' => __( 'Text Alignment', 'themify' ),
                                'type' => 'icon_radio',
                                'options' => array(
                                        'themify-text-left' =>$aligment[0],
					'themify-text-center' =>$aligment[1],
					'themify-text-right' =>$aligment[2]
				),
                                'default' => 'themify-text-center',
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
				'id' => 'css_class',
				'type' => 'text',
				'label' => __('Additional CSS Class', 'themify'),
				'class' => 'large exclude-from-reset-field',
				'help' => sprintf( '<br/><small>%s</small>', __( 'Add additional CSS class(es) for custom styling (<a href="https://themify.me/docs/builder#additional-css-class" target="_blank">learn more</a>).', 'themify' ) ),
				'render_callback' => array(
					'binding' => 'live'
				)
			)
		);
	}

	public function get_default_settings() {
		return array(
			'heading' => self::$texts['heading'],
			'sub_heading' =>self::$texts['sub_heading']
		);
	}

	public function get_styling() {
		$general = array(
			// Background
                        self::get_seperator('image_bacground',__( 'Background', 'themify' ),false),
                        self::get_image('.module-fancy-heading'),
                        self::get_color('.module-fancy-heading', 'background_color',__( 'Background Color', 'themify' ),'background-color'),
			self::get_repeat('.module-fancy-heading'),
			self::get_position('.module-fancy-heading'),
			// Padding
                        self::get_seperator('padding',__('Padding', 'themify')),
                        self::get_padding('.module-fancy-heading'),
			// Margin
			self::get_seperator('margin',__('Margin', 'themify')),
                        self::get_margin('.module-fancy-heading'),
			// Border
                        self::get_seperator('border',__('Border', 'themify')),
                        self::get_border('.module-fancy-heading')
		);

		$heading = array(
			// Font
                        self::get_seperator('font',__('Font', 'themify'),false),
                        self::get_font_family('.module .main-head'),
                        self::get_element_font_weight('.module .main-head'),
			self::get_color_type('font_color_type',__('Font Color Type', 'themify'),'font_color','font_gradient_color'),
			self::get_color('.module .main-head','font_color',__('Font Color', 'themify'),'color',true),
			self::get_gradient_color('.module .main-head','font_gradient_color',__('Font Color', 'themify')),
                        self::get_font_size('.module .main-head'),
                        self::get_line_height('.module .main-head'),
                        self::get_letter_spacing('.module .main-head'),
			self::get_text_transform('.module-fancy-heading .fancy-heading .main-head', 'text_transform_maintitle'),
			self::get_font_style('.module-fancy-heading .fancy-heading .main-head', 'font_style_maintitle'),
			// Main Heading Margin
			self::get_heading_margin_multi_field( '.module-fancy-heading .fancy-heading .main-head','', 'top','main'),
			self::get_heading_margin_multi_field( '.module-fancy-heading .fancy-heading .main-head','', 'bottom','main')
		);

		$subheading = array(
			// Font
                        self::get_seperator('font',__('Font', 'themify'),false),
                        self::get_font_family('.module .sub-head','font_family_subheading'),
                        self::get_element_font_weight('.module .sub-head','font_weight_subheading'),
                        self::get_color('.module .sub-head','font_color_subheading',__('Font Color', 'themify')),
                        self::get_font_size('.module .sub-head','font_size_subheading'),
                        self::get_line_height('.module .sub-head','line_height_subheading'),
                        self::get_letter_spacing('.module .sub-head','letter_spacing_subheading'),
                        self::get_text_transform('.module-fancy-heading .fancy-heading .sub-head', 'text_transform_subtitle'),
                        self::get_font_style('.module-fancy-heading .fancy-heading .sub-head', 'font_style_subtitle','font_bold_subtitle'),
			// Sub Heading Margin
                        self::get_heading_margin_multi_field('.module-fancy-heading .fancy-heading .sub-head','', 'top','sub'),
                        self::get_heading_margin_multi_field('.module-fancy-heading .fancy-heading .sub-head','', 'bottom','sub')
		);

		$fh_divider = array(
			// Divider Top/Bottom Margin
                        self::get_heading_margin_multi_field('.module.module-fancy-heading .sub-head:before','', 'top','divider'),
                        self::get_heading_margin_multi_field('.module.module-fancy-heading .sub-head:before','', 'bottom','divider'),
			// Divider Border
                        self::get_seperator('border',__('Border', 'themify'),false),
                        self::get_border('.module.module-fancy-heading .sub-head:before','d_border'),
			// Divider Width
                        self::get_seperator('width',__('Width', 'themify'),false),
                        self::get_width('.module.module-fancy-heading .sub-head:before','d_width')
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
					'heading' => array(
						'label' => __('Heading', 'themify'),
						'fields' => $heading
					),
					'subheading' => array(
						'label' => __('Sub Heading', 'themify'),
						'fields' => $subheading
					),
					'fh_divider' => array(
						'label' => __('Divider', 'themify'),
						'fields' => $fh_divider
					)
				)
			)
		);
	}

	protected function _visual_template() { ?>
		<div class="module module-<?php echo $this->slug; ?> {{ data.css_class }}">
                        <!--insert-->
			<# 
			var heading_tag = _.isUndefined( data.heading_tag ) ? 'h1' : data.heading_tag,
				text_alignment = _.isUndefined( data.text_alignment ) ? 'themify-text-center' : data.text_alignment;
			#>
			<{{ heading_tag }} class="fancy-heading {{ text_alignment }}">
				<span class="main-head">{{{ data.heading }}}</span>
				<span class="sub-head">{{{ data.sub_heading }}}</span>
			</{{ heading_tag }}>
		</div>
	<?php
	}

	/**
	 * Render plain content for static content.
	 * 
	 * @param array $module 
	 * @return string
	 */
	public function get_plain_content( $module ) {
		$mod_settings = wp_parse_args( $module['mod_settings'], array(
			'heading' => '',
			'heading_tag' => 'h1',
			'sub_heading' => ''
		) );
		return sprintf('<%s>%s<br/>%s</%s>', $mod_settings['heading_tag'], $mod_settings['heading'], $mod_settings['sub_heading'], $mod_settings['heading_tag'] );
		
	}
}
///////////////////////////////////////
// Module Options
///////////////////////////////////////
Themify_Builder_Model::register_module( 'TB_Fancy_Heading_Module' );
