<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Module Name: WooCommerce
 */
class TB_Products_Module extends Themify_Builder_Component_Module {
	function __construct() {
		parent::__construct(array(
			'name' => __('WooCommerce', 'builder-wc'),
			'slug' => 'products'
		));
	}

	function get_assets() {
		$instance = Builder_Woocommerce::get_instance();
		return array(
			'selector' => '.module-products',
			'css' => themify_enque($instance->url . 'assets/style.css'),
			'ver' => $instance->version
		);
	}

	public function get_options() {
                $is_img_enabled = Themify_Builder_Model::is_img_php_disabled();
		$image_sizes = !$is_img_enabled?themify_get_image_sizes_list( false ):array();
		return array(
			array(
				'id' => 'mod_title_products',
				'type' => 'text',
				'label' => __('Module Title', 'builder-wc'),
				'class' => 'large',
                                'render_callback' => array(
                                    'live-selector'=>'.module-title'
                                )
			),
			array(
				'id' => 'query_products',
				'type' => 'radio',
				'label' => __('Type', 'builder-wc'),
				'options' => array(
					'all' => __('All Products', 'builder-wc'),
					'featured' => __('Featured Products', 'builder-wc'),
					'onsale' => __('On Sale', 'builder-wc'),
					'toprated' => __('Top Rated', 'builder-wc'),
				),
				'default' => 'all',
			),
			array(
				'id' => 'category_products',
				'type' => 'query_category',
				'label' => __('Category', 'builder-wc'),
				'options' => array(
					'taxonomy' => 'product_cat',
				),
			),
                        array(
				'id' => 'hide_child_products',
				'type' => 'select',
				'label' => __('Show products only from parent category', 'builder-wc'),
				'options' => array(
					'no' => __('No', 'builder-wc'),
					'yes' => __('Yes', 'builder-wc'),
				)
			),
			array(
				'id' => 'hide_free_products',
				'type' => 'select',
				'label' => __('Hide Free Products', 'builder-wc'),
				'options' => array(
					'no' => __('No', 'builder-wc'),
					'yes' => __('Yes', 'builder-wc'),
				)
			),
                        array(
 				'id' => 'hide_outofstock_products',
 				'type' => 'select',
 				'label' => __('Hide Out of Stock Products', 'builder-wc'),
 				'options' => array(
 					'no' => __('No', 'builder-wc'),
 					'yes' => __('Yes', 'builder-wc'),
 				)
 			),
			array(
				'id' => 'post_per_page_products',
				'type' => 'text',
				'label' => __('Limit', 'builder-wc'),
				'class' => 'xsmall',
				'help' => __('number of posts to show', 'builder-wc')
			),
			array(
				'id' => 'offset_products',
				'type' => 'text',
				'label' => __('Offset', 'builder-wc'),
				'class' => 'xsmall',
				'help' => __('number of post to displace or pass over', 'builder-wc')
			),
			array(
				'id' => 'orderby_products',
				'type' => 'select',
				'label' => __('Order By', 'builder-wc'),
				'options' => array(
					'date' => __('Date', 'builder-wc'),
					'price' => __('Price', 'builder-wc'),
					'sales' => __('Sales', 'builder-wc'),
					'id' => __('Id', 'builder-wc'),
					'title' => __('Title', 'builder-wc'),
					'rand' => __('Random', 'builder-wc'),
				)
			),
			array(
				'id' => 'order_products',
				'type' => 'select',
				'label' => __('Order', 'builder-wc'),
				'help' => __('Descending = show newer posts first', 'builder-wc'),
				'options' => array(
					'desc' => __('Descending', 'builder-wc'),
					'asc' => __('Ascending', 'builder-wc')
				)
			),
			array(
				'id' => 'template_products',
				'type' => 'radio',
				'label' => __('Display as', 'builder-wc'),
				'options' => apply_filters( 'builder_products_templates', array(
					'list' => __('List', 'builder-wc'),
					'slider' => __('Slider', 'builder-wc'),
				) ),
				'default' => 'list',
				'option_js' => true
			),
			array(
				'id' => 'list',
				'type' => 'group',
				'fields' => array(
					array(
						'id' => 'layout_products',
						'type' => 'layout',
                        'mode'=>'sprite',
						'label' => __('Layout', 'builder-wc'),
						'options' => array(
							array('img' => 'list_post', 'value' => 'list-post', 'label' => __('List Post', 'builder-wc')),
							array('img' => 'grid3', 'value' => 'grid3', 'label' => __('Grid 3', 'builder-wc')),
							array('img' => 'grid2', 'value' => 'grid2', 'label' => __('Grid 2', 'builder-wc')),
							array('img' => 'grid4', 'value' => 'grid4', 'label' => __('Grid 4', 'builder-wc')),
							array('img' => 'list_thumb_image', 'value' => 'list-thumb-image', 'label' => __('List Thumb Image', 'builder-wc')),
							array('img' => 'grid2_thumb', 'value' => 'grid2-thumb', 'label' => __('Grid 2 Thumb', 'builder-wc'))
						)
					)
				),
				'wrap_with_class' => 'tb_group_element tb_group_element_list',
			),
			array(
				'id' => 'slider',
				'type' => 'group',
				'fields' => array(
					array(
						'id' => 'layout_slider',
						'type' => 'layout',
                                                'mode'=>'sprite',
						'label' => __('Slider Layout', 'builder-wc'),
						'separated' => 'top',
						'options' => array(
							array('img' => 'slider_default', 'value' => 'slider-default', 'label' => __('Slider Default', 'builder-wc')),
							array('img' => 'slider_image_top', 'value' => 'slider-overlay', 'label' => __('Slider Overlay', 'builder-wc')),
							array('img' => 'slider_caption_overlay', 'value' => 'slider-caption-overlay', 'label' => __('Slider Caption Overlay', 'builder-wc')),
							array('img' => 'slider_agency', 'value' => 'slider-agency', 'label' => __('Agency', 'builder-wc'))
						)
					),
					array(
						'id' => 'slider_option_slider',
						'type' => 'slider',
						'label' => __('Slider Options', 'builder-wc'),
						'options' => array(
							array(
								'id' => 'visible_opt_slider',
								'type' => 'select',
								'default' => 1,
								'options' => apply_filters( 'builder_products_visible_opt_slider', array( 1 => 1, 2, 3, 4, 5, 6, 7 ) ),
								'help' => __('Visible', 'builder-wc')
							),
							array(
								'id' => 'mob_visible_opt_slider',
								'type' => 'select',
								'options' => array('', 1, 2, 3, 4),
								'help' => __( 'Mobile Visible', 'builder-wc' )
							),
							array(
								'id' => 'auto_scroll_opt_slider',
								'type' => 'select',
								'default' => 4,
								'options' => apply_filters( 'builder_products_auto_scroll_opt_slider', array(
									'off' => __( 'Off', 'builder-wc' ),
									1 => __( '1 sec', 'builder-wc' ),
									2 => __( '2 sec', 'builder-wc' ),
									3 => __( '3 sec', 'builder-wc' ),
									4 => __( '4 sec', 'builder-wc' ),
									5 => __( '5 sec', 'builder-wc' ),
									6 => __( '6 sec', 'builder-wc' ),
									7 => __( '7 sec', 'builder-wc' ),
									8 => __( '8 sec', 'builder-wc' ),
									9 => __( '9 sec', 'builder-wc' ),
									10 => __( '10 sec', 'builder-wc' )
								) ),
								'help' => __('Auto Scroll', 'builder-wc')
							),
							array(
								'id' => 'scroll_opt_slider',
								'type' => 'select',
								'options' => apply_filters( 'builder_products_scroll_opt_slider', array( 1 => 1, 2, 3, 4, 5, 6, 7 ) ),
								'help' => __('Scroll', 'builder-wc')
							),
							array(
								'id' => 'speed_opt_slider',
								'type' => 'select',
								'options' => array(
									'normal' => __('Normal', 'builder-wc'),
									'fast' => __('Fast', 'builder-wc'),
									'slow' => __('Slow', 'builder-wc')
								),
								'help' => __('Speed', 'builder-wc')
							),
							array(
								'id' => 'effect_slider',
								'type' => 'select',
								'options' => array(
									'scroll' => __('Slide', 'builder-wc'),
									'fade' => __('Fade', 'builder-wc'),
									'crossfade' => __('Cross Fade', 'builder-wc'),
									'cover' => __('Cover', 'builder-wc'),
									'cover-fade' => __('Cover Fade', 'builder-wc'),
									'uncover' => __('Uncover', 'builder-wc'),
									'uncover-fade' => __('Uncover Fade', 'builder-wc'),
									'continuously' => __('Continuously', 'builder-wc')
								),
								'help' => __('Effect', 'builder-wc')
							),
							array(
								'id' => 'pause_on_hover_slider',
								'type' => 'select',
								'options' => array(
									'resume' => __('Resume', 'builder-wc'),
									'immediate' => __('Immediate', 'builder-wc'),
									'false' => __('Disable', 'builder-wc')
								),
								'help' => __('Pause On Hover', 'builder-wc')
							),
							array(
								'id' => 'wrap_slider',
								'type' => 'select',
								'help' => __('Wrap', 'builder-wc'),
								'options' => array(
									'yes' => __('Yes', 'builder-wc'),
									'no' => __('No', 'builder-wc')
								)
							),
							array(
								'id' => 'show_nav_slider',
								'type' => 'select',
								'help' => __('Show slider pagination', 'builder-wc'),
								'options' => array(
									'yes' => __('Yes', 'builder-wc'),
									'no' => __('No', 'builder-wc')
								)
							),
							array(
								'id' => 'show_arrow_slider',
								'type' => 'select',
								'help' => __('Show slider arrow buttons', 'builder-wc'),
								'options' => array(
									'yes' => __('Yes', 'builder-wc'),
									'no' => __('No', 'builder-wc')
								)
							),
							array(
								'id' => 'left_margin_slider',
								'type' => 'text',
								'class' => 'xsmall',
								'unit' => 'px',
								'help' => __('Left margin space between slides', 'builder-wc')
							),
							array(
								'id' => 'right_margin_slider',
								'type' => 'text',
								'class' => 'xsmall',
								'unit' => 'px',
								'help' => __('Right margin space between slides', 'builder-wc')
							),
							array(
								'id' => 'height_slider',
								'type' => 'select',
								'options' => array(
									'variable' => __('Variable', 'themify'),
									'auto' => __('Auto', 'themify')
								),
								'help' => __('Height <small class="description">"Auto" measures the highest slide and all other slides will be set to that size. "Variable" makes every slide has it\'s own height.</small>', 'builder-wc')
							)
						)
					)
				),
				'wrap_with_class' => 'tb_group_element tb_group_element_slider'
			),
			array(
				'id' => 'description_products',
				'type' => 'select',
				'label' => __('Product Description', 'builder-wc'),
				'options' => array(
					'none' => __('None', 'builder-wc'),
					'short' => __('Short Description', 'builder-wc'),
					'full' => __('Full Description', 'builder-wc'),
				)
			),
			array(
				'id' => 'hide_feat_img_products',
				'type' => 'select',
				'label' => __('Hide Product Image', 'builder-wc'),
				'options' => array(
					'no' => __('No', 'builder-wc'),
					'yes' => __('Yes', 'builder-wc'),
				)
			),
			array(
				'id' => 'image_size_products',
				'type' => 'select',
				'label' =>__('Image Size', 'builder-wc'),
				'empty' => array(
					'val' => '',
					'label' => ''
				),
				'hide' => !$is_img_enabled,
				'options' => $image_sizes
			),
			array(
				'id' => 'img_width_products',
				'type' => 'text',
				'label' => __('Image Width', 'builder-wc'),
				'class' => 'xsmall'
			),
			array(
				'id' => 'img_height_products',
				'type' => 'text',
				'label' => __('Image Height', 'builder-wc'),
				'class' => 'xsmall'
			),
			array(
				'id' => 'unlink_feat_img_products',
				'type' => 'select',
				'label' => __('Unlink Product Image', 'builder-wc'),
				'options' => array(
					'no' => __('No', 'builder-wc'),
					'yes' => __('Yes', 'builder-wc'),
				)
			),
			array(
				'id' => 'hide_post_title_products',
				'type' => 'select',
				'label' => __('Hide Products Title', 'builder-wc'),
				'options' => array(
					'no' => __('No', 'builder-wc'),
					'yes' => __('Yes', 'builder-wc'),
				)
			),
			array(
				'id' => 'unlink_post_title_products',
				'type' => 'select',
				'label' => __('Unlink Products Title', 'builder-wc'),
				'options' => array(
					'no' => __('No', 'builder-wc'),
					'yes' => __('Yes', 'builder-wc'),
				)
			),
			array(
				'id' => 'hide_price_products',
				'type' => 'select',
				'label' => __('Hide Price', 'builder-wc'),
				'options' => array(
					'no' => __('No', 'builder-wc'),
					'yes' => __('Yes', 'builder-wc'),
				)
			),
			array(
				'id' => 'hide_add_to_cart_products',
				'type' => 'select',
				'label' => __('Hide Add To Cart Button', 'builder-wc'),
				'options' => array(
					'no' => __('No', 'builder-wc'),
					'yes' => __('Yes', 'builder-wc'),
				)
			),
			array(
				'id' => 'hide_rating_products',
				'type' => 'select',
				'label' => __('Hide Rating', 'builder-wc'),
				'options' => array(
					'no' => __('No', 'builder-wc'),
					'yes' => __('Yes', 'builder-wc'),
				)
			),
			array(
				'id' => 'hide_sales_badge',
				'type' => 'select',
				'label' => __('Hide Sales Badge', 'builder-wc'),
				'options' => array(
					'no' => __('No', 'builder-wc'),
					'yes' => __('Yes', 'builder-wc'),
				)
			),
			array(
				'id' => 'hide_page_nav_products',
				'type' => 'select',
				'label' => __('Hide Product Navigation', 'builder-wc'),
				'options' => array(
					'yes' => __('Yes', 'builder-wc'),
					'no' => __('No', 'builder-wc')
				),
				'default' => 'Yes',
				'wrap_with_class' => 'tb_group_element tb_group_element_list'
			),
			// Additional CSS
			array(
				'type' => 'separator',
				'meta' => array( 'html' => '<hr/>')
			),
			array(
				'id' => 'css_products',
				'type' => 'text',
				'label' => __('Additional CSS Class', 'builder-wc'),
				'class' => 'large exclude-from-reset-field',
				'help' => sprintf( '<br/><small>%s</small>', __('Add additional CSS class(es) for custom styling (<a href="https://themify.me/docs/builder#additional-css-class" target="_blank">learn more</a>).', 'builder-wc') )
			)
		);
	}

	public function get_default_settings() {
		return array(
			'post_per_page_products' => 6,
			'layout_products' => 'grid3'
		);
	}
        
        public function get_visual_type() {
            return 'ajax';            
        }
        
	public function get_styling() {
		$general = array(
			// Background
                        self::get_seperator('image_bacground',__( 'Background', 'themify' ),false),
                        self::get_color('.module-products', 'background_color',__( 'Background Color', 'themify' ),'background-color'),
			// Font
                        self::get_seperator('font',__('Font', 'themify')),
                        self::get_font_family(array( '.module-products', '.module-products .product-title a' )),
						! method_exists( __CLASS__, 'get_element_font_weight' ) ? '' : self::get_element_font_weight( array( '.module-products', '.module-products .product-title a' ) ),
                        self::get_color(array( '.module-products', '.module-products .product-title a' ),'font_color',__('Font Color', 'themify')),
                        self::get_font_size('.module-products'),
                        self::get_line_height('.module-products'),
                        self::get_text_align('.module-products'),
			// Link
                        self::get_seperator('link',__('Link', 'themify')),
                        self::get_color('.module-products a:not(.add_to_cart_button)','link_color'),
                        self::get_color('.module-products a:not(.add_to_cart_button):hover','link_color_hover'),
                        self::get_text_decoration('.module-products a:not(.add_to_cart_button)'),
			// Padding
                        self::get_seperator('padding',__('Padding', 'themify')),
                        self::get_padding('.module-products'),
			// Margin
                        self::get_seperator('margin',__('Margin', 'themify')),
                        self::get_margin('.module-products'),
			// Border
                        self::get_seperator('border',__('Border', 'themify')),
                        self::get_border('.module-products')
		);
		$product_container = array(
			// Background
                        self::get_seperator('image_bacground',__( 'Background', 'themify' ),false),
                        self::get_color('.module-products .product', 'b_c_p_ctr',__( 'Background Color', 'themify' ),'background-color'),
			// Padding
                        self::get_seperator('padding',__('Padding', 'themify')),
                        self::get_padding('.module-products .product','p_p_ctr'),
			// Margin
                        self::get_seperator('margin',__('Margin', 'themify')),
                        self::get_margin('.module-products .product','m_p_ctr'),
			// Border
                        self::get_seperator('border',__('Border', 'themify')),
                        self::get_border('.module-products .product','b_p_ctr')
		);
		$product_content = array(
			// Background
                        self::get_seperator('image_bacground',__( 'Background', 'themify' ),false),
                        self::get_color(array( '.module-products .post-content', '.module-products .product-content'), 'b_c_p_ct',__( 'Background Color', 'themify' ),'background-color'),
			// Font
                        self::get_seperator('font',__('Font', 'themify'),false),
                        self::get_font_family(array( '.module-products .post-content', '.module-products .product-content' ), 'f_f_p_ct'),
						! method_exists( __CLASS__, 'get_element_font_weight' ) ? '' : self::get_element_font_weight(array( '.module-products .post-content', '.module-products .product-content'),'f_w_p_ct'),
                        self::get_color(array( '.module-products .post-content', '.module-products .product-content' ),'f_c_p_ct', __('Font Color', 'themify')),
                        self::get_font_size(array( '.module-products .post-content', '.module-products .product-content'),'f_s_p_ct'),
                        self::get_line_height(array( '.module-products .post-content', '.module-products .product-content'),'l_h_p_ct'),
                        self::get_text_align(array( '.module-products .post-content', '.module-products .product-content'),'t_a_p_ct'),
                        self::get_text_transform(array( '.module-products .post-content', '.module-products .product-content'),'t_t_p_ct'),
                        self::get_font_style(array( '.module-products .post-content', '.module-products .product-content'), 'f_sy_p_ct', 'f_w_p_ct'),
			// Padding
                        self::get_seperator('padding',__('Padding', 'themify')),
                        self::get_padding(array( '.module-products .post-content', '.module-products .product-content'),'p_p_ct'),
			// Margin
                        self::get_seperator('margin',__('Margin', 'themify')),
                        self::get_margin(array( '.module-products .post-content', '.module-products .product-content'),'m_p_ct'),
			// Border
                        self::get_seperator('border',__('Border', 'themify')),
                        self::get_border(array( '.module-products .post-content', '.module-products .product-content'),'b_p_ct')
		);
		$product_title = array(
			// Font
                        self::get_seperator('font',__('Font', 'themify'),false),
                        self::get_font_family(array( '.module-products .product h3', '.module-products .product h3 a' ), 'f_f_p_t'),
						! method_exists( __CLASS__, 'get_element_font_weight' ) ? '' : self::get_element_font_weight(array( '.module-products .product h3', '.module-products .product h3 a'),'f_w_p_t'),
                        self::get_color(array( '.module-products .product h3', '.module-products .product h3 a' ),'f_c_p_t', __('Font Color', 'themify')),
                        self::get_font_size('.module-products .product h3','f_s_p_t'),
                        self::get_line_height('.module-products .product h3','l_h_p_t'),
                        self::get_text_align('.module-products .product h3','t_a_p_t'),
                        self::get_text_transform('.module-products .product h3','t_t_p_t'),
                        self::get_font_style('.module-products .product h3', 'f_sy_p_t', 'f_w_p_t'),
			// Padding
                        self::get_seperator('padding',__('Padding', 'themify')),
                        self::get_padding('.module-products .product h3','p_p_t'),
			// Margin
                        self::get_seperator('margin',__('Margin', 'themify')),
                        self::get_margin('.module-products .product h3','m_p_t'),
			// Border
                        self::get_seperator('border',__('Border', 'themify')),
                        self::get_border('.module-products .product h3','b_p_t')
		);
		$price = array(
			// Font
                        self::get_seperator('font',__('Font', 'themify'),false),
                        self::get_font_family('.module-products .product .price', 'f_f_p_p'),
						! method_exists( __CLASS__, 'get_element_font_weight' ) ? '' : self::get_element_font_weight('.module-products .product .price', 'f_w_p_p'),
                        self::get_color('.module-products .product .price','f_c_p_p', __('Font Color', 'themify')),
                        self::get_font_size('.module-products .product .price','f_s_p_p'),
                        self::get_line_height('.module-products .product .price','l_h_p_p'),
                        self::get_text_align('.module-products .product .price','t_a_p_p'),
                        self::get_font_style('.module-products .product .price', 'f_sy_p_p', 'f_w_p_p'),
			// Padding
                        self::get_seperator('padding',__('Padding', 'themify')),
                        self::get_padding('.module-products .product .price','p_p_p'),
			// Margin
                        self::get_seperator('margin',__('Margin', 'themify')),
                        self::get_margin('.module-products .product .price','m_p_p'),
			// Border
                        self::get_seperator('border',__('Border', 'themify')),
                        self::get_border('.module-products .product .price','b_p_p')
		);
		$button = array(
			// Background
                        self::get_seperator('image_bacground',__( 'Background', 'themify' ),false),
                        self::get_color('.module-products .product .add_to_cart_button', 'b_c_p_b',__( 'Background Color', 'themify' ),'background-color'),
                        self::get_color('.module-products .product .add_to_cart_button:hover', 'b_c_h_p_b',__( 'Background Color', 'themify' ),'background-color'),
			// Font
                        self::get_seperator('font',__('Font', 'themify'),false),
                        self::get_font_family('.module-products .product .add_to_cart_button', 'f_f_p_b'),
						! method_exists( __CLASS__, 'get_element_font_weight' ) ? '' : self::get_element_font_weight('.module-products .product .add_to_cart_button', 'f_w_p_b'),
                        self::get_color('.module-products .product .add_to_cart_button','f_c_p_b', __('Font Color', 'themify')),
                        self::get_color('.module-products .product .add_to_cart_button:hover','f_c_h_p_b', __('Font Color', 'themify')),
                        self::get_font_size('.module-products .product .add_to_cart_button','f_s_p_b'),
                        self::get_line_height('.module-products .product .add_to_cart_button','l_h_p_b'),
                        self::get_text_align('.module-products .product .add_to_cart_button','t_a_p_b'),
			// Padding
                        self::get_seperator('padding',__('Padding', 'themify')),
                        self::get_padding('.module-products .product .add_to_cart_button','p_p_b'),
			// Margin
                        self::get_seperator('margin',__('Margin', 'themify')),
                        self::get_margin('.module-products .product .add_to_cart_button','m_p_b'),
			// Border
                        self::get_seperator('border',__('Border', 'themify')),
                        self::get_border('.module-products .product .add_to_cart_button','b_p_b')
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
						'label' => __('Product Container', 'themify'),
						'fields' => $product_container
					),
					'content' => array(
						'label' => __('Product Content', 'themify'),
						'fields' => $product_content
					),
					'title' => array(
						'label' => __('Product Title', 'themify'),
						'fields' => $product_title
					),
					'price' => array(
						'label' => __('Price', 'themify'),
						'fields' => $price
					),
					'button' => array(
						'label' => __('Button', 'themify'),
						'fields' => $button
					)
				)
			)
			
		);
	}
}

Themify_Builder_Model::register_module( 'TB_Products_Module' );

function builder_woocommerce_return_no() {
	return 'no';
}

function builder_woocommerce_return_yes() {
	return 'yes';
}