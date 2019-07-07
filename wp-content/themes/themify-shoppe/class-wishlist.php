<?php
/**
 * Class wishlist for Woocomerce
 * @package themify
 * @since 1.0.0
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

if (!themify_is_woocommerce_active()) {
    return;
}

class Themify_Wishlist {

    private static $cookie_name = 'themify_wishlist';
    private static $wishilist_page_slug = 'wishlist';
    private static $key = 'setting-wishlist_page';
    private static $is_wishlist_page = false;

    private function __construct() {
        
    }

    public static function button($id = FALSE) {
        if (!self::is_enabled()) {
            return;
        }
        if (!$id) {
            $id = get_the_ID();
        }
        $wishlist = self::get();
        ?>
		<div class="wishlist-wrap">
			<a data-id="<?php echo $id ?>" onclick="javascript:void(0)" class="<?php echo!self::$is_wishlist_page ? 'wishlist-button' : 'wishlist-remove' ?><?php if (in_array($id, $wishlist)): ?> wishlisted<?php endif; ?>" href="<?php echo add_query_arg(array('action' => 'themify_add_wishlist', 'id' => $id), admin_url('admin-ajax.php')) ?>">
				<?php echo!self::$is_wishlist_page ? '<span class="tooltip">' . __('Wishlist', 'themify') . '</span>' : __('X', 'themify') ?>
			</a>
		</div> 
        <?php
    }

    public static function activation() {
        if (!self::get_wishlist_page(false)) {
            $args = array(
                'name' => self::$wishilist_page_slug,
                'post_type' => 'page',
                'post_status' => 'publish',
                'numberposts' => 1
            );
            $wishlist_page = get_posts($args);
            if (!empty($wishlist_page)) {
                $post_id = wp_insert_post(array(
                    'post_name' => self::$wishilist_page_slug,
                    'post_title' => __('Wishlist', 'themify'),
                    'post_status' => 'publish',
                    'post_type' => 'page'
                ));
            } else {
                $wishlist_page = current($wishlist_page);
                $post_id = $whislist_page->ID;
            }
            if ($post_id > 0) {
                $data = themify_get_data();
                $data[self::$key] = $post_id;
                themify_set_data($data);
            }
            wp_reset_postdata();
        }
    }

    public static function get_wishlist_page($url = true) {
        static $whislist_page = null;
        if (is_null($whislist_page)) {
            $whislist_page = themify_get(self::$key);
        }
        if ($whislist_page && $url) {
            $whislist_page = get_the_permalink($whislist_page);
        }
        return $whislist_page;
    }

    public static function wishlist_page() {
        $page_id = get_the_ID();
        if( defined( 'ICL_LANGUAGE_CODE' ) ) {
                $wpml_wishlist_page_id = apply_filters( 'wpml_object_id', self::get_wishlist_page(false), 'page' );
                if($wpml_wishlist_page_id === $page_id){
                    $page_id = self::get_wishlist_page(false);
                }
        }
        self::$is_wishlist_page = is_page() && $page_id == self::get_wishlist_page(false);
        if (self::$is_wishlist_page) {
			header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
			header("Pragma: no-cache"); // HTTP 1.0.
			header("Expires: 0"); // Proxies.
			add_action('wp_head',array(__CLASS__,'head'));
        }
    }
	
    public static function head(){
		remove_action('the_content', array(__CLASS__, 'wishlist_result'), 20, 1);
		add_filter('the_content', array(__CLASS__, 'wishlist_result'), 20, 1);
		add_filter('body_class', array(__CLASS__, 'body_class'), 10, 1);
    }

    public static function body_class($classes) {
        $classes[] = 'wishlist-page';
        $classes[] = 'woocommerce';
        $classes[] = ' woocommerce-page';
        return $classes;
    }

    public static function wishlist_result($content) {
        $items = self::get();
        if (!empty($items)) {
            $query_args = array(
                'post_type' => 'product',
                'post_status' => 'publish',
                'post__in' => $items,
                'nopaging' => true,
                'no_found_rows'=>true
            );
            $check_ids = array();
            $wishlist_posts = new WP_Query(apply_filters('themify_wislist_query', $query_args));
            if ($wishlist_posts->have_posts()) {
                WC_Frontend_Scripts::load_scripts();
                ob_start();
                woocommerce_product_loop_start();
                while ($wishlist_posts->have_posts()) {
                    $wishlist_posts->the_post();
                    $check_ids[] = get_the_ID();
                    wc_get_template_part('content', 'product');
                }
                woocommerce_product_loop_end();
                $result = ob_get_contents();
                ob_end_clean();
            } else {
                $result = '<p class="themify_wishlist_no_items">' . __("There is no wishlist item.", 'themify') . '</p>';
            }
            wp_reset_postdata();
        } else {
            $result = '<p class="themify_wishlist_no_items">' . __("There is no wishlist item.", 'themify') . '</p>';
        }
        $result = '<div id="wishlist-wrapper">' . $result . '</div>';
        return $content . $result;
    }

    private static function get_expiration() {
        static $time = false;
        if (!$time) {
            $time = time() + apply_filters('themify_wishlist_cookie_expiration_time', 60 * 60 * 24 * 30); // 30 days
        }
        return $time;
    }

    public static function get_total() {
        $wishlist = self::get(true);
        return count(array_unique($wishlist));
    }

    public static function removeItem($id = false) {
        if (!$id) {
            $id = get_the_ID();
        }
        $wishlist = self::get();
        $index = array_search($id, $wishlist);
        if ($index !== false) {
            unset($wishlist[$index]);
            self::setCookies($wishlist);
        }
    }

    public static function destroy() {
        wc_setcookie(self::$cookie_name, array(), time() - 3600, false);
    }

    public static function setValue($id) {
        $wishlist = self::get();
        $wishlist[] = $id;
        self::setCookies($wishlist);
    }

    public static function setCookies($wishlist) {

        $wishlist = json_encode(stripslashes_deep(array_unique($wishlist)));
        $time = self::get_expiration();
        $_COOKIE[self::$cookie_name] = $wishlist;
        wc_setcookie(self::$cookie_name, $wishlist, $time, false);
    }

    public static function get($recalculate = false) {
        static $wishlist = null;
        if (is_null($wishlist) || $recalculate) {
            $wishlist = !empty($_COOKIE[self::$cookie_name]) ? json_decode(stripslashes($_COOKIE[self::$cookie_name]), true) : array();
        }
        return $wishlist;
    }

    public static function ajax_add() {
        if (!empty($_GET['id'])) {
            $id = intval($_GET['id']);
            $wishlist = self::get();
            $action = !empty($_GET['type']) && $_GET['type'] === 'remove' ? 'remove' : 'add';
            $is_add = in_array($id, $wishlist);
            $event = false;
            if ($action === 'add' && !$is_add) {
                $post = get_post($id);
                if ($post->post_type === 'product') {
                    self::setValue($id);
                    $event = true;
                }
            } elseif ($action === 'remove' && $is_add) {
                $post = get_post($id);
                if ($post->post_type === 'product') {
                    self::removeItem($id);
                    $event = true;
                }
            }
            if ($event) {
                $total = self::get_total();
                die("$total");
            }
        }
        wp_die();
    }

    public static function enqueue_settings($settings) {

        $settings['wishlist'] = array(
            'no_items' => __("There is no wishlist item.", 'themify'),
            'cookie' => self::$cookie_name,
            'expiration' => self::get_expiration(),
            'cookie_path' => COOKIEPATH ? COOKIEPATH : '/',
            'domain' => COOKIE_DOMAIN ? COOKIE_DOMAIN : ''
        );

        return $settings;
    }

    public static function config_setup($themify_theme_config) {
        $config = array();
        foreach ($themify_theme_config['panel']['settings']['tab']['shop_settings']['custom-module'] as $index => $val) {
            if ($index === 2) {
                $config[] = array(
                    'title' => __('Wishlist Settings', 'themify'),
                    'function' => array(__CLASS__, 'config_view')
                );
            }
            $config[] = $val;
        }
        $themify_theme_config['panel']['settings']['tab']['shop_settings']['custom-module'] = $config;

        return $themify_theme_config;
    }

    public static function config_view() {
        $key = 'setting-wishlist_disable';

        $html = '<p><span class="label">' . __('Wishlist', 'themify') . '</span>
                <label for="' . $key . '"><input type="checkbox" id="' . $key . '" name="' . $key . '" ' . checked(themify_get($key), 'on', false) . ' /> ' . __('Disable Wishlist', 'themify') . '</label></p>';


        $page_wishlist = themify_get(self::$key);
        $front = get_option('page_on_front');

        $args = array(
            'sort_order' => 'asc',
            'sort_column' => 'post_title',
            'post_type' => 'page',
            'post_status' => 'publish',
            'nopaging' => 1
        );

        $pages = new WP_Query($args);

        $html.= '<p data-show-if-element="[name=setting-wishlist_disable]" data-show-if-value=' . '["false"]' . '><span class="label">' . __('Wishlist Page', 'themify') . ' </span>';
        $html.='<select name="' . self::$key . '">';

        while ($pages->have_posts()) {
            $pages->the_post();
            $id = get_the_ID();
            if ($id != $front) {
                $selected = $page_wishlist == $id ? 'selected="selected"' : '';
                $html .= '<option ' . $selected . ' value="' . $id . '">';
                $html .= get_the_title();
                $html .= '</option>';
            }
        }
        $html .= '</select></p>';
        return $html;
    }

    public static function is_enabled() {
        static $is_enabled = null;
        if (is_null($is_enabled)) {
            $is_enabled = !themify_get('setting-wishlist_disable');
        }
        return $is_enabled;
    }

}

if (Themify_Wishlist::is_enabled()) {
    //Enqueue wishlist settigs
    add_filter('themify_shop_js_vars', array('Themify_Wishlist', 'enqueue_settings'), 10, 1);

    //Add to cart
    add_action('wp_ajax_themify_add_wishlist', array('Themify_Wishlist', 'ajax_add'));
    add_action('wp_ajax_nopriv_themify_add_wishlist', array('Themify_Wishlist', 'ajax_add'));

    //Wishlist Page
    add_action('template_redirect', array('Themify_Wishlist', 'wishlist_page'), 14);

    add_action('after_switch_theme', array('Themify_Wishlist', 'activation'));
}

//Settings Page
add_filter('themify_theme_config_setup', array('Themify_Wishlist', 'config_setup'), 14, 1);
