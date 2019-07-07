<div class="wpcm-filter wpcm-filter-mileage">
	<label><?php _e( 'Max Mileage', 'wp-car-manager' ); ?></label>
	<select name="mileage_to" data-placeholder="<?php _e( 'All', 'wp-car-manager' ); ?>">
		<option value="0"><?php _e( 'All', 'wp-car-manager' ); ?></option>
		<?php foreach (
			apply_filters( 'wpcm_filter_mileage', array(
				20000,
                40000,
                60000,
                75000,
				100000,
                125000,
				150000,
				200000
			) ) as $mileage
		) : ?>
			<option value="<?php echo esc_attr( $mileage ); ?>"><?php echo Never5\WPCarManager\Helper\Format::mileage( $mileage ); ?></option>
		<?php endforeach; ?>
	</select>
</div>