<?php
/**
 * Partial template for pagination.
 * Creates numbered pagination or displays button for infinite scroll based on user selection
 *
 * @since 1.0.0
 */
global $themify;
if ( 'infinite' == themify_theme_get( 'more_posts', 'infinite' ) )  {
	global $wp_query;
	$total_pages  = !empty($themify->query)?$themify->query->max_num_pages:$wp_query->max_num_pages;
	$current_page = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
	if ( $total_pages > $current_page ) {
		if ( $themify->query_category != '' ) {
			//If it's a Query Category page, set the number of total pages
			echo '<script type="text/javascript">var qp_max_pages = ' . $total_pages . '</script>';
		}
		echo '<p id="load-more"><a href="' . next_posts( $total_pages, false ) . '" class="load-more-button">' . __( 'Load More', 'themify' ) . '</a></p>';
	}
} else {
	if ( 'numbered' == themify_get( 'setting-entries_nav' ) || '' == themify_get( 'setting-entries_nav' ) ) {
		themify_pagenav('','',!empty($themify->query)?$themify->query:'');
	} else { ?>
		<div class="post-nav">
			<span class="prev"><?php next_posts_link( __( '&laquo; Older Entries', 'themify' ) ) ?></span>
			<span class="next"><?php previous_posts_link( __( 'Newer Entries &raquo;', 'themify' ) ) ?></span>
		</div>
	<?php
	}
} // infinite
?>