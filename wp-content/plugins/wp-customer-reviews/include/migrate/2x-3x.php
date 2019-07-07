<?php
function wpcr3_migrate_2x_3x(&$this2, $current_dbversion) {
	global $wpdb;
	
	// if anything fails, RETURN FALSE
	
	$old_2x_options = get_option("wpcr_options");
	if ($old_2x_options === false) { return true; }
	if (isset($old_2x_options['migrated_to_3x']) && $old_2x_options['migrated_to_3x'] == 1) { return true; }
	
	// update 2x options so it does not migrate again
	$old_2x_options['migrated_to_3x'] = 1;
	update_option('wpcr_options', $old_2x_options);
	
	$this2->options['act_email'] = $old_2x_options['act_email'];
	$this2->options['act_uniq'] = $old_2x_options['act_uniq'];
	$this2->options['support_us'] = $old_2x_options['support_us'];
	$this2->options['reviews_per_page'] = $old_2x_options['reviews_per_page'];
	
	if ($this2->options['activated'] == 0) {
		$this2->options['activated'] = $old_2x_options['activate']; // "activate" -> "activated"
	}
	
	$new = new stdClass();
	
	$new->type = $old_2x_options['hreview_type']; // "business" or "product" - all 2x reviews had to be the same type
	
	$new->business_city = $old_2x_options['business_city'];
	$new->business_country = $old_2x_options['business_country'];
	$new->business_name = $old_2x_options['business_name'];
	$new->business_phone = $old_2x_options['business_phone'];
	$new->business_state = $old_2x_options['business_state'];
	$new->business_street = $old_2x_options['business_street'];
	$new->business_url = $old_2x_options['business_url'];
	$new->business_zip = $old_2x_options['business_zip'];
	
	$ask_fields = $old_2x_options['ask_fields'];
	$new->ask_name = ($ask_fields["fname"] == 1); // boolean
	$new->ask_email = ($ask_fields["femail"] == 1); // boolean
	$new->ask_website = ($ask_fields["fwebsite"] == 1); // boolean
	$new->ask_title = ($ask_fields["ftitle"] == 1); // boolean
	
	$req_fields = $old_2x_options['require_fields'];
	$new->req_name = ($req_fields["fname"] == 1); // boolean
	$new->req_email = ($req_fields["femail"] == 1); // boolean
	$new->req_website = ($req_fields["fwebsite"] == 1); // boolean
	$new->req_title = ($req_fields["ftitle"] == 1); // boolean
	
	$show_fields = $old_2x_options['show_fields'];
	$new->show_name = ($show_fields["fname"] == 1); // boolean
	$new->show_email = ($show_fields["femail"] == 1); // boolean
	$new->show_website = ($show_fields["fwebsite"] == 1); // boolean
	$new->show_title = ($show_fields["ftitle"] == 1); // boolean
	
	$ask_custom = $old_2x_options['ask_custom'];
	for ($i=0;$i<3;$i++) {
		if (!isset($ask_custom[$i])) { $ask_custom[$i] = 0; }
	}
	$new->ask_custom_1 = ($ask_custom[0] == 1); // boolean
	$new->ask_custom_2 = ($ask_custom[1] == 1); // boolean
	$new->ask_custom_3 = ($ask_custom[2] == 1); // boolean
	
	$req_custom = $old_2x_options['require_custom'];
	for ($i=0;$i<3;$i++) {
		if (!isset($req_custom[$i])) { $req_custom[$i] = 0; }
	}
	$new->req_custom_1 = ($req_custom[0] == 1); // boolean
	$new->req_custom_2 = ($req_custom[1] == 1); // boolean
	$new->req_custom_3 = ($req_custom[2] == 1); // boolean
	
	$show_custom = $old_2x_options['show_custom'];
	for ($i=0;$i<3;$i++) {
		if (!isset($show_custom[$i])) { $show_custom[$i] = 0; }
	}
	$new->show_custom_1 = ($show_custom[0] == 1); // boolean
	$new->show_custom_2 = ($show_custom[1] == 1); // boolean
	$new->show_custom_3 = ($show_custom[2] == 1); // boolean
	
	$field_custom = $old_2x_options['field_custom'];
	for ($i=0;$i<3;$i++) {
		if (!isset($field_custom[$i])) { $field_custom[$i] = 0; }
	}
	$new->field_custom_1 = $field_custom[0]; // field label
	$new->field_custom_2 = $field_custom[1]; // field label
	$new->field_custom_3 = $field_custom[2]; // field label
	
	// begin: import reviews assigned to posts, update per-post wpcr meta
	
	$post_ids = array();
	
	// find posts that were automagically enabled with [WPCR_INSERT], but no checkbox enabled
	$queryOpts = array(
		'nopaging' => true,
		'ignore_sticky_posts ' => true,
		'post_type' => 'any',
		'post_status' => 'publish,pending,draft,future,private,trash',
		'fields' => 'ids',
		's' => '[WPCR_INSERT]'
	);
	$res = new WP_Query($queryOpts);
	$post_ids = array_merge($post_ids, $res->posts);
	
	// find posts which are enabled via checkbox
	$queryOpts = array(
		'nopaging' => true,
		'ignore_sticky_posts ' => true,
		'post_type' => 'any',
		'post_status' => 'publish,pending,draft,future,private,trash',
		'fields' => 'ids',
		'meta_query' => array(
			array(
				'key' => 'wpcr_enable',
				'value' => '1',
				'compare' => '='
			)
		)
	);
	$res = new WP_Query($queryOpts);
	$post_ids = array_merge($post_ids, $res->posts);
	
	// begin: find posts which have 2x reviews associated with their post ID
	// $reviews_2x will be re-used further down
	$reviews_2x = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wpcreviews ORDER BY page_id ASC, id ASC");
	$assigned_page_ids = array();
	foreach ($reviews_2x as $review) {
		$assigned_page_ids[] = intval($review->page_id);
	}
	$assigned_page_ids = array_unique($assigned_page_ids);
	
	if (count($assigned_page_ids)) {
		$queryOpts = array(
			'nopaging' => true,
			'ignore_sticky_posts ' => true,
			'post_type' => 'any',
			'post_status' => 'publish,pending,draft,future,private,trash',
			'post__in' => $assigned_page_ids,
			'fields' => 'ids'
		);
		$res = new WP_Query($queryOpts);		
		$post_ids = array_merge($post_ids, $res->posts);
	}
	// end: find posts which have 2x reviews associated with their post ID
	
	$post_ids = array_unique($post_ids);
	foreach ($post_ids as $postid) {
		$tmp = get_post_custom($postid);		
		$meta = new stdClass();
		foreach ($tmp as $key => $metaArr) {
			if (strpos($key, "wpcr_") !== false) {
				$meta->$key = $metaArr[0];
			}
		}
		
		if (isset($meta->wpcr_migrated_to_3x)) { continue; }
		
		// update $postid with v3 options ( product vs business )
		
		update_post_meta($postid, 'wpcr_migrated_to_3x', '1');
		update_post_meta($postid, 'wpcr3_enable', '1');
		update_post_meta($postid, 'wpcr3_format', $new->type);
		
		if ($new->type === "business") {
			update_post_meta($postid, 'wpcr3_business_name', $new->business_name);
			update_post_meta($postid, 'wpcr3_business_street1', $new->business_street);
			update_post_meta($postid, 'wpcr3_business_city', $new->business_city);
			update_post_meta($postid, 'wpcr3_business_state', $new->business_state);
			update_post_meta($postid, 'wpcr3_business_zip', $new->business_zip);
			update_post_meta($postid, 'wpcr3_business_country', $new->business_country);
			update_post_meta($postid, 'wpcr3_business_phone', $new->business_phone);
			update_post_meta($postid, 'wpcr3_business_url', $new->business_url);
		} else if ($new->type === "product") {
			update_post_meta($postid, 'wpcr3_product_name', $meta->wpcr_product_name);
			update_post_meta($postid, 'wpcr3_product_brand', $meta->wpcr_product_brand);
			if ($meta->wpcr_product_upc != "") {
				update_post_meta($postid, 'wpcr3_product_id', "upc:".$meta->wpcr_product_upc);
			} else if ($meta->wpcr_product_sku != "") {
				update_post_meta($postid, 'wpcr3_product_id', "sku:".$meta->wpcr_product_sku);
			} else if ($meta->wpcr_product_model != "") {
				update_post_meta($postid, 'wpcr3_product_id', "mpn:".$meta->wpcr_product_model);
			}
		}
	}
	// end: import per-post plugin settings
	
	// begin: import reviews
	foreach ($reviews_2x as $review) {
		$status = 'publish';
		if ($review->status == '0') { $status = 'pending'; }
		else if ($review->status == '1') { $status = 'publish'; }
		else if ($review->status == '2') { $status = 'trash'; }
		
		$name = (strlen($review->reviewer_name) === 0) ? 'Anonymous' : $review->reviewer_name;
		$datetime = date('m/d/Y h:i', strtotime($review->date_time));
		$title = "{$name} @ {$datetime}";
		
		$newpost = array(
			'post_author' => 1,
			'post_date' => $review->date_time,
			'post_content' => nl2br($review->review_text),
			'post_status' => $status, 
			'post_title' => $title, 
			'post_type' => 'wpcr3_review'
		);
		$newpostid = wp_insert_post($newpost, true);
		
		update_post_meta($newpostid, 'wpcr3_review_post', $review->page_id);
		
		update_post_meta($newpostid, 'wpcr3_review_name', $review->reviewer_name);
		update_post_meta($newpostid, 'wpcr3_review_email', $review->reviewer_email);
		update_post_meta($newpostid, 'wpcr3_review_ip', $review->reviewer_ip);
		update_post_meta($newpostid, 'wpcr3_review_website', $review->reviewer_url);
		
		update_post_meta($newpostid, 'wpcr3_review_title', $review->review_title);
		update_post_meta($newpostid, 'wpcr3_review_rating', $review->review_rating);
		update_post_meta($newpostid, 'wpcr3_review_admin_response', $review->review_response);
		
		if (isset($review->custom_fields) && substr($review->custom_fields, 0, 2) === "a:") {
			$custom_fields = unserialize($review->custom_fields);
			$field_num = 0;
			foreach ($custom_fields as $value) {
				$field_num++;
				update_post_meta($newpostid, "wpcr3_f{$field_num}", $value);
			}
		}
	}
	// end: import reviews
	
	// update 3x options
	update_option($this2->options_name, $this2->options);
	
	return true;
}
?>