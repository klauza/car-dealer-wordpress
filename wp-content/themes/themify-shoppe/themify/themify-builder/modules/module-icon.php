<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Module Name: Icon
 * Description: Display Icon content
 */

class TB_Icon_Module extends Themify_Builder_Component_Module {
   
    function __construct() {
        self::$texts['icon_label'] = __('Label', 'themify');
        parent::__construct(array(
            'name' => __('Icon', 'themify'),
            'slug' => 'icon'
        ));
    }

    public function get_options() {
        $colors = Themify_Builder_Model::get_colors();
        $colors[] = array('img' => 'transparent', 'value' => 'transparent', 'label' => __('Transparent', 'themify'));

        return array(
            array(
                'id' => 'icon_size',
                'type' => 'radio',
                'label' => __('Size', 'themify'),
                'options' => array(
                    'normal' => __('Normal', 'themify'),
                    'small' => __('Small', 'themify'),
                    'large' => __('Large', 'themify'),
                    'xlarge' => __('xLarge', 'themify')
                ),
                'render_callback' => array(
                    'binding' => 'live'
                ),
                'default' => 'normal'
            ),
            array(
                'id' => 'icon_style',
                'type' => 'radio',
                'label' => __('Icon Background Style', 'themify'),
                'options' => array(
                    'circle' => __('Circle', 'themify'),
                    'rounded' => __('Rounded', 'themify'),
                    'squared' => __('Squared', 'themify'),
                    'none' => __('None', 'themify')
                ),
                'render_callback' => array(
                    'binding' => 'live'
                ),
                'default' => 'circle'
            ),
            array(
                'id' => 'icon_position',
                'type' => 'radio',
                'label' => __('Icon Position ', 'themify'),
                'options' => array(
                    'icon_position_left' => __('Left', 'themify'),
                    'icon_position_center' => __('Center', 'themify'),
                    'icon_position_right' => __('Right', 'themify'),
                ),
                'render_callback' => array(
                    'binding' => 'live'
                ),
                'default' => 'icon_position_left'
            ),
            array(
                'id' => 'icon_arrangement',
                'type' => 'radio',
                'label' => __('Arrangement ', 'themify'),
                'options' => array(
                    'icon_horizontal' => __('Horizontally', 'themify'),
                    'icon_vertical' => __('Vertically', 'themify'),
                ),
                'render_callback' => array(
                    'binding' => 'live'
                ),
                'default' => 'icon_horizontal'
            ),
            array(
                'id' => 'content_icon',
                'type' => 'builder',
                'new_row_text' => __('Add new icon', 'themify'),
                'options' => array(
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
                                'render_callback' => array(
                                    'repeater' => 'content_icon',
                                    'binding' => 'live'
                                )
                            ),
                            array(
                                'id' => 'icon_color_bg',
                                'type' => 'layout',
                                'mode' => 'sprite',
                                'class' => 'tb_colors',
                                'label' => '',
                                'options' => $colors,
                                'bottom' => false,
                                'wrap_with_class' => 'fullwidth',
                                'render_callback' => array(
                                    'repeater' => 'content_icon',
                                    'binding' => 'live'
                                )
                            ),
                        ),
                        'render_callback' => array(
                            'binding' => 'live'
                        )
                    ),
                    array(
                        'id' => 'label',
                        'type' => 'text',
                        'label' =>self::$texts['icon_label'],
                        'class' => 'fullwidth',
                        'render_callback' => array(
                            'repeater' => 'content_icon',
                            'binding' => 'live',
                            'live-selector' => '.module-icon-item>span'
                        )
                    ),
                    array(
                        'id' => 'link',
                        'type' => 'text',
                        'label' => __('Link', 'themify'),
                        'class' => 'fullwidth',
                        'binding' => array(
                            'empty' => array(
                                'hide' => array('link_options')
                            ),
                            'not_empty' => array(
                                'show' => array('link_options', 'lightbox_size')
                            )
                        ),
                        'render_callback' => array(
                            'repeater' => 'content_icon',
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
                        'render_callback' => array(
                            'repeater' => 'content_icon',
                            'binding' => 'live'
                        ),
                        'wrap_with_class' => 'link_options'
                    ),
                    array(
                        'id' => 'lightbox_size',
                        'type' => 'multi',
                        'label' => __('Lightbox Dimension', 'themify'),
                        'options' => array(
                            array(
                                'id' => 'lightbox_width',
	                            'label' => __('Width', 'themify'),
	                            'value' => '',
	                            'render_callback' => array(
		                            'repeater' => 'content_icon',
		                            'binding' => false
	                            ),
	                            'type' => 'range',
	                            'units' => array(
		                            'PX' => array(
			                            'min' => -500,
			                            'max' => 500,
		                            ),
		                            '%' => array(
			                            'min' => 0,
			                            'max' => 100,
		                            ),
		                            'EM' => array(
			                            'min' => -10,
			                            'max' => 10,
		                            )
	                            )
                            ),
                            array(
                                'id' => 'lightbox_height',
                                'label' => __('Height', 'themify'),
                                'value' => '',
                                'render_callback' => array(
                                    'repeater' => 'content_icon',
                                    'binding' => false
                                ),
	                            'type' => 'range',
	                            'units' => array(
		                            'PX' => array(
			                            'min' => -500,
			                            'max' => 500,
		                            ),
		                            '%' => array(
			                            'min' => 0,
			                            'max' => 100,
		                            ),
		                            'EM' => array(
			                            'min' => -10,
			                            'max' => 10,
		                            )
	                            )
                            )
                        ),
                        'render_callback' => array(
                            'binding' => 'live'
                        ),
                        'wrap_with_class' => 'tb_group_element tb_group_element_lightbox'
                    )
                ),
                'render_callback' => array(
                    'binding' => 'live'
                ),
            ),
            // Additional CSS
            array(
                'type' => 'separator',
                'meta' => array('html' => '<hr/>')
            ),
            array(
                'id' => 'css_icon',
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
            'content_icon' => array(
                array(
                    'icon' => 'fa-home',
                    'label' => esc_html__('Icon label', 'themify'),
                    'icon_color_bg' => 'blue',
                    'link_options' => 'regular'
                )
            )
        );
    }

    public function get_styling() {
        $general = array(
            // Background
            self::get_seperator('image_bacground', __('Background', 'themify'), false),
            self::get_image(' div.module-icon'),
            self::get_color(' div.module-icon', 'background_color', __('Background Color', 'themify'), 'background-color'),
            self::get_repeat(' div.module-icon'),
			self::get_position(' div.module-icon'),
            // Font
            self::get_seperator('font', __('Font', 'themify')),
            self::get_font_family(' div.module-icon'),
            self::get_element_font_weight(' div.module-icon'),
            self::get_color_type('font_color_type',__('Font Color Type', 'themify'),'font_color','font_gradient_color'),
            self::get_color(' div.module-icon', 'font_color', __('Font Color', 'themify'),'color',true),
            self::get_gradient_color(' div.module-icon span','font_gradient_color',__('Font Color', 'themify')),
            self::get_font_size(array(' div.module-icon i', ' div.module-icon a', ' div.module-icon span')),
            self::get_line_height(array(' div.module-icon i', ' div.module-icon a', ' div.module-icon span')),
            self::get_letter_spacing(' div.module-icon'),
            self::get_text_align(' div.module-icon'),
            self::get_text_transform(' div.module-icon'),
            self::get_font_style(' div.module-icon'),
            self::get_text_decoration(' div.module-icon span','text_decoration_regular'),
            // Link
            self::get_seperator('link', __('Link', 'themify')),
            self::get_color(array(' div.module-icon span', ' div.module-icon i'), 'link_color'),
            self::get_color(' div.module-icon .module-icon-item:hover span', 'link_color_hover', __('Color Hover', 'themify')),
            self::get_text_decoration(array(' div.module-icon span')),
            // Padding
            self::get_seperator('padding', __('Padding', 'themify')),
            self::get_padding(' div.module-icon'),
            // Margin
            self::get_seperator('margin', __('Margin', 'themify')),
            self::get_margin(' div.module-icon'),
            // Border
            self::get_seperator('border', __('Border', 'themify')),
            self::get_border(' div.module-icon')
        );

        $icon = array(
            // Background
            self::get_seperator('image_bacground', __('Background', 'themify'), false),
            self::get_color(' div.module-icon .module-icon-item i', 'background_color_icon', __('Background Color', 'themify'), 'background-color'),
            self::get_color(' div.module-icon .module-icon-item:hover i', 'background_color_icon_hover', __('Background Hover', 'themify'), 'background-color'),
            // Font
            self::get_seperator('font', __('Color', 'themify')),
            self::get_color(' div.module-icon .module-icon-item i', 'font_color_icon', __('Color', 'themify')),
            self::get_color(' div.module-icon .module-icon-item:hover i', 'font_color_icon_hover', __('Color Hover', 'themify')),
            self::get_font_size(' div.module-icon .module-icon-item i', 'f_s_i')
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
                    'icon' => array(
                        'label' => __('Icon', 'themify'),
                        'fields' => $icon
                    )
                )
            )
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
        _.defaults(data, {
        'mod_title_icon' : '',
        'icon_size' : '',
        'icon_style' : '',
        'icon_arrangement' : 'icon_horizontal',
        'icon_position' : 'icon_position_left',
        'content_icon' : null,
        'css_icon' : ''
        });
        var def = {
        'label':'',
        'link':'',
        'icon':'',
        'new_window':false,
        'icon_color_bg':false,
        'link_options':'',
        'lightbox_width':'',
        'lightbox_height':'',
        'lightbox_width_unit':'',
        'lightbox_height_unit':''
        };
        #>

        <div class="module module-<?php echo $this->slug; ?> {{ font_color_type }} {{ data.css_icon }}">
             <!--insert-->
            <# if( data.mod_title_icon ) { #>
            <?php echo $module_args['before_title']; ?>
            {{{ data.mod_title_icon }}}
            <?php echo $module_args['after_title']; ?>
            <# } #>
            <div class="module-<?php echo $this->slug; ?> {{ data.icon_size }} {{ data.icon_style }} {{ data.icon_arrangement }} {{ data.icon_position }}">
                <#  
                if(data.content_icon){
                _.each( data.content_icon, function( item ) {
                _.defaults(item, def);
                var link_target = item.link_options === 'newtab' ? 'rel="noopener" target="_blank"' : '',
                link_lightbox_class = item.link_options === 'lightbox' ? ' class="lightbox-builder themify_lightbox"' : '',
                lightbox_data = item.lightbox_width || item.lightbox_height ? (' data-zoom-config="'+item.lightbox_width+item.lightbox_width_unit+'|'+item.lightbox_height+item.lightbox_height_unit+'"'): false;
                #>
                <div class="module-icon-item">
                    <# if(item.link){ #>
                        <a href="{{ item.link }}"{{ link_target }}{{ link_lightbox_class }}{{ lightbox_data }}>
                    <# } 
                    if (item.icon){ #>
                       <i class="{{ item.icon }} fa ui {{ item.icon_color_bg }}"></i>
                    <# } 
                    if (item.label){ #>
                        <span>{{ item.label }}</span>
                        <# }  if(item.link){ #>
                        </a>
                    <# } #>
                </div>
                <# });
                } #>
            </div>
        </div>
        <?php
    }

}

///////////////////////////////////////
// Module Options
///////////////////////////////////////
Themify_Builder_Model::register_module('TB_Icon_Module');
