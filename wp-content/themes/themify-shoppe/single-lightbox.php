<div id="pagewrap">
	<div id="body">
		<div id="layout" class="pagewidth clearfix">
			<div id="content" class="list-post">
				<div class="product-lightbox">

					<?php if (have_posts()) while (have_posts()) : the_post(); ?>

						<?php wc_get_template_part('content', 'single-product'); ?>

						<?php edit_post_link(__('Edit', 'themify'), '<span class="edit-button">[', ']</span>'); ?>

					<?php endwhile; ?>
				</div>
				<!-- /.lightbox-item -->
			</div>
		</div>
	</div>
</div>