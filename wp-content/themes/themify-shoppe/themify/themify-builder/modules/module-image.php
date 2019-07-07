<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Module Name: Image
 * Description: Display Image content
 */

class TB_Image_Module extends Themify_Builder_Component_Module {

    function __construct() {
        self::$texts['caption_image'] = __('Image Caption', 'themify');
        self::$texts['title_image'] = __('Image Title', 'themify');
        parent::__construct(array(
            'name' => __('Image', 'themify'),
            'slug' => 'image'
        ));
    }

    public function get_options() {
        $is_img_enabled = Themify_Builder_Model::is_img_php_disabled();
        $image_sizes = $is_img_enabled ? themify_get_image_sizes_list(false) : array();

        return array(
            array(
                'id' => 'mod_title_image',
                'type' => 'text',
                'label' => __('Module Title', 'themify'),
                'class' => 'large',
                'render_callback' => array(
                    'binding' => 'live',
                    'live-selector' => '.module-title'
                )
            ),
            array(
                'id' => 'style_image',
                'type' => 'layout',
                'label' => __('Image Style', 'themify'),
                'mode' => 'sprite',
                'options' => array(
                    array('img' => 'image_top', 'value' => 'image-top', 'label' => __('Image Top', 'themify')),
                    array('img' => 'image_left', 'value' => 'image-left', 'label' => __('Image Left', 'themify')),
                    array('img' => 'image_center', 'value' => 'image-center', 'label' => __('Image Center', 'themify')),
                    array('img' => 'image_right', 'value' => 'image-right', 'label' => __('Image Right', 'themify')),
                    array('img' => 'image_overlay', 'value' => 'image-overlay', 'label' => __('Partial Overlay', 'themify')),
                    array('img' => 'image_card_layout', 'value' => 'image-card-layout', 'label' => __('Card Layout', 'themify')),
                    array('img' => 'image_centered_overlay', 'value' => 'image-full-overlay', 'label' => __('Full Overlay', 'themify'))
                ),
				'binding' => array(
					'not_empty' => array(
						'hide' => array('caption_on_overlay')
					),
					'image-overlay' => array(
						'show' => array('caption_on_overlay')
					),
					'image-full-overlay' => array(
						'show' => array('caption_on_overlay')
					)
				),
                'render_callback' => array(
                    'binding' => 'live'
                )
            ),
			array(
				'id' => 'caption_on_overlay',
				'type' => 'checkbox',
				'pushed' => 'pushed',
				'options' => array(
					array( 'name' => 'yes', 'value' => __('Show caption overlay on hover only', 'themify') )
				),
				'render_callback' => array(
					'binding' => 'live'
				)
			),
            array(
                'id' => 'url_image',
                'type' => 'image',
                'label' => __('Image URL', 'themify'),
                'class' => 'xlarge',
                'render_callback' => array(
                    'binding' => 'live'
                )
            ),
            array(
                'id' => 'appearance_image',
                'type' => 'checkbox',
                'label' => __('Image Appearance', 'themify'),
                'options' => array(
                    array('name' => 'rounded', 'value' => __('Rounded', 'themify')),
                    array('name' => 'drop-shadow', 'value' => __('Drop Shadow', 'themify')),
                    array('name' => 'bordered', 'value' => __('Bordered', 'themify')),
                    array('name' => 'circle', 'value' => __('Circle', 'themify'), 'help' => __('(square format image only)', 'themify'))
                ),
                'render_callback' => array(
                    'binding' => 'live'
                )
            ),
            array(
                'id' => 'image_size_image',
                'type' => 'select',
                'label' => __('Image Size', 'themify'),
                'hide' => !$is_img_enabled,
                'options' => $image_sizes,
                'render_callback' => array(
                    'binding' => 'live'
                )
            ),
            array(
                'id' => 'image_fullwidth_container',
                'type' => 'multi',
                'label' => __('Width', 'themify'),
                'fields' => array(
                    array(
                        'id' => 'width_image',
                        'type' => 'text',
                        'label' => '',
                        'class' => 'xsmall',
                        'help' => 'px',
                        'value' => '',
                        'render_callback' => array(
                            'binding' => 'live'
                        )
                    ),
                    array(
                        'id' => 'auto_fullwidth',
                        'type' => 'checkbox',
                        'options' => array(array('name' => '1', 'value' => __('Auto fullwidth image', 'themify'))),
                        'render_callback' => array(
                            'binding' => 'live'
                        )
                    )
                )
            ),
            array(
                'id' => 'height_image',
                'type' => 'text',
                'label' => __('Height', 'themify'),
                'class' => 'xsmall',
                'help' => 'px',
                'value' => '',
                'render_callback' => array(
                    'binding' => 'live'
                )
            ),
            array(
                'id' => 'title_image',
                'type' => 'text',
                'label' => self::$texts['title_image'],
                'class' => 'fullwidth',
                'render_callback' => array(
                    'binding' => 'live'
                )
            ),
            array(
                'id' => 'link_image',
                'type' => 'text',
                'label' => __('Image Link', 'themify'),
                'class' => 'fullwidth',
                'binding' => array(
                    'empty' => array(
                        'hide' => array('param_image', 'image_zoom_icon', 'lightbox_size')
                    ),
                    'not_empty' => array(
                        'show' => array('param_image', 'image_zoom_icon', 'lightbox_size')
                    )
                ),
                'render_callback' => array(
                    'binding' => 'live'
                )
            ),
            array(
                'id' => 'param_image',
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
                    'binding' => 'live'
                ),
	            'binding' => array(
		            'regular' => array(
			            'hide' => array('lightbox_size')
		            ),
		            'newtab' => array(
			            'hide' => array('lightbox_size')
		            ),
		            'lightbox' => array(
			            'show' => array('lightbox_size')
		            )
	            )
            ),
	        array(
		        'id' => 'lightbox_size',
		        'type' => 'multi',
		        'label' => __('Lightbox Dimension', 'themify'),
		        'fields' => array(
			        array(
				        'id' => 'lightbox_width',
				        'type' => 'range',
				        'label' => __('Width', 'themify'),
				        'value' => '',
				        'render_callback' => array(
					        'binding' => false
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
				        'type' => 'range',
				        'label' => __('Height', 'themify'),
				        'value' => '',
				        'render_callback' => array(
					        'binding' => false
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
			        )
		        ),
		        'wrap_with_class' => 'tb_group_element tb_group_element_lightbox'
	        ),
            array(
                'id' => 'image_zoom_icon',
                'type' => 'checkbox',
                'label' => false,
                'pushed' => 'pushed',
                'options' => array(
                    array('name' => 'zoom', 'value' => __('Show zoom icon', 'themify'))
                ),
                'wrap_with_class' => 'tb_group_element tb_group_element_lightbox tb_group_element_newtab',
                'render_callback' => array(
                    'binding' => 'live'
                )
            ),
            array(
                'id' => 'caption_image',
                'type' => 'textarea',
                'label' =>self::$texts['caption_image'], 
                'class' => 'fullwidth',
                'render_callback' => array(
                    'binding' => 'live',
                    'live-selector' => '.image-caption'
                )
            ),
            array(
                'id' => 'alt_image',
                'type' => 'text',
                'label' => __('Image Alt Tag', 'themify'),
                'class' => 'fullwidth',
                'render_callback' => array(
                    'binding' => false
                )
            ),
            // Additional CSS
            array(
                'type' => 'separator',
                'meta' => array('html' => '<hr/>')
            ),
            array(
                'id' => 'css_image',
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
            'url_image' => 'https://themify.me/demo/themes/wp-content/uploads/image-placeholder-small.jpg'
        );
    }

    public function get_styling() {
        $general = array(
            // Background
            self::get_seperator('image_bacground', __('Background', 'themify'), false),
            self::get_image('.module-image'),
            self::get_color('.module-image', 'background_color', __('Background Color', 'themify'), 'background-color'),
            self::get_repeat('.module-image'),
			self::get_position('.module-image'),
            // Font
            self::get_seperator('font', __('Font', 'themify')),
            self::get_font_family(array('.module-image .image-content', '.module-image .image-title', '.module-image .image-title a')),
            self::get_element_font_weight(array('.module-image .image-content', '.module-image .image-title', '.module-image .image-title a')),
            self::get_color_type('font_color_type',__('Font Color Type', 'themify'),'font_color','font_gradient_color'),
            self::get_color(array('.module-image .image-content', '.module-image .image-title', '.module-image .image-title a', '.module-image h1', '.module-image h2', '.module-image h3:not(.module-title)', '.module-image h4', '.module-image h5', '.module-image h6'), 'font_color', __('Font Color', 'themify'),'color',true),
            self::get_gradient_color(array('.module-image .module-title,.module-image .image-content .image-caption', '.module-image .image-title', '.module-image .image-title a', '.module-image h1', '.module-image h2', '.module-image h3:not(.module-title)', '.module-image h4', '.module-image h5', '.module-image h6'),'font_gradient_color',__('Font Color', 'themify')),
            self::get_font_size('.module-image .image-content'),
            self::get_line_height('.module-image .image-content'),
            self::get_letter_spacing('.module-image .image-content'),
            self::get_text_align('.module-image .image-content'),
            self::get_text_transform('.module-image .image-content'),
            self::get_font_style('.module-image .image-content'),
            self::get_text_decoration('.module-image .image-content','text_decoration_regular'),
            // Link
            self::get_seperator('link', __('Link', 'themify')),
            self::get_color('.module-image a', 'link_color'),
            self::get_color('.module-image a:hover', 'link_color_hover', __('Color Hover', 'themify')),
            self::get_text_decoration('.module-image a'),
            // Padding
            self::get_seperator('padding', __('Padding', 'themify')),
            self::get_padding('.module-image'),
            // Margin
            self::get_seperator('margin', __('Margin', 'themify')),
            self::get_margin('.module-image'),
            // Border
            self::get_seperator('border', __('Border', 'themify')),
            self::get_border('.module-image')
        );

        $image_title = array(
            // Font
            self::get_seperator('font', __('Font', 'themify'), false),
            self::get_font_family(array('.module-image .image-title', '.module-image .image-title a'), 'font_family_title'),
            self::get_element_font_weight(array('.module-image .image-title', '.module-image .image-title a'), 'font_weight_title'),
            self::get_color(array('.module-image .image-title', '.module-image .image-title a', '.module-image.image-full-overlay .image-wrap:not(:empty) + .image-content h3:not(.module-title)', '.module-image.image-full-overlay .image-wrap:not(:empty) + .image-content h3:not(.module-title) a'), 'font_color_title', __('Font Color', 'themify')),
            self::get_color(array('.module-image .image-title:hover', '.module-image .image-title a:hover', '.module.module-image.image-full-overlay .image-wrap:not(:empty) + .image-content h3:not(.module-title):hover', '.module.module-image.image-full-overlay .image-wrap:not(:empty) + .image-content h3:not(.module-title) a:hover'), 'font_color_title_hover', __('Color Hover', 'themify')),
            self::get_font_size('.module-image .image-title', 'font_size_title'),
            self::get_line_height('.module-image .image-title', 'line_height_title'),
            self::get_letter_spacing('.module-image .image-title', 'letter_spacing_title'),
            self::get_text_transform('.module-image .image-title', 'text_transform_title'),
            self::get_font_style('.module-image .image-title', 'font_style_title','font_title_bold'),
            // Margin
            self::get_seperator('margin',__('Margin', 'themify')),
            self::get_margin('.module-image .image-title','title_margin')
        );
		
		$image_tab = array(
            // Background
            self::get_seperator('image_bacground', __('Background', 'themify'), false),
            self::get_color('.module-image .image-wrap img', 'i_t_b_c', __('Background Color', 'themify'), 'background-color'),
			// Padding
			self::get_seperator('padding',__('Padding', 'themify')),
			self::get_padding('.module-image .image-wrap img','i_t_p'),
            // Margin
            self::get_seperator('margin',__('Margin', 'themify')),
            self::get_margin('.module-image .image-wrap img','i_t_m'),
			// Border
			self::get_seperator('border',__('Border', 'themify')),
			self::get_border('.module-image .image-wrap img','i_t_b')
        );
        $image_caption = array(
            // Background
            self::get_seperator('image_bacground', __('Caption Overlay', 'themify'), false),
            self::get_color(array('.module-image.image-overlay .image-wrap a + .image-content', '.module-image.image-overlay img + .image-content', '.module-image.image-full-overlay .image-content::before', '.module-image.image-card-layout .image-content'), 'b_c_c', __('Overlay', 'themify'), 'background-color'),
            self::get_color(array('.module-image.image-overlay:hover .image-wrap a + .image-content', '.module-image.image-overlay:hover img + .image-content', '.module-image.image-full-overlay:hover .image-content::before', '.module-image.image-card-layout:hover .image-content'), 'b_c_c_h', __('Overlay Hover', 'themify'), 'background-color'),
            self::get_color(array('.module-image.image-overlay:hover .image-content .image-title', '.module-image.image-overlay:hover .image-content .image-title a', '.module-image.image-overlay:hover .image-content .image-caption', '.module-image.image-full-overlay:hover .image-content .image-title', '.module-image.image-full-overlay:hover .image-content .image-title a', '.module-image.image-full-overlay:hover .image-content .image-caption', '.module-image.image-full-overlay:hover .image-wrap:not(:empty) + .image-content h3:not(.module-title)', '.module-image.image-card-layout:hover .image-content', '.module-image.image-card-layout:hover .image-content .image-title'), 'f_c_c_h', __('Overlay Hover Font Color', 'themify')),
            // Font
            self::get_seperator('font', __('Font', 'themify'), false),
            self::get_font_family('.module-image .image-content .image-caption', 'font_family_caption'),
            self::get_element_font_weight('.module-image .image-content .image-caption', 'font_weight_caption'),
            self::get_color(array('.module-image .image-content .image-caption', '.module-image.image-full-overlay .image-wrap:not(:empty) + .image-content h3:not(.module-title)'), 'font_color_caption', __('Font Color', 'themify')),
            self::get_font_size('.module-image .image-content .image-caption', 'font_size_caption'),
            self::get_line_height('.module-image .image-content .image-caption', 'line_height_caption'),
			// Padding
			self::get_seperator('padding',__('Padding', 'themify')),
			self::get_padding('.module-image .image-content','c_p')
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
                        'label' => __('Module Title', 'themify'),
                        'fields' => $this->module_title_custom_style()
                    ),
                    'title' => array(
                        'label' => __('Image Title', 'themify'),
                        'fields' => $image_title
                    ),
					'image_tab' => array(
                        'label' => __('Image', 'themify'),
                        'fields' => $image_tab
                    ),
                    'caption' => array(
                        'label' => __('Image Caption', 'themify'),
                        'fields' => $image_caption
                    )
                )
            ),
        );
    }

    protected function _visual_template() {
        $module_args = self::get_module_args();
        ?>
        <#
		var font_color_type = '';
		if(themifybuilderapp.activeModel != null){
			var tempData = themifybuilderapp.Forms.serialize('tb_options_styling');
			font_color_type = ('font_color_type' in  tempData && tempData['font_color_type'].indexOf('gradient') !== -1)?'gradient':'solid';
			font_color_type = 'tb-font-color-' + font_color_type;
		}
        var fullwidth = data.auto_fullwidth == '1' ? 'auto_fullwidth' : '';
        var classWrap = data.style_image;
        if ( ('image-overlay' == classWrap || 'image-full-overlay' == classWrap) && data.caption_on_overlay == "yes" ){
            classWrap += ' active-caption-hover';
        }
        #>
        <div class="module module-<?php echo $this->slug; ?> {{ font_color_type }} {{ fullwidth }} {{ classWrap }} {{ data.css_image }} <# ! _.isUndefined( data.appearance_image ) ? print( data.appearance_image.split('|').join(' ') ) : ''; #>">
             <!--insert-->
            <# if ( data.mod_title_image ) { #>
                <?php echo $module_args['before_title']; ?>{{{ data.mod_title_image }}}<?php echo $module_args['after_title']; ?>
            <# } 
            var style='';
            if(!fullwidth){
            style = 'width:' + ( data.width_image ? data.width_image + 'px;' : 'auto;' );
            style += 'height:' + ( data.height_image ? data.height_image + 'px;' : 'auto;' );
            }
            var image = '<img src="'+ data.url_image +'" style="' + style + '"/>';
            #>
            <div class="image-wrap">
                <# if ( data.link_image ) { #>
                <a href="{{ data.link_image }}">
                    <# if( data.image_zoom_icon === 'zoom' ) { #>
                    <span class="zoom fa <# print( data.param_image == 'lightbox' ? 'fa-search' : 'fa-external-link' ) #>"></span>
                    <# } #>
                    {{{ image }}}
                </a>
                <# } else { #>
                {{{ image }}}
                <# } 
                if ( 'image-overlay' !== data.style_image ) { #>
            </div>
            <# } 
            if( data.title_image || data.caption_image ) { #>
            <div class="image-content">
                <# if ( data.title_image ) { #>
                <h3 class="image-title">
                    <# if ( data.link_image ) { #>
                    <a href="{{ data.link_image }}">{{{ data.title_image }}}</a>
                    <# } else { #>
                    {{{ data.title_image }}}
                    <# } #>
                </h3>
                <# } 
                if( data.caption_image ) { #>
                <div class="image-caption">{{{ data.caption_image }}}</div>
                <# } #>
            </div>
            <# } 
            if ( 'image-overlay' === data.style_image ) { #>
        </div>
        <# } #>
        </div>
        <?php
    }

}

///////////////////////////////////////
// Module Options
///////////////////////////////////////
Themify_Builder_Model::register_module('TB_Image_Module');
