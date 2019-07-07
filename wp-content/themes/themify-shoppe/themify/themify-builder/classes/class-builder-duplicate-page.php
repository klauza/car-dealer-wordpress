<?php
/**
 * Builder Duplicate API
 *
 * This class provide api to duplicate post or page including the builder data.
 * 
 *
 * @package    Themify_Builder
 * @subpackage Themify_Builder/classes
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Duplicate builder class.
 *
 * Main class to handle duplicate post/page with it's builder data.
 *
 *
 * @package    Themify_Builder
 * @subpackage Themify_Builder/classes
 * @author     Themify
 */
class ThemifyBuilderDuplicatePage {
	
	/**
	 * Define new url.
	 * 
	 * @access public
	 * @var string $new_url
	 */
	public $new_url = '';

	/**
	 * Whether return edit link or permalink
	 * default = false
	 * 
	 * @access public
	 * @var int $edit_link
	 */
	public $edit_link = false;
        
	/**
	 * Class Constructor.
	 * 
	 * @access public
	 */
	public function __construct() {
		// Actions
		add_action( 'init', array( $this, 'init' ), 10 );
		add_action( 'admin_init', array( $this, 'init' ), 10 );
		add_action('wp_ajax_tb_duplicate_page', array($this, 'duplicate_page_ajaxify'), 10);
	}

	/**
	 * Init function.
	 * 
	 * @access public
	 */
	public function init() {
            add_action( 'themify_builder_duplicate', array( $this, 'duplicate_data' ), 10, 2 );
	}
        
        
	/**
	 * Duplicate page
	 */
	function duplicate_page_ajaxify() {
		check_ajax_referer('tb_load_nonce', 'tb_load_nonce');
		$post_id = (int) $_POST['postid'];
		$post = get_post($post_id);
		if( is_object($post) ) {
			$this->edit_link = json_decode( $_POST['tb_is_admin'] );
			$this->duplicate($post);
			echo $this->new_url;
		}
		wp_die();
	}


	/**
	 * Perform duplicating post/page.
	 * 
	 * @access public
	 * @param object $post
	 * @param string $status
	 * @param string $parent_id
	 * @return int
	 */
	public function duplicate( $post, $status = '', $parent_id = '' ) {
                // We don't want to clone revisions
		if ( $post->post_type === 'revision' ){
                    return;
                }
                
		$prefix = $suffix = '';
                
		if ( $post->post_type !== 'attachment' ) {
			$suffix = ' Copy';
		}
		$new_post_author = wp_get_current_user();

		$new_post = array(
			'menu_order' => $post->menu_order,
			'comment_status' => $post->comment_status,
			'ping_status' => $post->ping_status,
			'post_author' => $new_post_author->ID,
			'post_content' => $post->post_content,
			'post_excerpt' => $post->post_excerpt,
			'post_mime_type' => $post->post_mime_type,
			'post_parent' => $new_post_parent = empty($parent_id)? $post->post_parent : $parent_id,
			'post_password' => $post->post_password,
			'post_status' => $new_post_status = (empty($status))? $post->post_status: $status,
			'post_title' => $prefix.$post->post_title.$suffix,
			'post_type' => $post->post_type
		);

		$new_post_id = wp_insert_post( $new_post );
		// apply hook to duplicate action
                do_action( 'themify_builder_duplicate', $new_post_id, $post );
                
		delete_post_meta( $new_post_id, '_themify_builder_dp_original' );
		add_post_meta( $new_post_id, '_themify_builder_dp_original', $post->ID );

		// If the copy is published or scheduled, we have to set a proper slug.
		if ( $new_post_status === 'publish' || $new_post_status === 'future' ) {
			$post_name = wp_unique_post_slug( $post->post_name, $new_post_id, $new_post_status, $post->post_type, $new_post_parent );
			$new_post = array();
			$new_post['ID'] = $new_post_id;
			$new_post['post_name'] = $post_name;

			// Update the post into the database
			wp_update_post( $new_post );
		}

		// check if admin
		if ( $this->edit_link ) {
			$this->new_url = get_edit_post_link( $new_post_id );
		} else {
			// set new url
			$this->new_url = $post->post_type === 'page' ? get_page_link( $new_post_id ) : get_permalink( $new_post_id );
		}
		return $new_post_id;
	}

	/**
	 * Duplicate custom fields / post meta.
	 * 
	 * @access public
	 * @param int $new_id
	 * @param object $post
	 */
	public function duplicate_postmeta( $new_id, $post ) {
		$post_meta_keys = get_post_custom_keys( $post->ID );
		if ( empty( $post_meta_keys ) ){
                    return;
                }
		$meta_keys = $post_meta_keys;
                global $ThemifyBuilder, $ThemifyBuilder_Data_Manager;
		foreach ( $meta_keys as $meta_key ) {
			if( $meta_key === '_themify_builder_settings_json' ) {
				$builder_data = $ThemifyBuilder->get_builder_data( $post->ID ); // get builder data from original post
                                $ThemifyBuilder_Data_Manager->save_data( $builder_data, $new_id ); // save the data for the new post
			} else {
				$meta_values = get_post_custom_values( $meta_key, $post->ID );
				foreach ( $meta_values as $meta_value ) {
					$meta_value = maybe_unserialize( $meta_value );
					update_post_meta( $new_id, $meta_key, $meta_value );
				}
			}
		}
	}

	/**
	 * Duplicate categories and custom taxonomies
	 * 
	 * @access public
	 * @param int $new_id
	 * @param object $post
	 */
	public function duplicate_taxonomies( $new_id, $post ) {
		global $wpdb;
		if ( isset( $wpdb->terms ) ) {
			// Clear default category (added by wp_insert_post)
			wp_set_object_terms( $new_id, NULL, 'category' );

			$post_taxonomies = get_object_taxonomies( $post->post_type );
			$taxonomies = $post_taxonomies;
			foreach ( $taxonomies as $taxonomy ) {
				$post_terms = wp_get_object_terms( $post->ID, $taxonomy, array( 'orderby' => 'term_order' ) );
				$terms = array();
                                $terms_count = count( $post_terms );
				for ( $i=0; $i < $terms_count; ++$i ) {
					$terms[] = $post_terms[ $i ]->slug;
				}
				wp_set_object_terms( $new_id, $terms, $taxonomy );
			}
		}
	}

	/**
	 * Duplicate attachment data entries
	 * 
	 * @access public
	 * Actual files does not copied
	 * @param int $new_id
	 * @param object $post
	 */
	public function duplicate_attachment( $new_id, $post ) {
		// get children
		$children = get_posts( array( 'post_type' => 'any', 'numberposts' => -1, 'post_status' => 'any', 'post_parent' => $post->ID ) );
		// clone old attachments
		foreach ( $children as $child ) {
			if ( $child->post_type === 'attachment' || $child->post_type===$post->post_type){
                            continue;
                        }
			$this->duplicate( $child, '', $new_id );
		}
	}
        
        public function duplicate_data($new_id, $post ){
            $this->duplicate_postmeta($new_id, $post);
            $this->duplicate_taxonomies($new_id, $post);
            $this->duplicate_attachment($new_id, $post);
        }
}

$GLOBALS['themifyBuilderDuplicate'] = new ThemifyBuilderDuplicatePage();