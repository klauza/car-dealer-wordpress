<?php
/**
 * Template for site footer
 * @package themify
 * @since 1.0.0
 */

/** Themify Default Variables
 *  @var object */
	global $themify; 
	
	?>
		
			<?php themify_layout_after(); // hook ?>
				
			</div>
			<!-- /body -->
			<?php if ( themify_theme_show_area( 'footer' ) && themify_theme_do_not_exclude_all( 'footer' ) ) : ?>
			
			<?php
					$footer_widgets = themify_theme_show_area( 'footer_widgets' );
					 if($footer_widgets){
						$footer_position = themify_get('footer_widget_position');
						if(!$footer_position){
							$footer_position = themify_get('setting-footer_widget_position');
						}
					}
					else{
						$footer_position = false;
					}
	
			?>
			<div id="footerwrap">

				<?php themify_footer_before(); // hook ?>
				
				<?php get_template_part( 'includes/footer-banners' ); ?>
				
				
				<footer id="footer" class="pagewidth clearfix" itemscope="itemscope" itemtype="https://schema.org/WPFooter">

					<?php themify_footer_start(); // hook ?>
					
					<div class="footer-column-wrap clearfix">
						<div class="footer-logo-wrap">
							<?php if ( themify_theme_show_area( 'footer_site_logo' ) ) : ?>
								<?php echo themify_logo_image( 'footer_logo', 'footer-logo' ); ?>																	  
								<!-- /footer-logo -->
							<?php endif; ?>
							<?php if ( is_active_sidebar( 'below-logo-widget' ) ) : ?>
								<div class="below-logo-widget">
									<?php dynamic_sidebar( 'below-logo-widget' ); ?>
								</div>
								<!-- /.below-logo-widget -->
							<?php endif; ?>
							<div class="footer-text-outer">
							
								<?php 
									if ( themify_theme_show_area( 'footer_back' ) ) {
										printf( '<div class="back-top clearfix %s"><div class="arrow-up"><a href="#header"></a></div></div>'
											, themify_check( 'setting-use_float_back' ) ? 'back-top-float back-top-hide' : '' );

									}
								?>
								
								<?php if ($footer_position!=='top') : ?>
									<div class="footer-text clearfix">
										<?php if ( themify_theme_show_area( 'footer_texts' ) ) : ?>
											<?php themify_the_footer_text(); ?>
											<?php themify_the_footer_text('right'); ?>
										<?php endif; ?>
									</div>
									<!-- /.footer-text -->
								<?php endif;?>
								
							</div>
						</div>
						
						
						<!-- /footer-logo-wrap -->
						<?php if ($footer_widgets) : ?>
						
							<div class="footer-widgets-wrap"> 
								<?php get_template_part( 'includes/footer-widgets' ); ?>
							</div>
							<!-- /footer-widgets-wrap -->
							<?php if($footer_position==='top'):?>
								<div class="footer-text clearfix">
									<?php if ( themify_theme_show_area( 'footer_texts' ) ) : ?>
										<?php themify_the_footer_text(); ?>
										<?php themify_the_footer_text('right'); ?>
									<?php endif; ?>
								</div>
								<!-- /.footer-text -->
							<?php endif;?>
						<?php endif;?>

						<?php if ( themify_theme_show_area( 'footer_menu_navigation' ) ) : ?>
							<div class="footer-nav-wrap">
								<?php wp_nav_menu( array(
									'theme_location' => 'footer-nav',
									'fallback_cb' => '',
									'container'  => '',
									'menu_id' => 'footer-nav',
									'menu_class' => 'footer-nav',
								)); ?>
							</div>
							<!-- /.footer-nav-wrap -->
						<?php endif; // exclude menu navigation ?>
						
					</div>
					
					<?php themify_footer_end(); // hook ?>

				</footer>
				<!-- /#footer -->

				<?php themify_footer_after(); // hook ?>

			</div>
			<!-- /#footerwrap -->
			
			<?php endif; // exclude footer ?>

		</div>
		<!-- /#pagewrap -->

		<?php
		/**
		 *  Stylesheets and Javascript files are enqueued in theme-functions.php
		 */
		?>

		<!-- wp_footer -->
		<?php wp_footer(); ?>
		<?php themify_body_end(); // hook ?>
	</body>
</html>