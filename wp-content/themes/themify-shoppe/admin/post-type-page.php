<?php
/**
 * Page Meta Box Options
 *
 * @since 1.0.0
 *
 * @param array $args
 *
 * @return array
 */
function themify_theme_page_meta_box( $args = array() ){
	extract( $args );
	return array(
		// Page Layout
		array(
			'name' 		=> 'page_layout',
			'title'		=> __('Sidebar Option', 'themify'),
			'description'	=> '',
			'type'		=> 'layout',
			'show_title' => true,
			'hide'		  => 'sidebar-none default',
			'meta'		=> array(
				array('value' => 'default', 'img' => 'images/layout-icons/default.png', 'selected' => true, 'title' => __('Default', 'themify')),
				array('value' => 'sidebar1', 'img' => 'images/layout-icons/sidebar1.png', 'title' => __('Sidebar Right', 'themify')),
				array('value' => 'sidebar1 sidebar-left', 'img' => 'images/layout-icons/sidebar1-left.png', 'title' => __('Sidebar Left', 'themify')),
				array('value' => 'sidebar-none', 'img' => 'images/layout-icons/sidebar-none.png', 'title' => __('No Sidebar ', 'themify'))
			),
			'default' => 'default'
		),
		// Sidebar Type
		array(
			'name'        => 'sidebar_type',
			'title'       => '',
			'description' => '',
			'type'		  => 'radio',
			'meta'        => array(
				array( 'value' => 'main', 'name' => __( 'Display default sidebar', 'themify' ), 'selected' => true  ),
				array( 'value' => 'shop', 'name' => __( 'Display Shop sidebar', 'themify' ), )
			),
			'display_callback' => 'themify_is_woocommerce_active',
			'enable_toggle' => true,
			'class'		  => 'hide-if sidebar-none default',
			'default' => 'main',
		),
		// Content Width
		array(
			'name'=> 'content_width',
			'title' => __('Content Width', 'themify'),
			'description' => 'Select "Fullwidth" if the page is to be built with the Builder without the sidebar (it will make the Builder content fullwidth).',
			'type' => 'layout',
			'show_title' => true,
			'meta' => array(
				array(
					'value' => 'default_width',
					'img' => 'themify/img/default.png',
					'selected' => true,
					'title' => __( 'Default', 'themify' )
				),
				array(
					'value' => 'full_width',
					'img' => 'themify/img/fullwidth.png',
					'title' => __( 'Fullwidth', 'themify' )
				)
			),
			'default' => 'default_width'
		),		
		// Hide page title
		array(
			'name' 		=> 'hide_page_title',
			'title'		=> __('Hide Page Title', 'themify'),
			'description'	=> '',
			'type' 		=> 'dropdown',
			'meta'		=> array(
				array('value' => 'default', 'name' => '', 'selected' => true),
				array('value' => 'yes', 'name' => __('Yes', 'themify')),
				array('value' => 'no',	'name' => __('No', 'themify'))
			),
			'default' => 'default'
		),
		// Custom menu
		array(
			'name'        => 'custom_menu',
			'title'       => __( 'Custom Menu', 'themify' ),
			'description' => '',
			'type'        => 'dropdown',
			// extracted from $args
			'meta'        => $args['nav_menus'],
		),
		
	);
}

/**
 * Query Posts Options
 * @param array $args
 * @return array
 * @since 1.0.0
 */
function themify_theme_query_post_meta_box($args = array()) {
	extract( $args );
	return array(
		// Notice
		array(
			'name' => '_query_posts_notice',
			'title' => '',
			'description' => '',
			'type' => 'separator',
			'meta' => array(
				'html' => '<div class="themify-info-link">' . sprintf( __( '<a href="%s">Query Posts</a> allows you to query WordPress posts from any category on the page. To use it, select a Query Category.', 'themify' ), 'https://themify.me/docs/query-posts' ) . '</div>'
			),
		),
		// Query Category
		array(
			'name' 		=> 'query_category',
			'title'		=> __('Query Category', 'themify'),
			'description'	=> __('Select a category or enter multiple category IDs (eg. 2,5,6). Enter 0 to display all category.', 'themify'),
			'type'		=> 'query_category',
			'meta'		=> array()
		),
		// Query All Post Types
		array(
			'name' => 'query_all_post_types',
			'type' => 'dropdown',
			'title' => __( 'Query All Post Types', 'themify'),
			'meta' =>array(
				array(
				'value' => '',
				'name' => '',
				),
				array(
				'value' => 'yes',
				'name' => 'Yes',
				),
				array(
				'value' => 'no',
				'name' => 'No',
				),
			)
		),
		// Descending or Ascending Order for Posts
		array(
			'name' 		=> 'order',
			'title'		=> __('Order', 'themify'),
			'description'	=> '',
			'type'		=> 'dropdown',
			'meta'		=> array(
				array('name' => __('Descending', 'themify'), 'value' => 'desc', 'selected' => true),
				array('name' => __('Ascending', 'themify'), 'value' => 'asc')
			),
			'default' => 'desc'
		),
		// Criteria to Order By
		array(
			'name' 		=> 'orderby',
			'title'		=> __('Order By', 'themify'),
			'description'	=> '',
			'type'		=> 'dropdown',
			'meta'		=> array(
				array('name' => __('Date', 'themify'), 'value' => 'date', 'selected' => true),
				array('name' => __('Random', 'themify'), 'value' => 'rand'),
				array('name' => __('Author', 'themify'), 'value' => 'author'),
				array('name' => __('Post Title', 'themify'), 'value' => 'title'),
				array('name' => __('Comments Number', 'themify'), 'value' => 'comment_count'),
				array('name' => __('Modified Date', 'themify'), 'value' => 'modified'),
				array('name' => __('Post Slug', 'themify'), 'value' => 'name'),
				array('name' => __('Post ID', 'themify'), 'value' => 'ID'),
				array('name' => __( 'Custom Field String', 'themify' ), 'value' => 'meta_value'),
				array('name' => __( 'Custom Field Numeric', 'themify' ), 'value' => 'meta_value_num')
			),
			'default' => 'date',
			'hide' => 'date|rand|author|title|comment_count|modified|name|ID field-meta-key'
		),
		array(
			'name'			=> 'meta_key',
			'title'			=> __( 'Custom Field Key', 'themify' ),
			'description'	=> '',
			'type'			=> 'textbox',
			'meta'			=> array('size' => 'medium'),
			'class'			=> 'field-meta-key'
		),
		// Post Layout
		array(
			'name' 		=> 'layout',
			'title'		=> __('Query Post Layout', 'themify'),
			'description'	=> '',
			'type'		=> 'layout',
			'show_title' => true,
			'meta'		=> array(
				array(
					'value' => 'list-post',
					'img' => 'images/layout-icons/list-post.png',
					'selected' => true
				),
				array(
					'value' => 'grid4',
					'img'   => 'images/layout-icons/grid4.png',
					'title' => __( 'Grid 4', 'themify' )
				),
				array(
					'value' => 'grid3',
					'img'   => 'images/layout-icons/grid3.png',
					'title' => __( 'Grid 3', 'themify' )
				),
				array(
					'value' => 'grid2',
					'img'   => 'images/layout-icons/grid2.png',
					'title' => __( 'Grid 2', 'themify' )
				),
				array(
					'value' => 'list-large-image',
					'img'   => 'images/layout-icons/list-large-image.png',
					'title' => __( 'List Large Image', 'themify' )
				),
				array(
					'value' => 'auto_tiles', 
					'img' => 'images/layout-icons/auto-tiles.png', 
					'title' => __('Auto Tiles', 'themify')
				)
			),
			'default' => 'list-post',
		),
		// Post Content Style
		array(
			'name' 		=> 'post_content_layout',
			'title'		=> __( 'Post Content Style', 'themify' ),
			'description'	=> '',
			'type'		=> 'dropdown',
			'meta'		=> array(
				array(
					'value' => '',
					'name' => '',
					'selected' => true,
				),
				array(
					'value' => 'overlay',
					'name' => __( 'Overlay', 'themify' ),
				),
				array(
					'value' => 'polaroid',
					'name' => __( 'Polaroid', 'themify' ),
				),
				array(
					'value' => 'boxed',
					'name' => __( 'Boxed', 'themify' ),
				),
				array(
					'value' => 'flip',
					'name' => __( 'Flip', 'themify' ),
				)
			)
		),
		// Post Masonry
		array(
			'name' 		=> 'post_masonry',
			'title'		=> __( 'Post Masonry', 'themify' ),
			'description'	=> '',
			'type'		=> 'dropdown',
			'meta'		=> array(
				array(
					'value' => '',
					'name' => '',
					'selected' => true,
				),
				array(
					'value' => 'yes',
					'name' => __( 'Enable', 'themify' ),
				),
				array(
					'value' => 'no',
					'name' => __( 'Disable', 'themify' ),
				)
			)
		),
		// Post Gutter
		array(
			'name' 		=> 'post_gutter',
			'title'		=> __( 'Post Gutter', 'themify' ),
			'description'	=> '',
			'type'		=> 'dropdown',
			'meta'		=> array(
				array(
					'value' => '',
					'name' => '',
					'selected' => true,
				),
				array(
					'value' => 'no-gutter',
					'name' => __( 'No gutter', 'themify' ),
				)
			)
		),
		// Infinite Scroll
		array(
			'name' 		=> 'more_posts',
			'title'		=> __( 'Infinite Scroll', 'themify' ),
			'description'	=> '',
			'type'		=> 'dropdown',
			'meta'		=> array(
				array(
					'value' => '',
					'name' => '',
					'selected' => true,
				),
				array(
					'value' => 'infinite',
					'name' => __( 'Enable', 'themify' ),
				),
				array(
					'value' => 'pagination',
					'name' => __( 'Disable', 'themify' ),
				)
			)
		),
		// Posts Per Page
		array(
			'name' 		=> 'posts_per_page',
			'title'		=> __('Posts Per Page', 'themify'),
			'description'	=> '',
			'type'		=> 'textbox',
			'meta'		=> array('size' => 'small')
		),
		// Display Content
		array(
			'name' 		=> 'display_content',
			'title'		=> __('Display Content', 'themify'),
			'description'	=> '',
			'type'		=> 'dropdown',
			'meta'		=> array(
				array( 'name' => __('Full Content', 'themify'), 'value' => 'content' ),
				array( 'name' => __('Excerpt', 'themify'), 'value' => 'excerpt', 'selected' => true ),
				array( 'name' => __('None', 'themify'), 'value' => 'none' )
			),
			'default' => 'excerpt',
		),
		// Featured Image Size
		array(
			'name'	=>	'feature_size_page',
			'title'	=>	__('Image Size', 'themify'),
			'description' => sprintf(__('Image sizes can be set at <a href="%s">Media Settings</a> and <a href="%s" target="_blank">Regenerated</a>', 'themify'), 'options-media.php', 'https://wordpress.org/plugins/regenerate-thumbnails/'),
			'type'		 =>	'featimgdropdown',
			'display_callback' => 'themify_is_image_script_disabled'
		),
		// Multi field: Image Dimension
		themify_image_dimensions_field(),
		// Hide Title
		array(
			'name' 		=> 'hide_title',
			'title'		=> __('Hide Post Title', 'themify'),
			'description'	=> '',
			'type' 		=> 'dropdown',
			'meta'		=> array(
				array('value' => 'default', 'name' => '', 'selected' => true),
				array('value' => 'yes', 'name' => __('Yes', 'themify')),
				array('value' => 'no',	'name' => __('No', 'themify'))
			),
			'default' => 'default',
		),
		// Unlink Post Title
		array(
			'name' 		=> 'unlink_title',
			'title' 		=> __('Unlink Post Title', 'themify'),
			'description' => __('Unlink post title (it will display the post title without link)', 'themify'),
			'type' 		=> 'dropdown',
			'meta'		=> array(
				array('value' => 'default', 'name' => '', 'selected' => true),
				array('value' => 'yes', 'name' => __('Yes', 'themify')),
				array('value' => 'no',	'name' => __('No', 'themify'))
			),
			'default' => 'default',
		),
		// Hide Post Date
		array(
			'name' 		=> 'hide_date',
			'title'		=> __('Hide Post Date', 'themify'),
			'description'	=> '',
			'type' 		=> 'dropdown',
			'meta'		=> array(
				array('value' => 'default', 'name' => '', 'selected' => true),
				array('value' => 'yes', 'name' => __('Yes', 'themify')),
				array('value' => 'no',	'name' => __('No', 'themify'))
			),
			'default' => 'default',
		),
		// Hide Post Meta
		themify_multi_meta_field(),
		// Hide Post Image
		array(
			'name' 		=> 'hide_image',
			'title' 		=> __('Hide Featured Image', 'themify'),
			'description' => '',
			'type' 		=> 'dropdown',
			'meta'		=> array(
				array('value' => 'default', 'name' => '', 'selected' => true),
				array('value' => 'yes', 'name' => __('Yes', 'themify')),
				array('value' => 'no',	'name' => __('No', 'themify'))
			),
			'default' => 'default',
		),
		// Unlink Post Image
		array(
			'name' 		=> 'unlink_image',
			'title' 		=> __('Unlink Featured Image', 'themify'),
			'description' => __('Display the Featured Image without link', 'themify'),
			'type' 		=> 'dropdown',
			'meta'		=> array(
				array('value' => 'default', 'name' => '', 'selected' => true),
				array('value' => 'yes', 'name' => __('Yes', 'themify')),
				array('value' => 'no',	'name' => __('No', 'themify'))
			),
			'default' => 'default',
		),
		// Page Navigation Visibility
		array(
			'name' 		=> 'hide_navigation',
			'title'		=> __('Hide Page Navigation', 'themify'),
			'description'	=> '',
			'type' 		=> 'dropdown',
			'meta'		=> array(
				array('value' => 'default', 'name' => '', 'selected' => true),
				array('value' => 'yes', 'name' => __('Yes', 'themify')),
				array('value' => 'no',	'name' => __('No', 'themify'))
			),
			'default' => 'default',
		),
	);
}



/**
 * Query Product Options
 * @param array $args
 * @return array
 * @since 1.0.0
 */
function themify_theme_query_product_meta_box($args = array()) {
	extract( $args );
	return array(
		// Query Category
		array(
			'name' 		=> 'product_query_category',
			'title'		=> __('Query Category', 'themify'),
			'description'	=> __('Select a category or enter multiple category IDs (eg. 2,5,6). Enter 0 to display all category.', 'themify'),
			'type'		=> 'query_category',
			'meta'		=> array('taxonomy' => 'product_cat')
		),
		array(
			'name' 		=> 'product_query_type',
			'title'		=> __('Type', 'themify'),
			'description'	=> '',
			'type'		=> 'dropdown',
			'meta'		=> array(
				array('name' => __('All', 'themify'), 'value' => 'all', 'selected' => true),
				array('name' => __('On Sale', 'themify'), 'value' => 'onsale'),
				array('name' => __('Free products', 'themify'), 'value' => 'free'),
				array('name' => __('Featured Products', 'themify'), 'value' => 'featured'),
			)
		),
		// Descending or Ascending Order for Posts
		array(
			'name' 		=> 'product_order',
			'title'		=> __('Order', 'themify'),
			'description'	=> '',
			'type'		=> 'dropdown',
			'meta'		=> array(
				array('name' => __('Descending', 'themify'), 'value' => 'desc', 'selected' => true),
				array('name' => __('Ascending', 'themify'), 'value' => 'asc')
			),
			'default' => 'desc'
		),
		// Criteria to Order By
		array(
			'name' 		=> 'product_orderby',
			'title'		=> __('Order By', 'themify'),
			'description'	=> '',
			'type'		=> 'dropdown',
			'meta'		=> array(
				array('name' => __('Date', 'themify'), 'value' => 'date', 'selected' => true),
				array('name' => __('Price', 'themify'), 'value' => 'price'),
				array('name' => __('Sales', 'themify'), 'value' => 'sales'),
				array('name' => __('Random', 'themify'), 'value' => 'rand'),
				array('name' => __('Author', 'themify'), 'value' => 'author'),
				array('name' => __('Post Title', 'themify'), 'value' => 'title'),
				array('name' => __('Comments Number', 'themify'), 'value' => 'comment_count'),
				array('name' => __('Modified Date', 'themify'), 'value' => 'modified'),
				array('name' => __('Post Slug', 'themify'), 'value' => 'name'),
				array('name' => __('Post ID', 'themify'), 'value' => 'ID'),
				array('name' => __( 'Custom Field String', 'themify' ), 'value' => 'meta_value'),
				array('name' => __( 'Custom Field Numeric', 'themify' ), 'value' => 'meta_value_num')
			),
			'default' => 'date',
			'hide' => 'date|rand|author|title|comment_count|modified|name|ID field-product-meta-key'
		),
		array(
			'name'			=> 'product_meta_key',
			'title'			=> __( 'Custom Field Key', 'themify' ),
			'description'	=> '',
			'type'			=> 'textbox',
			'meta'			=> array('size' => 'medium'),
			'class'			=> 'field-product-meta-key'
		),
		// Posts Per Page
		array(
			'name' 		=> 'product_posts_per_page',
			'title'		=> __('Products Per Page', 'themify'),
			'description'	=> '',
			'type'		=> 'textbox',
			'meta'		=> array('size' => 'small')
		),
		// Post Layout
		array(
			'name' 		=> 'product_layout',
			'title'		=> __('Product Layout', 'themify'),
			'description'	=> '',
			'type'		=> 'layout',
			'show_title' => true,
			'meta'		=> array(
				array(
					'value' => 'list-post',
					'img' => 'images/layout-icons/list-post.png',
					'selected' => true
				),
				array(
					'value' => 'grid4',
					'img'   => 'images/layout-icons/grid4.png',
					'title' => __( 'Grid 4', 'themify' )
				),
				array(
					'value' => 'grid3',
					'img'   => 'images/layout-icons/grid3.png',
					'title' => __( 'Grid 3', 'themify' )
				),
				array(
					'value' => 'grid2',
					'img'   => 'images/layout-icons/grid2.png',
					'title' => __( 'Grid 2', 'themify' )
				),
				array(
					'value' => 'auto_tiles', 
					'img' => 'images/layout-icons/auto-tiles.png', 
					'title' => __('Auto Tiles', 'themify')
				)
			),
			'default' => 'list-post',
		),
		// Product Content Style
		array(
			'name' 		=> 'product_content_layout',
			'title'		=> __( 'Product Content Style', 'themify' ),
			'description'	=> '',
			'type'		=> 'dropdown',
			'meta'		=> array(
				array(
					'value' => '',
					'name' => '',
					'selected' => true,
				),
				array(
					'value' => 'overlay',
					'name' => __( 'Overlay', 'themify' ),
				),
				array(
					'value' => 'polaroid',
					'name' => __( 'Polaroid', 'themify' ),
				),
				array(
					'value' => 'boxed',
					'name' => __( 'Boxed', 'themify' ),
				),
				array(
					'value' => 'flip',
					'name' => __( 'Flip', 'themify' ),
				)
			)
		),
		// Post Masonry
		array(
			'name' 		=> 'product_masonry',
			'title'		=> __( 'Masonry Layout', 'themify' ),
			'description'	=> '',
			'type'		=> 'dropdown',
			'meta'		=> array(
				array(
					'value' => '',
					'name' => '',
					'selected' => true,
				),
				array(
					'value' => 'yes',
					'name' => __( 'Enable', 'themify' ),
				),
				array(
					'value' => 'no',
					'name' => __( 'Disable', 'themify' ),
				)
			)
		),
		// Product Slider Hover
		array(
			'name' 		=> 'product_slider_hover',
			'title'		=> __( 'Product Hover Gallery', 'themify' ),
			'description'	=> '',
			'type'		=> 'dropdown',
			'meta'		=> array(
				array(
					'value' => '',
					'name' => '',
					'selected' => true,
				),
				array(
					'value' => 'enable',
					'name' => __( 'Enable', 'themify' ),
				),
				array(
					'value' => 'disable',
					'name' => __( 'Disable', 'themify' ),
				)
			)
		),
		// Post Gutter
		array(
			'name' 		=> 'product_gutter',
			'title'		=> __( 'Gutter Spacing', 'themify' ),
			'description'	=> '',
			'type'		=> 'dropdown',
			'meta'		=> array(
				array(
					'value' => '',
					'name' => '',
					'selected' => true,
				),
				array(
					'value' => 'no-gutter',
					'name' => __( 'No gutter', 'themify' ),
				)
			)
		),
		// Infinite Scroll
		array(
			'name' 		=> 'product_more_posts',
			'title'		=> __( 'Infinite Scroll', 'themify' ),
			'description'	=> '',
			'type'		=> 'dropdown',
			'meta'		=> array(
				array(
					'value' => '',
					'name' => '',
					'selected' => true,
				),
				array(
					'value' => 'infinite',
					'name' => __( 'Enable', 'themify' ),
				),
				array(
					'value' => 'pagination',
					'name' => __( 'Disable', 'themify' ),
				)
			)
		),
		array(
			'name' 		=> 'product_archive_show_short',
			'title'		=> __('Product Description', 'themify'),
			'description'	=> '',
			'type' 		=> 'dropdown',
			'meta'		=> array(
				array( 'value' => 'excerpt', 'name' => __( 'Short Description', 'themify' ), 'selected' => true ),
				array( 'value' => 'content', 'name' => __( 'Full Content', 'themify' ) ),
				array( 'value' => 'none', 	 'name' => __( 'None', 'themify' ) )
			)
		),
		// Multi field: Image Dimension
		themify_image_dimensions_field(array(),'product_image'),
		// Featured Image Size
		array(
			'name'	=>	'product_feature_size_page',
			'title'	=>	__('Image Size', 'themify'),
			'description' => sprintf(__('Image sizes can be set at <a href="%s">Media Settings</a> and <a href="%s" target="_blank">Regenerated</a>', 'themify'), 'options-media.php', 'https://wordpress.org/plugins/regenerate-thumbnails/'),
			'type'		 =>	'featimgdropdown',
			'display_callback' => 'themify_is_image_script_disabled'
		),
		array(
			'name' 		=> 'product_show_sorting_bar',
			'title'		=> __('Show Sorting Bar', 'themify'),
			'description'	=> '',
			'type' 		=> 'dropdown',
			'meta'		=> array(
				array('value' => '', 'name' => '', 'selected' => true),
				array('value' => 'no',	'name' => __('No', 'themify')),
				array('value' => 'yes', 'name' => __('Yes', 'themify')),
			)
		),
		// Page Navigation Visibility
		array(
			'name' 		=> 'product_hide_navigation',
			'title'		=> __('Hide Page Navigation', 'themify'),
			'description'	=> '',
			'type' 		=> 'dropdown',
			'meta'		=> array(
				array('value' => 'no',	'name' => __('No', 'themify'), 'selected' => true),
				array('value' => 'yes', 'name' => __('Yes', 'themify')),
			)
		),
		// Hide Title
		array(
			'name' 		=> 'product_hide_title',
			'title'		=> __('Hide Product Title', 'themify'),
			'description'	=> '',
			'type' 		=> 'dropdown',
			'meta'		=> array(
				array('value' => 'default', 'name' => '', 'selected' => true),
				array('value' => 'yes', 'name' => __('Yes', 'themify')),
				array('value' => 'no',	'name' => __('No', 'themify'))
			),
			'default' => 'default',
		),
		// Hide Price
		array(
			'name' 		=> 'product_hide_price',
			'title'		=> __('Hide Product Price', 'themify'),
			'description'	=> '',
			'type' 		=> 'dropdown',
			'meta'		=> array(
				array('value' => 'default', 'name' => '', 'selected' => true),
				array('value' => 'yes', 'name' => __('Yes', 'themify')),
				array('value' => 'no',	'name' => __('No', 'themify'))
			),
			'default' => 'default',
		),
		// Hide Quick Look
		array(
			'name' 		=> 'product_quick_look',
			'title'		=> __('Hide Quick Look', 'themify'),
			'description'	=> '',
			'type' 		=> 'dropdown',
			'meta'		=> array(
				array('value' => 'default', 'name' => '', 'selected' => true),
				array('value' => 'yes', 'name' => __('Yes', 'themify')),
				array('value' => 'no',	'name' => __('No', 'themify'))
			),
			'default' => 'default',
		),
		// Hide Product Share
		array(
			'name' 		=> 'product_social_share',
			'title'		=> __('Hide Product Share', 'themify'),
			'description'	=> '',
			'type' 		=> 'dropdown',
			'meta'		=> array(
				array('value' => 'default', 'name' => '', 'selected' => true),
				array('value' => 'yes', 'name' => __('Yes', 'themify')),
				array('value' => 'no',	'name' => __('No', 'themify'))
			),
			'default' => 'default',
		)
		
	);
}

/**
 * Portfolio Meta Box Options
 * @param array $args
 * @return array
 * @since 1.0.7
 */
function themify_theme_query_portfolio_meta_box($args = array()){
	extract( $args );
	return array(
		// Query Category
		array(
			'name' 		=> 'portfolio_query_category',
			'title'		=> __('Portfolio Category', 'themify'),
			'description'	=> __('Select a portfolio category or enter multiple portfolio category IDs (eg. 2,5,6). Enter 0 to display all portfolio categories.', 'themify'),
			'type'		=> 'query_category',
			'meta'		=> array('taxonomy' => 'portfolio-category')
		),
		// Descending or Ascending Order for Portfolios
		array(
			'name' 		=> 'portfolio_order',
			'title'		=> __('Order', 'themify'),
			'description'	=> '',
			'type'		=> 'dropdown',
			'meta'		=> array(
				array('name' => __('Descending', 'themify'), 'value' => 'desc', 'selected' => true),
				array('name' => __('Ascending', 'themify'), 'value' => 'asc')
			),
			'default' => 'desc',
		),
		// Criteria to Order By
		array(
			'name' 		=> 'portfolio_orderby',
			'title'		=> __('Order By', 'themify'),
			'description'	=> '',
			'type'		=> 'dropdown',
			'meta'		=> array(
				array('name' => __('Date', 'themify'), 'value' => 'date', 'selected' => true),
				array('name' => __('Random', 'themify'), 'value' => 'rand'),
				array('name' => __('Author', 'themify'), 'value' => 'author'),
				array('name' => __('Post Title', 'themify'), 'value' => 'title'),
				array('name' => __('Comments Number', 'themify'), 'value' => 'comment_count'),
				array('name' => __('Modified Date', 'themify'), 'value' => 'modified'),
				array('name' => __('Post Slug', 'themify'), 'value' => 'name'),
				array('name' => __('Post ID', 'themify'), 'value' => 'ID'),
				array('name' => __( 'Custom Field String', 'themify' ), 'value' => 'meta_value'),
				array('name' => __( 'Custom Field Numeric', 'themify' ), 'value' => 'meta_value_num')
			),
			'default' => 'date',
			'hide' => 'date|rand|author|title|comment_count|modified|name|ID field-portfolio-meta-key'
		),
		array(
			'name'			=> 'portfolio_meta_key',
			'title'			=> __( 'Custom Field Key', 'themify' ),
			'description'	=> '',
			'type'			=> 'textbox',
			'meta'			=> array('size' => 'medium'),
			'class'			=> 'field-portfolio-meta-key'
		),
		// Post Layout
		array(
			'name' 		=> 'portfolio_layout',
			'title'		=> __('Portfolio Layout', 'themify'),
			'description'	=> '',
			'type'		=> 'layout',
			'show_title' => true,
			'meta'		=> array(
				array(
					'value' => 'list-post',
					'img' => 'images/layout-icons/list-post.png',
					'selected' => true
				),
				array(
					'value' => 'grid4',
					'img' => 'images/layout-icons/grid4.png',
					'title' => __( 'Grid 4', 'themify' )
				),
				array(
					'value' => 'grid3',
					'img' => 'images/layout-icons/grid3.png',
					'title' => __( 'Grid 3', 'themify' )
				),
				array(
					'value' => 'grid2',
					'img' => 'images/layout-icons/grid2.png',
					'title' => __( 'Grid 2', 'themify' )
				),
				array(
					'value' => 'slider',
					'img'   => 'images/layout-icons/slider-default.png',
					'title' => __( 'Slider', 'themify' )
				),
			),
			'default' => 'list-post',
		),
		// Portfolio Content Layout
		array(
			'name' 		=> 'portfolio_post_content_layout',
			'title'		=> __( 'Portfolio Content Style', 'themify' ),
			'description'	=> '',
			'type'		=> 'dropdown',
			'meta'		=> array(
				array(
					'value' => '',
					'name' => '',
					'selected' => true,
				),
				array(
					'value' => 'overlay',
					'name' => __( 'Overlay', 'themify' ),
				),
				array(
					'value' => 'polaroid',
					'name' => __( 'Polaroid', 'themify' ),
				)
			)
		),
		// Post Masonry
		array(
			'name' 		=> 'portfolio_disable_masonry',
			'title'		=> __( 'Post Masonry', 'themify' ),
			'description'	=> '',
			'type'		=> 'dropdown',
			'meta'		=> array(
				array(
					'value' => '',
					'name' => '',
					'selected' => true,
				),
				array(
					'value' => 'yes',
					'name' => __( 'Enable', 'themify' ),
				),
				array(
					'value' => 'no',
					'name' => __( 'Disable', 'themify' ),
				)
			)
		),
		// Post Gutter
		array(
			'name' 		=> 'portfolio_post_gutter',
			'title'		=> __( 'Post Gutter', 'themify' ),
			'description'	=> '',
			'type'		=> 'dropdown',
			'meta'		=> array(
				array(
					'value' => '',
					'name' => '',
					'selected' => true,
				),
				array(
					'value' => 'no-gutter',
					'name' => __( 'No gutter', 'themify' ),
				)
			)
		),
		// Infinite Scroll
		array(
			'name' 		=> 'portfolio_more_posts',
			'title'		=> __( 'Infinite Scroll', 'themify' ),
			'description'	=> '',
			'type'		=> 'dropdown',
			'meta'		=> array(
				array(
					'value' => '',
					'name' => '',
					'selected' => true,
				),
				array(
					'value' => 'infinite',
					'name' => __( 'Enable', 'themify' ),
				),
				array(
					'value' => 'pagination',
					'name' => __( 'Disable', 'themify' ),
				)
			)
		),
		// Posts Per Page
		array(
			  'name' 		=> 'portfolio_posts_per_page',
			  'title'		=> __('Portfolios Per Page', 'themify'),
			  'description'	=> '',
			  'type'		=> 'textbox',
			  'meta'		=> array('size' => 'small')
			),

		// Display Content
		array(
			'name' 		=> 'portfolio_display_content',
			'title'		=> __('Display Content', 'themify'),
			'description'	=> '',
			'type'		=> 'dropdown',
			'meta'		=> array(
				array( 'name' => __('Full Content', 'themify'), 'value' => 'content' ),
				array( 'name' => __('Excerpt', 'themify'), 'value' => 'excerpt', 'selected' => true ),
				array( 'name' => __('None', 'themify'), 'value' => 'none' )
			),
			'default' => 'excerpt',
		),
		// Featured Image Size
		array(
			'name'	=>	'portfolio_feature_size_page',
			'title'	=>	__('Image Size', 'themify'),
			'description' => __('Image sizes can be set at <a href="options-media.php">Media Settings</a> and <a href="https://wordpress.org/plugins/regenerate-thumbnails/" target="_blank">Regenerated</a>', 'themify'),
			'type'		 =>	'featimgdropdown',
			'display_callback' => 'themify_is_image_script_disabled'
			),

		// Multi field: Image Dimension
		array(
			'type' => 'multi',
			'name' => '_portfolio_image_dimensions',
			'title' => __('Image Dimensions', 'themify'),
			'meta' => array(
				'fields' => array(
					// Image Width
					array(
					  'name' 		=> 'portfolio_image_width',
					  'label' => __('width', 'themify'),
					  'description' => '',
					  'type' 		=> 'textbox',
					  'meta'		=> array('size'=>'small')
					),
					// Image Height
					array(
					  'name' 		=> 'portfolio_image_height',
					  'label' => __('height', 'themify'),
					  'description' => '',
					  'type' 		=> 'textbox',
					  'meta'		=> array('size'=>'small')
					),
				),
				'description' => __('Enter height = 0 to disable vertical cropping with img.php enabled', 'themify'),
				'before' => '',
				'after' => '',
				'separator' => ''
			)
		),
		// Hide Title
		array(
			  'name' 		=> 'portfolio_hide_title',
			  'title'		=> __('Hide Portfolio Title', 'themify'),
			  'description'	=> '',
			  'type' 		=> 'dropdown',
			  'meta'		=> array(
				array('value' => 'default', 'name' => '', 'selected' => true),
				array('value' => 'yes', 'name' => __('Yes', 'themify')),
				array('value' => 'no',	'name' => __('No', 'themify'))
			),
			'default' => 'default',
		),
		// Unlink Post Title
		array(
			  'name' 		=> 'portfolio_unlink_title',
			  'title' 		=> __('Unlink Portfolio Title', 'themify'),
			  'description' => __('Unlink portfolio title (it will display the post title without link)', 'themify'),
			  'type' 		=> 'dropdown',
			  'meta'		=> array(
				array('value' => 'default', 'name' => '', 'selected' => true),
				array('value' => 'yes', 'name' => __('Yes', 'themify')),
				array('value' => 'no',	'name' => __('No', 'themify'))
			),
			'default' => 'default',
		),
		// Hide Post Meta
		array(
			'name' 		=> 'portfolio_hide_meta_all',
			'title' 	=> __('Hide Portfolio Meta', 'themify'),
			'description' => '',
			'type' 		=> 'dropdown',
			'meta'		=> array(
				array('value' => 'default', 'name' => '', 'selected' => true),
				array('value' => 'yes', 'name' => __('Yes', 'themify')),
				array('value' => 'no',	'name' => __('No', 'themify'))
			),
			'default' => 'default',
		),
		// Hide Post Image
		array(
			  'name' 		=> 'portfolio_hide_image',
			  'title' 		=> __('Hide Featured Image', 'themify'),
			  'description' => '',
			  'type' 		=> 'dropdown',
			  'meta'		=> array(
				array('value' => 'default', 'name' => '', 'selected' => true),
				array('value' => 'yes', 'name' => __('Yes', 'themify')),
				array('value' => 'no',	'name' => __('No', 'themify'))
			),
			'default' => 'default',
		),
		// Unlink Post Image
		array(
			  'name' 		=> 'portfolio_unlink_image',
			  'title' 		=> __('Unlink Featured Image', 'themify'),
			  'description' => __('Display the Featured Image Without Link', 'themify'),
			  'type' 		=> 'dropdown',
			  'meta'		=> array(
				array('value' => 'default', 'name' => '', 'selected' => true),
				array('value' => 'yes', 'name' => __('Yes', 'themify')),
				array('value' => 'no',	'name' => __('No', 'themify'))
			),
			'default' => 'default',
		),
		// Page Navigation Visibility
		array(
			  'name' 		=> 'portfolio_hide_navigation',
			  'title'		=> __('Hide Page Navigation', 'themify'),
			  'description'	=> '',
			  'type' 		=> 'dropdown',
			  'meta'		=> array(
				array('value' => 'default', 'name' => '', 'selected' => true),
				array('value' => 'yes', 'name' => __('Yes', 'themify')),
				array('value' => 'no',	'name' => __('No', 'themify'))
			),
			'default' => 'default',
		)
	);
}

/**
 * Theme Appearance Tab for Themify Custom Panel
 *
 * @since 1.0.0
 *
 * @param array $args
 *
 * @return array
 */
function themify_theme_page_theme_design_meta_box( $args = array() ) {
	return array(
		
		// Header Design
		array(
			'name'        => 'header_design',
			'title'       => __( 'Header Design', 'themify' ),
			'description' => '',
			'type'        => 'layout',
			'show_title'  => true,
			'meta'        => $args['header_design_options'],
			'hide'		  => 'none header-left-pane header-minbar-left header-minbar-right header-boxed-content header-right-pane',
			'default' => 'default',
		),
		// Sticky Header
		array(
			'name'        => 'fixed_header',
			'title'       => __( 'Sticky Header', 'themify' ),
			'description' => '',
			'type'		  => 'radio',
			'meta'		  => themify_ternary_options(),
			'class'		  => 'hide-if none header-overlay header-slide-left header-slide-right header-boxed-content header-left-pane header-right-pane header-minbar-left header-minbar-right',
			'default' => 'default',
		),
		// Full Height Header
		array(
			'name'        => 'full_height_header',
			'title'       => __( 'Full Height Header', 'themify' ),
			'description' => __( 'Full height will display the container in 100% viewport height', 'themify' ),
			'type'		  => 'radio',
			'meta'		  => themify_ternary_options(),
			'class'		  => 'hide-if default none header-logo-left header-left-pane header-minbar-left header-minbar-right header-slide-left header-slide-right header-top-bar header-boxed-layout header-boxed-content header-right-pane header-stripe header-logo-center header-overlay',
			'default' => 'default',
		),
		// Header Elements
		array(
			'name' 	=> '_multi_header_elements',	
			'title' => __('Header Elements', 'themify'), 	
			'description' => '',	
			'type' 	=> 'multi',
			'class' => 'hide-if none',		
			'meta'	=> array(
				'fields' => array(
					// Show Site Logo
					array(
						'name' 	=> 'exclude_site_logo',
						'description' => '',
						'title' => __( 'Site Logo', 'themify' ),
						'type' 	=> 'dropdownbutton',
						'states' => themify_ternary_states( array(
							'icon_no' => THEMIFY_URI . '/img/ddbtn-check.png',
							'icon_yes' => THEMIFY_URI . '/img/ddbtn-cross.png', 
							) ),
						'class' => 'hide-if none header-menu-split',
						'after' => '<div class="clear"></div>',
					),
					// Show Site Tagline
					array(
						'name' 	=> 'exclude_site_tagline',
						'description' => '',
						'title' => __( 'Site Tagline', 'themify' ),
						'type' 	=> 'dropdownbutton',
						'states' => themify_ternary_states( array(
							'icon_no' => THEMIFY_URI . '/img/ddbtn-check.png',
							'icon_yes' => THEMIFY_URI . '/img/ddbtn-cross.png', 
							) ),
						'class' => 'hide-if none',
						'after' => '<div class="clear"></div>',
					),
					// Show Search Button
					array(
						'name' 	=> 'exclude_search_button',
						'description' => '',
						'title' => __( 'Search Button', 'themify' ),
						'type' 	=> 'dropdownbutton',
						'states' => themify_ternary_states( array(
							'icon_no' => THEMIFY_URI . '/img/ddbtn-check.png',
							'icon_yes' => THEMIFY_URI . '/img/ddbtn-cross.png', 
							) ),
						'class' => 'hide-if none',
						'after' => '<div class="clear"></div>',
					),
					// Show Cart Icon
					array(
						'name'	=> 'exclude_cart',
						'description' => '',
						'title' => __( 'Cart Icon', 'themify' ),
						'type' 	=> 'dropdownbutton',
						'states' => themify_ternary_states( array(
							'icon_no' => THEMIFY_URI . '/img/ddbtn-check.png',
							'icon_yes' => THEMIFY_URI . '/img/ddbtn-cross.png', 
							) ),
						'class' => 'hide-if none',
						'after' => '<div class="clear"></div>',
						'display_callback' => 'themify_is_woocommerce_active'
					),
					// Show Wishlist Icon
					array(
						'name' 	=> 'exclude_wishlist',
						'description' => '',
						'title' => __( 'Wishlist Icon', 'themify' ),
						'type' 	=> 'dropdownbutton',
						'states' => themify_ternary_states( array(
							'icon_no' => THEMIFY_URI . '/img/ddbtn-check.png',
							'icon_yes' => THEMIFY_URI . '/img/ddbtn-cross.png', 
							) ),
						'class' => 'hide-if none',
						'after' => '<div class="clear"></div>',
						'display_callback' => 'themify_is_woocommerce_active'
					),
					// Show Icon Menu Links
					array(
						'name' 	=> 'exclude_icon_menu_links',
						'description' => '',
						'title' => __( 'Icon Menu Links', 'themify' ),
						'type' 	=> 'dropdownbutton',
						'states' => themify_ternary_states( array(
							'icon_no' => THEMIFY_URI . '/img/ddbtn-check.png',
							'icon_yes' => THEMIFY_URI . '/img/ddbtn-cross.png', 
							) ),
						'class' => 'hide-if none',
						'after' => '<div class="clear"></div>',
					),
					// Show Menu Navigation
					array(
						'name' 	=> 'exclude_menu_navigation',
						'description' => '',
						'title' => __( 'Menu Navigation', 'themify' ),
						'type' 	=> 'dropdownbutton',
						'states' => themify_ternary_states( array(
							'icon_no' => THEMIFY_URI . '/img/ddbtn-check.png',
							'icon_yes' => THEMIFY_URI . '/img/ddbtn-cross.png', 
							) ),
						'class' => 'hide-if none header-menu-split',
						'after' => '<div class="clear"></div>',
						'enable_toggle' => true
					),
					// Show Top Bar Widgets
					array(
						'name' 	=> 'exclude_top_bar_widgets',
						'description' => '',
						'title' => __( 'Top Bar Widgets', 'themify' ),
						'type' 	=> 'dropdownbutton',
						'states' => themify_ternary_states( array(
							'icon_no' => THEMIFY_URI . '/img/ddbtn-check.png',
							'icon_yes' => THEMIFY_URI . '/img/ddbtn-cross.png', 
							) ),
						'class' => 'hide-if none',
						'after' => '<div class="clear"></div>',
					),
				),
				'description' => '',
				'before' => '',
				'after' => '<div class="clear"></div>',
				'separator' => ''
			)
		),
		array(
			'name'		=> 'mobile_menu_styles',
			'title'		=> __('Mobile Menu Style', 'themify'),
			'type'		=> 'dropdown',
			'meta'		=> array(
				array( 'name' => __( 'Default', 'themify' ), 'value' => 'default' ),
				array( 'name' => __( 'Boxed', 'themify' ), 'value' => 'boxed' ),
				array( 'name' => __( 'Dropdown', 'themify' ), 'value' => 'dropdown' ),
				array( 'name' => __( 'Fade Overlay', 'themify' ), 'value' => 'fade-overlay' ),
				array( 'name' => __( 'Fadein Down', 'themify' ), 'value' => 'fadein-down' ),
				array( 'name' => __( 'Flip Down', 'themify' ), 'value' => 'flip-down' ),
				array( 'name' => __( 'FlipIn Left', 'themify' ), 'value' => 'flipin-left' ),
				array( 'name' => __( 'FlipIn Right', 'themify' ), 'value' => 'flipin-right' ),
				array( 'name' => __( 'Flip from Left', 'themify' ), 'value' => 'flip-from-left' ),
				array( 'name' => __( 'Flip from Right', 'themify' ), 'value' => 'flip-from-right' ),
				array( 'name' => __( 'Flip from Top', 'themify' ), 'value' => 'flip-from-top' ),
				array( 'name' => __( 'Flip from Bottom', 'themify' ), 'value' => 'flip-from-bottom' ),
				array( 'name' => __( 'Morphing', 'themify' ), 'value' => 'morphing' ),
				array( 'name' => __( 'Overlay ZoomIn', 'themify' ), 'value' => 'overlay-zoomin' ),
				array( 'name' => __( 'Overlay ZoomIn Right', 'themify' ), 'value' => 'overlay-zoomin-right' ),
				array( 'name' => __( 'Rotate ZoomIn', 'themify' ), 'value' => 'rotate-zoomin' ),
				array( 'name' => __( 'Slide Down', 'themify' ), 'value' => 'slide-down' ),
				array( 'name' => __( 'SlideIn Left', 'themify' ), 'value' => 'slidein-left' ),
				array( 'name' => __( 'SlideIn Right', 'themify' ), 'value' => 'slidein-right' ),
				array( 'name' => __( 'Split', 'themify' ), 'value' => 'split' ),
				array( 'name' => __( 'Swing Left to Right', 'themify' ), 'value' => 'swing-left-to-right' ),
				array( 'name' => __( 'Swing Right to Left', 'themify' ), 'value' => 'swing-right-to-left' ),
				array( 'name' => __( 'Swing Top to Bottom', 'themify' ), 'value' => 'swing-top-to-bottom' ),
				array( 'name' => __( 'Swipe Left', 'themify' ), 'value' => 'swipe-left' ),
				array( 'name' => __( 'Swipe Right', 'themify' ), 'value' => 'swipe-right' ),
				array( 'name' => __( 'Zoom Down', 'themify' ), 'value' => 'zoomdown' ),
			),
		),
		// Cart Style
		array(
			'name'          => 'cart_style',
			'title'         => __( 'Ajax Cart Style', 'themify' ),
			'description'   => '',
			'type'          => 'radio',
			'show_title'    => true,
			'meta'          => array(
				array(
					'value'    => '',
					'name'     => __( 'Default', 'themify' ),
					'selected' => true
				),
				array(
					'value' => 'dropdown',
					'name'  => __( 'Dropdown cart', 'themify' ),
				),
				array(
					'value' => 'slide-out',
					'name' => __( 'Slide-out cart', 'themify' ),
				),
				array(
					'value' => 'link_to_cart',
					'name' => __( 'Link to cart page', 'themify' ),
				),
			),
			'display_callback' => 'themify_is_woocommerce_active',
			'enable_toggle' => true,
			'class'     => 'hide-if none clear',
			'default' => '',
		),
		// Header Wrap
		array(
			'name'          => 'header_wrap',
			'title'         => __( 'Header Background Type', 'themify' ),
			'description'   => '',
			'type'          => 'radio',
			'show_title'    => true,
			'meta'          => array(
				array(
					'value'    => 'solid',
					'name'     => __( 'Solid Color/Image', 'themify' ),
					'selected' => true
				),
				array(
					'value' => 'transparent',
					'name'  => __( 'Transparent Header', 'themify' ),
				),
				array(
					'value' => 'slider',
					'name' => __( 'Image Slider', 'themify' ),
				),
				array(
					'value' => 'video',
					'name' => __( 'Video Background', 'themify' ),
				),
			),
			'enable_toggle' => true,
			'class'     => 'hide-if none clear',
			'default' => 'solid',
		),
		// Select Background Gallery
		array(
			'name' 		=> 'background_gallery',
			'title'		=> __('Header Slider', 'themify'),
			'description' => '',
			'type' 		=> 'gallery_shortcode',
			'toggle'	=> 'slider-toggle',
			'class'     => 'hide-if none',
		),
		// Background Mode
		array(
			'name'		=> 'background_mode',
			'title'		=> __('Slider Mode', 'themify'),
			'type'		=> 'radio',
			'meta'		=> array(
				array('value' => 'fullcover', 'selected' => $args['background_mode']=='fullcover', 'name' => __('Full Cover', 'themify')),
				array('value' => 'best-fit', 'selected' => $args['background_mode']=='best-fit', 'name' => __('Best Fit', 'themify'))
			),
			'enable_toggle'	=> true,
			'toggle'	=> 'enable_toggle_child slider-toggle',
			'class'     => 'hide-if none',
			'default' => 'fullcover',
		),
		// Background Position
		array(
			'name'		=> 'background_position',
			'title'		=> __('Slider Image Position', 'themify'),
			'type'		=> 'dropdown',
			'meta'		=> array(
				array('value' => '', 'name' => '', 'selected' => true),
				array('value' => 'left-top', 'name' => __('Left Top', 'themify')),
				array('value' => 'left-center', 'name' => __('Left Center', 'themify')),
				array('value' => 'left-bottom', 'name' => __('Left Bottom', 'themify')),
				array('value' => 'right-top', 'name' => __('Right Top', 'themify')),
				array('value' => 'right-center', 'name' => __('Right Center', 'themify')),
				array('value' => 'right-bottom', 'name' => __('Right Bottom', 'themify')),
				array('value' => 'center-top', 'name' => __('Center Top', 'themify')),
				array('value' => 'center-center', 'name' => __('Center Center', 'themify')),
				array('value' => 'center-bottom', 'name' => __('Center Bottom', 'themify'))
			),
			'toggle'	=> 'fullcover-toggle slider-toggle',
			'class'     => 'hide-if none',
		),
		array(
			'type' => 'multi',
			'name' => '_video_select',
			'title' => __('Header Video', 'themify'),
			'meta' => array(
				'fields' => array(
					// Video File
					array(
						'name' 		=> 'video_file',
						'title' 	=> __('Video File', 'themify'),
						'description' => '',
						'type' 		=> 'video',
						'meta'		=> array(),
					),
				),
				'description' => __('Video format: mp4. Note: video background does not play on some mobile devices, background image will be used as fallback.', 'themify'),
				'before' => '',
				'after' => '',
				'separator' => ''
			),
			'toggle'	=> 'video-toggle',
			'class'     => 'hide-if none',
		),
		// Background Color
		array(
			'name'        => 'background_color',
			'title'       => __( 'Header Background', 'themify' ),
			'description' => '',
			'type'        => 'color',
			'meta'        => array( 'default' => null ),
			'toggle'      => array( 'solid-toggle', 'slider-toggle', 'video-toggle' ),
			'class'     => 'hide-if none',
			'format'      => 'rgba',
		),
		// Background image
		array(
			'name'        => 'background_image',
			'title'       => '',
			'type'        => 'image',
			'description' => '',
			'meta'        => array(),
			'before'      => '',
			'after'       => '',
			'toggle'      => array( 'solid-toggle', 'video-toggle' ),
			'class'     => 'hide-if none',
		),
		// Background repeat
		array(
			'name'        => 'background_repeat',
			'title'       => '',
			'description' => __( 'Background Image Mode', 'themify' ),
			'type'        => 'dropdown',
			'meta'        => array(
				array(
					'value' => 'fullcover',
					'name'  => __( 'Fullcover', 'themify' )
				),
				array(
					'value' => 'repeat',
					'name'  => __( 'Repeat all', 'themify' )
				),
				array(
					'value' => 'no-repeat',
					'name'  => __( 'No repeat', 'themify' )
				),
				array(
					'value' => 'repeat-x',
					'name'  => __( 'Repeat horizontally', 'themify' )
				),
				array(
					'value' => 'repeat-y',
					'name'  => __( 'Repeat vertically', 'themify' )
				),
			),
			'toggle'      => array( 'solid-toggle', 'video-toggle' ),
			'class'     => 'hide-if none',
			'default' => 'fullcover',
		),
		// Header Slider Auto
		array(
			'name' 		=> 'background_auto',
			'title'		=> __('Autoplay', 'themify'),
			'description'	=> '',
			'type' 		=> 'dropdown',
			'meta'		=> array(
				array('value' => 'yes', 'name' => __('Yes', 'themify'), 'selected' => true),
				array('value' => 'no', 'name' => __('No', 'themify'))
			),
			'toggle'	=> 'slider-toggle',
			'default' => 'yes',
		),
		// Header Slider Auto Timeout
		array(
			'name' 		=> 'background_autotimeout',
			'title'		=> __('Autoplay Timeout', 'themify'),
			'description'	=> '',
			'type' 		=> 'dropdown',
			'meta'		=> array(
				array('value' => 1, 'name' => __('1 Secs', 'themify')),
				array('value' => 2, 'name' => __('2 Secs', 'themify')),
				array('value' => 3, 'name' => __('3 Secs', 'themify')),
				array('value' => 4, 'name' => __('4 Secs', 'themify')),
				array('value' => 5, 'name' => __('5 Secs', 'themify'), 'selected' => true),
				array('value' => 6, 'name' => __('6 Secs', 'themify')),
				array('value' => 7, 'name' => __('7 Secs', 'themify')),
				array('value' => 8, 'name' => __('8 Secs', 'themify')),
				array('value' => 9, 'name' => __('9 Secs', 'themify')),
				array('value' => 10, 'name' => __('10 Secs', 'themify'))
			),
			'toggle'	=> 'slider-toggle',
			'default' => 5,
		),
		// Header Slider Transition Speed
		array(
			'name' 		=> 'background_speed',
			'title'		=> __('Transition Speed', 'themify'),
			'description'	=> '',
			'type' 		=> 'dropdown',
			'meta'		=> array(
				array('value' => 1500, 'name' => __('Slow', 'themify')),
				array('value' => 500, 'name' => __('Normal', 'themify'), 'selected' => true),
				array('value' => 300, 'name' => __('Fast', 'themify'))
			),
			'toggle'	=> 'slider-toggle',
			'default' => 500,
		),
		// Header Slider Wrap
		array(
			'name' 		=> 'background_wrap',
			'title'		=> __('Wrap Slides', 'themify'),
			'description'	=> '',
			'type' 		=> 'dropdown',
			'meta'		=> array(
				array('value' => 'yes', 'name' => __('Yes', 'themify'), 'selected' => true),
				array('value' => 'no', 'name' => __('No', 'themify'))
			),
			'toggle'	=> 'slider-toggle',
			'default' => 'yes',
		),
		// Header wrap text color
		array(
			'name'        => 'headerwrap_text_color',
			'title'       => __( 'Header Text Color', 'themify' ),
			'description' => '',
			'type'        => 'color',
			'meta'        => array( 'default' => null ),
			'class'     => 'hide-if none',
			'format'      => 'rgba',
		),
		// Header wrap link color
		array(
			'name'        => 'headerwrap_link_color',
			'title'       => __( 'Header Link Color', 'themify' ),
			'description' => '',
			'type'        => 'color',
			'meta'        => array( 'default' => null ),
			'class'     => 'hide-if none',
			'format'      => 'rgba',
		),
		// Footer Separator
		array(
			'name' => 'footer_separator',
			'type' => 'separator',
			'meta' => array('html'=>'<hr />')
		),
		// Footer Design
		array(
			'name'        => 'footer_design',
			'title'       => __( 'Footer Design', 'themify' ),
			'description' => '',
			'type'        => 'layout',
			'show_title'  => true,
			'meta'        => $args['footer_design_options'],
			'hide'		  => 'none',
			'default' => 'default',
		),  
		// Footer Elements
		array(
			'name' 	=> '_multi_footer_elements',	
			'title' => __('Footer Elements', 'themify'), 	
			'description' => '',	
			'type' 	=> 'multi',
			'class' => 'hide-if none',		
			'meta'	=> array(
				'fields' => array(
					// Show Site Logo
					array(
						'name' 	=> 'exclude_footer_site_logo',
						'description' => '',
						'title' => __( 'Site Logo', 'themify' ),
						'type' 	=> 'dropdownbutton',
						'states' => themify_ternary_states( array(
							'icon_no' => THEMIFY_URI . '/img/ddbtn-check.png',
							'icon_yes' => THEMIFY_URI . '/img/ddbtn-cross.png', 
							) ),
						'class' => 'hide-if none',
						'after' => '<div class="clear"></div>',
					),
					// Show Footer Widgets
					array(
						'name' 	=> 'exclude_footer_widgets',
						'description' => '',
						'title' => __( 'Footer Widgets', 'themify' ),
						'type' 	=> 'dropdownbutton',
						'states' => themify_ternary_states( array(
							'icon_no' => THEMIFY_URI . '/img/ddbtn-check.png',
							'icon_yes' => THEMIFY_URI . '/img/ddbtn-cross.png', 
							) ),
						'class' => 'hide-if none',
						'after' => '<div class="clear"></div>',
					),
					// Show Texts
					array(
						'name' 	=> 'exclude_footer_texts',
						'description' => '',
						'title' => __( 'Footer Text', 'themify' ),
						'type' 	=> 'dropdownbutton',
						'states' => themify_ternary_states( array(
							'icon_no' => THEMIFY_URI . '/img/ddbtn-check.png',
							'icon_yes' => THEMIFY_URI . '/img/ddbtn-cross.png', 
							) ),
						'class' => 'hide-if none',
						'after' => '<div class="clear"></div>',
					),
					// Show Back to Top
					array(
						'name' 	=> 'exclude_footer_back',
						'description' => '',
						'title' => __( 'Back to Top Arrow', 'themify' ),
						'type' 	=> 'dropdownbutton',
						'states' => themify_ternary_states( array(
							'icon_no' => THEMIFY_URI . '/img/ddbtn-check.png',
							'icon_yes' => THEMIFY_URI . '/img/ddbtn-cross.png', 
							) ),
						'class' => 'hide-if none',
						'after' => '<div class="clear"></div>',
					),
				),
				'description' => '',
				'before' => '',
				'after' => '<div class="clear"></div>',
				'separator' => ''
			)
		),
		
		// Footer widget position
		array(
			'name'        => 'footer_widget_position',
			'title'       => __( 'Footer Widgets Position', 'themify' ),
			'class' => 'hide-if none',
			'description' => '',
			'type'        => 'dropdown',
			'meta'        => array(
				array(
					'value' => '',
					'name'  => __( 'Default', 'themify' )
				),
				array(
					'value' => 'bottom',
					'name'  => __( 'After Footer Text', 'themify' )
				),
				array(
					'value' => 'top',
					'name'  => __( 'Before Footer Text', 'themify' )
				)
			),
		),
		// Image Filter Separator
		array(
			'name' => 'image_filter_separator',
			'type' => 'separator',
			'meta' => array('html'=>'<hr />')
		),
		// Image Filter
		array(
			'name'        => 'imagefilter_options',
			'title'       => __( 'Image Filter', 'themify' ),
			'description' => '',
			'type'        => 'dropdown',
			'meta'        => array(
				array( 'name' => '', 'value' => 'initial' ),
				array( 'name' => __( 'None', 'themify' ), 'value' => 'none' ),
				array( 'name' => __( 'Grayscale', 'themify' ), 'value' => 'grayscale' ),
				array( 'name' => __( 'Sepia', 'themify' ), 'value' => 'sepia' ),
				array( 'name' => __( 'Blur', 'themify' ), 'value' => 'blur' ),
			),
			'default' => 'initial',
		),
		// Image Hover Filter
		array(
			'name'        => 'imagefilter_options_hover',
			'title'       => __( 'Image Hover Filter', 'themify' ),
			'description' => '',
			'type'        => 'dropdown',
			'meta'        => array(
				array( 'name' => '', 'value' => 'initial' ),
				array( 'name' => __( 'None', 'themify' ), 'value' => 'none' ),
				array( 'name' => __( 'Grayscale', 'themify' ), 'value' => 'grayscale' ),
				array( 'name' => __( 'Sepia', 'themify' ), 'value' => 'sepia' ),
				array( 'name' => __( 'Blur', 'themify' ), 'value' => 'blur' ),
			),
			'default' => 'initial',
		),
		// Image Filter Apply To
		array(
			'name'        => 'imagefilter_applyto',
			'title'       => __( 'Apply Filter To', 'themify' ),
			'description' => sprintf( __( 'Image filters can be set site-wide at <a href="%s" target="_blank">Themify > Settings > Theme Settings</a>', 'themify' ), admin_url( 'admin.php?page=themify#setting-theme_settings' ) ),
			'type'        => 'radio',
			'meta'        => array(
				#array( 'value' => 'initial', 'name' => __( 'Theme Default', 'themify' )),
				array( 'value' => 'all', 'name' => __( 'All Images', 'themify' ), 'selected' => true  ),
				array( 'value' => 'featured-only', 'name' => __( 'Featured Images Only', 'themify' ), )
			),
			'default' => 'all',
		),

	);
}

/**
 * Default Page Layout Module
 * @param array $data Theme settings data
 * @return string Markup for module.
 * @since 1.0.0
 */
function themify_default_page_layout($data = array()){
	$data = themify_get_data();

	/**
	 * Theme Settings Option Key Prefix
	 * @var string
	 */
	$prefix = 'setting-default_page_';

	/**
	 * Sidebar placement options
	 * @var array
	 */
	$sidebar_location_options = array(
		array('value' => 'sidebar1', 'img' => 'images/layout-icons/sidebar1.png', 'selected' => true, 'title' => __('Sidebar Right', 'themify')),
		array('value' => 'sidebar1 sidebar-left', 'img' => 'images/layout-icons/sidebar1-left.png', 'title' => __('Sidebar Left', 'themify')),
		array('value' => 'sidebar-none', 'img' => 'images/layout-icons/sidebar-none.png', 'title' => __('No Sidebar', 'themify'))
	);

	/**
	 * Tertiary options <blank>|yes|no
	 * @var array
	 */
	$default_options = array(
		array('name' => '', 'value' => ''),
		array('name' => __('Yes', 'themify'), 'value' => 'yes'),
		array('name' => __('No', 'themify'), 'value' => 'no')
	);

	/**
	 * Module markup
	 * @var string
	 */
	$output = '';

	/**
	 * Page sidebar placement
	 */
	$output .= '<p>
					<span class="label">' . __('Page Sidebar Option', 'themify') . '</span>';
	$val = isset( $data[$prefix.'layout'] ) ? $data[$prefix.'layout'] : '';
	foreach ( $sidebar_location_options as $option ) {
		if ( ( '' == $val || ! $val || ! isset( $val ) ) && ( isset( $option['selected'] ) && $option['selected'] ) ) {
			$val = $option['value'];
		}
		if ( $val == $option['value'] ) {
			$class = "selected";
		} else {
			$class = "";
		}
		$output .= '<a href="#" class="preview-icon '.$class.'" title="'.$option['title'].'"><img src="'.THEME_URI.'/'.$option['img'].'" alt="'.$option['value'].'"  /></a>';
	}
	$output .= '<input type="hidden" name="'.$prefix.'layout" class="val" value="'.$val.'" /></p>';

	/**
	 * Hide Title in All Pages
	 */
	$output .= '<p>
					<span class="label">' . __('Hide Title in All Pages', 'themify') . '</span>
					<select name="setting-hide_page_title">'.
						themify_options_module($default_options, 'setting-hide_page_title') . '
					</select>
				</p>';

	/**
	 * Hide Feauted images in All Pages
	 */
	$output .= '<p>
					<span class="label">' . __('Hide Featured Image', 'themify') . '</span>
					<select name="setting-hide_page_image">' .
						themify_options_module($default_options, 'setting-hide_page_image') . '
					</select>
				</p>';

	/**
	 * Page Comments
	 */
	$pre = 'setting-comments_pages';
	$output .= '<p><span class="label">' . __('Page Comments', 'themify') . '</span><label for="'.$pre.'"><input type="checkbox" id="'.$pre.'" name="'.$pre.'" ' . checked( themify_get( $pre ), 'on', false ) . ' /> ' . __('Disable comments in all Pages', 'themify') . '</label></p>';

	return $output;
}