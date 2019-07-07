<?php

/**
 * Menu Icons feature
 * 
 * Allows adding custom icons to WordPress menu items.
 * 
 * @package ThemifyFlow
 * @since 1.6.8
 */
class Themify_Menu_Icons {


	public static function get_instance() {
            static $instance = NULL;
            if($instance===null){
                $instance = new self;
            }
            return $instance;
	}

	/**
	 * Setup menu icon functionality
	 *
	 * @since 1.6.8
	 */
	private function __construct() {
		if( is_admin() ) {
			add_filter( 'wp_edit_nav_menu_walker', array( $this, 'wp_edit_nav_menu_walker' ) );
			add_action( 'wp_nav_menu_item_custom_fields', array( $this, 'wp_nav_menu_item_custom_fields' ), 12, 4 );
			add_action( 'wp_update_nav_menu_item', array( $this, 'wp_update_nav_menu_item' ), 10, 3 );
			add_action( 'delete_post', array( $this, 'delete_post' ), 1, 3 );
		} else {
			add_filter( 'wp_nav_menu_args', array( $this, 'wp_nav_menu_args' ) );
			add_filter( 'wp_nav_menu', array( $this, 'wp_nav_menu' ) );
		}
	}

	/**
	 * Start looking for menu icons
	 */
	function wp_nav_menu_args( $args ) {
		add_filter( 'the_title', array( $this, 'the_title' ), 10, 2 );
		return $args;
	}

	/**
	 * The menu is rendered, we longer need to look for menu icons
	 */
	function wp_nav_menu( $nav_menu ) {
		remove_filter( 'the_title', array( $this, 'the_title' ), 10, 2 );
		return $nav_menu;
	}

	/**
	 * Setup custom walker for Nav_Menu_Edit
	 *
	 * @since 1.6.8
	 */
	function wp_edit_nav_menu_walker( $walker ) {
		if( class_exists( 'Walker_Nav_Menu_Edit' ) ) {
			if( ! class_exists( 'Themify_Walker_Nav_Menu_Edit' ) ) {
				include_once THEMIFY_DIR . '/class-themify-walker-nav-menu-edit.php';
			}
			return 'Themify_Walker_Nav_Menu_Edit';
		}

		return $walker;
	}

	/**
	 * Save the icon meta for a menu item. Also removes the meta entirely if the field is cleared.
	 *
	 * @since 1.6.8
	 */
	function wp_update_nav_menu_item( $menu_id, $menu_item_db_id, $args ) {
		if( isset( $_POST['menu-item-icon'] ) && isset( $_POST['menu-item-icon'][$menu_item_db_id] ) ) {
			$meta_key = '_menu_item_icon';
			$meta_value = $this->get_menu_icon( $menu_item_db_id );
			$menu_item_icon =  $_POST['menu-item-icon'][$menu_item_db_id];
			$new_meta_value = stripcslashes( $menu_item_icon );

			if ( $new_meta_value && '' == $meta_value )
				add_post_meta( $menu_item_db_id, $meta_key, $new_meta_value, true );
			elseif ( $new_meta_value && $new_meta_value != $meta_value )
				update_post_meta( $menu_item_db_id, $meta_key, $new_meta_value );
			elseif ( '' == $new_meta_value && $meta_value )
				delete_post_meta( $menu_item_db_id, $meta_key, $meta_value );
		}
	}

	/**
	 * Clean up the icon meta field when a menu item is deleted
	 *
	 * @param int $post_id
	 *
	 * @since 1.6.8
	 */
	function delete_post( $post_id ) {
		if( is_nav_menu_item( $post_id ) ) {
			delete_post_meta( $post_id, '_menu_item_icon' );
		}
	}

	/**
	 * Display the icon picker for menu items in the backend
	 *
	 * @since 1.6.8
	 */
	function wp_nav_menu_item_custom_fields( $item_id, $item, $depth, $args ) {
		$saved_meta = $this->get_menu_icon( $item_id );
		if( ! wp_style_is( 'themify-font-icons-css' ) ) {
			wp_enqueue_style( 'themify-font-icons-css', THEMIFY_URI . '/fontawesome/css/font-awesome.min.css', array(), THEMIFY_VERSION );
		}
	?> 
		<p class="field-icon description description-thin">
			<label for="edit-menu-item-icon-<?php echo esc_attr( $item_id ); ?>">
				<?php _e( 'Icon', 'themify' ) ?><br/>
				<input type="text" name="menu-item-icon[<?php echo esc_attr( $item_id ); ?>]" id="edit-menu-item-icon-<?php echo esc_attr( $item_id ) ?>" size="8" class="edit-menu-item-icon themify_field_icon" value="<?php echo esc_attr( $saved_meta ); ?>">
				<a class="button button-secondary hide-if-no-js themify_fa_toggle" href="#" data-target="#edit-menu-item-icon-<?php echo esc_attr( $item_id ) ?>"><?php _e( 'Insert Icon', 'themify' ); ?></a>
			</label>
		</p>
	<?php }

	/**
	 * Append icon to a menu item
	 *
	 * @since 1.6.8
	 *
	 * @param string $title
	 * @param string $id
	 *
	 * @return string
	 */
	function the_title( $title, $id = '' ) {
		if ( '' != $id && $icon = $this->get_menu_icon( $id ) ) {
                    $title = '<i class="' . esc_attr( themify_get_icon( $icon ) ) . '"></i> ' . $title;
			
		}
		return $title;
	}

	/**
	 * Returns the icon name chosen for a given menu item
	 *
	 * @return string|null
	 * @since 1.6.8
	 */
	function get_menu_icon( $item_id ) {
		return get_post_meta( $item_id, '_menu_item_icon', true );
	}
}
Themify_Menu_Icons::get_instance();