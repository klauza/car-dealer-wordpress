<?php
	if (function_exists("current_user_can") === false || current_user_can('manage_options') !== true) {
		die("Access Denied");
	}

	// imports 2x reviews
	if (!isset($_POST['wpcr3_confirm']) || $_POST['wpcr3_confirm'] !== 'YES') {
		?>
		This will re-import all reviews AND settings from v2.x, even if they were previously imported.<br /><br />
		To continue the importing process, type YES in all caps below.<br /><br />
		<input name="wpcr3_confirm" type="text" value="" />&nbsp;&nbsp;
		<input name="wpcr3_debug_code" type="hidden" value="<?php echo $_POST['wpcr3_debug_code'] ?>" />
		<input type="submit" value="Confirm" />
		<?php
		die();
	}
	
	// remove upgraded bit from 2x settings
	$old_2x_options = get_option("wpcr_options");
	$old_2x_options['migrated_to_3x'] = 0;
	update_option('wpcr_options', $old_2x_options);
	
	// remove 3x upgraded bit for all wp posts
	$queryOpts = array(
		'nopaging' => true,
		'post_type' => 'any',
		'post_status' => 'publish,pending,draft,future,private,trash',
		'meta_query' => array(
			array(
				'key' => 'wpcr_migrated_to_3x',
				'value' => '1',
				'compare' => '='
			)
		)
	);
	$migrated_posts = new WP_Query($queryOpts);
	foreach ($migrated_posts->posts as $post) {
		delete_post_meta($post->ID, 'wpcr_migrated_to_3x');
	}
	
	// run 2x-3x migrate script
	include($this->getplugindir().'include/migrate/2x-3x.php');
	$migrate_ok = wpcr3_migrate_2x_3x($this, 248);
?>