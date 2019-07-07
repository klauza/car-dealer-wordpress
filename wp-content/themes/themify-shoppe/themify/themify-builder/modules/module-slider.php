<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Module Name: Slider
 * Description: Display slider content
 */
class TB_Slider_Module extends Themify_Builder_Component_Module {
	function __construct() {
		parent::__construct(array(
			'name' => __('Slider', 'themify'),
			'slug' => 'slider'
		));

		add_action( 'init', array( $this, 'setup_slider_cpt' ), 30 );
	}

	public function setup_slider_cpt() {
		if( Themify_Builder_Model::is_cpt_active( 'slider' ) ) {
			///////////////////////////////////////
			// Load Post Type
			///////////////////////////////////////
			$this->meta_box = $this->set_metabox();
			$this->initialize_cpt( array(
				'plural' => __('Slides', 'themify'),
				'singular' => __('Slide', 'themify'),
				'supports' => array('title', 'editor', 'author', 'custom-fields'),
				'menu_icon' => 'dashicons-slides'
			));

			if ( ! shortcode_exists( 'themify_'. $this->slug .'_posts' ) ) {
				add_shortcode( 'themify_'.$this->slug.'_posts', array( $this, 'do_shortcode' ) );
			}
		}
	}

	public function get_options() {

		$visible_opt = array(1 => 1, 2, 3, 4, 5, 6, 7);
                $is_img_enabled = Themify_Builder_Model::is_img_php_disabled();
		$image_sizes = !$is_img_enabled?themify_get_image_sizes_list( false ):array();
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
			20 => __( '20 sec', 'themify' ),
		);
		$display = array(
			'blog' => __('Posts', 'themify'),
			'image' => __('Images', 'themify'),
			'video' => __('Videos', 'themify'),
			'text' => __('Text', 'themify')
		);
		$slider_enabled = Themify_Builder_Model::is_cpt_active( 'slider' );
		$portfolio_enabled = Themify_Builder_Model::is_cpt_active( 'portfolio' );
		$testimonial_enabled = Themify_Builder_Model::is_cpt_active( 'testimonial' );

		$post_types = Themify_Builder_Model::get_public_post_types();
		$taxonomies = Themify_Builder_Model::get_public_taxonomies();

		if( $slider_enabled ) {
			$display['slider'] = __('Slider Posts', 'themify');
			unset( $post_types['slider'],$taxonomies['slider-category'] );
		}
		if( $portfolio_enabled ) {
			$display['portfolio'] = __('Portfolio', 'themify');
			unset( $post_types['portfolio'],$taxonomies['portfolio-category']  );
		}
		if( $testimonial_enabled ) {
			$display['testimonial'] = __('Testimonial', 'themify');
			unset( $post_types['testimonial'],$taxonomies['testimonial-category'] );
		}

		$options = array(
			array(
				'id' => 'mod_title_slider',
				'type' => 'text',
				'label' => __('Module Title', 'themify'),
				'class' => 'large',
                                'render_callback' => array(
                                    'live-selector'=>'.module-title'
                                )
			),
			array(
				'id' => 'layout_display_slider',
				'type' => 'radio',
				'label' => __('Display', 'themify'),
				'options' => $display,
				'default' => 'blog',
				'option_js' => true
			),
			array(
				'id' => 'post_type',
				'type' => 'select',
				'label' => __('Post Type', 'themify'),
				'options' => $post_types,
				'wrap_with_class' => 'tb_group_element tb_group_element_blog'
			),
			array(
				'id' => 'taxonomy',
				'type' => 'select',
				'label' => __('Taxonomy', 'themify'),
				'options' => $taxonomies,
				'wrap_with_class' => 'tb_group_element tb_group_element_blog'
			),
			///////////////////////////////////////////
			// Blog post option
			///////////////////////////////////////////
			array(
				'id' => 'blog_category_slider',
				'type' => 'query_category',
				'label' => __('Category', 'themify'),
				'options' => array(),
				'help' => sprintf(__('Add more <a href="%s" target="_blank">blog posts</a>', 'themify'), admin_url('post-new.php')),
				'wrap_with_class' => 'tb_group_element tb_group_element_blog'
			),
		);
		if( $slider_enabled ) {
			$options[] = array(
				'id' => 'slider_category_slider',
				'type' => 'query_category',
				'label' => __('Category', 'themify'),
				'options' => array(
					'taxonomy' => 'slider-category'
				),
				'help' => sprintf(__('Add more <a href="%s" target="_blank">slider posts</a>', 'themify'), admin_url('post-new.php?post_type=slider')),
				'wrap_with_class' => 'tb_group_element tb_group_element_slider'
			);
		}
		if( $portfolio_enabled ) {
			$options[] = array(
				'id' => 'portfolio_category_slider',
				'type' => 'query_category',
				'label' => __('Category', 'themify'),
				'options' => array(
					'taxonomy' => 'portfolio-category'
				),
				'help' => sprintf(__('Add more <a href="%s" target="_blank">portfolio posts</a>', 'themify'), admin_url('post-new.php?post_type=portfolio')),
				'wrap_with_class' => 'tb_group_element tb_group_element_portfolio'
			);
		}
		if( $testimonial_enabled ) {
			$options[] = array(
				'id' => 'testimonial_category_slider',
				'type' => 'query_category',
				'label' => __('Category', 'themify'),
				'options' => array(
					'taxonomy' => 'testimonial-category'
				),
				'help' => sprintf(__('Add more <a href="%s" target="_blank">testimonial posts</a>', 'themify'), admin_url('post-new.php?post_type=testimonial')),
				'wrap_with_class' => 'tb_group_element tb_group_element_testimonial'
			);
		}
		$options = array_merge( $options, array(
			array(
				'id' => 'posts_per_page_slider',
				'type' => 'text',
				'label' => __('Query', 'themify'),
				'class' => 'xsmall',
				'help' => __('number of posts to query', 'themify'),
				'wrap_with_class' => 'tb_group_element tb_group_element_blog tb_group_element_portfolio tb_group_element_slider tb_group_element_testimonial'
			),
			array(
				'id' => 'offset_slider',
				'type' => 'text',
				'label' => __('Offset', 'themify'),
				'class' => 'xsmall',
				'help' => __('number of post to displace or pass over', 'themify'),
				'wrap_with_class' => 'tb_group_element tb_group_element_blog tb_group_element_portfolio tb_group_element_slider tb_group_element_testimonial'
			),
			array(
				'id' => 'order_slider',
				'type' => 'select',
				'label' => __('Order', 'themify'),
				'help' => __('Descending = show newer posts first', 'themify'),
				'options' => array(
					'desc' => __('Descending', 'themify'),
					'asc' => __('Ascending', 'themify')
				),
				'wrap_with_class' => 'tb_group_element tb_group_element_blog tb_group_element_slider tb_group_element_portfolio tb_group_element_testimonial'
			),
			array(
				'id' => 'orderby_slider',
				'type' => 'select',
				'label' => __('Order By', 'themify'),
				'options' => array(
					'date' => __('Date', 'themify'),
					'id' => __('Id', 'themify'),
					'author' => __('Author', 'themify'),
					'title' => __('Title', 'themify'),
					'name' => __('Name', 'themify'),
					'modified' => __('Modified', 'themify'),
					'rand' => __('Random', 'themify'),
					'comment_count' => __('Comment Count', 'themify'),
					'meta_value' => __( 'Custom Field String', 'themify' ),
					'meta_value_num' => __( 'Custom Field Numeric', 'themify' )
				),
				'binding' => array(
					'meta_value' => array( 'show' => array( 'meta_key_slider' ) ),
					'meta_value_num' => array( 'show' => array( 'meta_key_slider' ) ),
					'date' => array( 'hide' => array( 'meta_key_slider' ) ),
					'id' => array( 'hide' => array( 'meta_key_slider' ) ),
					'author' => array( 'hide' => array( 'meta_key_slider' ) ),
					'title' => array( 'hide' => array( 'meta_key_slider' ) ),
					'name' => array( 'hide' => array( 'meta_key_slider' ) ),
					'rand' => array( 'hide' => array( 'meta_key_slider' ) ),
					'comment_count' => array( 'hide' => array( 'meta_key_slider' ) )
				),
				'wrap_with_class' => 'tb_group_element tb_group_element_blog tb_group_element_slider tb_group_element_portfolio tb_group_element_testimonial'
			),
			array(
				'id' => 'meta_key_slider',
				'type' => 'text',
				'label' => __( 'Custom Field Key', 'themify' ),
			),
			array(
				'id' => 'display_slider',
				'type' => 'select',
				'label' => __('Display', 'themify'),
				'options' => array(
					'content' => __('Content', 'themify'),
					'excerpt' => __('Excerpt', 'themify'),
					'none' => __('None', 'themify')
				),
				'wrap_with_class' => 'tb_group_element tb_group_element_blog tb_group_element_slider tb_group_element_portfolio tb_group_element_testimonial'
			),
			array(
				'id' => 'hide_post_title_slider',
				'type' => 'select',
				'label' => __('Hide Post Title', 'themify'),
				'options' => array(
                                        ''=>'',
					'yes' => __('Yes', 'themify'),
					'no' => __('No', 'themify')
				),
				'wrap_with_class' => 'tb_group_element tb_group_element_blog tb_group_element_slider tb_group_element_portfolio tb_group_element_testimonial'
			),
			array(
				'id' => 'unlink_post_title_slider',
				'type' => 'select',
				'label' => __('Unlink Post Title', 'themify'),
				'options' => array(
                                        ''=>'',
					'yes' => __('Yes', 'themify'),
					'no' => __('No', 'themify')
				),
				'wrap_with_class' => 'tb_group_element tb_group_element_blog tb_group_element_slider tb_group_element_portfolio'
			),
			array(
				'id' => 'hide_feat_img_slider',
				'type' => 'select',
				'label' => __('Hide Featured Image', 'themify'),
				'options' => array(
                                        ''=>'',
					'yes' => __('Yes', 'themify'),
					'no' => __('No', 'themify')
				),
				'wrap_with_class' => 'tb_group_element tb_group_element_blog tb_group_element_slider tb_group_element_portfolio tb_group_element_testimonial'
			),
			array(
				'id' => 'unlink_feat_img_slider',
				'type' => 'select',
				'label' => __('Unlink Featured Image', 'themify'),
				'options' => array(
                                        ''=>'',
					'yes' => __('Yes', 'themify'),
					'no' => __('No', 'themify')
				),
				'wrap_with_class' => 'tb_group_element tb_group_element_blog tb_group_element_slider tb_group_element_portfolio'
			),
			array(
				'id' => 'open_link_new_tab_slider',
				'type' => 'select',
				'label' => __('Open link in a new tab', 'themify'),
				'options' => array(
                                        ''=>'',
					'yes' => __('Yes', 'themify'),
					'no' => __('No', 'themify')
				),
				'wrap_with_class' => 'tb_group_element tb_group_element_blog tb_group_element_slider tb_group_element_portfolio tb_group_element_testimonial'
			),

			///////////////////////////////////////////
			// Image post option
			///////////////////////////////////////////
			array(
				'id' => 'img_content_slider',
				'type' => 'builder',
				'options' => array(
					array(
						'id' => 'img_url_slider',
						'type' => 'image',
						'label' => __('Image URL', 'themify'),
						'class' => 'xlarge',
						'render_callback' => array(
							'repeater' => 'img_content_slider'
						)
					),
					array(
						'id' => 'img_title_slider',
						'type' => 'text',
						'label' => __('Image Title', 'themify'),
						'class' => 'fullwidth',
						'render_callback' => array(
							'repeater' => 'img_content_slider'
						)
					),
					array(
						'id' => 'img_link_slider',
						'type' => 'text',
						'label' => __('Image Link', 'themify'),
						'class' => 'fullwidth',
						'render_callback' => array(
							'repeater' => 'img_content_slider'
						)
					),
					array(
						'id' => 'img_link_params',
						'type' => 'select',
						'label' => '&nbsp;',
						'options' => array(
							'' => '',
							'lightbox' => __( 'Open link in lightbox', 'themify' ),
							'newtab' => __( 'Open link in new tab', 'themify' )
						),
						'render_callback' => array(
							'repeater' => 'img_content_slider'
						)
					),
					array(
						'id' => 'img_caption_slider',
						'type' => 'textarea',
						'label' => __('Image Caption', 'themify'),
						'class' => 'fullwidth',
						'rows' => 6,
						'render_callback' => array(
							'repeater' => 'img_content_slider'
						)
					)
				),
				'wrap_with_class' => 'tb_group_element tb_group_element_image',
				'render_callback' => array(
					'control_type' => 'repeater'
				)
			),

			///////////////////////////////////////////
			// Video post option
			///////////////////////////////////////////
			array(
				'id' => 'video_content_slider',
				'type' => 'builder',
				'options' => array(
					array(
						'id' => 'video_url_slider',
						'type' => 'text',
						'label' => __('Video URL', 'themify'),
						'class' => 'xlarge',
						'help' => array(
							'new_line' => true,
							'text' => __('YouTube, Vimeo, etc', 'themify')
						),
						'render_callback' => array(
							'repeater' => 'video_content_slider'
						)
					),
					array(
						'id' => 'video_title_slider',
						'type' => 'text',
						'label' => __('Video Title', 'themify'),
						'class' => 'fullwidth',
						'render_callback' => array(
							'repeater' => 'video_content_slider'
						)
					),
					array(
						'id' => 'video_title_link_slider',
						'type' => 'text',
						'label' => __('Video Title Link', 'themify'),
						'class' => 'fullwidth',
						'render_callback' => array(
							'repeater' => 'video_content_slider'
						)
					),
					array(
						'id' => 'video_caption_slider',
						'type' => 'textarea',
						'label' => __('Video Caption', 'themify'),
						'class' => 'fullwidth',
						'rows' => 6,
						'render_callback' => array(
							'repeater' => 'video_content_slider'
						)
					),
					array(
						'id' => 'video_width_slider',
						'type' => 'text',
						'label' => __('Video Width', 'themify'),
						'class' => 'xsmall',
						'render_callback' => array(
							'repeater' => 'video_content_slider'
						)
					)
				),
				'wrap_with_class' => 'tb_group_element tb_group_element_video',
				'render_callback' => array(
					'control_type' => 'repeater'
				)
			),

			///////////////////////////////////////////
			// Text Slider option
			///////////////////////////////////////////
			array(
				'id' => 'text_content_slider',
				'type' => 'builder',
				'options' => array(
					array(
						'id' => 'text_caption_slider',
						'type' => 'wp_editor',
						'label' => false,
						'class' => 'fullwidth builder-field',
						'rows' => 6,
						'render_callback' => array(
							'repeater' => 'text_content_slider'
						)
					)
				),
				'wrap_with_class' => 'tb_group_element tb_group_element_text',
				'render_callback' => array(
					'control_type' => 'repeater'
				)
			),

			array(
				'id' => 'layout_slider',
				'type' => 'layout',
				'label' => __('Slider Layout', 'themify'),
				'separated' => 'top',
                                'mode'=>'sprite',
				'options' => array(
					array('img' => 'slider_default', 'value' => 'slider-default', 'label' => __('Slider Default', 'themify')),
					array('img' => 'slider_image_top', 'value' => 'slider-overlay', 'label' => __('Slider Overlay', 'themify')),
					array('img' => 'slider_caption_overlay', 'value' => 'slider-caption-overlay', 'label' => __('Slider Caption Overlay', 'themify')),
					array('img' => 'slider_agency', 'value' => 'slider-agency', 'label' => __('Agency', 'themify'))
				),
				'render_callback' => array(
					'binding' => 'live',
					'selector' => '' // apply to the container module
				)
			),
			array(
				'id' => 'image_size_slider',
				'type' => 'select',
				'label' => __('Image Size', 'themify'),
				'hide' => !$is_img_enabled,
				'options' => $image_sizes,
				'wrap_with_class' => 'tb_group_element tb_group_element_blog tb_group_element_slider tb_group_element_portfolio tb_group_element_image'
			),
			array(
				'id' => 'img_w_slider',
				'type' => 'text',
				'label' => __('Image Width', 'themify'),
				'class' => 'xsmall',
				'help' => 'px',
				'wrap_with_class' => 'tb_group_element tb_group_element_blog tb_group_element_slider tb_group_element_portfolio tb_group_element_image'
			),
			array(
				'id' => 'img_fullwidth_slider',
				'type' => 'checkbox',
				'label' => '&nbsp;',
				'help' => false,
				'wrap_with_class' => '',
				'options' => array(
							array( 'name' => 'fullwidth', 'value' =>__('Auto fullwidth image', 'themify') )
						)
			),
			array(
				'id' => 'img_h_slider',
				'type' => 'text',
				'label' => __('Image Height', 'themify'),
				'class' => 'xsmall',
				'help' => 'px',
				'wrap_with_class' => 'tb_group_element tb_group_element_blog tb_group_element_slider tb_group_element_portfolio tb_group_element_image'
			),

			array(
				'id' => 'slider_option_slider',
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
				'id' => 'css_slider',
				'type' => 'text',
				'label' => __('Additional CSS Class', 'themify'),
				'class' => 'large exclude-from-reset-field',
				'help' => sprintf( '<br/><small>%s</small>', __('Add additional CSS class(es) for custom styling (<a href="https://themify.me/docs/builder#additional-css-class" target="_blank">learn more</a>).', 'themify') )
			)
		) );
		return $options;
	}

	public function get_default_settings() {
		return array(
			'posts_per_page_slider' => 4,
			'display_slider' => 'none',
			'img_w_slider' => 360,
			'img_h_slider' => 200,
			'visible_opt_slider' => 3,
                        'post_type'=>'post'
		);
	}
        
        public function get_visual_type() {
            return 'ajax';            
        }

	public function get_styling() {
		$general = array(
                         // Background
                        self::get_seperator('image_bacground',__( 'Background', 'themify' ),false),
                        self::get_color('.module-slider', 'background_color',__( 'Background Color', 'themify' ),'background-color'),
			// Font
                        self::get_seperator('font',__('Font', 'themify')),
                        self::get_font_family(array( '.module-slider .slide-content', '.module-slider .slide-content .slide-title', '.module-slider .slide-content .slide-title a' )),
                        self::get_element_font_weight(array( '.module-slider .slide-content', '.module-slider .slide-content .slide-title', '.module-slider .slide-content .slide-title a' )),
						self::get_color_type('font_color_type',__('Font Color Type', 'themify'),'font_color','font_gradient_color'),
						self::get_color(array( '.module-slider .slide-content', '.module-slider .slide-content h1', '.module-slider .slide-content h2', '.module-slider .slide-content h3', '.module-slider .slide-content h4', '.module-slider .slide-content h5', '.module-slider .slide-content h6', '.module-slider .slide-content .slide-title', '.module-slider .slide-content .slide-title a' ),'font_color',__('Font Color', 'themify'),'color',true),
						self::get_gradient_color(array( '.module-slider .slide-content h3 a','.module-slider .slide-content p', '.module-slider .slide-content h1', '.module-slider .slide-content h2', '.module-slider .slide-content h4', '.module-slider .slide-content h5', '.module-slider .slide-content h6', '.module-slider .slide-content .slide-title', '.module-slider .slide-content .slide-title a' ),'font_gradient_color',__('Font Color', 'themify')),
                        self::get_font_size('.module-slider .slide-content'),
                        self::get_line_height('.module-slider .slide-content'),
                        self::get_letter_spacing('.module-slider .slide-content'),
                        self::get_text_align('.module-slider .slide-content'),
                        self::get_text_transform('.module-slider .slide-content'),
                        self::get_font_style('.module-slider .slide-content'),
                        self::get_text_decoration('.module-slider .slide-content','text_decoration_regular'),
			// Link
                        self::get_seperator('link',__('Link', 'themify')),
                        self::get_color( '.module-slider a','link_color'),
                        self::get_color('.module-slider a:hover','link_color_hover',__('Color Hover', 'themify')),
                        self::get_text_decoration('.module-slider a'),
			 // Padding
                        self::get_seperator('padding',__('Padding', 'themify')),
                        self::get_padding('.module-slider'),
			// Margin
                        self::get_seperator('margin',__('Margin', 'themify')),
                        self::get_margin('.module-slider'),
			// Border
                        self::get_seperator('border',__('Border', 'themify')),
                        self::get_border('.module-slider')
		);
		
		$container = array(
			// Background
                        self::get_seperator('image_background',__('Background', 'themify'),false),
                        self::get_color(' .themify_builder_slider .slide-inner-wrap','b_c_container',__('Background Color', 'themify'),'background-color'),
			// Padding
                        self::get_seperator('padding',__('Padding', 'themify')),
                        self::get_padding(' .themify_builder_slider .slide-inner-wrap','p_container'),
			// Margin
                        self::get_seperator('margin',__('Margin', 'themify')),
                        self::get_margin(' .themify_builder_slider .slide-inner-wrap','m_container'),
			// Border
                        self::get_seperator('border',__('Border', 'themify')),
                        self::get_border(' .themify_builder_slider .slide-inner-wrap','b_container')
		);

		$title = array(
			// Font
                        self::get_seperator('font',__('Font', 'themify'),false),
                        self::get_font_family(array( '.module-slider .slide-content .slide-title', '.module-slider .slide-content .slide-title a' ),'font_family_title'),
                        self::get_element_font_weight(array( '.module-slider .slide-content .slide-title', '.module-slider .slide-content .slide-title a' ),'font_weight_title'),
                        self::get_color(array( '.module-slider .slide-content .slide-title', '.module-slider .slide-content .slide-title a' ),'font_color_title',__('Font Color', 'themify')),
                        self::get_color(array( '.module-slider .slide-content .slide-title:hover', '.module-slider .slide-content .slide-title a:hover' ),'font_color_title_hover',__('Color Hover', 'themify')),
                        self::get_font_size( '.module-slider .slide-content .slide-title','font_size_title'),
                        self::get_line_height('.module-slider .slide-content .slide-title','line_height_title'),
						self::get_letter_spacing('.module-slider .slide-content .slide-title', 'letter_spacing_title'),
						self::get_text_transform('.module-slider .slide-content .slide-title', 'text_transform_title'),
						self::get_font_style('.module-slider .slide-content .slide-title', 'font_title','font_title_bold'),
			// Margin
                        self::get_seperator('margin',__('Margin', 'themify')),
                        self::get_margin('.module-slider .slide-content .slide-title','m_title'),
		);

		$content = array(
			// Font
			self::get_seperator('font',__('Font', 'themify'),false),
			self::get_font_family(array( '.module-slider .slide-content' ),'font_family_content'),
			self::get_element_font_weight(array( '.module-slider .slide-content' ),'font_weight_content'),
			self::get_color(array( '.module-slider .slide-content' ),'font_color_content',__('Font Color', 'themify')),
			self::get_font_size( '.module-slider .slide-content','font_size_content'),
			self::get_line_height('.module-slider .slide-content','line_height_content'),
			// Multi columns
			self::get_seperator('multi_columns', __('Multi-columns', 'themify')),
			self::get_multi_columns_count( ' .slide-content' ),
			self::get_multi_columns_gap( ' .slide-content' ),
			self::get_multi_columns_divider( ' .slide-content' ),
			// Padding
			self::get_seperator('padding',__('Padding', 'themify')),
			self::get_padding('.module-slider .slide-content','p_content'),
		);

		$controls = array(
			// Arrows
                        self::get_seperator('image_background',__('Arrows', 'themify'),false),
                        self::get_color('.themify_builder_slider_wrap.themify_builder_slider_vertical .carousel-prev,.themify_builder_slider_wrap.themify_builder_slider_vertical .carousel-next','b_c_arrows_controls',__('Background Color', 'themify'),'background-color'),
                        self::get_color('.themify_builder_slider_wrap.themify_builder_slider_vertical .carousel-prev:hover,.themify_builder_slider_wrap.themify_builder_slider_vertical .carousel-next:hover','b_c_h_arrows_controls',__('Hover Background Color', 'themify'),'background-color'),
						self::get_color(array( '.themify_builder_slider_wrap .carousel-prev::before,.themify_builder_slider_wrap .carousel-next::before' ),'f_c_arrows_controls',__('Color', 'themify')),
			// Pager
                        self::get_seperator('image_background',__('Pager', 'themify'),false),
						self::get_color(array( '.themify_builder_slider_wrap .carousel-pager a' ),'f_c_pager_controls',__('Color', 'themify')),
						self::get_color(array( '.themify_builder_slider_wrap .carousel-pager a:hover,.themify_builder_slider_wrap .carousel-pager a.selected' ),'f_c_h_pager_controls',__('Hover Color', 'themify'))
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
						'label' => __('Slide Container', 'themify'),
						'fields' => $container
					),
					'title' => array(
						'label' => __('Slider Title', 'themify'),
						'fields' => $title
					),
					'content' => array(
						'label' => __('Slider Content', 'themify'),
						'fields' => $content
					),
					'controls' => array(
						'label' => __('Slider Controls', 'themify'),
						'fields' => $controls
					)
				)
			)
		);

	}

	function set_metabox() {

		/** Slider Meta Box Options */
		$meta_box = array(
			// Feature Image
			Themify_Builder_Model::$post_image,
			// Featured Image Size
			Themify_Builder_Model::$featured_image_size,
			// Image Width
			Themify_Builder_Model::$image_width,
			// Image Height
			Themify_Builder_Model::$image_height,
			// External Link
			Themify_Builder_Model::$external_link,
			// Lightbox Link
			Themify_Builder_Model::$lightbox_link,
			array(
				'name' 		=> 'video_url',
				'title' 	=> __('Video URL', 'themify'),
				'description' => __('URL to embed a video instead of featured image', 'themify'),
				'type' 		=> 'textbox',
				'meta'		=> array()
			)
		);
		return $meta_box;
	}

	function do_shortcode( $atts ) {

		extract( shortcode_atts( array(
			'visible' => '1',
			'scroll' => '1',
			'auto' => 0,
			'pause_hover' => 'no',
			'wrap' => 'yes',
			'excerpt_length' => '20',
			'speed' => 'normal',
			'slider_nav' => 'yes',
			'pager' => 'yes',
			'limit' => 5,
			'category' => 0,
			'image' => 'yes',
			'image_w' => '240px',
			'image_fullwidth' => '',
			'image_h' => '180px',
			'more_text' => __('More...', 'themify'),
			'title' => 'yes',
			'display' => 'none',
			'post_meta' => 'no',
			'post_date' => 'no',
			'width' => '',
			'height' => '',
			'class' => '',
			'unlink_title' => 'no',
			'unlink_image' => 'no',
			'image_size' => 'thumbnail',
			'post_type' => 'post',
			'taxonomy' => 'category',
			'order' => 'DESC',
			'orderby' => 'date',
			'effect' => 'scroll',
			'style' => 'slider-default'
		), $atts ) );

		$sync = array(
			'mod_title_slider' => '',
			'layout_display_slider' => 'slider',
			'slider_category_slider' => $category,
			'posts_per_page_slider' => $limit,
			'offset_slider' => '',
			'order_slider' => $order,
			'orderby_slider' => $orderby,
			'display_slider' => $display,
			'hide_post_title_slider' => $title === 'yes' ? 'no' : 'yes',
			'unlink_post_title_slider' => $unlink_title,
			'hide_feat_img_slider' => '',
			'unlink_feat_img_slider' => $unlink_image,
			'layout_slider' => $style,
			'image_size_slider' => $image_size,
			'img_w_slider' => $image_w,
			'img_fullwidth_slider' => $image_fullwidth,
			'img_h_slider' => $image_h,
			'visible_opt_slider' => $visible,
			'auto_scroll_opt_slider' => $auto,
			'scroll_opt_slider' => $scroll,
			'speed_opt_slider' => $speed,
			'effect_slider' => $effect,
			'pause_on_hover_slider' => $pause_hover,
			'wrap_slider' => $wrap,
			'show_nav_slider' => $pager,
			'show_arrow_slider' => $slider_nav,
			'left_margin_slider' => '',
			'right_margin_slider' => '',
			'css_slider' => $class
		);
		$module = array(
			'module_ID' => $this->slug . '-' . rand(0,10000),
			'mod_name' => $this->slug,
			'settings' => $sync
		);

		return self::retrieve_template( 'template-' . $this->slug .'-' . $this->slug . '.php', $module, '', '', false );
	}

	/**
	 * Render plain content for static content.
	 * 
	 * @param array $module 
	 * @return string
	 */
	public function get_plain_content( $module ) {
		$mod_settings = wp_parse_args( $module['mod_settings'], array(
			'layout_display_slider' => 'blog'
		) );
		if ( 'blog' === $mod_settings['layout_display_slider'] ) return '';
		return parent::get_plain_content( $module );
	}
}

///////////////////////////////////////
// Module Options
///////////////////////////////////////
Themify_Builder_Model::register_module( 'TB_Slider_Module' );
