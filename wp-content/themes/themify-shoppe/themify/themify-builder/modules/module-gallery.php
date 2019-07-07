<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Module Name: Gallery
 * Description: Display WP Gallery Images
 */
class TB_Gallery_Module extends Themify_Builder_Component_Module {
	function __construct() {
		parent::__construct(array(
			'name' => __('Gallery', 'themify'),
			'slug' => 'gallery'
		));
	}

	public function get_options() {
		$columns = range( 0, 9 );
                $is_img_enabled = Themify_Builder_Model::is_img_php_disabled();
		$image_size = themify_get_image_sizes_list( false );
		return array(
			array(
				'id' => 'mod_title_gallery',
				'type' => 'text',
				'label' => __('Module Title', 'themify'),
				'class' => 'large',
                                'render_callback' => array(
                                        'live-selector'=>'.module-title'
				)
			),
			array(
				'id' => 'layout_gallery',
				'type' => 'radio',
				'label' => __('Gallery Layout', 'themify'),
				'options' => array(
					'grid' => __('Grid', 'themify'),
					'showcase' => __('Showcase', 'themify'),
					'lightboxed' => __('Lightboxed', 'themify'),
					'slider' => __( 'Slider', 'themify' )
				),
				'default' => 'grid',
				'option_js' => true
			),
			array(
				'id' => 'layout_masonry',
				'type' => 'checkbox',
				'label' => false,
				'pushed' => 'pushed',
				'options' => array(
					array( 'name' => 'masonry', 'value' => __( 'Use Masonry', 'themify' ) )
				),
				'wrap_with_class' => 'tb_group_element tb_group_element_grid'
			),
			array(
				'id' => 'thumbnail_gallery',
				'type' => 'image',
				'label' => __('Thumbnail', 'themify'),
				'class' => 'large',
				'wrap_with_class' => 'tb_group_element tb_group_element_lightboxed'
			),
			array(
				'id' => 'shortcode_gallery',
				'type' => 'textarea',
				'class' => 'tb_thumbs_preview tb_shortcode_input',
				'label' => __('Insert Gallery Shortcode', 'themify'),
				'help' => sprintf('<a href="#" class="builder_button tb_gallery_btn">%s</a>', __('Insert Gallery', 'themify'))
			),
			array(
				'id' => 'gallery_pagination',
				'type' => 'checkbox',
				'label' => __('Pagination', 'themify'),
				'wrap_with_class' => 'tb_group_element tb_group_element_grid',
				'options' => array(array( 'name' => 'pagination', 'value' =>'') ),
				'option_js' => true
			),
			array(
				'id' => 'gallery_per_page',
				'type' => 'text',
				'label' => __('Images per page', 'themify'),
				'wrap_with_class' => 'tb_group_element tb_group_element_grid tb-checkbox_element tb-checkbox_element_pagination',
				'class' => 'xsmall'
			),
			array(
				'id' => 'gallery_image_title',
				'type' => 'checkbox',
				'label' => __('Image Title', 'themify'),
				'options' => array(array( 'value' => __('Display library image title', 'themify'), 'name' =>'yes') )
			),
			array(
				'id' => 'gallery_exclude_caption',
				'type' => 'checkbox',
				'label' => __( 'Exclude Caption', 'themify' ),
				'options' => array(array( 'value' => __( 'Hide Image Caption', 'themify' ), 'name' =>'yes' ) ),
			),
			array(
				'id' => 's_image_w_gallery',
				'type' => 'text',
				'label' => __('Image Width', 'themify'),
				'class' => 'xsmall',
				'hide' => $is_img_enabled,
				'help' => 'px',
				'wrap_with_class' => 'tb_group_element tb_group_element_showcase tb_group_element_slider'
			),
			array(
				'id' => 's_image_h_gallery',
				'type' => 'text',
				'label' =>__('Image Height', 'themify'),
				'class' => 'xsmall',
				'hide' => $is_img_enabled,
				'help' => 'px',
				'wrap_with_class' => 'tb_group_element tb_group_element_showcase tb_group_element_slider'
			),
			array(
				'id' => 's_image_size_gallery',
				'type' => 'select',
				'label' => __('Main Image Size', 'themify'),
				'hide' => !$is_img_enabled,
				'options' => $image_size
			),
			array(
				'id' => 'thumb_w_gallery',
				'type' => 'text',
				'label' => __('Thumbnail Width', 'themify'),
				'class' => 'xsmall',
				'hide' => $is_img_enabled,
				'help' => 'px'
			),
			array(
				'id' => 'thumb_h_gallery',
				'type' => 'text',
				'label' =>__('Thumbnail Height', 'themify'),
				'class' => 'xsmall',
				'hide' => $is_img_enabled,
				'help' => 'px'
			),
			array(
				'id' => 'image_size_gallery',
				'type' => 'select',
				'label' => __('Image Size', 'themify'),
				'hide' => !$is_img_enabled,
				'options' => $image_size
			),
			array(
				'id' => 'gallery_columns',
				'type' => 'select',
				'label' =>__('Columns', 'themify'),
				'options' => $columns,
				'wrap_with_class' => 'tb_group_element tb_group_element_grid'
			),
			array(
				'id' => 'slider_option_slider',
				'type' => 'slider',
				'label' => __('Slider Options', 'themify'),
				'options' => array(
					array(
						'id' => 'visible_opt_slider',
						'type' => 'select',
						'default' => 4,
						'options' => array(2 => 2, 3, 4, 5, 6, 7),
						'help' => __('Visible Thumbnails', 'themify')
					),
					array(
						'id' => 'mob_visible_opt_slider',
						'type' => 'select',
						'options' => array('', 2, 3, 4),
						'help' => __( 'Mobile Visible Thumbnails', 'themify' )
					),
					array(
						'id' => 'auto_scroll_opt_slider',
						'type' => 'select',
						'default' => 4,
						'options' => array(
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
						),
						'help' => __('Auto Scroll', 'themify')
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
						'id' => 'show_arrow_slider',
						'type' => 'select',
						'help' => __('Show slider arrow buttons', 'themify'),
						'options' => array(
							'yes' => __('Yes', 'themify'),
							'no' => __('No', 'themify')
						),
						'binding' => array(
							'no' => array(
								'hide' => array('show_arrow_buttons_vertical')
							),
							'select'=>array(
								'value'=>'no',
								'show'=>array('show_arrow_buttons_vertical')
							)
						)
					),
					array(
						'id' => 'show_arrow_buttons_vertical',
						'type' => 'checkbox',
						'label' => false,
						'help' =>false,
						'wrap_with_class' => '',
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
					)
				),
				'wrap_with_class' => 'tb_group_element tb_group_element_slider'
			),
			array(
				'id' => 'link_opt',
				'type' => 'select',
				'label' => __('Link to', 'themify'),
				'options' => array(
					'post' => __('Attachment Page','themify'),
					'file' => __('Media File','themify'),
					'none' => __('None','themify')
				),
				'default' => __('Media File','themify'),
				'wrap_with_class' => 'tb_group_element tb_group_element_grid tb_group_element_slider',
				'binding' => array(
					'file' => array( 'show' => array( 'link_image_size' ) ),
					'post' => array( 'hide' => array( 'link_image_size' ) ),
					'none' => array( 'hide' => array( 'link_image_size' ) )
				)
			),
			array(
				'id' => 'link_image_size',
				'type' => 'select',
				'label' => __('Link to Image Size', 'themify'),
				'options' => $image_size,
				'default' => __( 'Original Image', 'themify' ),
				'wrap_with_class' => 'tb_group_element tb_group_element_grid tb_group_element_slider'
			),
			array(
				'id' => 'appearance_gallery',
				'type' => 'checkbox',
				'label' => __('Image Appearance', 'themify'),
				'options' => array(
					array( 'name' => 'rounded', 'value' => __('Rounded', 'themify')),
					array( 'name' => 'drop-shadow', 'value' => __('Drop Shadow', 'themify')),
					array( 'name' => 'bordered', 'value' => __('Bordered', 'themify')),
					array( 'name' => 'circle', 'value' => __('Circle', 'themify'), 'help' => __('(square format image only)', 'themify'))
				)
			),
			// Additional CSS
			array(
				'type' => 'separator',
				'meta' => array( 'html' => '<hr/>')
			),
			array(
				'id' => 'css_gallery',
				'type' => 'text',
				'label' => __('Additional CSS Class', 'themify'),
				'class' => 'large exclude-from-reset-field',
				'help' => sprintf( '<br/><small>%s</small>', __('Add additional CSS class(es) for custom styling (<a href="https://themify.me/docs/builder#additional-css-class" target="_blank">learn more</a>).', 'themify') )
			)
		);
	}

	public function get_default_settings() {
		return array(
			'gallery_columns' => 4,
			'layout_gallery'=>'grid',
			'thumb_w_gallery' => 300,
			'thumb_h_gallery' => 200
		);
	}
        
        public function get_visual_type() {
            return 'ajax';            
        }
        

	public function get_styling() {
		$general = array(
			// Background
                        self::get_seperator('image_bacground',__( 'Background', 'themify' ),false),
                        self::get_image('.module-gallery'),
                        self::get_color('.module-gallery', 'background_color',__( 'Background Color', 'themify' ),'background-color'),
						self::get_repeat('.module-gallery'),
						self::get_position('.module-gallery'),
			// Font
                        self::get_seperator('font',__('Font', 'themify')),
                        self::get_font_family('.module-gallery'),
                        self::get_element_font_weight('.module-gallery'),
						self::get_color_type('font_color_type',__('Font Color Type', 'themify'),'font_color','font_gradient_color'),
						self::get_color('.module-gallery','font_color',__('Font Color', 'themify'),'color',true),
						self::get_gradient_color('.module-gallery strong','font_gradient_color',__('Font Color', 'themify')),
                        self::get_font_size('.module-gallery'),
                        self::get_line_height('.module-gallery .gallery-caption'),
                        self::get_letter_spacing('.module-gallery'),
                        self::get_text_align('.module-gallery .gallery-caption'),
                        self::get_text_transform('.module-gallery'),
                        self::get_font_style('.module-gallery'),
                        self::get_text_decoration('.module-gallery .themify_image_title','text_decoration_regular'),
			// Link
                        self::get_seperator('link',__('Link', 'themify')),
                        self::get_color( '.module-gallery a','link_color'),
                        self::get_color('.module-gallery a:hover','link_color_hover',__('Color Hover', 'themify')),
                        self::get_text_decoration('.module-gallery a'),
			// Padding
                        self::get_seperator('padding',__('Padding', 'themify')),
                        self::get_padding('.module-gallery'),
			// Margin
                        self::get_seperator('margin',__('Margin', 'themify')),
                        self::get_margin('.module-gallery'),
                        // Border
                        self::get_seperator('border',__('Border', 'themify')),
                        self::get_border('.module-gallery')
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
					)
				)
			)
		);

	}

	/**
	 * Render plain content for static content.
	 * 
	 * @param array $module 
	 * @return string
	 */
	public function get_plain_content( $module ) {
		$mod_settings = wp_parse_args( $module['mod_settings'], array(
			'mod_title_gallery' => '',
			'shortcode_gallery' => ''
		) );
		$text =  '' !== $mod_settings['mod_title_gallery']?sprintf( '<h3>%s</h3>', $mod_settings['mod_title_gallery'] ):'';
		$text .= $mod_settings['shortcode_gallery'];
		return $text;
	}
}

///////////////////////////////////////
// Module Options
///////////////////////////////////////
Themify_Builder_Model::register_module( 'TB_Gallery_Module' );
