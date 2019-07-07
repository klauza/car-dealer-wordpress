<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Module Name: Service Menu
 * Description: Display a Service item
 */
class TB_Service_Menu_Module extends Themify_Builder_Component_Module {
	function __construct() {
                self::$texts['title_service_menu'] = __('Menu Title', 'themify');
                self::$texts['description_service_menu'] =  __('Description', 'themify');
                self::$texts['price_service_menu'] =  __('Price', 'themify');
                self::$texts['highlight_text_service_menu'] = __('Highlight Text', 'themify');
		parent::__construct(array(
			'name' => __('Service Menu', 'themify'),
			'slug' => 'service-menu'
		));
	}
	public function get_options() {
                $is_img_enabled = Themify_Builder_Model::is_img_php_disabled();
		$image_sizes = !$is_img_enabled?themify_get_image_sizes_list( false ):array();
                $colors = Themify_Builder_Model::get_colors();
                $colors[] = array('img' => 'transparent', 'value' => 'transparent', 'label' => __('Transparent', 'themify'));
		return array(
			array(
				'id' => 'style_service_menu',
				'type' => 'layout',
				'label' => __('Menu Style', 'themify'),
                                'mode'=>'sprite',
				'options' => array(
					array('img' => 'image_top', 'value' => 'image-top', 'label' => __('Image Top', 'themify')),
					array('img' => 'image_left', 'value' => 'image-left', 'label' => __('Image Left', 'themify')),
					array('img' => 'image_center', 'value' => 'image-center', 'label' => __('Image Center', 'themify')),
					array('img' => 'image_right', 'value' => 'image-right', 'label' => __('Image Right', 'themify')),
					array('img' => 'image_overlay', 'value' => 'image-overlay', 'label' => __('Image Overlay', 'themify')),
					array('img' => 'image_horizontal', 'value' => 'image-horizontal', 'label' => __('Horizontal Image', 'themify'))
				)
			),
			array(
				'id' => 'title_service_menu',
				'type' => 'text',
				'label' => self::$texts['title_service_menu'],
				'class' => 'large',
                                'render_callback' => array(
                                    'live-selector'=>'.tb-menu-title'
                                )
			),
			array(
				'id' => 'description_service_menu',
				'type' => 'textarea',
				'label' =>self::$texts['description_service_menu'],
				'class' => 'fullwidth',
                                'render_callback' => array(
                                    'live-selector'=>'.tb-menu-description'
                                )
			),
			array(
				'id' => 'price_service_menu',
				'type' => 'text',
				'label' => self::$texts['price_service_menu'],
				'class' => 'small',
                                'render_callback' => array(
                                    'live-selector'=>'.tb-menu-price'
                                )
                                
			),
			array(
				'id' => 'image_service_menu',
				'type' => 'image',
				'label' => __('Image URL', 'themify'),
				'class' => 'xlarge'
			),
			array(
				'id' => 'appearance_image_service_menu',
				'type' => 'checkbox',
				'label' => __('Image Appearance', 'themify'),
				'options' => array(
					array( 'name' => 'rounded', 'value' => __('Rounded', 'themify')),
					array( 'name' => 'drop-shadow', 'value' => __('Drop Shadow', 'themify')),
					array( 'name' => 'bordered', 'value' => __('Bordered', 'themify')),
					array( 'name' => 'circle', 'value' => __('Circle', 'themify'), 'help' => __('(square format image only)', 'themify'))
				)
			),
			array(
				'id' => 'image_size_service_menu',
				'type' => 'select',
				'label' => __('Image Size', 'themify'),
				'hide' => !$is_img_enabled,
				'options' => $image_sizes
			),
			array(
				'id' => 'width_service_menu',
				'type' => 'text',
				'label' => __('Width', 'themify'),
				'class' => 'xsmall',
				'help' => 'px',
				'value' => ''
			),
			array(
				'id' => 'height_service_menu',
				'type' => 'text',
				'label' => __('Height', 'themify'),
				'class' => 'xsmall',
				'help' => 'px',
				'value' => ''
			),
			array(
				'id' => 'link_service_menu',
				'type' => 'text',
				'label' => __('Image Link', 'themify'),
				'class' => 'fullwidth',
				'binding' => array(
					'empty' => array(
						'hide' => array('link_options', 'image_zoom_icon', 'lightbox_size')
					),
					'not_empty' => array(
						'show' => array('link_options', 'image_zoom_icon', 'lightbox_size')
					)
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
				'option_js' => true
			),
			array(
				'id' => 'image_zoom_icon',
				'type' => 'checkbox',
				'label' => false,
				'pushed' => 'pushed',
				'options' => array(
					array( 'name' => 'zoom', 'value' => __( 'Show zoom icon', 'themify' ) )
				),
				'wrap_with_class' => 'tb_group_element tb_group_element_lightbox tb_group_element_newtab'
			),
			array(
				'id' => 'lightbox_size',
				'type' => 'multi',
				'label' => __('Lightbox Dimension', 'themify'),
				'fields' => array(
					array(
						'id' => 'lightbox_width',
						'type' => 'text',
						'label' => __( 'Width', 'themify' ),
						'value' => ''
					),
					array(
						'id' => 'lightbox_size_unit_width',
						'type' => 'select',
						'label' => __( 'Units', 'themify' ),
						'options' => array(
							'pixels' => __('px ', 'themify'),
							'percents' => __('%', 'themify')
						),
						'default' => 'pixels'
					),
					array(
						'id' => 'lightbox_height',
						'type' => 'text',
						'label' => __( 'Height', 'themify' ),
						'value' => ''
					),
					array(
						'id' => 'lightbox_size_unit_height',
						'type' => 'select',
						'label' => __( 'Units', 'themify' ),
						'options' => array(
							'pixels' => __('px ', 'themify'),
							'percents' => __('%', 'themify')
						),
						'default' => 'pixels'
					)
				),
				'wrap_with_class' => 'tb_group_element tb_group_element_lightbox'
			),

			array(
				'id' => 'highlight_service_menu',
				'type' => 'checkbox',
				'label' => __( 'Highlight', 'themify' ),
				'options' => array(
					array( 'name' => 'highlight', 'value' => __('Highlight this item', 'themify'), 'binding' => array(
						'checked' => array(
							'show' => array( 'highlight_text_service_menu', 'highlight_color_service_menu' )
						),
						'not_checked' => array(
							'hide' => array( 'highlight_text_service_menu', 'highlight_color_service_menu' )
						)
					) ),
				),
				'new_line' => false
			),
			array(
				'id' => 'highlight_text_service_menu',
				'type' => 'text',
				'label' => '&nbsp;',
				'after' => self::$texts['highlight_text_service_menu'],
				'class' => 'large',
                                'render_callback' => array(
                                    'live-selector'=>'.tb-highlight-text'
                                )
			),
			array(
				'id' => 'highlight_color_service_menu',
				'type' => 'layout',
				'label' => '&nbsp;',
                                'mode'=>'sprite',
                                'class'=>'tb_colors',
				'options' => $colors
			),
			// Additional CSS
			array(
				'type' => 'separator',
				'meta' => array( 'html' => '<hr/>')
			),
			array(
				'id' => 'css_service_menu',
				'type' => 'text',
				'label' => __('Additional CSS Class', 'themify'),
				'class' => 'large exclude-from-reset-field',
				'help' => sprintf( '<br/><small>%s</small>', __( 'Add additional CSS class(es) for custom styling (<a href="https://themify.me/docs/builder#additional-css-class" target="_blank">learn more</a>).', 'themify' ) )
			)
		);
	}

	public function get_default_settings() {
		return array(
			'title_service_menu' => esc_html__( 'Menu title', 'themify' ),
			'description_service_menu' => esc_html__( 'Description', 'themify' ),
			'price_service_menu' => '$200',
			'style_service_menu' => 'image-left',
			'image_service_menu' => 'https://themify.me/demo/themes/wp-content/uploads/addon-samples/menu-pizza.png',
			'width_service_menu' => 100
		);
	}
        
        public function get_visual_type() {
            return 'ajax';            
        }

	public function get_styling() {
		$general = array(
                        // Background
                        self::get_seperator('image_bacground',__( 'Background', 'themify' ),false),
                        self::get_color('.module-service-menu', 'background_color',__( 'Background Color', 'themify' ),'background-color'),
			// Font
                        self::get_seperator('font',__('Font', 'themify')),
                        self::get_font_family(array( '.module-service-menu .tb-image-content', '.module-service-menu .tb-image-title', '.module-service-menu .tb-image-title a' )),
                        self::get_element_font_weight(array( '.module-service-menu .tb-image-content', '.module-service-menu .tb-image-title', '.module-service-menu .tb-image-title a' )),
						self::get_color_type('font_color_type',__('Font Color Type', 'themify'),'font_color','font_gradient_color'),
						self::get_color(array( '.module-service-menu .tb-image-content', '.module-service-menu .tb-image-title', '.module-service-menu .tb-image-title a', '.module-service-menu h1', '.module-service-menu h2', '.module-service-menu h3:not(.module-title)', '.module-service-menu h4', '.module-service-menu h5', '.module-service-menu h6' ),'font_color',__('Font Color', 'themify'),'color',true),
						self::get_gradient_color(array( '.module-service-menu .tb-image-content .tb-menu-title', '.module-service-menu .tb-image-content .tb-menu-price', '.module-service-menu .tb-image-content .tb-menu-description' ),'font_gradient_color',__('Font Color', 'themify')),
						self::get_font_size('.module-service-menu .tb-image-content'),
                        self::get_line_height('.module-service-menu .tb-image-content'),
                        self::get_letter_spacing('.module-service-menu .tb-image-content'),
                        self::get_text_align('.module-service-menu .tb-image-content'),
                        self::get_text_transform('.module-service-menu .tb-image-content'),
                        self::get_font_style('.module-service-menu .tb-image-content'),
                        self::get_text_decoration('.module-service-menu .tb-image-content','text_decoration_regular'),
			// Link
                        self::get_seperator('link',__('Link', 'themify')),
                        self::get_color( '.module-service-menu a','link_color'),
                        self::get_color('.module-service-menu a:hover','link_color_hover',__('Color Hover', 'themify')),
                        self::get_text_decoration('.module-service-menu a'),
                        // Padding
                        self::get_seperator('padding',__('Padding', 'themify')),
                        self::get_padding('.module-service-menu'),
			// Margin
                        self::get_seperator('margin',__('Margin', 'themify')),
                        self::get_margin('.module-service-menu'),
                        // Border
                        self::get_seperator('border',__('Border', 'themify')),
                        self::get_border('.module-service-menu')
		);

		$menu_title = array(
			// Font
                        self::get_seperator('font',__('Font', 'themify'),false),
                        self::get_font_family('.module-service-menu .tb-menu-title','font_family_title'),
                        self::get_element_font_weight('.module-service-menu .tb-menu-title','font_weight_title'),
                        self::get_color('.module-service-menu .tb-menu-title','font_color_title',__('Font Color', 'themify')),
                        self::get_font_size('.module-service-menu .tb-menu-title','font_size_title'),
                        self::get_line_height('.module-service-menu .tb-menu-title','line_height_title'),
						self::get_letter_spacing('.module-service-menu .tb-menu-title', 'letter_spacing_title'),
						self::get_text_transform('.module-service-menu .tb-menu-title', 'text_transform_title'),
						self::get_font_style('.module-service-menu .tb-menu-title', 'font_style_title','font_title_bold'),
		);

		$menu_description = array(
			// Font
                        self::get_seperator('font',__('Font', 'themify'),false),
                        self::get_font_family('.module-service-menu .tb-menu-description','font_family_description'),
                        self::get_element_font_weight('.module-service-menu .tb-menu-description','font_weight_description'),
                        self::get_color('.module-service-menu .tb-menu-description','font_color_description',__('Font Color', 'themify')),
                        self::get_font_size('.module-service-menu .tb-menu-description','font_size_description'),
                        self::get_line_height('.module-service-menu .tb-menu-description','line_height_description')
		);

		$price = array(
			// Font
                        self::get_seperator('font',__('Font', 'themify'),false),
                        self::get_font_family('.module-service-menu .tb-menu-price','font_family_price'),
                        self::get_element_font_weight('.module-service-menu .tb-menu-price','font_weight_price'),
                        self::get_color('.module-service-menu .tb-menu-price','font_color_price',__('Font Color', 'themify')),
                        self::get_font_size('.module-service-menu .tb-menu-price','font_size_price'),
                        self::get_line_height('.module-service-menu .tb-menu-price','line_height_price'),
			// Margin
						self::get_heading_margin_multi_field( '.module-service-menu .tb-menu-price','', 'top','t_price' ),
						self::get_heading_margin_multi_field( '.module-service-menu .tb-menu-price','', 'bottom','b_price' ),
		);

		$highlight_text = array(
			// Background
                        self::get_seperator('image_bacground',__( 'Background', 'themify' ),false),
                        self::get_color('.module-service-menu .tb-highlight-text', 'background_color_highlight_text',__( 'Background Color', 'themify' ),'background-color'),
			// Font
                        self::get_seperator('font',__('Font', 'themify'),false),
                        self::get_font_family('.module-service-menu .tb-highlight-text','font_family_highlight_text'),
                        self::get_element_font_weight('.module-service-menu .tb-highlight-text','font_weight_highlight_text'),
                        self::get_color('.module-service-menu .tb-highlight-text','font_color_highlight_text',__('Font Color', 'themify')),
                        self::get_font_size('.module-service-menu .tb-highlight-text','font_size_highlight_text'),
                        self::get_line_height('.module-service-menu .tb-highlight-text','line_height_highlight_text'),
            // Padding
						self::get_seperator('padding', __('Padding', 'themify')),
						self::get_padding('.module-service-menu .tb-highlight-text', 'h_t_p'),
            // Margin
						self::get_seperator('margin', __('Margin', 'themify')),
						self::get_margin('.module-service-menu .tb-highlight-text', 'h_t_m'),
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
					'title' => array(
						'label' => __('Menu Title', 'themify'),
						'fields' => $menu_title
					),
					'caption' => array(
						'label' => __('Menu Description', 'themify'),
						'fields' => $menu_description
					),
					'price' => array(
						'label' => __('Price', 'themify'),
						'fields' => $price
					),
					'highlight_text' => array(
						'label' => __('Highlight Text', 'themify'),
						'fields' => $highlight_text
					)
				)
			)
		);

	}
}
///////////////////////////////////////
// Module Options
///////////////////////////////////////
Themify_Builder_Model::register_module( 'TB_Service_Menu_Module' );