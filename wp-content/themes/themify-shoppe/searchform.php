<?php
/**
 * Template for search form.
 * @package themify
 * @since 1.0.0
 */
?>
<form method="get" id="searchform" action="<?php echo home_url(); ?>/">

	<i class="icon-search"></i>

	<input type="text" name="s" id="s" title="<?php _e( 'Search', 'themify' ); ?>" value="<?php echo get_search_query(); ?>" />

</form>