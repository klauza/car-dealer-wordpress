<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Module Name: WooCommerce Product Categories
 */
class TB_Product_Categories_Module extends Themify_Builder_Component_Module {
	function __construct() {
		parent::__construct(array(
			'name' => __('Product Categories', 'builder-wc'),
			'slug' => 'product-categories'
		));
	}

	function get_assets() {
		$instance = Builder_Woocommerce::get_instance();
		return array(
			'selector' => '.module-product-categories',
			'css' => themify_enque($instance->url . 'assets/style.css'),
			'ver' => $instance->version
		);
	}

	public function get_options() {
		return array(
			array(
				'id' => 'mod_title',
				'type' => 'text',
				'label' => __('Module Title', 'builder-wc'),
				'class' => 'large',
				'render_callback' => array(
					'live-selector'=>'.module-title'
				)
			),
			array(
				'id' => 'columns',
				'type' => 'layout',
				'mode'=>'sprite',
				'label' => __('Layout', 'builder-wc'),
				'options' => array(
					array('img' => 'list_post', 'value' => 1, 'label' => __('1 Column', 'builder-wc')),
					array('img' => 'grid2', 'value' => 2, 'label' => __('2 Columns', 'builder-wc')),
					array('img' => 'grid3', 'value' => 3, 'label' => __('3 Columns', 'builder-wc')),
					array('img' => 'grid4', 'value' => 4, 'label' => __('4 Columns', 'builder-wc'),'selected'=>true),
				)
			),
			array(
				'id' => 'child_of',
				'type' => 'product_categories',
				'label' => __('Categories', 'builder-wc'),
			),
			array(
				'id' => 'exclude',
				'type' => 'text',
				'label' => __('Exclude Categories', 'builder-wc'),
				'class' => 'large',
				'help' => __('Comma-separated list of product category IDs to exclude.', 'builder-wc'),
			),
			array(
				'id' => 'orderby',
				'type' => 'select',
				'label' => __('Order By', 'builder-wc'),
				'options' => array(
					'name' => __('Name', 'builder-wc'),
					'id' => __('ID', 'builder-wc'),
					'count' => __('Product Count', 'builder-wc'),
				)
			),
			array(
				'id' => 'order',
				'type' => 'select',
				'label' => __('Order', 'builder-wc'),
				'help' => __('Descending = show newer posts first', 'builder-wc'),
				'options' => array(
					'desc' => __('Descending', 'builder-wc'),
					'asc' => __('Ascending', 'builder-wc')
				)
			),
			array(
				'id' => 'number',
				'type' => 'text',
				'label' => __('Limit', 'builder-wc'),
				'class' => 'xsmall',
				'help' => __('The maximum number of terms to show. Leave empty to show all.', 'builder-wc'),
			),
			array(
				'id' => 'hide_empty',
				'type' => 'select',
				'label' => __('Hide Empty Categories', 'builder-wc'),
				'options' => array(
					'yes' => __('Yes', 'builder-wc'),
					'no' => __('No', 'builder-wc'),
				)
			),
			array(
				'id' => 'pad_counts',
				'type' => 'select',
				'label' => __('Show Product Counts', 'builder-wc'),
				'options' => array(
					'yes' => __('Yes', 'builder-wc'),
					'no' => __('No', 'builder-wc'),
				)
			),
			array(
				'id' => 'display',
				'type' => 'radio',
				'label' => __('Display inside category', 'builder-wc'),
				'options' => array(
					'products' => __('Latest Products', 'builder-wc'),
					'subcategories' => __('Subcategories', 'builder-wc'),
					'none' => __('None', 'builder-wc'),
				),
				'option_js' => true,
			),
			array(
				'id' => 'latest_products',
				'type' => 'select',
				'label' => __('Latest Products', 'builder-wc'),
				'options' => array(
					'1' => 1,
					'2' => 2,
					'3' => 3,
					'4' => 4,
					'5' => 5,
					'6' => 6,
					'7' => 7,
					'8' => 8,
					'9' => 9,
					'10' => 10
				),
				'help' => __('Number of latest products to show.', 'builder-wc'),
				'wrap_with_class' => 'tb_group_element tb_group_element_products',
			),
			array(
				'id' => 'subcategories_number',
				'type' => 'text',
				'label' => __('Subcategories Limit', 'builder-wc'),
				'class' => 'xsmall',
				'help' => __('The maximum number of subcategories to show. Leave empty to show all.', 'builder-wc'),
				'wrap_with_class' => 'tb_group_element tb_group_element_subcategories',
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
			'latest_products' => '3',
			'columns' => '4'
		);
	}

	public function get_animation() {
		return array();
	}
        
	public function get_visual_type() {
		return 'ajax';            
	}

	public function get_styling() {
		return array(
			// Background
			self::get_seperator('image_bacground',__( 'Background', 'themify' ),false),
			self::get_color('.module-product-categories', 'background_color',__( 'Background Color', 'themify' ),'background-color'),
			// Font
			self::get_seperator('font',__('Font', 'themify')),
			self::get_font_family('.module-product-categories'),
			! method_exists( __CLASS__, 'get_element_font_weight' ) ? '' : self::get_element_font_weight( '.module-product-categories' ),
			self::get_color('.module-product-categories .products .product a','font_color',__('Font Color', 'themify')),
			self::get_font_size('.module-product-categories'),
			self::get_line_height('.module-product-categories'),
			self::get_text_align('.module-product-categories .products .product'),
			self::get_text_transform('.module-product-categories .products .product h3', 'text_transform_title'),
			self::get_font_style('.module-product-categories .products .product h3', 'font_style_title'),
			// Link
			self::get_seperator('link',__('Link', 'themify')),
			self::get_color('.module.module-product-categories a h3','link_color'),
			self::get_color('.module.module-product-categories a h3:hover','link_color_hover'),
			self::get_text_decoration('.module.module-product-categories a h3'),
			// Padding
			self::get_seperator('padding',__('Padding', 'themify')),
			self::get_padding('.module-product-categories'),
			// Margin
			self::get_seperator('margin',__('Margin', 'themify')),
			self::get_margin('.module-product-categories'),
			// Border
			self::get_seperator('border',__('Border', 'themify')),
			self::get_border('.module-product-categories')
		);
	}
}

function themify_builder_field_product_categories( $field, $module_name ) {
	$dropdown = wp_dropdown_categories( array(
		'taxonomy' => 'product_cat',
		'class' => 'tb_lb_option',
		'show_option_all' => false,
		'hide_empty' => 1,
		'echo' => false,
		'name' => $field['id'],
		'selected' => '',
		'value_field' => 'slug',
	) );
	$before =
		'<optgroup label="' . __( 'All Categories', 'builder-wc' ) . '">'
			. '<option value="0">' . __( 'All Categories', 'builder-wc' ) . '</option>'
		. '</optgroup>'
		. '<optgroup label="' . __( 'Only Top Level', 'builder-wc' ) . '">'
			. '<option value="top-level">' . __( 'Only Top Level Categories', 'builder-wc' ) . '</option>'
		. '</optgroup>'
		. '<optgroup label="' . __( 'Category', 'builder-wc' ) . '">';
	$dropdown = preg_replace( '/<select([^>]*)>/', '<select data-control-type="change" data-control-binding="refresh" $1>' . $before, $dropdown );
	$dropdown = preg_replace( '/<\/select>/', '</optgroup></select>', $dropdown );
	echo '<div class="tb_field ' . $field['id'] . '">
		<div class="tb_label">'. $field['label'] .'</div>
		<div class="tb_input"><div class="selectwrapper">',$dropdown,'</div>';

	if( isset( $field['description'] ) ){
		echo '<p class="description">' . $field['description'] . '</p>';
	}

	echo '</div></div>';
}

Themify_Builder_Model::register_module( 'TB_Product_Categories_Module' );