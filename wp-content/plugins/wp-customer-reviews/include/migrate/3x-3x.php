<?php
function wpcr3_migrate_3x_3x(&$this2, $current_dbversion) {
	// if anything fails, RETURN FALSE
	
	if ($this2->pro === false) {
		// if Lite version, on every new plugin version we replace templates in the DB with default template files
		// this is because templates are stored in the DB and there is no method for Lite users to edit them but they need to get updates.
		$this2->options['templates'] = $this2->default_options['templates'];
		update_option($this2->options_name, $this2->options);
	}
	
	/* example of upgrade to 3.0.1 which needs migration tasks */
	if ($current_dbversion < 301) {
		/* 
			TODO: perform any migration tasks
			$current_dbversion = $this2->update_db_version(301);
		*/
	}

	/* example of upgrade to 3.0.3 which needs migration tasks */
	if ($current_dbversion < 302) {
		/* 
			TODO: perform any migration tasks 
			$current_dbversion = $this2->update_db_version(302);
		*/
	}
	
	return true;
}
?>