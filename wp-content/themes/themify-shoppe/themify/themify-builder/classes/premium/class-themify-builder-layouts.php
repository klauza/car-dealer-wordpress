<?php
/**
 * This file defines Builder Layouts and Layout Parts
 *
 * Themify_Builder_Layouts class register post type for Layouts and Layout Parts
 * Custom metabox, shortcode, and load layout / layout part.
 * 
 *
 * @package    Themify_Builder
 * @subpackage Themify_Builder/classes
 */

/**
 * The Builder Layouts class.
 *
 * This class register post type for Layouts and Layout Parts
 * Custom metabox, shortcode, and load layout / layout part.
 *
 *
 * @package    Themify_Builder
 * @subpackage Themify_Builder/classes
 * @author     Themify
 */
class Themify_Builder_Layouts {

	/**
	 * Post Type Layout Object.
	 * 
	 * @access public
	 * @var object $layout.
	 */
	public $layout;

	/**
	 * Post Type Layout Part Object.
	 * 
	 * @access public
	 * @var object $layout_part.
	 */
	public $layout_part;

	/**
	 * Store registered layout / part post types.
	 * 
	 * @access public
	 * @var array $post_types.
	 */
	public $post_types = array();

	/**
	 * Holds a list of layout provider instances
	 */
	public $provider_instances = array();

	/**
	 * Constructor
	 * 
	 * @access public
	 */
	public function __construct() {
            $this->register_layout();
            if(is_admin()){
                $this->register_providers();
                // Builder write panel
                add_filter( 'themify_do_metaboxes', array( $this, 'layout_write_panels' ), 11 );
                add_filter( 'themify_post_types', array( $this, 'extend_post_types' ) );
                add_action( 'add_meta_boxes_tbuilder_layout_part', array( $this, 'custom_meta_boxes' ) );

                add_action( 'wp_ajax_tb_load_layout', array( $this, 'load_layout_ajaxify' ), 10 );
                add_action( 'wp_ajax_tb_set_layout', array( $this, 'set_layout_ajaxify' ), 10 );
                add_action( 'wp_ajax_tb_custom_layout_form', array( $this, 'custom_layout_form_ajaxify' ), 10 );
                add_action( 'wp_ajax_tb_save_custom_layout', array( $this, 'save_custom_layout_ajaxify' ), 10 );

                // Quick Edit Links
                add_filter( 'post_row_actions', array( $this, 'row_actions' ) );
                add_filter( 'page_row_actions', array( $this, 'row_actions' ) );
                add_filter( 'bulk_actions-edit-tbuilder_layout_part', array( $this, 'row_bulk_actions' ) );
                add_filter( 'bulk_actions-edit-tbuilder_layout', array( $this, 'row_bulk_actions' ) );
                add_filter( 'handle_bulk_actions-edit-tbuilder_layout_part', array( $this, 'export_row_bulk' ), 10, 3);
                add_filter( 'handle_bulk_actions-edit-tbuilder_layout', array( $this, 'export_row_bulk' ), 10, 3);
                add_action( 'admin_init', array( $this, 'duplicate_action' ) );
                add_action( 'admin_init', array( $this, 'export_row' ) );

                add_action( 'admin_init', array( $this, 'cleanup_builtin_layouts' ) );
                add_filter( 'themify_builder_post_types_support', array( $this, 'add_builder_support' ) );

                // Ajax hook for Layout and Layout Parts import file.
                add_action('wp_ajax_tbuilder_plupload_layout', array( $this, 'row_bulk_import'));
                add_action('admin_head-edit.php', array( $this, 'row_bulk_import_button'));
            }
            add_shortcode( 'themify_layout_part', array( $this, 'layout_part_shortcode' ) );
            add_filter( 'template_include', array( $this, 'template_singular_layout' ) );
	}


	/**
	 * Registers providers for layouts in Builder
	 *
	 * @since 2.0.0
	 */
	public function register_providers() {
		$providers = apply_filters( 'themify_builder_layout_providers', array(
			'Themify_Builder_Layouts_Provider_Pre_Designed',
			'Themify_Builder_Layouts_Provider_Theme',
			'Themify_Builder_Layouts_Provider_Custom'
		) );
		foreach( $providers as $provider ) {
			if( class_exists( $provider ) ) {
				$instance = new $provider();
				$this->provider_instances[ $instance->get_id() ] = $instance;
			}
		}
	}

	/**
	 * Get a single layout provider instance
	 *
	 * @since 2.0.0
	 */
	public  function get_provider( $id ) {
		return isset( $this->provider_instances[ $id ] )?$this->provider_instances[ $id ]:false;
	}

	/**
	 * Register Layout and Layout Part Custom Post Type
	 * 
	 * @access public
	 */
	public function register_layout() {
		if ( ! class_exists( 'CPT' ) ) {
			include THEMIFY_BUILDER_LIBRARIES_DIR . '/CPT.php';
		}

		// create a template custom post type
		$this->layout = new CPT( array(
			'post_type_name' => 'tbuilder_layout',
			'singular' => __('Layout', 'themify'),
			'plural' => __('Layouts', 'themify')
		), array(
			'supports' => array('title', 'thumbnail'),
			'exclude_from_search' => true,
			'show_in_nav_menus' => false,
			'show_in_menu' => false,
			'public' => true
		));

		// define the columns to appear on the admin edit screen
		$this->layout->columns(array(
			'cb' => '<input type="checkbox" />',
			'title' => __('Title', 'themify'),
			'thumbnail' => __('Thumbnail', 'themify'),
			'author' => __('Author', 'themify'),
			'date' => __('Date', 'themify')
		));

		// populate the thumbnail column
		$this->layout->populate_column('thumbnail', array( $this, 'populate_column_layout_thumbnail' ) );

		// use "pages" icon for post type
		$this->layout->menu_icon('dashicons-admin-page');

		// create a template custom post type
		$this->layout_part = new CPT( array(
			'post_type_name' => 'tbuilder_layout_part',
			'singular' => __('Layout Part', 'themify'),
			'plural' => __('Layout Parts', 'themify'),
			'slug' => 'tbuilder-layout-part'
		), array(
			'supports' => array('title', 'thumbnail'),
			'exclude_from_search' => true,
			'show_in_nav_menus' => false,
			'show_in_menu' => false,
			'public' => true
		));

		// define the columns to appear on the admin edit screen
		$this->layout_part->columns(array(
			'cb' => '<input type="checkbox" />',
			'title' => __('Title', 'themify'),
			'shortcode' => __('Shortcode', 'themify'),
			'author' => __('Author', 'themify'),
			'date' => __('Date', 'themify')
		));

		// populate the thumbnail column
		$this->layout_part->populate_column('shortcode', array( $this, 'populate_column_layout_part_shortcode' ) );

		// use "pages" icon for post type
		$this->layout_part->menu_icon('dashicons-screenoptions');

		$this->set_post_type_var( $this->layout->post_type_name );
		$this->set_post_type_var( $this->layout_part->post_type_name );

		add_post_type_support( $this->layout->post_type_name, 'revisions' );
		add_post_type_support( $this->layout_part->post_type_name, 'revisions' );
	}

	/**
	 * Set the post type variable.
	 * 
	 * @access public
	 * @param string $name 
	 */
	public function set_post_type_var( $name ) {
                $this->post_types[] = $name;
	}

	/**
	 * Custom column thumbnail.
	 * 
	 * @access public
	 * @param array $column 
	 * @param object $post 
	 */
	public function populate_column_layout_thumbnail( $column, $post ) {
		echo get_the_post_thumbnail( $post->ID, 'thumbnail');
	}

	/**
	 * Custom column for shortcode.
	 * 
	 * @access public
	 * @param array $column 
	 * @param object $post 
	 */
	public function populate_column_layout_part_shortcode( $column, $post ) {
            echo sprintf( '[themify_layout_part id=%d]', $post->ID ),'<br/>',sprintf( '[themify_layout_part slug=%s]', $post->post_name );
	}

	/**
	 * Metabox Panel
	 *
	 * @access public
	 * @param $meta_boxes
	 * @return array
	 */
	public function layout_write_panels( $meta_boxes ) {
		global $pagenow;

		if ( ! in_array( $pagenow, array( 'post-new.php', 'post.php' ),true ) ) {
			return $meta_boxes;
		}

		$meta_settings = array(
			array(
				'name' 		=> 'post_image',
				'title' 	=> __('Layout Thumbnail', 'themify'),
				'description' => '',
				'type' 		=> 'image',
				'meta'		=> array()
			)
		);
			
		$all_meta_boxes = array();
		$all_meta_boxes[] = apply_filters( 'layout_write_panels_meta_boxes', array(
			'name'		=> __( 'Settings', 'themify' ),
			'id' 		=> 'layout-settings-builder',
			'options'	=> $meta_settings,
			'pages'    	=> $this->layout->post_type_name
		) );
		return array_merge( $meta_boxes, $all_meta_boxes);
	}

	/**
	 * Includes this custom post to array of cpts managed by Themify
	 * 
	 * @access public
	 * @param Array $types
	 * @return Array
	 */
	public function extend_post_types( $types ) {
		$cpts = array( $this->layout->post_type_name, $this->layout_part->post_type_name );
		return array_merge( $types, $cpts );
	}

	/**
	 * Add meta boxes to layout and/or layout part screens.
	 *
	 * @access public
	 * @param object $post
	 */
	public function custom_meta_boxes( $post ) {
		add_meta_box( 'layout-part-info', __( 'Using this Layout Part', 'themify' ), array( $this, 'layout_part_info' ), $this->layout_part->post_type_name, 'side', 'default' );
	}

	/**
	 * Displays information about this layout part.
	 * 
	 * @access public
	 */
	public function layout_part_info() {
		$layout_part = get_post();
		echo '<div>' , __( 'To display this Layout Part, insert this shortcode:', 'themify' ) , '<br/>
		<input type="text" readonly="readonly" class="widefat" onclick="this.select()" value="' . esc_attr( '[themify_layout_part id="' . $layout_part->ID . '"]' ) . '" />';
		if ( ! empty( $layout_part->post_name ) ) {
			echo '<input type="text" readonly="readonly" class="widefat" onclick="this.select()" value="' . esc_attr( '[themify_layout_part slug="' . $layout_part->post_name . '"]' ) . '" />';
		}
		echo '</div>';
	}

	/**
	 * Load list of available Templates
	 * 
	 * @access public
	 */
	public function load_layout_ajaxify() {

		check_ajax_referer( 'tb_load_nonce', 'nonce' );

		include_once THEMIFY_BUILDER_INCLUDES_DIR . '/themify-builder-layout-lists.php';
		die();
	}

	/**
	 * Custom layout for Template / Template Part Builder Editor.
	 * 
	 * @access public
	 */
	public function template_singular_layout( $original_template ) {
		if ( is_singular( array( $this->layout->post_type_name, $this->layout_part->post_type_name ) ) ) {
			$templatefilename = 'template-builder-editor.php';
			
			$return_template = locate_template(
				array(
					trailingslashit( 'themify-builder/templates' ) . $templatefilename
				)
			);

			// Get default template
			if ( ! $return_template )
				$return_template = THEMIFY_BUILDER_TEMPLATES_DIR . '/' . $templatefilename;

			return $return_template;
		} else {
			return $original_template;
		}
	}

	/**
	 * Set/Append template to current active builder.
	 * 
	 * @access public
	 */
	public function set_layout_ajaxify() {
		check_ajax_referer( 'tb_load_nonce', 'nonce' );
		$template_slug = $_POST['layout_slug'];
		$current_builder_id = (int) $_POST['id'];
		$layout_group = $_POST['layout_group'];
		$builder_data = '';
		$response = array();
		if( isset( $this->provider_instances[ $layout_group ] ) ) {
                    $builder_data = $this->provider_instances[ $layout_group ]->get_builder_data( $template_slug );
		}

		if ( ! is_wp_error( $builder_data ) && ! empty( $builder_data ) ) { 
                        if(empty($_POST['mode'])){
                            $old_builder_data = $GLOBALS['ThemifyBuilder_Data_Manager']->get_data( $current_builder_id );
                            $count = count( $old_builder_data );
                            foreach ($builder_data as $data ) {
                                    $data['row_order'] = $count;
                                    $old_builder_data[] = $data;
                                    ++$count;
                            }
                            $builder_data = $old_builder_data;
                        }
                       
			$response = $GLOBALS['ThemifyBuilder_Data_Manager']->save_data( $builder_data, $current_builder_id, 'layout' );
                        global $ThemifyBuilder;
                        if(!empty($response['css']) && ($fonts = $ThemifyBuilder->stylesheet->enqueue_fonts( array() ))){
                            $response['css']['fonts'] = $fonts;
                        }
			$response['status'] = 'success';
			$response['msg'] = '';
		} else {
			$response['status'] = 'failed';
			$response['msg'] = $builder_data->get_error_message();
		}
                $mode = !empty($_POST['mode'])?'themify_builder_layout_appended':'themify_builder_layout_loaded';
		do_action($mode, compact( 'template_slug', 'current_builder_id', 'layout_group', 'builder_data' ) );

		wp_send_json( $response );
		die();
	}

	/**
	 * Layout Part Shortcode
	 * 
	 * @access public
	 * @param array $atts 
	 * @return string
	 */
	public function layout_part_shortcode( $atts ) {
		
		
		$args = array(
                    'post_type' => $this->layout_part->post_type_name,
                    'post_status' => 'publish',
                    'numberposts' => 1,
                    'orderby'=>'ID',
                    'order'=>'ASC'
		);
                if ( ! empty( $atts['slug'] ) ){
                    $args['name'] = $atts['slug'];
                }
		if ( ! empty( $atts['id'] ) ){
                    $args['p'] = $atts['id'];
                }
		$template = get_posts( $args );
		$output = '';
		if ( $template ) {
                    global $ThemifyBuilder;
					$builder_data = $ThemifyBuilder->get_builder_data( $template[0]->ID );
					// Check For page break module
					if(!Themify_Builder::$frontedit_active){
						$module_list = $ThemifyBuilder->get_flat_modules_list( $template[0]->ID );
						$page_breaks = 0;
						foreach($module_list as $module){
							if('page-break' === $module['mod_name']){
								$page_breaks++;
							}
						}
						if($page_breaks>0){
							$builder_data = $ThemifyBuilder->load_current_inner_page_content($builder_data,$page_breaks);
							
						}
					}
					if ( ! empty( $builder_data ) ) {
                        $output = Themify_Builder_Component_Base::retrieve_template( 'builder-layout-part-output.php', array( 'builder_output' => $builder_data, 'builder_id' => $template[0]->ID), '', '', false );
                        if(!TFCache::is_ajax()){
                            $output = $ThemifyBuilder->get_builder_stylesheet($output).$output;
                        }
                    }
		}

		return $output;
	}

	/**
	 * Render Layout Form in lightbox
	 * 
	 * @access public
	 */
	public function custom_layout_form_ajaxify() {
		check_ajax_referer( 'tb_load_nonce', 'nonce' );
		$postid = (int) $_POST['postid'];

		$fields = array(
			array(
				'id' => 'layout_img_field',
				'type' => 'image',
				'label' => __('Image Preview', 'themify'),
				'class' => 'xlarge'
			),
			array(
				'id' => 'layout_title_field',
				'type' => 'text',
				'label' => __('Title', 'themify')
			)
		);
		include_once THEMIFY_BUILDER_INCLUDES_DIR . '/themify-builder-options.php' ;
		include THEMIFY_BUILDER_INCLUDES_DIR . '/themify-builder-save-layout-form.php';
		wp_die();
	}

	/**
	 * Save as Layout
	 * 
	 * @access public
	 */
	public function save_custom_layout_ajaxify() {
		check_ajax_referer( 'tb_load_nonce', 'nonce' );
		global $ThemifyBuilder;
		$data = array();
		$response = array(
			'status' => 'failed',
			'msg' => __('Something went wrong', 'themify')
		);
		if ( isset( $_POST['form_data'] ) ){
                        parse_str( $_POST['form_data'], $data );
                    if (! empty( $data['postid'] ) ) {
                            $template = get_post( $data['postid'] );
                            $title = ! empty( $data['layout_title_field'] ) ? sanitize_text_field( $data['layout_title_field'] ) : $template->post_title . ' Layout';
                            $builder_data = $ThemifyBuilder->get_builder_data( $template->ID );
                            if ( ! empty( $builder_data ) ) {
                                    $new_id = wp_insert_post(array(
                                            'post_status' => 'publish',
                                            'post_type' => $this->layout->post_type_name,
                                            'post_author' => $template->post_author,
                                            'post_title' => $title,
                                    ));

                                    $GLOBALS['ThemifyBuilder_Data_Manager']->save_data( $builder_data, $new_id);

                                    // Set image as Featured Image
                                    if (! empty( $data['layout_img_field_attach_id'] ) ){
                                        set_post_thumbnail( $new_id, $data['layout_img_field_attach_id'] );
                                    }
                                    $response['status'] = 'success';
                                    $response['msg'] = '';
                            }
                    }
                }
		wp_send_json( $response );
	}

	/**
	 * Add custom link actions in post / page rows
	 * 
	 * @access public
	 * @param array $actions 
	 * @return array
	 */
	public function row_actions( $actions ) {
		global $post;
		$builder_link = sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( get_permalink( $post->ID ) . '#builder_active' ), __('Themify Builder', 'themify' ));
		if ( $this->layout->post_type_name === get_post_type()  ||  $this->layout_part->post_type_name === get_post_type()) {
			$actions['themify-builder-duplicate'] = sprintf( '<a href="%s">%s</a>', wp_nonce_url( admin_url( 'post.php?post=' . $post->ID . '&action=duplicate_tbuilder' ), 'duplicate_themify_builder' ), __('Duplicate', 'themify') );
			$actions['tbuilder-export'] = sprintf( '<a href="%s">%s</a>', wp_nonce_url( admin_url( 'post.php?post=' . $post->ID . '&action=tbuilder_export' ), 'tbuilder_layout_export' ), __('Export', 'themify') );
			$actions['themify-builder'] = $builder_link;
		} else {
			// print builder links on another post types
			$registered_post_types = themify_post_types();
			if ( in_array( get_post_type(), $registered_post_types,true ) ) 
				$actions['themify-builder'] = $builder_link;
		}

		return $actions;
	}

	/**
	 * Add custom link actions in Layout / Layout Part rows bulk action
	 * 
	 * @access public
	 * @param array $actions 
	 * @return array
	 */
	public function row_bulk_actions( $actions ) {
		
		$actions['tbuilder-bulk-export'] = __( 'Export', 'themify');

		return $actions;
	}

	/**
	 * Export Layouts and Layout Parts.
	 * 
	 * @access public
	 */
	public function export_row() {
		if ( isset( $_GET['action'] ) && 'tbuilder_export' === $_GET['action'] && wp_verify_nonce($_GET['_wpnonce'], 'tbuilder_layout_export') ) {
			$postid = array((int) $_GET['post']);
			if(!$this->export_row_bulk('', 'tbuilder-bulk-export' , $postid))
				wp_redirect( admin_url( 'edit.php?post_type=' . get_post_type( $postid[0] ) ) );
			exit;
		}
	}

	/**
	 * Export Layouts and Layout Parts.
	 * 
	 * @access public
	 */
	public function export_row_bulk( $redirect_to, $action, $pIds ) {
		if ( $action !== 'tbuilder-bulk-export' || empty($pIds)) {
			return $redirect_to;
		}

		$data = array('import' => '', 'content' => array());
		$type = get_post_type($pIds[0]);
		$data['import'] = ($type == 'tbuilder_layout_part') ? 'Layout Parts' : 'Layouts';

		foreach ( $pIds as $pId ) {
                        $data['content'][] =  array( 
                                                    'title' => get_the_title($pId),
                                                    'settings' => get_post_meta( $pId, '_themify_builder_settings_json', true )
                                                );
		}
		
		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}
		WP_Filesystem();
		global $wp_filesystem;

		if(class_exists('ZipArchive')){
			$datafile = 'export_file.txt';
			$wp_filesystem->put_contents( $datafile, serialize( $data ) );
			$files_to_zip = array( $datafile );
			$file = 'themify_' . $data['import'] . '_export_' . date('Y_m_d') . '.zip';
			$result = themify_create_zip( $files_to_zip, $file, true );
		}
		if(isset($result) && $result){
			if ( ( isset( $file ) ) && ( $wp_filesystem->exists( $file ) ) ) {
				ob_start();
				header('Pragma: public');
				header('Expires: 0');
				header('Content-type: application/force-download');
				header('Content-Disposition: attachment; filename="' . $file . '"');
				header('Content-Transfer-Encoding: Binary'); 
				header('Content-length: '.filesize($file));
				header('Connection: close');
				ob_clean();
				flush();
				echo $wp_filesystem->get_contents( $file );
				$wp_filesystem->delete( $datafile );
				$wp_filesystem->delete( $file );
				exit();
			} else {
				return false;
			}
		} else {
			if ( ini_get( 'zlib.output_compression' ) ) {
				ini_set( 'zlib.output_compression', 'Off' );
			}
			ob_start();
			header('Content-Type: application/force-download');
			header('Pragma: public');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Cache-Control: private',false);
			header('Content-Disposition: attachment; filename="themify_' . $data['import'] . '_export_'.date("Y_m_d").'.txt"');
			header('Content-Transfer-Encoding: binary');
			ob_clean();
			flush();
			echo serialize($data);
			exit();
		}

		return false;
	}

	/**
	 * Import Layout and Layout Parts.
	 * 
	 * @access public
	 */
	public function row_bulk_import() {
		$imgid = $_POST['imgid'];
		
		! empty( $_POST[ '_ajax_nonce' ] ) && check_ajax_referer($imgid . 'themify-plupload');

		/** Handle file upload storing file|url|type. @var Array */
		$file = wp_handle_upload($_FILES[$imgid . 'async-upload'], array('test_form' => true, 'action' => 'tbuilder_plupload_layout'));

		// if $file returns error, return it and exit the function
		if (! empty( $file['error'] ) ) {
			echo json_encode($file);
			exit;
		}

		//let's see if it's an image, a zip file or something else
		$ext = explode('/', $file['type']);
		// Import routines
		if( 'zip' === $ext[1] || 'rar' === $ext[1] || 'plain' === $ext[1] ){

			$url = wp_nonce_url('edit.php');

			if (false === ($creds = request_filesystem_credentials($url) ) ) {
				return true;
			}
			if ( ! WP_Filesystem($creds) ) {
				request_filesystem_credentials($url, '', true);
				return true;
			}

			global $wp_filesystem;
			$base_path = wp_upload_dir();
			$base_path = trailingslashit( $base_path['path'] );

			if( 'zip' === $ext[1] || 'rar' === $ext[1] ) {
				unzip_file($file['file'], $base_path);
				if( $wp_filesystem->exists( $base_path . 'export_file.txt' ) ) {
					$data = $wp_filesystem->get_contents( $base_path . 'export_file.txt' );
					$msg = $this->set_data( unserialize( $data ) );
					if($msg)
						$file['error'] = $msg;
					$wp_filesystem->delete($base_path . 'export_file.txt');
					$wp_filesystem->delete($file['file']);
				} else {
					$file['error'] = __('Data could not be loaded', 'themify');
				}
			} else {
				if( $wp_filesystem->exists( $file['file'] ) ){
					$data = $wp_filesystem->get_contents( $file['file'] );
					$msg = $this->set_data( unserialize( $data ) );
					if($msg)
						$file['error'] = $msg;
					$wp_filesystem->delete($file['file']);
				} else {
					$file['error'] = __('Data could not be loaded', 'themify');
				}
			}
			
		}
		$file['type'] = $ext[1];
		// send the uploaded file url in response
		echo json_encode($file);
		exit;
	}

	public function row_bulk_import_button() {
		$post_type = get_current_screen()->post_type;

		if( 'tbuilder_layout' !== $post_type && 'tbuilder_layout_part' !== $post_type )
                     return;

		$message = 'tbuilder_layout' !== $post_type? 'Layouts' : 'Layout Parts';
		// Enqueue media scripts
		wp_enqueue_media();

		// Plupload
		wp_enqueue_script( 'plupload-all' );
		wp_enqueue_script( 'themify-plupload' );

		$button = themify_get_uploader('tbuilder-layout-import', array(
								'label'		=> __('Import', 'themify'),
								'preset'	=> false,
								'preview'   => false,
								'tomedia'	=> false,
								'topost'	=> '',
								'fields'	=> '',
								'featured'	=> '',
								'message'	=> '',
								'fallback'	=> '',
								'dragfiles' => false,
								'confirm'	=> __('Import will add all the '.$message.' containing in the file. Press OK to continue, Cancel to stop.', 'themify'),
								'medialib'	=> false,
								'formats'	=> 'zip,txt',
								'type'		=> '',
								'action'    => 'tbuilder_plupload_layout',
							)
						);
 ?>
                <style type="text/css">
			.tbuilder-layout-import{
				display: inline-block;
				top: 0px;
				margin: 0px;
				vertical-align: bottom;
				border:none;
				margin-left: 5px;
			}
			.tbuilder-layout-import .plupload-button
				{
					padding: 4px 10px;
					position: relative;
					top: -4px;
					text-decoration: none;
					border: none;
					border: 1px solid #ccc;
					border-radius: 2px;
					background: #f7f7f7;
					text-shadow: none;
					font-weight: 600;
					font-size: inherit;
					line-height: normal;
					color: #0073aa;
					cursor: pointer;
					outline: 0;
					box-shadow: none;
					height:auto;
				}
			.tbuilder-layout-import .plupload-button:hover
				{
					border-color: #008EC2;
					background: #00a0d2;
					color: #fff;
				}
		</style>
		<script type="text/javascript">
			jQuery(document).ready( function($) 
			{
				$('.page-title-action').after('<div class="tbuilder-layout-import" style="display:inline-block"><?php echo  preg_replace('~[\r\n\t]+~', '', addslashes($button)); ?></div>');
			});     
		</script>
<?php 
	}

	private function set_data($data){
		$error = false;

		if(!isset($data['import']) || !isset($data['content']) || !is_array($data['content'])){
			$error = __('Incorrect Import File', 'themify');
		} else {

			if($data['import'] === 'Layouts')
				$type = 'tbuilder_layout';
			elseif ($data['import'] === 'Layout Parts'){
				$type = 'tbuilder_layout_part';
			} else {
				$error = __('Failed to import. Unknown data.', 'themify');
			}

			if(!$error){
				global $ThemifyBuilder_Data_Manager;

				foreach($data['content'] as $psot){
					$new_id = wp_insert_post(array(
						'post_status' => 'publish',
						'post_type' => $type,
						'post_author' => get_current_user_id(),
						'post_title' => $psot['title'],
						'post_content' => ''
					));
					if(!empty($psot['settings'])){
						$ThemifyBuilder_Data_Manager->save_data( json_decode($psot['settings'],true), $new_id );
					}
				}
			}
		}

		return $error;
	}

	/**
	 * Duplicate Post in Admin Edit page.
	 * 
	 * @access public
	 */
	public function duplicate_action() {
		if ( isset( $_GET['action'] ) && 'duplicate_tbuilder' === $_GET['action'] && wp_verify_nonce($_GET['_wpnonce'], 'duplicate_themify_builder') ) {
			global $themifyBuilderDuplicate;
			$postid = (int) $_GET['post'];
			$layout = get_post( $postid );

			$new_id = $themifyBuilderDuplicate->duplicate( $layout );
			delete_post_meta( $new_id, '_themify_builder_prebuilt_layout' );

			wp_redirect( admin_url( 'edit.php?post_type=' . get_post_type( $postid ) ) );
			exit;
		}
	}

	/**
	 * Get layouts cache dir.
	 * 
	 * @access public
	 * @return array
	 */
	static public function get_cache_dir() {
		$upload_dir = wp_upload_dir();

		$dir_info = array(
			'path'   => $upload_dir['basedir'] . '/themify-builder/',
			'url'    => $upload_dir['baseurl'] . '/themify-builder/'
		);

		if( ! file_exists( $dir_info['path'] ) ) {
			wp_mkdir_p( $dir_info['path'] );
		}

		return $dir_info;
	}

	/**
	 * Add Builder support to Layout and Layout Part post types.
	 * 
	 * @access public
	 * @since 2.4.8
	 */
	public function add_builder_support( $post_types ) {
		$post_types['tbuilder_layout'] = 'tbuilder_layout';
		$post_types['tbuilder_layout_part'] = 'tbuilder_layout_part';

		return $post_types;
	}

	/**
	 * Runs once and removes the builtin layout posts as no longer needed
	 *
	 * @access public
	 * @since 1.5.1
	 */
	public function cleanup_builtin_layouts() {
		global $post;
		if( get_option( 'themify_builder_cleanup_builtin_layouts' ) === 'yes' )
			return;

		$posts = new WP_Query( array(
			'post_type' => $this->layout->post_type_name,
			'posts_per_page' => -1,
			'orderby' => 'title',
			'order' => 'ASC',
			'meta_key' => '_themify_builder_prebuilt_layout',
			'meta_value' => 'yes'
		));
		if( $posts->have_posts() ) { 
                    while( $posts->have_posts() ) {
                        $posts->the_post();
                        wp_delete_post( $post->ID, true );
                    }  
                }
		wp_reset_postdata();

		update_option( 'themify_builder_cleanup_builtin_layouts', 'yes' );
	}
        
        
}

/**
 * Base class for Builder layout provider
 *
 * Different types of layouts that can be imported in Builder must each extend this base class
 *
 * @since 2.0.0
 */
class Themify_Builder_Layouts_Provider {

	/**
	 * Get the ID of provider
	 *
	 * @return string
	 */
	public function get_id() {}

	/**
	 * Get the label of provider
	 *
	 * @return string
	 */
	public function get_label() {}

	/**
	 * Get a list of available layouts provided by this class
	 *
	 * @return array
	 */
	public function get_layouts() {
		return array();
	}

	/**
	 * Check if the layout provider has any layouts available
	 *
	 * @return bool
	 */
	public function has_layouts() {
		$layouts = $this->get_layouts();
		return ! empty( $layouts );
	}

	/**
	 * Returns Builder data for a given layout $slug, or a WP_Error instance should that fail
	 *
	 * @return array|WP_Error
	 */
	public function get_builder_data( $slug ) {
		return array();
	}

	/**
	 * Create the tab interface in Load Layouts screen
	 *
	 * @return string
	 */
	public function get_list_output() {
		$layouts = $this->get_layouts();
		if( ! empty( $layouts ) ) : ?>
			<div id="tb_tabs_<?php echo $this->get_id(); ?>" class="tb_tab">
				<ul class="tb_layout_lists">

					<?php foreach( $layouts as $layout ) : ?>
					<li class="layout_preview_list">
						<div class="layout_preview" data-slug="<?php echo esc_attr( $layout['slug'] ); ?>" data-group="<?php echo $this->get_id(); ?>">
							<div class="thumbnail"><?php echo $layout['thumbnail']; ?></div><!-- /thumbnail -->
							<div class="layout_action">
								<div class="layout_title"><?php echo $layout['title']; ?></div><!-- /template_title -->
							</div><!-- /template_action -->
						</div><!-- /template_preview -->
					</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php
		endif;
	}

	/**
	 * Gets a path to a layouts list file, returns the list
	 *
	 * @return array
	 */
	public function get_layouts_from_file( $path ) {
                $layouts = array();
                if( is_file( $path ) ) {
                        foreach( include( $path ) as $layout ) {
                                $layouts[] = array(
                                        'title' => $layout['title'],
                                        'slug' => $layout['data'],
                                        'thumbnail' => sprintf( '<img src="%s">', $layout['thumb'] ),
                                );
                        }
                }
		return $layouts;
	}

	/**
	 * Get the Builder data from an exported file
	 * Automatically unzips the file if it's compressed
	 *
	 * @return array|WP_Error
	 */
	function get_builder_data_from_file( $file ) {
		if( is_file( $file ) ) {
			$cache_dir = themify_get_cache_dir();
			$extract_file = $cache_dir['path'] . basename( $file );
			WP_Filesystem();
			/* extract the file */
			$extract_action = unzip_file( $file, $extract_file );
			if( is_wp_error( $extract_action ) ) {
				return $extract_action;
			} else {
				$extract_file = $cache_dir['path'] . basename( $file ) . '/builder_data_export.txt';
				/* use include to read the file, seems safer than wp_filesystem */
				ob_start();
				include $extract_file;
				$builder_data = ob_get_clean();
				$builder_data = json_decode( $builder_data, true );
				return $builder_data;
			}
		} else {
			return new WP_Error( 'fail', __( 'Layout does not exist.', 'themify' ) );
		}
	}
        
        public function print_template_form(){}
}

/**
 * "Custom" layout provider, adds the posts from "tbuilder_layout" post type as layouts
 *
 * @since 2.0.0
 */
class Themify_Builder_Layouts_Provider_Custom extends Themify_Builder_Layouts_Provider {

	public function get_id() {
		return 'custom';
	}

	public function get_label() {
		return __( 'Custom', 'themify' );
	}

	/**
	 * Get a list of "custom" layouts, each post from the "tbuilder_layout" post type
	 * is a Custom layout, this returns a list of them all
	 *
	 * @return array
	 */
	public function get_layouts() {
            global $post;
            $layouts = array();
            $posts = new WP_Query( array(
                    'post_type' => 'tbuilder_layout',
                    'posts_per_page' => -1,
                    'orderby' => 'title',
                    'order' => 'ASC',
            ));

            if( $posts->have_posts() ){
                while( $posts->have_posts() ){
                    $posts->the_post();
                    $layouts[] = array(
                            'title' => get_the_title(),
                            'slug' => $post->post_name,
                            'thumbnail' => has_post_thumbnail() ? get_the_post_thumbnail(null, 'thumbnail', array( 150, 150 ) ) : sprintf( '<img src="%s">',  get_template_directory_uri().'/themify/themify-builder/img/placeholder.png' ),
                    );
                } 
            }
            wp_reset_postdata();
            return $layouts;
	}

	public function get_builder_data( $slug ) {
		global $ThemifyBuilder;
		$args = array(
			'name' => $slug,
			'post_type' => 'tbuilder_layout',
			'post_status' => 'publish',
			'numberposts' => 1
		);
		$template = get_posts( $args );
		if ( $template ) {
			return $ThemifyBuilder->get_builder_data( $template[0]->ID );
		} else {
			return new WP_Error( 'fail', __('Requested layout not found.', 'themify') );
		}
	}
}

/**
 * Pre-designed layouts in Builder
 *
 * To see a list of pre-designed layouts go to https://themify.me/demo/themes/builder-layouts/
 * The list of these layouts is loaded in themify-builder-app.js
 *
 * @since 2.0.0
 */
class Themify_Builder_Layouts_Provider_Pre_Designed extends Themify_Builder_Layouts_Provider {

	public function get_id() {
		return 'pre-designed';
	}

	public function get_label() {
		return __( 'Pre-designed', 'themify' );
	}

	/**
	 * Check if the provider has any layouts
	 *
	 * The pre-designed layouts are always available!
	 *
	 * @return true
	 */
	public function has_layouts() {
		return true;
	}

	public function get_list_output() {
            ?>
		<div id="tb_tabs_pre-designed" class="tb_tab">
			<input type="text" placeholder="<?php _e( 'Search', 'themify' ); ?>" id="tb_layout_search" />
			<div class="tb_ui_dropdown">
				<span class="tb_ui_dropdown_label"><?php _e('All','themify')?></span>
				<ul class="tb_ui_dropdown_items">
					<li><a href="#" class="all"><?php _e( 'All', 'themify' ); ?></a></li>
				</ul>
			</div>
			<div id="tb_load_layout_error" style="display: none;">
				<?php _e( 'There was an error in load layouts, please make sure your internet is connected and check if Themify site is available.', 'themify' ); ?>
			</div>
		</div>
            <?php
	}
        
        public function print_template_form(){
            ?>
            
                <script type="text/html" id="tmpl-themify-builder-layout-item">
                    <ul class="tb_layout_lists">
                            <# jQuery.each( data, function( i, e ) { #>
                            <li class="layout_preview_list" data-category="{{{e.category}}}">
                                    <div class="layout_preview" data-id="{{{e.id}}}" data-slug="{{{e.slug}}}" data-group="pre-designed">
                                            <div class="thumbnail"><img src="{{{e.thumbnail}}}" /></div>
                                            <div class="layout_action">
                                                    <div class="layout_title">{{{e.title}}}</div>
                                                    <a class="layout-preview-link themify_lightbox" href="{{{e.url}}}" target="_blank" title="<?php _e( 'Preview', 'themify' ); ?>"><i class="ti-search"></i></a>
                                            </div><!-- /template_action -->
                                    </div><!-- /template_preview -->
                            </li>
                            <# } ) #>
                    </ul>
		</script>
            <?php
        }

        /**
	 * Get the Builder data for a particular layout
	 *
	 * The builder data is sent via JavaScript (themify-builder-app.js)
	 *
	 * @return array|WP_Error
	 */
	public function get_builder_data( $slug ) {
		if( isset( $_POST['builder_data'] ) ) {
			return json_decode( stripslashes_deep( $_POST['builder_data'] ), true );
		} else {
			return new WP_Error( 'fail', __( 'Failed to get Builder data.', 'themify' ) );
		}
	}
}

/**
 * Adds Builder layouts bundled with themes
 *
 * These layouts should be placed in /builder-layouts directory inside the theme's root folder
 *
 * @since 2.0.0
 */
class Themify_Builder_Layouts_Provider_Theme extends Themify_Builder_Layouts_Provider {

	public function get_id() {
		return 'theme';
	}

	public function get_label() {
		return __( 'Theme', 'themify' );
	}

	/**
	 * Get a list of layouts from /builder-layouts/layouts.php file inside the theme
	 *
	 * @return array
	 */
	public function get_layouts() {
		return $this->get_layouts_from_file( get_template_directory() . '/builder-layouts/layouts.php' );
	}

	/**
	 * Get the Builder data from a file in /builder-layouts directory in the theme
	 *
	 * @return array|WP_Error
	 */
	public function get_builder_data( $slug ) {
		$file = get_template_directory() . '/builder-layouts/' . $slug;
		return $this->get_builder_data_from_file( $file );
	}
}