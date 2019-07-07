<?php
/**
 * Main Themify class
 * @package themify
 * @since 1.0.0
 */

class Themify {
	/** Default sidebar layout
	 * @var string */
	public $layout;
	/** Default posts layout
	 * @var string */
	public $post_layout;
	public $post_layout_type = 'default';
	public $hide_title;
	public $hide_meta;
	public $hide_meta_author;
	public $hide_meta_category;
	public $hide_meta_comment;
	public $hide_meta_tag;
	public $hide_date;
	public $hide_image;
	public $media_position;
        
	public $unlink_title;
	public $unlink_image;
	
	public $display_content = '';
	public $auto_featured_image;
	
	public $width = '';
	public $height = '';
	
	public $avatar_size = 96;
	public $page_navigation;
	public $posts_per_page;
	
	public $image_align = '';
	public $image_setting = '';
	
	public $page_id = '';
	public $page_image_width = 1160;
	public $query_category = '';
	public $query_post_type = '';
	public $query_taxonomy = '';
	public $paged = '';
	public $query_all_post_types;

	////////////////////////////////////////////
	// Product Variables
	////////////////////////////////////////////
	public $query = '';
	public $query_products = '';
	public $products_per_page = '';
	public $product_category = '';
	public $query_products_field = '';
	public $is_related_loop = false;
	public $is_single_product_main = false;
	public $product_archive_show_short = '';
	public $hide_product_title = 'no';
	public $unlink_product_title = 'no';
	public $hide_product_image = 'no';
	public $unlink_product_image = 'no';
	public $builder_args = array();//for builder modules

	/////////////////////////////////////////////
	// Set Default Image Sizes 					
	/////////////////////////////////////////////
	
	// Default Index Layout
	static $content_width = 1160;
	static $sidebar1_content_width = 790;
	
	// Default Single Post Layout
	static $single_content_width = 1160;
	static $single_sidebar1_content_width = 790;
	
	// Default Single Image Size
	static $single_image_width = 1160;
	static $single_image_height = 500;
	
	// Grid4
	static $grid4_width = 260;
	static $grid4_height = 160;
	
	// Grid3
	static $grid3_width = 360;
	static $grid3_height = 225;
	
	// Grid2
	static $grid2_width = 560;
	static $grid2_height = 350;
	
	// List Large
	static $list_large_image_width = 350;
	static $list_large_image_height = 200;
	 
	// List Thumb
	static $list_thumb_image_width = 230;
	static $list_thumb_image_height = 200;
	
	// List Grid2 Thumb
	static $grid2_thumb_width = 120;
	static $grid2_thumb_height = 100;
	
	// List Post
	static $list_post_width = 1160;
	static $list_post_height = 500;
	
	// Sorting Parameters
	public $order = 'DESC';
	public $orderby = 'date';
	public $order_meta_key = false;

	public $is_builder_loop = false;

	function __construct() {
		
		///////////////////////////////////////////
		//Global options setup
		///////////////////////////////////////////
		$this->layout = themify_get('setting-default_layout');
		if($this->layout == '' ) $this->layout = 'sidebar1'; 
		
		$this->post_layout = themify_get('setting-default_post_layout', 'list-post');
		
		$this->page_title = themify_get('setting-hide_page_title');
		$this->hide_title = themify_get('setting-default_post_title');
		$this->unlink_title = themify_get('setting-default_unlink_post_title');
		$this->media_position = themify_check( 'setting-default_media_position' ) ? themify_get( 'setting-default_media_position' ) : 'above';
		$this->hide_image = themify_get('setting-default_post_image');
		$this->unlink_image = themify_get('setting-default_unlink_post_image');
		$this->auto_featured_image = !themify_check('setting-auto_featured_image')? 'field_name=post_image, image, wp_thumb&' : '';
		$this->hide_page_image = themify_get( 'setting-hide_page_image' ) == 'yes' ? 'yes' : 'no';
		$this->image_page_single_width = themify_check( 'setting-page_featured_image_width' ) ? themify_get( 'setting-page_featured_image_width' ) : $this->page_image_width;
		$this->image_page_single_height = themify_check( 'setting-page_featured_image_height' ) ? themify_get( 'setting-page_featured_image_height' ) : 0;
		
		$this->hide_meta = themify_get('setting-default_post_meta');
		$this->hide_meta_author = themify_get('setting-default_post_meta_author');
		$this->hide_meta_category = themify_get('setting-default_post_meta_category');
		$this->hide_meta_comment = themify_get('setting-default_post_meta_comment');
		$this->hide_meta_tag = themify_get('setting-default_post_meta_tag');

		$this->hide_date = themify_get('setting-default_post_date');
		
		// Set Order & Order By parameters for post sorting
		$this->order = themify_check('setting-index_order')? themify_get('setting-index_order'): 'DESC';
		$this->orderby = themify_check('setting-index_orderby')? themify_get('setting-index_orderby'): 'date';

		if( in_array( $this->orderby, array( 'meta_value', 'meta_value_num' ) ) ) {
			$this->order_meta_key = themify_get( 'setting-index_meta_key', '' );
		}

		$this->display_content = themify_get('setting-default_layout_display');
		$this->excerpt_length = themify_get( 'setting-default_excerpt_length' );
		$this->avatar_size = apply_filters('themify_author_box_avatar_size', 96);
		
		add_action('template_redirect', array(&$this, 'template_redirect'));

		if( $this->display_content === 'excerpt' && ! empty( $this->excerpt_length ) ) {
			add_filter( 'excerpt_length', array( $this, 'custom_except_length' ), 999 );
		}
	}

	function template_redirect() {
		if( is_archive() || is_home() || is_search() || is_page() ) {
			$this->query_taxonomy = 'category';
			$this->query_post_type = 'post';
		}
		
		$post_image_width = $post_image_height = '';

		if ( is_page() ) {
			if( post_password_required() ) return;

			$this->page_id = get_the_ID();
			$this->post_layout = themify_get( 'layout', 'list-post' );

			// set default post layout
			if( $this->post_layout == '' ) {
				$this->post_layout = 'list-post';
			}

			$post_image_width = themify_get( 'image_width' );
			$post_image_height = themify_get( 'image_height' );

			if( themify_get( 'query_all_post_types' ) ) {
				$this->query_all_post_types = themify_get( 'query_all_post_types' ) === 'yes';
			}
		}
		if( ! is_numeric( $post_image_width ) ) {
			$post_image_width = themify_get( 'setting-image_post_width' );
		}

		if( ! is_numeric( $post_image_height ) ) {
			$post_image_height = themify_get( 'setting-image_post_height' );
		}


		if( is_singular() ) {
			$this->display_content = 'content';
		}
	
		if( ! is_numeric( $post_image_width ) || ! is_numeric( $post_image_height ) ) {
			///////////////////////////////////////////
			// Setting image width, height
			///////////////////////////////////////////
			switch ( $this->post_layout ) {
				case 'grid4':
					$this->width = self::$grid4_width;
					$this->height = self::$grid4_height;
				break;
				case 'grid3':
					$this->width = self::$grid3_width;
					$this->height = self::$grid3_height;
				break;
				case 'grid2':
					$this->width = self::$grid2_width;
					$this->height = self::$grid2_height;
				break;
				case 'list-large-image':
					$this->width = self::$list_large_image_width;
					$this->height = self::$list_large_image_height;
				break;
				case 'list-thumb-image':
					$this->width = self::$list_thumb_image_width;
					$this->height = self::$list_thumb_image_height;
				break;
				case 'grid2-thumb':
					$this->width = self::$grid2_thumb_width;
					$this->height = self::$grid2_thumb_height;
				break;
				default :
					$this->width = self::$list_post_width;
					$this->height = self::$list_post_height;
				break;
			}
		}
		
		
		if (is_numeric($post_image_width) ) {
			$this->width = $post_image_width;
		}

		if (is_numeric($post_image_height) ) {
			$this->height = $post_image_height;
		}

		///////////////////////////////////////////////////////
		// Query Posts
		///////////////////////////////////////////////////////
		if( is_page() ) {
			global $paged;

			if( get_query_var( 'paged' ) ):
				$this->paged = get_query_var( 'paged' );
			elseif( get_query_var( 'page' ) ):
				$this->paged = get_query_var( 'page' );
			else:
				$this->paged = 1;
			endif;
			
			$paged = $this->paged;
			$this->query_category = themify_get( 'query_category' );
			
			$this->layout = themify_check( 'page_layout' )
				&& themify_get( 'page_layout' ) !== 'default'
					? themify_get( 'page_layout' )
					: themify_get( 'setting-default_page_layout' );
			
			if( $this->layout == '' ) {
				$this->layout = 'sidebar1';
			}
			
			$this->post_layout = themify_get( 'layout', 'list-post' );
			$this->page_title = themify_get( 'hide_page_title' ) != 'default'
				&& themify_check( 'hide_page_title' )
					? themify_get( 'hide_page_title' )
					: themify_get( 'setting-hide_page_title' );

			$this->hide_title = themify_get( 'hide_title' );
			$this->unlink_title = themify_get( 'unlink_title' );
			$this->hide_image = themify_get( 'hide_image' );
			$this->unlink_image = themify_get( 'unlink_image' );

			// Post Meta Values ///////////////////////
			$post_meta_keys = array(
				'_author'	=> 'post_meta_author',
				'_category' => 'post_meta_category',
				'_comment'	=> 'post_meta_comment',
				'_tag'		=> 'post_meta_tag'
			);

			$post_meta_key = 'setting-default_';
			$this->hide_meta = themify_check( 'hide_meta_all' )
				? themify_get( 'hide_meta_all' )
				: themify_get( $post_meta_key . 'post_meta' );

			foreach( $post_meta_keys as $k => $v ) {
				$this->{'hide_meta' . $k} = themify_check( 'hide_meta' . $k )
					? themify_get( 'hide_meta' . $k )
					: themify_get( $post_meta_key . $v );
			}

			$this->display_content = themify_get( 'display_content', 'excerpt' );
			$this->post_image_width = themify_get( 'image_width' );
			$this->post_image_height = themify_get( 'image_height' );
			$this->page_navigation = themify_get( 'hide_navigation' );
			$this->posts_per_page = themify_get( 'posts_per_page' );
			$this->order = themify_get( 'order', 'desc' );
			$this->orderby = themify_get( 'orderby', 'date' );

			if( in_array( $this->orderby, array( 'meta_value', 'meta_value_num' ) ) ) {
				$this->order_meta_key = themify_get( 'meta_key', '' );
			}

			if ( 'default' != themify_get( 'hide_date' ) ) {
				$this->hide_date = themify_get( 'hide_date' );
			} else {
				$this->hide_date = themify_check( 'setting-default_post_date' )
					? themify_get( 'setting-default_post_date' ) : 'no';
			}

		} elseif( is_single() ) {
			$is_portfolio = is_singular( 'portfolio' );
			$this->post_layout_type = themify_get( 'post_layout' );

			if ( ! $this->post_layout_type || $this->post_layout_type === 'default' ) {
				$this->post_layout_type = $is_portfolio
					? themify_get( 'setting-default_portfolio_single_portfolio_layout_type' )
					: themify_get( 'setting-default_page_post_layout_type' );
			}

			$this->hide_title = themify_get( 'hide_post_title' ) !== 'default'
				&& themify_check( 'hide_post_title' )
					? themify_get( 'hide_post_title' )
					: themify_get( 'setting-default_page_post_title' );

			$this->unlink_title = themify_get( 'unlink_post_title' ) !== 'default'
				&& themify_check( 'unlink_post_title' )
					? themify_get( 'unlink_post_title' )
					: themify_get( 'setting-default_page_unlink_post_title' );

			$this->hide_date = themify_get( 'hide_post_date' ) !== 'default'
				&& themify_check( 'hide_post_date' )
					? themify_get( 'hide_post_date' )
					: themify_get( 'setting-default_page_post_date' );

			$this->hide_image = themify_get( 'hide_post_image' ) !== 'default'
				&& themify_check( 'hide_post_image' )
					? themify_get( 'hide_post_image' )
					: themify_get( 'setting-default_page_post_image' );

			$this->unlink_image = themify_get( 'unlink_post_image' ) !== 'default'
				&& themify_check( 'unlink_post_image' )
					? themify_get( 'unlink_post_image' )
					: themify_get( 'setting-default_page_unlink_post_image' );

			$this->media_position = 'above';

			// Post Meta Values ///////////////////////
			$post_meta_keys = array(
				'_author'	=> 'post_meta_author',
				'_category'	=> 'post_meta_category',
				'_comment'	=> 'post_meta_comment',
				'_tag'		=> 'post_meta_tag'
			);

			$post_meta_key = 'setting-default_page_';
			$this->hide_meta = themify_check( 'hide_meta_all' )
				? themify_get( 'hide_meta_all' )
				: themify_get( $post_meta_key . 'post_meta' );

			if( is_singular( 'product' ) ) {
				$this->hide_meta = 'no';
			}

			foreach( $post_meta_keys as $k => $v ) {
				$this->{'hide_meta' . $k} = themify_check( 'hide_meta' . $k )
					? themify_get( 'hide_meta' . $k )
					: themify_get( $post_meta_key . $v );
			}
			
			$this->layout = in_array( themify_get( 'layout' )
				, array('sidebar-none', 'sidebar1', 'sidebar1 sidebar-left', 'sidebar2') )
					? themify_get( 'layout' )
					: themify_get( 'setting-default_page_post_layout' );

			// set default layout
			if( $this->layout == '' ) {
				$this->layout = 'sidebar1';
			}
			
			$this->display_content = '';
			
			self::$content_width = self::$single_content_width;
			self::$sidebar1_content_width = self::$single_sidebar1_content_width;
			
			 // Set Default Image Sizes for Single
			$post_image_width = themify_get( 'setting-image_post_single_width' );
			$post_image_height = themify_get( 'setting-image_post_single_height' );
			$this->width =is_numeric( $post_image_width ) ? $post_image_width : self::$single_image_width;
			$this->height = is_numeric( $post_image_height ) ? $post_image_height : self::$single_image_height;
		}
		elseif ( is_archive() ) {

			$excluded_types = apply_filters( 'themify_exclude_CPT_for_sidebar', array('post', 'page', 'attachment', 'tbuilder_layout', 'tbuilder_layout_part', 'section'));;
			$postType = get_post_type();
			
			if ( !in_array($postType, $excluded_types) ) {
				if ( themify_check( 'setting-custom_post_'. $postType .'_archive' ) ) {
					$this->layout = themify_get( 'setting-custom_post_'. $postType .'_archive' );
				}
			}
		}

		/////////////////////////////////////////////////////////////
		// Query Products
		/////////////////////////////////////////////////////////////
		if ( themify_is_woocommerce_active() ) {

			if ( is_woocommerce() ) {
				$this->post_layout = themify_get( 'setting-products_layout', 'list-post' );
				$this->layout = themify_get( 'setting-shop_layout', 'sidebar-none' );
				$this->query_post_type ='product';
			}

			if ( is_page() && '' != themify_get( 'product_query_category' ) ) {

				$pq = 'product_';
				$this->product_category = themify_get( $pq . 'query_category' );
				$this->query_products_field = 'slug';
				$this->query_post_type ='product';

				// Page Navigation //////////////////////////////////////////////////
				$this->page_navigation = themify_get( $pq . 'hide_navigation', 'no' );

				// Sidebar and Products Layout //////////////////////////////////////
				if ( 'default' != themify_get( $pq.'layout' ) ) {
					$this->post_layout = themify_get( $pq.'layout' );
				} elseif ( themify_check( 'setting-product_query_layout' ) ) {
					$this->post_layout = themify_get( 'setting-product_query_layout' );
				} else {
					$this->post_layout = 'list-post';
				}

				if ( 'default' != themify_get( 'page_layout' ) ) {
					$this->layout = themify_get( 'page_layout' );
				} elseif ( themify_check( 'setting-product_query_page_layout' ) ) {
					$this->layout = themify_get( 'setting-product_query_page_layout' );
				} else {
					$this->layout = 'sidebar-none';
				}

				$this->query_type = themify_get( $pq . 'query_type', 'all' );
				// Products Per Page /////////////////////////////////////////////////
				$this->products_per_page = themify_get( $pq.'posts_per_page', get_option( 'posts_per_page' ) );

				// Order & OrderBy ///////////////////////////////////////////////////
				$this->orderby = themify_get( $pq . 'orderby', 'date' );

				if( in_array( $this->orderby, array( 'meta_value', 'meta_value_num' ) ) ) {
					$this->order_meta_key = themify_get( $pq . '_meta_key', '' );
				}

				if( isset( $_GET['orderby'] ) ) {
					$this->orderby = $_GET['orderby'];
				}

				$this->query_category = $this->product_category;
				$this->order = themify_get( $pq . 'order', 'desc' );
				$this->width = themify_theme_get( 'image_width', '', 'setting-default_product_index_image_post_width' );
				$this->height = themify_theme_get( 'image_height', '', 'setting-default_product_index_image_post_height' );
				$this->builder_args['is_product'] = true;
				// Init this var so it looks like a query category page //////////////
			
				// Product Short Description or Full Content /////////////////////////
					$this->product_archive_show_short = themify_get( $pq . 'archive_show_short', 'none' );

				// Set Up Product Query //////////////////////////////////////////////
				global $paged;

				if ( get_query_var( 'paged' ) ) {
					$paged = get_query_var( 'paged' );
				} else if ( get_query_var( 'page' ) ) {
					$paged = get_query_var( 'page' );
				} else {
					$paged = 1;
				}

				$this->query_products = array(
					'post_type' => 'product',
					'posts_per_page' => $this->products_per_page,
					'order' => $this->order,
					'orderby' => $this->orderby,
					'paged' => $paged
				);

				// custom ordering specific to Products post type
				if( isset( $_GET['orderby'] ) && $_GET['orderby'] == 'price' ) {
					/* the orderby=price is set from the WC sorting bar, in the sorting bar 
					 * the orderby=price has specific ordering: from low to high prices
					 */
					$this->query_products['meta_key'] = '_price';
					$this->query_products['orderby'] = 'meta_value_num';
					$this->query_products['order'] = 'ASC'; // lowest-to-highest prices
				} elseif( $this->orderby == 'price' || $this->orderby == 'price-desc' ) {
					$this->query_products['meta_key'] = '_price';
					$this->query_products['orderby'] = 'meta_value_num';
				} elseif( $this->orderby == 'sales' || $this->orderby == 'popularity' ) {
					$this->query_products['meta_key'] = 'total_sales';
					$this->query_products['orderby'] = 'meta_value_num';
				} elseif( $this->orderby == 'rating' ) {
					$this->query_products['orderby'] = 'date';
					add_action( 'themify_query_products_before', array( $this, 'query_products_rating_order' ) );
					add_action( 'themify_query_products_after', array( $this, 'query_products_rating_order_end' ) );
				}

				// Query modifiers
				if( $this->query_type == 'onsale' ) {
					$product_ids_on_sale = wc_get_product_ids_on_sale();
					$product_ids_on_sale[] = 0;
					$this->query_products['post__in'] = $product_ids_on_sale;
				} elseif( $this->query_type == 'featured' ) {
					$this->query_products['meta_query'][] = array(
						'key'	=> '_featured',
						'value' => 'yes'
					);
				} elseif( $this->query_type == 'free' ) {
					$this->query_products['meta_query'][] = array(
						'key'		=> '_price',
						'value'		=> 0,
						'compare'	=> '=',
						'type'		=> 'DECIMAL',
					);
				}
				if ( '-1' == $this->product_category ) {
					$this->query_products['meta_query'] = array(
						array(
							'key'	=> '_featured',
							'value' => 'yes',
						)
					);
				} elseif ( isset( $this->product_category ) && '0' != $this->product_category ) {
					$pcats = explode( ',', $this->product_category );

					if ( ctype_digit( $pcats[0] ) ) {
						$this->query_products_field = 'id';
					}

					$this->query_products['tax_query'] = array(
						array(
							'taxonomy'	=> 'product_cat',
							'field'		=> $this->query_products_field,
							'terms'		=> $pcats
						)
					);
				}
			}
		}
		
		if( is_single() && $this->hide_image !== 'yes' ) {
			$this->image_align = themify_get( 'setting-image_post_single_align' );
			$this->image_setting = 'setting=image_post_single&';
		} elseif( $this->query_category != '' && $this->hide_image != 'yes' ) {
			$this->image_align = '';
			$this->image_setting = '';
		} else {
			$this->image_align = themify_get( 'setting-image_post_align' );
			$this->image_setting = 'setting=image_post&';
		}

		$this->post_layout_type = themify_get( $this->query_post_type . '_content_layout', 'default' ) === 'default'
			? themify_get( 'setting-' . $this->query_post_type . '_content_layout', 'default' )
			: themify_get( $this->query_post_type . '_content_layout' );
	}

	function custom_except_length() {
		return apply_filters( 'themify_custom_excerpt_length', $this->excerpt_length );
	}

	/**
	 * Fix product order by rating in Query Products
	 *
	 * @since 1.7.9
	 */
	function query_products_rating_order() {
		add_filter( 'posts_clauses', array( wc()->query, 'order_by_rating_post_clauses' ) );
	}

	/**
	 * Disable order by rating in Query Products
	 *
	 * @since 1.7.9
	 */
	function query_products_rating_order_end() {
		remove_filter( 'posts_clauses', array( wc()->query, 'order_by_rating_post_clauses' ) );
	}
}

/**
 * Initializes Themify class
 * @since 1.0.0
 */
function themify_global_options(){
	global $themify;
	$themify = new Themify();
}
add_action( 'after_setup_theme','themify_global_options', 12 );

?>