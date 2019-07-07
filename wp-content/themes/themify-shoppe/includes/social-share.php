<?php
/**
 * Template to display social share buttons.
 * @since 1.0.0
 */

if ( Themify_Social_Share::is_enabled( 'single' ) || Themify_Social_Share::is_enabled( 'archive' ) ) :
?>
<?php $networks = Themify_Social_Share::get_active_networks();?>
<?php if(!empty($networks)):?>
<div class="share-wrap">
	<a class="share-button" href="javascript:void(0);"></a>
	<div class="<?php echo esc_attr( 'social-share msss' . get_the_ID() ); ?>">
		<?php foreach($networks as $k=>$n):?>
			<div class="<?php echo strtolower($k)?>-share">
				<a onclick="window.open('<?php echo Themify_Social_Share::get_network_url($k)?>','<?php echo $k?>','<?php echo Themify_Social_Share::get_window_params($k)?>')" title="<?php esc_attr_e($n)?>" rel="nofollow" href="javascript:void(0);" class="share"></a>
			</div>
		<?php endforeach;?>
	</div>
</div>
<?php endif;?>

<!-- .post-share -->

<?php endif; // social share enabled in archive or single ?>