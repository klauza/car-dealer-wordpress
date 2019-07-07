'use strict';
( function( $ ) {
	var p, e = $('#themify_news div.inside:visible').find('.widget-loading');
	if ( e.length ) {
		p = e.parent();
		setTimeout( function(){
			p.load( ajaxurl + '?action=themify_admin_widgets&widget=themify_news&pagenow=' + pagenow, '', function() {
				p.hide().slideDown('normal', function(){
					$(this).css('display', '');
				});
			});
		}, 0 );
	}

	// Themify ajax update
	function ThemifyWidgetUpdate( $buttons ) {
		this.$buttons = $buttons;
		this.plugin = null;
		this.slug = null;
		this.runEvents();
	}

	ThemifyWidgetUpdate.prototype = ( function() {
		return {
			runEvents: function() {
				var _this = this;

				if( _this.$buttons.length ) {
					this.$buttons.on( 'click', function( e ) {
						var $button = $( this ),
							$buttonParent = $button.closest( 'li' );
						e.preventDefault();

						$buttonParent.addClass( 'themify-ajax-active' );
						_this.getData( $button.attr( 'href' ) );
						_this.doUpdate( function() {
							$buttonParent.addClass( 'themify-ajax-success' );
							themifyAdminWidget && $buttonParent.attr( 'data-success-label', themifyAdminWidget.labels.successUpdate );
							_this.updateTransient();
						}, function() {
							$buttonParent.removeClass( 'themify-ajax-active' );
							themifyAdminWidget && $button.text( themifyAdminWidget.labels.errorUpdate );
						} );
					} );
				}
			},
			doUpdate: function( success, error ) {
				if( this.plugin && this.slug ) {
					wp.updates.ajax( 'update-plugin', {
						plugin: this.plugin,
						slug: this.slug,
						success: success,
						error: error
					} );

				}
			},
			getData: function( url ) {
				var data = this.getQueryParams( url );
				
				if( data.plugin ) {
					this.plugin = data.plugin;
					this.slug = data.plugin.split( '/' )[0];
				}
			},
			getQueryParams: function( url ) {
				var params = {},
					tokens,
					re = /[?&]?([^=]+)=([^&]*)/g;

				url = url.split('+').join(' ');

				while ( tokens = re.exec( url ) ) {
					params[decodeURIComponent( tokens[1] )] = decodeURIComponent( tokens[2] );
				}

				return params;
			},
			updateTransient() {
				$.ajax( {
					url: ajaxurl,
					type: 'POST',
					data: {
						action: 'themify_admin_widget_delete_transient',
						nonce: themifyAdminWidget.nonce
					},
				} );
			}
		};
		
	} )();

	new ThemifyWidgetUpdate( $( '.themify-update-ajax' ) );

} )( jQuery );