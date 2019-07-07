<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Module Name: Text
 * Description: Display text content
 */
class TB_Text_Module extends Themify_Builder_Component_Module {
	function __construct() {
                self::$texts['content_text'] =  __('Text content', 'themify');
		parent::__construct(array(
			'name' => __('Text', 'themify'),
			'slug' => 'text'
		));
	}
        public function get_title( $module ) {
            return isset( $module['mod_settings']['content_text'] ) ? wp_trim_words($module['mod_settings']['content_text'],100 ) : '';
	}
	public function get_options() {
		return array(
			array(
				'id' => 'mod_title_text',
				'type' => 'text',
				'label' => __('Module Title', 'themify'),
				'class' => 'large',
				'render_callback' => array(
                                    'binding' => 'live',
                                    'live-selector'=>'.module-title'
				)
			),
			array(
				'id' => 'content_text',
				'type' => 'wp_editor',
				'class' => 'fullwidth',
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'text_drop_cap',
				'type' => 'checkbox',
				'label' => __('Drop-Cap','themify'),
				'help' => false,
				'wrap_with_class' => '',
				'options' => array(
							array( 'name' => 'dropcap', 'value' =>__('Enable drop-cap', 'themify') )
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
				'help' => sprintf( '<br/><small>%s</small>', __( 'Add additional CSS class(es) for custom styling (<a href="https://themify.me/docs/builder#additional-css-class" target="_blank">learn more</a>).', 'themify' ) ),
				'render_callback' => array(
					'binding' => 'live'
				)
			)
		);
	}

	public function get_default_settings() {
		return array(
			'content_text' =>self::$texts['content_text']
		);
	}


	public function get_styling() {
		$general = array(
			// Background
			self::get_seperator('image_bacground',__( 'Background', 'themify' ),false),
			self::get_image('.module-text'),
			self::get_color('.module-text', 'background_color',__( 'Background Color', 'themify' ),'background-color'),
			self::get_repeat('.module-text'),
			self::get_position('.module-text'),
			// Font
			self::get_seperator('font',__('Font', 'themify')),
			self::get_font_family(array( '.module-text', '.module-text h1', '.module-text h2', '.module-text h3:not(.module-title)', '.module-text h4', '.module-text h5', '.module-text h6' )),
			self::get_element_font_weight(array( '.module-text', '.module-text h1', '.module-text h2', '.module-text h3:not(.module-title)', '.module-text h4', '.module-text h5', '.module-text h6' )),
			self::get_color_type('font_color_type',__('Font Color Type', 'themify'),'font_color','font_gradient_color'),
			self::get_color(array( '.module-text', '.module-text h1', '.module-text h2', '.module-text h3:not(.module-title)', '.module-text h4', '.module-text h5', '.module-text h6' ),'font_color',__('Font Color', 'themify'),'color',true),
			self::get_gradient_color(array( '.module-text p', '.module-text h1', '.module-text h2', '.module-text h3:not(.module-title)', '.module-text h4', '.module-text h5', '.module-text h6' ),'font_gradient_color',__('Font Color', 'themify')),
			self::get_font_size('.module-text'),
			self::get_line_height('.module-text'),
			self::get_letter_spacing('.module-text'),
			self::get_text_align('.module-text'),
			self::get_text_transform('.module-text'),
			self::get_font_style('.module-text'),
			self::get_text_decoration('.module-text','text_decoration_regular'),
			// Paragraph
			self::get_seperator('paragraph',__('Paragraph', 'themify')),
			self::get_heading_margin_multi_field( '.module-text', 'p', 'top' ),
			self::get_heading_margin_multi_field( '.module-text', 'p', 'bottom' ),
			// Link
			self::get_seperator('link',__('Link', 'themify')),
			self::get_color( '.module-text a','link_color'),
			self::get_color('.module-text a:hover','link_color_hover',__('Color Hover', 'themify')),
			self::get_text_decoration('.module-text a'),
			// Multi-column
			self::get_seperator('multi_columns', __('Multi-columns', 'themify')),
			self::get_multi_columns_count( '.module-text' ),
			self::get_multi_columns_gap( '.module-text' ),
			self::get_multi_columns_divider( '.module-text' ),
			// Padding
			self::get_seperator('padding',__('Padding', 'themify')),
			self::get_padding('.module-text'),
			// Margin
			self::get_seperator('margin',__('Margin', 'themify')),
			self::get_margin('.module-text'),
			// Border
			self::get_seperator('border',__('Border', 'themify')),
			self::get_border('.module-text')
		);

		$heading = array();

		for($i=1;$i<=6;++$i){
			$h = 'h'.$i;
			$heading = array_merge($heading,array( 
				self::get_seperator('font',sprintf(__('Heading %s Font', 'themify'),$i),$i!==1),
				self::get_font_family('.module.module-text '.$h.($i===3?':not(.module-title)':''),'font_family_'.$h),
				self::get_element_font_weight('.module.module-text '.$h.($i===3?':not(.module-title)':''),'font_weight_'.$h),
				self::get_color_type('font_color_type_'.$h,__('Font Color Type', 'themify'),'font_color_'.$h,'font_gradient_color_'.$h),
				self::get_color('.module.module-text '.$h.($i===3?':not(.module-title)':''),'font_color_'.$h,__('Font Color', 'themify'),'color',true),
				self::get_gradient_color('.module.module-text '.$h.($i===3?':not(.module-title)':''),'font_gradient_color_'.$h,__('Font Color', 'themify')),
				self::get_font_size('.module-text '.$h,'font_size_'.$h),
				self::get_line_height('.module-text '.$h,'line_height_'.$h),
				self::get_letter_spacing('.module-text '.$h,'letter_spacing_'.$h),
				self::get_text_transform('.module-text '.$h,'text_transform_'.$h),
				self::get_font_style('.module-text '.$h,'font_style_'.$h,'font_weight_'.$h),
				// Heading  Margin
				self::get_heading_margin_multi_field('.module-text', $h, 'top' ),
				self::get_heading_margin_multi_field('.module-text', $h, 'bottom' ),
			));
		}
		
		$dropcap = array(
			// Background
			self::get_seperator('dropcap_bacground',__( 'Background', 'themify' ),false),
			self::get_color('.tb_module_front.tb_text_dropcap .tb_action_wrap + :first-letter,.tb_text_dropcap > :first-child:first-letter', 'dropcap_background_color',__( 'Background Color', 'themify' ),'background-color'),
			// Font
			self::get_seperator('font',__('Font', 'themify')),
			self::get_font_family('.tb_module_front.tb_text_dropcap .tb_action_wrap + :first-letter,.tb_text_dropcap > :first-child:first-letter','font_dropcap_family'),
			self::get_element_font_weight('.tb_module_front.tb_text_dropcap .tb_action_wrap + :first-letter,.tb_text_dropcap > :first-child:first-letter','dropcap_font_weight'),
			self::get_color('.tb_module_front.tb_text_dropcap .tb_action_wrap + :first-letter,.tb_text_dropcap > :first-child:first-letter','dropcap_font_color',__('Font Color', 'themify')),
			self::get_font_size('.tb_module_front.tb_text_dropcap .tb_action_wrap + :first-letter,.tb_text_dropcap > :first-child:first-letter', 'dropcap_font_size'),
			self::get_line_height('.tb_module_front.tb_text_dropcap .tb_action_wrap + :first-letter,.tb_text_dropcap > :first-child:first-letter', 'dropcap_line_height'),
			self::get_text_transform('.tb_module_front.tb_text_dropcap .tb_action_wrap + :first-letter,.tb_text_dropcap > :first-child:first-letter', 'dropcap_letter_transform'),
			self::get_font_style('.tb_module_front.tb_text_dropcap .tb_action_wrap + :first-letter,.tb_text_dropcap > :first-child:first-letter', 'font_dropcap','font_dropcap_bold'),
            self::get_text_decoration('.tb_module_front.tb_text_dropcap .tb_action_wrap + :first-letter,.tb_text_dropcap > :first-child:first-letter','dropcap_decoration_regular'),
			// Padding
			self::get_seperator('padding',__('Padding', 'themify')),
			self::get_padding('.tb_module_front.tb_text_dropcap .tb_action_wrap + :first-letter,.tb_text_dropcap > :first-child:first-letter','dropcap_padding'),
			// Margin
			self::get_seperator('margin',__('Margin', 'themify')),
			self::get_margin('.tb_module_front.tb_text_dropcap .tb_action_wrap + :first-letter,.tb_text_dropcap > :first-child:first-letter','dropcap_margin'),
			// Border
			self::get_seperator('border',__('Border', 'themify')),
			self::get_border('.tb_module_front.tb_text_dropcap .tb_action_wrap + :first-letter,.tb_text_dropcap > :first-child:first-letter','dropcap_border')
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
					'heading' => array(
						'label' => __('Heading', 'themify'),
						'fields' => $heading
					),
					'dropcap' => array(
						'label' => __('Drop-Cap', 'themify'),
						'fields' => $dropcap
					)
				)
			)
		);

	}

	protected function _visual_template() {
		$module_args = self::get_module_args();?>
		<#
		var font_color_type = '';
		if(themifybuilderapp.activeModel != null){
			var tempData = themifybuilderapp.Forms.serialize('tb_options_styling');
			font_color_type = ('font_color_type' in  tempData && tempData['font_color_type'].indexOf('gradient') !== -1)?'gradient':'solid';
			font_color_type = 'tb-font-color-' + font_color_type;
		}
		#>
		<div class="module module-<?php echo $this->slug; ?> {{ font_color_type }} {{ data.add_css_text }} <# ! _.isUndefined( data.text_drop_cap ) && data.text_drop_cap === 'dropcap' ? print( 'tb_text_dropcap' ) : ''; #>">
			 <!--insert-->
			<# if ( data.mod_title_text ) { #>
			<?php echo $module_args['before_title']; ?>{{{ data.mod_title_text }}}<?php echo $module_args['after_title']; ?>
			<# } #>
			{{{ data.content_text?data.content_text.replace(/(<|&lt;)!--more(.*?)?--(>|&gt;)/, '<span class="tb-text-more-link-indicator"><span>'):'' }}}
		</div>
		<#
		setTimeout(function(){
			if(font_color_type != ''){
			var $ = jQuery;
			for(var i=1;i<=6;i++){
				var h_color_type = ('font_color_type_h'+i in tempData && tempData['font_color_type_h'+i].indexOf('gradient') !== -1)?'gradient':'solid';
				var color_property = 'gradient' == h_color_type ? 'font_gradient_color_h'+i+'-gradient':'font_color_h' + i;
				if('' != tempData[color_property]){
					h_color_type = 'tb-font-color-' + h_color_type;
					$('.tb_element_cid_' + data.cid + ' h' + i).addClass(h_color_type);
				}
			}
		}
		},1);
		#>
	<?php
	}

	/**
	 * Generate read more link for text module
	 *
	 * @param string $content
	 * @return string generated load more link in the text.
	 */
	public static function generate_read_more( $content )
	{
		if (preg_match( '/(<|&lt;)!--more(.*?)?--(>|&gt;)/', $content, $matches)) {
			$text = trim($matches[2]);
			if (!empty($text)) {
				$read_more_text = $text;
			} else {
				$read_more_text = apply_filters( 'themify_builder_more_text', __( 'More ', 'themify' ) );
			}
			$content = str_replace( $matches[0], '<div><span class="more-text" style="display:none">', $content );
			$content .= '</span></div><a href="#" class="tb-text-more-link module-text-more tb-more-tag">' . $read_more_text . '</a>';
		}
		return $content;
	}
}

///////////////////////////////////////
// Module Options
///////////////////////////////////////
Themify_Builder_Model::register_module( 'TB_Text_Module' );
