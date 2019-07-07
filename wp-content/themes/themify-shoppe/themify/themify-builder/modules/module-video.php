<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Module Name: Video
 * Description: Display Video content
 */
class TB_Video_Module extends Themify_Builder_Component_Module {
	function __construct() {
                self::$texts['title_video'] =  __('Video Title', 'themify');
                self::$texts['caption_video'] = __('Video Caption', 'themify');
		parent::__construct(array(
			'name' => __('Video', 'themify'),
			'slug' => 'video'
		));
	}
        
        public function get_title( $module ) {
		return isset( $module['mod_settings']['title_video'] ) ? esc_html( $module['mod_settings']['title_video'] ) : '';
	}

	public function get_options() {
		 return array(
			array(
				'id' => 'mod_title_video',
				'type' => 'text',
				'label' => __('Module Title', 'themify'),
				'class' => 'large',
                                'render_callback' => array(
                                    'live-selector'=>'.module-title'
				)
			),
			array(
				'id' => 'style_video',
				'type' => 'layout',
				'label' => __('Video Style', 'themify'),
                                'mode'=>'sprite',
				'options' => array(
					array('img' => 'video_top', 'value' => 'video-top', 'label' => __('Video Top', 'themify')),
					array('img' => 'video_left', 'value' => 'video-left', 'label' => __('Video Left', 'themify')),
					array('img' => 'video_right', 'value' => 'video-right', 'label' => __('Video Right', 'themify')),
					array('img' => 'video_overlay', 'value' => 'video-overlay', 'label' => __('Video Overlay', 'themify'))
				),
				'render_callback' => array(
					'binding' => 'live',
					'selector' => '' // empty means apply to module container
				)
			),
			array(
				'id' => 'url_video',
				'type' => 'text',
				'label' => __('Video URL', 'themify'),
				'class' => 'fullwidth',
				'help' => __('YouTube, Vimeo, etc. video <a href="https://themify.me/docs/video-embeds" target="_blank">embed link</a>', 'themify')
			),
			array(
				'id' => 'autoplay_video',
				'type' => 'radio',
				'label' => __( 'Autoplay', 'themify' ),
				'options' => array(
					'no'=> __( 'No', 'themify' ),
					'yes'=>__( 'Yes', 'themify' ),
				),
				'default' => 'no'
			),
			array(
				'id' => 'width_video',
				'type' => 'text',
				'label' => __('Video Width', 'themify'),
				'class' => 'xsmall',
				'help' => __('Enter fixed witdth (eg. 200px) or relative (eg. 100%). Video height is auto adjusted.', 'themify'),
				'break' => true,
				'unit' => array(
					'id' => 'unit_video',
					'options' => array(
						array( 'id' => 'pixel_unit', 'value' => 'px'),
						array( 'id' => 'percent_unit', 'value' => '%')
					)
				)
			),
			array(
				'id' => 'title_video',
				'type' => 'text',
				'label' =>self::$texts['title_video'],
				'class' => 'xlarge',
                                'render_callback' => array(
                                    'live-selector'=>'.video-title'
				)
			),
			array(
				'id' => 'title_link_video',
				'type' => 'text',
				'label' => __('Video Title Link', 'themify'),
				'class' => 'xlarge'
			),
			array(
				'id' => 'caption_video',
				'type' => 'textarea',
				'label' => self::$texts['caption_video'],
				'class' => 'fullwidth',
                                'render_callback' => array(
                                    'live-selector'=>'.video-caption'
				)
			),
			// Additional CSS
			array(
				'type' => 'separator',
				'meta' => array( 'html' => '<hr/>')
			),
			array(
				'id' => 'css_video',
				'type' => 'text',
				'label' => __('Additional CSS Class', 'themify'),
				'class' => 'large exclude-from-reset-field',
				'help' => sprintf( '<br/><small>%s</small>', __( 'Add additional CSS class(es) for custom styling (<a href="https://themify.me/docs/builder#additional-css-class" target="_blank">learn more</a>).', 'themify' ) )
			)
		);
	}

	public function get_default_settings() {
		return  array(
			'url_video' => 'https://www.youtube.com/watch?v=waM20ewLj34'
		);
	}
        
        public function get_visual_type() {
            return 'ajax';            
        }
        

	public function get_styling() {
		$general = array(
			// Background
						self::get_seperator('image_bacground',__( 'Background', 'themify' ),false),
						self::get_color('.module-video', 'background_color',__( 'Background Color', 'themify' ),'background-color'),
			// Font
						self::get_seperator('font',__('Font', 'themify')),
						self::get_font_family(array( '.module-video .video-content', '.module-video .video-title', '.module-video .video-title a' )),
						self::get_element_font_weight(array( '.module-video .video-content', '.module-video .video-title', '.module-video .video-title a' )),
						self::get_color_type('font_color_type',__('Font Color Type', 'themify'),'font_color','font_gradient_color'),
						self::get_color(array( '.module-video .video-content', '.module-video .video-title', '.module-video .video-title a', '.module-video h1', '.module-video h2', '.module-video h3:not(.module-title)', '.module-video h4', '.module-video h5', '.module-video h6' ),'font_color',__('Font Color', 'themify'),'color',true),
						self::get_gradient_color(array( '.module-video .video-title a','.module-video .video-caption' ),'font_gradient_color',__('Font Color', 'themify')),	
                        self::get_font_size( '.module-video .video-content'),
                        self::get_line_height('.module-video .video-content'),
                        self::get_letter_spacing('.module-video .video-content'),
                        self::get_text_align('.module-video .video-content'),
                        self::get_text_transform('.module-video .video-content'),
                        self::get_font_style('.module-video .video-content'),
                        self::get_text_decoration('.module-video .video-content','text_decoration_regular'),
			// Link
                        self::get_seperator('link',__('Link', 'themify')),
                        self::get_color( '.module-video a','link_color'),
                        self::get_color('.module-video a:hover','link_color_hover',__('Color Hover', 'themify')),
                        self::get_text_decoration('.module-video a'),
                         // Padding
                        self::get_seperator('padding',__('Padding', 'themify')),
                        self::get_padding('.module-video'),
			// Margin
                        self::get_seperator('margin',__('Margin', 'themify')),
                        self::get_margin('.module-video'),
                        // Border
                        self::get_seperator('border',__('Border', 'themify')),
                        self::get_border('.module-video')
		);

		$video_title = array(
                        self::get_seperator('font',__('Font', 'themify'),false),
                        self::get_font_family(array( '.module-video .video-title', '.module-video .video-title a' ),'font_family_title'),
                        self::get_element_font_weight(array( '.module-video .video-title', '.module-video .video-title a' ),'font_weight_title'),
                        self::get_color(array( '.module-video .video-title', '.module-video .video-title a' ),'font_color_title',__('Font Color', 'themify')),
                        self::get_color( array( '.module-video .video-title:hover', '.module-video .video-title a:hover' ),'font_color_title_hover',__('Color Hover', 'themify')),
                        self::get_font_size( '.module-video .video-title','font_size_title'),
                        self::get_line_height('.module-video .video-title','line_height_title'),
						self::get_letter_spacing('.module-video .video-title', 'letter_spacing_title'),
						self::get_text_transform('.module-video .video-title', 'text_transform_title'),
						self::get_font_style('.module-video .video-title', 'font_title','font_title_bold'),
		);

		$video_caption = array(
						// Background
						self::get_seperator('image_bacground', __('Caption Overlay', 'themify'), false),
						self::get_color('.module-video.video-overlay .video-wrap + .video-content', 'background_color_video_caption', __('Overlay', 'themify'), 'background-color'),
						self::get_color('.module-video.video-overlay:hover .video-wrap + .video-content', 'b_c_h_v_caption', __('Overlay Hover', 'themify'), 'background-color'),
						self::get_color(array('.module-video.video-overlay:hover .video-content .video-title', '.module-video.video-overlay:hover .video-content .video-caption'), 'f_c_h_v_caption', __('Overlay Hover Font Color', 'themify')),
                        // Font
                        self::get_seperator('font',__('Font', 'themify'),false),
                        self::get_font_family('.module-video .video-content .video-caption','font_family_caption'),
                        self::get_element_font_weight('.module-video .video-content .video-caption','font_weight_caption'),
                        self::get_color('.module-video .video-content .video-caption','font_color_caption',__('Font Color', 'themify')),
                        self::get_font_size('.module-video .video-content .video-caption','font_size_caption'),
                        self::get_line_height('.module-video .video-content .video-caption','line_height_caption')
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
						'label' => __('Video Title', 'themify'),
						'fields' => $video_title
					),
					'caption' => array(
						'label' => __('Video Caption', 'themify'),
						'fields' => $video_caption
					)
				)
			)
		);

	}

	public static function autoplay_callback( $match ){
		return str_replace( $match[1], add_query_arg( 'autoplay', 1, $match[1] ), $match[0] );
	}
        
        public static function modify_youtube_embed_url($html, $url, $args){
            $parse_url = parse_url($url);
            if(!empty($parse_url['query']) || !empty($parse_url['fragment'])){
                    $parse_url['host'] = str_replace('www.','',$parse_url['host']);
                    $query = !empty($parse_url['query'])?$parse_url['query']:false;
                    $query.= !empty($parse_url['fragment'])?$parse_url['fragment']:'';
                    if(trim($parse_url['path'],'/')!=='playlist' && ($parse_url['host']==='youtu.be' || $parse_url['host']==='youtube.com')){
                            $query = preg_replace('@v=([^"&]*)@','',$query);
                            $query = str_replace('&038;','&',$query);
                            return  $query?preg_replace('@embed/([^"&]*)@', 'embed/$1?'.$query, $html):$html;
                    }
                    elseif($parse_url['host']==='vimeo.com'){
                             $query = str_replace('&038;','&',$query);
                             return  $query?preg_replace('@video/([^"&]*)@', 'video/$1?'.$query, $html):$html;
                    }
            }
            return $html;
        }
}

///////////////////////////////////////
// Module Options
///////////////////////////////////////
Themify_Builder_Model::register_module( 'TB_Video_Module' );
