<?php global $themify;?>
<?php if($themify->hide_meta != 'yes' && ($themify->hide_meta_tag != 'yes' || $themify->hide_meta_category != 'yes')): ?>
	<span class="post-cat-tag-wrap">
		<?php if($themify->hide_meta_category != 'yes'): ?>
			<?php the_terms( get_the_ID(), 'category', ' <span class="post-category">', '<span class="post-meta-separator">,</span> ', '</span>' ); ?>
		<?php endif; ?>
		<?php if($themify->hide_meta_tag != 'yes'): ?>
			<?php the_terms( get_the_ID(), 'post_tag', ' <span class="post-tag">', '<span class="post-meta-separator">,</span> ', '</span>' ); ?>
		<?php endif; ?>
	</span>
<?php endif;?>