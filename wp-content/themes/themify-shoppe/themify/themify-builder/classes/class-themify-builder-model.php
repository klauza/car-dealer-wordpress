<?php

/**
 * Class for interact with DB or data resource and state. 
 *
 * @package    Themify_Builder
 * @subpackage Themify_Builder/classes
 */
final class Themify_Builder_Model {

    /**
     * Feature Image
     * @var array
     */
    public static $post_image = array();

    /**
     * Feature Image Size
     * @var array
     */
    public static $featured_image_size = array();

    /**
     * Image Width
     * @var array
     */
    public static $image_width = array();

    /**
     * Image Height
     * @var array
     */
    public static $image_height = array();

    /**
     * External Link
     * @var array
     */
    public static $external_link = array();

    /**
     * Lightbox Link
     * @var array
     */
    public static $lightbox_link = array();
    public static $modules = array();
    public static $layouts_version_name = 'tbuilder_layouts_version';
    private static $transient_name = 'tb_edit_';

    /**
     * Active custom post types registered by Builder.
     *
     * @var array
     */
    public static $builder_cpt = array();

    /**
     * Directory Registry
     */
    private static $directory_registry = array();

    private function __construct() {
        
    }

    public static function register_module($module_class) {
        $instance = new $module_class();
        self::$modules[$instance->slug] = self::is_front_builder_activate() || is_admin()?self::add_favorite_option( $instance ):$instance;
    }

	/**
	 * Add favorite option to module instance
	 * @return object
	 */
	private static function add_favorite_option(Themify_Builder_Component_Module $instance ) {
                static $favorite_data = NULL;
                if($favorite_data===NULL){
                    $favorite_data = get_user_option( 'themify_module_favorite', get_current_user_id() );
                    $favorite_data = ! empty( $favorite_data )?(array) json_decode( $favorite_data ):array();
                }
                $instance->favorite = ! empty( $favorite_data[ $instance->slug ] );
		return $instance;
	}

    /**
     * Check whether builder is active or not
     * @return bool
     */
    public static function builder_check() {
        static $enable_builder = NULL;
        if ($enable_builder === null) {
            $enable_builder = apply_filters('themify_enable_builder', themify_builder_get('setting-page_builder_is_active', 'builder_is_active'));
            $enable_builder = !('disable' === $enable_builder);
        }
        return $enable_builder;
    }

    /**
     * Check whether module is active
     * @param $name
     * @return boolean
     */
    public static function check_module_active($name) {
        return isset(self::$modules[$name]);
    }

    /**
     * Check is frontend editor page
     */
    public static function is_frontend_editor_page() {
        static $active = NULL;
        if ($active === null) {
            $active = is_user_logged_in() && current_user_can('edit_pages', get_the_ID());
            $active = apply_filters('themify_builder_is_frontend_editor', $active);
        }
        return $active;
    }

    /**
     * Check if builder frontend edit being invoked
     */
    public static function is_front_builder_activate() {
        static $active = NULL;
        if ($active === null) {
            $active = isset($_GET['tb-preview']) && self::is_frontend_editor_page();
        }
        return $active;
    }

    /**
     * Load general metabox fields
     */
    public static function load_general_metabox() {
        // Feature Image
        self::$post_image = apply_filters('themify_builder_metabox_post_image', array(
            'name' => 'post_image',
            'title' => __('Featured Image', 'themify'),
            'description' => '',
            'type' => 'image',
            'meta' => array()
        ));
        // Featured Image Size
        self::$featured_image_size = apply_filters('themify_builder_metabox_featured_image_size', array(
            'name' => 'feature_size',
            'title' => __('Image Size', 'themify'),
            'description' => sprintf(__('Image sizes can be set at <a href="%s">Media Settings</a>', 'themify'), admin_url('options-media.php')),
            'type' => 'featimgdropdown'
        ));
        // Image Width
        self::$image_width = apply_filters('themify_builder_metabox_image_width', array(
            'name' => 'image_width',
            'title' => __('Image Width', 'themify'),
            'description' => '',
            'type' => 'textbox',
            'meta' => array('size' => 'small')
        ));
        // Image Height
        self::$image_height = apply_filters('themify_builder_metabox_image_height', array(
            'name' => 'image_height',
            'title' => __('Image Height', 'themify'),
            'description' => '',
            'type' => 'textbox',
            'meta' => array('size' => 'small'),
            'class' => self::is_img_php_disabled() ? 'builder_show_if_enabled_img_php' : '',
        ));
        // External Link
        self::$external_link = apply_filters('themify_builder_metabox_external_link', array(
            'name' => 'external_link',
            'title' => __('External Link', 'themify'),
            'description' => __('Link Featured Image and Post Title to external URL', 'themify'),
            'type' => 'textbox',
            'meta' => array()
        ));
        // Lightbox Link
        self::$lightbox_link = apply_filters('themify_builder_metabox_lightbox_link', array(
            'name' => 'lightbox_link',
            'title' => __('Lightbox Link', 'themify'),
            'description' => __('Link Featured Image to lightbox image, video or external iframe', 'themify'),
            'type' => 'textbox',
            'meta' => array()
        ));
    }

    /**
     * Get module name by slug
     * @param string $slug 
     * @return string
     */
    public static function get_module_name($slug) {
        return isset(self::$modules[$slug]) && is_object(self::$modules[$slug]) ? self::$modules[$slug]->name : $slug;
    }

    /**
     * Set Pre-built Layout version
     */
    public static function set_current_layouts_version($version) {
        return set_transient(self::$layouts_version_name, $version);
    }

    /**
     * Get current Pre-built Layout version
     */
    public static function get_current_layouts_version() {
        $current_layouts_version = get_transient(self::$layouts_version_name);
        if (false === $current_layouts_version) {
            self::set_current_layouts_version('0');
            $current_layouts_version = '0';
        }
        return $current_layouts_version;
    }

    /**
     * Check whether layout is pre-built layout or custom
     */
    public static function is_prebuilt_layout($id) {
        $protected = get_post_meta($id, '_themify_builder_prebuilt_layout', true);
        return isset($protected) && 'yes' === $protected;
    }

    /**
     * Return animation presets
     */
    public static function get_preset_animation() {
        static $animation = null;
        if ($animation === null) {
            $animation = array(
                array('group_label' => __('Attention Seekers', 'themify'), 'options' => array(
                        array('value' => 'bounce', 'name' => __('bounce', 'themify')),
                        array('value' => 'flash', 'name' => __('flash', 'themify')),
                        array('value' => 'pulse', 'name' => __('pulse', 'themify')),
                        array('value' => 'rubberBand', 'name' => __('rubberBand', 'themify')),
                        array('value' => 'shake', 'name' => __('shake', 'themify')),
                        array('value' => 'swing', 'name' => __('swing', 'themify')),
                        array('value' => 'tada', 'name' => __('tada', 'themify')),
                        array('value' => 'wobble', 'name' => __('wobble', 'themify')),
                        array('value' => 'jello', 'name' => __('jello', 'themify'))
                    )),
                array('group_label' => __('Bouncing Entrances', 'themify'), 'options' => array(
                        array('value' => 'bounceIn', 'name' => __('bounceIn', 'themify')),
                        array('value' => 'bounceInDown', 'name' => __('bounceInDown', 'themify')),
                        array('value' => 'bounceInLeft', 'name' => __('bounceInLeft', 'themify')),
                        array('value' => 'bounceInRight', 'name' => __('bounceInRight', 'themify')),
                        array('value' => 'bounceInUp', 'name' => __('bounceInUp', 'themify'))
                    )),
                array('group_label' => __('Bouncing Exits', 'themify'), 'options' => array(
                        array('value' => 'bounceOut', 'name' => __('bounceOut', 'themify')),
                        array('value' => 'bounceOutDown', 'name' => __('bounceOutDown', 'themify')),
                        array('value' => 'bounceOutLeft', 'name' => __('bounceOutLeft', 'themify')),
                        array('value' => 'bounceOutRight', 'name' => __('bounceOutRight', 'themify')),
                        array('value' => 'bounceOutUp', 'name' => __('bounceOutUp', 'themify'))
                    )),
                array('group_label' => __('Fading Entrances', 'themify'), 'options' => array(
                        array('value' => 'fadeIn', 'name' => __('fadeIn', 'themify')),
                        array('value' => 'fadeInDown', 'name' => __('fadeInDown', 'themify')),
                        array('value' => 'fadeInDownBig', 'name' => __('fadeInDownBig', 'themify')),
                        array('value' => 'fadeInLeft', 'name' => __('fadeInLeft', 'themify')),
                        array('value' => 'fadeInLeftBig', 'name' => __('fadeInLeftBig', 'themify')),
                        array('value' => 'fadeInRight', 'name' => __('fadeInRight', 'themify')),
                        array('value' => 'fadeInRightBig', 'name' => __('fadeInRightBig', 'themify')),
                        array('value' => 'fadeInUp', 'name' => __('fadeInUp', 'themify')),
                        array('value' => 'fadeInUpBig', 'name' => __('fadeInUpBig', 'themify'))
                    )),
                array('group_label' => __('Fading Exits', 'themify'), 'options' => array(
                        array('value' => 'fadeOut', 'name' => __('fadeOut', 'themify')),
                        array('value' => 'fadeOutDown', 'name' => __('fadeOutDown', 'themify')),
                        array('value' => 'fadeOutDownBig', 'name' => __('fadeOutDownBig', 'themify')),
                        array('value' => 'fadeOutLeft', 'name' => __('fadeOutLeft', 'themify')),
                        array('value' => 'fadeOutLeftBig', 'name' => __('fadeOutLeftBig', 'themify')),
                        array('value' => 'fadeOutRight', 'name' => __('fadeOutRight', 'themify')),
                        array('value' => 'fadeOutRightBig', 'name' => __('fadeOutRightBig', 'themify')),
                        array('value' => 'fadeOutUp', 'name' => __('fadeOutUp', 'themify')),
                        array('value' => 'fadeOutUpBig', 'name' => __('fadeOutUpBig', 'themify'))
                    )),
                array('group_label' => __('Flippers', 'themify'), 'options' => array(
                        array('value' => 'flip', 'name' => __('flip', 'themify')),
                        array('value' => 'flipInX', 'name' => __('flipInX', 'themify')),
                        array('value' => 'flipInY', 'name' => __('flipInY', 'themify')),
                        array('value' => 'flipOutX', 'name' => __('flipOutX', 'themify')),
                        array('value' => 'flipOutY', 'name' => __('flipOutY', 'themify'))
                    )),
                array('group_label' => __('Lightspeed', 'themify'), 'options' => array(
                        array('value' => 'lightSpeedIn', 'name' => __('lightSpeedIn', 'themify')),
                        array('value' => 'lightSpeedOut', 'name' => __('lightSpeedOut', 'themify'))
                    )),
                array('group_label' => __('Rotating Entrances', 'themify'), 'options' => array(
                        array('value' => 'rotateIn', 'name' => __('rotateIn', 'themify')),
                        array('value' => 'rotateInDownLeft', 'name' => __('rotateInDownLeft', 'themify')),
                        array('value' => 'rotateInDownRight', 'name' => __('rotateInDownRight', 'themify')),
                        array('value' => 'rotateInUpLeft', 'name' => __('rotateInUpLeft', 'themify')),
                        array('value' => 'rotateInUpRight', 'name' => __('rotateInUpRight', 'themify'))
                    )),
                array('group_label' => __('Rotating Exits', 'themify'), 'options' => array(
                        array('value' => 'rotateOut', 'name' => __('rotateOut', 'themify')),
                        array('value' => 'rotateOutDownLeft', 'name' => __('rotateOutDownLeft', 'themify')),
                        array('value' => 'rotateOutDownRight', 'name' => __('rotateOutDownRight', 'themify')),
                        array('value' => 'rotateOutUpLeft', 'name' => __('rotateOutUpLeft', 'themify')),
                        array('value' => 'rotateOutUpRight', 'name' => __('rotateOutUpRight', 'themify'))
                    )),
                array('group_label' => __('Specials', 'themify'), 'options' => array(
                        array('value' => 'hinge', 'name' => __('hinge', 'themify')),
                        array('value' => 'rollIn', 'name' => __('rollIn', 'themify')),
                        array('value' => 'rollOut', 'name' => __('rollOut', 'themify'))
                    )),
                array('group_label' => __('Zoom Entrances', 'themify'), 'options' => array(
                        array('value' => 'zoomIn', 'name' => __('zoomIn', 'themify')),
                        array('value' => 'zoomInDown', 'name' => __('zoomInDown', 'themify')),
                        array('value' => 'zoomInLeft', 'name' => __('zoomInLeft', 'themify')),
                        array('value' => 'zoomInRight', 'name' => __('zoomInRight', 'themify')),
                        array('value' => 'zoomInUp', 'name' => __('zoomInUp', 'themify'))
                    )),
                array('group_label' => __('Zoom Exits', 'themify'), 'options' => array(
                        array('value' => 'zoomOut', 'name' => __('zoomOut', 'themify')),
                        array('value' => 'zoomOutDown', 'name' => __('zoomOutDown', 'themify')),
                        array('value' => 'zoomOutLeft', 'name' => __('zoomOutLeft', 'themify')),
                        array('value' => 'zoomOutRight', 'name' => __('zoomOutRight', 'themify')),
                        array('value' => 'zoomOutUp', 'name' => __('zoomOutUp', 'themify'))
                    )),
                array('group_label' => __('Slide Entrance', 'themify'), 'options' => array(
                        array('value' => 'slideInDown', 'name' => __('slideInDown', 'themify')),
                        array('value' => 'slideInLeft', 'name' => __('slideInLeft', 'themify')),
                        array('value' => 'slideInRight', 'name' => __('slideInRight', 'themify')),
                        array('value' => 'slideInUp', 'name' => __('slideInUp', 'themify'))
                    )),
                array('group_label' => __('Slide Exit', 'themify'), 'options' => array(
                        array('value' => 'slideOutDown', 'name' => __('slideOutDown', 'themify')),
                        array('value' => 'slideOutLeft', 'name' => __('slideOutLeft', 'themify')),
                        array('value' => 'slideOutRight', 'name' => __('slideOutRight', 'themify')),
                        array('value' => 'slideOutUp', 'name' => __('slideOutUp', 'themify'))
                    ))
            );
        }
        return $animation;
    }

    /**
     * Get Post Types which ready for an operation
     * @return array
     */
    public static function get_post_types() {

        // If it's not a product search, proceed: retrieve the post types.
        $types = get_post_types(array('exclude_from_search' => false));
        if (self::is_themify_theme()) {
            // Exclude pages /////////////////
            $exclude_pages = themify_builder_get('setting-search_settings_exclude');
            if (!empty($exclude_pages)) {
                unset($types['page']);
            }
            // Exclude custom post types /////
            $exclude_types = apply_filters('themify_types_excluded_in_search', get_post_types(array(
                '_builtin' => false,
                'public' => true,
                'exclude_from_search' => false
            )));

            foreach (array_keys($exclude_types) as $type) {
                $check = themify_builder_get('setting-search_exclude_' . $type);
                if (!empty($check)) {
                    unset($types[$type]);
                }
            }
        }
        // Exclude Layout and Layout Part custom post types /////
        unset($types['section'], $types['tbuilder_layout'], $types['tbuilder_layout_part']);

        return $types;
    }

    /**
     * Check whether builder animation is active
     * @return boolean
     */
    public static function is_animation_active() {
        static $is_animation = NULL;
        if ($is_animation === null) {
            // check if mobile exclude disabled OR disabled all transition
            $val = themify_builder_get('setting-page_builder_animation_appearance', 'builder_animation_appearance');
            $disable_all = $val === 'all';
            $disable_mobile = $disable_all || $val === 'mobile';
            $is_animation = self::is_premium() && (self::is_front_builder_activate() || !(($disable_all || ( $disable_mobile && themify_is_touch()))));
        }
        return $is_animation;
    }

    /**
     * Check whether builder parallax is active
     * @return boolean
     */
    public static function is_parallax_active() {
        static $is_parallax = NULL;
        if ($is_parallax === null) {
            // check if mobile exclude disabled OR disabled all transition
            $val = themify_builder_get('setting-page_builder_animation_parallax_bg', 'builder_animation_parallax_bg');
            $disable_all = $val === 'all';
            $disable_mobile = $disable_all || $val === 'mobile';
            $is_parallax = self::is_premium() && !(( $disable_mobile && themify_is_touch() ) || $disable_all);
        }
        return $is_parallax;
    }

    /**
     * Check whether builder parallax scroll is active
     * @return boolean
     */
    public static function is_parallax_scroll_active() {
        static $is_parallax_scroll = NULL;
        if ($is_parallax_scroll === null) {
            // check if mobile exclude disabled OR disabled all transition
            $val = themify_builder_get('setting-page_builder_animation_parallax_scroll', 'builder_animation_parallax_scroll');
            $disable_all = $val === 'all';
            $disable_mobile = $disable_all || $val === 'mobile';
            $is_parallax_scroll = self::is_premium() && !(( $disable_mobile && themify_is_touch() ) || $disable_all);
        }
        return $is_parallax_scroll;
    }

    /**
     * Check whether builder sticky scroll is active
     * @return boolean
     */
    public static function is_sticky_scroll_active() {
        static $is_sticky_scroll = NULL;
        if ($is_sticky_scroll === null) {
            // check if mobile exclude disabled OR disabled all transition
            $val = themify_builder_get('setting-page_builder_animation_sticky_scroll', 'builder_animation_sticky_scroll');
            $disable_all = $val === 'all';
            $disable_mobile = $disable_all || $val === 'mobile';
            $is_sticky_scroll = self::is_premium() && !(( $disable_mobile && themify_is_touch() ) || $disable_all);
        }
        return $is_sticky_scroll;
    }

    /**
     * Get Grid Settings
     * @return array
     */
    public static function get_grid_settings($setting = 'grid') {
        
        $gutters = array(
            array('name' => __('Default', 'themify'), 'value' => 'gutter-default'),
            array('name' => __('Narrow', 'themify'), 'value' => 'gutter-narrow'),
            array('name' => __('None', 'themify'), 'value' => 'gutter-none'),
        );

        $columnAlignment = array(
            array('img' => 'alignment_top', 'alignment' => 'col_align_top'),
            array('img' => 'alignment_middle', 'alignment' => 'col_align_middle'),
            array('img' => 'alignment_bottom', 'alignment' => 'col_align_bottom')
        );
        switch ($setting) {
            case 'grid':
                $value = array(
                    // Grid FullWidth
                    array('img' => '1_col', 'data' => array('-full')),
                    // Grid 2
                    array('img' => '2_col', 'data' => array('4-2', '4-2')),
                    // Grid 3
                    array('img' => '3_col', 'data' => array('3-1', '3-1', '3-1')),
                    // Grid 4
                    array('img' => '4_col', 'data' => array('4-1', '4-1', '4-1', '4-1')),
                    // Grid 5
                    array('img' => '5_col', 'data' => array('5-1', '5-1', '5-1', '5-1', '5-1')),
                    // Grid 6
                    array('img' => '6_col', 'data' => array('6-1', '6-1', '6-1', '6-1', '6-1', '6-1')),
                    array('img' => '1_4_3_4', 'data' => array('4-1', '4-3')),
                    array('img' => '1_4_1_4_2_4', 'data' => array('4-1', '4-1', '4-2')),
                    array('img' => '1_4_2_4_1_4', 'data' => array('4-1', '4-2', '4-1'),'exclude'=>true),
                    array('img' => '2_4_1_4_1_4', 'data' => array('4-2', '4-1', '4-1')),
                    array('img' => '3_4_1_4', 'data' => array('4-3', '4-1')),
                    array('img' => '2_3_1_3', 'data' => array('3-2', '3-1')),
                    array('img' => '1_3_2_3', 'data' => array('3-1', '3-2'))
                );
                break;
            case 'column_dir':
                $value = array(
                    array('img' => 'column_ltr', 'dir' => 'ltr'),
                    array('img' => 'column_rtl', 'dir' => 'rtl')
                );
                break;
            case 'column_alignment':
                $value = $columnAlignment;
                break;
            case 'column_alignment_class':
                $columnAlignmentClass = array();
                foreach ($columnAlignment as $ca) {
                    $columnAlignmentClass[] = $ca['alignment'];
                }
                $value = implode(' ', $columnAlignmentClass);
                break;
            case 'gutter_class':
                $guiterClass = array();
                foreach ($gutters as $g) {
                    $guiterClass[] = $g['value'];
                }
                $value = implode(' ', $guiterClass);
                break;
            default :
                $value = $gutters;
                break;
        }
        return $value;
    }

    /**
     * Returns list of colors and thumbnails
     *
     * @return array
     */
    public static function get_colors() {
        static $colors = null;
        if ($colors === null) {
            $colors = apply_filters( 'themify_builder_module_color_presets', array(
                array('img' => 'default', 'value' => 'default', 'label' => __('default', 'themify')),
                array('img' => 'black', 'value' => 'black', 'label' => __('black', 'themify')),
                array('img' => 'grey', 'value' => 'gray', 'label' => __('gray', 'themify')),
                array('img' => 'blue', 'value' => 'blue', 'label' => __('blue', 'themify')),
                array('img' => 'light-blue', 'value' => 'light-blue', 'label' => __('light-blue', 'themify')),
                array('img' => 'green', 'value' => 'green', 'label' => __('green', 'themify')),
                array('img' => 'light-green', 'value' => 'light-green', 'label' => __('light-green', 'themify')),
                array('img' => 'purple', 'value' => 'purple', 'label' => __('purple', 'themify')),
                array('img' => 'light-purple', 'value' => 'light-purple', 'label' => __('light-purple', 'themify')),
                array('img' => 'brown', 'value' => 'brown', 'label' => __('brown', 'themify')),
                array('img' => 'orange', 'value' => 'orange', 'label' => __('orange', 'themify')),
                array('img' => 'yellow', 'value' => 'yellow', 'label' => __('yellow', 'themify')),
                array('img' => 'red', 'value' => 'red', 'label' => __('red', 'themify')),
                array('img' => 'pink', 'value' => 'pink', 'label' => __('pink', 'themify'))
            ) );
        }
        return $colors;
    }

    /**
     * Returns list of appearance
     *
     * @return array
     */
    public static function get_appearance() {
        static $appearance = null;
        if ($appearance === null) {
            $appearance = array(
                array('name' => 'rounded', 'value' => __('Rounded', 'themify')),
                array('name' => 'gradient', 'value' => __('Gradient', 'themify')),
                array('name' => 'glossy', 'value' => __('Glossy', 'themify')),
                array('name' => 'embossed', 'value' => __('Embossed', 'themify')),
                array('name' => 'shadow', 'value' => __('Shadow', 'themify'))
            );
        }
        return $appearance;
    }

    /**
     * Returns list of border styles
     *
     * @return array
     */
    public static function get_border_styles() {
        static $border_style = NULL;
        if ($border_style === null) {
            $border_style = array(
                array('value' => 'solid', 'name' => __('Solid', 'themify')),
                array('value' => 'dashed', 'name' => __('Dashed', 'themify')),
                array('value' => 'dotted', 'name' => __('Dotted', 'themify')),
                array('value' => 'double', 'name' => __('Double', 'themify')),
                array('value' => 'none', 'name' => __('None', 'themify'))
            );
        }
        return $border_style;
    }

    /**
     * Returns list of units
     *
     * @return array
     */
    public static function get_units() {
        static $units = null;
        if ($units === null) {
            $units = array(
                array('value' => 'px', 'name' => __('px', 'themify')),
                array('value' => 'em', 'name' => __('em', 'themify')),
                array('value' => '%', 'name' => __('%', 'themify')),
            );
        }
        return $units;
    }

    /**
     * Returns list of text_aligment
     *
     * @return array
     */
    public static function get_text_aligment() {
        static $text_align = null;
        if ($text_align === null) {
            $text_align = array(
                array('value' => 'left', 'name' => __('Left', 'themify'), 'icon' => '<span class="ti-align-left"></span>'),
                array('value' => 'center', 'name' => __('Center', 'themify'), 'icon' => '<span class="ti-align-center"></span>'),
                array('value' => 'right', 'name' => __('Right', 'themify'), 'icon' => '<span class="ti-align-right"></span>'),
                array('value' => 'justify', 'name' => __('Justify', 'themify'), 'icon' => '<span class="ti-align-justify"></span>')
            );
        }
        return $text_align;
    }

    /**
     * Returns list of background repeat values
     *
     * @return array
     */
    public static function get_repeat() {
        static $repeat = null;
        if ($repeat === null) {
            $repeat = array(
                array('value' => 'repeat', 'name' => __('Repeat All', 'themify')),
                array('value' => 'repeat-x', 'name' => __('Repeat Horizontally', 'themify')),
                array('value' => 'repeat-y', 'name' => __('Repeat Vertically', 'themify')),
                array('value' => 'no-repeat', 'name' => __('Do not repeat', 'themify')),
                array('value' => 'fullcover', 'name' => __('Fullcover', 'themify'))
            );
        }
        return $repeat;
    }

	/**
	 * Returns list of background position values
	 *
	 * @return array
	 */
	public static function get_position() {
		static $position = null;
		if ($position === null) {
			$position = array(
				array('value' => 'left-top', 'name' => __( 'Left Top', 'themify')),
				array('value' => 'left-center', 'name' => __( 'Left Center', 'themify' )),
				array('value' => 'left-bottom', 'name' => __( 'Left Bottom', 'themify' )),
				array('value' => 'right-top', 'name' => __( 'Right top', 'themify')),
				array('value' => 'right-center', 'name' => __( 'Right Center', 'themify' )),
				array('value' => 'right-bottom', 'name' => __( 'Right Bottom', 'themify' )),
				array('value' => 'center-top', 'name' => __( 'Center Top', 'themify' )),
				array('value' => 'center-center', 'name' => __( 'Center Center', 'themify' )),
				array('value' => 'center-bottom', 'name' => __( 'Center Bottom', 'themify' ))
			);
		}
		return $position;
	}

    /**
     * Returns list of text_decoration
     *
     * @return array
     */
    public static function get_text_decoration() {
        static $decoration = null;
        if ($decoration === null) {
            $decoration = array(
                array('value' => 'underline', 'name' => __('Underline', 'themify'), 'icon' => '<span class="tb_text_underline">U</span>'),
                array('value' => 'overline', 'name' => __('Overline', 'themify'), 'icon' => '<span class="tb_text_overline">O</span>'),
                array('value' => 'line-through', 'name' => __('Line through', 'themify'), 'icon' => '<span class="tb_text_through">S</span>'),
                array('value' => 'none', 'name' => __('None', 'themify'), 'icon' => '–')
            );
        }
        return $decoration;
    }

    /**
     * Returns list of font style option
     *
     * @return array
     */
    public static function get_font_style() {
        static $font_style = NULL;

        if ($font_style === null) {
            $font_style = array(
                array('value' => 'italic', 'name' => __('Italic', 'themify'), 'icon' => '<span class="tb_font_italic">I</span>'),
                array('value' => 'normal', 'name' => __('Normal', 'themify'), 'icon' => 'N')
            );
        }

        return $font_style;
    }

    /**
	 * Returns list of font weight option
	 *
	 * @return array
	 */
	public static function get_font_weight() {
		static $font_weight = NULL;
		if ($font_weight === null) {
			$font_weight = array(
				array('value' => 'bold', 'name' => __('Bold', 'themify'), 'icon' => '<span class="tb_font_bold">B</span>'),
			);
		}
		return $font_weight;
	}

    /**
     * Returns list of text transform options
     *
     * @return array
     */
    public static function get_text_transform() {
        static $text_transform = NULL;

        if ($text_transform === null) {
            $text_transform = array(
                array('value' => 'uppercase', 'name' => __('Uppercase', 'themify'), 'icon' => 'AB'),
                array('value' => 'lowercase', 'name' => __('Lowercase', 'themify'), 'icon' => 'ab'),
                array('value' => 'capitalize', 'name' => __('Capitalize', 'themify'), 'icon' => 'Ab'),
                array('value' => 'none','name'=> __('None', 'themify'), 'icon' => '–')
            );
        }

        return $text_transform;
    }

    /**
     * Check whether image script is use or not
     *
     * @since 2.4.2 Check if it's a Themify theme or not. If it's not, it's Builder standalone plugin.
     *
     * @return boolean
     */
    public static function is_img_php_disabled() {
        static $is_disabled = NULL;
        if ($is_disabled === null) {
            $is_disabled = themify_builder_get('setting-img_settings_use', 'image_setting-img_settings_use') ? true : false;
        }
        return $is_disabled;
    }

    public static function is_fullwidth_layout_supported() {
        static $is_full_width = null;
        if ($is_full_width === null) {
            $is_full_width = apply_filters('themify_builder_fullwidth_layout_support', false) ? true : false;
        }
        return $is_full_width;
    }

    /**
     * Get alt text defined in WP Media attachment by a given URL
     *
     * @since 2.2.5
     * 
     * @param string $image_url
     * 
     * @return string
     */
    public static function get_alt_by_url($image_url) {
        $upload_dir = wp_upload_dir();
        $attachment_id = themify_get_attachment_id_from_url($image_url, $upload_dir['baseurl']);
        if ($attachment_id) {
            if ($alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true)) {
                return $alt;
            }
        }
        return '';
    }

    public static function get_all_module_style_rules() {
        global $ThemifyBuilder;
        $return = array();

        foreach (self::$modules as $module) {
            $styling = $module->get_styling();
            $all_rules = $ThemifyBuilder->stylesheet->make_styling_rules($styling);

            if (!empty($all_rules)) {
                foreach ($all_rules as $key => $rule) {
                    $return[$module->slug][$key] = array('prop' => $rule['prop'], 'selector' => (array) $rule['selector']);
                }
            }
        }
        return $return;
    }

    /**
     * Get all modules settings for used in localize script.
     * 
     * @access public
     * @return array
     */
    public static function get_modules_localize_settings() {
        $return = array();

        foreach (self::$modules as $module) {
            $default = $module->get_default_settings();
            $return[$module->slug]['name'] = $module->name;
            if($module->favorite){
                $return[ $module->slug ]['favorite'] = 1;
            }
            if ($default) {
                $return[$module->slug]['defaults'] = $default;
            }
            $type = $module->get_visual_type();
            if ($type) {
                $return[$module->slug]['type'] = $type;
            }
            // Localize styling options separately for copy/paste styling feature
            $styling = self::get_styling_options($module);
            if(!empty($styling)) {
                $return[$module->slug]['styling'] = $styling;
            }
        }
        uasort($return, array(__CLASS__, 'sortBy'));
        return $return;
    }

    private static function sortBy($a, $b) {
        return strnatcasecmp($a['name'], $b['name']);
    }

    public static function get_all_component_style_rules() {
        global $ThemifyBuilder;
        $return = array();

        foreach (Themify_Builder_Components_Manager::get_component_types() as $component_type) {
            $styling = $component_type->get_styling();
            $all_rules = $ThemifyBuilder->stylesheet->make_styling_rules($styling);

            if (!empty($all_rules)) {
                foreach ($all_rules as $key => $rule) {
                    $return[$component_type->get_name()][$key] = array('prop' => $rule['prop'], 'selector' => isset($rule['selector']) ? (array) $rule['selector'] : '');
                }
            }
        }

        return $return;
    }

    public static function get_elements_style_rules() {
        return array_merge(self::get_all_module_style_rules(), self::get_all_component_style_rules());
    }

    public static function is_premium() {
        static $is_premium = null;
        if ($is_premium === null) {
            $is_premium = !self::is_themify_theme() ? THEMIFY_BUILDER_NAME === 'themify-builder' : true;
        }
        return $is_premium;
    }

	public static function hasAccess() {
		static $has_access = null;

		if ( $has_access === null ) {
			$has_access = self::is_themify_theme() 
				? Themify_Access_Role::check_access_backend() 
				: (self::is_premium() && class_exists('Themify_Access_Role') 
					? Themify_Access_Role::check_access_backend() 
					: current_user_can( 'manage_options' ) );
		}

		return $has_access;
	}

    public static function get_addons_assets() {
        return apply_filters('themify_builder_addons_assets', array());
    }

    public static function localize_js($object_name, $l10n) {
        foreach ((array) $l10n as $key => $value) {
            if (is_scalar($value)) {    
                $l10n[$key] = html_entity_decode((string) $value, ENT_QUOTES, 'UTF-8');
            }
        }
        return $l10n ? "var $object_name = " . wp_json_encode($l10n) . ';' : '';
    }

    public static function format_text($content) {
        global $wp_embed;

        /**
         * @note: htmlspecialchars_decode must run *before* do_shortcode,
         * otherwise it can break shortcodes that output JSON data.
         * Ticket #5065
         */
        $content = htmlspecialchars_decode($content);
        $content = wptexturize($content);

        $pattern = '|<p>\s*(https?://[^\s"]+)\s*</p>|im'; // pattern to check embed url
        $to = '<p>' . PHP_EOL . '$1' . PHP_EOL . '</p>'; // add line break 
        $content = convert_chars($content);
        $content = $wp_embed->run_shortcode($content);
        $content = do_shortcode(shortcode_unautop($content));
        $content = preg_replace($pattern, $to, $content);
        $content = $wp_embed->autoembed($content);

        return convert_smilies($content);
    }

    /**
     * Return whether this is a Themify theme or not.
     *
     * @return bool
     */
    public static function is_themify_theme() {
        $is_theme = null;
        if ($is_theme === null) {
            $is_theme = !(defined('THEMIFY_BUILDER_VERSION') && preg_match('/[1-9].[0-9].[0-9]/', THEMIFY_BUILDER_VERSION));
        }
        return $is_theme;
    }

    /**
     * Get module php files data
     * @param string $select
     * @return array
     */
    public static function get_modules($select = 'all') {
        $_modules = array();
        $directories = self::get_directory_path('modules');

        foreach ($directories as $dir) {
            if (file_exists($dir)) {
                $d = dir($dir);
                while (( false !== ( $entry = $d->read() ))) {
                    if ($entry !== '.' && $entry !== '..' && $entry !== '.svn') {
                        $path = $d->path . $entry;
                        $module_name = basename($path);
                        $_modules[$module_name] = $path;
                    }
                }
            }
        }
        foreach ($_modules as $value) {
            if (is_dir($value)) {
                continue; /* clean-up, make sure no directories is included in the list */
            }
            $path_info = pathinfo($value);
            if (strpos($path_info['filename'], 'module-') !== 0) {
                continue; /* convention: module's file name must begin with module-* */
            }

            $id = str_replace('module-', '', $path_info['filename']);
            $name = isset(self::$modules[$id])?self::$modules[$id]->name:current(get_file_data($value, array('Module Name')));
            $modules[$id] = array(
                'name' => $name,
                'id' => $id,
                'dirname' => $path_info['dirname'],
                'basename' => $path_info['basename'],
            );
        }

        if (!empty($modules)) {
           
            if ('active' === $select) {
                if(self::is_themify_theme()){
                    $data = themify_get_data();
                    $pre= 'setting-page_builder_exc_';
                }
                else{
                    $pre= 'builder_exclude_module_';
                    $data = self::get_builder_settings();
                }
                foreach ($modules as $key => $m) {
                    $exclude = $pre.$m['id'];
                    if (!empty($data[$exclude])) {
                        unset($modules[$key]);
                    }
                }
            } elseif ('registered' === $select) {
                foreach ($modules as $key => $m) {
                    /* check if module is registered */
                    if (!self::check_module_active($key)) {
                        unset($modules[$key]);
                    }
                }
            }
        } 
        return $modules;
    }

    /**
     * Check whether theme loop template exist
     * @param string $template_name 
     * @param string $template_path 
     * @return boolean
     */
    public static function is_loop_template_exist($template_name, $template_path) {
        return !locate_template(array(trailingslashit($template_path) . $template_name)) ? false : true;
    }

    /**
     * Register default directories used to load modules and their templates
     */
    public static function setup_default_directories() {
        $theme_dir = get_template_directory();
        self::register_directory('templates', THEMIFY_BUILDER_TEMPLATES_DIR, 1);
        self::register_directory('templates', $theme_dir . '/themify-builder/', 15);
        if (is_child_theme()) {
            self::register_directory('templates', get_stylesheet_directory() . '/themify-builder/', 20);
        }
        self::register_directory('modules', THEMIFY_BUILDER_MODULES_DIR, 1);
        self::register_directory('modules', $theme_dir . '/themify-builder-modules/', 15);
    }

    public static function register_directory($context, $path, $priority = 10) {
        self::$directory_registry[$context][$priority][] = trailingslashit($path);
    }

    public static function get_directory_path($context) {
        static $dir = array();
        if(!isset($dir[$context])){
            krsort( self::$directory_registry[$context] ); 
            $dir[$context] = call_user_func_array('array_merge', self::$directory_registry[$context]);
            unset(self::$directory_registry[$context] );
        }
        return $dir[$context];
    }

    public static function remove_cache($post_id, $tag = false, array $args = array()) {
        if (Themify_Builder_Model::is_premium()) {
            TFCache::remove_cache($tag, $post_id, $args);
        }
    }

    public static function is_cpt_active($post_type) {
        $active = in_array($post_type, self::$builder_cpt);
        return apply_filters("builder_is_{$post_type}_active", $active);
    }

	public static function builder_cpt_check() {
		global $wpdb;

		foreach (array('slider', 'highlight', 'testimonial', 'portfolio') as $post_type) {
			if( post_type_exists( $post_type ) ) {
				self::$builder_cpt[] = $post_type;
			} else {
				$posts = get_posts( array(
					'post_type' => $post_type,
					'posts_per_page' => 1,
					'post_status' => 'any',
				) );
				if ( ! empty( $posts ) ) {
					self::$builder_cpt[] = $post_type;
				}
			}
		}
	}

    /**
     * Get a list of post types that can be accessed publicly
     *
     * does not include attachments, Builder layouts and layout parts,
     * and also custom post types in Builder that have their own module.
     *
     * @return array of key => label pairs
     */
    public static function get_public_post_types($exclude = true) {
        static $result = null;
        if ($result === null || !$exclude) {
            $post_types = get_post_types(array('public' => true, 'publicly_queryable' => 'true'), 'objects');
            $excluded_types = array('attachment', 'tbuilder_layout', 'tbuilder_layout_part', 'section');
            if ($exclude) {
                $excluded_types = array_merge(self::$builder_cpt, $excluded_types);
            }
            foreach ($post_types as $key => $value) {
                if (!in_array($key, $excluded_types)) {
                    $result[$key] = $value->labels->singular_name;
                }
            }

            $result = apply_filters('builder_get_public_post_types', $result);
        }
        return $result;
    }

    /**
     * Get a list of taxonomies that can be accessed publicly
     *
     * does not include post formats, section categories (used by some themes),
     * and also custom post types in Builder that have their own module.
     *
     * @return array of key => label pairs
     */
    public static function get_public_taxonomies($exclude = true) {
        static $result = null;
        if ($result === null || !$exclude) {
            $taxonomies = get_taxonomies(array('public' => true), 'objects');
            $excludes = array('post_format', 'section-category');
            if ($exclude) { // exclude taxonomies from Builder CPTs
                foreach (self::$builder_cpt as $value) {
                    $excludes[] = "{$value}-category";
                }
            }
            foreach ($taxonomies as $key => $value) {
                if (!in_array($key, $excludes)) {
                    $result[$key] = $value->labels->name;
                }
            }

            $result = apply_filters('builder_get_public_taxonomies', $result);
        }
        return $result;
    }

    /**
     * Get images from gallery shortcode
     * @return object
     */
    public static function get_images_from_gallery_shortcode($shortcode) {
        preg_match('/\[gallery.*ids=.(.*).\]/', $shortcode, $ids);
        if(isset($ids[1])){
            $ids = trim($ids[1], '\\');
            $ids = trim($ids, '"');
            $image_ids = explode(',', $ids);
            $orderby = self::get_gallery_param_option($shortcode, 'orderby');
            $orderby = $orderby != '' ? $orderby : 'post__in';
            $order = self::get_gallery_param_option($shortcode, 'order');
            $order = $order != '' ? $order : 'ASC';

            // Check if post has more than one image in gallery
            return get_posts(array(
                'post__in' => $image_ids,
                'post_type' => 'attachment',
                'post_mime_type' => 'image',
                'numberposts' => -1,
                'orderby' => $orderby,
                'order' => $order
            ));
        }
        return array();
    }

    /**
     * Get gallery shortcode options
     * @param $shortcode
     * @param $param
     */
    public static function get_gallery_param_option($shortcode, $param = 'link') {
        $pattern = '/\[gallery .*?(?=' . $param . ')' . $param . '=.([^\']+)./si';
        preg_match($pattern, $shortcode, $out);

        $out = isset($out[1]) ? explode('"', $out[1]) : array('');
        return $out[0];
    }

    public static function parse_slug_to_ids($slug_string, $post_type = 'post') {
        $slug_arr = explode(',', $slug_string);
        $return = array();
        if (!empty($slug_arr)) {
            foreach ($slug_arr as $slug) {
                $return[] = self::get_id_by_slug(trim( $slug), $post_type );
            }
        }
        return $return;
    }

    public static function get_id_by_slug($slug, $post_type = 'post') {
        $args = array(
            'name' => $slug,
            'post_type' => $post_type,
            'post_status' => 'publish',
            'numberposts' => 1,
            'no_found_rows' => true
        );
        $my_posts = get_posts($args);
        return $my_posts ? $my_posts[0]->ID : null;
    }

    public static function getMapKey() {
        return themify_builder_get('setting-google_map_key', 'builder_settings_google_map_key');
    }

    /**
     * Get initialization parameters for plupload. Filtered through themify_builder_plupload_init_vars.
     * @return mixed|void
     * @since 1.4.2
     */
    public static function get_builder_plupload_init() {
        return apply_filters('themify_builder_plupload_init_vars', array(
            'runtimes' => 'html5,flash,silverlight,html4',
            'browse_button' => 'tb_plupload_browse_button', // adjusted by uploader
            'container' => 'tb_plupload_upload_ui', // adjusted by uploader
            'drop_element' => 'tb_plupload_upload_ui',  // adjusted by uploader
            'file_data_name' => 'async-upload', // adjusted by uploader
            'multiple_queues' => true,
            'max_file_size' => wp_max_upload_size() . 'b',
            'url' => admin_url('admin-ajax.php'),
            'flash_swf_url' => includes_url('js/plupload/plupload.flash.swf'),
            'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
            'filters' => array(array(
                    'title' => __('Allowed Files', 'themify'),
                    'extensions' => 'jpg,jpeg,gif,png,zip,txt'
                )),
            'multipart' => true,
            'urlstream_upload' => true,
            'multi_selection' => false, // added by uploader
            // additional post data to send to our ajax hook
            'multipart_params' => array(
                '_ajax_nonce' => '', // added by uploader
                'action' => 'themify_builder_plupload_action', // the ajax action name
                'imgid' => 0 // added by uploader
            )
        ));
    }

    /**
     * Get Builder Settings
     */
    public static function get_builder_settings() {
        static $themify_builder_data = null;
        if ($themify_builder_data === null) {
            $themify_builder_data = get_option('themify_builder_setting');
            if (is_array($themify_builder_data) && !empty($themify_builder_data)) {
                foreach ($themify_builder_data as $name => $value) {
                    $themify_builder_data[$name] = stripslashes($value);
                }
            } else {
                $themify_builder_data = array();
            }
        }
        return $themify_builder_data;
    }
    
    public static function get_animation(){
        static $animation = null;
        if ($animation === null) {
            $animation = array(
                array(
                    'type' => 'separator',
                    'meta' => array('html' => '<h4>' . esc_html__('Appearance Animation', 'themify') . '</h4>')
                ),
                array(
                    'id' => 'multi_Animation Effect',
                    'type' => 'multi',
                    'label' => __('Effect', 'themify'),
                    'fields' => array(
                        array(
                            'id' => 'animation_effect',
                            'type' => 'animation_select'
                        ),
                        array(
                            'id' => 'animation_effect_delay',
                            'type' => 'text',
                            'class' => 'xsmall',
                            'description' => __('Delay', 'themify')
                        ),
                        array(
                            'id' => 'animation_effect_repeat',
                            'type' => 'text',
                            'class' => 'xsmall',
                            'description' => __('Repeat', 'themify')
                        )
                    ),
                    'is_premium'=>self::is_premium()
                ),
               
            );
        }
        return $animation;
    }


    /**
     * Get ID
     */
    public static function get_ID() {
		if ( themify_is_woocommerce_active() && is_shop() ) {
			$page_id = version_compare( WOOCOMMERCE_VERSION, '3.0.0', '>=' )
				? wc_get_page_id( 'shop' )
				: woocommerce_get_page_id( 'shop' );
		  } else {
			$page_id = get_the_id();
		  }
		
		return $page_id;
    }

    /**
     * Get Grid menu list
     */
    public static function grid($handle = 'row') {
        $grid_lists = self::get_grid_settings();
        $gutters = self::get_grid_settings('gutter');
        $column_alignment = self::get_grid_settings('column_alignment');
        $column_direction = self::get_grid_settings('column_dir');
        $breakpoints = themify_get_breakpoints();
        $breakpoints = array_merge(array('desktop' => ''), $breakpoints);
        $is_premium = Themify_Builder_Model::is_premium();
        ?>
        <div class="tb_grid_menu" data-handle="<?php echo $handle; ?>">
                <ul class="grid_tabs">
                    <?php foreach ($breakpoints as $b => $v): ?>
                        <li<?php if(!$is_premium && $b!=='desktop'):?> class="tb_lite"<?php endif;?>>
                            <?php if(!$is_premium && $b!=='desktop'):?><span class="themify_lite_tooltip"></span><?php endif;?>
                            <a data-handle="<?php echo $handle; ?>" title="<?php echo $b==='tablet_landscape' ? __( 'Tablet Landscape', 'themify' ) : ucfirst($b);?>" href="#<?php echo $b ?>" class="tab_<?php echo $b ?>">
                                <i class="<?php if($b==='tablet_landscape'):?>ti-tablet <?php endif;?>ti-<?php echo $b ?>"></i>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="tb_grid_tab tb_grid_desktop">
                    <ul class="tb_grid_list clearfix">
                        <?php foreach ($grid_lists as &$li): ?>
                        <?php  $li['col'] = count($li['data']);?>
                            <li><a href="#" class="tb_column_select grid-layout-<?php echo esc_attr(implode('-', $li['data'])); ?>" data-type="desktop" data-handle="<?php echo $handle; ?>" data-col="<?php echo $li['col']; ?>" data-grid="<?php echo esc_attr(json_encode($li['data'])); ?>"><span class="tb_grids tb_<?php echo $li['img'] ?>"></span></a></li>
                        <?php endforeach; ?>
                    </ul>

                    <ul class="tb_column_alignment clearfix tb_actions">
                        <?php foreach ($column_alignment as $aligm): ?>
                            <li <?php if ($aligm['alignment'] === 'col_align_top') echo ' class="selected"' ?>><a href="#" class="tb_column_select column-alignment-<?php echo $aligm['alignment']; ?>" data-handle="<?php echo $handle; ?>" data-alignment="<?php echo $aligm['alignment'] ?>"><span class="tb_<?php echo $aligm['img'] ?>"></span></a></li>
                        <?php endforeach; ?>

                        <li><?php _e('Column Alignment', 'themify') ?></li>
                    </ul>
                    <ul class="tb_column_direction clearfix tb_actions">
                        <?php foreach ($column_direction as $dir): ?>
                            <li<?php if ($dir['dir'] === 'ltr') echo ' class="selected"' ?>>
                                <a href="#" class="tb_dir_select column-dir-<?php echo $dir['dir']; ?>" data-handle="<?php echo $handle; ?>" data-dir="<?php echo $dir['dir']; ?>"><span class="tb_<?php echo $dir['img'] ?>"></span></a>
                            </li>    					
                        <?php endforeach; ?>
                        <li><?php _e('Column Direction', 'themify') ?></li>
                    </ul>
                    <div class="tb_column_gutter clearfix">
                        <select class="gutter_select" data-handle="<?php echo $handle; ?>">
                            <?php foreach ($gutters as $gutter): ?>
                                <option value="<?php echo esc_attr($gutter['value']); ?>"><?php echo esc_html($gutter['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span><?php _e('Gutter Spacing', 'themify') ?></span>
                    </div>
                </div>
                <div class="tb_grid_tab tb_grid_reposnive tb_grid_tablet_landscape">
                    <ul class="tb_grid_list clearfix">
                        <li class="selected"><a href="#" class="tb_column_select tb1 tablet_landscape-auto" data-type="tablet_landscape" data-handle="<?php echo $handle; ?>" data-grid='["-auto"]'><span class="tb_grids tb_auto"></span></a></li>
                        <?php foreach ($grid_lists as $k => &$li): ?>
                            <?php 
                            if(!isset($li['exclude'])){
                                $li['data'] = array_values(array_unique($li['data']));
                            }
                            ?>
                            <li><a href="#" class="tb_column_select tb<?php echo $li['col']; ?> grid-layout-<?php echo esc_attr(implode('-', $li['data'])); ?>" data-type="tablet_landscape" data-handle="<?php echo $handle; ?>" data-col="<?php echo $li['col']; ?>" data-grid="<?php echo esc_attr(json_encode($li['data'])); ?>"><span class="tb_grids tb_<?php echo $li['img'] ?>"></span></a></li>
                        <?php endforeach; ?>
                    </ul>
                    <ul class="tb_column_direction clearfix tb_actions">
                        <?php foreach ($column_direction as $aligm): ?>
                            <li<?php if ($aligm['dir'] === 'ltr') echo ' class="selected"' ?>>
                                <a href="#" class="tb_dir_select column-dir-<?php echo $aligm['dir']; ?>" data-handle="<?php echo $handle; ?>" data-dir="<?php echo $aligm['dir']; ?>"><span class="tb_<?php echo $aligm['img'] ?>"></span></a>
                            </li>                                 
                        <?php endforeach; ?>
                        <li><?php _e('Column Direction', 'themify') ?></li>
                    </ul>
                </div>
                <div class="tb_grid_tab tb_grid_reposnive tb_grid_tablet">
                    <ul class="tb_grid_list clearfix">
                        <li class="selected"><a href="#" class="tb_column_select tb1 tablet-auto" data-type="tablet" data-handle="<?php echo $handle; ?>" data-grid='["-auto"]'><span class="tb_grids tb_auto"></span></a></li>
                        <?php foreach ($grid_lists as $k => $li): ?>
                            <li><a href="#" class="tb_column_select tb<?php echo $li['col']; ?> grid-layout-<?php echo esc_attr(implode('-', $li['data'])); ?>" data-type="tablet" data-handle="<?php echo $handle; ?>" data-col="<?php echo $li['col']; ?>" data-grid="<?php echo esc_attr(json_encode($li['data'])); ?>"><span class="tb_grids tb_<?php echo $li['img'] ?>"></span></a></li>
                        <?php endforeach; ?>
                    </ul>
                    <ul class="tb_column_direction clearfix tb_actions">
                        <?php foreach ($column_direction as $aligm): ?>
                            <li<?php if ($aligm['dir'] === 'ltr') echo ' class="selected"' ?>>
                                <a href="#" class="tb_dir_select column-dir-<?php echo $aligm['dir']; ?>" data-handle="<?php echo $handle; ?>" data-dir="<?php echo $aligm['dir']; ?>"><span class="tb_<?php echo $aligm['img'] ?>"></span></a>
                            </li>                                 
                        <?php endforeach; ?>
                        <li><?php _e('Column Direction', 'themify') ?></li>
                    </ul>
                </div>
                <div class="tb_grid_tab tb_grid_reposnive tb_grid_mobile">
                    <ul class="tb_grid_list clearfix">
                        <li class="selected"><a href="#" class="tb_column_select tb1 mobile-auto" data-type="mobile" data-handle="<?php echo $handle; ?>"   data-col="1"  data-grid='["-auto"]'><span class="tb_grids tb_auto"></span></a></li>
                        <?php foreach ($grid_lists as $li): ?>
                            <li><a href="#" class="tb_column_select tb<?php echo $li['col']; ?> grid-layout-<?php echo esc_attr(implode('-', $li['data'])); ?>" data-type="mobile" data-handle="<?php echo $handle; ?>"   data-col="<?php echo $li['col']; ?>"  data-grid="<?php echo esc_attr(json_encode($li['data'])); ?>"><span class="tb_grids tb_<?php echo $li['img'] ?>"></span></a></li>
                        <?php endforeach; ?>
                    </ul>
                    <ul class="tb_column_direction clearfix tb_actions">
                        <?php foreach ($column_direction as $aligm): ?>
                            <li<?php if ($aligm['dir'] === 'ltr') echo ' class="selected"' ?>>
                                <a href="#" class="tb_dir_select column-dir-<?php echo $aligm['dir']; ?>" data-handle="<?php echo $handle; ?>" data-dir="<?php echo $aligm['dir']; ?>"><span class="tb_<?php echo $aligm['img'] ?>"></span></a>
                            </li>
                        <?php endforeach; ?>
                        <li><?php _e('Column Direction', 'themify') ?></li>
                    </ul>
                </div>
        </div>
        <?php
    }
    
    public static function get_transient_time(){
        return apply_filters('themify_builder_ticks',MINUTE_IN_SECONDS /2);
    }

    public static function set_edit_transient($post_id,$value){
        
        return set_transient(self::$transient_name.$post_id, $value,self::get_transient_time());
    }
    
    public static function get_edit_transient($post_id){
        return get_transient(self::$transient_name.$post_id);
    }
    
    public static function remove_edit_transient($post_id){
        return delete_transient(self::$transient_name.$post_id);
    }
    
    public static function get_icon($icon){
        if(strpos($icon,'fa-')===0){
                $icon='fa '.$icon;
}
        return $icon;
    }

    /**
     * Check if gutenberg active
     * @return boolean
     */
     public static function is_gutenberg_active() {
	static $is = null;
	if ($is === null) {
	    global $wp_version;
	    $is = self::is_plugin_active('gutenberg/gutenberg.php') || version_compare( $wp_version, '5.0', '>=' );
	    if($is===true && version_compare( $wp_version, '5.0', '>=' )){
		$is = !self::is_plugin_active('disable-gutenberg/disable-gutenberg.php') && !self::is_plugin_active('classic-editor/classic-editor.php');
	    }
	}
	return $is;
    }
	    
    /**
    * Plugin Active checking
    * 
    * @access public
    * @param string $plugin 
    * @return bool
    */
   public static function is_plugin_active( $plugin ) {
           static $plugins = null;
           static $active_plugins = array();
           if($plugins===null){
               $plugins = is_multisite()?get_site_option( 'active_sitewide_plugins' ):false;
               $active_plugins = (array) apply_filters( 'active_plugins', get_option( 'active_plugins' ));
           }
           return ($plugins!==false && isset( $plugins[ $plugin ] )) || in_array( $plugin, (array) $active_plugins );
   }

    /**
     * Check if we are gutenberg editor
     * @return boolean
     */
    public static function is_gutenberg_editor() {
        static $is = null;
        if($is===null){
        /*
         * There have been reports of specialized loading scenarios where `get_current_screen`
         * does not exist. In these cases, it is safe to say we are not loading Gutenberg.
         */
        global $post;
        $is= !(!is_admin() || isset( $_GET['classic-editor'] ) || ! function_exists( 'get_current_screen' ) || get_current_screen()->base !== 'post' ||  !self::is_gutenberg_active() || !self::gutenberg_can_edit_post( $post ) );
        }
        return $is;
    }

    private static function gutenberg_can_edit_post( $post ) {
        $post     = get_post( $post );
        $can_edit = !(!$post || 'trash' === $post->post_status || ! self::gutenberg_can_edit_post_type( $post->post_type ) || ! current_user_can( 'edit_post', $post->ID ) || (absint( get_option( 'page_for_posts' ) ) === $post->ID && empty( $post->post_content ) ));

        /**
         * Filter to allow plugins to enable/disable Gutenberg for particular post.
         *
         * @since 3.5
         *
         * @param bool $can_edit Whether the post can be edited or not.
         * @param WP_Post $post The post being checked.
         */
        return apply_filters( 'gutenberg_can_edit_post', $can_edit, $post );

    }

    private static function gutenberg_can_edit_post_type( $post_type ) {
        $can_edit = !(!post_type_exists( $post_type ) || !post_type_supports( $post_type, 'editor' ));
        if($can_edit===true){
            $post_type_object = get_post_type_object( $post_type );
            $can_edit = !($post_type_object && ! $post_type_object->show_in_rest);
        }
        /**
         * Filter to allow plugins to enable/disable Gutenberg for particular post types.
         *
         * @since 1.5.2
         *
         * @param bool   $can_edit  Whether the post type can be edited or not.
         * @param string $post_type The post type being checked.
         */
        return apply_filters( 'gutenberg_can_edit_post_type', $can_edit, $post_type );
    }
    
    /**
     * Get frame type
     * @return string|boolean
     */
    public static function get_frame( $settings, $side ) {
            if ( isset( $settings[ "{$side}-frame_type" ] ) && $settings[ "{$side}-frame_type" ] === $side . '-presets' && ! empty( $settings[ "{$side}-frame_layout" ] ) ) {
                    return 'presets';
            } elseif ( isset( $settings[ "{$side}-frame_type" ] ) && $settings[ "{$side}-frame_type" ] === $side . '-custom' && ! empty( $settings[ "{$side}-frame_custom" ] ) ) {
                    return 'custom';
            } else {
                    return false;
            }
    }

    /**
     * Get module styling settings for used in localize script.
     * 
     * @access public
     * @param array $module 
     * @return array
     */
    public static function get_styling_options($module) {
        $return = array();
        $styling = $module->get_styling();
        if($styling) {
            if(isset($styling[0]['tabs'])){
                $tabs = $styling[0]['tabs'];
                foreach($tabs as $tab){
                    foreach($tab['fields'] as $option){
                        $return[]=$option['id'];
                    }
                }
            }else{
                foreach($styling as $option){
                    $return[]=$option['id'];
                }
            }
        }
        return $return;
    }
}
