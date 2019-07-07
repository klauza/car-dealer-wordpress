<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Module Name: Plain Text
 * Description: Display plain text
 */
class TB_Plain_Text_Module extends Themify_Builder_Component_Module {
	function __construct() {
                self::$texts['plain_text'] = __('Plain Text', 'themify');
		parent::__construct(array(
			'name' => __('Plain Text', 'themify'),
			'slug' => 'plain-text'
		));
	}

	public function get_plain_text( $module ) {
		return isset( $module['plain_text'] ) ? $module['plain_text'] : '';
	}

	public function get_options() {
		return array(
			array(
				'id' => 'plain_text',
				'type' => 'textarea',
				'class' => 'fullwidth',
				'label' => '&nbsp;',
				'rows' => 12,
                                'render_callback'=>array(
                                    'binding' => 'live',
                                    'event'=>'keyup'
                                )
			),
			// Additional CSS
			array(
				'type' => 'separator',
				'meta' => array( 'html' => '<hr/>')
			),
			array(
				'id' => 'add_css_text',
				'type' => 'text',
				'label' => __('Additional CSS Class', 'themify'),
				'class' => 'large exclude-from-reset-field',
				'help' => sprintf( '<br/><small>%s</small>', __( 'Add additional CSS class(es) for custom styling (<a href="https://themify.me/docs/builder#additional-css-class" target="_blank">learn more</a>).', 'themify' ) )
			)
		);

	}


	public function get_styling() {
		return array(
                        // Background
                        self::get_seperator('image_bacground',__( 'Background', 'themify' ),false),
                        self::get_image('.module-plain-text'),
                        self::get_color('.module-plain-text', 'background_color',__( 'Background Color', 'themify' ),'background-color'),
						self::get_repeat('.module-plain-text'),
						self::get_position('.module-plain-text'),
			// Font
                        self::get_seperator('font',__('Font', 'themify')),
                        self::get_font_family(array( '.module-plain-text', '.module-plain-text h1', '.module-plain-text h2', '.module-plain-text h3:not(.module-title)', '.module-plain-text h4', '.module-plain-text h5', '.module-plain-text h6' )),
                        self::get_element_font_weight(array( '.module-plain-text', '.module-plain-text h1', '.module-plain-text h2', '.module-plain-text h3:not(.module-title)', '.module-plain-text h4', '.module-plain-text h5', '.module-plain-text h6' )),
						self::get_color_type('font_color_type',__('Font Color Type', 'themify'),'font_color','font_gradient_color'),
						self::get_color(array( '.module-plain-text', '.module-plain-text h1', '.module-plain-text h2', '.module-plain-text h3:not(.module-title)', '.module-plain-text h4', '.module-plain-text h5', '.module-plain-text h6' ),'font_color',__('Font Color', 'themify'),'color',true),
						self::get_gradient_color(array( '.module-plain-text', '.module-plain-text h1', '.module-plain-text h2', '.module-plain-text h3:not(.module-title)', '.module-plain-text h4', '.module-plain-text h5', '.module-plain-text h6' ),'font_gradient_color',__('Font Color', 'themify')),
						self::get_font_size('.module-plain-text'),
                        self::get_line_height('.module-plain-text'),
                        self::get_letter_spacing('.module-plain-text'),
                        self::get_text_align('.module-plain-text'),
                        self::get_text_transform('.module-plain-text'),
                        self::get_font_style('.module-plain-text'),
                        self::get_text_decoration('.module-plain-text','text_decoration_regular'),
			// Link
                        self::get_seperator('link',__('Link', 'themify')),
                        self::get_color( '.module-plain-text a','link_color'),
                        self::get_color('.module-plain-text a:hover','link_color_hover',__('Color Hover', 'themify')),
                        self::get_text_decoration('.module-plain-text a'),
			// Padding
                        self::get_seperator('padding',__('Padding', 'themify')),
                        self::get_padding('.module-plain-text'),
			// Margin
                        self::get_seperator('margin',__('Margin', 'themify')),
                        self::get_margin('.module-plain-text'),
                        // Border
                        self::get_seperator('border',__('Border', 'themify')),
                        self::get_border('.module-plain-text')
		);

	}
        
	protected function _visual_template() { ?>
		<div class="module module-<?php echo $this->slug; ?> {{ data.add_css_text }}">
			{{{ data.plain_text }}}
		</div>
	<?php
	}
}

///////////////////////////////////////
// Module Options
///////////////////////////////////////
Themify_Builder_Model::register_module( 'TB_Plain_Text_Module' );
