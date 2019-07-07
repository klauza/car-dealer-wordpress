<?php
/**
 * The file that enable builder revisions.
 *
 * Themify_Builder_Revisions class provide hooks and filter to WP Revisions API
 * This enable builder being tracked by WP Revisions and able to restore
 * the revision for builder.
 * 
 *
 * @package    Themify_Builder
 * @subpackage Themify_Builder/classes
 */

/**
 * The Builder Revision class.
 *
 * This is used to handle all revisions operation and method.
 *
 *
 * @package    Themify_Builder
 * @subpackage Themify_Builder/classes
 * @author     Themify
 */
class Themify_Builder_Revisions {
        
	/**
	 * Builder instance.
	 *
	 * @access   private
	 * @var      object    $builder
	 */
	private $builder;

	/**
	 * Builder meta key.
	 * 
	 * @access private
	 * @var string $builder_meta_key
	 */
	private $builder_meta_key;

	/**
	 * Constructor.
	 * 
	 * @param object Themify_Builder $builder 
	 */
	public function __construct( Themify_Builder $builder ) {
                $this->builder = $builder;
		$this->builder_meta_key = $GLOBALS['ThemifyBuilder_Data_Manager']->meta_key;

		$ajax_events = array(
			'load_revision_lists' => false,
			'save_revision' => false,
			'restore_revision_page' => false,
			'delete_revision' => false
		);

		foreach ( $ajax_events as $ajax_event => $nopriv ) {
			add_action( 'wp_ajax_tb_' . $ajax_event, array( $this, $ajax_event ) );

			if ( $nopriv ) {
				add_action( 'wp_ajax_nopriv_tb_' . $ajax_event, array( $this, $ajax_event ) );
			}
		}

		add_action( 'save_post', array( $this, 'save_post_revision' ), 10, 2 );
		add_filter('_wp_post_revision_fields', array( $this, 'post_revision_fields'), 10, 1);
		add_filter('_wp_post_revision_field__themify_builder_settings_json', array( $this, 'post_revision_field'), 10, 2 );
		add_action( 'wp_restore_post_revision', array( $this, 'restore_revision' ), 10, 2 );
	}

	/**
	 * Ajax Get all post revisions list.
	 * 
	 * @access public
	 */
	public function load_revision_lists() {
		check_ajax_referer('tb_load_nonce', 'tb_load_nonce');

		$post_id = (int) $_POST['postid'];
		$revisions = wp_get_post_revisions( $post_id );
		$can_edit_post = $post_id===0?true:current_user_can( 'edit_post', $post_id );
                include THEMIFY_BUILDER_INCLUDES_DIR . '/themify-builder-revision-list.php';
		wp_die();
	}

	/**
	 * Ajax save revision.
	 * 
	 * @access public
	 */
	public function save_revision() {
		check_ajax_referer('tb_load_nonce', 'tb_load_nonce');

		$post_id = (int) $_POST['postid'];
		$post = get_post( $post_id );
		$rev_comment = sanitize_text_field( $_POST['rev_comment'] );

		if ( ! current_user_can( 'edit_post', $post_id ) )
			wp_send_json_error( esc_html__( 'Error. You do not have access to save revision.', 'themify' ) );

		if ( ! wp_revisions_enabled( $post ) ) 
			wp_send_json_error( esc_html__( 'Error. The revision is not enable in this post or has been reach the revision post limit.', 'themify' ) );

		if ( is_object( $post ) ){
			$post = get_object_vars( $post );
                }
		unset( $post['post_modified'],$post['post_modified_gmt'] );
		$new_revision_id = _wp_put_post_revision( $post );
		if ( ! is_wp_error( $new_revision_id ) ) {
			update_metadata( 'post', $new_revision_id, '_builder_custom_rev_comment', $rev_comment );
			wp_send_json_success( $new_revision_id );
		} else {
			wp_send_json_error( esc_html__( 'Cannot save revision, please try again.', 'themify' ) );
		}
                wp_die();
	}

	/**
	 * Hook themify builder field to revisions fields.
	 * 
	 * @access public
	 * @param array $fields 
	 * @return array
	 */
	public function post_revision_fields( $fields ) {
		$fields[ $this->builder_meta_key ] = esc_html__( 'Themify Builder', 'themify' );
		return $fields;
	}

	/**
	 * Render the builder output in revision compare slider.
	 * 
	 * @access public
	 * @param string $value 
	 * @param string $field 
	 * @return string
	 */
	public function post_revision_field( $value, $field ) {
		global $revision;
		if( is_object( $revision ) ) {
			$data = stripslashes_deep( json_decode( $value, true ) );
			if (!empty( $data ) && is_array( $data ) ) {
                            return Themify_Builder_Component_Base::retrieve_template('builder-output.php', array('builder_output' => $data, 'builder_id' => $revision->ID), '', '', false);
                        }
		}
	}

	/**
	 * Hook to restore revision.
	 * 
	 * @access public
	 * @param int $post_id 
	 * @param int $revision_id 
	 */
	public function restore_revision( $post_id, $revision_id ) {
		$builder_data  = get_metadata( 'post', $revision_id, $this->builder_meta_key, true );
		$builder_data = $GLOBALS['ThemifyBuilder_Data_Manager']->construct_data( $builder_data, $post_id );

		if ( false !== $builder_data ){
                    update_post_meta( $post_id, $this->builder_meta_key, $builder_data );
                }
	}

	/**
	 * Ajax restore revision.
	 * 
	 * @access public
	 */
	public function restore_revision_page() {
		check_ajax_referer('tb_load_nonce', 'tb_load_nonce');

		$rev_id = (int) $_POST['revid'];
		$revision = wp_get_post_revision( $rev_id );

		if ( ! current_user_can( 'edit_post', $revision->post_parent ) ){
                    wp_send_json_error( esc_html__( 'Error. You do not have access to restore revision.', 'themify' ) );
                    return;
                }

		if ( $revision ) {
			$builder_data  = get_metadata( 'post', $revision->ID, $this->builder_meta_key, true );
			if ( false !== $builder_data ) {
                                $builder_data = json_decode($builder_data,true);
                                $response = array();
				$response = $GLOBALS['ThemifyBuilder_Data_Manager']->save_data( $builder_data, $revision->post_parent,'revision' );
                                $response['status'] = 'success';
                                $response['builder_data'] = $builder_data;
				wp_send_json( $response );
			} else {
				wp_send_json_error( esc_html__( 'Cannot restore this revision. builder data not founded.', 'themify' ) );
			}
		} else {
			wp_send_json_error( esc_html__( 'Revision post is not found or invalid ID', 'themify' ) );
		}
                wp_die();
	}

	/**
	 * Ajax delete revision.
	 * 
	 * @access public
	 * @return json
	 */
	public function delete_revision() {
		check_ajax_referer('tb_load_nonce', 'tb_load_nonce');	
		$rev_id = (int) $_POST['revid'];
		$revision = wp_get_post_revision( $rev_id );
		if ( ! current_user_can( 'edit_post', $revision->post_parent ) ){
                    wp_send_json_error( esc_html__( 'Error. You do not have access to delete revision.', 'themify' ) );
                    return;
                }

		$delete = wp_delete_post_revision( $rev_id );
		if ( ! is_wp_error( $delete ) ) {
			wp_send_json_success( $rev_id );
		} else {
			wp_send_json_error( esc_html__( 'Unable to delete this revision, please try again!', 'themify' ) );
		}
                wp_die();
	}

	/**
	 * Save builder on save_post hook
	 * 
	 * @access public
	 * @param int $post_id 
	 * @param object $post 
	 */
	public function save_post_revision( $post_id, $post ) {
		$parent_id = wp_is_post_revision( $post_id );

		if ( $parent_id ) {
			$parent  = get_post( $parent_id );
			$builder_data = $this->builder->get_builder_data( $parent->ID );
			$builder_data = $GLOBALS['ThemifyBuilder_Data_Manager']->construct_data( $builder_data, $parent->ID );

			if ( false !== $builder_data ){
                            update_metadata( 'post', $post_id, $this->builder_meta_key, $builder_data );
                        }
		}
	}

	/**
	 * Check if revision has builder data.
	 * 
	 * @access public
	 * @param int $post_id 
	 * @return boolean
	 */
	public function check_has_builder( $post_id ) {
		$builder_data  = get_metadata( 'post', $post_id, $this->builder_meta_key, true );
		return ! empty( $builder_data );
	}
}