<?php 
	/**
		* Plugin main page
		*
		* @package     Wow Plugin
		* @copyright   Copyright (c) 2018, Dmytro Lobov
		* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
		* @since       1.0
	*/
	
	if ( ! defined( 'ABSPATH' ) ) exit;
	
	require_once plugin_dir_path( __FILE__ ) . 'tools-data-base.php';
	
	$current_tab = ( isset( $_REQUEST["tab"] ) ) ? sanitize_text_field( $_REQUEST["tab"] ) : 'list';
	
	$tabs = apply_filters( 'wp_coder_menu', array(
		'list'      => __( 'List', 'wpcoder' ), 	
		'add_new'   => __( 'Add new', 'wpcoder' ),
		'items'     => __( 'Items', 'wpcoder' ),
		'extension' => __( 'Extension', 'wpcoder' ),
		'support'   => __( 'Support', 'wpcoder' ),
	) );	
?>

<style>
	.nav-tab-wrapper {
		margin-bottom: 20px;
	}
	.wrap .nav-tab-wrapper .page-title-action {
    top: 4px;
    margin-left: 5px;
}
.wow-facebook::after {	
	font-family: dashicons;		
	content: "\f305";
	margin-left: 5px;	
}
.dashicons {
	color: #e95645;
	margin-right: 10px;
}
.ideas {
	font-size: 14px;
	font-weight: 600;
}
</style>

<div class="wrap">
	<h1 class="wp-heading-inline"><?php echo $this->plugin_name; ?> v. <?php echo $this->plugin_version; ?></h1>
	<a href="?page=<?php echo $this->plugin_slug; ?>&tab=add_new" class="page-title-action"><?php _e( 'Add New', 'wpcoder' ); ?></a> <a href="https://www.facebook.com/wowaffect/" class="page-title-action wow-facebook"  target="_blank">Stay in touch</a> 
	<hr class="wp-header-end">
	<p class="ideas">
		<span class="dashicons dashicons-megaphone"></span> <?php printf( __( 'We want to hear your Ideas about improving the plugin. <a href="%1$s" target="_blank">Send Message</a>!', $this->plugin_pref ), 'https://wow-estore.com/item/wp-coder-extension/' ); ?>		 
	</p>
	<?php
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $tabs as $tab => $name ) {
			$class = ( $tab === $current_tab ) ? ' nav-tab-active' : '';
			if ( $tab == 'add_new' ) {
				$action = ( isset( $_REQUEST["act"] ) ) ? sanitize_text_field( $_REQUEST["act"] ) : '';
				if ( !empty( $action ) && $action == 'update' ) {
					echo '<a class="nav-tab' .esc_attr( $class ) . '" href="?page=' . $this->plugin_slug . '&tab=' . esc_attr( $tab ) . '">' . __( 'Update', 'leadgeneration' ) . ' #' . absint( $_REQUEST["id"] ) . '</a>';
				}
				else {
					echo '<a class="nav-tab' .esc_attr( $class ) . '" href="?page=' . $this->plugin_slug . '&tab=' . esc_attr( $tab ) . '">' . esc_attr( $name ) . '</a>';
				}
			}
			else {
				echo '<a class="nav-tab' .esc_attr( $class ) . '" href="?page=' . $this->plugin_slug . '&tab=' . esc_attr( $tab ) . '">' . esc_attr( $name ) . '</a>';
			}		
			
		}
		echo '</h2>';
		
		$file = apply_filters( 'wp_coder_file', $current_tab );
		include_once ( $file . '.php' );
		
	?>
</div>


