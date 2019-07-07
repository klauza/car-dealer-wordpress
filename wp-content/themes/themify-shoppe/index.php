<?php
/**
 * Template for common archive pages, author and search results
 * @package themify
 * @since 1.0.0
 */

get_header(); ?>

<?php
/** Themify Default Variables
 *  @var object */
global $themify;
?>

<!-- layout -->
<div id="layout" class="pagewidth clearfix">

	<!-- content -->
    <?php themify_content_before(); //hook ?>
	<div id="content" class="clearfix">
    	<?php themify_content_start(); //hook ?>

		<?php themify_page_title(); ?>
		<?php themify_page_description(); ?>

		<?php
		/////////////////////////////////////////////
		// Loop
		/////////////////////////////////////////////
		?>
		<?php if (have_posts()) : ?>
			
			<?php if(themify_is_product_search()):?>
				<?php get_template_part( 'includes/query-product', get_post_type() ); ?>
			<?php else:?>
				<!-- loops-wrapper -->
				<div id="loops-wrapper" class="loops-wrapper <?php  esc_attr_e( themify_theme_query_classes() ); ?>">
					
					
					<?php while (have_posts()) : the_post(); ?>

						<?php get_template_part( 'includes/loop', get_post_type() ); ?>

					<?php endwhile; ?>

				</div>
				<!-- /loops-wrapper -->

				<?php get_template_part( 'includes/pagination'); ?>
			<?php endif;?>

		<?php
		/////////////////////////////////////////////
		// Error - No Page Found
		/////////////////////////////////////////////
		?>

		<?php else : ?>

			<p><?php _e( 'Sorry, nothing found.', 'themify' ); ?></p>

		<?php endif; ?>
	<?php themify_content_end(); //hook ?>
	</div>
    <?php themify_content_after(); //hook ?>
	<!-- /#content -->

	<?php
	/////////////////////////////////////////////
	// Sidebar
	/////////////////////////////////////////////
	if ($themify->layout != 'sidebar-none'): get_sidebar(); endif; ?>

</div>
<!-- /#layout -->

<?php get_footer(); ?>
