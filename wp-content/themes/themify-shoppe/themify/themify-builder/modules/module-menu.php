<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Module Name: Menu
 * Description: Display Custom Menu
 */
class TB_Menu_Module extends Themify_Builder_Component_Module {
	function __construct() {
		parent::__construct(array(
			'name' => __('Menu', 'themify'),
			'slug' => 'menu'
		));
	}
		
		public function get_title( $module ) {
		return isset( $module['mod_settings']['custom_menu'] ) ? $module['mod_settings']['custom_menu'] : '';
	}

	public function get_options() {
		$menus = get_terms( 'nav_menu', array( 'hide_empty' => true ) );
				$colors = Themify_Builder_Model::get_colors();
				$colors[] = array('img' => 'transparent', 'value' => 'transparent', 'label' => __('Transparent', 'themify'));
		return array(
			array(
				'id' => 'mod_title_menu',
				'type' => 'text',
				'label' => __('Module Title', 'themify'),
				'class' => 'large',
                                'render_callback' => array(
                                    'live-selector'=>'.module-title'
                                )
			),
			array(
				'id' => 'layout_menu',
				'type' => 'layout',
				'label' => __('Menu Layout', 'themify'),
                                'mode'=>'sprite',
				'options' => array(
					array('img' => 'menu_bar', 'value' => 'menu-bar', 'label' => __('Menu Bar', 'themify')),
					array('img' => 'menu_fullbar', 'value' => 'fullwidth', 'label' => __('Menu Fullbar', 'themify')),
					array('img' => 'menu_vertical', 'value' => 'vertical', 'label' => __('Menu Vertical', 'themify'))
				)
			),
			array(
				'id' => 'custom_menu',
				'type' => 'select_menu',
				'label' => __('Custom Menu', 'themify'),
				'options' => $menus,
				'help' => sprintf(__('Add more <a href="%s" target="_blank">menu</a>', 'themify'), admin_url( 'nav-menus.php' )),
				'break' => true
			),
			array(
				'id' => 'allow_menu_fallback',
				'pushed' => 'pushed',
				'type' => 'checkbox',
				'label' => false,
				'options' => array(
					array( 'name' => 'allow_fallback', 'value' => __( 'If no menu found, list all pages', 'themify' ) )
				)
			),
			array(
				'id' => 'allow_menu_breakpoint',
				'pushed' => 'pushed',
				'type' => 'checkbox',
				'label' => false,
				'options' => array(
					array( 'name' => 'allow_menu', 'value' => __( 'Enable mobile menu', 'themify' ) )
				),
				'option_js' => true
			),
			array(
				'id' => 'menu_breakpoint',
				'pushed' => 'pushed',
				'class'=>'xsmall',
				'type' => 'text',
				'label' => false,
				'after' => __('Mobile menu breakpoint (px)', 'themify'),
				'binding' => array(
					'empty' => array(
						'hide' => array('menu_slide_direction')
					),
					'not_empty' => array(
						'show' => array('menu_slide_direction')
					)
				),
				'wrap_with_class' => 'ui-helper-hidden tb-checkbox_element tb-checkbox_element_allow_menu'
			),
			array(
				'id' => 'menu_slide_direction',
				'pushed' => 'pushed',
				'type' => 'select',
				'label' => false,
				'after' => __('Mobile slide direction', 'themify'),
				'options' => array(
					'right' => __('Right', 'themify'),
					'left' => __('Left', 'themify')
				),
				'wrap_with_class' => 'ui-helper-hidden tb-checkbox_element tb-checkbox_element_allow_menu'
			),
			array(
				'id' => 'color_menu',
				'type' => 'layout',
				'label' => __('Menu Color', 'themify'),
                                'class'=>'tb_colors',
                                'mode'=>'sprite',
				'options' =>$colors
			),
			array(
				'id' => 'according_style_menu',
				'type' => 'checkbox',
				'label' => __('Menu Appearance', 'themify'),
				'options' => Themify_Builder_Model::get_appearance()
			),
			// Additional CSS
			array(
				'type' => 'separator',
				'meta' => array( 'html' => '<hr/>')
			),
			array(
				'id' => 'css_menu',
				'type' => 'text',
				'label' => __('Additional CSS Class', 'themify'),
				'class' => 'large exclude-from-reset-field',
				'help' => sprintf( '<br/><small>%s</small>', __('Add additional CSS class(es) for custom styling (<a href="https://themify.me/docs/builder#additional-css-class" target="_blank">learn more</a>).', 'themify') )
			)
		);
	}
		
		public function get_visual_type() {
			return 'ajax';            
		}
		
	public function get_styling() {
		$general = array(
			// Background
			self::get_seperator('image_bacground',__( 'Background', 'themify' ),false),
			self::get_color('.module-menu .nav li', 'background_color',__( 'Background Color', 'themify' ),'background-color'),
			// Font
			self::get_seperator('font',__('Font', 'themify')),
			self::get_font_family('.module-menu .nav li'),
			self::get_element_font_weight('.module-menu .nav li'),
			self::get_color('.module-menu .nav li','font_color',__('Font Color', 'themify')),
			self::get_font_size('.module-menu .nav li'),
			self::get_letter_spacing('.module-menu .nav li'),
			self::get_text_align('.module-menu .nav'),
			self::get_text_transform('.module-menu .nav'),
			self::get_font_style('.module-menu .nav'),
			// Padding
			self::get_seperator('padding',__('Padding', 'themify')),
			self::get_padding('.module-menu .nav li'),
			// Margin
			self::get_seperator('margin',__('Margin', 'themify')),
			self::get_margin('.module-menu'),
			// Border
			self::get_seperator('border',__('Border', 'themify')),
			self::get_border( '.module-menu .nav li')
		);

		$menu_links = array (
			// Background
			self::get_seperator('link',__( 'Background', 'themify' ),false),
			self::get_color('.module-menu .nav > li > a', 'link_background_color',__( 'Background Color', 'themify' ),'background-color'),
			self::get_color('.module-menu .nav > li > a:hover', 'link_hover_background_color',__( 'Background Hover', 'themify' ),'background-color'),
			// Link
			self::get_seperator('link',__('Font', 'themify')),
			self::get_color( '.module-menu .nav > li > a','link_color'),
			self::get_color('.module-menu  .nav > li > a:hover','link_color_hover',__('Color Hover', 'themify')),
			self::get_text_decoration('.module-menu .nav > li > a'),
			// Padding
			self::get_seperator('padding',__('Padding', 'themify')),
			self::get_padding('.module-menu .nav > li > a', 'p_m_l'),
			// Margin
			self::get_seperator('margin',__('Margin', 'themify')),
			self::get_margin('.module-menu .nav > li > a', 'm_m_l'),
			// Border
			self::get_seperator('border',__('Border', 'themify')),
			self::get_border('.module-menu .nav > li > a', 'b_m_l')
		);

		$current_menu_links = array (
			// Background
			self::get_seperator('current-links',__( 'Background', 'themify' ),false),
			self::get_color('.module-menu li.current_page_item > a, .module-menu li.current-menu-item > a', 'current-links_background_color',__( 'Background Color', 'themify' ),'background-color'),
			self::get_color('.module-menu li.current_page_item > a:hover, .module-menu li.current-menu-item > a:hover', 'current-links_hover_background_color',__( 'Background Hover', 'themify' ),'background-color'),
			// Link
			self::get_seperator('current-links',__('Font', 'themify')),
			self::get_color( '.module-menu li.current_page_item > a, .module-menu li.current-menu-item > a','current-links_color'),
			self::get_color('.module-menu li.current_page_item > a:hover, .module-menu li.current-menu-item > a:hover','current-links_color_hover',__('Color Hover', 'themify')),
			self::get_text_decoration('.module-menu li.current_page_item a, .module-menu li.current-menu-item a','current-links_text_decoration')
		);

		$menu_dropdown = array (
			// Background
			self::get_seperator('link',__( 'Background', 'themify' ),false),
			self::get_color('.module-menu li > ul', 'dropdown_background_color',__( 'Background Color', 'themify' ),'background-color'),
			// Link
			self::get_seperator('link',__('Font Color', 'themify')),
			self::get_color( '.module-menu li > ul','dropdown_color'),
			// Padding
			self::get_seperator('padding',__('Padding', 'themify')),
			self::get_padding( '.module-menu li > ul','dropdown_padding'),
			// Border
			self::get_seperator('border',__('Border', 'themify')),
			self::get_border( '.module-menu li > ul','dropdown_border')
		);

		$menu_dropdown_links = array (
			// Background
			self::get_seperator('link',__( 'Background', 'themify' ),false),
			self::get_color('.module-menu li > ul a', 'dropdown_links_background_color',__( 'Background Color', 'themify' ),'background-color'),
			self::get_color('.module-menu li > ul a:hover', 'dropdown_links_hover_background_color',__( 'Background Hover', 'themify' ),'background-color'),
			// Font
			self::get_seperator('font',__('Font', 'themify')),
			self::get_font_family('.module-menu li > ul a','font_family_menu_dropdown_links'),
			self::get_element_font_weight('.module-menu li > ul a','font_weight_menu_dropdown_links'),
			self::get_color( '.module-menu li > ul a','dropdown_links_color'),
			self::get_color('.module-menu li > ul a:hover','dropdown_links_hover_color',__('Color Hover', 'themify')),
			self::get_font_size('.module-menu li > ul a','font_size_menu_dropdown_links'),
			self::get_line_height('.module-menu li > ul a','l_h_m_d_l'),
			self::get_letter_spacing('.module-menu li > ul a','l_s_m_d_l'),
			self::get_text_align('.module-menu li > ul a','t_a_m_d_l'),
			self::get_text_transform('.module-menu li > ul a','t_t_m_d_l'),
			self::get_font_style('.module-menu li > ul a','f_d_l','f_d_b'),
			self::get_text_decoration('.module-menu li > ul a','t_d_m_d_l'),
			// Padding
			self::get_seperator('padding',__('Padding', 'themify')),
			self::get_padding( '.module-menu li > ul a','d_l_p'),
			// Margin
			self::get_seperator('margin',__('Margin', 'themify')),
			self::get_margin( '.module-menu li > ul a','d_l_m'),
			// Border
			self::get_seperator('border',__('Border', 'themify')),
			self::get_border( '.module-menu li > ul a','d_l_b')
		);

		$menu_mobile = array (
			// Background
			self::get_seperator('link',__( 'Background', 'themify' ),false),
			self::get_color( '.ui.mobile-menu-module', 'mobile_menu_background_color',__( 'Background Color', 'themify' ),'background-color'),
			// Link
			self::get_seperator('link',__('Font', 'themify')),
			self::get_font_family('.ui.mobile-menu-module li a','f_f_m_m'),
			self::get_element_font_weight('.ui.mobile-menu-module li a','f_w_m_m'),
			self::get_color( '.ui.mobile-menu-module li a','m_c_m_m'),
			self::get_color( '.ui.mobile-menu-module li a:hover','m_c_h_m_m',__('Color Hover', 'themify') ),
			self::get_font_size('.ui.mobile-menu-module li a','f_s_m_m'),
			self::get_line_height('.ui.mobile-menu-module li a','l_h_m_m'),
			self::get_letter_spacing('.ui.mobile-menu-module li a','l_s_m_m'),
			self::get_text_align('.ui.mobile-menu-module li a','t_a_m_m'),
			self::get_text_transform('.ui.mobile-menu-module li a','t_t_m_m'),
			self::get_font_style('.ui.mobile-menu-module li a','f_sy_m_m','f_b_m_m'),
			self::get_text_decoration('.ui.mobile-menu-module li a','t_d_m_m')
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
					'links' => array(
						'label' => __('Menu Links', 'themify'),
						'fields' => $menu_links
					),
					'current-links' => array(
						'label' => __('Current Links', 'themify'),
						'fields' => $current_menu_links
					),
					'dropdown' => array(
						'label' => __('Dropdown Container', 'themify'),
						'fields' => $menu_dropdown
					),
					'dropdown_links' => array(
						'label' => __('Dropdown Links', 'themify'),
						'fields' => $menu_dropdown_links
					),
					'mobile' => array(
						'label' => __('Mobile Menu', 'themify'),
						'fields' => $menu_mobile
					)
				)
			)
		);

	}
}

///////////////////////////////////////
// Module Options
///////////////////////////////////////
Themify_Builder_Model::register_module( 'TB_Menu_Module' );
