<?php
/**
 * Codes to run only once after theme activation
 *
 * @package Themify
 */

if(!function_exists('themify_builder_fix_escaped_slashes')){
    function themify_builder_fix_escaped_slashes() {
            if( ! function_exists( 'themify_get_flag' ) || themify_get_flag( 'builder_escaped_slashes_fix' ) )
                    return;

            global $wpdb;
            $wpdb->query( "UPDATE {$wpdb->postmeta} SET meta_value = Replace( meta_value, '\\\/', '/' ) WHERE meta_key = '_themify_builder_settings_json'" );
            themify_set_flag( 'builder_escaped_slashes_fix' );
    }
    add_action( 'init', 'themify_builder_fix_escaped_slashes' );
}