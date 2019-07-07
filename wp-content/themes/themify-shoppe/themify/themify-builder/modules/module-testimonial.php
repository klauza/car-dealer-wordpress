<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Module Name: Testimonial
 * Description: Display testimonial custom post type
 */
class TB_Testimonial_Module extends Themify_Builder_Component_Module {
	function __construct() {
		parent::__construct(array(
			'name' => __('Testimonial', 'themify'),
			'slug' => 'testimonial'
		));

		///////////////////////////////////////
		// Load Post Type
		///////////////////////////////////////
		$this->meta_box = $this->set_metabox();
		$this->initialize_cpt( array(
			'plural' => __('Testimonials', 'themify'),
			'singular' => __('Testimonial', 'themify'),
			'menu_icon' => 'dashicons-testimonial'
		));

		if ( ! shortcode_exists( 'themify_'. $this->slug .'_posts' ) ) {
			add_shortcode( 'themify_'.$this->slug.'_posts', array( $this, 'do_shortcode' ) );
		}
	}

	public function get_title( $module ) {
		$type = isset( $module['mod_settings']['type_query_testimonial'] ) ? $module['mod_settings']['type_query_testimonial'] : 'category';
		$category = isset( $module['mod_settings']['category_testimonial'] ) ? $module['mod_settings']['category_testimonial'] : '';
		$slug_query = isset( $module['mod_settings']['query_slug_testimonial'] ) ? $module['mod_settings']['query_slug_testimonial'] : '';

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
				'id' => 'mod_title_testimonial',
				'type' => 'text',
				'label' => __('Module Title', 'themify'),
				'class' => 'large',
                                'render_callback' => array(
                                    'live-selector'=>'.module-title'
                                )
			),
			array(
				'id' => 'layout_testimonial',
				'type' => 'layout',
				'label' => __('Testimonial Layout', 'themify'),
                                'mode'=>'sprite',
				'options' => array(
					array('img' => 'grid4', 'value' => 'grid4', 'label' => __('Grid 4', 'themify')),
					array('img' => 'grid3', 'value' => 'grid3', 'label' => __('Grid 3', 'themify')),
					array('img' => 'grid2', 'value' => 'grid2', 'label' => __('Grid 2', 'themify')),
					array('img' => 'fullwidth', 'value' => 'fullwidth', 'label' => __('fullwidth', 'themify'))
				)
			),
			array(
				'id' => 'type_query_testimonial',
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
				'id' => 'category_testimonial',
				'type' => 'query_category',
				'label' => __('Category', 'themify'),
				'options' => array(
					'taxonomy' => 'testimonial-category'
				),
				'help' => sprintf(__('Add more <a href="%s" target="_blank">testimonials</a>', 'themify'), admin_url('post-new.php?post_type=testimonial')),
				'wrap_with_class' => 'tb_group_element tb_group_element_category'
			),
			array(
				'id' => 'query_slug_testimonial',
				'type' => 'text',
				'label' => __('Testimonial Slugs', 'themify'),
				'class' => 'large',
				'wrap_with_class' => 'tb_group_element tb_group_element_post_slug',
				'help' => '<br/>' . __( 'Insert Testimonial slug. Multiple slug should be separated by comma (,)', 'themify')
			),
			array(
				'id' => 'post_per_page_testimonial',
				'type' => 'text',
				'label' => __('Limit', 'themify'),
				'class' => 'xsmall',
				'help' => __('number of posts to show', 'themify')
			),
			array(
				'id' => 'offset_testimonial',
				'type' => 'text',
				'label' => __('Offset', 'themify'),
				'class' => 'xsmall',
				'help' => __('number of post to displace or pass over', 'themify')
			),
			array(
				'id' => 'order_testimonial',
				'type' => 'select',
				'label' => __('Order', 'themify'),
				'help' => __('Descending = show newer posts first', 'themify'),
				'options' => array(
					'desc' => __('Descending', 'themify'),
					'asc' => __('Ascending', 'themify')
				)
			),
			array(
				'id' => 'orderby_testimonial',
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
					'meta_value' => array( 'show' => array( 'meta_key_testimonial' ) ),
					'meta_value_num' => array( 'show' => array( 'meta_key_testimonial' ) ),
					'date' => array( 'hide' => array( 'meta_key_testimonial' ) ),
					'id' => array( 'hide' => array( 'meta_key_testimonial' ) ),
					'author' => array( 'hide' => array( 'meta_key_testimonial' ) ),
					'title' => array( 'hide' => array( 'meta_key_testimonial' ) ),
					'name' => array( 'hide' => array( 'meta_key_testimonial' ) ),
					'rand' => array( 'hide' => array( 'meta_key_testimonial' ) ),
					'comment_count' => array( 'hide' => array( 'meta_key_testimonial' ) )
				),
			),
			array(
				'id' => 'meta_key_testimonial',
				'type' => 'text',
				'label' => __( 'Custom Field Key', 'themify' ),
			),
			array(
				'id' => 'display_testimonial',
				'type' => 'select',
				'label' => __('Display', 'themify'),
				'options' => array(
					'content' => __('Content', 'themify'),
					'excerpt' => __('Excerpt', 'themify'),
					'none' => __('None', 'themify')
				)
			),
			array(
				'id' => 'hide_feat_img_testimonial',
				'type' => 'select',
				'label' => __('Hide Featured Image', 'themify'),
				'options' => array(
                                        ''=>'',
					'yes' => __('Yes', 'themify'),
					'no' => __('No', 'themify')
				)
			),
			array(
				'id' => 'image_size_testimonial',
				'type' => 'select',
				'label' =>__('Image Size', 'themify'),
				'hide' => !$is_img_enabled,
				'options' => $image_sizes
			),
			array(
				'id' => 'img_width_testimonial',
				'type' => 'text',
				'label' => __('Image Width', 'themify'),
				'class' => 'xsmall'
			),
			array(
				'id' => 'img_height_testimonial',
				'type' => 'text',
				'label' => __('Image Height', 'themify'),
				'class' => 'xsmall'
			),
			array(
				'id' => 'hide_post_title_testimonial',
				'type' => 'select',
				'label' => __('Hide Post Title', 'themify'),
				'options' => array(
                                        ''=>'',
					'yes' => __('Yes', 'themify'),
					'no' => __('No', 'themify')
				)
			),
			array(
				'id' => 'hide_page_nav_testimonial',
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
				'id' => 'css_testimonial',
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
                        self::get_color('.module-testimonial .post', 'background_color',__( 'Background Color', 'themify' ),'background-color'),
			// Font
                        self::get_seperator('font',__('Font', 'themify')),
                        self::get_font_family(array( '.module-testimonial .post-title', '.module-testimonial .post-title a' )),
                        self::get_element_font_weight(array( '.module-testimonial .post-title', '.module-testimonial .post-title a' )),
                        self::get_color(array( '.module-testimonial .post', '.module-testimonial h1', '.module-testimonial h2', '.module-testimonial h3:not(.module-title)', '.module-testimonial h4', '.module-testimonial h5', '.module-testimonial h6', '.module-testimonial .post-title', '.module-testimonial .post-title a' ),'font_color',__('Font Color', 'themify')),
                        self::get_font_size('.module-testimonial .post'),
                        self::get_line_height('.module-testimonial .post'),
                        self::get_letter_spacing('.module-testimonial .post'),
                        self::get_text_align('.module-testimonial .post'),
                        self::get_text_transform('.module-testimonial .post'),
                        self::get_font_style('.module-testimonial .post'),
                        self::get_text_decoration('.module-testimonial .post','text_decoration_regular'),
			// Link
                        self::get_seperator('link',__('Link', 'themify')),
                        self::get_color( '.module-testimonial a','link_color'),
                        self::get_color('.module-testimonial a:hover','link_color_hover',__('Color Hover', 'themify')),
                        self::get_text_decoration('.module-testimonial a'),
			 // Padding
                        self::get_seperator('padding',__('Padding', 'themify')),
                        self::get_padding('.module-testimonial .post'),
			// Margin
                        self::get_seperator('margin',__('Margin', 'themify')),
                        self::get_margin('.module-testimonial .post'),
                        // Border
                        self::get_seperator('border',__('Border', 'themify')),
                        self::get_border('.module-testimonial .post')
		);

		$testimonial_title = array(
			// Font
                        self::get_seperator('font',__('Font', 'themify'),false),
                        self::get_font_family(array( '.module-testimonial .post-title', '.module-testimonial .post-title a' ),'font_family_title'),
                        self::get_element_font_weight(array( '.module-testimonial .post-title', '.module-testimonial .post-title a' ),'font_weight_title'),
                        self::get_color(array( '.module-testimonial .post-title', '.module-testimonial .post-title a' ),'font_color_title',__('Font Color', 'themify')),
                        self::get_color(array( '.module-testimonial .post-title:hover', '.module-testimonial .post-title a:hover' ),'font_color_title_hover',__('Color Hover', 'themify')),
                        self::get_font_size('.module-testimonial .post-title','font_size_title'),
                        self::get_line_height('.module-testimonial .post-title','line_height_title'),
						self::get_letter_spacing('.module-testimonial .post-title', 'letter_spacing_title'),
                        self::get_text_transform('.module-testimonial .post-title','t_t_t'),
                        self::get_font_style('.module-testimonial .post-title','f_sy_t','f_b_t')
		);

		$testimonial_content = array(
			// Font
                        self::get_font_family('.module-testimonial .testimonial-post .post-content','font_family_content'),
                        self::get_element_font_weight('.module-testimonial .testimonial-post .post-content','font_weight_content'),
                        self::get_color('.module-testimonial .testimonial-post .post-content','font_color_content',__('Font Color', 'themify')),
                        self::get_font_size('.module-testimonial .testimonial-post .post-content','font_size_content'),
                        self::get_line_height('.module-testimonial .testimonial-post .post-content','line_height_content')
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
						'label' => __('Testimonial Title', 'themify'),
						'fields' => $testimonial_title
					),
					'content' => array(
						'label' => __('Testimonial Content', 'themify'),
						'fields' => $testimonial_content
					)
				)
			)
		);

	}
        
        public function get_visual_type() {
            return 'ajax';            
        }
        
	function set_metabox() {
		// Testimonial Meta Box Options
		$meta_box = array(
			// Feature Image
			Themify_Builder_Model::$post_image,
			// Featured Image Size
			Themify_Builder_Model::$featured_image_size,
			// Image Width
			Themify_Builder_Model::$image_width,
			// Image Height
			Themify_Builder_Model::$image_height,
			// Testimonial Author Name
			array(
				'name' 		=> '_testimonial_name',
				'title' 	=> __('Testimonial Author Name', 'themify'),
				'description' => '',
				'type' 		=> 'textbox',
				'meta'		=> array()
			),
			// Testimonial Author Link
			array(
				'name' 		=> '_testimonial_link',
				'title' 	=> __('Testimonial Author Link', 'themify'),
				'description' => '',
				'type' 		=> 'textbox',
				'meta'		=> array()
			),
			// Testimonial Author Company
			array(
				'name' 		=> '_testimonial_company',
				'title' 	=> __('Testimonial Author Company', 'themify'),
				'description' => '',
				'type' 		=> 'textbox',
				'meta'		=> array()
			),
			// Testimonial Author Position
			array(
				'name' 		=> '_testimonial_position',
				'title' 	=> __('Testimonial Author Position', 'themify'),
				'description' => '',
				'type' 		=> 'textbox',
				'meta'		=> array()
			)
		);
		return $meta_box;
	}

	function do_shortcode( $atts ) {

		extract( shortcode_atts( array(
			'id' => '',
			'title' => 'no', // no
			'image' => 'yes', // no
			'image_w' => 80,
			'image_h' => 80,
			'display' => 'content', // excerpt, none
			'more_link' => false, // true goes to post type archive, and admits custom link
			'more_text' => __('More &rarr;', 'themify'),
			'limit' => 4,
			'category' => 0, // integer category ID
			'order' => 'DESC', // ASC
			'orderby' => 'date', // title, rand
			'style' => 'grid2', // grid3, grid4, list-post
			'show_author' => 'yes', // no
			'section_link' => false // true goes to post type archive, and admits custom link
		), $atts ) );

		$sync = array(
			'mod_title_testimonial' => '',
			'layout_testimonial' => $style,
			'category_testimonial' => $category,
			'post_per_page_testimonial' => $limit,
			'offset_testimonial' => '',
			'order_testimonial' => $order,
			'orderby_testimonial' => $orderby,
			'display_testimonial' => $display,
			'hide_feat_img_testimonial' => '',
			'image_size_testimonial' => '',
			'img_width_testimonial' => $image_w,
			'img_height_testimonial' => $image_h,
			'unlink_feat_img_testimonial' => 'no',
			'hide_post_title_testimonial' => $title === 'yes' ? 'no' : 'yes',
			'unlink_post_title_testimonial' => 'no',
			'hide_post_date_testimonial' => 'no',
			'hide_post_meta_testimonial' => 'no',
			'hide_page_nav_testimonial' => 'yes',
			'animation_effect' => '',
			'css_testimonial' => ''
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

if( ! function_exists( 'themify_builder_testimonial_author_name' ) ) :
	function themify_builder_testimonial_author_name( $post, $show_author ) {
		$out = '';
		if( 'yes' === $show_author){
			if( $author = get_post_meta( $post->ID, '_testimonial_name', true ) )
				$out = '<span class="dash"></span><cite class="testimonial-name">' . $author . '</cite> <br/>';

			if( $position = get_post_meta( $post->ID, '_testimonial_position', true ) )
				$out .= '<em class="testimonial-title">' . $position;

				if( $link = get_post_meta( $post->ID, '_testimonial_link', true ) ){
					if( $position ){
						$out .= ', ';
					}
					else {
						$out .= '<em class="testimonial-title">';
					}
					$out .= '<a href="'.esc_url($link).'">';
				}

					if( $company = get_post_meta( $post->ID, '_testimonial_company', true ) )
						$out .= $company;
					else
						$out .= $link;

				if( $link ) $out .= '</a>';

			$out .= '</em>';
		}
		return $out;
	}
endif;

///////////////////////////////////////
// Module Options
///////////////////////////////////////
if( Themify_Builder_Model::is_cpt_active( 'testimonial' ) ) {
	Themify_Builder_Model::register_module( 'TB_Testimonial_Module' );
}
