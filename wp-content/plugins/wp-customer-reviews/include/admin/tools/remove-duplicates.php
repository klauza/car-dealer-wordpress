<?php
	if (function_exists("current_user_can") === false || current_user_can('manage_options') !== true) {
		die("Access Denied");
	}

	// removes duplicate 3x reviews
	// determines duplicate by name + email + reviewed page id + date
	// keeps lowest page ids found
	if (!isset($_POST['wpcr3_confirm']) || $_POST['wpcr3_confirm'] !== 'YES') {
		?>
		This will attempt to de-dupe reviews. Duplicates are determined by comparing title + content + timestamp + reviewed page id<br /><br />
		To continue the duplicate removal process, type YES in all caps below.<br /><br />
		<input name="wpcr3_confirm" type="text" value="" />&nbsp;&nbsp;
		<input name="wpcr3_debug_code" type="hidden" value="<?php echo $_POST['wpcr3_debug_code'] ?>" />
		<input type="submit" value="Confirm" />
		<?php
		die();
	}
	
	$queryOpts = array(
		'nopaging' => true,
		'post_type' => $this->prefix.'_review',
		'post_status' => 'publish,pending,draft,future,private' // no trash
	);
	$posts = new WP_Query($queryOpts);
	
	$unique_hashes = array();
	
	foreach ($posts->posts as $post) {
		$reviewed_post_id = get_post_meta($post->ID, $this->prefix.'_review_post' ,true);		
		$hash = "'{$post->post_date}'__'{$post->post_title}'__'{$post->post_content}'__'{$reviewed_post_id}'";
		$hash = md5($hash);
		
		if (!in_array($hash, $unique_hashes)) {
			$unique_hashes[] = $hash;
			continue;
		}
		
		// if we get here, we have a dupe
		print "Deleting duplicate review $post->ID $post->title<br />";
		wp_delete_post($post->ID, true);
	}
?>