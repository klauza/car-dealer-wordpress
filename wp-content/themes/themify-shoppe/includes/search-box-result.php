<?php 
global $query,$is_woocmerce_active,$is_post,$is_page;
if( $query->have_posts()):
?>
	<ul class="search-option-tab">
		<li class="active"><a href="#all"><?php _e('All results','themify')?></a></li>
		<?php if(($is_woocmerce_active && ($is_post || $is_page)) || (!$is_woocmerce_active && $is_post && $is_page)):?>
			<?php if($is_woocmerce_active):?>
				<li><a href="#product"><?php _e('Shop','themify')?></a></li>
			<?php endif;?>
			<?php if($is_post):?>
				<li><a href="#post"><?php _e('Blog','themify')?></a></li>
			<?php endif;?>
			<?php if($is_page):?>
				<li><a href="#page"><?php _e('Page','themify')?></a></li>
			<?php endif;?>
		<?php endif;?>
	</ul>
	<?php $is_disabled = themify_is_image_script_disabled();?>
	<?php  while ( $query->have_posts() ):?>
		<?php 
		$query->the_post();
		$post_type = get_post_type();
		$chechk_product = $is_woocmerce_active && $post_type==='product';	
		if(has_post_thumbnail()){
			$post_image = !$is_disabled?themify_get_image(array('w'=>47,'h'=>48,'crop'=>true,'urlonly'=>true,'ignore'=>true)):'';
			if(!$post_image){
				$post_image = $chechk_product?get_the_post_thumbnail_url( null,'shop_thumbnail'):get_the_post_thumbnail_url( null,'thumbnail');
			}
		}
		else{
			$post_image = '//placehold.it/47x48';
		}
	?>
		<div class="result-item <?php echo $chechk_product?'result-product':($is_page && $post_type==='page'?'result-page':'result-post')?>"> 
			<a href="<?php the_permalink()?>">
				<img src="<?php echo $post_image;?>" width="47" height="48" />
				<h3 class="title"><?php the_title()?></h3>
				<?php if($chechk_product):?>
					<?php global $product?>
					<span class="price"><?php echo $product->get_price_html()?></span>
				<?php endif;?>
			</a> 
		</div>
		<!-- /result-item -->
	<?php endwhile;?>
	
	<?php if($query->max_num_pages>1):?>
		<div class="view-all-wrap"> 
			<?php $search_link = get_search_link($_POST['term']);?>
			<?php if(($is_woocmerce_active && ($is_post || $is_page)) || (!$is_woocmerce_active && $is_post && $is_page)):?>
				<?php if($is_woocmerce_active):?>
					<a id="result-link-product" href="<?php echo add_query_arg(array('type'=>'product'),$search_link)?>" class="view-all-button"><?php _e('View All Products','themify')?></a> 
				<?php endif;?>
				<?php if($is_post):?>
					<a id="result-link-post" href="<?php echo add_query_arg(array('type'=>'post'),$search_link)?>" class="view-all-button"><?php _e('View All Posts','themify')?></a> 
				<?php endif;?>
				<?php if($is_page):?>
					<a id="result-link-page" href="<?php echo add_query_arg(array('type'=>'page'),$search_link)?>" class="view-all-button"><?php _e('View All Pages','themify')?></a> 
				<?php endif;?>
			<?php endif;?>
			<a id="result-link-item" href="<?php echo $search_link ?>" class="view-all-button"><?php _e('View All','themify')?></a> 
		</div>
		<!-- /view-all-wrap -->
	<?php endif;?>
	<?php else:?>
		<p><?php _e('No Items Found','themify');?></p>
<?php endif;?>