<?php
/**
 * This file contain abstraction class to create module object.
 *
 * Themify_Builder_Component_Module class should be used as main class and
 * create any child extend class for module.
 * 
 *
 * @package    Themify_Builder
 * @subpackage Themify_Builder/classes
 */

/**
 * The abstract class for Module.
 *
 * Abstraction class to initialize module object, don't initialize
 * this class directly but please create child class of it.
 *
 *
 * @package    Themify_Builder
 * @subpackage Themify_Builder/classes
 * @author     Themify
 */
class Themify_Builder_Component_Module extends Themify_Builder_Component_Base {

    /**
     * Module Name.
     * 
     * @access public
     * @var string $name
     */
    public $name;

    /**
     * Module Slug.
     * 
     * @access public
     * @var string $slug
     */
    public $slug;

    /**
     * Custom Post Type arguments.
     * 
     * @access public
     * @var array $cpt_args
     */
    public $cpt_args = array();

    /**
     * Metabox options.
     * 
     * @access public
     * @var array $meta_box
     */
    protected $meta_box = array();
    

    /**
     * Constructor.
     * 
     * @access public
     * @param array $params 
     */
    public function __construct($params) {
        $this->name = $params['name'];
        $this->slug = $params['slug'];
        add_filter('themify_builder_addons_assets', array($this, 'themify_builder_addons_assets'), 10, 1);
        add_filter('themify_builder_module_lightbox_form_settings', array($this, 'get_animation_settings'), 10, 2);
    }

    /**
     * Get module options.
     * 
     * @access public
     */
    public function get_options() {
        return array();
    }

    /**
     * Get module styling options.
     * 
     * @access public
     */
    public function get_styling() {
        return array();
    }

    /**
     * Render a module, as a plain text
     *
     * @return string
     */
    public function get_plain_text($module) {
        $options = $this->get_options();
        if (empty($options))
            return '';
        $out = array();

        foreach ($options as $field) {
            // sanitization, check for existence of needed keys
            if (!isset($field['type'],$field['id'],$module[$field['id']])) {
                continue;
            }
            // text, textarea, and wp_editor field types
            if (in_array($field['type'], array('text', 'textarea', 'wp_editor'), true)) {
                $out[] = $module[$field['id']];
            }
            // builder field type
            elseif ($field['type'] === 'builder' && is_array($module[$field['id']])) {
                // gather text field types included in the "builder" field type
                $text_fields = array();
                foreach ($field['options'] as $row_field) {
                    if (isset($row_field['type']) && in_array($row_field['type'], array('text', 'textarea', 'wp_editor'), true)) {
                        $text_fields[] = $row_field['id'];
                    }
                }
                foreach ($module[$field['id']] as $row) {
                    // separate fields from the row that have text fields
                    $texts = array_intersect_key($row, array_flip($text_fields));
                    // add them to the output
                    $out = array_merge(array_values($texts), $out);
                }
            }
        }

        return implode(' ', $out);
    }

    /**
     * Load builder modules
     */
    public static function load_modules() {
        // load modules
        $active_modules = Themify_Builder_Model::get_modules('active');

        foreach ($active_modules as $m) {
            $path = $m['dirname'] . '/' . $m['basename'];
            require_once( $path );
        }
    }

    public function get_assets() {
        
    }

    /**
     * Load the script files for the frontend
     *
     * @todo: move this to do_assets() method
     */
    function themify_builder_addons_assets($assets) {
        $_assets = $this->get_assets();
        if (!empty($_assets)) {
            $assets[$this->slug] = $_assets;
        }
        return $assets;
    }

    public function do_assets() {
        $output = '';
        static $done = false;
        if ($done===false) {
            $assets = $this->get_assets();
            if (!empty($assets['css'])) {
                foreach ((array) $assets['css'] as $stylesheet) {
                    $ver = isset($assets['ver']) ? '?ver=' . $assets['ver'] : '';
                    $link_tag = "<link id='{$this->slug}-css' rel='stylesheet' href='{$stylesheet}{$ver}' type='text/css' />";
                    $output .= '<script type="text/javascript">document.body.insertAdjacentHTML( "beforebegin", "' . $link_tag . '" )</script>';
                }
            }
            $done = true;
        }
        return $output;
    }

    public function render($slug, $mod_id, $builder_id, $settings, $all_data = array() ) {
        $template = in_array($slug, array('highlight', 'testimonial', 'post', 'portfolio'), true) ? 'blog' : $slug;
        
        if ( isset( $all_data['element_id'] ) ) 
            $settings['element_id'] = $all_data['element_id'];

        $vars = array(
            'module_ID' => $mod_id,
            'mod_name' => $slug,
            'builder_id' => $builder_id,
            'mod_settings' => $settings
        );
        return self::retrieve_template('template-' . $template . '.php', $vars, '', '', false);
    }

    /**
     * Initialize Custom Post Type.
     * 
     * @access public
     * @param array $args 
     */
    public function initialize_cpt($args) {
        $this->cpt_args = $args;
        add_action( 'init', array($this, 'load_cpt'), 40 );
        add_filter( 'post_updated_messages', array($this, 'cpt_updated_messages') );
    }

    /**
     * Load Custom Post Type.
     * 
     * @access public
     */
    public function load_cpt() {
        global $ThemifyBuilder;

        if (post_type_exists($this->slug)) {
            // check taxonomy register
            if (!taxonomy_exists($this->slug . '-category')) {
                $this->register_taxonomy();
            }
        } else {
            $this->register_cpt();
            $this->register_taxonomy();
            add_filter('themify_do_metaboxes', array($this, 'cpt_meta_boxes'));

            // push to themify builder class
            $ThemifyBuilder->push_post_types($this->slug);
        }
    }

    /**
     * Customize post type updated messages.
     * 
     * @access public
     * @param $messages
     * @return mixed
     */
    public function cpt_updated_messages($messages) {
        global $post, $post_ID;
        $view = get_permalink($post_ID);

        $messages[$this->slug] = array(
            0 => '',
            1 => sprintf(__('%s updated. <a href="%s">View %s</a>.', 'themify'), $this->name, esc_url($view), $this->name),
            2 => __('Custom field updated.', 'themify'),
            3 => __('Custom field deleted.', 'themify'),
            4 => sprintf(__('%s updated.', 'themify'), $this->name),
            5 => isset($_GET['revision']) ? sprintf(__('%s restored to revision from %s', 'themify'), $this->name, wp_post_revision_title((int) $_GET['revision'], false)) : false,
            6 => sprintf(__('%s published.', 'themify'), $this->name),
            7 => sprintf(__('%s saved.', 'themify'), $this->name),
            8 => sprintf(__('%s submitted.', 'themify'), $this->name),
            9 => sprintf(__('%s scheduled for: <strong>%s</strong>.', 'themify'), $this->name, date_i18n(__('M j, Y @ G:i', 'themify'), strtotime($post->post_date))),
            10 => sprintf(__('%s draft updated.', 'themify'), $this->name)
        );
        return $messages;
    }

    /**
     * Register Post type.
     * 
     * @access public
     * @return void
     */
    public function register_cpt() {
        $cpt = $this->cpt_args;
        $options = array(
            'labels' => array(
                'name' => $cpt['plural'],
                'singular_name' => $cpt['singular'],
                'add_new' => __('Add New', 'themify'),
                'add_new_item' => sprintf(__('Add New %s', 'themify'), $cpt['singular']),
                'edit_item' => sprintf(__('Edit %s', 'themify'), $cpt['singular']),
                'new_item' => sprintf(__('New %s', 'themify'), $cpt['singular']),
                'view_item' => sprintf(__('View %s', 'themify'), $cpt['singular']),
                'search_items' => sprintf(__('Search %s', 'themify'), $cpt['plural']),
                'not_found' => sprintf(__('No %s found', 'themify'), $cpt['plural']),
                'not_found_in_trash' => sprintf(__('No %s found in Trash', 'themify'), $cpt['plural']),
                'menu_name' => $cpt['plural']
            ),
            'supports' => isset($cpt['supports']) ? $cpt['supports'] : array('title', 'editor', 'thumbnail', 'custom-fields', 'excerpt'),
            //'menu_position' => $position++,
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => false,
            'publicly_queryable' => true,
            'rewrite' => array('slug' => isset($cpt['rewrite']) ? $cpt['rewrite'] : strtolower($cpt['singular'])),
            'query_var' => true,
            'can_export' => true,
            'capability_type' => 'post',
            'menu_icon' => isset($cpt['menu_icon']) ? $cpt['menu_icon'] : ''
        );

        register_post_type($this->slug, $options);
    }

    /**
     * Register Taxonomy.
     * 
     * @access public
     * @return void
     */
    public function register_taxonomy() {
        global $ThemifyBuilder;

        $cpt = $this->cpt_args;
        $options = array(
            'labels' => array(
                'name' => sprintf(__('%s Categories', 'themify'), $cpt['singular']),
                'singular_name' => sprintf(__('%s Category', 'themify'), $cpt['singular']),
                'search_items' => sprintf(__('Search %s Categories', 'themify'), $cpt['singular']),
                'popular_items' => sprintf(__('Popular %s Categories', 'themify'), $cpt['singular']),
                'all_items' => sprintf(__('All Categories', 'themify'), $cpt['singular']),
                'parent_item' => sprintf(__('Parent %s Category', 'themify'), $cpt['singular']),
                'parent_item_colon' => sprintf(__('Parent %s Category:', 'themify'), $cpt['singular']),
                'edit_item' => sprintf(__('Edit %s Category', 'themify'), $cpt['singular']),
                'update_item' => sprintf(__('Update %s Category', 'themify'), $cpt['singular']),
                'add_new_item' => sprintf(__('Add New %s Category', 'themify'), $cpt['singular']),
                'new_item_name' => sprintf(__('New %s Category', 'themify'), $cpt['singular']),
                'separate_items_with_commas' => sprintf(__('Separate %s Category with commas', 'themify'), $cpt['singular']),
                'add_or_remove_items' => sprintf(__('Add or remove %s Category', 'themify'), $cpt['singular']),
                'choose_from_most_used' => sprintf(__('Choose from the most used %s Category', 'themify'), $cpt['singular']),
                'menu_name' => sprintf(__('%s Category', 'themify'), $cpt['singular']),
            ),
            'public' => true,
            'show_in_nav_menus' => false,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_tagcloud' => true,
            'hierarchical' => true,
            'rewrite' => true,
            'query_var' => true
        );

        register_taxonomy($this->slug . '-category', array($this->slug), $options);
        add_filter('manage_edit-' . $this->slug . '-category_columns', array($ThemifyBuilder, 'taxonomy_header'), 10, 2);
        add_filter('manage_' . $this->slug . '-category_custom_column', array($ThemifyBuilder, 'taxonomy_column_id'), 10, 3);

        // admin column custom taxonomy
        add_filter('manage_taxonomies_for_' . $this->slug . '_columns', array($this, 'category_columns'));
    }

    /**
     * Category Columns.
     * 
     * @access public
     * @param array $taxonomies 
     * @return array
     */
    public function category_columns($taxonomies) {
        $taxonomies[] = $this->slug . '-category';
        return $taxonomies;
    }

    /**
     * If there's not an options tab in Themify Custom Panel meta box already defined for this post type, like "Portfolio Options", add one.
     *
     * @since 2.3.8
     *
     * @param array $meta_boxes
     *
     * @return array
     */
    public function cpt_meta_boxes($meta_boxes = array()) {
        $meta_box_id = $this->slug . '-options';
        if (!in_array($meta_box_id, wp_list_pluck($meta_boxes, 'id'), true)) {
            $meta_boxes = array_merge($meta_boxes, array(
                array(
                    'name' => esc_html__(sprintf(__('%s Options', 'themify'), $this->cpt_args['singular'])),
                    'id' => $meta_box_id,
                    'options' => $this->meta_box,
                    'pages' => $this->slug
                )
            ));
        }
        return $meta_boxes;
    }

    /**
     * Get Module Title.
     * 
     * @access public
     * @param object $module 
     */
    public function get_title($module) {
        return '';
    }

    public function get_name() {
        return $this->slug;
    }

    public function print_template($echo=false) {
        ob_start();
        $template_file = Themify_Builder_Component_Base::locate_template( 'template-' . $this->slug . '-visual.php' );
        if( file_exists( $template_file ) ) {
            include $template_file;
        } else {
            $this->_visual_template();
        }
        $content_template = ob_get_clean();
        if (empty($content_template)) {
            return false;
        }
        $output = '<script type="text/html" id="tmpl-builder-'.$this->slug.'-content">'.$content_template.'</script>';
        if(!$echo){
            return $output;
        }
        echo $output;
    }

    protected function _visual_template() {
        
    }

    public function get_default_settings() {
        return false;
    }

    public function get_visual_type() {
        return 'live';
    }

    protected static function get_module_args() {
        static $args = null;
        if($args===null){
            $args = array();
            $args['before_title'] = '<h3 class="module-title">';
            $args['after_title'] = '</h3>';
            $args = apply_filters('themify_builder_module_args', $args);
        }
        return $args;
    }

    private function get_animation() {
       return Themify_Builder_Model::get_animation();
    }

    private function get_form_settings() {
        $module_form_settings = array(
            'setting' => array(
                'name' => ucfirst($this->name),
                'options' => apply_filters('themify_builder_module_settings_fields', $this->get_options(), $this)
            ),
            'styling' => array(
                'name' => esc_html__('Styling', 'themify'),
                'options' => apply_filters('themify_builder_styling_settings_fields', $this->get_styling(), $this)
            )
        );
        return apply_filters('themify_builder_module_lightbox_form_settings', $module_form_settings, $this);
    }
    
    public function get_animation_settings( $settings, $module){
        $settings['animation'] = array(
                'name' => esc_html__('Animation', 'themify'),
                'options' => apply_filters('themify_builder_animation_settings_fields', $this->get_animation(), $this)
        );
        return $settings;
    }

    protected function _form_template() {
        $module_form_settings = $this->get_form_settings();
        ?>
        <form id="tb_module_settings">
            <div id="tb_lightbox_options_tab_items">
                <?php
                foreach ($module_form_settings as $setting_key => $setting):
                    if (!empty($setting['options'])):
                        ?>
                        <li <?php if ($setting_key === 'setting'): ?>class="current"<?php endif; ?>>
                            <a href="#tb_options_<?php echo $setting_key; ?>"><?php echo esc_attr($setting['name']); ?></a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <?php $this->get_save_btn( esc_html__( 'Done', 'themify' ) ); ?>
            <?php foreach ($module_form_settings as $setting_key => $setting): ?>
                <div id="tb_options_<?php echo $setting_key; ?>" class="tb_options_tab_wrapper">
                    <?php
                    if ($setting_key === 'styling') {
                        self::get_breakpoint_switcher();
                    }
                    ?>
                    <div class="tb_options_tab_content">
                        <?php
                        if (!empty($setting['options'])) {

                            if ('setting' === $setting_key) {
                                themify_builder_module_settings_field($setting['options'], $this->slug);
                            } else {
                                themify_render_styling_settings($setting['options']);
                                if ('styling' === $setting_key) {
                                    ?>
                                    <p>
                                        <a href="#" class="reset-styling">
                                            <i class="ti-close"></i>
                                            <?php _e('Reset Styling', 'themify') ?>
                                        </a>
                                    </p>
                                    <?php
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </form>
        <?php
    }

    /**
     * Get template for module
     * @param $mod
     * @param bool $echo
     * @param bool $wrap
     * @param array $identifier
     * @return bool|string
     */
    public static function template($mod, $builder_id = 0, $echo = true, $identifier = array()) {
        if (!Themify_Builder::$frontedit_active) {
            /* allow addons to control the display of the modules */
            $display = apply_filters('themify_builder_module_display', true, $mod, $builder_id, $identifier);
            if (false === $display || ( isset( $mod['mod_settings']['visibility_all'] ) && $mod['mod_settings']['visibility_all'] === 'hide_all' ) ) {
                return false;
            }
        }
        $mod['mod_name'] = isset($mod['mod_name']) ? $mod['mod_name'] : '';
        // check whether module active or not
        if (!Themify_Builder_Model::check_module_active($mod['mod_name'])) {
            return false;
        }
        $mod['mod_settings'] = isset($mod['mod_settings']) ? $mod['mod_settings'] : array();

        $mod_id = $mod['mod_name'] . '-' . $builder_id . '-' . implode('-', $identifier);
        $output = PHP_EOL; // add line break

        $mod['mod_settings'] = $mod['mod_settings']+self::get_module_args();
        ob_start();
        do_action('themify_builder_background_styling',$builder_id,$mod,$mod_id,'module');
        $output .= ob_get_clean();
        // add line break
        $output .= PHP_EOL;
			
        // render the module
        $output .= Themify_Builder_Model::$modules[ $mod['mod_name'] ]->render($mod['mod_name'], $mod_id, $builder_id, $mod['mod_settings'], $mod);
        // add line break
        $output .= PHP_EOL;

        if ($echo) {
            echo $output;
        } else {
            return $output;
        }
    }

    /**
     * Returns module title custom style
     * @param string $slug 
     * @return array
     */
    protected function module_title_custom_style() {
        $selector = sprintf('.module.module-%s .module-title', $this->slug);
        return array(
            // Background
            self::get_seperator('module_title_background', __('Background', 'themify')),
            self::get_color($selector, 'background_color_module_title', __('Background Color', 'themify'), 'background-color'),
            // Font
            self::get_seperator('module_title_font', __('Font', 'themify')),
            self::get_font_family($selector, 'font_family_module_title'),
			self::get_element_font_weight($selector, 'font_weight_module_title'),
            self::get_color($selector, 'font_color_module_title', __('Font Color', 'themify')),
            self::get_font_size($selector, 'font_size_module_title'),
            self::get_line_height($selector, 'line_height_module_title'),
            self::get_text_align($selector, 'text_align_module_title')
        );
    }

    /**
     * Get plain content of the module output.
     * 
     * @param array $module 
     * @return string
     */
    public function get_plain_content( $module ) {
        $module['mod_settings'] = wp_parse_args( $module['mod_settings'], array(
            '_render_plain_content' => true
        ) );

        // Remove format text filter including do_shortcode
        if (!Themify_Builder_Model::is_front_builder_activate()) {
            remove_filter('themify_builder_module_content', array('Themify_Builder_Model', 'format_text'));
        }
        return self::template( $module, 0, false );
    }
     
}
