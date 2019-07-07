<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Module Name: Portfolio
 * Description: Display portfolio custom post type
 */
class TB_Portfolio_Module extends Themify_Builder_Component_Module {

	function __construct() {
		parent::__construct(array(
			'name' => __('Portfolio', 'themify'),
			'slug' => 'portfolio'
		));

		///////////////////////////////////////
		// Load Post Type
		///////////////////////////////////////
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		if( ! Themify_Builder_Model::is_plugin_active( 'themify-portfolio-post/themify-portfolio-post.php' ) ) {
			$this->meta_box = $this->set_metabox();
			$this->initialize_cpt( array(
				'plural' => __('Portfolios', 'themify'),
				'singular' => __('Portfolio', 'themify'),
				'rewrite' => apply_filters('themify_portfolio_rewrite', 'project'),
				'menu_icon' => 'dashicons-portfolio'
			));

			if ( ! shortcode_exists( 'themify_portfolio_posts' ) ) {
				add_shortcode( 'themify_portfolio_posts', array( $this, 'do_shortcode' ) );
			}
		}
	}

	public function get_title( $module ) {
		$type = isset( $module['mod_settings']['type_query_portfolio'] ) ? $module['mod_settings']['type_query_portfolio'] : 'category';
		$category = isset( $module['mod_settings']['category_portfolio'] ) ? $module['mod_settings']['category_portfolio'] : '';
		$slug_query = isset( $module['mod_settings']['query_slug_portfolio'] ) ? $module['mod_settings']['query_slug_portfolio'] : '';

		if ( 'category' === $type ) {
			return sprintf( '%s : %s', __('Category', 'themify'), $category );
		} else {
			return sprintf( '%s : %s', __('Slugs', 'themify'), $slug_query );
		}
	}

	public function get_options() {
		
                $is_img_enabled = Themify_Builder_Model::is_img_php_disabled();
		$image_sizes = !$is_img_enabled?themify_get_image_sizes_list( false ):array();
		return array(
			array(
				'id' => 'mod_title_portfolio',
				'type' => 'text',
				'label' => __('Module Title', 'themify'),
				'class' => 'large',
                                'render_callback' => array(
                                    'live-selector'=>'.module-title'
                                )
			),
			array(
				'id' => 'layout_portfolio',
				'type' => 'layout',
				'label' => __('Portfolio Layout', 'themify'),
                                'mode'=>'sprite',
				'options' => array(
					array('img' => 'grid4', 'value' => 'grid4', 'label' => __('Grid 4', 'themify')),
					array('img' => 'grid3', 'value' => 'grid3', 'label' => __('Grid 3', 'themify')),
					array('img' => 'grid2', 'value' => 'grid2', 'label' => __('Grid 2', 'themify')),
					array('img' => 'fullwidth', 'value' => 'fullwidth', 'label' => __('fullwidth', 'themify'))
				)
			),
			array(
				'id' => 'type_query_portfolio',
				'type' => 'radio',
				'label' => __('Query by', 'themify'),
				'options' => array(
					'category' => __('Category', 'themify'),
					'post_slug' => __('Slug', 'themify')
				),
				'default' => 'category',
				'option_js' => true,
			),
			array(
				'id' => 'category_portfolio',
				'type' => 'query_category',
				'label' => __('Category', 'themify'),
				'options' => array(
					'taxonomy' => 'portfolio-category'
				),
				'help' => sprintf(__('Add more <a href="%s" target="_blank">portfolio posts</a>', 'themify'), admin_url('post-new.php?post_type=portfolio')),
				'wrap_with_class' => 'tb_group_element tb_group_element_category'
			),
			array(
				'id' => 'query_slug_portfolio',
				'type' => 'text',
				'label' => __('Portfolio Slugs', 'themify'),
				'class' => 'large',
				'wrap_with_class' => 'tb_group_element tb_group_element_post_slug',
				'help' => '<br/>' . __( 'Insert Portfolio slug. Multiple slug should be separated by comma (,)', 'themify')
			),
			array(
				'id' => 'post_per_page_portfolio',
				'type' => 'text',
				'label' => __('Limit', 'themify'),
				'class' => 'xsmall',
				'help' => __('number of posts to show', 'themify')
			),
			array(
				'id' => 'offset_portfolio',
				'type' => 'text',
				'label' => __('Offset', 'themify'),
				'class' => 'xsmall',
				'help' => __('number of post to displace or pass over', 'themify')
			),
			array(
				'id' => 'order_portfolio',
				'type' => 'select',
				'label' => __('Order', 'themify'),
				'help' => __('Descending = show newer posts first', 'themify'),
				'options' => array(
					'desc' => __('Descending', 'themify'),
					'asc' => __('Ascending', 'themify')
				)
			),
			array(
				'id' => 'orderby_portfolio',
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
					'meta_value' => array( 'show' => array( 'meta_key_portfolio' ) ),
					'meta_value_num' => array( 'show' => array( 'meta_key_portfolio' ) ),
					'date' => array( 'hide' => array( 'meta_key_portfolio' ) ),
					'id' => array( 'hide' => array( 'meta_key_portfolio' ) ),
					'author' => array( 'hide' => array( 'meta_key_portfolio' ) ),
					'title' => array( 'hide' => array( 'meta_key_portfolio' ) ),
					'name' => array( 'hide' => array( 'meta_key_portfolio' ) ),
					'rand' => array( 'hide' => array( 'meta_key_portfolio' ) ),
					'comment_count' => array( 'hide' => array( 'meta_key_portfolio' ) )
				)
			),
			array(
				'id' => 'meta_key_portfolio',
				'type' => 'text',
				'label' => __( 'Custom Field Key', 'themify' ),
			),
			array(
				'id' => 'display_portfolio',
				'type' => 'select',
				'label' => __('Display', 'themify'),
				'options' => array(
					'content' => __('Content', 'themify'),
					'excerpt' => __('Excerpt', 'themify'),
					'none' => __('None', 'themify')
				)
			),
			array(
				'id' => 'hide_feat_img_portfolio',
				'type' => 'select',
				'label' => __('Hide Featured Image', 'themify'),
				'options' => array(
                                        ''=>'',
					'yes' => __('Yes', 'themify'),
					'no' => __('No', 'themify')
				)
			),
			array(
				'id' => 'image_size_portfolio',
				'type' => 'select',
				'label' =>  __('Image Size', 'themify'),
				'hide' => !$is_img_enabled,
				'options' => $image_sizes
			),
			array(
				'id' => 'img_width_portfolio',
				'type' => 'text',
				'label' => __('Image Width', 'themify'),
				'class' => 'xsmall'
			),
			array(
				'id' => 'img_height_portfolio',
				'type' => 'text',
				'label' => __('Image Height', 'themify'),
				'class' => 'xsmall'
			),
			array(
				'id' => 'unlink_feat_img_portfolio',
				'type' => 'select',
				'label' => __('Unlink Featured Image', 'themify'),
				'options' => array(
                                        ''=>'',
					'yes' => __('Yes', 'themify'),
					'no' => __('No', 'themify')
				)
			),
			array(
				'id' => 'hide_post_title_portfolio',
				'type' => 'select',
				'label' => __('Hide Post Title', 'themify'),
				'options' => array(
                                        ''=>'',
					'yes' => __('Yes', 'themify'),
					'no' => __('No', 'themify')
				)
			),
			array(
				'id' => 'unlink_post_title_portfolio',
				'type' => 'select',
				'label' => __('Unlink Post Title', 'themify'),
				'options' => array(
                                        ''=>'',
					'yes' => __('Yes', 'themify'),
					'no' => __('No', 'themify')
				)
			),
			array(
				'id' => 'hide_post_date_portfolio',
				'type' => 'select',
				'label' => __('Hide Post Date', 'themify'),
				'options' => array(
                                        ''=>'',
					'yes' => __('Yes', 'themify'),
					'no' => __('No', 'themify')
				)
			),
			array(
				'id' => 'hide_post_meta_portfolio',
				'type' => 'select',
				'label' => __('Hide Post Meta', 'themify'),
				'options' => array(
                                        ''=>'',
					'yes' => __('Yes', 'themify'),
					'no' => __('No', 'themify')
				)
			),
			array(
				'id' => 'hide_page_nav_portfolio',
				'type' => 'select',
				'label' => __('Hide Page Navigation', 'themify'),
				'options' => array(
                                        ''=>'',
					'yes' => __('Yes', 'themify'),
					'no' => __('No', 'themify')
				)
			),
			// Additional CSS
			array(
				'type' => 'separator',
				'meta' => array( 'html' => '<hr/>')
			),
			array(
				'id' => 'css_portfolio',
				'type' => 'text',
				'label' => __('Additional CSS Class', 'themify'),
				'class' => 'large exclude-from-reset-field',
				'help' => sprintf( '<br/><small>%s</small>', __('Add additional CSS class(es) for custom styling (<a href="https://themify.me/docs/builder#additional-css-class" target="_blank">learn more</a>).', 'themify') )
			)
		);
	}

	public function get_default_settings() {
		return array(
			'post_per_page_portfolio' => 4,
			'display_portfolio' => 'excerpt'
		);
	}
        
        public function get_visual_type() {
            return 'ajax';            
        }


	public function get_styling() {
		$general = array(
			// Background
                        self::get_seperator('image_bacground',__( 'Background', 'themify' ),false),
                        self::get_color('.module-portfolio .post', 'background_color',__( 'Background Color', 'themify' ),'background-color'),
			// Font
                        self::get_seperator('font',__('Font', 'themify')),
                        self::get_font_family(array( '.module-portfolio .post-title', '.module-portfolio .post-title a' )),
                        self::get_element_font_weight(array( '.module-portfolio .post-title', '.module-portfolio .post-title a' )),
                        self::get_color(array( '.module-portfolio .post', '.module-portfolio h1', '.module-portfolio h2', '.module-portfolio h3:not(.module-title)', '.module-portfolio h4', '.module-portfolio h5', '.module-portfolio h6', '.module-portfolio .post-title', '.module-portfolio .post-title a' ),'font_color',__('Font Color', 'themify')),
                        self::get_font_size('.module-portfolio .post'),
                        self::get_line_height('.module-portfolio .post'),
                        self::get_letter_spacing('.module-portfolio .post'),
                        self::get_text_align('.module-portfolio .post'),
                        self::get_text_transform('.module-portfolio .post'),
                        self::get_font_style('.module-portfolio .post'),
                        self::get_text_decoration('.module-portfolio .post','text_decoration_regular'),
			// Link
                        self::get_seperator('link',__('Link', 'themify')),
                        self::get_color( '.module-portfolio a','link_color'),
                        self::get_color('.module-portfolio a:hover','link_color_hover',__('Color Hover', 'themify')),
                        self::get_text_decoration('.module-portfolio a'),
			// Padding
                        self::get_seperator('padding',__('Padding', 'themify')),
                        self::get_padding('.module-portfolio .post'),
			// Margin
                        self::get_seperator('margin',__('Margin', 'themify')),
                        self::get_margin('.module-portfolio .post'),
                        // Border
                        self::get_seperator('border',__('Border', 'themify')),
                        self::get_border('.module-portfolio .post')
		);

		$portfolio_title = array(
			// Font
                        self::get_seperator('font',__('Font', 'themify'),false),
                        self::get_font_family(array( '.module-portfolio .post-title', '.module-portfolio .post-title a' ),'font_family_title'),
                        self::get_element_font_weight(array( '.module-portfolio .post-title', '.module-portfolio .post-title a' ),'font_weight_title'),
                        self::get_color(array( '.module-portfolio .post-title', '.module-portfolio .post-title a' ),'font_color_title',__('Font Color', 'themify')),
                        self::get_color(array( '.module-portfolio .post-title:hover', '.module-portfolio .post-title a:hover' ),'font_color_title_hover',__('Color Hover', 'themify')),
                        self::get_font_size('.module-portfolio .post-title','font_size_title'),
                        self::get_line_height('.module-portfolio .post-title','line_height_title'),
						self::get_letter_spacing('.module-portfolio .post-title', 'letter_spacing_title'),
						self::get_text_transform('.module-portfolio .post-title', 't_t_title'),
						self::get_font_style('.module-portfolio .post-title', 'f_sy_t','f_b_t')
		);

		$portfolio_meta = array(
			// Font
                        self::get_seperator('font',__('Font', 'themify'),false),
                        self::get_font_family(array( '.module-portfolio .post-content .post-meta', '.module-portfolio .post-content .post-meta a' ),'font_family_meta'),
                        self::get_element_font_weight(array( '.module-portfolio .post-content .post-meta', '.module-portfolio .post-content .post-meta a' ),'font_weight_meta'),
                        self::get_color(array( '.module-portfolio .post-content .post-meta', '.module-portfolio .post-content .post-meta a' ),'font_color_meta',__('Font Color', 'themify')),
                        self::get_font_size('.module-portfolio .post-content .post-meta','font_size_meta'),
                        self::get_line_height('.module-portfolio .post-content .post-meta','line_height_meta')
		);

		$portfolio_date = array(
			// Font
                        self::get_seperator('font',__('Font', 'themify'),false),
                        self::get_font_family(array('.module-portfolio .post .post-date', '.module-portfolio .post .post-date a'),'font_family_date'),
                        self::get_element_font_weight(array('.module-portfolio .post .post-date', '.module-portfolio .post .post-date a'),'font_weight_date'),
                        self::get_color(array('.module-portfolio .post .post-date', '.module-portfolio .post .post-date a'),'font_color_date',__('Font Color', 'themify')),
                        self::get_color(array('.module-portfolio .post .post-date:hover', '.module-portfolio .post .post-date a:hover'),'font_color_date_hover',__('Color Hover', 'themify')),
                        self::get_font_size('.module-portfolio .post .post-date','font_size_date'),
                        self::get_line_height('.module-portfolio .post .post-date','line_height_date')
		);

		$portfolio_content = array(
			// Font
                        self::get_seperator('font',__('Font', 'themify'),false),
                        self::get_font_family('.module-portfolio .post-content .entry-content','font_family_content'),
                        self::get_element_font_weight('.module-portfolio .post-content .entry-content','font_weight_content'),
                        self::get_color('.module-portfolio .post-content .entry-content','font_color_content',__('Font Color', 'themify')),
                        self::get_font_size('.module-portfolio .post-content .entry-content','font_size_content'),
                        self::get_line_height('.module-portfolio .post-content .entry-content','line_height_content')
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
						'label' => __('Portfolio Title', 'themify'),
						'fields' => $portfolio_title
					),
					'meta' => array(
						'label' => __('Portfolio Meta', 'themify'),
						'fields' => $portfolio_meta
					),
					'date' => array(
						'label' => __('Portfolio Date', 'themify'),
						'fields' => $portfolio_date
					),
					'content' => array(
						'label' => __('Portfolio Content', 'themify'),
						'fields' => $portfolio_content
					)
				)
			),
		);

	}

	function set_metabox() {
		/** Portfolio Meta Box Options */
		$meta_box = array(
			// Feature Image
			Themify_Builder_Model::$post_image,
			// Featured Image Size
			Themify_Builder_Model::$featured_image_size,
			// Image Width
			Themify_Builder_Model::$image_width,
			// Image Height
			Themify_Builder_Model::$image_height,
			// Hide Title
			array(
				'name' 		=> 'hide_post_title',
				'title'		=> __('Hide Post Title', 'themify'),
				'description'	=> '',
				'type' 		=> 'dropdown',
				'meta'		=> array(
					array('value' => 'default', 'name' => '', 'selected' => true),
					array('value' => 'yes', 'name' => __('Yes', 'themify')),
					array('value' => 'no',	'name' => __('No', 'themify'))
				)
			),
			// Unlink Post Title
			array(
				'name' 		=> 'unlink_post_title',
				'title' 		=> __('Unlink Post Title', 'themify'),
				'description' => __('Unlink post title (it will display the post title without link)', 'themify'),
				'type' 		=> 'dropdown',
				'meta'		=> array(
					array('value' => 'default', 'name' => '', 'selected' => true),
					array('value' => 'yes', 'name' => __('Yes', 'themify')),
					array('value' => 'no',	'name' => __('No', 'themify'))
				)
			),
			// Hide Post Date
			array(
				'name' 		=> 'hide_post_date',
				'title'		=> __('Hide Post Date', 'themify'),
				'description'	=> '',
				'type' 		=> 'dropdown',
				'meta'		=> array(
					array('value' => 'default', 'name' => '', 'selected' => true),
					array('value' => 'yes', 'name' => __('Yes', 'themify')),
					array('value' => 'no',	'name' => __('No', 'themify'))
				)
			),
			// Hide Post Meta
			array(
				'name' 		=> 'hide_post_meta',
				'title'		=> __('Hide Post Meta', 'themify'),
				'description'	=> '',
				'type' 		=> 'dropdown',
				'meta'		=> array(
					array('value' => 'default', 'name' => '', 'selected' => true),
					array('value' => 'yes', 'name' => __('Yes', 'themify')),
					array('value' => 'no',	'name' => __('No', 'themify'))
				)
			),
			// Hide Post Image
			array(
				'name' 		=> 'hide_post_image',
				'title' 		=> __('Hide Featured Image', 'themify'),
				'description' => '',
				'type' 		=> 'dropdown',
				'meta'		=> array(
					array('value' => 'default', 'name' => '', 'selected' => true),
					array('value' => 'yes', 'name' => __('Yes', 'themify')),
					array('value' => 'no',	'name' => __('No', 'themify'))
				)
			),
			// Unlink Post Image
			array(
				'name' 		=> 'unlink_post_image',
				'title' 		=> __('Unlink Featured Image', 'themify'),
				'description' => __('Display the Featured Image without link', 'themify'),
				'type' 		=> 'dropdown',
				'meta'		=> array(
					array('value' => 'default', 'name' => '', 'selected' => true),
					array('value' => 'yes', 'name' => __('Yes', 'themify')),
					array('value' => 'no',	'name' => __('No', 'themify'))
				)
			),
			// External Link
			Themify_Builder_Model::$external_link,
			// Lightbox Link
			Themify_Builder_Model::$lightbox_link
		);
		return $meta_box;
	}

	function do_shortcode( $atts ) {

		extract( shortcode_atts( array(
			'id' => '',
			'title' => 'yes',
			'unlink_title' => 'no',
			'image' => 'yes', // no
			'image_w' => '',
			'image_h' => '',
			'display' => 'none', // excerpt, content
			'post_meta' => 'yes', // yes
			'post_date' => 'yes', // yes
			'more_link' => false, // true goes to post type archive, and admits custom link
			'more_text' => __('More &rarr;', 'themify'),
			'limit' => 4,
			'category' => 0, // integer category ID
			'order' => 'DESC', // ASC
			'orderby' => 'date', // title, rand
			'style' => '', // grid3, grid2
			'sorting' => 'no', // yes
			'page_nav' => 'no', // yes
			'paged' => '0', // internal use for pagination, dev: previously was 1
			// slider parameters
			'autoplay' => '',
			'effect' => '',
			'timeout' => '',
			'speed' => ''
		), $atts ) );

		$sync = array(
			'mod_title_portfolio' => '',
			'layout_portfolio' => $style,
			'category_portfolio' => $category,
			'post_per_page_portfolio' => $limit,
			'offset_portfolio' => '',
			'order_portfolio' => $order,
			'orderby_portfolio' => $orderby,
			'display_portfolio' => $display,
			'hide_feat_img_portfolio' => $image === 'yes' ? 'no' : 'yes',
			'image_size_portfolio' => '',
			'img_width_portfolio' => $image_w,
			'img_height_portfolio' => $image_h,
			'unlink_feat_img_portfolio' => 'no',
			'hide_post_title_portfolio' => $title === 'yes' ? 'no' : 'yes',
			'unlink_post_title_portfolio' => $unlink_title,
			'hide_post_date_portfolio' => $post_date === 'yes' ? 'no' : 'yes',
			'hide_post_meta_portfolio' => $post_meta === 'yes' ? 'no' : 'yes',
			'hide_page_nav_portfolio' => $page_nav === 'no' ? 'yes' : 'no',
			'animation_effect' => '',
			'css_portfolio' => ''
		);
		$module = array(
			'module_ID' => $this->slug . '-' . rand(0,10000),
			'mod_name' => $this->slug,
			'mod_settings' => $sync
		);

		return self::retrieve_template( 'template-' . $this->slug . '.php', $module, '', '', false );
	}

	/**
	 * Render plain content for static content.
	 * 
	 * @param array $module 
	 * @return string
	 */
	public function get_plain_content( $module ) {
		return ''; // no static content for dynamic content
	}
}

///////////////////////////////////////
// Module Options
///////////////////////////////////////

if( Themify_Builder_Model::is_cpt_active( 'portfolio' ) ) {
    Themify_Builder_Model::register_module( 'TB_Portfolio_Module' );
}
