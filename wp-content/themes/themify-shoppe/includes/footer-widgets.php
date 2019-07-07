<?php
/**
 * Template to load footer widgets.
 * @package themify
 * @since 1.0.0
 */
 
$footer_widget_option = ( '' == themify_get('setting-footer_widgets') ) ? 'footerwidget-4col' : themify_get('setting-footer_widgets');

if ( $footer_widget_option != 'none' ) : ?>

	<div class="footer-widgets clearfix">

		<?php
		$columns = array('footerwidget-4col' => array('col4-1','col4-1','col4-1','col4-1'),
						 'footerwidget-3col' => array('col3-1','col3-1','col3-1'),
						 'footerwidget-2col' => array('col4-2','col4-2'),
						 'footerwidget-1col' => array('') );
		$x = 0;
		foreach($columns[$footer_widget_option] as $col): ?>
			<?php 
				 $x++;
				 if( 1 == $x ){ 
					  $class = 'first';
				 } else {
					  $class = '';	
				 }
			?>
			<div class="<?php echo esc_attr( $col . ' ' . $class ); ?>">
				<?php dynamic_sidebar( 'footer-widget-' . $x ); ?>
			</div>
		<?php endforeach; ?>

	</div>
	<!-- /.footer-widgets -->

<?php endif; // end footer widget option ?>