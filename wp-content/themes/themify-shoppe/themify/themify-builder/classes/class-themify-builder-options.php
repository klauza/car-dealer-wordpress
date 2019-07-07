<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Themify_Builder_Options' ) ) {
	
	/**
	 * Class Builder Options
	 */
	class Themify_Builder_Options {
		
		protected $option_name = 'themify_builder_setting';
		protected $option_value = array();
		protected $current_tab = '';
		public static $slug = 'themify-builder';

		/**
		 * Constructor
		 */
		public function __construct() {
			if ( is_admin() ){
				add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
				add_action( 'admin_init', array( $this, 'page_init' ) );
				add_action( 'admin_enqueue_scripts', array( $this, 'load_enqueue_scripts' ) );
			}
			else{
				add_action( 'wp_head', array( $this, 'show_custom_css' ) );
			}
			add_filter( 'themify_top_pages', array( $this, 'queue_top_pages' ) );
			add_filter( 'themify_pagenow', array( $this, 'queue_pagenow' ) );
		}
		
		public function add_plugin_page(){
			if( Themify_Builder_Model::hasAccess() ){
				// This page will be under "Settings"
				$name = Themify_Builder_Model::is_premium()?__( 'Themify Builder', 'themify' ):__( 'Builder Lite', 'themify' );
				add_menu_page( $name, $name, 'manage_options', self::$slug, array( $this, 'create_admin_page'), plugins_url( THEMIFY_BUILDER_NAME.'/themify/img/favicon.png' ), 50 );
				add_submenu_page( self::$slug, __( 'Settings', 'themify' ), __( 'Settings', 'themify' ), 'manage_options', self::$slug );
				
				if (Themify_Builder_Model::is_premium() && Themify_Builder_Model::builder_check()) {
					
					add_submenu_page ( 'themify-builder', __( 'Builder Layouts', 'themify' ), __( 'Builder Layouts', 'themify' ), 'edit_posts', 'edit.php?post_type=tbuilder_layout' );
					add_submenu_page( 'themify-builder', __( 'Builder Layout Parts', 'themify' ), __( 'Builder Layout Parts', 'themify' ), 'edit_posts', 'edit.php?post_type=tbuilder_layout_part' );
					
				}
			   
			}
		}
		function redirect(){
			if(!empty($_GET['page'])){
				wp_redirect('edit.php?post_type='.$_GET['page']);
			}
		}

		public function create_admin_page() {
			$this->option_value = get_option( $this->option_name );

			if ( isset( $_GET['action'] ) ) {
				$action = 'upgrade';
				themify_builder_updater();
			}

			?>
		<div class="wrap">
			<h2><?php _e('Themify Builder', 'themify') ?></h2>			
			<form method="post" action="options.php">
				<div class="icon32" id="icon-options-general"><br /></div><h2 class="nav-tab-wrapper themify-nav-tab-wrapper">
					<input type="hidden" name="<?php echo esc_attr( $this->option_name ); ?>[tabs_active]" value="<?php echo esc_attr( $this->current_tab ); ?>">
					<?php
						$tabs = array(
							'builder' => __( 'Settings', 'themify' ),
							'role_access' => __( 'Role Access', 'themify' ),
							'image_setting' => __( 'Image Script', 'themify' ),
							'custom_css' => __('Custom CSS', 'themify'),
							'builder_settings'=>__('Google Maps','themify'),
							'regenerate_css'=>__('Regenerate CSS','themify')
						);
						$tabs = apply_filters('themify_builder_settings_tab_array', $tabs);

						foreach ( $tabs as $name => $label ) {
							echo '<a href="' . admin_url( 'admin.php?page=' . self::$slug. '&tab=' . $name ) . '" class="nav-tab ';
							if( $this->current_tab === $name ) echo 'nav-tab-active';
							echo '">' . $label . '</a>';
						}
					?>
				</h2>
				<?php
					// This prints out all hidden setting fields
					settings_fields( 'themify_builder_option_group' );	
					do_settings_sections( self::$slug );
				?>
				<?php  
					if($this->current_tab!=='builder_upgrade'){
						submit_button();
					}
					else{
						include( THEMIFY_BUILDER_DIR . '/about.php' );
						?>
						<a class="button button-primary" href="//themify.me/builder" target="_blank"><?php _e('Upgrade Builder','themify')?></a>
						<?php 
					}
				?>
			</form>

			<!-- alerts -->
			<div class="alert"></div> 
			<!-- /alerts -->
			
			<!-- prompts -->
			<div class="prompt-box">
				<div class="show-login">
					<form id="themify_update_form" method="post" action="<?php echo admin_url( 'admin.php?page=' . self::$slug . '&action=upgrade&login=true' ); ?>">
					<p class="prompt-msg"><?php _e('Enter your Themify login info to upgrade', 'themify'); ?></p>
					<p><label><?php _e('Username', 'themify'); ?></label> <input type="text" name="username" class="username" value=""/></p>
					<p><label><?php _e('Password', 'themify'); ?></label> <input type="password" name="password" class="password" value=""/></p>
					<input type="hidden" value="true" name="login" />
					<p class="pushlabel"><input name="login" type="submit" value="Login" class="button tb_upgrade_login" /></p>
					</form>
				</div>
				<div class="show-error">
					<p class="error-msg"><?php _e('There were some errors updating the theme', 'themify'); ?></p>
				</div>
			</div>
			<!-- /prompts -->

			<script type="text/javascript">
				function switch_image_field() {
                                    var img_field =jQuery('.img_field').closest('tr'),
                                        size = jQuery('.image_global_size_field').closest('tr');
					if(!jQuery('.disable_img_php').is(':checked')) {
						size.hide();
						img_field.show();
					} else {
                                                size.show();
						img_field.hide();
					}	
				}
				switch_image_field();
				jQuery('.disable_img_php').on('click', switch_image_field);
			</script>
		</div>
		<?php
		}
		
		public function page_init() {		
			register_setting( 'themify_builder_option_group', $this->option_name, array( $this, 'before_save' ) );
			$current_tab = ( empty( $_GET['tab'] ) ) ? 'builder' : sanitize_text_field( urldecode( $_GET['tab'] ) );
			$this->current_tab = $current_tab;

			switch ( $current_tab ) {
				case 'image_setting':
					// image script settings
					add_settings_section(
						'setting_builder_image_section',
						__( 'Image Script Settings', 'themify' ),
						'',
						self::$slug
					);

					add_settings_field(
						'image_script', 
						__('Disable', 'themify'),
						array( $this, 'image_script_field' ), 
						self::$slug,
						'setting_builder_image_section'			
					);

					add_settings_field(
						'image_global_size', 
						__('Default Featured Image Size', 'themify'), 
						array( $this, 'image_global_field' ), 
						self::$slug,
						'setting_builder_image_section'			
					);

				break;

				case 'role_access':

					add_settings_section(
						'setting_builder_role_access_section',
						__( 'Role Access Settings', 'themify' ),
						'',
						self::$slug
					);

					add_settings_field(
						'builder_backend',
						__( 'Builder Backend Role Access', 'themify' ), 
						array( $this, 'role_access_fields' ), 
						self::$slug,
						'setting_builder_role_access_section',
						array( 'mode' => 'backend' )
					);

					add_settings_field(
						'builder_frontend',
						__( 'Builder Frontend Role Access', 'themify' ), 
						array( $this, 'role_access_fields' ), 
						self::$slug,
						'setting_builder_role_access_section',
						array( 'mode' => 'frontend' )
					);

				break;

				case 'custom_css':
					// image script settings
					add_settings_section(
						'setting_builder_custom_css_section',
						__( 'Custom CSS', 'themify' ),
						'',
						self::$slug
					);

					add_settings_field(
						'custom_css', 
						false, 
						array( $this, 'custom_css_field' ), 
						self::$slug,
						'setting_builder_custom_css_section'			
					);
				break;
				case 'builder_settings':
					if(Themify_Builder_Model::is_premium()){
						// image script settings
						add_settings_section(
							'setting_builder_settings_section',
							null,
							'',
							self::$slug
						);

						add_settings_field(
							'google_map', 
							__('Google Map Api Key','themify'), 
							array( $this, 'google_map_api_key_field' ), 
							self::$slug,
							'setting_builder_settings_section'			
						);
					}
				break;
				case 'builder_upgrade':
					if(!Themify_Builder_Model::is_premium()){
						// image script settings
						add_settings_section(
							'setting_builder_upgrade_section',
							'',
							'',
							self::$slug
						);
						add_settings_field(
							'upgrade_field', 
							false, 
							array( $this, 'builder_upgrade' ), 
							self::$slug,
							'setting_builder_upgrade_section'			
						);
					}
				break;
				case 'regenerate_css':
					add_settings_section(
						'setting_builder_regenerate_css_section',
						__( 'Regenerate CSS Files', 'themify' ),
						'',
						self::$slug
					);

					add_settings_field(
						'regenerate_css', 
						__('Regenerate CSS Files','themify'), 
						array( $this, 'regenerate_css_field' ), 
						self::$slug,
						'setting_builder_regenerate_css_section'			
					);
				break;
				default:
					add_settings_section(
						'setting_builder_section',
						__( 'Builder Settings', 'themify' ),
						'',
						self::$slug
					);
						
					add_settings_field(
						'builder_active', 
						__( 'Themify Builder', 'themify' ), 
						array( $this, 'builder_active_field' ), 
						self::$slug,
						'setting_builder_section'			
					);

					if ( Themify_Builder_Model::builder_check() ) {
												
						add_settings_field(
							'builder_cache', 
							__( 'Builder Cache', 'themify' ), 
							array( $this, 'builder_disable_cache' ), 
							self::$slug,
							'setting_builder_section'
						);

						if (!Themify_Builder_Model::is_premium() || TFCache::check_version()) {
							add_settings_field(
								'builder_clear_cache', 
								null, 
								array( $this, 'builder_clear_cache' ), 
								self::$slug,
								'setting_builder_section'
							);
						}
						add_settings_field(
							'builder_minify', 
							__( 'Minified CSS/JS', 'themify' ), 
							array( $this, 'builder_minify' ), 
							self::$slug,
							'setting_builder_section'
						);	
						add_settings_field(
							'builder_shortcuts', 
							__( 'Keyboard Shortcuts', 'themify' ), 
							array( $this, 'builder_disable_shortcuts' ), 
							self::$slug,
							'setting_builder_section'			
						);

						add_settings_field(
							'builder_disable_wp_editor', 
							__( 'Disable WordPress Editor', 'themify' ),
							array( $this, 'builder_disable_wp_editor' ),
							self::$slug,
							'setting_builder_section'
						);

						add_settings_field(
							'builder_google_fonts', 
							__( 'Google Fonts List', 'themify' ), 
							array( $this, 'builder_show_all_google_fonts' ), 
							self::$slug,
							'setting_builder_section'			
						);
												
											   
						add_settings_field(
							'builder_animation', 
							__( 'Animation Effects', 'themify' ), 
							array( $this, 'builder_animation_field' ), 
							self::$slug,
							'setting_builder_section'
						);

						add_settings_field(
							'builder_responsive', 
							esc_html__( 'Responsive Design', 'themify' ), 
							array( $this, 'builder_responsive_field' ), 
							self::$slug,
							'setting_builder_section'
						);
											   
						add_settings_field(
							'builder_excludes', 
							__( 'Exclude Builder Modules', 'themify' ), 
							array( $this, 'builder_exclude_field' ), 
							self::$slug,
							'setting_builder_section'			
						);
					}
				break;
			}

		}

		public function show_custom_css(){
			$settings = get_option( $this->option_name );
			$custom_css = isset( $settings['custom_css-custom_css'] ) ? $settings['custom_css-custom_css'] : false;
			if ( $custom_css ){
				echo PHP_EOL . '<!-- Builder Custom Style -->' . PHP_EOL,
                                    '<style type="text/css">' . PHP_EOL,
                                    $custom_css . PHP_EOL,
                                    '</style>' . PHP_EOL . '<!-- / end builder custom style -->' . PHP_EOL;
			}
		}

		function before_save($input) {
			$active_tabs = $input['tabs_active'];
			$exist_data = get_option( $this->option_name );
			$exist_data = is_array($exist_data) ? $exist_data : array();
			
			foreach($exist_data as $k => $v) {
				if ( strpos( $k, $active_tabs ) !== false ) {
					unset($exist_data[$k]);
				}
			}

			$all = array_merge($exist_data, $input);
			if(class_exists('TFCache')){
				$remove = $all['builder_is_active']!=='enable' || isset($all['builder_disable_cache']);
				TFCache::rewrite_htaccess($remove);
			}
			return $all;
		}
		
		function print_section_builder_info(){
			_e('Enable/Disable Themify Builder', 'themify');
		}
		
		function builder_active_field(){
			$selected = isset( $this->option_value[$this->current_tab.'_is_active'] ) ? $this->option_value[$this->current_tab.'_is_active'] : 'enable';
			?>
			<select class="tb_is_active" name="<?php echo esc_attr( $this->option_name . '['.$this->current_tab.'_is_active]' ); ?>">
				<option value="enable" <?php selected( $selected, 'enable'); ?>><?php _e('Enable', 'themify') ?></option>
				<option value="disable" <?php selected( $selected, 'disable'); ?>><?php _e('Disable', 'themify') ?></option>
			</select>
                        <br/><small style="display:none" class="pushlabel" data-show-if-element=".tb_is_active" data-show-if-value="disable">
                            <?php echo esc_html__( 'WARNING: When Builder is disabled, all Builder content/layout will not appear. They will re-appear once Builder is enabled.', 'themify' ) ?>
                        </small>
                        <?php
		}

		function builder_exclude_field() {
			$modules = Themify_Builder_Model::get_modules( 'all' );
			
			foreach( $modules as $k => $v ) :
				$name = $this->current_tab . '_exclude_module_' . $v['id'];
				$field_name = $this->option_name . '['.$name.']';
				$mod_checked = isset($this->option_value[$name] ) ? $this->option_value[$name] : 0; 
				?>
                                    <p>
                                        <input id="exclude_module_<?php echo $v['id']; ?>" type="checkbox" value="1" name="<?php echo $field_name; ?>" <?php checked( $mod_checked, 1 ); ?>>
                                        <label for="exclude_module_<?php echo $v['id']; ?>"><?php echo sprintf(__('Exclude %s module', 'themify'), $v['name']); ?></label>
                                    </p>
				<?php
			endforeach;
		}

		/**
		 * Disable builder cache field.
		 * 
		 * @since 1.5.4
		 * @access public
		 */
		public function builder_disable_cache() {
			if(!Themify_Builder_Model::is_premium() || TFCache::check_version()){
				$pre = $this->current_tab . '_';
				$disable_cache_status = isset( $this->option_value[$pre.'disable_cache'] ) ? $this->option_value[$pre.'disable_cache'] : '';
				$out = sprintf( '<p><label for="%s"><input type="radio" id="%s" class="builder-disable-cache" name="%s" value="%s" %s> %s %s</label><br/><label for="%s"><input type="radio" id="%s" class="builder-disable-cache" name="%s" value="%s" %s> %s %s</label></p>',
						 $pre . 'disable_cache' ,
						 $pre . 'disable_cache' ,
						 $this->option_name . '[' . $pre . 'disable_cache' . ']' ,
						'disable' ,
						checked( 'disable', $disable_cache_status, false ) . checked( 'on', $disable_cache_status, false ) . checked( '', $disable_cache_status, false ),
						'Disable' ,
						wp_kses_post( __('(disable it if you experience Builder issues and conflicts)', 'themify') ),
						$pre . 'enable_cache' ,
						$pre . 'enable_cache',
						 $this->option_name . '[' . $pre . 'disable_cache' . ']' ,
						'enable' ,
						checked( 'enable', $disable_cache_status, false ),
						 'Enable',
						wp_kses_post( __('(enable it for faster page load)', 'themify') )
				);
				if(!Themify_Builder_Model::is_premium()){
					$out='<div class="tb_lite">'.$out.'</div>';
				}
				echo $out;
			}
			else{
				_e('Your server does not support Builder cache, thus it is disabled. It requires PHP 5.4+','themify');
			}
		}

		/**
		 * Clear builder cache.
		 * 
		 * @since 1.5.4
		 * @access public
		 */
		public function builder_clear_cache() {
			$pre = $this->current_tab . '_';
			$out='<div style="display: none" data-show-if-element=".builder-disable-cache:checked" data-show-if-value="enable" data-sync-row-el="th">';
			$expire =  isset( $this->option_value[$pre.'cache_expiry'] )?$this->option_value[$pre.'cache_expiry']:'';
			$expire = $expire>0?(int)$expire:2;
			$out.=sprintf('<input type="text" class="width2" value="%s" name="%s" />  &nbsp;&nbsp;<span>%s</span>',
					$expire,
					 $this->option_name . '[' . $pre . 'cache_expiry' . ']' ,
					__( 'Expire Cache (days)', 'themify' )
				);
			$out.='<br/><br/>';
			$out.= sprintf( '<a href="#" data-action="themify_clear_all_caches" data-confim="'.__( 'This will clear all builder caches. click ok to continue.', 'themify' ).'" data-clearing-text="%s" data-done-text="%s" data-default-text="%s" data-default-icon="ti-eraser" class="button button-secondary js-clear-builder-cache"> <i class="ti-eraser"></i> <span>%s</span></a><br/><span class="description">%s</span>',
				esc_html__( 'Clearing cache...', 'themify' ),
				esc_html__( 'Done', 'themify' ),
				esc_html__( 'Clear cache', 'themify' ),
				esc_html__( 'Clear cache', 'themify' ),
				esc_html__( 'Clear all Builder cache', 'themify' )
			);
			$out.='</div>';
			if(!Themify_Builder_Model::is_premium()){
				$out='<div class="tb_lite">'.$out.'</div>';
			}
			echo $out;
		}
		
		public function builder_minify(){
			$pre = $this->current_tab . '_';
			$out = sprintf( '<p><label for="%s"><input type="checkbox" id="%s" name="%s"%s> %s</label></p>',
				$pre . 'minified',
				$pre . 'minified',
				 $this->option_name . '[' . $pre . 'minified' . ']' ,
				checked( true, isset( $this->option_value[$pre.'minified'] ), false ),
				wp_kses_post( __( 'Do not use minified Javascript & CSS files', 'themify') )
			);
			echo $out;
		}

		/**
		 * Render checkbox to disable shortcuts.
		 *
		 * @since 1.4.7
		 * @access public
		 */
		function builder_disable_shortcuts() {
			$pre = $this->current_tab . '_';
			$out = sprintf( '<p><label for="%s"><input type="checkbox" id="%s" name="%s"%s> %s</label></p>',
				$pre . 'disable_shortcuts' ,
				$pre . 'disable_shortcuts' ,
				 $this->option_name . '[' . $pre . 'disable_shortcuts' . ']' ,
				checked( true, isset( $this->option_value[$pre.'disable_shortcuts'] ), false ),
				wp_kses_post( __( 'Disable Builder shortcuts (eg. disable shortcut like Cmd+S = save)', 'themify') )
			);
			echo $out;
		}

		function builder_disable_wp_editor() {
			$key = $this->current_tab . '_disable_wp_editor';

			printf( '<p><label for="%1$s"><input type="checkbox" id="%1$s" name="%2$s"%3$s> %4$s</label></p>'
				, $key
				, $this->option_name . '[' . $key . ']'
				, checked( true, isset( $this->option_value[$key] ), false )
				, wp_kses_post( __( 'Disable WordPress editor when Builder is in use', 'themify' ) )
			);
		}

		function builder_show_all_google_fonts() {
			$pre = $this->current_tab . '_';
			$current_value = !empty( $this->option_value[$pre . 'google_fonts'] ) ? $this->option_value[$pre . 'google_fonts'] : 'less';
			$out = sprintf( '<p><label><input type="radio" name="%1$s" value="less" %4$s>%2$s</label></p><p><label><input type="radio" name="%1$s" value="full" %5$s>%3$s</label></p>'
				, $this->option_name . '[' . $pre . 'google_fonts' . ']' 
				, __( 'Show recommended Google Fonts only', 'themify' )
				, __( 'Show all Google Fonts (showing all fonts will take longer to load)', 'themify' )
				, checked( 'less', $current_value, false )
				, checked( 'full', $current_value, false ) );

			echo $out;
		}

		function builder_animation_field() {
			$pre = $this->current_tab . '_animation_';

			
			$output = sprintf('<p><label for="%s" class="label">%s</label><br><select id="%s" name="%s">%s</select></p>',
				 $pre . 'appearance' ,
				esc_html__( 'Appearance Animation', 'themify' ),
				 $pre . 'appearance' ,
				$this->option_name . '[' .$pre . 'appearance]' ,
				sprintf( '<option value="" %s></option>'
					, selected( !empty( $this->option_value[$pre . 'appearance'] )
						? $this->option_value[$pre . 'appearance'] : '', '', false ) ) .
				sprintf( '<option value="mobile" %s>%s</option>'
					, selected( !empty( $this->option_value[$pre . 'appearance'] )
						? $this->option_value[$pre . 'appearance'] : '', 'mobile', false )
					, esc_html__( 'Disable on mobile & tablet', 'themify' ) ) .
				sprintf( '<option value="all" %s>%s</option>'
					, selected( !empty( $this->option_value[$pre . 'appearance'] )
						? $this->option_value[$pre . 'appearance'] : '', 'all', false )
					, esc_html__( 'Disable on all devices', 'themify' ) )
			);
			$output .= sprintf('<p><label for="%s" class="label">%s</label><br><select id="%s" name="%s">%s</select></p>',
				$pre . 'parallax_bg' ,
				esc_html__( 'Parallax Background', 'themify' ),
				$pre . 'parallax_bg' ,
				 $this->option_name . '[' .$pre . 'parallax_bg]' ,
				sprintf( '<option value="" %s></option>'
					, selected( !empty( $this->option_value[$pre . 'parallax_bg'] )
						? $this->option_value[$pre . 'parallax_bg'] : '', '', false ) ) .
				sprintf( '<option value="mobile" %s>%s</option>'
					, selected( !empty( $this->option_value[$pre . 'parallax_bg'] )
						? $this->option_value[$pre . 'parallax_bg'] : '', 'mobile', false )
					, esc_html__( 'Disable on mobile & tablet', 'themify' ) ) .
				sprintf( '<option value="all" %s>%s</option>'
					, selected( !empty( $this->option_value[$pre . 'parallax_bg'] )
						? $this->option_value[$pre . 'parallax_bg'] : '', 'all', false )
					, esc_html__( 'Disable on all devices', 'themify' ) )
			);
			$output .= sprintf('<p><label for="%s" class="label">%s</label><br><select id="%s" name="%s">%s</select></p>',
				$pre . 'parallax_scroll' ,
				esc_html__( 'Float Scrolling', 'themify' ),
				$pre . 'parallax_scroll' ,
				$this->option_name . '[' .$pre . 'parallax_scroll]' ,
				sprintf( '<option value="" %s></option>'
					, selected( !empty( $this->option_value[$pre . 'parallax_scroll'] )
						? $this->option_value[$pre . 'parallax_scroll'] : '', '', false ) ) .
				sprintf( '<option value="mobile" %s>%s</option>'
					, selected( isset( $this->option_value[$pre . 'parallax_scroll'] )
						? $this->option_value[$pre . 'parallax_scroll'] : 'mobile', 'mobile', false )
					, esc_html__( 'Disable on mobile & tablet', 'themify' ) ) .
				sprintf( '<option value="all" %s>%s</option>'
					, selected( !empty( $this->option_value[$pre . 'parallax_scroll'] )
						? $this->option_value[$pre . 'parallax_scroll'] : '', 'all', false )
					, esc_html__( 'Disable on all devices', 'themify' ) )
			);
			$output .= sprintf( '<span class="pushlabel"><small>%s</small></span>', 
				esc_html__( 'If animation is disabled, the element will appear static', 'themify' )
			);

			echo $output;
		}

		/**
		 * Responsive Design Fields.
		 * 
		 * @access public
		 */
		public function builder_responsive_field() {
                        $break_points = themify_get_breakpoints('',true);
			$pre = $this->current_tab . '_responsive_design_';
			$bp_tablet_landscape = ! empty( $this->option_value[ $pre . 'tablet_landscape'] )  ? $this->option_value[ $pre . 'tablet_landscape'] : $break_points['tablet_landscape'][1];
			$bp_tablet = ! empty( $this->option_value[ $pre . 'tablet'] ) ? $this->option_value[ $pre . 'tablet'] : $break_points['tablet'][1];
			$bp_mobile = ! empty( $this->option_value[ $pre . 'mobile'] )  ? $this->option_value[ $pre . 'mobile'] : $break_points['mobile'];

			$out= sprintf( '<p class="clearfix"><span class="label">%s</span></p>', esc_html__( 'Responsive Breakpoints:', 'themify' ) );
			$out .= sprintf( '<div class="themify-ui-slider clearfix"><div class="themify-slider-label label">%s</div><div class="label input-range width10"><div class="range-slider width8"></div><input type="text" name="%s" value="%s" data-min="%d" data-max="%d" class="width4" readonly> px</div></div>',
				esc_html__( 'Tablet Landscape', 'themify' ),
				$this->option_name . '[' . $pre . 'tablet_landscape' . ']',
				$bp_tablet_landscape,
				$break_points['tablet_landscape'][0],
                                $break_points['tablet_landscape'][1],
				$bp_tablet_landscape
			);
			$out .= sprintf( '<div class="themify-ui-slider clearfix"><div class="themify-slider-label label">%s</div><div class="label input-range width10"><div class="range-slider width8"></div><input type="text" name="%s" value="%s" data-min="%d" data-max="%d" class="width4" readonly> px</div></div>',
				esc_html__( 'Tablet Portrait', 'themify' ),
				$this->option_name . '[' . $pre . 'tablet' . ']',
				$bp_tablet,
				$break_points['tablet'][0],
                                $break_points['tablet'][1],
				$bp_tablet
			);
			$out .= sprintf( '<div class="themify-ui-slider clearfix"><div class="themify-slider-label label">%s</div><div class="label input-range width10"><div class="range-slider width8"></div><input type="text" name="%s" value="%s" data-min="%d" data-max="%d" class="width4" readonly> px</div></div>',
				esc_html__( 'Mobile', 'themify' ),
				$this->option_name . '[' . $pre . 'mobile' . ']',
				$bp_mobile,
				320,
				$break_points['mobile'],
				$bp_mobile
			);
			if(!Themify_Builder_Model::is_premium()){
				$out='<div class="tb_lite">'.$out.'</div>';
			}
			echo $out;
		}

		function image_script_field() {
			$name = $this->current_tab . '-img_settings_use';
			$field_name = $this->option_name . '['.$name.']';
			$checked = isset($this->option_value[$name]) ? $this->option_value[$name] : '';
			echo '<input id="themify_setting-img_settings_use" type="checkbox" name="'.$field_name.'" class="disable_img_php" value="1" '.checked( $checked, 1, false ).'/>',
                            '<label for="themify_setting-img_settings_use">&nbsp; '.__('Disable image script globally',
					'themify').'</label>',
                            '<br /><span><small>'.__('(WordPress Featured Image or original images will be used)', 'themify').'</small></span>';
		}

		function image_global_field() {
			$feature_sizes = themify_get_image_sizes_list();

			$name = $this->current_tab.'-global_feature_size';
			$field_name = $this->option_name . '['.$name.']';
			$selected = isset($this->option_value[$name]) ? $this->option_value[$name] : '';
			echo '<select name="'.$field_name.'" class="image_global_size_field">';
			foreach($feature_sizes as $option){
				echo '<option value="' . $option['value'] . '"'.selected( $selected, $option['value'] ).'>' . esc_html( $option['name'] ) . '</option>';
			}
			echo '</select>';
		}

		function role_access_fields( $atts ) {
			global $wp_roles;

			$prefix = 'setting-' . $atts[ 'mode' ] . '-';
			$roles = $wp_roles->get_names();
			$role_options = array(
				'default' => __( 'Default', 'themify' ),
				'enable' => __( 'Enable', 'themify' ),
				'disable' => __( 'Disable', 'themify' )
			);

			// Remove the adminitrator and subscriber user role from the array
			unset( $roles['administrator']);

			// Remove all the user roles with no "edit_posts" capability
			foreach( $roles as $role => $slug ) {
				if( empty( $wp_roles->roles[$role]['capabilities']['edit_posts'] ) ){
					unset( $roles[$role] );
                                }
			}

			printf( '<ul%s>', ! Themify_Builder_Model::is_premium() ? ' class="tb_lite"' : '' );
			
			foreach( $roles as $role => $slug ) :
				$option_name = $this->option_name . '[' . $prefix . $role . ']';
				$this->option_value = get_option( $this->option_name );
				$value = isset( $this->option_value[ $prefix . $role ] )
					? $this->option_value[ $prefix . $role ] : 'default'; ?>

				<li class="role-access-controller">
					<div class="role-title"><?php echo esc_attr( $slug ); ?></div>

					<?php foreach( $role_options as $key => $label ) : ?>
						<div class="role-option role-<?php echo $key; ?>">
							<?php printf( '<input type="radio" id="%1$s-%2$s" name="%2$s" value="%1$s" %3$s>', $key, esc_attr( $option_name ), checked( $value, $key, false ) ); ?>
							<?php printf( '<label for="%s-%s">%s</label>', $key, esc_attr( $option_name ), $label ); ?>
						</div>
					<?php endforeach; ?>
				</li>
			<?php endforeach;
			echo '</ul>';
		}

		function image_quality_field() {
			$name = $this->current_tab.'-img_settings_quality';
			$field_name = $this->option_name . '['.$name.']';
			$value = isset($this->option_value[$name]) ? $this->option_value[$name] : '';
			echo '<input type="text" name="'.$field_name.'" value="'.$value.'" class="img_field">','&nbsp; <small>'. __('max 100 (higher = better quality, but bigger file size)', 'themify') ,'</small>';
		}

		function image_crop_align_field() {
			$options = array(
				array('value' => 'c', 'name' => __('Center', 'themify')),
				array('value' => 't', 'name' => __('Top', 'themify')),
				array('value' => 'tr',	'name' => __('Top Right', 'themify')),
				array('value' => 'tl',	'name' => __('Top Left', 'themify')),
				array('value' => 'b',	'name' => __('Bottom', 'themify')),
				array('value' => 'br',	'name' => __('Bottom Right', 'themify')),
				array('value' => 'bl',	'name' => __('Bottom Left', 'themify')),
				array('value' => 'l',	'name' => __('Left', 'themify')),
				array('value' => 'r',	'name' => __('Right', 'themify'))
			);
			$name = $this->current_tab .'-img_settings_crop_option';
			$field_name = $this->option_name . '['.$name.']';
			echo '<select name="'.$field_name.'" class="img_field"><option></option>';
			foreach($options as $option){
				$selected = isset( $this->option_value[$name] ) ? $this->option_value[$name] : '';
                                    echo '<option value="' . $option['value']  . '" '.selected( $selected, $option['value']).'>' . esc_html( $option['name'] ) . '</option>';
				}
			echo '</select>';
		}

		function image_vertical_crop_field() {
			$options_vertical = array(
				array('name'=> '','value' => ''),
				array('name'=> __('Yes', 'themify'), 'value' => 'yes'),
				array('name'=> __('No', 'themify'),	 'value' => 'no')
			);
			$name = $this->current_tab .'-img_settings_vertical_crop_option';
			$field_name = $this->option_name . '['.$name.']';
			$selected = isset( $this->option_value[$name] ) ? $this->option_value[$name] : '';
			echo '<select name="' . $field_name . '" class="img_field">';
			foreach($options_vertical as $option_vertical){
				echo '<option value="' . esc_attr( $option_vertical['value'] ) . '"'.selected( $selected, $option_vertical['value'] ).'>' . esc_html( $option_vertical['name'] ) . '</option>';
			}
			echo '</select>&nbsp; <small>' . __('(Select \'no\' to disable vertical cropping globally)', 'themify') , '</small>';
		}

		function custom_css_field(){
			$name = $this->current_tab . '-custom_css';
			$field_name = $this->option_name . '['.$name.']';
			$out = '<textarea name="'.$field_name.'" style="width:100%;height:600px;">';
			$out.=isset( $this->option_value[$name] ) ? $this->option_value[$name] : '';
			$out.='</textarea>';
			if(!Themify_Builder_Model::is_premium()){
				$out='<div class="tb_lite">'.$out.'</div>';
			}
			echo $out;
		}
				
		function google_map_api_key_field(){
			$pre = $this->current_tab . '_';
			$out='<div>';
			$google_map_key =  isset( $this->option_value[$pre.'google_map_key'] )?$this->option_value[$pre.'google_map_key']:'';
			$out.=sprintf('<input type="text" style="min-width:300px;" value="%s" name="%s" />',
						$google_map_key,
						esc_attr( $this->option_name . '[' . $pre . 'google_map_key' . ']' )
					);
			$out.='<br/><br/>';
			$out.= __('Google API key is required to use Builder Map module.','themify').' <a href="//developers.google.com/maps/documentation/javascript/get-api-key#key" target="_blank">'.__( 'Generate Api key', 'themify' ).'</a>';
			$out.='</div>';
			echo $out;
				
		}

		function regenerate_css_field(){
			$json_files = '';
			$upload_dir = wp_upload_dir();
			$themify_css_dir = $upload_dir['basedir'].'/themify-css';
			$css_files = glob("$themify_css_dir/*.css");
			if(!empty($css_files)){
				$group = $counter = 1;
				foreach($css_files as $file){
					if($counter>10){
						$counter = 1;
						$group++;
					}
					$file = basename($file, ".css");
					$id = preg_replace("/[^0-9]/","",$file);
					if(''!=$id){
						$files[$group][] = $id;
						$counter++;
					}
				}
				$json_files = json_encode($files);
			}
			$in_progress = false;
			for($i=1;$i<=$group;$i++){
				if ( true === ( get_transient( 'themify_regenerate_css_in_progress_'.$i ) ) ) {
					$in_progress = true;
					break;
				}
			}
			$disabled = $in_progress ? 'disabled="disabled"' : '';
			$value = $in_progress ? __('Regenerating ...','themify') : __('Regenerate CSS Files','themify');
			$output ='<div>';
			$output .= '<input data-files=\''.$json_files.'\' id="builder-regenerate-css-files" type="button" name="builder-regenerate-css-files" '.$disabled.' value="'.$value.'" class="button big-button"/>';
			$output .='<br/><br/>';
			$output .= __('Builder styling are output to the generated CSS files stored in \'wp-content/uploads\' folder. Regenerate files will update all data in the generated files (eg. correct background image paths, etc.).','themify');
			$output .='</div>';
			echo $output;
		}
		
		function builder_upgrade(){
		
		}

		function queue_top_pages( $pages ) {
                    $pages[] =  'toplevel_page_themify-builder' ;
                    return $pages;
		}

		function queue_pagenow( $pagenows ) {
                    $pagenows[] =  'themify-builder' ;
                    return $pagenows;
		}

		function load_enqueue_scripts( $page ) {
			if ( 'toplevel_page_themify-builder' === $page ) {
				wp_enqueue_script( 'jquery-ui-slider' );
				wp_enqueue_script( 'jquery-ui-sortable' );
				wp_enqueue_script( 'themify-builder-plugin-upgrade', themify_enque(THEMIFY_BUILDER_URI . '/js/themify.builder.upgrader.js'), array('jquery'), false, true );

				add_action( 'admin_head', array( $this, 'custom_admin_css' ) );
			}
		}

		/**
		 * Add custom inline css in plugin settting page.
		 * 
		 * @access public
		 */
		public function custom_admin_css() {
			echo '<style type="text/css" rel="stylesheet">
			.width8 { width: 300px; }
			.width4 { width: 80px; }
			.width10 { width: 68%; }
			@media only screen and (max-width: 820px) {
                            .width4,.width8 {
                                max-width: 100%;
                            }
			}
			</style>';
		}
		
	}
}