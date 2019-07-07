<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Module Name: Accordion
 * Description: Display Accordion content
 */

class TB_Accordion_Module extends Themify_Builder_Component_Module {
    
    public function __construct() {
        self::$texts['title_accordion'] =__('Accordion Title', 'themify');
        self::$texts['text_accordion'] =__('Accordion content', 'themify');
        parent::__construct(array(
            'name' => __('Accordion', 'themify'),
            'slug' => 'accordion'
        ));
    }

    public function get_title($module) {
        return isset($module['mod_settings']['mod_title_accordion']) ? wp_trim_words($module['mod_settings']['mod_title_accordion'], 100) : '';
    }

    public function get_options() {
        $colors = Themify_Builder_Model::get_colors();
        $colors[] = array('img' => 'transparent', 'value' => 'transparent', 'label' => __('Transparent', 'themify'));
        return array(
            array(
                'id' => 'mod_title_accordion',
                'type' => 'text',
                'label' => __('Module Title', 'themify'),
                'class' => 'large',
                'render_callback' => array(
                    'binding' => 'live',
                    'live-selector'=>'.module-title'
                )
            ),
            array(
                'id' => 'content_accordion',
                'type' => 'builder',
                'options' => array(
                    array(
                        'id' => 'title_accordion',
                        'type' => 'text',
                        'label' => self::$texts['title_accordion'],
                        'class' => 'large',
                        'render_callback' => array(
                            'repeater' => 'content_accordion',
                            'binding' => 'live',
                            'live-selector'=>'.accordion-title a'
                        )
                    ),
                    array(
                        'id' => 'text_accordion',
                        'type' => 'wp_editor',
                        'label' => false,
                        'class' => 'fullwidth',
                        'rows' => 6,
                        'render_callback' => array(
                            'repeater' => 'content_accordion',
                            'binding' => 'live'
                        )
                    ),
                    array(
                        'id' => 'default_accordion',
                        'type' => 'radio',
                        'label' => __('Default', 'themify'),
                        'default' => 'closed',
                        'options' => array(
                            'closed' => __('closed', 'themify'),
                            'open' => __('open', 'themify')
                        ),
                        'render_callback' => array(
                            'repeater' => 'content_accordion',
                            'binding' => 'live'
                        )
                    )
                ),
                'render_callback' => array(
                    'binding' => 'live'
                )
            ),
            array(
                'type' => 'separator',
                'meta' => array('html' => '<hr/>')
            ),
            array(
                'id' => 'layout_accordion',
                'type' => 'layout',
                'mode' => 'sprite',
                'label' => __('Accordion layout', 'themify'),
                'options' => array(
                    array('img' => 'accordion_default', 'value' => 'default', 'label' => __('Contiguous Panels', 'themify')),
                    array('img' => 'accordion_separate', 'value' => 'separate', 'label' => __('Separated Panels', 'themify'))
                ),
                'render_callback' => array(
                    'binding' => 'live'
                )
            ),
            array(
                'id' => 'expand_collapse_accordion',
                'type' => 'radio',
                'label' => __('Expand / Collapse', 'themify'),
                'default' => 'toggle',
                'options' => array(
                    'toggle' => __('Toggle <small>(only clicked item is toggled)</small>', 'themify'),
                    'accordion' => __('Accordion <small>(collapse all, but keep clicked item expanded)</small>', 'themify')
                ),
                'break' => true,
                'render_callback' => array(
                    'binding' => 'live'
                )
            ),
            array(
                'id' => 'color_accordion',
                'type' => 'layout',
                'mode' => 'sprite',
                'class' => 'tb_colors',
                'label' => __('Accordion Color', 'themify'),
                'options' => $colors,
                'render_callback' => array(
                    'binding' => 'live'
                )
            ),
            array(
                'id' => 'accordion_appearance_accordion',
                'type' => 'checkbox',
                'label' => __('Accordion Appearance', 'themify'),
                'options' => Themify_Builder_Model::get_appearance(),
                'render_callback' => array(
                    'binding' => 'live'
                )
            ),
            array(
                'id' => 'multi_accordion_icon',
                'type' => 'multi',
                'label' => __('Icon', 'themify'),
                'fields' => array(
                    array(
                        'id' => 'icon_accordion',
                        'type' => 'icon',
                        'label' => __('Closed Accordion Icon', 'themify'),
                        'class' => 'large',
                        'render_callback' => array(
                            'binding' => 'live'
                        )
                    ),
                    array(
                        'id' => 'icon_active_accordion',
                        'type' => 'icon',
                        'label' => __('Opened Accordion Icon', 'themify'),
                        'class' => 'large',
                        'render_callback' => array(
                            'binding' => 'live'
                        )
                    ),
                )
            ),
            // Additional CSS
            array(
                'type' => 'separator',
                'meta' => array('html' => '<hr/>')
            ),
            array(
                'id' => 'css_accordion',
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
            'content_accordion' => array(
                array('title_accordion' => self::$texts['title_accordion'], 'text_accordion' => self::$texts['text_accordion'])
            )
        );
    }

    public function get_styling() {
        $general = array(
            // Background
            self::get_seperator('image_background', __('Background', 'themify'), false),
            self::get_color(array(' .ui.module-accordion'), 'background_color', __('Background Color', 'themify'), 'background-color'),
            // Font
            self::get_seperator('font', __('Font', 'themify')),
            self::get_font_family(' .ui.module-accordion'),
            self::get_element_font_weight(' .ui.module-accordion'),
            self::get_color_type('font_color_type',__('Font Color Type', 'themify'),'font_color','font_gradient_color'),
            self::get_color(array(' .ui.module-accordion', ' .ui.module-accordion h1', ' .ui.module-accordion h2', ' .ui.module-accordion h3', ' .ui.module-accordion h4', ' .ui.module-accordion h5', ' .ui.module-accordion h6'), 'font_color', __('Font Color', 'themify'),'color',true),
            self::get_gradient_color(array( ' .ui.module-accordion a', ' .ui.module-accordion h1', ' .ui.module-accordion h2', ' .ui.module-accordion h3', ' .ui.module-accordion h4', ' .ui.module-accordion h5', ' .ui.module-accordion h6' ),'font_gradient_color',__('Font Color', 'themify')),
            self::get_font_size(' .ui.module-accordion'),
            self::get_line_height(' .ui.module-accordion'),
            self::get_letter_spacing(' .ui.module-accordion'),
            self::get_text_align(' .ui.module-accordion'),
            self::get_text_transform(' .ui.module-accordion'),
            self::get_font_style(' .ui.module-accordion'),
            self::get_text_decoration(' .ui.module-accordion','t_d_r'),
            // Link
            self::get_seperator('link', __('Link', 'themify')),
            self::get_color(' .ui.module-accordion a', 'link_color'),
            self::get_color(' .ui.module-accordion a:hover', 'link_color_hover', __('Color Hover', 'themify')),
            self::get_text_decoration(' .ui.module-accordion a'),
            // Padding
            self::get_seperator('padding', __('Padding', 'themify')),
            self::get_padding(array(' .ui.module-accordion')),
            // Margin
            self::get_seperator('margin', __('Margin', 'themify')),
            self::get_margin(' .ui.module-accordion'),
            // Border
            self::get_seperator('border', __('Border', 'themify')),
            self::get_border(' .ui.module-accordion', 'border_accordion')
        );

        $accordion_title = array(
            // Background
            self::get_seperator('image_background', __('Background', 'themify'), false),
            self::get_color(' .ui.module-accordion .accordion-title a', 'background_color_title', __('Background Color', 'themify'), 'background-color'),
            // Font
            self::get_seperator('font', __('Font', 'themify')),
            self::get_font_family(' .ui.module-accordion .accordion-title', 'font_family_title'),
            self::get_element_font_weight(' .ui.module-accordion .accordion-title', 'font_weight_title'),
            self::get_color(array(' .ui.module-accordion .accordion-title', '  .ui.module-accordion .accordion-title a'), 'font_color_title', __('Font Color', 'themify')),
            self::get_font_size(' .ui.module-accordion .accordion-title', 'font_size_title'),
            self::get_line_height(' .ui.module-accordion .accordion-title', 'line_height_title'),
			self::get_letter_spacing(' .ui.module-accordion .accordion-title', 'l_s_t'),
            self::get_text_transform(' .ui.module-accordion .accordion-title', 't_t_t'),
            self::get_font_style('.module.module-accordion .accordion-title', 'f_s_t','f_t_b'),
            self::get_text_decoration('.module.module-accordion .accordion-title','t_d_t'),
			// Border
			self::get_seperator('border', __('Border', 'themify')),
			self::get_border(' .ui.module-accordion .accordion-title', 'b_a_t'),
			// Padding
			self::get_seperator('padding', __('Padding', 'themify')),
			self::get_padding(' .ui.module-accordion .accordion-title', 'p_a_t')
        );

        $accordion_icon = array(
            self::get_color(array(' .ui.module-accordion .accordion-title .accordion-active-icon'), 'icon_color', __('Open Icon Color', 'themify')),
            self::get_color(array(' .ui.module-accordion .accordion-title .accordion-icon'), 'icon_active_color', __('Closed Icon Color', 'themify')),
            self::get_font_size(array(' .ui.module-accordion .accordion-title i'), 'icon_size', __('Icon Size', 'themify'))
        );

        $accordion_content = array(
            // Background
            self::get_seperator('image_background', __('Background', 'themify'), false),
            self::get_color(' .ui.module-accordion .accordion-content', 'background_color_content', __('Background Color', 'themify'), 'background-color'),
            // Font
            self::get_seperator('font', __('Font', 'themify')),
            self::get_font_family(' .ui.module-accordion .accordion-content, .ui.module-accordion .accordion-content *', 'font_family_content'),
            self::get_element_font_weight(' .ui.module-accordion .accordion-content, .ui.module-accordion .accordion-content *', 'font_weight_content'),
            self::get_color(array(' .ui.module-accordion .accordion-content', ' .ui.module-accordion .accordion-content h1', ' .ui.module-accordion .accordion-content h2', ' .ui.module-accordion .accordion-content h3', ' .ui.module-accordion .accordion-content h4', ' .ui.module-accordion .accordion-content h5', ' .ui.module-accordion .accordion-content h6'), 'font_color_content', __('Font Color', 'themify')),
            self::get_font_size(' .ui.module-accordion .accordion-content', 'font_size_content'),
            self::get_line_height(' .ui.module-accordion .accordion-content', 'line_height_content'),
			// Multi columns
			self::get_seperator('multi_columns', __('Multi-columns', 'themify')),
			self::get_multi_columns_count( ' .accordion-content' ),
			self::get_multi_columns_gap( ' .accordion-content' ),
			self::get_multi_columns_divider( ' .accordion-content' ),
			// Border
			self::get_seperator('border', __('Border', 'themify')),
			self::get_border(' .ui.module-accordion .accordion-content', 'b_a_c'),
			// Padding
			self::get_seperator('padding', __('Padding', 'themify')),
			self::get_padding(' .ui.module-accordion .accordion-content', 'p_a_c')
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
                        'label' => __('Accordion Title', 'themify'),
                        'fields' => $accordion_title
                    ),
                    'icon' => array(
                        'label' => __('Accordion Icon', 'themify'),
                        'fields' => $accordion_icon
                    ),
                    'content' => array(
                        'label' => __('Accordion Content', 'themify'),
                        'fields' => $accordion_content
                    )
                )
            ),
        );
    }

    protected function _visual_template() {
        $module_args = self::get_module_args();
        ?>
        <div class="module module-<?php echo $this->slug; ?> {{ data.css_accordion }}" data-behavior="{{ data.expand_collapse_accordion }}">
            <!--insert-->
            <# if ( data.mod_title_accordion ) { #>
            <?php echo $module_args['before_title']; ?>{{{ data.mod_title_accordion }}}<?php echo $module_args['after_title']; ?>
            <# }

            if ( data.content_accordion ) { #>
            <ul class="module-<?php echo $this->slug; ?> ui {{ data.layout_accordion }} {{ data.color_accordion }} <# ! _.isUndefined( data.accordion_appearance_accordion ) ? print( data.accordion_appearance_accordion.split('|').join(' ') ) : ''; #>">
                <#
                _.each( data.content_accordion, function( item ) { #>
                <li class="<# 'open' === item.default_accordion ? print('builder-accordion-active') : ''; #>">

                    <div class="accordion-title">
                        <a href="#">
                            <# if ( data.icon_accordion ) { #>
                            <i class="accordion-icon fa {{ data.icon_accordion }}"></i>
                            <# } 

                             if ( data.icon_active_accordion ) { #>
                            <i class="accordion-active-icon fa {{ data.icon_active_accordion }}"></i>
                            <# } #>

                            {{{ item.title_accordion }}}
                        </a>
                    </div>

                    <div class="accordion-content <# 'open' !== item.default_accordion ? print('default-closed') : ''; #> clearfix">
                        {{{ item.text_accordion }}}
                    </div>
                </li>
                <# } ); #>
            </ul>
            <# } #>
        </div>
        <?php
    }

    /**
     * Render plain content for static content.
     * 
     * @param array $module 
     * @return string
     */
    public function get_plain_content($module) {
        $mod_settings = wp_parse_args($module['mod_settings'], array(
            'mod_title_accordion' => '',
            'content_accordion' => array()
        ));
        $text = '';

        if ('' !== $mod_settings['mod_title_accordion'])
            $text = sprintf('<h3>%s</h3>', $mod_settings['mod_title_accordion']);

        if (!empty($mod_settings['content_accordion'])) {
            $text .= '<ul>';
            foreach ($mod_settings['content_accordion'] as $accordion) {
                $accordion = wp_parse_args($accordion, array(
                    'title_accordion' => '',
                    'text_accordion' => '',
                ));
                $text .= sprintf('<li><h4>%s</h4>%s</li>', $accordion['title_accordion'], $accordion['text_accordion']);
            }
            $text .= '</ul>';
        }
        return $text;
    }

}

///////////////////////////////////////
// Module Options
///////////////////////////////////////
Themify_Builder_Model::register_module('TB_Accordion_Module');
