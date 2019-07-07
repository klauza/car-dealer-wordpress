<?php $current_theme = wp_get_theme(); ?>

<div class="promote-themes"></div>

<script type="text/html" id="tmpl-themify-featured-theme-item">
<ol class="grid3 theme-list clearfix">
	<# jQuery.each( data, function( i, e ) { #>
	<# if ( data.currentThemeURI != e.url ) { #>
		<li class="theme-post post type-post status-publish format-standard hentry category-corporate-themes category-ecommerce-themes category-featured-themes category-portfolio-themes category-responsive-themes category-themes tag-featured-theme">
			<figure class="theme-image">
				<a href="{{{e.url}}}" target="_blank">
					<img src="https://themify.me/wp-content/product-img/{{{e.slug}}}-thumb.jpg" alt="{{{e.title}}}">
				</a>
			</figure>
			<div class="theme-info">
				<div class="theme-title">
					<h3><a href="{{{e.url}}}" target="_blank">{{{e.title}}}</a></h3>
					<a class="tag-button lightbox" target="_blank" href="https://themify.me/demo/themes/{{{e.slug}}}"><?php _e( 'demo', 'themify' ); ?></a>
				</div>
				<!-- /theme-title -->
				<div class="theme-excerpt">
					<p>{{{e.description}}}</p>
				</div>
				<!-- /theme-excerpt -->
			</div>
			<!-- /theme-info -->	
		</li>
	<# } #>
	<# } ) #>
</ol>
</script>

<script type="text/javascript">
	jQuery(function($) {
		var container = $('.promote-themes');
		$.getJSON( 'https://themify.me/public-api/featured-themes/index.json' )
		.done(function( data ){
			var template = wp.template( 'themify-featured-theme-item' );
			data.currentThemeURI = "<?php echo $current_theme->display( 'ThemeURI' ); ?>";

			container.append( template( data ) );
		}).fail(function( jqxhr, textStatus, error ){
			container.append( '<p><?php _e( 'Something went wrong while fetching the Featured Themes. Please try again later.', 'themify' ); ?></p>' );
		});
	}(jQuery));
</script>