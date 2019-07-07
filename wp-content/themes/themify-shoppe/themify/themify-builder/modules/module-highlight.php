<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Module Name: Highlight
 * Description: Display highlight custom post type
 */
class TB_Highlight_Module extends Themify_Builder_Component_Module {
	function __construct() {
		parent::__construct(array(
			'name' => __('Highlight', 'themify'),
			'slug' => 'highlight'
		));

		///////////////////////////////////////
		// Load Post Type
		///////////////////////////////////////
		$this->meta_box = $this->set_metabox();
		$this->initialize_cpt( array(
			'plural' => __('Highlights', 'themify'),
			'singular' => __('Highlight', 'themify'),
			'menu_icon' => 'dashicons-welcome-write-blog'
		));

		if ( ! shortcode_exists( 'themify_highlight_posts' ) ) {
			add_shortcode( 'themify_highlight_posts', array( $this, 'do_shortcode' ) );
		}
	}

	public function get_title( $module ) {
		$type = isset( $module['mod_settings']['type_query_highlight'] ) ? $module['mod_settings']['type_query_highlight'] : 'category';
		$category = isset( $module['mod_settings']['category_highlight'] ) ? $module['mod_settings']['category_highlight'] : '';
		$slug_query = isset( $module['mod_settings']['query_slug_highlight'] ) ? $module['mod_settings']['query_slug_highlight'] : '';

		return 'category' === $type?sprintf( '%s : %s', __('Category', 'themify'), $category ):sprintf( '%s : %s', __('Slugs', 'themify'), $slug_query );
	}

	public function get_options() {
                $is_img_enabled = Themify_Builder_Model::is_img_php_disabled();
		$image_sizes = !$is_img_enabled?themify_get_image_sizes_list( false ):array();
		return array(
			array(
				'id' => 'mod_title_highlight',
				'type' => 'text',
				'label' => __('Module Title', 'themify'),
				'class' => 'large',
                                'render_callback' => array(
                                        'live-selector'=>'.module-title'
				)
			),
			array(
				'id' => 'layout_highlight',
				'type' => 'layout',
				'label' => __('Highlight Layout', 'themify'),
                                'mode'=>'sprite',
				'options' => array(
					array('img' => 'grid4', 'value' => 'grid4', 'label' => __('Grid 4', 'themify')),
					array('img' => 'grid3', 'value' => 'grid3', 'label' => __('Grid 3', 'themify')),
					array('img' => 'grid2', 'value' => 'grid2', 'label' => __('Grid 2', 'themify')),
					array('img' => 'fullwidth', 'value' => 'fullwidth', 'label' => __('fullwidth', 'themify'))
				)
			),
			array(
				'id' => 'type_query_highlight',
				'type' => 'radio',
				'label' => __('Query by', 'themify'),
				'options' => array(
					'category' => __('Category', 'themify'),
					'post_slug' => __('Slug', 'themify')
				),
				'default' => 'category',
				'option_js' => true
			),
			array(
				'id' => 'category_highlight',
				'type' => 'query_category',
				'label' => __('Category', 'themify'),
				'options' => array(
					'taxonomy' => 'highlight-category'
				),
				'help' => sprintf(__('Add more <a href="%s" target="_blank">highlight posts</a>', 'themify'), admin_url('post-new.php?post_type=highlight')),
				'wrap_with_class' => 'tb_group_element tb_group_element_category'
			),
			array(
				'id' => 'query_slug_highlight',
				'type' => 'text',
				'label' => __('Highlight Slugs', 'themify'),
				'class' => 'large',
				'wrap_with_class' => 'tb_group_element tb_group_element_post_slug',
				'help' => '<br/>' . __( 'Insert Highlight slug. Multiple slug should be separated by comma (,)', 'themify')
			),
			array(
				'id' => 'post_per_page_highlight',
				'type' => 'text',
				'label' => __('Limit', 'themify'),
				'class' => 'xsmall',
				'help' => __('number of posts to show', 'themify')
			),
			array(
				'id' => 'offset_highlight',
				'type' => 'text',
				'label' => __('Offset', 'themify'),
				'class' => 'xsmall',
				'help' => __('number of post to displace or pass over', 'themify')
			),
			array(
				'id' => 'order_highlight',
				'type' => 'select',
				'label' => __('Order', 'themify'),
				'help' => __('Descending = show newer posts first', 'themify'),
				'options' => array(
					'desc' => __('Descending', 'themify'),
					'asc' => __('Ascending', 'themify')
				)
			),
			array(
				'id' => 'orderby_highlight',
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
					'meta_value' => array( 'show' => array( 'meta_key_highlight' ) ),
					'meta_value_num' => array( 'show' => array( 'meta_key_highlight' ) ),
					'date' => array( 'hide' => array( 'meta_key_highlight' ) ),
					'id' => array( 'hide' => array( 'meta_key_highlight' ) ),
					'author' => array( 'hide' => array( 'meta_key_highlight' ) ),
					'title' => array( 'hide' => array( 'meta_key_highlight' ) ),
					'name' => array( 'hide' => array( 'meta_key_highlight' ) ),
					'rand' => array( 'hide' => array( 'meta_key_highlight' ) ),
					'comment_count' => array( 'hide' => array( 'meta_key_highlight' ) )
				)
			),
			array(
				'id' => 'meta_key_highlight',
				'type' => 'text',
				'label' => __( 'Custom Field Key', 'themify' ),
			),
			array(
				'id' => 'display_highlight',
				'type' => 'select',
				'label' => __('Display', 'themify'),
				'options' => array(
					'content' => __('Content', 'themify'),
					'excerpt' => __('Excerpt', 'themify'),
					'none' => __('None', 'themify')
				)
			),
			array(
				'id' => 'hide_feat_img_highlight',
				'type' => 'select',
				'label' => __('Hide Featured Image', 'themify'),
				'options' => array(
                                        ''=>'',
					'yes' => __('Yes', 'themify'),
					'no' => __('No', 'themify')
				)
			),
			array(
				'id' => 'image_size_highlight',
				'type' => 'select',
				'label' =>  __('Image Size', 'themify'),
				'hide' => !$is_img_enabled,
				'options' => $image_sizes
			),
			array(
				'id' => 'img_width_highlight',
				'type' => 'text',
				'label' => __('Image Width', 'themify'),
				'class' => 'xsmall'
			),
			array(
				'id' => 'img_height_highlight',
				'type' => 'text',
				'label' => __('Image Height', 'themify'),
				'class' => 'xsmall'
			),
			array(
				'id' => 'hide_post_title_highlight',
				'type' => 'select',
				'label' => __('Hide Post Title', 'themify'),
				'options' => array(
                                        ''=>'',
					'yes' => __('Yes', 'themify'),
					'no' => __('No', 'themify')
				)
			),
			array(
				'id' => 'hide_page_nav_highlight',
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
				'id' => 'css_highlight',
				'type' => 'text',
				'label' => __('Additional CSS Class', 'themify'),
				'class' => 'large exclude-from-reset-field',
				'help' => sprintf( '<br/><small>%s</small>', __('Add additional CSS class(es) for custom styling (<a href="https://themify.me/docs/builder#additional-css-class" target="_blank">learn more</a>).', 'themify') )
			)
		);
	}


	public function get_styling() {
		$general = array(
			// Background
                        self::get_seperator('image_bacground',__( 'Background', 'themify' ),false),
                        self::get_color('.module-highlight .post', 'background_color',__( 'Background Color', 'themify' ),'background-color'),
			// Font
                        self::get_seperator('font',__('Font', 'themify')),
                        self::get_font_family( array( '.module-highlight .post-title', '.module-highlight .post-title a' )),
                        self::get_element_font_weight( array( '.module-highlight .post-title', '.module-highlight .post-title a' )),
                        self::get_color( array( '.module-highlight .post', '.module-highlight h1', '.module-highlight h2', '.module-highlight h3:not(.module-title)', '.module-highlight h4', '.module-highlight h5', '.module-highlight h6', '.module-highlight .post-title', '.module-highlight .post-title a' ),'font_color',__('Font Color', 'themify')),
                        self::get_font_size('.module-highlight .post'),
                        self::get_line_height('.module-highlight .post'),
                        self::get_letter_spacing('.module-highlight .post'),
                        self::get_text_align('.module-highlight .post'),
                        self::get_text_transform('.module-highlight .post'),
                        self::get_font_style('.module-highlight .post'),
                        self::get_text_decoration('.module-highlight .post','text_decoration_regular'),
			// Link
                        self::get_seperator('link',__('Link', 'themify')),
                        self::get_color( '.module-highlight a','link_color'),
                        self::get_color('.module-highlight a:hover','link_color_hover',__('Color Hover', 'themify')),
                        self::get_text_decoration('.module-highlight a'),
                        self::get_seperator('padding',__('Padding', 'themify')),
                        self::get_padding('.module-highlight .post'),
			// Margin
                        self::get_seperator('margin',__('Margin', 'themify')),
                        self::get_margin('.module-highlight .post'),
                        // Border
                        self::get_seperator('border',__('Border', 'themify')),
                        self::get_border('.module-highlight .post')
		);

		$highlight_title = array(
			// Font
                        self::get_seperator('font',__('Font', 'themify'),false),
                        self::get_font_family( array( '.module-highlight .post-title', '.module-highlight .post-title a' ),'font_family_title'),
                        self::get_element_font_weight( array( '.module-highlight .post-title', '.module-highlight .post-title a' ),'font_weight_title'),
                        self::get_color(array( '.module-highlight .post-title', '.module-highlight .post-title a' ),'font_color_title',__('Font Color', 'themify')),
                        self::get_color(array( '.module-highlight .post-title:hover', '.module-highlight .post-title a:hover' ),'font_color_title_hover',__('Color Hover', 'themify')),
                        self::get_font_size('.module-highlight .post-title','font_size_title'),
                        self::get_line_height('.module-highlight .post-title','line_height_title'),
						self::get_letter_spacing('.module-highlight .post-title', 'letter_spacing_title')
		);

		$highlight_content = array(
			// Font
                        self::get_seperator('font',__('Font', 'themify'),false),
                        self::get_font_family('.module-highlight .highlight-post .post-content','font_family_content'),
                        self::get_element_font_weight('.module-highlight .highlight-post .post-content','font_weight_content'),
                        self::get_color('.module-highlight .highlight-post .post-content','font_color_content',__('Font Color', 'themify')),
                        self::get_font_size('.module-highlight .highlight-post .post-content','font_size_content'),
                        self::get_line_height('.module-highlight .highlight-post .post-content','line_height_content')
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
						'label' => __('Highlight Title', 'themify'),
						'fields' => $highlight_title
					),
					'content' => array(
						'label' => __('Highlight Content', 'themify'),
						'fields' => $highlight_content
					)
				)
			)
		);

	}
        
        public function get_visual_type() {
            return 'ajax';            
        }
        
	function set_metabox() {
		// Highlight Meta Box Options
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
			Themify_Builder_Model::$lightbox_link
		);
		return $meta_box;
	}

	function do_shortcode( $atts ) {
		extract( shortcode_atts( array(
			'id' => '',
			'title' => 'yes', // no
			'image' => 'yes', // no
			'image_w' => 68,
			'image_h' => 68,
			'display' => 'content', // excerpt, none
			'more_link' => false, // true goes to post type archive, and admits custom link
			'more_text' => __('More &rarr;', 'themify'),
			'limit' => 6,
			'category' => 0, // integer category ID
			'order' => 'DESC', // ASC
			'orderby' => 'date', // title, rand
			'style' => 'grid3', // grid4, grid2, list-post
			'section_link' => false // true goes to post type archive, and admits custom link
		), $atts ) );

		$sync = array(
			'mod_title_highlight' => '',
			'layout_highlight' => $style,
			'category_highlight' => $category,
			'post_per_page_highlight' => $limit,
			'offset_highlight' => '',
			'order_highlight' => $order,
			'orderby_highlight' => $orderby,
			'display_highlight' => $display,
			'hide_feat_img_highlight' => $image === 'yes' ? 'no' : 'yes',
			'image_size_highlight' => '',
			'img_width_highlight' => $image_w,
			'img_height_highlight' => $image_h,
			'hide_post_title_highlight' => $title === 'yes' ? 'no' : 'yes',
			'hide_post_date_highlight' => '',
			'hide_post_meta_highlight' => '',
			'hide_page_nav_highlight' => 'yes',
			'animation_effect' => '',
			'css_highlight' => ''
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
if( Themify_Builder_Model::is_cpt_active( 'highlight' ) ) {
    Themify_Builder_Model::register_module( 'TB_Highlight_Module' );
}
