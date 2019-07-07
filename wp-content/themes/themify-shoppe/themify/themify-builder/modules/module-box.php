<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Module Name: Box
 * Description: Display box content
 */
class TB_Box_Module extends Themify_Builder_Component_Module {
	function __construct() {
                self::$texts['content_box'] =__('Box content', 'themify');
		parent::__construct(array(
			'name' => __('Box', 'themify'),
			'slug' => 'box'
		));
	}

	public function get_options() {
		return array(
			array(
				'id' => 'mod_title_box',
				'type' => 'text',
				'label' => __('Module Title', 'themify'),
				'class' => 'large',
				'render_callback' => array(
					'binding' => 'live',
                                        'live-selector'=>'.module-title'
				)
			),
			array(
				'id' => 'content_box',
				'type' => 'wp_editor',
				'class' => 'fullwidth',
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'color_box',
				'type' => 'layout',
                                'mode'=>'sprite',
                                'class'=>'tb_colors',
				'label' => __('Box Color', 'themify'),
				'options' => Themify_Builder_Model::get_colors(),
				'bottom' => true,
				'render_callback' => array(
					'binding' => 'live'
				)
			),
			array(
				'id' => 'appearance_box',
				'type' => 'checkbox',
				'label' => __('Appearance', 'themify'),
				'options' =>Themify_Builder_Model::get_appearance(),
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
				'id' => 'add_css_box',
				'type' => 'text',
				'label' => __('Additional CSS Class', 'themify'),
				'help' => sprintf( '<br/><small>%s</small>', __('Add additional CSS class(es) for custom styling (<a href="https://themify.me/docs/builder#additional-css-class" target="_blank">learn more</a>).', 'themify') ),
				'class' => 'large exclude-from-reset-field',
				'render_callback' => array(
					'binding' => 'live'
				)
			)
		);
	}

	public function get_default_settings() {
		return array(
			'content_box' =>self::$texts['content_box']
		);
	}

	

	public function get_styling() {
		$general = array(
			//bacground
                        self::get_seperator('image_bacground',__( 'Background', 'themify' ),false),
                        self::get_image('.module-box .module-box-content'),
                        self::get_color('.module-box .module-box-content', 'background_color',__( 'Background Color', 'themify' ),'background-color'),
                        self::get_repeat('.module-box .module-box-content'),
						self::get_position('.module-box .module-box-content'),
			// Font
                        self::get_seperator('font',__('Font', 'themify')),
                        self::get_font_family(array('.module-box','.module-box h1','.module-box h2','.module-box h3:not(.module-title)','.module-box h4','.module-box h5','.module-box h6')),
                        self::get_element_font_weight(array('.module-box','.module-box h1','.module-box h2','.module-box h3:not(.module-title)','.module-box h4','.module-box h5','.module-box h6')),
			self::get_color_type('font_color_type',__('Font Color Type', 'themify'),'font_color','font_gradient_color'),
			self::get_color(array('.module-box .module-box-content','.module-box h1','.module-box h2','.module-box h3:not(.module-title)','.module-box h4','.module-box h5','.module-box h6'),'font_color',__('Font Color', 'themify'),'color',true),
			self::get_gradient_color(array('.module-box p','.module-box h1','.module-box h2','.module-box h3:not(.module-title)','.module-box h4','.module-box h5','.module-box h6'),'font_gradient_color',__('Font Color', 'themify')),
			self::get_font_size('.module-box'),
			self::get_line_height('.module-box'),
                        self::get_letter_spacing('.module-box'),
                        self::get_text_align('.module-box'),
			self::get_text_transform('.module-box'),
                        self::get_font_style('.module-box'),
                        self::get_text_decoration('.module-box .module-box-content','text_decoration_regular'),
			// Link
                        self::get_seperator('link',__('Link', 'themify')),
                        self::get_color( '.module-box a','link_color'),
                        self::get_color( '.module-box a:hover','link_color_hover',__('Color Hover', 'themify')),
                        self::get_text_decoration( '.module-box a'),
			// Padding
                        self::get_seperator('padding',__('Padding', 'themify')),
                        self::get_padding('.module-box .module-box-content'),
			// Margin
                        self::get_seperator('margin',__('Margin', 'themify')),
                        self::get_margin('.module-box'),
			// Border
                        self::get_seperator('border',__('Border', 'themify')),
                        self::get_border('.module-box .module-box-content')
                       
		);
                $heading = array();
                for($i=1;$i<=6;++$i){
                    $h = 'h'.$i;
                    $heading = array_merge($heading,array(
			self::get_seperator('font',sprintf(__('Heading %s Font', 'themify'),$i),$i!==1),
			self::get_font_family('.module.module-box '.$h.($i===3?':not(.module-title)':''),'font_family_'.$h),
			self::get_element_font_weight('.module.module-box '.$h.($i===3?':not(.module-title)':''),'font_weight_'.$h),
			self::get_color_type('font_color_type_'.$h,__('Font Color Type', 'themify'),'font_color_'.$h,'font_gradient_color_'.$h),
			self::get_color('.module.module-box '.$h.($i===3?':not(.module-title)':''),'font_color_'.$h,__('Font Color', 'themify'),'color',true),
			self::get_gradient_color('.module.module-box '.$h.($i===3?':not(.module-title)':''),'font_gradient_color_'.$h,__('Font Color', 'themify')),
			self::get_font_size('.module-box '.$h,'font_size_'.$h),
			self::get_line_height('.module-box '.$h,'line_height_'.$h),
			self::get_letter_spacing('.module-box '.$h,'letter_spacing_'.$h),
			self::get_text_transform('.module-box '.$h,'text_transform_'.$h),
			self::get_font_style('.module-box '.$h,'font_style_'.$h,'font_weight_'.$h),
			// Heading  Margin
			self::get_heading_margin_multi_field('.module-box', $h, 'top' ),
			self::get_heading_margin_multi_field('.module-box', $h, 'bottom' ),
			));
                }
		return array(
			array(
				'type' => 'tabs',
				'id' => 'module-styling',
				'tabs' => array(
					'general' => array(
                                            'label' => __('General', 'themify'),
                                            'fields' => $general
					),
					'heading' => array(
						'label' => __('Heading', 'themify'),
						'fields' => $heading
					)
				)
			),
		);

	}

	protected function _visual_template() { 
		$module_args = self::get_module_args(); ?>
		<#
		var font_color_type = '';
		if(themifybuilderapp.activeModel != null){
			var tempData = themifybuilderapp.Forms.serialize('tb_options_styling');
			font_color_type = ('font_color_type' in  tempData && tempData['font_color_type'].indexOf('gradient') !== -1)?'gradient':'solid';
			font_color_type = 'tb-font-color-' + font_color_type;
		}
		#>
		<div class="module module-<?php echo $this->slug ; ?> {{ font_color_type }}">
                        <!--insert-->
			<# if ( data.mod_title_box ) { #>
			<?php echo $module_args['before_title']; ?>{{{ data.mod_title_box }}}<?php echo $module_args['after_title']; ?>
			<# } #>
			
			<div class="ui module-<?php echo $this->slug; ?>-content {{ data.color_box }} {{ data.add_css_box }} {{ data.background_repeat }} <# ! _.isUndefined( data.appearance_box ) ? print( data.appearance_box.split('|').join(' ') ) : ''; #>">
				{{{ data.content_box }}}
			</div>
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
}

///////////////////////////////////////
// Module Options
///////////////////////////////////////
Themify_Builder_Model::register_module( 'TB_Box_Module' );
