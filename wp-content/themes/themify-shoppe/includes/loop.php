<?php
/**
 * Template for generic post display.
 * @package themify
 * @since 1.0.0
 */
?>
<?php 
$is_single = is_single();
if(!$is_single){ 
	global $more; $more = 0; 
} //enable more link ?>

<?php
/** Themify Default Variables
 *  @var object */
global $themify; ?>

<?php themify_post_before(); // hook ?>
<article id="post-<?php the_id(); ?>" <?php post_class( 'post clearfix' ); ?>>
	<?php themify_post_start(); // hook ?>

	<?php if('below' != $themify->media_position) get_template_part( 'includes/post-media', 'loop'); ?>

	<div class="post-content">
        <?php if($themify->unlink_image != 'yes' && $themify->post_layout==='auto_tiles'):?>
            <a href="<?php the_permalink(); ?>" class="tiled_overlay_link"></a>
        <?php endif;?>
		<div class="post-content-inner-wrapper">
			<div class="post-content-inner">
	
			<?php get_template_part( 'includes/post-cats', 'loop')?>
	
			
			<?php if ( $themify->hide_title != 'yes' ): ?>
				<?php themify_post_title(); ?>
			<?php endif; //post title ?>
			
			<?php if($themify->hide_meta != 'yes'): ?>
				<p class="post-meta entry-meta">
					<?php if($themify->hide_meta_author != 'yes'): ?>
						<span class="post-author"><?php echo themify_get_author_link(); ?></span>
					<?php endif; ?>
					
					<?php  if( !themify_get('setting-comments_posts') && comments_open() && $themify->hide_meta_comment != 'yes' ) : ?>
						<span class="post-comment"><?php comments_popup_link( __( '0 Comment', 'themify' ), __( '1 Comment', 'themify' ), __( '% Comments', 'themify' ) ); ?></span>
					<?php endif; ?>
					
					<?php get_template_part( 'includes/post-date', 'loop')?>
				</p>
			<?php endif; //post meta ?>

			<?php if('below' == $themify->media_position) get_template_part( 'includes/post-media', 'loop'); ?>
			
			<div class="entry-content">
				<?php if ( 'excerpt' == $themify->display_content && ! is_attachment() ) : ?>
					
					<?php the_excerpt(); ?>

					<?php if(!$is_single && themify_check('setting-excerpt_more') ) : ?>
						<p><a href="<?php the_permalink(); ?>" class="more-link"><?php echo themify_check('setting-default_more_text')? themify_get('setting-default_more_text') : __('More &rarr;', 'themify') ?></a></p>
					<?php endif; ?>
				
				<?php elseif($themify->display_content !== 'none'): ?>

					<?php the_content(themify_check('setting-default_more_text')? themify_get('setting-default_more_text') : __('More &rarr;', 'themify')); ?>

				<?php endif; //display content ?>
			</div>
			<?php edit_post_link(__('Edit', 'themify'), '<span class="edit-button">[', ']</span>'); ?>
			</div>
		</div>
	</div>
	<!-- /.post-content -->
	<?php themify_post_end(); // hook ?>

</article>
<!-- /.post -->
<?php themify_post_after(); // hook ?>
