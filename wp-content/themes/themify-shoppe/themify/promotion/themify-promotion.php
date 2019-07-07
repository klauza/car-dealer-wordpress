<?php

function themify_admin_promotion() {
	$theme = wp_get_theme();
	
	/**
	 * Add submenu entry that opens a new window with featured themes
	 */
	call_user_func( 'add_submenu_page', 'themify', $theme->display( 'Name' ), __( 'More Themes', 'themify' ), 'manage_options', 'more_themes', 'themify_more_themes' );
	
}
add_action( 'admin_menu', 'themify_admin_promotion', 12 );

function themify_more_themes() {
	if ( ! current_user_can( 'manage_options' ) )
		wp_die( __( 'You do not have sufficient permissions to update this site.', 'themify' ) );
	
	include_once THEMIFY_DIR . '/promotion/template.php';
}