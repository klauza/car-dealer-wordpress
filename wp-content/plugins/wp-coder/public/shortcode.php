<?php 
	/**
		* Shortcode
		*
		* @package     Wow_Plugin
		* @copyright   Copyright (c) 2018, Dmytro Lobov
		* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
		* @since       1.0
	*/
	namespace wpcoder;
	
	if ( ! defined( 'ABSPATH' ) ) exit;
	
	extract( shortcode_atts ( array('id' => "" ), $atts ) );		
	global $wpdb;
	$table = $wpdb->prefix . 'wow_' . $this->plugin_pref;    
	$sSQL = $wpdb->prepare( "select * from $table WHERE id = %d", $id );
	$result = $wpdb->get_results( $sSQL ); 	
	if ( count( $result ) > 0 ) {
		foreach ($result as $key => $val) {
			$param = unserialize($val->param);
			ob_start();
			include( 'partials/public.php' );			
			$content = ob_get_contents();
			ob_end_clean();
			$path_style = $this->basedir . 'style-' . $val->id . '.css';
			$path_script = $this->basedir . 'script-' . $val->id . '.js';			
			$file_style = $this->plugin_dir . 'admin/partials/generator/style.php';
			$file_script = $this->plugin_dir . 'admin/partials/generator/script.php';
			if ( file_exists( $file_style ) && !file_exists( $path_style ) ) {
				ob_start();
				include ( $file_style );
				$content_style = ob_get_contents();
				ob_end_clean();
				file_put_contents( $path_style, $content_style );
			}			
			if ( file_exists( $file_script ) && !file_exists( $path_script ) ) {
				ob_start();
				include ( $file_script );
				$content_script = ob_get_contents();
				$script_packer =  __NAMESPACE__ . '\\JavaScriptPacker';
				$packer = new $script_packer( $content_script, 'Normal', true, false );
				$packed = $packer->pack();
				ob_end_clean();
				file_put_contents( $path_script, $packed );				
			}			
			
			$param['item_user'] = !empty( $param['item_user'] ) ? $param['item_user'] : '1';
			if ( $param['item_user'] == 1 ) {
				$user = true;
			}
			else if ( $param['item_user'] == 2 ) {
				if ( is_user_logged_in() ) {
					if ( $param['user_role'] == 'all' ){
						$user = true;
					}
					else {
						$current_user = wp_get_current_user();
						if ( $param['user_role'] == $current_user->roles[0] ){
							$user = true;										
						}
						else {
							$user = false;										
						}									
					}								
				}
				else {
					$user = false;
				}							
			}
			else if ( $param['item_user'] == 3 ) {
				$user = !is_user_logged_in();
			}
			
			
			if ( !empty($param['depending_language'] ) ) {
				$item_lang = $param['lang'];
				$site_lang = get_locale();				
				if (substr($site_lang, 0, 2) == $item_lang) {
					$lang = true;
				}
				else {
					$lang = false;
				}
			}
			else if ( empty( $param['depending_language'] ) ) {
				$lang = true;
			}
			
			if ($user == true && $lang == true) {
				echo $content;
				$time = !empty( $param['time'] ) ? $param['time'] : '';
					
				$count_include = !empty( $param['include'] ) ? count( $param['include'] ) : 0;
				if ( $count_include > 0 ) {
					for ( $i = 0; $i < $count_include; $i++ ) {
						if( $param['include'][$i] == 'css' && !empty( $param['include_file'][$i] ) ) {						
							wp_enqueue_style( $this->plugin_slug . '-' . $val->id . '-css-' . $i, $param['include_file'][$i], array(), $this->plugin_version );
						}
						elseif( $param['include'][$i] == 'js' && !empty( $param['include_file'][$i] ) ) {
							wp_enqueue_script( $this->plugin_slug . '-' . $val->id . '-js-' . $i, $param['include_file'][$i], array( 'jquery' ), $this->plugin_version );
						}					
					}
				}
				
				if ( file_exists( $path_style ) && !empty( $param['content_css'] ) ) {
					wp_enqueue_style( $this->plugin_slug . '-style-' . $val->id, $this->baseurl . 'style-' . $val->id  . '.css', array(), $time);	
				}
				if ( file_exists( $path_script ) && !empty( $param['content_js'] ) ) {					
					wp_enqueue_script( $this->plugin_slug . '-script-' . $val->id, $this->baseurl . 'script-' . $val->id . '.js', array( 'jquery' ), $time );
				}		
				
			}
		}		
	} 	
return ;	