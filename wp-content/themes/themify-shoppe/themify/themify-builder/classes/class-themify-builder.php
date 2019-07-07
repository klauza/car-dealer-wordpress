<?php

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

if (!class_exists('Themify_Builder')) :

    /**
     * Main Themify Builder class
     * 
     * @package default
     */
    class Themify_Builder {
		

        /**
         * @var string
         */
        public $meta_key_transient;

        /**
         * @var array
         */
        public $builder_settings = array();

        /**
         * @var array
         */
        public $module_settings = array();

        /**
         * @var array
         */
        public $registered_post_types = array('post', 'page');

        /**
         * Define builder grid active or not
         * @var bool
         */
        public static $frontedit_active = false;

        /**
         * Define load form
         * @var string
         */
        public $load_form = 'module';

        /**
         * Get status of builder content whether inside builder content or not
         */
        public $in_the_loop = false;

        /**
         * A list of posts which have been rendered by Builder
         */
        private $post_ids = array();
        public $stylesheet;

        /**
         * Lists of builder data to used when frontend editor active
         */
        private $frontend_builder_ids = array();

        /**
         * Skip the condition checks in show_in_front(), force Builder to display the output
         */
        public $skip_display_check = false;
        
        private $restriction_id = null;

        /**
         * Themify Builder Constructor
         */
        public function __construct() {
            
        }

        /**
         * Class Init
         */
        public function init() {
			global $themify_builder_plugin_compat;
            // Include required files
            $this->includes_always();
            Themify_Builder_Model::setup_default_directories();
            Themify_Builder_Components_Manager::init();
            do_action('themify_builder_setup_modules', $this);
            add_action('init', array('Themify_Builder_Component_Module', 'load_modules'), 11);
            if (!Themify_Builder_Model::is_front_builder_activate()) {
                add_filter('themify_builder_module_content', array('Themify_Builder_Model', 'format_text'));
                /**
                 * WordPress 4.4 Responsive Images support */
                global $wp_version;
                if (version_compare($wp_version, '4.4', '>=')) {
                    add_filter('themify_builder_module_content', 'wp_make_content_images_responsive');
                    add_filter('themify_image_make_responsive_image', 'wp_make_content_images_responsive');
                }
            }
			// Actions
			add_action('init', array($this, 'setup'), 10);
            new Themify_Builder_Include($this);
            if (Themify_Builder_Model::is_frontend_editor_page()) {

                if (Themify_Builder_Model::is_front_builder_activate()) {
                    // load module panel frontend
                    add_filter('script_loader_tag', array($this, 'defer_js'), 11, 3);
                    add_action('wp_footer', array($this, 'load_javascript_template_front'), 10);
                    add_filter('show_admin_bar', '__return_false');
                } else {

                    $this->includes_editable();

                    // Ajax Actions
                    if (defined('DOING_AJAX')) {
                        add_action('wp_ajax_tb_load_module_partial', array($this, 'load_module_partial_ajaxify'), 10);
                        add_action('wp_ajax_tb_render_element', array($this, 'render_element_ajaxify'), 10);
                        add_action('wp_ajax_tb_load_shortcode_preview', array($this, 'shortcode_preview'), 10);
                        add_action('wp_ajax_themify_get_tax', array($this, 'themify_get_tax'), 10);
                        add_action('wp_ajax_themify_builder_get_tax_data', array($this, 'themify_builder_get_tax_data'), 10);
                        add_action('wp_ajax_tb_render_element_shortcode', array($this, 'render_element_shortcode_ajaxify'), 10);
                        // Builder Save Data
                        add_action('wp_ajax_tb_save_data', array($this, 'save_data_builder'), 10);
                        add_action('wp_ajax_themify_builder_plupload_action', array($this, 'builder_plupload'), 10);
			// AJAX Action Save Module Favorite Data
                        add_action( 'wp_ajax_tb_module_favorite', array( $this, 'save_module_favorite_data' ) );
                        //AJAX Action Get Visual Templates
                        add_action('wp_ajax_tb_load_visual_templates',array($this,'load_visual_templates'));
                        //AJAX Action Get Form Templates
                        add_action('wp_ajax_tb_load_form_templates',array($this,'load_form_templates'));
                        //AJAX Action update ticks and TakeOver
                        add_action('wp_ajax_tb_update_tick',array($this,'updateTick'));
                        add_action('wp_ajax_tb_help',array($this,'help'));
                        // Regenerate CSS Files
                        add_action('wp_ajax_themify_regenerate_css_files_ajax',array($this,'themify_regenerate_css_files_ajax'));
                        
                    } else {
                        // Builder write panel
                        if (is_admin()) {
                            // Filtered post types
                            add_filter('themify_post_types', array($this, 'extend_post_types'));
                            Themify_Builder_Model::load_general_metabox(); // setup metabox fields
                            add_filter('themify_do_metaboxes', array($this, 'builder_write_panels'), 11);
                            add_action('themify_builder_metabox', array($this, 'add_builder_metabox'), 10);
                            add_action('admin_enqueue_scripts', array($this, 'load_admin_interface'), 10);
                            add_action( 'load-post.php', array( $this, 'builder_static_badge_scripts' ) );
                            add_action( 'load-post-new.php', array( $this, 'builder_static_badge_scripts' ) );
                            
                            add_filter( 'mce_css', array( $this, 'builder_static_badge_css' ) );
                            // Switch to frontend
                            add_action('save_post', array($this, 'switch_frontend'), 999, 1);
							// Disable WP Editor
							add_filter( 'edit_form_after_title', array( $this, 'themify_disable_wp_editor' ), 99 );
                        } else {
                            add_action('admin_bar_menu', array($this, 'builder_admin_bar_menu'), 100);
                            add_action('wp_footer',array($this,'async_footer'));
                        }
                    }
                    // Import Export
                    new Themify_Builder_Import_Export($this);
                }
				
                    // Fix security restrictions
                    add_filter( 'user_can_richedit', '__return_true' );
            }
            else{
                add_filter('post_class', array('Themify_Builder_Component_Base', 'filter_post_class'));
            }

            // Asynchronous Loader
            add_action('wp_enqueue_scripts', array($this, 'register_frontend_js_css'), 9);

            // Hook to frontend
            add_filter( 'the_content', array( $this, 'builder_clear_static_content' ), 1 );
            add_filter('the_content', array($this, 'builder_show_on_front'), 11);
            add_filter('body_class', array($this, 'body_class'), 10);
            // Add extra protocols like skype: to WordPress allowed protocols.
            if (!has_filter('kses_allowed_protocols', 'themify_allow_extra_protocols') && function_exists('themify_allow_extra_protocols')) {
                add_filter('kses_allowed_protocols', 'themify_allow_extra_protocols');
            }

            // Plugin compatibility
			$themify_builder_plugin_compat = new Themify_Builder_Plugin_Compat();

            // if (!is_admin() || defined('DOING_AJAX')) {
            $this->stylesheet = new Themify_Builder_Stylesheet($this);
            add_filter('themify_main_script_vars', array($this, 'add_minify_vars'));

            // Library Module, Rows and Layout Parts
            new Themify_Builder_Library_Items();
        }

        //temp code for compatibility  builder new version with old version of addons to avoid the fatal error, can be removed after updating(2017.07.20)
        public function register_directory() {}

        public function is_themify_theme() {}

        /**
         * Return Builder data for a post
         *
         * @since 1.4.2
         * @return array
         */
        public function get_builder_data($post_id) {
            global $ThemifyBuilder_Data_Manager;
            $builder_data = $ThemifyBuilder_Data_Manager->get_data($post_id);
            if (!is_array($builder_data)) {
                $builder_data = array();
            }
            return apply_filters('themify_builder_data', $builder_data, $post_id);
        }

        /**
         * Return all modules for a post as a two-dimensional array
         *
         * @since 1.4.2
         * @return array
         */
        public function get_flat_modules_list($post_id = null, $builder_data = null) {
            if ($builder_data == null) {
                $builder_data = $this->get_builder_data($post_id);
            }

            $_modules = array();
            // loop through modules in Builder
            if (is_array($builder_data)) {
                foreach ($builder_data as $row) {
                    if (!empty($row['cols'])) {
                        foreach ($row['cols'] as $col) {
                            if (!empty($col['modules'])) {
                                foreach ($col['modules'] as $mod) {
                                    if (isset($mod['mod_name'])) {
                                        $_modules[] = $mod;
                                    }
                                    // Check for Sub-rows
                                    if (!empty($mod['cols'])) {
                                        foreach ($mod['cols'] as $sub_col) {
                                            if (!empty($sub_col['modules'])) {
                                                foreach ($sub_col['modules'] as $sub_module) {
                                                    $_modules[] = $sub_module;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            return $_modules;
        }

        /**
         * Return first not empty text module
         *
         * @since 1.4.2
         * @return string
         */
        public function get_first_text($post_id = null, $builder_data = null) {
            if ($builder_data == null) {
                $builder_data = $this->get_builder_data($post_id);
            }
            // loop through modules in Builder
            if (is_array($builder_data)) {
                foreach ($builder_data as $row) {
                    if (!empty($row['cols'])) {
                        foreach ($row['cols'] as $col) {
                            if (!empty($col['modules'])) {
                                foreach ($col['modules'] as $mod) {
                                    if (isset($mod['mod_name']) && $mod['mod_name'] === 'text' && !empty($mod['mod_settings']['content_text'])) {
                                        return $mod['mod_settings']['content_text'];
                                    }
                                    // Check for Sub-rows
                                    if (!empty($mod['cols'])) {
                                        foreach ($mod['cols'] as $sub_col) {
                                            if (!empty($sub_col['modules'])) {
                                                foreach ($sub_col['modules'] as $sub_module) {
                                                    if (isset($sub_module['mod_name']) && $sub_module['mod_name'] === 'text' && !empty($sub_module['mod_settings']['content_text'])) {
                                                        return $sub_module['mod_settings']['content_text'];
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            return '';
        }
        
        /**
         * Load JS and CSs for async loader.
         *
         * @since 2.1.9
         */
        public function async_footer(){
            wp_deregister_script('wp-embed');
            $icons = Themify_Icon_Picker::get_instance();
            wp_enqueue_style('themify-builder-loader', themify_enque(THEMIFY_BUILDER_URI . '/css/themify.builder.loader.css'), null, THEMIFY_VERSION);
            wp_enqueue_script('themify-builder-loader', themify_enque(THEMIFY_BUILDER_URI . '/js/themify.builder.loader.js'), array('jquery'), THEMIFY_VERSION, true);
            wp_localize_script('themify-builder-loader', 'tbLoaderVars', array(
                'styles' => apply_filters('themify_styles_top_frame', array(
                        themify_enque(THEMIFY_BUILDER_URI . '/css/combine.css'),
                        themify_enque(THEMIFY_BUILDER_URI . '/css/toolbar.css'),
                        is_rtl() ? themify_enque( THEMIFY_BUILDER_URI . '/css/toolbar-rtl.css' ) : '',
                        themify_enque(THEMIFY_URI . '/themify-icons/themify-icons.css'),
                        THEMIFY_BUILDER_URI . '/css/animate.min.css',
                        themify_enque(THEMIFY_BUILDER_URI . '/css/themify.combobox.css'),
                        themify_enque($icons->url . 'assets/styles.css'),
                        themify_enque(THEMIFY_METABOX_URI . 'css/jquery.minicolors.css'),
                    )
                ),
                'js' => apply_filters('themify_js_top_frame', array()),
                'turnOnBuilder' => __('Turn On Builder', 'themify'),
                'isTouch' => themify_is_touch() ? true : false
            ));
            echo '<div style="display:none;">';
                wp_editor(' ', 'tb_lb_hidden_editor');
            echo '</div>';
        }

        /**
         * Init function
         */
        public function setup() {
            $this->meta_key_transient = apply_filters('themify_builder_meta_key_transient', 'themify_builder_settings_transient');
            /* git #1862 */
            Themify_Builder_Model::builder_cpt_check();
        }

        private function includes_always() {
            include THEMIFY_BUILDER_CLASSES_DIR . '/class-builder-data-manager.php';
            include THEMIFY_BUILDER_CLASSES_DIR . '/class-themify-builder-stylesheet.php';
            include THEMIFY_BUILDER_CLASSES_DIR . '/class-themify-builder-plugin-compat.php';
            include THEMIFY_BUILDER_CLASSES_DIR . '/class-themify-builder-components-manager.php';
            include THEMIFY_BUILDER_CLASSES_DIR . '/class-themify-builder-library-item.php';
            include THEMIFY_BUILDER_CLASSES_DIR . '/class-themify-builder-widgets.php';

            if ( Themify_Builder_Model::is_gutenberg_active() ) {
                include THEMIFY_BUILDER_CLASSES_DIR . '/class-themify-builder-gutenberg.php';
            }
        }

        private function includes_editable() {
            // Class duplicate page
            include_once THEMIFY_BUILDER_INCLUDES_DIR . '/themify-builder-options.php';
            include THEMIFY_BUILDER_CLASSES_DIR . '/class-builder-duplicate-page.php';
            include THEMIFY_BUILDER_CLASSES_DIR . '/class-themify-builder-import-export.php';
        }

        /**
         * List of post types that support the editor
         *
         * @since 2.4.8
         */
        public function builder_post_types_support() {
            $public_post_types = get_post_types(array(
                'public' => true,
                '_builtin' => false,
                'show_ui' => true,
            ));
            $post_types = array_merge($public_post_types, array('post', 'page'));
            foreach ($post_types as $key => $type) {
                if (!post_type_supports($type, 'editor')) {
                    unset($post_types[$key]);
                }
            }

            return apply_filters('themify_builder_post_types_support', $post_types);
        }

        /**
         * Builder write panels
         *
         * @param $meta_boxes
         *
         * @return array
         */
        public function builder_write_panels($meta_boxes) {
            if ( Themify_Builder_Model::is_gutenberg_editor() ) 
                return $meta_boxes;

            // Page builder Options
            $page_builder_options = apply_filters('themify_builder_write_panels_options', array(
                
                array(
                    'name' => 'page_builder',
                    'title' => __('Themify Builder', 'themify'),
                    'description' => '',
                    'type' => 'page_builder'
                ),
                array(
                    'name' => 'builder_switch_frontend',
                    'title' => false,
                    'type' => 'textbox',
                    'value' => 0,
                    'meta' => array('size' => 'small')
                )
            ));

            $types = $this->builder_post_types_support();
            $all_meta_boxes = array();
            foreach ($types as $type) {
                $all_meta_boxes[] = apply_filters('themify_builder_write_panels_meta_boxes', array(
                    'name' => __('Themify Builder', 'themify'),
                    'id' => 'page-builder',
                    'options' => $page_builder_options,
                    'pages' => $type
                ));
            }

            return array_merge($meta_boxes, $all_meta_boxes);
        }

        /**
         * Add builder metabox
         */
        public function add_builder_metabox() {
            include THEMIFY_BUILDER_INCLUDES_DIR . '/themify-builder-meta.php';
        }

        /**
         * Load interface js and css
         *
         * @since 2.1.9
         */
        public function load_frontend_interface() {
           
            // load only when builder is turn on
            wp_enqueue_style('themify-builder-combine-css', themify_enque(THEMIFY_BUILDER_URI . '/css/combine.css'), array(), THEMIFY_VERSION);
            wp_enqueue_style('themify-builder-admin-ui', themify_enque(THEMIFY_BUILDER_URI . '/css/themify-builder-admin-ui.css'), array(), THEMIFY_VERSION);
            if (is_rtl()) {
                wp_enqueue_style('themify-builder-admin-ui-rtl', themify_enque(THEMIFY_BUILDER_URI . '/css/themify-builder-admin-ui-rtl.css'), array('themify-builder-admin-ui'), THEMIFY_VERSION);
            }
            wp_enqueue_style('google-fonts-builder', themify_https_esc('http://fonts.googleapis.com/css') . '?family=Open+Sans:400,300,600|Montserrat');



            if (class_exists('Jetpack_VideoPress')) {
                // Load this so submit_button() is available in VideoPress' print_media_templates().
                require_once ABSPATH . 'wp-admin/includes/template.php';
            }
            $enqueue_scripts = array(
                'wp-backbone',
                'jquery-ui-draggable',
                'shortcode',
                'word-count',
                'themify-colorpicker',
                'themify-builder-google-webfont',
                'themify-combobox',
                'themify-builder-common-js',
                'themify-builder-app-js'
            );
           
            require_once ABSPATH . WPINC . '/media-template.php';
            ob_start();
            do_action('themify_builder_frontend_data');
            wp_print_media_templates();
            $top_iframe_data = ob_get_contents();
            ob_end_clean();
            
            foreach ($enqueue_scripts as $script) {
                switch ($script) {
                    case 'themify-combobox':
                        wp_enqueue_script($script, THEMIFY_BUILDER_URI . '/js/themify.combobox.min.js', array(), false, true);
                        break;
                    case 'themify-colorpicker':
                        wp_enqueue_script($script, THEMIFY_METABOX_URI . 'js/jquery.minicolors.min.js', array(), false, true); // grab from themify framework
                        break;

                    case 'themify-builder-google-webfont':
                        wp_enqueue_script($script, themify_https_esc('http://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js'), 1, true);
                        break;

                    case 'themify-builder-common-js':
                        // front ui js
                        wp_register_script('themify-builder-simple-bar-js', THEMIFY_BUILDER_URI . '/js/simplebar.min.js', array(), '2.0.3', true);
                        wp_register_script($script, themify_enque(THEMIFY_BUILDER_URI . '/js/themify.builder.common.js'), array('themify-builder-simple-bar-js'), THEMIFY_VERSION, true);

                        wp_enqueue_script($script);
                        // Icon picker
                        Themify_Icon_Picker::get_instance()->enqueue();
                        break;
                    case 'themify-builder-app-js':
                        // is mobile version
                        if (themify_is_touch()) {
                            wp_enqueue_script('themify-builder-mobile-ui-js', THEMIFY_BUILDER_URI . '/js/jquery.ui.touch-punch.min.js', array('jquery-ui-mouse'), THEMIFY_VERSION, true);
                        }
                        wp_enqueue_script('themify-builder-js', themify_enque(THEMIFY_BUILDER_URI . '/js/themify.builder.script.js'), array(), THEMIFY_VERSION, true);
                        wp_enqueue_script('themify-builder-draggable', themify_enque(THEMIFY_BUILDER_URI . '/js/themify-builder-drag.js'), array(), THEMIFY_VERSION, true);
                    //    wp_enqueue_script('themify-builder-sortable', themify_enque(THEMIFY_BUILDER_URI . '/js/sortable.js'), array(), THEMIFY_VERSION, true);
                        wp_enqueue_script($script, themify_enque(THEMIFY_BUILDER_URI . '/js/themify-builder-app.js'), array('themify-builder-draggable'), THEMIFY_VERSION, true);
                        global $shortcode_tags,$wp_styles;
                        wp_localize_script($script, 'themifyBuilder', apply_filters('themify_builder_ajax_front_vars', array(
                            'ajaxurl' => admin_url('admin-ajax.php'),
                            'includes_url' => includes_url(),
                            'meta_url'=>THEMIFY_METABOX_URI,
                            'isTouch' => themify_is_touch() ? 'true' : 'false',
                            'tb_load_nonce' => wp_create_nonce('tb_load_nonce'),
                            'is_premium' => Themify_Builder_Model::is_premium(),
                            'isThemifyTheme' => Themify_Builder_Model::is_themify_theme() ? 'true' : 'false',
                            'disableShortcuts' => themify_builder_get('setting-page_builder_disable_shortcuts', 'builder_disable_shortcuts'),
                            'available_shortcodes' => array_keys($shortcode_tags),
                            'widget_css'=>array($wp_styles->registered['widgets']->src,$wp_styles->registered['customize-widgets']->src),
                            'media_css'=>array($wp_styles->registered['wp-mediaelement']->src,$wp_styles->registered['mediaelement']->src),
                            // for live styling
                            'fonts' => array('safe' => themify_get_web_safe_font_list(), 'google' => themify_get_google_web_fonts_list()),
                            // Breakpoints
                            'breakpoints' => themify_get_breakpoints(),
                            'modules' => Themify_Builder_Model::get_modules_localize_settings(),
                            'i18n' => self::get_i18n(),
                            'data'=>$top_iframe_data,
                            'debug'=>defined('THEMIFY_DEBUG') && THEMIFY_DEBUG,
                            'empty_css'=>THEMIFY_BUILDER_URI.'/css/empty.css'
                        )));
                        wp_enqueue_script('jquery-knob', THEMIFY_BUILDER_URI . '/js/jquery.knob.min.js', array(), null, true);
                        if (Themify_Builder_Model::is_premium()) {
                            wp_enqueue_script('themifyGradient', themify_enque(THEMIFY_BUILDER_URI . '/js/premium/themifyGradient.js'), array('themify-colorpicker'), THEMIFY_VERSION, true);
                        }
                        wp_enqueue_script('themify-builder-front-ui-js', themify_enque(THEMIFY_BUILDER_URI . '/js/themify-builder-visual.js'), array('themify-builder-common-js'), THEMIFY_VERSION, true);
                        wp_localize_script('themify-builder-front-ui-js', 'themify_builder_plupload_init', Themify_Builder_Model::get_builder_plupload_init());
                        break;
                    default:
                        wp_enqueue_script($script);
                        break;
                }
            }
            do_action('themify_builder_frontend_enqueue');
        }

        private static function get_i18n() {
            return array(
                'confirmRestoreRev' => __('Save the current state as a revision before replacing?', 'themify'),
                'dialog_import_page_post' => __('Would you like to replace or append the builder?', 'themify'),
                'confirm_on_duplicate_page' => __('Save the Builder before duplicating this page?', 'themify'),
                'moduleDeleteConfirm' => __('Press OK to remove this module', 'themify'),
                'rowDeleteConfirm' => __('Press OK to remove this row', 'themify'),
                'subrowDeleteConfirm' => __('Press OK to remove this sub row', 'themify'),
                'importFileConfirm' => __('This import will override all current Builder data. Press OK to continue', 'themify'),
                'confirm_template_selected' => __('Would you like to replace or append the layout?', 'themify'),
                'enterRevComment' => __('Add optional revision comment:', 'themify'),
                'confirmDeleteRev' => __('Are you sure want to delete this revision', 'themify'),
                'switchToFrontendLabel' => __('Themify Builder', 'themify'),
                'text_no_localStorage' => __("Your browser does not support this feature. Please use a modern browser such as Google Chrome or Safari.", 'themify'),
                'text_confirm_data_paste' => __('This will overwrite the data. Ok to proceed?', 'themify'),
                'text_alert_wrong_paste' => __('Error: Paste valid data only (paste row data to row, sub-row data to sub-row, module data to module).', 'themify'),
                'text_import_layout_button' => __('Import Layout', 'themify'),
                'rowLibraryDeleteConfirm' => __('Are you sure to delete this saved row?', 'themify'),
                'moduleLibraryDeleteConfirm' => __('Are you sure to delete this saved module?', 'themify'),
                'partLibraryDeleteConfirm' => __('Are you sure to delete this? It can not be undone. Once the Layout Part is deleted, all pages with this Layout Part will be gone.', 'themify'),
				'incorrectImageURL' => __('Incorrect image url! Please enter a valid image url.', 'themify')
            );
        }

        /**
         * Load admin js and css
         * @param $hook
         */
		public function load_admin_interface( $hook ) {
			if ( in_array( $hook, array('post-new.php', 'post.php'), true ) && in_array( get_post_type(), themify_post_types(),true ) && Themify_Builder_Model::hasAccess() ) {
				add_action( 'admin_footer', array($this, 'load_javascript_template_admin'), 10);
				wp_enqueue_style( 'themify-builder-loader', themify_enque( THEMIFY_BUILDER_URI . '/css/themify.builder.loader.css' ), null, THEMIFY_VERSION );
				wp_enqueue_style( 'themify-builder-combine-css', themify_enque( THEMIFY_BUILDER_URI . '/css/combine.css' ), null, THEMIFY_VERSION );
				wp_enqueue_style( 'themify-builder-toolbar-css', themify_enque( THEMIFY_BUILDER_URI . '/css/toolbar.css' ), null, THEMIFY_VERSION );
				if ( is_rtl() ) {
					wp_enqueue_style( 'themify-builder-toolbar-rtl-css', themify_enque( THEMIFY_BUILDER_URI . '/css/toolbar-rtl.css' ), null, THEMIFY_VERSION );
				}
				wp_enqueue_style( 'themify-builder-admin-ui', themify_enque( THEMIFY_BUILDER_URI . '/css/themify-builder-admin-ui.css' ), null, THEMIFY_VERSION );
				wp_enqueue_style( 'themify-builder-style', themify_enque( THEMIFY_BUILDER_URI . '/css/themify-builder-style.css' ), null, THEMIFY_VERSION );
				is_rtl() && wp_enqueue_style( 'themify-builder-admin-ui-rtl', themify_enque( THEMIFY_BUILDER_URI . '/css/themify-builder-admin-ui-rtl.css' ), array('themify-builder-admin-ui'), THEMIFY_VERSION );
				// Enqueue builder admin scripts
				$enqueue_scripts = array(
					'main',
					'jquery-ui-core',
					'jquery-ui-draggable',
					'jquery-ui-selectmenu',
					'jquery-ui-sortable',
					'themify-builder-google-webfont',
					'themify-combobox',
					'themify-builder-common-js',
					'themify-builder-app-js',
					'themify-builder-backend-js'
				);
				
				foreach ( $enqueue_scripts as $script ) {
					switch ($script) {
						case 'main':
							wp_enqueue_script( 'themify-main-script', themify_enque(THEMIFY_URI.'/js/main.js'), array('jquery'), THEMIFY_VERSION, true );
							break;
						case 'themify-combobox':
							wp_enqueue_style($script . '-css', themify_enque(THEMIFY_BUILDER_URI . '/css/themify.combobox.css'), null, THEMIFY_VERSION);
							wp_enqueue_script($script, THEMIFY_BUILDER_URI . '/js/themify.combobox.min.js', array('jquery'));
							break;
						case 'themify-builder-google-webfont':
							wp_enqueue_script($script, themify_https_esc('http://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js'));
							break;
						case 'themify-builder-common-js':
							wp_register_script('themify-builder-simple-bar-js', THEMIFY_BUILDER_URI . '/js/simplebar.min.js', array(), '2.0.3', true);
							wp_register_script('themify-builder-common-js', themify_enque(THEMIFY_BUILDER_URI . '/js/themify.builder.common.js'), array('themify-builder-simple-bar-js'), THEMIFY_VERSION, true);
							wp_enqueue_script('themify-builder-common-js');
							break;

						case 'themify-builder-app-js':
							wp_enqueue_script($script, themify_enque(THEMIFY_BUILDER_URI . '/js/themify-builder-app.js'), array('themify-builder-common-js'), THEMIFY_VERSION, true);
							break;
						case 'themify-builder-backend-js':
							wp_enqueue_script('jquery-knob', THEMIFY_BUILDER_URI . '/js/jquery.knob.min.js', array(), null, true);
							if (Themify_Builder_Model::is_premium()) {
								wp_enqueue_script('themifyGradient', themify_enque(THEMIFY_BUILDER_URI . '/js/premium/themifyGradient.js'), array('themify-colorpicker'), THEMIFY_VERSION, true);
							}
							wp_register_script('themify-builder-backend-js', themify_enque(THEMIFY_BUILDER_URI . '/js/themify-builder-backend.js'), array(), THEMIFY_VERSION, true);
                                                        global $wp_styles;
							wp_localize_script('themify-builder-backend-js', 'themifyBuilder', apply_filters('themify_builder_ajax_admin_vars', array(
								'ajaxurl' => admin_url('admin-ajax.php'),
								'includes_url' => includes_url(),
                                                                'meta_url'=>THEMIFY_METABOX_URI,
								'tb_load_nonce' => wp_create_nonce('tb_load_nonce'),
								'post_ID' => Themify_Builder_Model::get_ID(),
								'isTouch' => themify_is_touch() ? 'true' : 'false',
								'is_premium'=>Themify_Builder_Model::is_premium(),
								'isThemifyTheme' => Themify_Builder_Model::is_themify_theme() ? 'true' : 'false',
								'disableShortcuts' => themify_builder_get('setting-page_builder_disable_shortcuts', 'builder_disable_shortcuts'),
								// Breakpoints
								'breakpoints' => themify_get_breakpoints(),
                                                            
                                                                'widget_css'=>array($wp_styles->registered['widgets']->src,$wp_styles->registered['customize-widgets']->src),
								// Output builder data to use by Backbone Models
								'builder_data' => $this->get_builder_data(get_the_ID()),
								'fonts' => array('safe' => themify_get_web_safe_font_list(), 'google' => themify_get_google_web_fonts_list()),
								'modules' => Themify_Builder_Model::get_modules_localize_settings(),
								'i18n' => self::get_i18n(),
                                                                'builder_url'=>THEMIFY_BUILDER_URI,
                                                                'ticks'=>self::get_tick_options(),
                                                                'is_gutenberg_editor' => Themify_Builder_Model::is_gutenberg_editor(),
                                                                'debug'=>defined('THEMIFY_DEBUG') && THEMIFY_DEBUG
							)));
							wp_localize_script('themify-builder-backend-js','tbLocalScript',array('version' => THEMIFY_VERSION));
							wp_enqueue_script('themify-builder-backend-js');
							wp_localize_script($script, 'themify_builder_plupload_init', Themify_Builder_Model::get_builder_plupload_init());
							break;

						default:
							wp_enqueue_script($script);
							break;
					}
				}
				do_action('themify_builder_admin_enqueue');
                                add_filter('admin_body_class',array($this,'admin_body_class'),10,1);
			}
		}

        /**
         * Register styles and scripts necessary for Builder template output.
         * These are enqueued when user initializes Builder or from a template output.
         *
         * Registered style handlers:
         *
         * Registered script handlers:
         * themify-builder-module-plugins-js
         * themify-builder-script-js
         *
         * @since 2.1.9
         */
        public function register_frontend_js_css() {
            wp_enqueue_style('builder-styles', themify_enque(THEMIFY_BUILDER_URI . '/css/themify-builder-style.css'), array(), THEMIFY_VERSION);
            add_filter('style_loader_tag', array($this, 'builder_stylesheet_style_tag'), 10, 4);

            wp_localize_script('themify-main-script', 'tbLocalScript', apply_filters('themify_builder_script_vars', array(
                'isAnimationActive' => Themify_Builder_Model::is_animation_active(),
                'isParallaxActive' => Themify_Builder_Model::is_parallax_active(),
                'isParallaxScrollActive' => Themify_Builder_Model::is_parallax_scroll_active(),
                'isStickyScrollActive' => Themify_Builder_Model::is_sticky_scroll_active(),
                'animationInviewSelectors' => array('.module.wow', '.module_row.wow', '.builder-posts-wrap > .post.wow'),
                'backgroundSlider' => array(
                    'autoplay' => 5000,
                ),
                'animationOffset' => 100,
                'videoPoster' => THEMIFY_BUILDER_URI . '/img/blank.png',
                'backgroundVideoLoop' => 'yes',
                'builder_url' => THEMIFY_BUILDER_URI,
                'framework_url' => THEMIFY_URI,
                'version' => THEMIFY_VERSION,
                'fullwidth_support' => Themify_Builder_Model::is_fullwidth_layout_supported(),
                'fullwidth_container' => 'body',
                'loadScrollHighlight' => true,
                'addons' => Themify_Builder_Model::get_addons_assets(),
                'breakpoints' => themify_get_breakpoints(),
                'ticks'=>self::get_tick_options()
            )));
            //Inject variable values in gallery script
            wp_localize_script('themify-main-script', 'themifyScript', array(
                'lightbox' => themify_lightbox_vars_init(),
                'lightboxContext' => apply_filters('themify_lightbox_context', 'body')
                    )
            );
            //Inject variable values in Scroll-Highlight script
            wp_localize_script('themify-main-script', 'tbScrollHighlight', apply_filters('themify_builder_scroll_highlight_vars', array(
                'fixedHeaderSelector' => '',
                'speed' => 900,
                'navigation' => '#main-nav, .module-menu .ui.nav',
                'scrollOffset' => 0
            )));
        }

        /**
         * Prevent builder-style.css stylesheet from loading in the page, the stylesheet is loaded in themify.builder.script.js
         *
         * @return html
         */
        public function builder_stylesheet_style_tag($tag, $handle, $href, $media) {
            if ('builder-styles' === $handle) {
                $tag = '<meta name="builder-styles-css" content="builder-styles-css" id="builder-styles-css">' . "\n";
            }

            return $tag;
        }

        public function defer_js($tag,$handle,$src) {
            return $handle==='jquery-core'?$tag:str_replace(' src', ' defer="defer" src', $tag);
        }

        public function shortcode_preview() {
            check_ajax_referer('tb_load_nonce', 'tb_load_nonce');
            if (!empty($_POST['shortcode'])) {
                $shortcode = sanitize_text_field($_POST['shortcode']);
                $images = Themify_Builder_Model::get_images_from_gallery_shortcode($shortcode);
                if (!empty($images)) {
                    $html = '<div class="tb_shortcode_preview">';
                    foreach ($images as $image) {
                        $img_data = wp_get_attachment_image_src($image->ID, 'thumbnail');
                        $html.='<img src="' . $img_data[0] . '" width="50" height="50" />';
                    }
                    $html.='</div>';
                    echo $html;
                }
            }
            wp_die();
        }

        public function themify_get_tax() {
            if (!empty($_GET['tax']) && !empty($_GET['term'])) {
                $terms_by_tax = get_terms(sanitize_key($_GET['tax']), array('hide_empty' => true, 'name__like' => sanitize_text_field($_GET['term'])));
                $items = array();
                if (!empty($terms_by_tax)) {
                    foreach ($terms_by_tax as $t) {
                        $items[] = array('value' => $t->slug, 'label' => $t->name);
                    }
                }
                echo wp_json_encode($items);
            }
            wp_die();
        }

        public function themify_builder_get_tax_data() {
            if (!empty($_POST['data'])) {
                $respose = array();
                foreach ($_POST['data'] as $k => $v) {
                    $tax = key($v);
                    $slug = $v[$tax];
                    $terms_by_slug = get_term_by('slug', $slug, $tax);
                    $respose[] = array('tax' => $tax, 'val' => $terms_by_slug->name);
                }
                echo wp_json_encode($respose);
            }
            wp_die();
        }
        /**
         * Load module partial when update live content
         */
        public function load_module_partial_ajaxify() {
            check_ajax_referer('tb_load_nonce', 'tb_load_nonce');
            $cid = $_POST['tb_cid'];
            $identifier = array($cid);
            self::$frontedit_active = true;
			if( ! empty( $_POST['tb_post_id'] ) ) {
				Themify_Builder_Component_Base::$post_id = $_POST['tb_post_id'];
			}

            $new_modules = array(
                'mod_name' => $_POST['tb_module_slug'],
                'mod_settings' => json_decode(stripslashes($_POST['tb_module_data']), true)
            );
          
            Themify_Builder_Component_Module::template($new_modules, $cid, true, $identifier);

            wp_die();
        }

        public function render_element_ajaxify() {
            check_ajax_referer('tb_load_nonce', 'tb_load_nonce');

            $response = array();
            $batch = json_decode(stripslashes($_POST['batch']), true);

			if( ! empty( $_POST['tb_post_id'] ) ) {
				Themify_Builder_Component_Base::$post_id = $_POST['tb_post_id'];
			}

            if (!empty($batch)) {
                
                foreach ($batch as $b) {
                    $type = $b['data']['elType'];
                    self::$frontedit_active = true;
                    switch ($type) {
                        case 'module':

                            $identifier = array($b['jobID']);
                            $markup = Themify_Builder_Component_Module::template($b['data'], $b['jobID'], false, $identifier);
                            $type = $b['data']['mod_name'];
                            break;

                        case 'subrow':

                            $b['data']['row_order'] = $b['jobID'];
                            unset($b['data']['cols']);
                            $markup = Themify_Builder_Component_SubRow::template($b['jobID'], $b['jobID'], $b['jobID'], $b['data'], $b['jobID']);
                            break;

                        case 'column':

                            $row = array('row_order' => $b['jobID']);
                            unset($b['data']['modules']);
                            $b['data']['column_order'] = $b['jobID'];
                            $markup = Themify_Builder_Component_Column::template($b['jobID'], $row, $b['jobID'], $b['data'], $b['jobID']);
                            break;

                        case 'row':

                            $b['data']['row_order'] = $b['jobID'];
                            unset($b['data']['cols']);
                            $markup = Themify_Builder_Component_Row::template($b['jobID'], $b['data'], $b['jobID']);
                            break;
                    }
                    $response[$b['jobID']] = $markup;
                }
            }
            die(json_encode($response));
        }

        public function render_element_shortcode_ajaxify() {
			check_ajax_referer( 'tb_load_nonce', 'tb_load_nonce' );
			
			$shortcodes = $styles = array();
			$shortcode_data = json_decode( stripslashes_deep( $_POST['shortcode_data']), true );
			
			if ( is_array( $shortcode_data ) ) {
				foreach ( $shortcode_data as $shortcode ) {
					$shortcodes[] = array('key' => $shortcode, 'html' => do_shortcode( $shortcode ) );
				}
			}
			
			global $wp_styles;
			if(isset($wp_styles) && !empty( $shortcodes ) ) {
				ob_start();
				$tmp = $wp_styles->do_items();
				ob_end_clean();
				foreach($tmp as $handler){
					if(isset($wp_styles->registered[$handler])){
						$styles[] = array(
                                                                's'=>$wp_styles->registered[$handler]->src,
                                                                'v'=>$wp_styles->registered[$handler]->ver,
                                                                'm'=>isset($wp_styles->registered[$handler]->args)?$wp_styles->registered[$handler]->args:'all'
                                                            );
                                        }
				}
				unset($tmp);
			}

			wp_send_json_success( array(
				'shortcodes' => $shortcodes,
				'styles' => $styles
			) );
		}

        /**
         * Save builder main data
         */
        public function save_data_builder() {
            check_ajax_referer('tb_load_nonce', 'tb_load_nonce');
            // Information about writing process.
            $results = array();
            $data = json_decode(stripslashes_deep($_POST['data']), true);
            if (!empty($data) && is_array($data)) {
                $post_id = (int) $_POST['id'];
                $saveto = $_POST['tb_saveto'];
                if ('main' === $saveto) {
                    global $ThemifyBuilder_Data_Manager;
                    $results = $ThemifyBuilder_Data_Manager->save_data($data, $post_id, $saveto, $_POST['sourceEditor']);
                    $results['builder_data'] = json_decode(stripslashes_deep($results['builder_data']), true);
                    // update the post modified date time, to indicate the post has been modified
                    if(! isset( $_POST[ 'only_data' ] ) ) {
                        wp_update_post(array(
                            'ID' => $post_id,
                            'post_modified' => current_time('mysql'),
                            'post_modified_gmt' => current_time('mysql', 1),
                        ));
                    }
                }
            }
            wp_send_json_success($results);
        }

		/**
		 * Clear out static Builder data if it exists inside the_content
		 * Leaves out the bare HTML comment placeholder as Builder will later
		 * uses it to determine where its output should go (self::builder_show_on_front)
		 *
		 * @return string
		 */
		public function builder_clear_static_content( $content ) {
                    global $ThemifyBuilder_Data_Manager, $wp_current_filter;
                    // Skip for excerpt display
                    return in_array('get_the_excerpt', $wp_current_filter, true )?$content:$ThemifyBuilder_Data_Manager->update_static_content_string( '<!--themify_builder_static--><!--/themify_builder_static-->', $content );
		}

        /**
         * Hook to content filter to show builder output
         * @param $content
         * @return string
         */
        public function builder_show_on_front($content) {
            global $post, $ThemifyBuilder_Data_Manager;
            $post_id = Themify_Builder_Model::get_ID();
            // Exclude builder output in admin post list mode excerpt, Dont show builder on product single description
            if ( ( ! $this->skip_display_check ) && (
				! is_object( $post )
				|| ( is_admin() && ! defined( 'DOING_AJAX' ) )
				|| ( ! Themify_Builder_Model::is_front_builder_activate() && false === apply_filters( 'themify_builder_display', true, $post_id ) )
				|| post_password_required()
				|| ( themify_is_woocommerce_active() && ( ( is_shop() ) || ( is_singular( 'product' ) && 'product' === get_post_type() ) ) )
            ) ) {
                return $content;
            }
            do_action('themify_builder_before_template_content_render');
			
            //the_excerpt
            global $wp_current_filter;
            if (in_array('get_the_excerpt', $wp_current_filter,true)) {
                if ($content) {
                    return $content;
                }
                return $this->get_first_text($post_id);
            }
            // Infinite-loop prevention
            if (empty($this->post_ids)) {
                $this->post_ids[] = $post_id;
            } elseif (in_array($post_id, $this->post_ids,true)) {
                // we have already rendered this, go back.
                return $content;
            }

			$module_list = $this->get_flat_modules_list( $post_id );
				
            if (!$this->in_the_loop && Themify_Builder_Model::is_front_builder_activate()) {
				if( ! empty( $module_list ) && themify_builder_get( 'setting-page_builder_disable_wp_editor', 'builder_disable_wp_editor' ) ) {
					$content = '';
				}
				
                return $this->get_active_builder_data($post_id,$content).$this->get_builder_stylesheet('');
            }

            // Builder display position
            $display_position = apply_filters('themify_builder_display_position', 'below', $post_id);

            $this->post_ids[] = $post_id;

            $builder_data = $this->get_builder_data($post_id);

            if (!is_array($builder_data) || strpos($content, '#more-')!==false) {
                $builder_data = array();
            }

            // Check For page break module
            $page_breaks = 0;
            foreach($module_list as $module){
                if( isset( $module['mod_name'] ) && 'page-break' == $module['mod_name']){
                    $page_breaks++;
                }
            }
            if($page_breaks>0){
                $builder_data = $this->load_current_inner_page_content($builder_data,$page_breaks);
            }
            Themify_Builder_Component_Base::$post_id = $post_id;
            $template = $this->in_the_loop ? 'builder-output-in-the-loop.php' : 'builder-output.php';
            $builder_output = Themify_Builder_Component_Base::retrieve_template($template, array('builder_output' => $builder_data, 'builder_id' => $post_id), '', '', false);
            
            if ( $ThemifyBuilder_Data_Manager->has_static_content( $content ) ) {
                    $content = $ThemifyBuilder_Data_Manager->update_static_content_string( $builder_output, $content );
            } else {
                if ('above' === $display_position) {
                    $content = $builder_output . $content;
                } else {
                    $content .= $builder_output;
                }
            }

			if( ! empty( $module_list ) && themify_builder_get( 'setting-page_builder_disable_wp_editor', 'builder_disable_wp_editor' ) ) {
				$content = $builder_output;
			}

            $this->post_ids = array_unique($this->post_ids);
            if (array_shift($this->post_ids) ===$post_id) {
                // the loop is finished, reset the ID list
                $this->post_ids = array();
            }

            // Make sure static content is excluded, otherwise hide it with css
            $content = $ThemifyBuilder_Data_Manager->wrap_static_content_if_fail( $content );

            // load Builder stylesheet if necessary
            $content = $this->get_builder_stylesheet($builder_output) . $content;

            return $content;
        }
		
        public function get_active_builder_data($post_id,$content=''){
                global $ThemifyBuilder_Data_Manager;
                $builder_data = $this->get_builder_data($post_id);
                $this->frontend_builder_ids[ $post_id ] = $builder_data;

                add_action( 'themify_builder_frontend_enqueue', array( $this, 'enqueue_frontend_builder_data'));

                $wrapper = sprintf('<div id="themify_builder_content-%1$d" data-postid="%1$d" class="themify_builder_content themify_builder_content-%1$d themify_builder"></div>', $post_id);

                // Start builder block replacement
				if (Themify_Builder_Model::is_gutenberg_active() && $ThemifyBuilder_Data_Manager->has_builder_block($content)) {
					$content = $ThemifyBuilder_Data_Manager->replace_builder_block_tag($wrapper, $content);
					$content = $ThemifyBuilder_Data_Manager->update_static_content_string('', $content); // remove static content tag
					return $content;
				}

                // Start static content replacement
                if ( $ThemifyBuilder_Data_Manager->has_static_content( $content ) ) {
                    $content = $ThemifyBuilder_Data_Manager->update_static_content_string( $wrapper, $content );
                    return $content;
                }

                return $content . $wrapper;
        }

        /**
         * Enqueue builder data on frontend editor active
         * 
         * @access public
         */
        public function enqueue_frontend_builder_data() {
            if ( !empty( $this->frontend_builder_ids ) ) {
                foreach( $this->frontend_builder_ids as $key => $data ) {
                    wp_localize_script('themify-builder-front-ui-js', 'builderdata_' . $key, array(
                        'data' => $data
                    ));
                }
            }
        }

        /**
         * Returns <link> tag for Builder stylesheet or enqueue it properly, if necessary.
         *
         * @return string
         */
        public function get_builder_stylesheet($builder_output) {
            /* in RSS feeds and REST API endpoints, do not output the scripts */
            if( is_feed() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
                    return;
            }
            static $builder_loaded = false;
            if (!$builder_loaded && (Themify_Builder_Model::is_front_builder_activate() ||  strpos( $builder_output, 'module_row' ) !== false)) { // check if builder has any content
                $builder_loaded = true;
                wp_dequeue_style('builder-styles');
                $link_tag = "<link id='builder-styles' rel='stylesheet' href='" . themify_enque(THEMIFY_BUILDER_URI . '/css/themify-builder-style.css') . '?ver=' . THEMIFY_VERSION . "' type='text/css' />";
                return '<script type="text/javascript">
                            if(!document.getElementById( "builder-styles" ) && document.getElementById( "builder-styles-css" ) ) document.getElementById( "builder-styles-css" ).insertAdjacentHTML( "beforebegin", "' . $link_tag . '" );
                        </script>';
            }
            return '';
        }

        /**
         * Loads JS templates for front-end editor.
         */
        public function load_javascript_template_front() {
            $this->load_frontend_interface();
            include(THEMIFY_BUILDER_INCLUDES_DIR . '/tpl/themify-builder-js-tmpl-common.php');
            include(THEMIFY_BUILDER_INCLUDES_DIR . '/tpl/themify-builder-js-tmpl-front.php');
            include(THEMIFY_BUILDER_INCLUDES_DIR . '/themify-builder-module-panel.php');
        }

        /**
         * Loads JS templates for WordPress admin dashboard editor.
         */
        public function load_javascript_template_admin() {
            include(THEMIFY_BUILDER_INCLUDES_DIR . '/tpl/themify-builder-js-tmpl-common.php');
            include(THEMIFY_BUILDER_INCLUDES_DIR . '/tpl/themify-builder-js-tmpl-admin.php');
        }

        /**
         * Plupload ajax action
         */
        public function builder_plupload() {
            // check ajax nonce
            check_ajax_referer('tb_load_nonce');
            if (!current_user_can('upload_files')) {
                die;
            }

            $imgid = $_POST['imgid'];
            /** If post ID is set, uploaded image will be attached to it. @var String */
            $postid = $_POST['topost'];

            /** Handle file upload storing file|url|type. @var Array */
            $file = wp_handle_upload($_FILES[$imgid . 'async-upload'], array('test_form' => true, 'action' => 'themify_builder_plupload_action'));

            //let's see if it's an image, a zip file or something else
            $ext = explode('/', $file['type']);

            // Import routines
            if ('zip' === $ext[1] || 'rar' === $ext[1] || 'plain' === $ext[1]) {

                $url = wp_nonce_url('admin.php?page=themify');

                if (false === ( $creds = request_filesystem_credentials($url) )) {
                    return true;
                }
                if (!WP_Filesystem($creds)) {
                    request_filesystem_credentials($url, '', true);
                    return true;
                }

                global $wp_filesystem;
                $is_txt = $path = false;
                if ('zip' === $ext[1] || 'rar' === $ext[1]) {
                    $destination = wp_upload_dir();
                    $destination_path = $destination['path'];
                    unzip_file($file['file'], $destination_path);
                    if ($wp_filesystem->exists($destination_path . '/builder_data_export.txt')) {
                        $path = $destination_path . '/builder_data_export.txt';
                        $is_txt = true;
                    }
                } elseif ($wp_filesystem->exists($file['file'])) {
                    $path = $file['file'];
                }

                if ($path) {
                    $data = $wp_filesystem->get_contents($path);
                    $data = is_serialized($data) ? maybe_unserialize($data) : json_decode($data);
                    // set data here
                    $file = array_merge($file, $GLOBALS['ThemifyBuilder_Data_Manager']->save_data($data, $postid, 'import'));
                    if ($is_txt) {
                        $wp_filesystem->delete($path);
                    }
                    $wp_filesystem->delete($file['file']);
                } else {
                    _e('Data could not be loaded', 'themify');
                }
            } else {
                // Insert into Media Library
                // Set up options array to add this file as an attachment
                $attachment = array(
                    'post_mime_type' => sanitize_mime_type($file['type']),
                    'post_title' => str_replace('-', ' ', sanitize_file_name(pathinfo($file['file'], PATHINFO_FILENAME))),
                    'post_status' => 'inherit'
                );

                if ($postid) {
                    $attach_id = wp_insert_attachment($attachment, $file['file'], $postid);
                }
                // Common attachment procedures
                require_once( ABSPATH . 'wp-admin' . '/includes/image.php' );
                $attach_data = wp_generate_attachment_metadata($attach_id, $file['file']);
                wp_update_attachment_metadata($attach_id, $attach_data);

                if ($postid) {
                    $large = wp_get_attachment_image_src($attach_id, 'large');
                    $thumb = wp_get_attachment_image_src($attach_id, 'thumbnail');

                    //Return URL for the image field in meta box
                    $file['large_url'] = $large[0];
                    $file['thumb'] = $thumb[0];
                    $file['id'] = $attach_id;
                }
            }

            $file['type'] = $ext[1];
            // send the uploaded file url in response
            echo json_encode($file);
            exit;
        }

        /**
         * Display Toggle themify builder
         * wp admin bar
         */
        public function builder_admin_bar_menu($wp_admin_bar) {
            global $wp_query;
            if (is_admin() || !Themify_Builder_Model::is_frontend_editor_page() || ( is_post_type_archive() && !is_post_type_archive('product') ) || !is_admin_bar_showing() || isset($wp_query->query_vars['product_cat']) || is_tax('product_tag')) {
                return;
            }
            if(themify_is_woocommerce_active() && is_shop()){
                $post_id = get_option( 'woocommerce_shop_page_id' );
            }
            else{
                $p = get_queried_object(); //get_the_ID can back wrong post id
                $post_id = isset( $p->ID ) ? $p->ID : false;
                unset($p);
            }
            if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
                return;
            }

            $args = array(
                array(
                    'id' => 'themify_builder',
                    'title' => sprintf('<span data-id="'.$post_id.'" class="tb_front_icon"></span> %s', esc_html__('Turn On Builder', 'themify')),
                    'href' => '#',
                    'meta' => array('class' => 'toggle_tb_builder')
                )
            );

            if (is_singular() || is_page()) {
                $args = apply_filters('themify_builder_admin_bar_menu_single_page', $args);
            }

            foreach ($args as $arg) {
                $wp_admin_bar->add_node($arg);
            }
        }

        /**
         * Switch to frontend
         * @param int $post_id
         */
        public function switch_frontend($post_id) {
            //verify post is not a revision
           if (isset($_POST['builder_switch_frontend_noncename']) && $_POST['builder_switch_frontend_noncename']==='ok' && !wp_is_post_revision($post_id)){
                // redirect to frontend
                $_POST['builder_switch_frontend'] = 0;
                $_POST['builder_switch_frontend_noncename'] = 0;
                $post_url = get_permalink($post_id);
                wp_redirect(themify_https_esc($post_url) . '#builder_active');
                exit;
            }
        }

		/**
		 * Disable WP Editor
		 */
		public function themify_disable_wp_editor() {
			if( themify_get( 'setting-page_builder_is_active' ) !== 'disable'
				&& themify_builder_get( 'setting-page_builder_disable_wp_editor', 'builder_disable_wp_editor' ) ) {
				$module_list = $this->get_flat_modules_list( get_the_ID() );

				echo '<div class="themify-wp-editor-holder' . ( ! empty( $module_list ) ? ' themify-active-holder' : '' ) . '">
					<a href="' . get_permalink() . '#builder_active">' . esc_html__( 'Edit With Themify Builder', 'themify' ) . '</a>
				</div>';
				unset( $module_list );
			}
		}

        /**
         * Add Builder body class
         * @param $classes
         * @return mixed|void
         */
        public function body_class($classes) {
            if (themify_is_touch()) {
                $classes[] = 'istouch';
                // return the $classes array
                $ios = $this->check_for_old_ios();
                if ($ios) {
                    $classes[] = $ios;
                }
            }
            if(Themify_Builder_Model::is_frontend_editor_page()){
                $post_id = Themify_Builder_Model::get_ID();
                $uid = Themify_Builder_Model::get_edit_transient($post_id);
                
                if($uid>0 && $uid!=get_current_user_id()){
                    $classes[] = 'tb_restriction';
                    $this->restriction_id = $uid;
                    $this->restriction_data();
                }
                elseif (Themify_Builder_Model::is_front_builder_activate()) {
                    $classes[] = 'themify_builder_active builder-breakpoint-desktop';
                    if(empty($_SERVER['HTTP_PURPOSE']) || $_SERVER['HTTP_PURPOSE']!=='prefetch'){
                        Themify_Builder_Model::set_edit_transient($post_id,get_current_user_id());
                    }
                }
            }
            if (Themify_Builder_Model::is_parallax_active()) {
                $classes[] = 'builder-parallax-scrolling-active';
            }
            return apply_filters('themify_builder_body_class', $classes);
        }
        
        public function admin_body_class($classes){
            $classes.= ' builder-breakpoint-desktop';
            $post_id = Themify_Builder_Model::get_ID();
            $uid = Themify_Builder_Model::get_edit_transient($post_id);
            $current = get_current_user_id();
            if($uid>0 && $uid!=$current){
                $classes.= ' tb_restriction';
                $this->restriction_id = $uid;
                $this->restriction_data();
            }
            elseif(!$uid){
               Themify_Builder_Model::set_edit_transient($post_id,$current); 
            }
            return $classes;
        }


        private function restriction_data(){
            if(is_admin()){
                add_action('admin_footer', array($this, 'load_restriction'));
            }
            else{
                add_action('wp_footer', array($this, 'load_restriction'));
            }
        }
        
        public function load_restriction($takeover=false){
            $data = get_userdata($this->restriction_id);
            include(THEMIFY_BUILDER_INCLUDES_DIR . '/tpl/themify-builder-js-tmpl-locked.php');
        }

        /**
         * Includes this custom post to array of cpts managed by Themify
         * @param Array
         * @return Array
         */
        public function extend_post_types($types) {
            static $post_types = null;
            if ($post_types === null) {
                $post_types = array_unique(array_merge(
                                $this->registered_post_types, array_values(get_post_types(array(
                    'public' => true,
                    '_builtin' => false,
                    'show_ui' => true,
                                )))
                ));
            }
            return array_unique(array_merge($types, $post_types));
        }

        /**
         * Push the registered post types to object class
         * @param $type
         */
        public function push_post_types($type) {
            $this->registered_post_types[] = $type;
        }

        /**
         * Reset builder query
         * @param $action
         */
        public function reset_builder_query($action = 'reset') {
            if ('reset' === $action) {
                remove_filter('the_content', array($this, 'builder_show_on_front'), 11);
            } elseif ('restore' === $action) {
                add_filter('the_content', array($this, 'builder_show_on_front'), 11);
            }
        }

        /**
         * Check is plugin active
         */
        public function is_plugin_active($plugin) {
            return in_array($plugin, apply_filters('active_plugins', get_option('active_plugins')),true);
        }

        /**
         * Get google fonts
         */
        public function get_custom_google_fonts() {
            global $themify;
            $fonts = array();
            if (!empty($themify->builder_google_fonts)) {
                $themify->builder_google_fonts = substr($themify->builder_google_fonts, 0, -1);
                $fonts = explode('|', $themify->builder_google_fonts);
            }
            return $fonts;
        }

        public function check_for_old_ios() {
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'iPod') !== false) {
                preg_match('/OS\s(\d+)_\d/', $_SERVER['HTTP_USER_AGENT'], $version);
                if (!empty($version[1]) && $version[1] < 8) {
                    return 'ios7';
                }
            }

            return false;
        }

        public function add_minify_vars($vars) {
            $vars['minify']['js']['themify.builder.script'] = themify_enque(THEMIFY_BUILDER_URI . '/js/themify.builder.script.js', true);
            $vars['minify']['js']['themify.scroll-highlight'] = themify_enque(THEMIFY_BUILDER_URI . '/js/themify.scroll-highlight.js', true);
            $vars['minify']['js']['themify-youtube-bg'] = themify_enque(THEMIFY_BUILDER_URI . '/js/themify-youtube-bg.js', true);
            $vars['minify']['js']['themify.parallaxit'] = themify_enque(THEMIFY_BUILDER_URI . '/js/premium/themify.parallaxit.js', true);
            $vars['minify']['js']['themify.ticks'] = themify_enque(THEMIFY_BUILDER_URI . '/js/themify-ticks.js', true);
            $vars['minify']['css']['themify-builder-style'] = themify_enque(THEMIFY_BUILDER_URI . '/css/themify-builder-style.css', true);

            return $vars;
        }

        /**
         * Register Static badge js script
         */
        public function builder_static_badge_scripts() {
            add_action( 'admin_footer', array( $this, 'print_static_content_badge_templates' ) );
			//add_action( 'edit_form_top', array( $this, 'static_badge_hide_content' ) );
            wp_enqueue_script('themify-static-badge', themify_enque(THEMIFY_BUILDER_URI . '/js/themify-builder-static-badge.js'), array('mce-view'), false, 1);
        }

        public function static_badge_hide_content() {
            global $post;
            $post->post_content = preg_replace( '/<!--themify_builder_static-->([\s\S]*?)<!--\/themify_builder_static-->/', '<!--themify_builder_static-->&nbsp;<!--/themify_builder_static-->', $post->post_content );
        }

        /**
         * Static badge js template
         */
        public function print_static_content_badge_templates() { ?>
            <script type="text/html" id="tmpl-tb-static-badge">
                <div class="tb_static_badge_box">
                    <?php if( ! Themify_Builder_Model::is_gutenberg_editor() ): ?>
                    <h4><?php esc_html_e( 'Themify Builder Placeholder', 'themify' );?></h4>
                    <p><?php esc_html_e( 'This badge represents where the Builder content will append on the frontend. You can move this placeholder anywhere within the editor or add content before or after.', 'themify' );?></p>
                    <p><?php echo sprintf( '%s <a href="#" class="tb_mce_view_frontend_btn">%s</a> | <a href="#" class="tb_mce_view_backend_btn">%s</a>', esc_html__( 'Edit Builder:', 'themify' ), esc_html__( 'Frontend', 'themify' ), esc_html__( 'Backend', 'themify' ) ); ?></p>
                    <?php endif; ?>
                </div>
            </script>
            <?php if( Themify_Builder_Model::is_gutenberg_editor() ): ?>
                <div style="display: none;"><?php wp_editor( ' ', 'tb_lb_hidden_editor' );?></div>
            <?php endif; ?>
        <?php
        }

        /**
         * Register css in tinymce editor.
         * @param string $mce_css 
         * @return string
         */
        public function builder_static_badge_css( $mce_css ) {
            global $pagenow;
            if (in_array( $pagenow, array( 'post.php', 'post-new.php' ),true ) ) {
                $mce_css .= ', ' . themify_enque(THEMIFY_BUILDER_URI . '/css/themify-builder-static-badge.css');
            }
            return $mce_css;
        }

        /**
         * Save Module Favorite Data
         *
         * @return void
         */
        public function save_module_favorite_data() {
                $module = isset( $_POST[ 'module_name' ] ) ? $_POST[ 'module_name' ] : null;
                $module_state = isset( $_POST[ 'module_state' ] ) ? $_POST[ 'module_state' ] : 0;

                if( $module ) {
                        $user_favorite_modules = get_user_option( 'themify_module_favorite', get_current_user_id() );
                        $user_favorite_modules = ! empty( $user_favorite_modules )
                                ? array_merge( (array) json_decode( $user_favorite_modules ) , array( $module => $module_state ) )
                                : array( $module => $module_state );
                        update_user_option( get_current_user_id()
                                , 'themify_module_favorite'
                                , json_encode( $user_favorite_modules ) );
                }

                die();
        }

        /**
         * Proxy for Themify_Builder_Component_Base::retrieve_template() method, kept for backward compatibility.
         *
         * @param $template_name
         * @param array $args
         * @param string $template_path
         * @param string $default_path
         * @param bool $echo
         *
         * @return string
         */
        public function retrieve_template( $template_name, $args = array(), $template_path = '', $default_path = '', $echo = true ) {
                return Themify_Builder_Component_Base::retrieve_template( $template_name, $args, $template_path, $default_path, $echo );
        }
        
        public function load_visual_templates(){
            check_ajax_referer('tb_load_nonce', 'tb_load_nonce');
            $response = array();
            foreach (Themify_Builder_Model::$modules as $module) {
                $template = $module->print_template();
                if($template){
                    $response[$module->slug] = preg_replace('!\s+!', ' ', $template);
                }
            }
            echo json_encode($response);
            wp_die();
        }
        
        public function load_form_templates(){
            check_ajax_referer('tb_load_nonce', 'tb_load_nonce');
            Themify_Builder_Components_Manager::render_components_form_content(true);
            wp_die();
        }
        
        private static function get_tick_options(){
            return array(
                    'tick'=>Themify_Builder_Model::get_transient_time(),
                    'ajaxurl' => admin_url('admin-ajax.php'),
                    'postID'=>Themify_Builder_Model::get_ID()
                );
        } 


        public function updateTick(){
            if(!empty($_POST['postID'])){
                $id = (int)$_POST['postID'];
                $uid = Themify_Builder_Model::get_edit_transient($id);
                $current = get_current_user_id();      
                if(!$uid || $uid==$current || !empty($_POST['take'])){
                    Themify_Builder_Model::set_edit_transient($id,$current);
                    echo '1';
                }
                elseif($uid && $uid!=$current && empty($_POST['take'])){
                    
                    $this->restriction_id = $uid;
                    $this->load_restriction(true);
                }
            }
            wp_die();
        }
        
        public function help(){
            check_ajax_referer('tb_load_nonce', 'tb_load_nonce');
            include THEMIFY_BUILDER_INCLUDES_DIR.'/themify-builder-help-video.php';
            die;
        }

        /**
         * Load content of only current inner page
         * @param $builder_data 
         * @param $page_breaks count of page break modules
         * @return array
         */
        public function load_current_inner_page_content($builder_data,$page_breaks) {
            global $page, $pages, $multipage, $numpages;
            $page  = (int) get_query_var( 'page' );
            $temp_data = array();
            $page_num = 1;
            foreach($builder_data as $row){
                if(isset($row['styling']) && isset($row['styling']['custom_css_row']) && strpos($row['styling']['custom_css_row'],'tb-page-break')!==false){
                    $page_num++;
                    continue;
                }
                $temp_data[$page_num][] = $row;
            }
            // Adjust the globals
            $page      = ( $page > $page_breaks+1 || $page < 1 ) ? 1 : $page;
            $numpages  = $page_breaks+1;
            $multipage = 1;
            if(isset($temp_data[$page])){
                return $temp_data[$page];
            }else{
                return $builder_data;
            }
        }

        /**
         * Find for old URLs in generated CSS files and regenerates them base on new URLs
         *
         */
        public function themify_regenerate_css_files_ajax() {
            check_ajax_referer( 'ajax-nonce', 'nonce' );
            if ( isset( $_POST['files'] ) && '' != $_POST['files'] ) {
                $files_id = json_decode(stripslashes($_POST['files']));
                $upload_dir = wp_upload_dir();
                $themify_css_dir = $upload_dir['basedir'].'/themify-css';
                $filesystem = Themify_Filesystem::get_instance();
                foreach($files_id as $file_id){
                    $file = $themify_css_dir . '/themify-builder-'.$file_id.'-generated.css';
                    if ( $content = $filesystem->execute->get_contents($file) ) {
                        $new_content = preg_replace("/(https?:\/\/.*wp-content\/uploads)/Ui", $upload_dir['baseurl'], $content);
                        $filesystem->execute->put_contents($file,$new_content);
                    }
                }
                set_transient( 'themify_regenerate_css_in_progress_'.$_POST['group_number'], true,1000);
                $finished = true;
                for($i=1;$i<=$_POST['total_groups'];$i++){
                    if($i == $_POST['group_number']){
                        continue;
                    }
                    if ( false === ( get_transient( 'themify_regenerate_css_in_progress_'.$i ) ) ) {
                        $finished = false;
                        break;
                    }
                }
                if ( $finished ) {
                    for($i=1;$i<=$_POST['total_groups'];$i++){
                        delete_transient( 'themify_regenerate_css_in_progress_'.$i );
                    }
                    echo 'finished';
                } else{
                    echo  $_POST['group_number'].' from '.$_POST['total_groups'];
                }
            }
            die();
        }
}
endif;
