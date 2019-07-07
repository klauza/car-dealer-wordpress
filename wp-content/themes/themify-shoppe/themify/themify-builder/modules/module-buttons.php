<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Module Name: Button
 * Description: Display Button content
 */

class TB_Buttons_Module extends Themify_Builder_Component_Module {
	function __construct() {
                self::$texts['label'] =__( 'Text', 'themify' );
		parent::__construct( array(
			'name' => __('Button', 'themify'),
			'slug' => 'buttons'
		));
	}

	public function get_title( $module ) {
		return  isset( $module['mod_settings']['mod_title_button'] ) ? wp_trim_words($module['mod_settings']['mod_title_button'], 100 ) : '';
	}

	public function get_options() {
		$colors = Themify_Builder_Model::get_colors();
		$colors[] = array('img' => 'transparent', 'value' => 'transparent', 'label' => __('Transparent', 'themify'));

		return  array(
			array(
				'id'=>'buttons_size',
				'label' => __( 'Size', 'themify' ),
				'type' => 'layout',
				'mode' => 'sprite',
				'options' => array(
					array('img' => 'normall_button', 'value' => 'normal', 'label' => __('Normal', 'themify')),
					array('img' => 'small_button', 'value' => 'small', 'label' => __('Small', 'themify')),
					array('img' => 'large_button', 'value' => 'large', 'label' => __('Large', 'themify')),
					array('img' => 'xlarge_button', 'value' => 'xlarge', 'label' => __('xLarge', 'themify')),
				),
				'render_callback' => array(
                    'binding' => 'live'
				)
			),
			array(
				'id'=>'buttons_shape',
				'type' => 'layout',
				'mode' => 'sprite',
				'label' => __( 'Shape', 'themify' ),
				'options' => array(
					array('img' => 'squared_button', 'value' => 'squared', 'label' => __('Squared', 'themify')),
					array('img' => 'circle_button', 'value' => 'circle', 'label' => __('Circle', 'themify')),
					array('img' => 'rounded_button', 'value' => 'rounded', 'label' => __('Rounded', 'themify')),
				),
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id'=>'buttons_style',
				'type' => 'layout',
				'mode' => 'sprite',
				'label' => __( 'Background', 'themify' ),
				'options' => array(
					array('img' => 'solid_button', 'value' => 'solid', 'label' => __('Solid', 'themify')),
					array('img' => 'outline_button', 'value' => 'outline', 'label' => __('Outline', 'themify')),
					array('img' => 'transparent_button', 'value' => 'transparent', 'label' => __('Transparent', 'themify')),
				),
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id'=>'display',
				'type' => 'layout',
				'mode' => 'sprite',
				'label' => __( 'Display', 'themify' ),
				'options' => array(
					array('img' => 'horizontal_button', 'value' => 'buttons-horizontal', 'label' => __('Horizontal', 'themify')),
					array('img' => 'vertical_button', 'value' => 'buttons-vertical', 'label' => __('Vertical', 'themify')),
				),
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'fullwidth_button',
				'type' => 'checkbox',
				'label' => __('Fullwidth Button', 'themify'),
				'options' => array(
					array( 'name' => 'buttons-fullwidth', 'value' => __('Display buttons fullwidth', 'themify') )
				),
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'nofollow_link',
				'type' => 'checkbox',
				'label' => __('Nofollow Link', 'themify'),
				'options' => array(
					array( 'name' => 'yes', 'value' => __('Enable nofollow (search engines won\'t crawl this link)', 'themify') )
				),
				'render_callback' => array(
					'binding' => 'live'
				)
			),
            array(
				'id' => 'download_link',
				'type' => 'checkbox',
				'label' => __('Download-able Link', 'themify'),
				'options' => array(
					array( 'name' => 'yes', 'value' => __('Download link as file', 'themify') )
				),
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'content_button',
				'type' => 'builder',
				'new_row_text'=>__('Add new button','themify'),
				'options' => array(
					array(
						'id' => 'label',
						'type' => 'text',
						'label' => self::$texts['label'],
						'class' => 'fullwidth',
						'render_callback' => array(
							'repeater' => 'content_button',
							'binding' => 'live',
                            'live-selector'=>'.builder_button span'
						)
					),
					array(
						'id' => 'link',
						'type' => 'text',
						'label' => __( 'Link', 'themify' ),
						'class' => 'fullwidth',
						'binding' => array(
							'empty' => array(
								'hide' => array('link_options', 'button_color')
							),
							'not_empty' => array(
								'show' => array('link_options', 'button_color')
							)
						),
						'render_callback' => array(
							'repeater' => 'content_button',
							'binding' => 'live'
						)
					),
					array(
						'id' => 'link_options',
						'type' => 'radio',
						'label' => __('Open Link In', 'themify'),
						'options' => array(
							'regular' => __('Same window', 'themify'),
							'lightbox' => __('Lightbox ', 'themify'),
							'newtab' => __('New tab ', 'themify')
						),
						'new_line' => false,
						'default' => 'regular',
						'option_js' => true,
						'wrap_with_class' => 'link_options',
						'render_callback' => array(
							'repeater' => 'content_button',
							'binding' => 'live'
						)
					),
					array(
						'id' => 'lightbox_size',
						'type' => 'multi',
						'label' => __('Lightbox Dimension', 'themify'),
						'render_callback' => array(
							'repeater' => 'content_button',
							'binding' => 'live'
						),
						'options' => array(
							array(
								'id' => 'lightbox_width',
								'type' => 'range',
								'label' => __( 'Width', 'themify' ),
								'value' => '',
								'render_callback' => array(
									'binding' => 'live'
								),
								'units' => array(
									'PX' => array(
										'min' => 0,
										'max' => 500,
									),
									'%' => array(
										'min' => 0,
										'max' => 100,
									)
								)
							),
							array(
								'id' => 'lightbox_height',
								'label' => __( 'Height', 'themify' ),
								'value' => '',
								'render_callback' => array(
									'binding' => 'live'
								),
								'type' => 'range',
								'units' => array(
									'PX' => array(
										'min' => 0,
										'max' => 500,
									),
									'%' => array(
										'min' => 0,
										'max' => 100,
									)
								)
							)
						),
						'wrap_with_class' => 'tb_group_element tb_group_element_lightbox lightbox_size'
					),
					array(
						'id' => 'button_container',
						'type' => 'multi',
						'label' => __( 'Color', 'themify' ),
						'wrap_with_class' => 'button_color',
						'options' => array(
							array(
								'id' => 'button_color_bg',
								'type' => 'layout',
								'label' =>'',
								'class' => 'tb_colors',
								'mode' => 'sprite',
								'options' => $colors,
								'bottom' => false,
								'wrap_with_class' => 'fullwidth',
								'render_callback' => array(
									'repeater' => 'content_button',
									'binding' => 'live'
								)
							)
						)
					),
					array(
						'id' => 'icon_container',
						'type' => 'multi',
						'label' => __('Icon', 'themify'),
						'wrap_with_class' => 'fullwidth',
						'options' => array(
							array(
								'id' => 'icon',
								'type' => 'text',
								'iconpicker' => true,
								'label' => '',
								'class' => 'fullwidth themify_field_icon',
								'wrap_with_class' => 'fullwidth',
								'binding' => array(
									'empty' => array(
										'hide' => array('icon_alignment')
									),
									'not_empty' => array(
										'show' => array('icon_alignment')
									)
								),
								'render_callback' => array(
									'repeater' => 'content_button',
									'binding' => 'live'
								)
							)
						)
					),
					array(
						'id' => 'icon_alignment',
						'type' => 'select',
						'label' => __( 'Icon Alignment', 'themify' ),
						'options' => array(
							'left' => __( 'Left', 'themify'),
							'right' => __( 'Right', 'themify')
						),
						'default' => 'left',
						'wrap_with_class' => 'icon_alignment',
						'render_callback' => array(
							'repeater' => 'content_button',
							'binding' => 'live'
					)
					)
				),
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			// Additional CSS
			array(
				'type' => 'separator',
				'meta' => array('html' => '<hr/>')
			),
			array(
				'id' => 'css_button',
				'type' => 'text',
				'label' => __('Additional CSS Class', 'themify'),
				'class' => 'large exclude-from-reset-field',
				'help' => sprintf('<br/><small>%s</small>', __('Add additional CSS class(es) for custom styling (<a href="https://themify.me/docs/builder#additional-css-class" target="_blank">learn more</a>).', 'themify')),
				'render_callback' => array(
					'binding' => 'live'
				)
			)
		);
	}

	public function get_default_settings() {
		return array(
			'content_button' => array(
				array( 
					'label' => esc_html__( 'Button Text', 'themify' ), 
					'link' => 'https://themify.me/',
					'link_options' => 'regular'
				)
			)
		);
	}
        

	public function get_styling() {
		$general = array(
			// Background
                        self::get_seperator('image_bacground',__( 'Background', 'themify' ),false),
                        self::get_image('.module.module-buttons'),
						self::get_color('.module.module-buttons', 'background_color',__( 'Background Color', 'themify' ),'background-color'),
						self::get_repeat('.module.module-buttons'),
						self::get_position('.module.module-buttons'),
                        // Font
                        self::get_seperator('font',__('Font', 'themify')),
                        self::get_font_family(' div.module-buttons'),
                        self::get_element_font_weight(' div.module-buttons'),
						self::get_color_type('font_color_type',__('Font Color Type', 'themify'),'font_color','font_gradient_color'),
						self::get_color(' .module-buttons-item a','font_color',__('Font Color', 'themify'),'color',true),
						self::get_gradient_color(' .module-buttons-item a span','font_gradient_color',__('Font Color', 'themify')),
                        self::get_font_size( array(' div.module-buttons i',' div.module-buttons a',' div.module-buttons span')),
                        self::get_line_height(array(' div.module-buttons i',' div.module-buttons a',' div.module-buttons span')),
                        self::get_letter_spacing(array(' div.module-buttons i',' div.module-buttons a',' div.module-buttons span')),
                        self::get_text_align(' div.module-buttons'),
                        self::get_text_transform(' div.module-buttons'),
                        self::get_font_style(' div.module-buttons'),
                        self::get_text_decoration(' div.module-buttons','text_decoration_regular'),
			// Padding
                        self::get_seperator('padding',__('Padding', 'themify')),
                        self::get_padding(' div.module-buttons'),
			// Margin
                        self::get_seperator('margin',__('Margin', 'themify')),
                        self::get_margin('.module-buttons'),
			// Border
                        self::get_seperator('border',__('Border', 'themify')),
                        self::get_border(' div.module-buttons')
		);

		$button_link = array(
			// Background
                        self::get_seperator('image_bacground',__( 'Background', 'themify' ),false),
                        self::get_color(' .module-buttons .module-buttons-item a', 'button_background_color',__( 'Background Color', 'themify' ),'background-color'),
                        self::get_color(' .module-buttons .module-buttons-item a:hover', 'button_hover_background_color',__( 'Background Hover', 'themify' ),'background-color'),
			
			// Link
                        self::get_seperator('link',__('Link', 'themify')),
                        self::get_color(' .module-buttons .module-buttons-item a', 'link_color'),
                        self::get_color(' .module-buttons .module-buttons-item a:hover', 'link_color_hover',__('Color Hover', 'themify')),
                        self::get_text_decoration(array(' .module-buttons .module-buttons-item a span')),
			// Padding
                        self::get_seperator('padding',__('Padding', 'themify')),
                        self::get_padding(' .module-buttons .module-buttons-item a','padding_link'),
			// Margin
                        self::get_seperator('margin',__('Margin', 'themify')),
                        self::get_margin(' .module-buttons .module-buttons-item a','link_margin'),
			// Border
                        self::get_seperator('border',__('Border', 'themify')),
                        self::get_border(' .module-buttons .module-buttons-item a','link_border')
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
					'button_link' => array(
						'label' => __('Button Link', 'themify'),
						'fields' => $button_link
					)
				)
			)
		);

	}

	protected function _visual_template() { ?>
        <# var downloadLink = ( data.download_link == 'yes' ) ? 'download' : ''; #>
		<div class="module module-<?php echo $this->slug; ?> {{ data.css_button }}">
                        <!--insert-->
			<# if ( data.content_button ) { #>
				<div class="module-<?php echo $this->slug; ?> {{ data.buttons_size }} {{ data.buttons_style }} {{ data.buttons_shape }}">
					<# _.each( data.content_button, function( item ) { #>

						<div class="module-buttons-item {{ data.fullwidth_button }} {{ data.display }}">
							<# if ( item.link ) { #>
							<a class="ui builder_button {{ item.button_color_bg }}" href="{{ item.link }}" {{downloadLink}}>
							<# }
							 if ( item.icon_alignment && item.icon_alignment === 'right' ) { #>

								<span>{{ item.label }}</span>

							<# if ( item.icon ) { #>
								<i class="<# print(themifybuilderapp.Utils.getIcon(item.icon))#>"></i>
							<# } 
							 } else { 

								 if ( item.icon ) { #>
								<i class="<# print(themifybuilderapp.Utils.getIcon(item.icon))#>"></i>
								<# } #>

								<span>{{ item.label }}</span>

							<# } 
							 if ( item.link ) { #>
							</a>
							<# } #>
						</div>

					<# } ); #>
				</div>
			<# } #>
		</div>
	<?php
	}
}

///////////////////////////////////////
// Module Options
///////////////////////////////////////
Themify_Builder_Model::register_module( 'TB_Buttons_Module' );
