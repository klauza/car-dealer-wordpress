<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Module Name: Testimonial Slider
 * Description: Display testimonial custom post type
 */
class TB_Testimonial_Slider_Module extends Themify_Builder_Component_Module {
	function __construct() {
		parent::__construct(array(
			'name' => __('Testimonial Slider', 'themify'),
			'slug' => 'testimonial-slider'
		));
		
	}

	public function get_title( $module ) {
		$type = isset( $module['mod_settings']['type_query_testimonial'] ) ? $module['mod_settings']['type_query_testimonial'] : 'category';
		$category = isset( $module['mod_settings']['category_testimonial'] ) ? $module['mod_settings']['category_testimonial'] : '';
		$slug_query = isset( $module['mod_settings']['query_slug_testimonial'] ) ? $module['mod_settings']['query_slug_testimonial'] : '';

		if ( 'category' === $type ) {
			return sprintf( '%s : %s', __('Category', 'themify'), $category );
		} else {
			return sprintf( '%s : %s', __('Slugs', 'themify'), $slug_query );
		}
	}

	public function get_options() {
		$visible_opt = array(1 => 1, 2, 3, 4, 5, 6, 7);
		$auto_scroll_opt = array(
			'off' => __( 'Off', 'themify' ),
			1 => __( '1 sec', 'themify' ),
			2 => __( '2 sec', 'themify' ),
			3 => __( '3 sec', 'themify' ),
			4 => __( '4 sec', 'themify' ),
			5 => __( '5 sec', 'themify' ),
			6 => __( '6 sec', 'themify' ),
			7 => __( '7 sec', 'themify' ),
			8 => __( '8 sec', 'themify' ),
			9 => __( '9 sec', 'themify' ),
			10 => __( '10 sec', 'themify' ),
			15 => __( '15 sec', 'themify' ),
			20 => __( '20 sec', 'themify' )
		);
		return array(
			array(
				'id' => 'mod_title_testimonial',
				'type' => 'text',
				'label' => __('Module Title', 'themify'),
				'class' => 'large',
                                'render_callback' => array(
                                    'live-selector'=>'.module-title'
                                )
			),
			array(
				'id' => 'layout_testimonial',
				'type' => 'layout',
				'label' => __('Testimonial Slider Layout', 'themify'),
                                'mode'=>'sprite',
				'options' => array(
					array('img' => 'testimonials_image_top', 'value' => 'image-top', 'label' => __('Image Top', 'themify')),
					array('img' => 'testimonials_image_bottom', 'value' => 'image-bottom', 'label' => __('Image Bottom', 'themify')),
					array('img' => 'testimonials_image_bubble', 'value' => 'image-bubble', 'label' => __('Image Bubble', 'themify'))
				)
			),
			array(
				'id' => 'tab_content_testimonial',
				'type' => 'builder',
				'options' => array(
					array(
						'id' => 'title_testimonial',
						'type' => 'text',
						'label' => __('Testimonial Title', 'themify'),
						'class' => 'fullwidth',
						'render_callback' => array(
                                                    'repeater' => 'tab_content_testimonial',
                                                    'live-selector'=>'.testimonial-title'
						)
					),
					array(
						'id' => 'content_testimonial',
						'type' => 'wp_editor',
						'label' => false,
						'class' => 'fullwidth',
						'rows' => 6,
						'render_callback' => array(
                                                    'repeater' => 'tab_content_testimonial'
						)
					),
					array(
						'id' => 'person_picture_testimonial',
						'type' => 'image',
						'label' => __('Person Picture', 'themify'),
						'class' => 'xlarge',
						'render_callback' => array(
							'repeater' => 'tab_content_testimonial'
						)
					),
					array(
						'id' => 'person_name_testimonial',
						'type' => 'text',
						'label' => __('Person Name', 'themify'),
						'class' => 'fullwidth',
						'render_callback' => array(
                                                    'repeater' => 'tab_content_testimonial',
                                                    'live-selector'=>'.person-name'
						)
					),
					array(
						'id' => 'person_position_testimonial',
						'type' => 'text',
						'label' => __('Person Position', 'themify'),
						'class' => 'fullwidth',
						'render_callback' => array(
                                                    'repeater' => 'tab_content_testimonial',
                                                    'live-selector'=>'.person-position'
						)
					),
					array(
						'id' => 'company_testimonial',
						'type' => 'text',
						'label' => __('Company', 'themify'),
						'class' => 'fullwidth',
						'render_callback' => array(
                                                    'repeater' => 'tab_content_testimonial',
                                                    'live-selector'=>'.person-company'
						)
					),
					array(
						'id' => 'company_website_testimonial',
						'type' => 'text',
						'label' => __('Company Website', 'themify'),
						'class' => 'fullwidth',
						'render_callback' => array(
                                                    'repeater' => 'tab_content_testimonial'
						)
					)
				)
			),
			array(
				'id' => 'img_w_slider',
				'type' => 'text',
				'label' => __('Image Width', 'themify'),
                                'default'=>100,
				'class' => 'xsmall',
				'help' => 'px'
			),
			array(
				'id' => 'img_h_slider',
				'type' => 'text',
				'label' => __('Image Height', 'themify'),
                                'default'=>100,
				'class' => 'xsmall',
				'help' => 'px'
			),
			array(
				'id' => 'slider_option_testimonial',
				'type' => 'slider',
				'label' => __('Slider Options', 'themify'),
				'options' => array(
					array(
						'id' => 'visible_opt_slider',
						'type' => 'select',
						'default' => 1,
						'options' => $visible_opt,
						'help' => __('Visible', 'themify')
					),
					array(
						'id' => 'mob_visible_opt_slider',
						'type' => 'select',
						'options' => array('', 1, 2, 3, 4),
						'help' => __( 'Mobile Visible', 'themify' )
					),
					array(
						'id' => 'auto_scroll_opt_slider',
						'type' => 'select',
						'default' => 4,
						'options' => $auto_scroll_opt,
						'help' => __('Auto Scroll', 'themify')
					),
					array(
						'id' => 'scroll_opt_slider',
						'type' => 'select',
						'options' => $visible_opt,
						'help' => __('Scroll', 'themify')
					),
					array(
						'id' => 'speed_opt_slider',
						'type' => 'select',
						'options' => array(
							'normal' => __('Normal', 'themify'),
							'fast' => __('Fast', 'themify'),
							'slow' => __('Slow', 'themify')
						),
						'help' => __('Speed', 'themify')
					),
					array(
						'id' => 'effect_slider',
						'type' => 'select',
						'options' => array(
							'scroll' => __('Slide', 'themify'),
							'fade' => __('Fade', 'themify'),
							'crossfade' => __('Cross Fade', 'themify'),
							'cover' => __('Cover', 'themify'),
							'cover-fade' => __('Cover Fade', 'themify'),
							'uncover' => __('Uncover', 'themify'),
							'uncover-fade' => __('Uncover Fade', 'themify'),
							'continuously' => __('Continuously', 'themify')
						),
						'help' => __('Effect', 'themify')
					),
					array(
						'id' => 'pause_on_hover_slider',
						'type' => 'select',
						'options' => array(
							'resume' => __('Yes', 'themify'),
							'false' => __('No', 'themify')
						),
						'help' => __('Pause On Hover', 'themify')
					),
					array(
						'id' => 'wrap_slider',
						'type' => 'select',
						'help' => __('Wrap', 'themify'),
						'options' => array(
							'yes' => __('Yes', 'themify'),
							'no' => __('No', 'themify')
						)
					),
					array(
						'id' => 'show_nav_slider',
						'type' => 'select',
						'help' => __('Show slider pagination', 'themify'),
						'options' => array(
							'yes' => __('Yes', 'themify'),
							'no' => __('No', 'themify')
						)
					),
					array(
						'id' => 'show_arrow_slider',
						'type' => 'select',
						'help' => __('Show slider arrow buttons', 'themify'),
						'options' => array(
							'yes' => __('Yes', 'themify'),
							'no' => __('No', 'themify')
						)
					),
                                        array(
						'id' => 'show_arrow_buttons_vertical',
						'type' => 'checkbox',
                                                'label' => false,
						'help' =>false,
                                                'wrap_with_class'=>'',
						'options' => array(
							array( 'name' => 'vertical', 'value' =>__('Display arrow buttons vertical middle on the left/right side', 'themify') )
						)
					),
					array(
						'id' => 'left_margin_slider',
						'type' => 'text',
						'class' => 'xsmall',
						'unit' => 'px',
						'help' => __('Left margin space between slides', 'themify')
					),
					array(
						'id' => 'right_margin_slider',
						'type' => 'text',
						'class' => 'xsmall',
						'unit' => 'px',
						'help' => __('Right margin space between slides', 'themify')
					),
					array(
						'id' => 'height_slider',
						'type' => 'select',
						'options' => array(
							'variable' => __('Variable', 'themify'),
							'auto' => __('Auto', 'themify')
						),
						'help' => __('Height <small class="description">"Auto" measures the highest slide and all other slides will be set to that size. "Variable" makes every slide has it\'s own height.</small>', 'themify')
					)
				)
			),							
			// Additional CSS
			array(
				'type' => 'separator',
				'meta' => array( 'html' => '<hr/>')
			),
			array(
				'id' => 'css_testimonial',
				'type' => 'text',
				'label' => __('Additional CSS Class', 'themify'),
				'class' => 'large exclude-from-reset-field',
				'help' => sprintf( '<br/><small>%s</small>', __('Add additional CSS class(es) for custom styling (<a href="https://themify.me/docs/builder#additional-css-class" target="_blank">learn more</a>).', 'themify') )
			)
		);
	}

	public function get_default_settings() {
		return array(
			'layout_testimonial' => 'image-top',
                        'img_h_slider'=>100,
                        'img_w_slider'=>100,
			'tab_content_testimonial' => array(
				array( 
					'title_testimonial' => esc_html__( 'Optional Title', 'themify' ), 
					'content_testimonial' => esc_html__( 'Testimonial content', 'themify' ),
					'person_name_testimonial' => 'John Smith',
					'person_position_testimonial' => 'CEO',
					'company_testimonial' => 'X-corporation'
				)
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
                        self::get_color('.module-testimonial-slider', 'background_color',__( 'Background Color', 'themify' ),'background-color'),
			// Font
                        self::get_seperator('font',__('Font', 'themify')),
                        self::get_font_family(array('.module-testimonial-slider .testimonial-content','.module-testimonial-slider .testimonial-content .testimonial-title', '.module-testimonial-slider .testimonial-content .testimonial-title a')),
                        self::get_element_font_weight(array('.module-testimonial-slider .testimonial-content','.module-testimonial-slider .testimonial-content .testimonial-title', '.module-testimonial-slider .testimonial-content .testimonial-title a')),
						self::get_color_type('font_color_type',__('Font Color Type', 'themify'),'font_color','font_gradient_color'),
						self::get_color( array('.module-testimonial-slider .testimonial-content', '.module-testimonial-slider .testimonial-content h1', '.module-testimonial-slider .testimonial-content h2', '.module-testimonial-slider .testimonial-content h3', '.module-testimonial-slider .testimonial-content h4', '.module-testimonial-slider .testimonial-content h5', '.module-testimonial-slider .testimonial-content h6', '.module-testimonial-slider .testimonial-content .testimonial-title', '.module-testimonial-slider .testimonial-content .testimonial-title a'),'font_color',__('Font Color', 'themify'),'color',true),
						self::get_gradient_color(array( '.module-testimonial-slider .testimonial-content h3','.module-testimonial-slider .testimonial-content .testimonial-content-main p','.module-testimonial-slider .testimonial-content .testimonial-content-main h1','.module-testimonial-slider .testimonial-content .testimonial-content-main h2','.module-testimonial-slider .testimonial-content .testimonial-content-main h3','.module-testimonial-slider .testimonial-content .testimonial-content-main h4','.module-testimonial-slider .testimonial-content .testimonial-content-main h5','.module-testimonial-slider .testimonial-content .testimonial-content-main h6','.module-testimonial-slider .testimonial-content .person-name','.module-testimonial-slider .testimonial-content .person-position','.module-testimonial-slider .testimonial-content .person-company > *' ),'font_gradient_color',__('Font Color', 'themify')),
                        self::get_font_size('.module-testimonial-slider .testimonial-content'),
                        self::get_line_height('.module-testimonial-slider .testimonial-content'),
                        self::get_letter_spacing('.module-testimonial-slider .testimonial-content'),
                        self::get_text_align('.module-testimonial-slider .testimonial-content'),
                        self::get_text_transform('.module-testimonial-slider .testimonial-content'),
                        self::get_font_style('.module-testimonial-slider .testimonial-content'),
                        self::get_text_decoration('.module-testimonial-slider .testimonial-content','text_decoration_regular'),
			// Link
                        self::get_seperator('link',__('Link', 'themify')),
                        self::get_color( '.module-testimonial-slider a','link_color'),
                        self::get_color('.module-testimonial-slider a:hover','link_color_hover',__('Color Hover', 'themify')),
                        self::get_text_decoration('.module-testimonial-slider a'),
			// Padding
                        self::get_seperator('padding',__('Padding', 'themify')),
                        self::get_padding('.module-testimonial-slider'),
			// Margin
                        self::get_seperator('margin',__('Margin', 'themify')),
                        self::get_margin('.module-testimonial-slider'),
                        // Border
                        self::get_seperator('border',__('Border', 'themify')),
                        self::get_border('.module-testimonial-slider')
		);

		$testimonial_title = array(
			// Font
                        self::get_seperator('font',__('Font', 'themify'),false),
                        self::get_font_family( array('.module-testimonial-slider .testimonial-content .testimonial-title', '.module-testimonial-slider .testimonial-content .testimonial-title a'),'font_family_title'),
                        self::get_element_font_weight( array('.module-testimonial-slider .testimonial-content .testimonial-title', '.module-testimonial-slider .testimonial-content .testimonial-title a'),'font_weight_title'),
                        self::get_color(array('.module-testimonial-slider .testimonial-content .testimonial-title', '.module-testimonial-slider .testimonial-content .testimonial-title a'),'font_color_title',__('Font Color', 'themify')),
                        self::get_font_size( '.module-testimonial-slider .testimonial-content .testimonial-title','font_size_title'),
                        self::get_line_height('.module-testimonial-slider .testimonial-content .testimonial-title','line_height_title'),
						self::get_letter_spacing('.module-testimonial-slider .testimonial-content .testimonial-title', 'letter_spacing_title'),
						self::get_text_transform('.module-testimonial-slider .testimonial-content .testimonial-title', 'text_transform_title'),
						self::get_font_style('.module-testimonial-slider .testimonial-content .testimonial-title', 'font_style_title','font_title_bold'),
		);

		$testimonial_content = array(
			// Background
                        self::get_seperator('content_background',__( 'Background', 'themify' ),false),
                        self::get_color('.module-testimonial-slider .testimonial-entry-content', 'background_color_content',__( 'Background Color', 'themify' ),'background-color'),
			// Font
						self::get_seperator('font',__('Font', 'themify')),
                        self::get_font_family('.module-testimonial-slider .testimonial-entry-content','font_family_content'),
                        self::get_element_font_weight('.module-testimonial-slider .testimonial-entry-content','font_weight_content'),
                        self::get_color('.module-testimonial-slider .testimonial-entry-content','font_color_content',__('Font Color', 'themify')),
                        self::get_font_size('.module-testimonial-slider .testimonial-entry-content','font_size_content'),
                        self::get_line_height('.module-testimonial-slider .testimonial-entry-content','line_height_content'),
			// Padding
                        self::get_seperator('padding',__('Padding', 'themify')),
                        self::get_padding('.module-testimonial-slider .testimonial-entry-content', 'content_padding'),
			// Border
                        self::get_seperator('border',__('Border', 'themify')),
                        self::get_border('.module-testimonial-slider .testimonial-entry-content', 'content_border')
		);
		
		$testimonial_container = array(
			// Background
                        self::get_seperator('container_background',__( 'Background', 'themify' ),false),
                        self::get_color('.module-testimonial-slider .testimonial-content', 'b_c_container',__( 'Background Color', 'themify' ),'background-color'),
			// Padding
                        self::get_seperator('padding',__('Padding', 'themify')),
                        self::get_padding('.module-testimonial-slider .testimonial-content', 'p_container'),
			// Border
                        self::get_seperator('border',__('Border', 'themify')),
                        self::get_border('.module-testimonial-slider .testimonial-content', 'b_container')
		);

		$person_info = array(
			// Font
                        self::get_seperator('font',__('Person Name Font', 'themify'),false),
                        self::get_font_family( array('.module-testimonial-slider .testimonial-content .person-name'),'font_family_person_name'),
                        self::get_element_font_weight( array('.module-testimonial-slider .testimonial-content .person-name'),'font_weight_person_name'),
                        self::get_color(array('.module-testimonial-slider .testimonial-content .person-name'),'font_color_person_name',__('Font Color', 'themify')),
                        self::get_font_size( '.module-testimonial-slider .testimonial-content .person-name','font_size_person_name'),
                        self::get_line_height('.module-testimonial-slider .testimonial-content .person-name','line_height_person_name'),
                        self::get_text_transform('.module-testimonial-slider .testimonial-content .person-name','text_transform_person_name'),
                        self::get_font_style('.module-testimonial-slider .testimonial-content .person-name','font_person_name','font_person_name_bold'),

			// Font
                        self::get_seperator('font',__('Person Position Font', 'themify'),false),
                        self::get_font_family( array('.module-testimonial-slider .testimonial-content .person-position'),'font_family_person_position'),
                        self::get_element_font_weight( array('.module-testimonial-slider .testimonial-content .person-position'),'font_weight_person_position'),
                        self::get_color(array('.module-testimonial-slider .testimonial-content .person-position'),'font_color_person_position',__('Font Color', 'themify')),
                        self::get_font_size( '.module-testimonial-slider .testimonial-content .person-position','font_size_person_position'),
                        self::get_line_height('.module-testimonial-slider .testimonial-content .person-position','line_height_person_position'),
                        self::get_text_transform('.module-testimonial-slider .testimonial-content .person-position','text_transform_person_position'),
                        self::get_font_style('.module-testimonial-slider .testimonial-content .person-position','font_person_position','font_person_position_bold'),

			// Font
                        self::get_seperator('font',__('Person Company Font', 'themify'),false),
                        self::get_font_family( array('.module-testimonial-slider .testimonial-content .person-company'),'font_family_company'),
                        self::get_element_font_weight( array('.module-testimonial-slider .testimonial-content .person-company'),'font_weight_company'),
                        self::get_color(array('.module-testimonial-slider .testimonial-content .person-company'),'font_color_company',__('Font Color', 'themify')),
                        self::get_font_size( '.module-testimonial-slider .testimonial-content .person-company','font_size_company'),
                        self::get_line_height('.module-testimonial-slider .testimonial-content .person-company','line_height_company'),
                        self::get_text_transform('.module-testimonial-slider .testimonial-content .person-company','text_transform_company'),
                        self::get_font_style('.module-testimonial-slider .testimonial-content .person-company','font_company','font_company_bold')
		);

		$controls = array(
			// Arrows
                        self::get_seperator('image_background',__('Arrows', 'themify'),false),
                        self::get_color('.themify_builder_slider_wrap .carousel-prev,.themify_builder_slider_wrap .carousel-next','background_color_arrows_controls',__('Background Color', 'themify'),'background-color'),
                        self::get_color('.themify_builder_slider_wrap .carousel-prev:hover,.themify_builder_slider_wrap .carousel-next:hover','background_color_hover_arrows_controls',__('Hover Background Color', 'themify'),'background-color'),
						self::get_color(array( '.themify_builder_slider_wrap .carousel-prev::before,.themify_builder_slider_wrap .carousel-next::before' ),'font_color_arrows_controls',__('Color', 'themify')),
						self::get_color(array( '.themify_builder_slider_wrap .carousel-prev:hover::before,.themify_builder_slider_wrap .carousel-next:hover::before' ),'font_color_arrows_controls_hover',__('Hover Color', 'themify')),
			// Pager
                        self::get_seperator('image_background',__('Pager', 'themify'),false),
						self::get_color(array( '.themify_builder_slider_wrap .carousel-pager a' ),'font_color_pager_controls',__('Color', 'themify')),
						self::get_color(array( '.themify_builder_slider_wrap .carousel-pager a:hover','.themify_builder_slider_wrap .carousel-pager a.selected' ),'font_color_hover_pager_controls',__('Hover Color', 'themify'))
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
					'container' => array(
						'label' => __( 'Testimonial Container', 'themify' ),
						'fields' => $testimonial_container
					),
					'title' => array(
						'label' => __('Testimonial Title', 'themify'),
						'fields' => $testimonial_title
					),
					'content' => array(
						'label' => __('Testimonial Content', 'themify'),
						'fields' => $testimonial_content
					),
					'person_info' => array(
						'label' => __('Person Info', 'themify'),
						'fields' => $person_info
					),
					'controls' => array(
						'label' => __('Slider Controls', 'themify'),
						'fields' => $controls
					)
				)
			)
		);

	}
}

///////////////////////////////////////
// Module Options
///////////////////////////////////////

Themify_Builder_Model::register_module( 'TB_Testimonial_Slider_Module' );

