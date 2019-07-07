<?php
/**
 * Template to load top bar widgets.
 * @package themify
 * @since 1.0.0
 */

?>

<div class="top-bar-widgets">
	<div class="top-bar-widget-inner pagewidth clearfix">
		<div class="top-bar-left">
			<?php dynamic_sidebar( 'top-bar-left'); ?>
		</div>
		<div class="top-bar-right">
			<?php dynamic_sidebar( 'top-bar-right'); ?>
		</div>
		<!-- /.top-bar-widget-inner -->
	</div>
</div>
<!-- /.top-bar-widget -->