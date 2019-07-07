<?php $socials = themify_get_footer_banners();?>
<?php if(!empty($socials)):?>
	<div class="footer-social-wrap">
		<?php 
			$styles ='';
			$key = 'settings-footer_banner_';
		?>
		<?php foreach($socials as $k=>$v):?>
			<?php $input=$key.$k;?>
			<?php if(themify_check($input)):?>
				<?php 
					$link=themify_get($key.$k.'_link');
					$username=themify_get($key.$k.'_username');
					$image=themify_get($key.$k.'_image');
					if($image){
						$styles.='.footer-social-wrap .footer-social-badge.'.$k.' a{background-image: url('.esc_url($image).');}'."\n";
					}
					
				?>
				<div class="footer-social-badge <?php echo $k?>">
					<a href="<?php echo $link?esc_url($link):''?>">
						<strong><?php echo $v?></strong>
						<span><?php echo $username?></span>
					</a>
				</div>
			<?php endif;?>
		<?php endforeach;?>
		<?php if($styles):?>
			<style type="text/css" scoped>
				<?php echo $styles?>
			</style>
		<?php endif;?>
	</div>
<?php endif;?>