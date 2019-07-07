<?php
	if (function_exists("current_user_can") === false || current_user_can('manage_options') !== true) {
		die("Access Denied");
	}

	// deletes all 3x reviews
	if (!isset($_POST['wpcr3_confirm']) || $_POST['wpcr3_confirm'] !== 'YES') {
		?>
		This will permanently delete all of your reviews.<br /><br />
		To continue the deletion process, type YES in all caps below.<br /><br />
		<input name="wpcr3_confirm" type="text" value="" />&nbsp;&nbsp;
		<input name="wpcr3_debug_code" type="hidden" value="<?php echo $_POST['wpcr3_debug_code'] ?>" />
		<input type="submit" value="Confirm" />
		<?php
		die();
	}

	$queryOpts = array(
		'nopaging' => true,
		'post_type' => 'wpcr3_review',
		'post_status' => 'publish,pending,draft,future,private,trash'
	);
	$posts = new WP_Query($queryOpts);
	foreach ($posts->posts as $post) {
		print "Deleting review $post->ID<br />";
		wp_delete_post($post->ID, true);
	}
?>