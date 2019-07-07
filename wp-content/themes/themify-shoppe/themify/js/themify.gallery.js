// Themify Lightbox and Fullscreen /////////////////////////
var ThemifyGallery = {};

(function($){

	'use strict';

ThemifyGallery = {
	
	config: {
		fullscreen: themifyScript.lightbox.fullscreenSelector,
		lightbox: themifyScript.lightbox.lightboxSelector,
		lightboxGallery: themifyScript.lightbox.gallerySelector,
		lightboxContentImages: themifyScript.lightbox.lightboxContentImagesSelector,
		is_touch:$('body').hasClass('touch'),
		context: document
	},
	
	init: function(config){
		if (config && typeof config == 'object') {
			$.extend(ThemifyGallery.config, config);
		}
		if (config.extraLightboxArgs && typeof config == 'object') {
			for (var attrname in config.extraLightboxArgs) {
				themifyScript.lightbox[attrname] = config.extraLightboxArgs[attrname];
			}
		}
		this.parseArgs();
		this.doLightbox();
	},
	parseArgs: function(){
		$.each(themifyScript.lightbox, function(index, value){
			if( 'false' == value || 'true' == value ){
				themifyScript.lightbox[index] = 'false'!=value;
			} else if( parseInt(value) ){
				themifyScript.lightbox[index] = parseInt(value);
			} else if( parseFloat(value) ){
				themifyScript.lightbox[index] = parseFloat(value);
			}
		});
	},
	
	doLightbox: function(){
		var context = this.config.context,
			patterns = {};
		
		if(typeof $.fn.magnificPopup !== 'undefined' && typeof themifyScript.lightbox.lightboxOn !== 'undefined') {
			
			// Lightbox Link
			$(context).on('click', ThemifyGallery.config.lightbox, function(event){
				event.preventDefault();
				if ( $('.mfp-wrap.mfp-gallery').length ) return;

				var $self = $(this),
					targetItems,
					$link = ( $self.find( '> a' ).length > 0 ) ? $self.find( '> a' ).attr( 'href' ) : $self.attr('href'),
					$type = ThemifyGallery.getFileType($link),
					$is_video = ThemifyGallery.isVideo($link),
					$groupItems = $type === 'inline' || $type === 'iframe' ? [] : ($self.data('rel')?$('a[data-rel="'+$self.data('rel')+'"]'):$self.closest( '.themify_builder_row, .loops-wrapper' ).find( '.themify_lightbox > img' ).parent()),
					index = $groupItems.length > 1 ? $groupItems.index( this ) : 0,
					$title = (typeof $(this).children('img').prop('alt') !== 'undefined') ? $(this).children('img').prop('alt') : $(this).prop('title'),
					$iframe_width = $is_video ? '100%' : (ThemifyGallery.getParam('width', $link)) ? ThemifyGallery.getParam('width', $link) : '94%',
					$iframe_height = $is_video ? '100%' : (ThemifyGallery.getParam('height', $link)) ? ThemifyGallery.getParam('height', $link) : '100%';
				if($iframe_width.indexOf("%") === -1) $iframe_width += 'px';
				if($iframe_height.indexOf("%") === -1) $iframe_height += 'px';

				if($is_video){
					if( ThemifyGallery.isYoutube( $link ) ) {
						var params = ThemifyGallery.getCustomParams( $link );

						// YouTube URL pattern
						if( params ) {
							patterns.youtube = {
								id: 'v=',
								index: 'youtube.com/',
								src: '//www.youtube.com/embed/%id%' + params
							};
						}

						// YouTube sanitize the URL properly
						$link = ThemifyGallery.getYoutubePath( $link );
					} else if( ThemifyGallery.isVimeo( $link ) ) {
						var params = ThemifyGallery.getCustomParams( $link );

						// Vimeo URL pattern
						if( params ) {
							patterns.vimeo = {
								id: '/',
								index: 'vimeo.com/',
								src: '//player.vimeo.com/video/%id%' + params
							};
						}

						$link = $link.split('?')[0];
					}
				}
				if( $groupItems.length > 1 && index !== -1 ) {
					targetItems = [];

					$groupItems.each( function( i, el ) {
						targetItems.push( {
							src: ThemifyGallery.getiFrameLink( $(el).prop( 'href' ) ),
							title: (typeof $(el).find('img').prop('alt') !== 'undefined') ? $(el).find('img').prop('alt') : '',
							type: ThemifyGallery.getFileType( $(el).prop( 'href' ) )
						} );
					} );

				} else {
					index = 0; // ensure index is set to 0 so the proper popup shows
					targetItems = {
						src: ThemifyGallery.getiFrameLink( $link ),
						title: $title,
					};
				}

				var iOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream,
					iOSScrolling = iOS ? 'scrolling="no" ' : '';

				var $args = {
					items: targetItems,
					type: $type,
					iframe: {
						markup: '<div class="mfp-iframe-scaler" style="max-width: '+$iframe_width+' !important; height: '+$iframe_height+';">'+
						'<div class="mfp-close"></div>'+
						'<div class="mfp-iframe-wrapper">'+
						'<iframe class="mfp-iframe" '+ iOSScrolling +'noresize="noresize" frameborder="0" allowfullscreen></iframe>'+
						'</div>'+
						'</div>',
						patterns: patterns
					},
					callbacks: {
						open: function() {
							var zoomConfig = $self.data( 'zoom-config' ),
								cssRules = {};
							if( !zoomConfig ) { return; }
							zoomConfig = zoomConfig.split( '|' );

							if( typeof zoomConfig[0] !== 'undefined' ) {
								cssRules.width = zoomConfig[0];
							}

							if( typeof zoomConfig[1] !== 'undefined' ) {
								cssRules.height = zoomConfig[1];
							}
							
							$(this.content).parent().css( cssRules );
						}
					}
				};

				if( $groupItems.length > 1 ) {
					$.extend( $args, {
						gallery: {
							enabled: true
						}
					} );
				}

				if($self.find('img').length > 0) {
					$.extend( $args, {
						mainClass: 'mfp-with-zoom',
						zoom: {
							enabled: !ThemifyGallery.config.is_touch,
							duration: 300,
							easing: 'ease-in-out',
							opener: function() {
								return $self.find('img');
							}
						}
					});
				}

				if($is_video){
					$args['mainClass'] += ' video-frame';
				} else {
					$args['mainClass'] += ' standard-frame';
				}
				if(ThemifyGallery.isInIframe()) {
					window.parent.jQuery.magnificPopup.open($args);
				} else {
					$.magnificPopup.open($args, index);
				}
			});
			
			// Images in post content
			$(themifyScript.lightbox.contentImagesAreas, context).each(function() {
				var images = [],
					links = [];
				if(themifyScript.lightbox.lightboxContentImages && themifyScript.lightbox.lightboxGalleryOn){
					$(ThemifyGallery.config.lightboxContentImages, $(this)).filter( function(){
						if(!$(this).parent().hasClass('gallery-icon') && !$(this).hasClass('themify_lightbox')){
							links.push($(this));
							var description = $(this).prop('title');
							if($(this).next('.wp-caption-text').length > 0){
								// If there's a caption set for the image, use it
								description = $(this).next('.wp-caption-text').html();
							} else {
								// Otherwise, see if there's an alt attribute set
								description = $(this).children('img').prop('alt');
							}
							images.push({ src: $(this).prop('href'), title: description, type: 'image' });
							return $(this);
						}
					}).each(function(index) {
						if (links.length > 0) {
							$(this).on('click', function(event){
								event.preventDefault();
								var $self = $(this);
								var $args = {
									items: {
										src: images[index].src,
										title: images[index].title
									},
									type: 'image'
								};
								if($self.find('img').length > 0) {
									$.extend( $args, {
										mainClass: 'mfp-with-zoom',
										zoom: {
											enabled: !ThemifyGallery.config.is_touch,
											duration: 300,
											easing: 'ease-in-out',
											opener: function() {
												return $self.find('img');
											}
										}
									});
								}
								if(ThemifyGallery.isInIframe()) {
									window.parent.jQuery.magnificPopup.open($args);
								} else {
									$.magnificPopup.open($args);
								}
							});
						}
					});
				}
			});
			
			// Images in WP Gallery
			if(themifyScript.lightbox.lightboxGalleryOn){
				$('body').on('click', ThemifyGallery.config.lightboxGallery, function(event){
					if( 'image' !== ThemifyGallery.getFileType( $(this).prop( 'href' ) ) ) {
						return;
					}
					event.preventDefault();
                    var $gallery = $( ThemifyGallery.config.lightboxGallery, $( this ).closest( '.module, .gallery' ) ),
						images = [];
					$gallery.each(function() {
						var description = $(this).prop('title');
						if($(this).parent().next('.gallery-caption').length > 0){
							// If there's a caption set for the image, use it
							description = $(this).parent().next('.wp-caption-text').html();
						} else if ( $(this).children('img').length > 0 ) {
							// Otherwise, see if there's an alt attribute set
							description = $(this).children('img').prop('alt');
						} else if ( $(this).find('.gallery-caption').find('.entry-content').length > 0 ) {
							description = $(this).find('.gallery-caption').find('.entry-content').text();
						}
						images.push({ src: $(this).prop('href'), title: description, type: 'image' });
					});
					var $args = {
						gallery: {
							enabled: true
						},
						items: images,
						mainClass: 'mfp-with-zoom',
						zoom: {
							enabled: !ThemifyGallery.config.is_touch,
							duration: 300,
							easing: 'ease-in-out',
							opener: function(openerElement) {
								var imageEl = $($gallery[openerElement.index]);
								return imageEl.is('img') ? imageEl : imageEl.find('img');
							}
						}
					};
					if(ThemifyGallery.isInIframe()){
						window.parent.jQuery.magnificPopup.open($args, $gallery.index(this));
					} else {
						$.magnificPopup.open($args, $gallery.index(this));
					}
				});
			}
		}
	},
	
	countItems : function(type){
		var context = this.config.context;
		if('lightbox' === type) return $(this.config.lightbox, context).length + $(this.config.lightboxGallery, context).length + $(ThemifyGallery.config.lightboxContentImages, context).length;
		else return $(this.config.fullscreen, context).length + $(ThemifyGallery.config.lightboxContentImages, context).length;
	},

	isInIframe: function(){
		if( typeof ThemifyGallery.config.extraLightboxArgs !== 'undefined' ) {
			return typeof ThemifyGallery.config.extraLightboxArgs.displayIframeContentsInParent !== 'undefined' && true == ThemifyGallery.config.extraLightboxArgs.displayIframeContentsInParent;
		} else {
			return false;
		}
	},
	
	getFileType: function( itemSrc ) {
		if ( itemSrc.match( /\.(gif|jpg|jpeg|tiff|png)(\?fit=\d+(,|%2C)\d+)?$/i ) ) { // ?fit is added by JetPack
			return 'image';
		} else if(itemSrc.match(/\bajax=true\b/i)) {
			return 'ajax';
		} else if(itemSrc.substr(0,1) === '#') {
			return 'inline';
		} else {
			return 'iframe';
		}
	},
	isVideo: function( itemSrc ) {
		return ThemifyGallery.isYoutube( itemSrc )
			|| ThemifyGallery.isVimeo(itemSrc) || itemSrc.match(/\b.mov\b/i)
			|| itemSrc.match(/\b.swf\b/i);
	},
	isYoutube : function( itemSrc ) {
		return itemSrc.match( /youtube\.com\/watch/i ) || itemSrc.match( /youtu\.be/i );
	},
	isVimeo : function( itemSrc ) {
		return itemSrc.match(/vimeo\.com/i)
	},
	getYoutubePath : function( url ) {
		if( url.match( /youtu\.be/i ) ) {
			// convert youtu.be/ urls to youtube.com
			return '//youtube.com/watch?v=' + url.match( /youtu\.be\/([^\?]*)/i )[1];
		} else {
			return '//youtube.com/watch?v=' + ThemifyGallery.getParam( 'v', url );
		}
	},
	/**
	 * Add ?iframe=true to the URL if the lightbox is showing external page
	 * this enables us to detect the page is in an iframe in the server
	 */
	getiFrameLink : function( link ) {
		if( ThemifyGallery.getFileType( link ) === 'iframe' && ThemifyGallery.isVideo( link ) === null ) {
			link = Themify.UpdateQueryString( 'iframe', 'true', link )
		}
		return link;
	},
	getParam: function( name, url ) {
		name = name.replace( /[\[]/, "\\\[" ).replace( /[\]]/, "\\\]" );
		var regexS = "[\\?&]" + name + "=([^&#]*)",
			regex = new RegExp( regexS ),
			results = regex.exec( url );
		return results == null ? "" : results[1];
	},
	getCustomParams: function( url ) {
		var params = url.split( '?' )[1];
		params = params ? '&' + params.replace( /[\\?&]?(v|autoplay)=[^&#]*/g, '' ).replace( /^&/g, '' ) : '';
		
		return '?autoplay=1' + params;
	}
};

}(jQuery));