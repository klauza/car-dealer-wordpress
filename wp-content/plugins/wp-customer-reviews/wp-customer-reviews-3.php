<?php
/*
 * Plugin Name: WP Customer Reviews
 * Plugin URI: http://www.gowebsolutions.com/wp-customer-reviews/
 * Description: Allows your visitors to leave business / product reviews. Testimonials are in Microdata / Microformat and may display star ratings in search results.
 * Version: 3.4.1
 * Author: Go Web Solutions
 * Author URI: http://www.gowebsolutions.com/
 * Text Domain: wp-customer-reviews
 * License: MIT
 *
 * Copyright (c) 2017 Go Web Solutions
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * 
 */

class WPCustomerReviews3 {
	var $debug = false;
	var $prefix = 'wpcr3';
	var $dashname = 'wp-customer-reviews-3';
	var $url = 'http://www.gowebsolutions.com/wp-customer-reviews/';
	var $support_link = 'http://wordpress.org/tags/wp-customer-reviews?forum_id=10';
	var $prolink = 'http://www.gowebsolutions.com/wp-customer-reviews/';
	var $plugin_info = false;
	var $plugin_version = '0.0.0';
	var $adminClass = false;
	var $proClass = false;
	var $pro = false;
	var $force_active_page = false;
	var $options = array();
	var $options_name = 'wpcr3_options';
	var $options_url_slug = 'wpcr3_options';
	var $p = '';
	var $all_templates = array(
		'frontend_review_holder' => 'html',
		'frontend_review_form' => 'html',
		'frontend_review_form_text_field' => 'html',
		'frontend_review_form_rating_field' => 'html',
		'frontend_review_form_review_field' => 'html',
		'frontend_review_item' => 'html',
		'frontend_review_item_aggregate' => 'html',
		'frontend_review_item_reviews' => 'html',
		'frontend_review_pagination' => 'html',
		'frontend_review_rating_stars' => 'html',
		'wp-customer-reviews-css' => 'css'
	);
	
	function __construct() {
	}
	
	function start() {
		$this->debug = (isset($_SERVER) && isset($_SERVER['SERVER_NAME']) && stripos($_SERVER['SERVER_NAME'], 'wptest.bomp') !== FALSE); 
		
		if ($this->debug === true || $this->remote_debug()) {
			restore_error_handler();
			error_reporting(E_ALL);
			ini_set('error_reporting', E_ALL);
			ini_set('html_errors', TRUE);
			ini_set('display_errors', TRUE);
		}
		
		register_activation_hook(__FILE__, array(&$this, 'activate'));
		register_deactivation_hook(__FILE__, array(&$this, 'deactivate'));
	
		// we use priority 11 to allow v2 and v3 to coexist
		add_action('init', array(&$this, 'init'), 11);
	}
	
	function remote_debug() {
		return isset($_GET['wpcr3_debug']);
	}
	
	function plugin_get_info() {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php');
		return get_plugin_data( __FILE__ );
	}
	
	function include_goatee() {
		if (!defined('HAS_'.$this->prefix.'_GOATEE')) {
			define('HAS_'.$this->prefix.'_GOATEE', 1);
			include_once($this->getplugindir() . 'include/goatee-php/wpcr-goatee.php'); // include Goatee templating functions
		}
	}
	
	function include_pro() {
		if (!defined('HAS_'.$this->prefix.'_PRO')) {
			$pro_file = $this->getplugindir() . '../wp-customer-reviews-pro-activation/wp-customer-reviews-3-pro-inc.php';
			$pro_exists = file_exists($pro_file);
			if ($pro_exists === false) { return; }

			include_once( ABSPATH . 'wp-admin/includes/plugin.php');
			if (is_plugin_active("wp-customer-reviews-pro-activation/wp-customer-reviews-3-pro.php") === false) { return; }
			
			define('HAS_'.$this->prefix.'_PRO', 1);
			include_once($pro_file); // include pro functions
			$this->proClass = new WPCustomerReviews3Pro();
			$this->proClass->start_pro($this);
		}
	}
	
	// forward to pro class
	function pro_function() {
		if (!$this->pro) { die("Pro Function called without pro loaded."); }
		$args = func_get_args();
		$function = array_shift($args);		
		return call_user_func_array(array($this->proClass,$function), $args);
	}
	
	function include_admin() {
		if (!defined('HAS_'.$this->prefix.'_ADMIN')) {
			define('HAS_'.$this->prefix.'_ADMIN', 1);
			include_once($this->getplugindir() . 'include/admin/wp-customer-reviews-3-admin.php'); // include admin functions
			$this->adminClass = new WPCustomerReviewsAdmin3();
			$this->adminClass->start_admin($this);
		}
    }
	
	// forward to admin class
	function admin_function() {
		$this->include_admin();
		$args = func_get_args();
		$function = array_shift($args);
		return call_user_func_array(array($this->adminClass,$function), $args);
	}

	function admin_menu() {
		$this->admin_function('real_admin_menu');
    }
	
    function admin_init() {
		$this->admin_function('real_admin_init');
    }
	
	// if $this->p->$key does not exist, set it to empty string
	function param($keys, &$object = "") {
		if (!is_array($keys)) { $keys = array($keys); }
		if ($object === "") { $object = $this->p; }
		foreach ($keys as $key) {
			if (is_object($object)) {
				if (!isset($object->$key)) {
					$object->$key = '';
				}
			} else if (is_array($object)) {
				if (!array_key_exists($key, $object)) {
					$object[$key] = '';
				}
			}
		}
	}

    function get_options() {
        $this->options = get_option($this->options_name);
    }

    function make_p_obj() {
        $this->p = new stdClass();

        foreach ($_GET as $c => $val) {
            if (is_array($val)) {
                $this->p->$c = $val;
            } else {
                $this->p->$c = trim(stripslashes($val));
            }
        }

        foreach ($_POST as $c => $val) {
            if (is_array($val)) {
                $this->p->$c = $val;
            } else {
                $this->p->$c = trim(stripslashes($val));
            }
        }
    }
    
    function is_active_page() {
        global $post;
		
		// if using WPCR_INSERT, we always force the page active so reviews will output
        if ($this->force_active_page === 'shortcode_insert') {
			return $this->force_active_page;
		}
        
		if ($this->remote_debug()) {
			$debug = "\n <div style='display:none;'>";
			$debug .= "\n wpcr3_info is_active_page() post={$post->ID} is_active_page=";
			$debug .= "".print_r(is_singular(), true);
			$debug .= ",".print_r(is_single(), true);
			$debug .= ",".print_r(is_page(), true);
			$debug .= "\n </div>";
			print $debug;
		}
		
		// not on a single post/page, do not output
        if (!is_singular()) { return 0; }
        
        $enabled_post = get_post_meta($post->ID, 'wpcr3_enable', true);
        if ($enabled_post == '1') { return 'enabled'; }
        
        return 0;
    }
	
	// run after each shortcode has finished
	function reset_active_page() {
		$this->force_active_page = false;
	}
    
    function rand_string($length) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $str = '';

        $size = strlen($chars);
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[rand(0, $size - 1)];
        }

        return $str;
    }

	function get_aggregate_reviews($postid) {
		global $wpdb;
		
		/*
		Removed below INNER JOIN for 3.2.2 to allow in-text shortcode (and do_shortcode) to display correct review count even when WPCR-enabled checkbox is not checked on the page. (loc 1/2)
		... FROM {$wpdb->prefix}posts p1 ...
		>> INNER JOIN {$wpdb->prefix}postmeta pm1 ON pm1.meta_key = 'wpcr3_enable' AND pm1.meta_value = '1' AND pm1.post_id = p1.id
		... pm2 ON pm2.meta_key ...
		*/
		
		$query = $wpdb->prepare("
			SELECT 
			COUNT(*) AS aggregate_count, AVG(tmp2.rating) AS aggregate_rating
			FROM (
				SELECT pm4.meta_value AS rating
				FROM (
					SELECT DISTINCT pm2.post_id
					FROM {$wpdb->prefix}posts p1
					INNER JOIN {$wpdb->prefix}postmeta pm2 ON pm2.meta_key = 'wpcr3_review_post' AND pm2.meta_value = p1.id
					WHERE p1.id = %d
				) tmp1
				INNER JOIN {$wpdb->prefix}posts p2 ON p2.id = tmp1.post_id AND p2.post_status = 'publish' AND p2.post_type = 'wpcr3_review'
				INNER JOIN {$wpdb->prefix}postmeta pm4 ON pm4.post_id = p2.id AND pm4.meta_key = 'wpcr3_review_rating' AND pm4.meta_value IS NOT NULL AND pm4.meta_value != '0'
				GROUP BY p2.id
			) tmp2
		", intval($postid));
		
		$results = $wpdb->get_results($query);
		
		$rtn = new stdClass();
		
		if (count($results)) {
			$rtn->aggregate_count = $results[0]->aggregate_count;
			$rtn->aggregate_rating = $results[0]->aggregate_rating;
			if ($rtn->aggregate_count == 0) { $rtn->aggregate_rating = 0; }
			$rtn->stars = $this->get_rating_template($rtn->aggregate_rating, false);
		}
		
		return $rtn;
	}
	
	// filters show reviews only display for posts that have wpcr enabled on them AND are published
	function reviews_attached_to_enabled_published_posts($where) {
		global $wpdb;

		/*
		Removed below INNER JOIN for 3.2.2 to allow in-text shortcode (and do_shortcode) to display correct review count even when WPCR-enabled checkbox is not checked on the page. (loc 2/2)
		... pm2 ON pm2.meta_key ...
		> INNER JOIN {$wpdb->prefix}postmeta pm3 ON pm3.meta_key = 'wpcr3_enable' AND pm3.meta_value = '1'
		... p3 ON p3.id = pm2.meta_value ...
		*/

		$where .= "
			AND {$wpdb->prefix}posts.id IN (
				SELECT DISTINCT p2.id FROM {$wpdb->prefix}posts p2
				INNER JOIN {$wpdb->prefix}postmeta pm2 ON pm2.meta_key = 'wpcr3_review_post' AND pm2.post_id = p2.id
				INNER JOIN {$wpdb->prefix}posts p3 ON p3.id = pm2.meta_value AND p3.post_status = 'publish'
				WHERE p2.post_type = 'wpcr3_review'
			)
		";
		return $where;
	}

    function get_reviews($postid, $thispage, $opts) {
		$queryOpts = array(
			'orderby' => 'date',
			'order' => 'DESC',
			'showposts' => min($opts->perpage, $opts->num),
			'post_type' => $this->prefix.'_review',
			'post_status' => 'publish',
			'paged' => $thispage
		);
		
		if ($postid != -1) {
			// if $postid is not -1 (all reviews from all posts), need to filter by meta value for post id
			$meta_query = array('relation' => 'AND');
			$meta_query[] = array(
				'key' => "{$this->prefix}_review_post",
				'value' => $postid,
				'compare' => '='
			);
			$queryOpts['meta_query'] = $meta_query;
		}
		
		add_filter('posts_where', array(&$this, 'reviews_attached_to_enabled_published_posts'));
		$reviews = new WP_Query($queryOpts);
		remove_filter('posts_where', array(&$this, 'reviews_attached_to_enabled_published_posts'));
		
		$rtn = new stdClass();
		$rtn->reviews = array();
		$rtn->found_posts = $reviews->found_posts;
		
		foreach ($reviews->posts as $post) {
			$review = $this->get_post_custom_single($post->ID);
			
			$review['id'] = $post->ID;
			$review['stars'] = $this->get_rating_template($review[$this->prefix.'_review_rating'], false);
			
			$params = array($this->prefix.'_review_name', $this->prefix.'_review_title', $this->prefix.'_review_website', $this->prefix.'_review_admin_response');
			$this->param($params, $review);
			
			if ($this->options['standard_fields']['fname']['show'] == 0 || $review[$this->prefix.'_review_name'] == '') {
				$review[$this->prefix.'_review_name'] = 'Anonymous';
			}
			
			if ($this->options['standard_fields']['ftitle']['show'] == 0) {
				$review[$this->prefix.'_review_title'] = '';
			}
			
			if ($this->options['standard_fields']['fwebsite']['show'] == 0) {
				$review[$this->prefix.'_review_website'] = '';
			}
			
			$review[$this->prefix.'_custom_fields'] = array();
			
			if ($opts->hidecustom == 0) {
				foreach($this->options['custom_fields'] as $name => $fieldArr) {
					$params = array($this->prefix.'_'.$name);
					$this->param($params, $review);
					$value = trim($review[$this->prefix.'_'.$name]);
					if ($fieldArr['show'] == 1 && strlen($value)) {
						$review[$this->prefix.'_custom_fields'][] = array(
							'label' => $fieldArr['label'],
							'value' => $value
						);
					}
				}
			}
			
			$review[$this->prefix.'_review_admin_response'] = nl2br($review[$this->prefix.'_review_admin_response']);
			
			if ($opts->hideresponse == 1) {
				unset($review[$this->prefix.'_review_admin_response']);
			}
			
			$rtn->reviews[] = $review;
		}
		
        return $rtn;
    }

    function iso8601($time=false) {
        if ($time === false) { $time = time(); }
        $date = date('Y-m-d\TH:i:sO', $time);
        return (substr($date, 0, strlen($date) - 2) . ':' . substr($date, -2));
    }

    function pagination($opts, $thispage, $total_results) {
        $rtn = false;
		$thispage = intval($thispage);
        if ($thispage == 0) { $thispage = 1; }
        $pages = intval(ceil($total_results / $opts->perpage));
		
        if ($pages > 1) {
			$rtn = array();
			$rtn['pageOpts'] = htmlspecialchars(json_encode($opts), ENT_QUOTES);
			$rtn['onPage'] = $thispage;
			$rtn['numPages'] = $pages;
			$rtn['pages'] = array();
			$rtn['prevPage'] = max(1, $thispage - 1);
			$rtn['nextPage'] = min($pages, $thispage + 1);
			
			$range = 2;
			$showitems = ($range * 2) + 1;

			$rtn['hasPrev'] = false;
            if ($thispage !== 1) {
                $rtn['hasPrev'] = true;
            }

            for ($i = 1; $i <= $pages; $i++) {
                if ($i === $thispage) {
					$tmp = array();
					$tmp['pageNum'] = $i;
					$tmp['current'] = true;
					$rtn['pages'][] = $tmp;
                } else if (!($i >= ($thispage + $range + 1) || $i <= ($thispage - $range - 1)) || $pages <= $showitems) {
                    $tmp = array();
					$tmp['pageNum'] = $i;
					$tmp['current'] = false;
					$rtn['pages'][] = $tmp;
                }
            }
			
			$rtn['hasNext']	= false;
            if ($thispage !== $pages) {
                $rtn['hasNext'] = true;
            }
        }
		
		return $rtn;
    }
	
	function get_meta_or_default($metaArr, $key, $default) {
		return (isset($metaArr[$key])) ? $metaArr[$key] : $default;
	}
	
	function get_post_custom_single($postid) {
		$post = get_post($postid);
		$meta = get_post_custom($postid);
		
		$out = array();
		foreach ($meta as $key => $valArr) {
			if ($key === "") { continue; }
			$out[$key] = $valArr[0];
		}
		
		if ($post->post_type !== $this->prefix.'_review') {
			$format = $this->get_meta_or_default($out, $this->prefix.'_format', "Blank Format");
			if ($format === 'business') {
				$out['is_business'] = true;
			} else if ($format === 'product') {
				$out['is_product'] = true;
			}
		}
		
		$out['content'] = nl2br($post->post_content);
		
		$post_date = explode(" ",$post->post_date);
		$out['post_date'] = $post_date[0];
		$out['post_date'] = date("M j, Y", strtotime($out['post_date']));
		
		return $out;
	}
	
	function inject_parent_info($reviews, $postid, $parentData, $opts) {
		$format = $this->get_meta_or_default($parentData, $this->prefix.'_format', "Blank Format");
		$business_name = $parentData[$this->prefix.'_business_name'];
		$product_name = $parentData[$this->prefix.'_product_name'];
		$postLink = get_permalink($postid);

		foreach($reviews as &$review) {
			if ($format === 'business') {
				$review['is_business'] = true;
				$review['item_name'] = $business_name;
			} else if ($format === 'product') {
				$review['is_product'] = true;
				$review['item_name'] = $product_name;
			}
			$review['postLink'] = $postLink;
			$review['on_same_page'] = ($opts->on_postid == $postid);

			if ($opts->snippet > 0) {
				$review['content'] = $this->trim_text_to_word($review, $opts);
			}
		}
		
		return $reviews;
	}

	function default_parentData($parentData) {
		// default some fields for those too lazy to use the plugin properly
		$blog_name = get_bloginfo('name');
		$blog_url = get_bloginfo('url');
		$business_name = $this->get_meta_or_default($parentData, $this->prefix.'_business_name', $blog_name);
		$product_name = $this->get_meta_or_default($parentData, $this->prefix.'_product_name', $blog_name);
		$parentData[$this->prefix.'_business_name'] = $business_name;
		$parentData[$this->prefix.'_business_url'] = $blog_url;
		$parentData[$this->prefix.'_product_name'] = $product_name;
		
		// todo: replace with provided image in future
		$parentData[$this->prefix.'_business_image'] = $this->getpluginurl_abs() . 'css/1x1.png';
		$parentData[$this->prefix.'_product_image'] = $this->getpluginurl_abs() . 'css/1x1.png';

		return $parentData;
	}
		
	function output_reviews_show($opts) {
		global $post;
		
		// required: showform, num, postid, classes, showsupport
		// optional: hidecustom, hideresponse, snippet, morelink, thispage, ajax, hidereviews, on_postid (internal), wrapper (internal)
		
		$params = array('morelink', 'classes', 'on_postid', 'wrapper');
		$this->param($params, $opts);
		
		$intArr = array('postid', 'num', 'paginate', 'perpage', 'hidecustom', 'snippet', 'showform', 'hidereviews', 'hideresponse', 'ajax', 'thispage', 'on_postid', 'wrapper');
		foreach ($intArr as $key) {
			$opts->$key = isset($opts->$key) ? $this->strip_trim_intval($opts->$key) : 0;
		}
		
		if ($opts->num < 1) { $opts->num = 9999; }
		if ($opts->thispage < 1) { $opts->thispage = 1; }
		if ($opts->perpage < 1) { $opts->perpage = intval($this->options['reviews_per_page']); }
		
		$postid = $opts->postid;
        $thispage = $opts->thispage;
		
		if ($opts->num > 0 && $opts->num < $opts->perpage) {
			$opts->perpage = $opts->num;
		}
		
		if ($opts->ajax == 1) {
			$opts->showform = 0;
		}
		
		$this->include_goatee();
		
		$tmp = $this->get_reviews($postid, $thispage, $opts);
		$found_posts = $tmp->found_posts;
		$reviews = $tmp->reviews;
		$page_count = count($reviews);
		
		$ajaxurl_arr = json_encode(explode(".",str_replace('/','|',$this->getAjaxURL())));
		
		$on_postid = $opts->on_postid;
		if (isset($post)) {
			$opts->on_postid = $post->ID;
			$on_postid = $opts->on_postid;
		}
		
		$main_data = array(
			'classes' => $opts->classes,
			'review_form' => '',
			'reviews' => '',
			'power' => '',
			'hidereviews' => ($opts->hidereviews == 1),
			'ajaxurl' => $ajaxurl_arr,
			'postid' => $postid,
			'on_postid' => $on_postid
		);
		
		$got_post_meta = array();
		
		$data['found_posts'] = $found_posts;
		
		$pagination = "";
		if ($opts->hidereviews == 0) {
			if ($found_posts > $opts->perpage && $opts->paginate === 1) {
				$pagination = $this->pagination($opts, $thispage, $found_posts);
				$pagination = wpcr_Goatee::fill($this->options['templates']['frontend_review_pagination'], $pagination);
			}
		}
		
		if ($opts->showsupport == 1 && $this->options['support_us'] == 1) {
			$main_data['power'] = 'Powered by <strong><a target="_blank" href="'.$this->url.'">'.$this->plugin_info['Name'].'</a></strong>';
		}
		
		$main_data['pagination'] = $pagination;
		
		if ($postid > 0) {
			// if viewing reviews of a single post id
			$got_post_meta[$postid] = $this->get_post_custom_single($postid);
			$data = &$got_post_meta[$postid];
			$data = $this->default_parentData($data);

			$params = array($this->prefix.'_hideform');
			$this->param($params, $data);
			
			$showform = ($opts->showform == 1 && $data[$this->prefix.'_hideform'] != 1);
			$main_data['review_form'] = $this->show_reviews_form($postid, $found_posts, $showform);
			
			$reviews = $this->inject_parent_info($reviews, $postid, $data, $opts);
			
			$data['postLink'] = get_permalink($postid);
			$data['on_same_page'] = ($opts->on_postid == $postid);
			
			$data['reviews'] = array(
				'template' => $this->options['templates']['frontend_review_item_reviews'],
				'data' => array(
					'reviews' => $reviews
				)
			);
				
			$data['aggregate'] = array(
				'template' => $this->options['templates']['frontend_review_item_aggregate'],
				'data' => array(
					'aggregate' => $this->get_aggregate_reviews($postid)
				)
			);
			
			$main_data['reviews'] .= wpcr_Goatee::fill($this->options['templates']['frontend_review_item'], $data);
		} else {
			// we are in a shortcode/widget that is showing reviews for multiple posts
			// so we need to fill $got_post_meta with the post meta related to each $review			
			foreach ($reviews as $review) {
				$review_of_postid = intval($review[$this->prefix.'_review_post']);
				
				if (!array_key_exists($review_of_postid, $got_post_meta)) {
					$got_post_meta[$review_of_postid] = $this->get_post_custom_single($review_of_postid);
				}
			
				$data = &$got_post_meta[$review_of_postid];
				$data = $this->default_parentData($data);
				
				$tmpReviews = array($review);
				$tmpReview = $this->inject_parent_info($tmpReviews, $review_of_postid, $data, $opts);
				$review = $tmpReview[0];
			
				// 1. no aggregate shown for multiple businesses/products as it would confuse crawlers which one to show in SERPs
				// 2. no form shown for showing "all" reviews because we don't know what post to bind the form to
				
				if ($opts->hidereviews == 0) {
					$data['reviews'] = array(
						'template' => $this->options['templates']['frontend_review_item_reviews'],
						'data' => array(
							'reviews' => array($review)
						)
					);
				}
				
				$main_data['reviews'] .= wpcr_Goatee::fill($this->options['templates']['frontend_review_item'], $data);
			}
		}
		
		// Useful info: http://wordpress.stackexchange.com/a/39928 ( faster way to get meta values for many posts )
		// Useful info: http://stackoverflow.com/a/18422969 ( outputting multiple ratings for one item )
		
		$reviews_content = wpcr_Goatee::fill($this->options['templates']['frontend_review_holder'], $main_data);
		$reviews_content = preg_replace('/\n\r|\r\n|\n|\r|\t/', '', $reviews_content); // minify to prevent automatic line breaks, not removing double spaces
		
		if ($opts->wrapper === 1) {
			$data_attr = $this->get_data_attr_wrapper($postid);
			$reviews_content = "<div {$data_attr}>".$reviews_content."</div>";
		}
		
		return $reviews_content;
    }
    
    // stripts html then trims text, but does not break up a word
    function trim_text_to_word($review, $opts) {
		$text = $review['content'];
		$text = str_replace("<br", " <br", $text);
		$text = trim(strip_tags($text));
		$len = $opts->snippet;
		
        if (strlen($text) > $len) {		
			preg_match('/^.{0,'.$len.'}(?:.*?)\b/siu', $text, $matches);
			$text = $matches[0] . "... ";
			if (strlen(trim($opts->morelink)) > 0) {
				$postLink = $review['postLink']."#wpcr3_id_".$review['id'];
				$text .= "<a href='{$postLink}'>$opts->morelink</a>";
			}
        }
        return $text;
    }
	
	function print_filters_for($hook = '') {
		global $wp_filter;
		if( empty( $hook ) || !isset( $wp_filter[$hook] ) ) { return; }
		print '<pre>';
		print_r( $wp_filter[$hook] );
		print '</pre>';
	}
	
	function get_data_attr_wrapper($postid) {
		return "data-wpcr3-content=\"{$postid}\"";
	}

    function do_the_content($original_content, $fix = "wp") {
        global $post;
		
		if ($this->remote_debug()) {
			$debug = "\n<div style='display:none;'>wpcr3_info top_start</div>\n";
			$debug .= "\n<div style='display:none;'>wpcr3_info fix={$fix}</div>\n";
			$debug .= "\n<div style='display:none;'>wpcr3_info original_content={$original_content}</div>\n";
			$debug .= "\n<div style='display:none;'>wpcr3_info top_end</div>\n";
			print $debug;
		}
		
		if (!isset($post) || !isset($post->ID) || intval($post->ID) == 0) {
			// we need a post object to do anything useful
			
			if ($this->remote_debug()) {
				$debug = "\n<div style='display:none;'>wpcr3_info no post id fix={$fix}</div>\n";
				$debug .= "\n<div style='display:none;'>wpcr3_info above returned from filter 1</div>\n";
				print $debug;
			}
			
			return $original_content;
		}
		
		$postid = $post->ID;
		$data_attr = $this->get_data_attr_wrapper($postid);
		$already_ran = (strpos($original_content, $data_attr) !== FALSE) ? 1 : 0;
		
		if ($this->remote_debug()) {
			$debug = "\n <div style='display:none;'>";
			$debug .= "\n wpcr3_info postid={$postid} fix={$fix} already_ran={$already_ran} strlen=".strlen($original_content);
			$debug .= "\n </div>";
			print $debug;
		}
		
		// return original content if reviews should not display for this post
		$is_active_page = $this->is_active_page();
		
		if ($this->remote_debug()) {
			$debug = "\n <div style='display:none;'>wpcr3_info post={$post->ID} fix={$fix} already_ran={$already_ran} is_active_page={$is_active_page}</div> \n";
			$debug .= "\n <div style='display:none;'>wpcr3_info above returned from filter 2</div> \n";
			print $debug;
		}
		
		if ($already_ran === 1 || $is_active_page === 0) {
			return $original_content;
		}
		
		if ($this->remote_debug()) {
			$debug = "\n <div style='display:none;'>wpcr3_info on_active_page post={$post->ID} fix={$fix} is_active_page={$is_active_page}</div> \n";
			$debug .= "\n <div style='display:none;'>wpcr3_info above returned from filter 3</div> \n";
			print $debug;
		}
		
		$this->reset_active_page();
		
		$hideform = get_post_meta($post->ID, $this->prefix.'_hideform', true);
		$showform = ($hideform !== "1");
		
		$opts = new stdClass();
		$opts->showform = $showform;
		$opts->showsupport = $this->options['support_us'];
		$opts->postid = $postid;
		$opts->perpage = $this->options['reviews_per_page'];
		$opts->paginate = 1;
		$opts->classes = $this->prefix."_in_content";
		$opts->wrapper = 1;
		
		$reviews_content = $this->output_reviews_show($opts);
		$original_content .= $reviews_content;
		return $original_content;
    }

    function get_rating_template($rating, $enable_hover) {
		$data = array(
			'hoverable' => $enable_hover,
			'stars' => $rating,
			'rating_width' => 20 * $rating // 20% for each star if having 5 stars
		);
        return wpcr_Goatee::fill($this->options['templates']['frontend_review_rating_stars'], $data);
    }
	
	function get_form_field($name, $fieldArr) {
		$posted_name = $this->prefix.'_'.$name;
		
		$params = array($posted_name);
		$this->param($params);
		
		$required = ($fieldArr['require'] == 1);
		$data = array(
			'name' => $this->prefix.'_'.$name, 
			'label' => $fieldArr['label'],
			'required' => $required ? '*' : '',
			'class' => $required ? $this->prefix.'_required' : '',
			'value' => $this->p->$posted_name
		);
		$field = wpcr_Goatee::fill($this->options['templates']['frontend_review_form_text_field'], $data);		
		return $field;
	}
	
	function get_rating_field() {
		$data = array(
			'rating_stars' => $this->get_rating_template(0, true)
		);
		$field = wpcr_Goatee::fill($this->options['templates']['frontend_review_form_rating_field'], $data);
		return $field;
	}
	
	function get_review_field() {
		$posted_name = $this->prefix.'_ftext';
		
		$params = array($posted_name);
		$this->param($params);
		
		$data = array(
			'value' => $this->p->$posted_name
		);
		$field = wpcr_Goatee::fill($this->options['templates']['frontend_review_form_review_field'], $data);		
		return $field;
	}
	
	// currently, because of how JS "wpcr3" object works, we can only display one form on a single page. To fix, we would need to make "wpcr3" a "new wpcr3();"
    function show_reviews_form($postid, $found_posts, $showform) {		
		$input_fields = '';
		
		foreach($this->options['standard_fields'] as $name => $fieldArr) {
			if ($fieldArr["ask"] == 1) {
				$input_fields .= $this->get_form_field($name, $fieldArr);
			}
		}
		
		foreach($this->options['custom_fields'] as $name => $fieldArr) {
			if ($fieldArr["ask"] == 1) {
				$input_fields .= $this->get_form_field($name, $fieldArr);
			}
		}
		
        $has_required_fields = strpos($input_fields, $this->prefix.'_required') !== false;
		$rating_field = $this->get_rating_field();
		$review_field = $this->get_review_field();
		
		$data = array(
			'input_fields' => $input_fields,
			'rating_field' => $rating_field,
			'review_field' => $review_field,
			'has_required_fields' => $has_required_fields,
			'postid' => $postid,
			'found_posts' => $found_posts,
			'showform' => $showform
		);
		
        return wpcr_Goatee::fill($this->options['templates']['frontend_review_form'], $data);
    }
	
	function generateTitle($fname) {
		$fname = (strlen($fname) === 0) ? 'Anonymous' : $fname;
		$datetime = date('m/d/Y h:i');
		return "{$fname} @ {$datetime}";
	}
	
	function css() {
		header('Content-type: text/css');
		die($this->template('wp-customer-reviews-css'));
	}
	
	function ajax() {
		header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Content-type: application/json');
		
		$rtn = new stdClass();
		$rtn->err = array();
		$rtn->success = false;
		
		$posted = new stdClass();
		foreach ($this->p as $k => $v) {
			$k = str_replace($this->prefix.'_', '', $k);
			$posted->$k = trim(strip_tags($v));
		}
		
		$params = array('checkid', 'postid', 'on_postid', 'fname', 'femail', 'ajaxAct', 'page', 'pageOpts');
		$this->param($params, $posted);
		
		$cookieName = $this->prefix.'_checkid';
		
		if ($posted->ajaxAct === 'cookie') {
			setcookie($cookieName, $posted->postid);
		} else if ($posted->ajaxAct === 'form') {
			if ($posted->checkid != $posted->postid) { $rtn->err[] = 'You have failed the spambot check. Code 1'; }
			
			// if (!isset($_COOKIE[$cookieName])) { $rtn->err[] = 'You have failed the spambot check. Code 2'; }
			// if ($posted->checkid != $_COOKIE[$cookieName]) { $rtn->err[] = 'You have failed the spambot check. Code 3'; }
			
			if ($posted->fconfirm1 != '0') { $rtn->err[] = 'You have failed the spambot check. Code 4'; }
			if ($posted->fconfirm2 != '1') { $rtn->err[] = 'You have failed the spambot check. Code 5'; }
			if ($posted->fconfirm3 != '1') { $rtn->err[] = 'You have failed the spambot check. Code 6'; }
			if ($posted->url != '') { $rtn->err[] = 'You have failed the spambot check. Code 7'; }
			if ($posted->website != '') { $rtn->err[] = 'You have failed the spambot check. Code 8'; }
			if ($posted->femail != '' && filter_var($posted->femail, FILTER_VALIDATE_EMAIL) == false) { $rtn->err[] = 'Please enter a valid email address.'; }
			
			// remove the cookie
			setcookie($cookieName, "", time()-3600);
			
			if (count($rtn->err)) { die(json_encode($rtn)); } // die here if we failed any spambot checks
			
			// passed all spambot checks, continue
			
			$title = $this->generateTitle($posted->fname);
			
			$newpost = array(
				'post_author' => 1,
				'post_date' => date('Y-m-d H:i:s'),
				'post_content' => nl2br($posted->ftext),
				'post_status' => 'pending', 
				'post_title' => $title, 
				'post_type' => $this->prefix.'_review'
			);
			$newpostid = wp_insert_post($newpost, true);
			
			update_post_meta($newpostid, $this->prefix.'_review_ip', $_SERVER['REMOTE_ADDR']);
			
			if (isset($posted->postid)) { update_post_meta($newpostid, $this->prefix.'_review_post', $posted->postid); }
			if (isset($posted->fname)) { update_post_meta($newpostid, $this->prefix.'_review_name', $posted->fname); }
			if (isset($posted->femail)) { update_post_meta($newpostid, $this->prefix.'_review_email', $posted->femail); }
			if (isset($posted->frating)) { update_post_meta($newpostid, $this->prefix.'_review_rating', $posted->frating); }
			if (isset($posted->ftitle)) { update_post_meta($newpostid, $this->prefix.'_review_title', $posted->ftitle); }
			if (isset($posted->fwebsite)) { update_post_meta($newpostid, $this->prefix.'_review_website', $posted->fwebsite); }
			
			foreach($this->options['custom_fields'] as $name => $fieldArr) {
				if ($fieldArr['ask'] == 1 && isset($posted->$name)) {
					update_post_meta($newpostid, $this->prefix.'_'.$name, $posted->$name);
				}
			}
			
			$datetime = date('m/d/Y h:i');
			@wp_mail(get_bloginfo('admin_email'), "WP Customer Reviews: New Review Posted on {$datetime}", "A new review has been posted on ".get_bloginfo('name')." via WP Customer Reviews. \n\nYou will need to approve this review before it will appear on your site.");
		} else if ($posted->ajaxAct === "pager") {
			$opts = json_decode($posted->pageOpts);
			$opts->thispage = $posted->page;
			$opts->ajax = 1;
			$opts->showsupport = 0;
			$opts->on_postid = $posted->on_postid;
			$rtn->output = $this->output_reviews_show($opts);
		}
		
		$rtn->success = true;
		die(json_encode($rtn));
	}
	
	function getAjaxURL() {
		return admin_url('admin-ajax.php').'?action=wpcr3-ajax';
		//return $this->getpluginurl()."include/ajax.php";
	}
	
	// used in extended classes to grab already-set information we care about from main class
	function setSharedVars($parentClass) {
		$this->plugin_info = &$parentClass->plugin_info; // array by &reference
		$this->plugin_version = $this->plugin_info["Version"];
		$this->pro = $parentClass->pro;
		
		$this->options = &$parentClass->options; // array by &reference
		$this->p = $parentClass->p; // object is already by &reference
	}
	
	function can_write_css() {
		$filename = $this->getplugindir().'css/wp-customer-reviews-generated.css';
		return array("filename" => $filename, "can_write" => is_writeable($filename));
	}

    function init() {
		$this->include_pro();
		
		add_action('admin_menu', array(&$this, 'admin_menu')); // adding menu items to admin must be done in admin_menu which gets executed BEFORE admin_init
		add_action('admin_init', array(&$this, 'admin_init'));
		add_filter('the_content', array(&$this, 'do_the_content'), 15); // prio 15 makes sure this hits after wptexturize and other garbage that likes to destroy our output
		
		// "Wordpress SEO - Yoast" hijacks the_content and breaks all kinds of plugins
		// luckily they provide a filter to fix it
		// add_filter('wpseo_pre_analysis_post_content', array(&$this, 'do_the_content_wpseo'), 15);
		
		// $this->print_filters_for('the_content'); exit();
		
		add_action('wp_ajax_'.$this->prefix.'-ajax', array(&$this, 'ajax'));
		add_action('wp_ajax_nopriv_'.$this->prefix.'-ajax', array(&$this, 'ajax'));
	
		$this->plugin_info = $this->plugin_get_info();
		$this->plugin_version = $this->plugin_info["Version"];
	
        $this->make_p_obj(); // make P variables object
        $this->get_options(); // populate the options array
		$this->create_post_type();
        
		// remove any existing shortcode to allow v2 and v3 to coexist
		remove_shortcode('WPCR_INSERT');
		remove_shortcode('WPCR_SHOW');
		
        add_shortcode('WPCR_INSERT', array(&$this, 'shortcode_insert'));
        add_shortcode('WPCR_SHOW', array(&$this, 'shortcode_show'));
		add_shortcode('WPCR_HCARD', array(&$this, 'shortcode_hcard')); // deprecated, returns blank
		
		// we insert styles/scripts in init because some themes are horrible
		
		$can_write_css = $this->can_write_css();
		if ($can_write_css["can_write"] === true) {
			wp_register_style('wp-customer-reviews-3-frontend', $this->getpluginurl() . 'css/wp-customer-reviews-generated.css', array(), $this->plugin_version);
		} else {
			wp_register_style('wp-customer-reviews-3-frontend', $this->getpluginurl() . 'css/wp-customer-reviews-generated.css.php', array(), $this->plugin_version);
		}
		
        wp_register_style('wp-customer-reviews-3-frontend', $this->getpluginurl() . 'css/wp-customer-reviews-generated.css', array(), $this->plugin_version);
        wp_register_script('wp-customer-reviews-3-frontend', $this->getpluginurl() . 'js/wp-customer-reviews.js', array('jquery'), $this->plugin_version);
		wp_enqueue_style('wp-customer-reviews-3-frontend');
		wp_enqueue_script('wp-customer-reviews-3-frontend');
    }
	
	function create_post_type() {
		$defaults1 = array(
			'labels' => array(),
			'public' => false,
			'exclude_from_search' => true,
			'publicly_queryable' => false,
			'show_in_nav_menus' => false,
			'show_ui' => true,
			'show_in_menu' => $this->prefix.'_view_reviews',
			'menu_position' => 25,
			'show_in_admin_bar' => false,
			'has_archive' => false,
			'rewrite' => false,
			'supports' => array('title'),
			'map_meta_cap' => true
		);
	
		$defaults2 = $defaults1;
		$defaults2['labels'] = array(
			'name' => 'WP Customer Reviews',
			'singular_name' => 'Review',
			'menu_name' => 'All Reviews',
			'add_new_item' => 'Add New Customer Review',
			'edit_item' => 'Edit Customer Review',
			'new_item' => 'New Customer Review',
			'view_item' => 'View Customer Review',
			'search_items' => 'Search Customer Reviews',
			'not_found' => 'No Reviews Found',
			'not_found_in_trash' => 'No Reviews Found in Trash'
		);
		$defaults2['supports'] = array('title', 'editor');
		$err = register_post_type($this->prefix.'_review', $defaults2);
	}
    
    function shortcode_insert() {
        $this->force_active_page = 'shortcode_insert';
        return $this->do_the_content('', 'shortcode_insert');
    }
	
	function strip_trim($val) {
		return trim(strip_tags($val));
	}
	
	function strip_trim_intval($val) {
		return intval($this->strip_trim($val));
	}
    
    function shortcode_show($atts) {
		$attArr = shortcode_atts(array(
			'postid' => 'all', 'num' => 5, 'paginate' => 1, 'perpage' => 5, 'hidecustom' => 0, 'snippet' => 0,
			'more' => '', 'showform' => 1, 'hidereviews' => 0, 'hideresponse' => 0
		), $atts);
		
		$opts = new stdClass();
        foreach ($attArr as $key => $val) {
			$opts->$key = $val;
		}
		
		$opts->postid = $this->strip_trim($opts->postid);
        
        if (strtolower($opts->postid) == 'all') { $opts->postid = -1; } // -1 queries all reviews
        $opts->morelink = $opts->more;
		
		$intArr = array('postid', 'num', 'paginate', 'perpage', 'hidecustom', 'snippet', 'showform', 'hidereviews', 'hideresponse');
		foreach ($intArr as $key) {
			$opts->$key = isset($opts->$key) ? $this->strip_trim_intval($opts->$key) : 0;
		}
        
		if ($opts->postid === -1) { $opts->showform = 0; } // do not show form if postid is "all"
		$opts->showsupport = 0;
		if ($opts->showform == 1) { $opts->showsupport = 1; }
		
		$opts->wrapper = 1;
		
        return $this->output_reviews_show($opts);
    }
	
	// deprecated, returns blank
	function shortcode_hcard($atts) {
		return '';
	}

    function activate() {
        add_option($this->prefix.'_gotosettings', true); // used for redirecting to settings page upon initial activation
    }
	
	function deactivate() {
        // do not fire on upgrading plugin or upgrading WP - only on true manual deactivation
        if (isset($this->p->action) && $this->p->action == 'deactivate') {
			$this->admin_function('notify_activate', 2);
        }
    }
	
	function template($name) {
		return $this->options['templates'][$name];
	}

    function getpluginurl() {
		return trailingslashit(plugins_url(basename(dirname(__FILE__))));
	}
	
	function getpluginurl_abs() {
		$url = $this->getpluginurl();
		if (strpos($url, "://") === false) {
			$proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? "https://" : "http://";
			$url = $proto . $_SERVER['HTTP_HOST'] . $url;
		}
		return $url;
    }

    function getplugindir() {
        return trailingslashit(WP_PLUGIN_DIR . '/' . str_replace(basename(__FILE__), "", plugin_basename(__FILE__)));
    }
}

function start_wpcr3() {
	$WPCustomerReviews3 = new WPCustomerReviews3();
	$WPCustomerReviews3->start();
	return $WPCustomerReviews3;
}

$WPCustomerReviews3 = start_wpcr3();
?>