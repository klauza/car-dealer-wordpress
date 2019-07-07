/*
 * Themify Mega Menu Plugin
 */
;(function ($) {
	$.fn.ThemifyMegaMenu = function( custom ) {

		var options = $.extend({
				events: 'mouseenter'
			}, custom),
			cacheMenu = {};

		return this.each(function() {
			var $thisMega = $(this),
				$megaMenuPosts = $('.mega-menu-posts', $thisMega);

			$thisMega.on(options.events+' touchend', '.mega-link', function(event) {

				if ( $(window).width() < tf_mobile_menu_trigger_point ) {
					return;
				}

				event.preventDefault();
				var $self = $(this),
					termid = $self.data('termid'),
					tax = $self.data('tax');

				if( 'string' == typeof cacheMenu[termid] ) {
					$megaMenuPosts.html( cacheMenu[termid] );
				} else {
					if( $self.hasClass( 'loading' ) ) {
						return;
					}
					$self.addClass( 'loading' );
					$.post(
						themifyScript.ajax_url,
						{
							action: 'themify_theme_mega_posts',
							termid: termid,
							tax: tax
						},
						function( response ) {
							$megaMenuPosts.html( response );
							cacheMenu[termid] = response;
							$self.removeClass( 'loading' );
						}
					);
				}
			});

			// when hovering over top-level mega menu items, show the first one automatically
			$thisMega.on( 'mouseenter', '> a', function(){
				$( this ).closest( 'li' ).find( '.mega-sub-menu .mega-link:first' ).trigger( options.events );
			} )
			.on( 'dropdown_open', function(){
				$( this ).find( '.mega-sub-menu .mega-link:first' ).trigger( options.events );
			});

		});
	};

	$(document).ready(function() {
		if( 'undefined' !== typeof $.fn.ThemifyMegaMenu ) {
			/* add required wrappers for mega menu items */
			$( '.has-mega-sub-menu' ).each(function(){
				var $this = $( this );

				$this.find( '> ul' ).removeAttr( 'class' )
					.wrap( '<div class="mega-sub-menu sub-menu" />' )
					.after( '<div class="mega-menu-posts" />' )
					.find( 'li.menu-item-type-taxonomy' ) // only taxonomy terms can display mega posts
						.addClass( 'mega-link' );
			});

			$('.has-mega-sub-menu').ThemifyMegaMenu({
				events: themifyScript.events
			});
		}
	});

})(jQuery);