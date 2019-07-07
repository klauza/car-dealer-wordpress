<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Module Name: Tab
 * Description: Display Tab content
 */
class TB_Tab_Module extends Themify_Builder_Component_Module {
    
	public function __construct() {
                self::$texts['title_tab'] =  __('Tab Title', 'themify');
                self::$texts['text_tab'] =  __('Tab Content', 'themify');
		parent::__construct(array(
			'name' => __('Tab', 'themify'),
			'slug' => 'tab'
		));
	}

	public function get_options() {
		return array(
			array(
				'id' => 'mod_title_tab',
				'type' => 'text',
				'label' => __('Module Title', 'themify'),
				'class' => 'large',
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'tab_content_tab',
				'type' => 'builder',
				'options' => array(
					array(
						'id' => 'tab_title_multi',
						'type' => 'multi',
						'label' => '',
						'options' => array(
							array(
								'id' => 'title_tab',
								'type' => 'text',
								'label' => self::$texts['title_tab'],
								'class' => 'fullwidth',
								'render_callback' => array(
									'repeater' => 'tab_content_tab',
									'binding' => 'live',
                                                                        'live-selector'=>'.tab-nav span'
								)
							),
							array(
								'id' => 'icon_tab',
								'type' => 'text',
								'label' => __('Icon', 'themify'),
								'iconpicker' => true,
								'class' => 'large',
								'render_callback' => array(
									'repeater' => 'tab_content_tab',
									'binding' => 'live'
								)
							),
						)
					),
					array(
						'id' => 'text_tab',
						'type' => 'wp_editor',
						'label' => false,
						'class' => 'fullwidth',
						'rows' => 6,
						'render_callback' => array(
							'repeater' => 'tab_content_tab',
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
				'meta' => array( 'html' => '<hr/>')
			),
			array(
				'id' => 'layout_tab',
				'type' => 'layout',
				'label' => __('Tab Layout', 'themify'),
                                'mode'=>'sprite',
				'options' => array(
					array('img' => 'tab_frame', 'value' => 'tab-frame', 'label' => __('Tab Frame', 'themify')),
					array('img' => 'tab_window', 'value' => 'panel', 'label' => __('Tab Window', 'themify')),
					array('img' => 'tab_vertical', 'value' => 'vertical', 'label' => __('Tab Vertical', 'themify')),
					array('img' => 'tab_top', 'value' => 'minimal', 'label' => __('Tab Top', 'themify'))
				),
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'style_tab',
				'type' => 'select',
				'label' => __('Tab Icon', 'themify'),
				'options' => array(
					'default' => __('Icon beside the title', 'themify'),
					'icon-top' => __('Icon above the title', 'themify'),
					'icon-only' => __('Just icons', 'themify'),
				),
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'allow_tab_breakpoint',
				'pushed' => 'pushed',
				'type' => 'checkbox',
				'label' => false,
				'options' => array(
					array( 'name' => 'allow_tab', 'value' => __( 'Enable tab breakpoint', 'themify' ) )
				),
				'option_js' => true
			),
			array(
				'id' => 'tab_breakpoint',
				'pushed' => 'pushed',
				'class'=>'xsmall',
				'type' => 'text',
				'label' => false,
				'after' => __('Tab breakpoint (px)', 'themify'),
				'wrap_with_class' => 'ui-helper-hidden tb-checkbox_element tb-checkbox_element_allow_tab'
			),

			array(
				'id' => 'color_tab',
				'type' => 'layout',
                                'mode'=>'sprite',
				'label' => __('Tab Color', 'themify'),
                                'class'=>'tb_colors',
				'options' => Themify_Builder_model::get_colors(),
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'tab_appearance_tab',
				'type' => 'checkbox',
				'label' => __('Tab Appearance', 'themify'),
				'options' => Themify_Builder_model::get_appearance(),
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
				'id' => 'css_tab',
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
			'tab_content_tab' => array(
				array( 'title_tab' =>self::$texts['title_tab'] , 'text_tab' => self::$texts['text_tab'] )
			),
			'layout_tab' => 'minimal'
		);
	}

	public function get_styling() {
		$general = array(
			// Background
                        self::get_seperator('image_bacground',__( 'Background', 'themify' ),false),
                        self::get_color('.ui.module-tab', 'background_color',__( 'Background Color', 'themify' ),'background-color'),
			// Font
                        self::get_seperator('font',__('Font', 'themify')),
                        self::get_font_family('.ui.module-tab'),
                        self::get_element_font_weight('.ui.module-tab'),
						self::get_color_type('font_color_type',__('Font Color Type', 'themify'),'font_color','font_gradient_color'),
						self::get_color( array( '.ui.module-tab', '.ui.module-tab .tab-content', '.ui.module-tab h1', '.ui.module-tab h2', '.ui.module-tab h3:not(.module-title)', '.ui.module-tab h4', '.ui.module-tab h5', '.ui.module-tab h6' ),'font_color',__('Font Color', 'themify'),'color',true),
						self::get_gradient_color(array( '.ui.module-tab .tab-content p', '.ui.module-tab .tab-content h1', '.ui.module-tab .tab-content h2', '.ui.module-tab .tab-content h3:not(.module-title)', '.ui.module-tab .tab-content h4', '.ui.module-tab .tab-content h5', '.ui.module-tab .tab-content h6' ),'font_gradient_color',__('Font Color', 'themify')),
                        self::get_font_size('.ui.module-tab'),
                        self::get_line_height('.ui.module-tab'),
                        self::get_letter_spacing('.ui.module-tab'),
                        self::get_text_align('.ui.module-tab'),
                        self::get_text_transform('.ui.module-tab'),
                        self::get_font_style('.ui.module-tab'),
                        self::get_text_decoration('.ui.module-tab','text_decoration_regular'),
			// Link
                        self::get_seperator('link',__('Link', 'themify')),
                        self::get_color( '.ui.module-tab a','link_color'),
                        self::get_color( '.ui.module-tab a:hover','link_color_hover',__('Color Hover', 'themify')),
                        self::get_text_decoration( '.ui.module-tab a'),
			// Padding
                        self::get_seperator('padding',__('Padding', 'themify')),
                        self::get_padding('.ui.module-tab'),
			// Margin
                        self::get_seperator('margin',__('Margin', 'themify')),
                        self::get_margin('.ui.module-tab'),
			// Border
                        self::get_seperator('border',__('Border', 'themify')),
                        self::get_border('.ui.module-tab')
		);

		$title = array(
			// Background
                        self::get_seperator('image_bacground',__( 'Background', 'themify' ),false),
                        self::get_color('.ui.module-tab ul.tab-nav li', 'background_color_title',__( 'Background Color', 'themify' ),'background-color'),
			// Font
                        self::get_seperator('font',__('Font', 'themify')),
                        self::get_font_family('.ui.module-tab ul.tab-nav li a','font_family_title'),
                        self::get_element_font_weight('.ui.module-tab ul.tab-nav li a','font_weight_title'),
                        self::get_color('.ui.module-tab ul.tab-nav li a','font_color_title',__('Font Color', 'themify')),
                        self::get_font_size('.ui.module-tab ul.tab-nav li a','font_size_title'),
                        self::get_line_height('.ui.module-tab ul.tab-nav li a','line_height_title'),
						self::get_letter_spacing('.ui.module-tab ul.tab-nav li a', 'letter_spacing_title'),
                        self::get_text_align(array ( '.ui.module-tab ul.tab-nav', '.ui.module-tab ul.tab-nav li' ),'title_text_align'),
						self::get_text_transform('.ui.module-tab ul.tab-nav li a', 't_t_t'),
						self::get_font_style('.ui.module-tab ul.tab-nav li a', 'f_sy_t','f_t_b'),
			// Active Tab
                        self::get_seperator('active_tab',__('Active Tab', 'themify')),
                        self::get_color('.ui.module-tab ul.tab-nav li.current a','active_font_color_title',__('Color Active', 'themify')),
                        self::get_color('.ui.module-tab ul.tab-nav li.current', 'active_background_color_title',__( 'Background Active', 'themify' ),'background-color'),
                        self::get_color('.ui.module-tab ul.tab-nav li.current a:hover', 'active_hover_font_color_title',__( 'Color Hover', 'themify' ),'color'),
                        self::get_color('.ui.module-tab ul.tab-nav li.current:hover', 'active_hover_background_color_title',__( 'Background Hover', 'themify' ),'background-color'),
		   // Border
                        self::get_seperator('border',__('Border', 'themify')),
                        self::get_border('.ui.module-tab ul.tab-nav li','title_border')
		);

		$icon = array(
                        self::get_color( '.ui.module-tab ul.tab-nav li i','icon_color'),
                        self::get_font_size('.ui.module-tab ul.tab-nav li i','icon_size'),
			// Active Tab
                        self::get_seperator('active_tab_icon',__('Active Tab', 'themify')),
                        self::get_color('.ui.module-tab ul.tab-nav li.current i','active_tab_icon_color')
		);

		$content = array(
			// Background
                        self::get_seperator('image_bacground',__( 'Background', 'themify' ),false),
                        self::get_color('.ui.module-tab .tab-content', 'background_color_content',__( 'Background Color', 'themify' ),'background-color'),
			// Font
                        self::get_seperator('font',__('Font', 'themify')),
                        self::get_font_family('.ui.module-tab .tab-content','font_family_content'),
                        self::get_element_font_weight('.ui.module-tab .tab-content','font_weight_content'),
                        self::get_color('.ui.module-tab .tab-content,.module-tab .tab-content h1,.module-tab .tab-content h2,.module-tab .tab-content h3:not(.module-title),.module-tab .tab-content h4,.module-tab .tab-content h5,.module-tab .tab-content h6','font_color_content',__('Font Color', 'themify')),
                        self::get_font_size('.ui.module-tab .tab-content','font_size_content'),
                        self::get_line_height('.ui.module-tab .tab-content','line_height_content'),
			// Padding
                        self::get_seperator('padding_content',__('Padding', 'themify')),
                        self::get_padding('.ui.module-tab .tab-content','p_c'),
			// Border
                        self::get_seperator('border',__('Border', 'themify')),
                        self::get_border('.ui.module-tab .tab-content','b_c')
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
					'title' => array(
						'label' => __('Tab Title', 'themify'),
						'fields' => $title
					),
					'icon' => array(
						'label' => __('Tab Icon', 'themify'),
						'fields' => $icon
					),
					'content' => array(
						'label' => __('Tab Content', 'themify'),
						'fields' => $content
					)
				)
			)
		);

	}

	protected function _visual_template() {
		$module_args = self::get_module_args();?>
		<div class="module module-<?php echo  $this->slug; ?> ui tab-style-{{ data.style_tab }} {{ data.layout_tab }} {{ data.color_tab }} {{ data.css_tab }} <# ! _.isUndefined( data.tab_appearance_tab ) ? print( data.tab_appearance_tab.split('|').join(' ') ) : ''; #>" <# ( "allow_tab" == data.allow_tab_breakpoint && "" != data.tab_breakpoint ) ? print( "data-tab-breakpoint='"+ data.tab_breakpoint +"'" ) : ""; #>  >
			 <!--insert-->
            <# if ( data.mod_title_tab ) { #>
			<?php echo $module_args['before_title']; ?>{{{ data.mod_title_tab }}}<?php echo $module_args['after_title']; ?>
			<# }

			if ( data.tab_content_tab ) {
				var i = 0; #>
				<div class="builder-tabs-wrap">
                    <span class="tab-nav-current-active">{{{ data.tab_content_tab[0].title_tab }}}</span>
                    <ul class="tab-nav">
					<# _.each( data.tab_content_tab, function( item ) { #>
						<li class="<# i === 0 && print('current') #>" aria-expanded="{{i === 0}}">
							<a class='tb-tab-a' href="#tab-{{ data.cid }}-{{ i }}">
								<# if ( item ) { #>
									<# if ( item.icon_tab ) { #><i class="fa {{ item.icon_tab }}"></i><# } #>
									<# if ( item.title_tab ) { #><span class='tb-tab-span'>{{ item.title_tab }}</span><# } #>
								<# } #>
							</a>
						</li>
					<# i++; } ); #>
				</ul>

				<# i = 0; _.each( data.tab_content_tab, function( item ) { #>
					<div id="tab-{{ data.cid }}-{{ i }}" class="tab-content" aria-hidden="{{i !== 0}}"><# item && item.text_tab && print( item.text_tab ) #></div>
				<# i++; } ); #>
				</div>
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
	public function get_plain_content( $module ) {
		$mod_settings = wp_parse_args( $module['mod_settings'], array(
			'mod_title_tab' => '',
			'tab_content_tab' => array()
		) );
		$text = '';

		if ( '' !== $mod_settings['mod_title_tab'] ) 
			$text = sprintf( '<h3>%s</h3>', $mod_settings['mod_title_tab'] );
		
		if ( !empty( $mod_settings['tab_content_tab'] )  ) {
			$text .= '<ul>';
			foreach( $mod_settings['tab_content_tab'] as $content ) {
				$content = wp_parse_args($content, array(
					'title_tab' => '',
					'text_tab' => '',
				));
				$text .= sprintf('<li><h4>%s</h4>%s</li>', $content['title_tab'], $content['text_tab'] );  
			}
			$text .= '</ul>';
		}
		return $text;
	}
}

///////////////////////////////////////
// Module Options
///////////////////////////////////////
Themify_Builder_Model::register_module( 'TB_Tab_Module' );
