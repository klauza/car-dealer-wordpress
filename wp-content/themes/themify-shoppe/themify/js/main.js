// tfsmartresize helper
!function (e) {
    var t, i, n = e.event;
    t = n.special.tfsmartresize = {setup: function () {
            e(this).on('resize', t.handler);
        }, teardown: function () {
            e(this).off('resize', t.handler);
        }, handler: function (e, r) {
            var s = this, a = arguments, o = function () {
                e.type = 'tfsmartresize', n.dispatch.apply(s, a);
            };
            i && clearTimeout(i), r ? o() : i = setTimeout(o, t.threshold);
        }, threshold: 150};
}(jQuery);
var Themify, ThemifyGallery;
(function ($, window, document, undefined) {
    'use strict';
    window.addEventListener( 'load', function () {
        window.loaded = true;
        if (!Themify.is_builder_active) {
            Themify.triggerEvent( window, 'resize' );
        }
        $( 'body' ).addClass( 'page-loaded' );
    });
    Themify = {
        fonts: [],
        cssLazy: [],
        jsLazy: [],
        body:null,
        is_builder_active: false,
        is_builder_loaded:false,
        triggerEvent: function (a, b) {
            var c;
            document.createEvent ? (c = document.createEvent('HTMLEvents'), c.initEvent(b, !0, !0)) : document.createEventObject && (c = document.createEventObject(), c.eventType = b), c.eventName = b, a.dispatchEvent ? a.dispatchEvent(c) : a.fireEvent && htmlEvents["on" + b] ? a.fireEvent("on" + c.eventType, c) : a[b] ? a[b]() : a["on" + b] && a["on" + b]()
        },
        UpdateQueryString : function ( a, b, c ) {
                c||(c=window.location.href);var d=RegExp("([?|&])"+a+"=.*?(&|#|$)(.*)","gi");if(d.test(c))return b!==void 0&&null!==b?c.replace(d,"$1"+a+"="+b+"$2$3"):c.replace(d,"$1$3").replace(/(&|\?)$/,"");if(b!==void 0&&null!==b){var e=-1!==c.indexOf("?")?"&":"?",f=c.split("#");return c=f[0]+e+a+"="+b,f[1]&&(c+="#"+f[1]),c}return c;
        },
        Init: function () {
            Themify.body = $('body');//cache body, main.js is loading in the footer
            if (typeof themify_vars !== 'undefined') {
                if (typeof tbLocalScript !== 'undefined' && tbLocalScript !== null) {
                    var self = Themify;
                    $(document).ready(function () {
                        self.is_builder_active =  document.body.classList.contains('themify_builder_active');
                        tbLocalScript.isTouch =  document.body.classList.contains('touch');
                        if (!self.is_builder_active && $('.themify_builder_content div:not(.js-turn-on-builder)').length > 0) {
                            self.LoadAsync(tbLocalScript.builder_url + '/js/themify.builder.script.js');
                        }
                    });
                }
                this.bindEvents();
            }
        },
        bindEvents: function () {
            var $self = Themify;
            if (window.loaded) {
                $self.domready();
                $self.windowload();
            }
            else {
                $(window).load($self.windowload);
                $(document).ready($self.domready);
            }
        },
        domready: function () {
            Themify.LazyLoad();
            function callback(el, type) {
                var slug = type === 'module' && themifybuilderapp.activeModel !== null ? themifybuilderapp.activeModel.get('mod_name') : false;
                Themify.addonLoad(el, slug);
                Themify.InitCarousel(el);
                Themify.InitMap(el);
            }
            if (!Themify.is_builder_active) {
                callback();
            }
            else {
                Themify.body.on('builder_load_module_partial', function (e, el, type) {
                    callback(el, type);
                    Themify.InitGallery(el);
                });
            }
        },
        windowload: function () {
            $('.shortcode.slider, .shortcode.post-slider, .slideshow-wrap').css({'height': 'auto', 'visibility': 'visible'});
            Themify.InitGallery();
        },
        LazyLoad: function () {
            var self = Themify,
                    is_fontawesome = self.is_builder_active || $('.fa').length > 0,
                    is_themify_icons = self.is_builder_active || $('span[class*="ti-"], i[class*="ti-"], .module-menu[data-menu-breakpoint]').length > 0;
            if (!is_fontawesome) {
                is_fontawesome = self.checkFont('FontAwesome');
            }
            if (!is_themify_icons) {
                is_themify_icons = self.checkFont('Themify');
            }

            if (is_fontawesome) {
                self.LoadCss(themify_vars.url + '/fontawesome/css/font-awesome.min.css', themify_vars.version);
            }
            if (is_themify_icons) {
                self.LoadCss(themify_vars.url + '/themify-icons/themify-icons.min.css', themify_vars.version);
            }
            if ($('i[class*="icon-"]').length > 0 && typeof themify_vars.fontello_path === 'string') {
                self.LoadCss(themify_vars.fontello_path);
            }
            if (self.is_builder_active || $('.shortcode').length > 0) {
                self.LoadCss(themify_vars.url + '/css/themify.framework.css', null, $('#themify-framework-css')[0]);
            }
        },
        addonLoad: function (el, slug) {
            /*Load addons css/js,we don't need to wait the loading of builder*/
            if (typeof tbLocalScript !== 'undefined' && tbLocalScript && Object.keys(tbLocalScript.addons).length > 0) {
                var self = Themify,
                    addons = slug && tbLocalScript.addons[slug] !== undefined ? [tbLocalScript.addons[slug]] : tbLocalScript.addons;
                for (var i in addons) {
                    if ($(addons[i].selector).length > 0) {
                        if (addons[i].css) {
                            self.LoadCss(addons[i].css, addons[i].ver);
                        }
                        if (addons[i].js) {
                            if (addons[i].external) {
                                var s = document.createElement('script');
                                s.type = 'text/javascript';
                                s.text = addons[i].external;
                                var t = document.getElementsByTagName('script')[0];
                                t.parentNode.insertBefore(s, t);
                            }
                            self.LoadAsync(addons[i].js, null, addons[i].ver);
                        }
                        delete tbLocalScript.addons[i];
                        if (el) {
                            break;
                        }
                    }
                }
            }
        },
        InitCarousel: function (el) {
            
            var sliders = $('.slides[data-slider]', el);
            function carouselCalback(el) {
                sliders.each(function () {
                    if($(this).closest('.carousel-ready').length>0){
                        return true;
                    }
                    $(this).find('> br, > p').remove();
                    var $this = $(this),
						$data = JSON.parse( atob( $(this).data('slider') ) ),
						height = (typeof $data.height === 'undefined') ? 'auto' : $data.height,
						$numsldr = $data.numsldr,
						$slideContainer = undefined !== $data.custom_numsldr ? '#' + $data.custom_numsldr : '#slider-' + $numsldr,
						$speed = $data.speed >= 1000 ? $data.speed : 1000 * $data.speed,
						$args = {
							responsive: true,
							swipe: true,
							circular: $data.wrapvar,
							infinite: $data.wrapvar,
							auto: {
								play: $data.auto == 0 ? false : true,
								timeoutDuration: $data.auto >= 1000 ? $data.auto : 1000 * $data.auto,
								duration: $speed,
								pauseOnHover: $data.pause_hover
							},
							scroll: {
								items: parseInt($data.scroll),
								duration: $speed,
								fx: $data.effect
							},
							items: {
								visible: {
									min: 1,
									max: parseInt($data.visible)
								},
								width: 120,
								height: height
							},
							onCreate: function (items) {
								$this.closest('.caroufredsel_wrapper').outerHeight($this.outerHeight(true));
								$($slideContainer).css({'visibility': 'visible', 'height': 'auto'});
								$this.closest( '.carousel-wrap' ).addClass( 'carousel-ready' );
							}
						};

                    if ($data.slider_nav) {
                        $args.prev = $slideContainer + ' .carousel-prev';
                        $args.next = $slideContainer + ' .carousel-next';
                    }
                    if ($data.pager) {
                        $args.pagination = $slideContainer + ' .carousel-pager';
                    }
                    $this.imagesLoaded().always(function () {
                        $this.carouFredSel($args);
                    });
                });

                $(window).off('tfsmartresize.tfcarousel').on('tfsmartresize.tfcarousel', function () {
                    sliders.each(function () {
                        var heights = [],
                                newHeight,
                                $self = $(this);
                        $self.find('li').each(function () {
                            heights.push($(this).outerHeight(true));
                        });
                        newHeight = Math.max.apply(Math, heights);
                        $self.outerHeight(newHeight);
                        $self.parent().outerHeight(newHeight);
                    });
                });
            }
            if (sliders.length > 0) {
                var $self = this;
                $self.LoadAsync(themify_vars.includesURL + 'js/imagesloaded.min.js', function () {
                    if ('undefined' === typeof $.fn.carouFredSel) {
                        $self.LoadAsync(themify_vars.url + '/js/carousel.min.js', function () {
                            carouselCalback(el);
                        }, null, null, function () {
                            return ('undefined' !== typeof $.fn.carouFredSel);
                        });
                    }
                    else {
                        carouselCalback(el);
                    }
                }, null, null, function () {
                    return ('undefined' !== typeof $.fn.imagesLoaded);
                });
            }
        },
        InitMap: function (el) {
            var self = Themify;
            if ($('.themify_map', el).length > 0) {
                if (typeof google !== 'object' || typeof google.maps !== 'object') {
                    if (!themify_vars.map_key) {
                        themify_vars.map_key = '';
                    }
                    self.LoadAsync('//maps.googleapis.com/maps/api/js', self.MapCallback,'v=3.exp&callback=Themify.MapCallback&key=' + themify_vars.map_key,false,function(){
                        return typeof google === 'object' && typeof google.maps === 'object';
                    });
                } else {
                    if (themify_vars.isCached && themify_vars.isCached === 'enable') {
                        google.maps = {__gjsload__: function () {
                                return;
                            }};
                        self.LoadAsync('//maps.googleapis.com/maps/api/js', self.MapCallback, 'v=3.exp&callback=Themify.MapCallback&key=' + themify_vars.map_key, false,function(){
							return typeof google === 'object' && typeof google.maps === 'object';
						});
                    } else {
                        self.MapCallback(el);
                    }
                }
            }
        },
        MapCallback: function (el) {
            $('.themify_map', el).each(function ($i) {
                var $this = $( this ),
					address = $this.data( 'address' ),
					zoom = parseInt( $this.data( 'zoom' ) ),
					type = $this.data( 'type' ),
					scroll = $this.data( 'scroll' ) == 'true',
					drag = $this.data( 'drag' ) == 'true',
					delay = $i * 1000;
                setTimeout(function () {
                    var geo = new google.maps.Geocoder(),
                            latlng = new google.maps.LatLng(-34.397, 150.644),
                            mapOptions = {
                                zoom: zoom,
                                center: latlng,
                                mapTypeId: google.maps.MapTypeId.ROADMAP,
                                scrollwheel: scroll,
                                draggable: drag
                            };
                    switch (type.toUpperCase()) {
                        case 'ROADMAP':
                            mapOptions.mapTypeId = google.maps.MapTypeId.ROADMAP;
                            break;
                        case 'SATELLITE':
                            mapOptions.mapTypeId = google.maps.MapTypeId.SATELLITE;
                            break;
                        case 'HYBRID':
                            mapOptions.mapTypeId = google.maps.MapTypeId.HYBRID;
                            break;
                        case 'TERRAIN':
                            mapOptions.mapTypeId = google.maps.MapTypeId.TERRAIN;
                            break;
                    }

                    var map = new google.maps.Map( $this[0], mapOptions ),
						revGeocoding = $this.data( 'reverse-geocoding' ) ? true : false;

                    google.maps.event.addListenerOnce(map, 'idle', function () {
                        Themify.body.trigger('themify_map_loaded', [$this, map]);
                    });

                    /* store a copy of the map object in the dom node, for future reference */
                    $this.data('gmap_object', map);

                    if (revGeocoding) {
                        var latlngStr = address.split(',', 2),
                                lat = parseFloat(latlngStr[0]),
                                lng = parseFloat(latlngStr[1]),
                                geolatlng = new google.maps.LatLng(lat, lng),
                                geoParams = {'latLng': geolatlng};
                    } else {
                        var geoParams = {'address': address};
                    }

                    geo.geocode(geoParams, function (results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            var position = revGeocoding ? geolatlng : results[0].geometry.location;
                            map.setCenter(position);
                            var marker = new google.maps.Marker({
									map: map,
									position: position
								}),
								info = $this.data('info-window');
                            if (undefined !== info) {
                                var contentString = '<div class="themify_builder_map_info_window">' + info + '</div>',
                                        infowindow = new google.maps.InfoWindow({
                                            content: contentString
                                        });

                                google.maps.event.addListener(marker, 'click', function () {
                                    infowindow.open(map, marker);
                                });
                            }
                        }
                    });
                }, delay);
            });
        },
        LoadAsync: function (src, callback, version, defer, test) {
            var id = this.hash(src), // Make script path as ID
                exist = this.jsLazy.indexOf(id) !== -1,
                existElemens = exist || document.getElementById(id);
                if(!exist){ 
                    this.jsLazy.push(id);
                }
            if (existElemens) {
                if (callback) {
                    if (test) {
                        var callbackTimer = setInterval(function () {
                            var call = false;
                            try {
                                call = test.call();
                            } catch (e) {
                            }
                            if (call) {
                                clearInterval(callbackTimer);
                                callback.call();
                            }
                        }, 20);
                    } else {
                        callback();
                    }
                }
                return;
            }
            else if (test) {
                try {
                    if (test.call()) {
                        if (callback) {
                            callback.call();
                        }
                        return;
                    }
                } catch (e) {
                }
            }
            if (src.indexOf('.min.js') === -1 && typeof themify_vars!=='undefined') {
                var name = src.match(/([^\/]+)(?=\.\w+$)/);
                if (name && name[0]) {
                    name = name[0];
                    if (themify_vars.minify.js[name]) {
                        src = src.replace(name + '.js', name + '.min.js');
                    }
                }
            }
            var s, r, t;
            r = false;
            s = document.createElement('script');
            s.type = 'text/javascript';
            s.id = id;
            if(!version && version!==false && 'undefined' !== typeof tbLocalScript ){
                    version = tbLocalScript.version;
            }
            s.src = version? src + '?ver=' + version : src;
            s.async = true;
            s.onload = s.onreadystatechange = function () {
                if (!r && (!this.readyState || this.readyState === 'complete'))
                {
                    r = true;
                    if (callback) {
                        callback();
                    }
                }
            };
            t = document.getElementsByTagName('script')[0];
            t.parentNode.insertBefore(s, t);
        },
        LoadCss: function (href, version, before, media, callback) {
			if ( typeof href === 'undefined' ) return;
			
            if(!version && version!==false && 'undefined' !== typeof tbLocalScript ){
                    version = tbLocalScript.version;
            }
            var id = this.hash(href),
                exist = this.cssLazy.indexOf(id)  !== -1,
                existElemens =exist || document.getElementById(id),
                fullHref =  version? href + '?ver=' + version : href; 
            if(!exist){
                this.cssLazy.push(id);
            }
            if (existElemens || $("link[href='" + fullHref + "']").length > 0) {
                if(callback){
                    callback();
                }
                return;
            }
            if (href.indexOf('.min.css') === -1 && typeof themify_vars!=='undefined') {
                var name = href.match(/([^\/]+)(?=\.\w+$)/);
                if (name && name[0]) {
                    name = name[0];
                    if (themify_vars.minify.css[name]) {
                        fullHref = fullHref.replace(name + '.css', name + '.min.css');
                    }
                }
            }
            var doc = window.document,
                    ss = doc.createElement('link'),
                    ref;
            if (before) {
                ref = before;
            }
            else {
                var refs = (doc.body || doc.head).childNodes;
                ref = refs[ refs.length - 1];
            }

            var sheets = doc.styleSheets;
            ss.rel = 'stylesheet';
            ss.href = fullHref;
            // temporarily set media to something inapplicable to ensure it'll fetch without blocking render
            ss.media = 'only x';
            ss.async = 'async';
            ss.id = id;

            // Inject link
            // Note: `insertBefore` is used instead of `appendChild`, for safety re: http://www.paulirish.com/2011/surefire-dom-element-insertion/
            ref.parentNode.insertBefore(ss, (before ? ref : ref.nextSibling));
            // A method (exposed on return object for external use) that mimics onload by polling document.styleSheets until it includes the new sheet.
            var onloadcssdefined = function (cb) {
                var resolvedHref = ss.href,
                    i = sheets.length;
                while (i--) {
                    if (sheets[ i ].href === resolvedHref) {
                        if (callback) {
                            callback();
                        }
                        return cb();
                    }
                }
                setTimeout(function () {
                    onloadcssdefined(cb);
                });
            };

            // once loaded, set link's media back to `all` so that the stylesheet applies once it loads
            ss.onloadcssdefined = onloadcssdefined;
            onloadcssdefined(function () {
                ss.media = media || 'all';
            });
            return ss;
        },
        checkFont: function (font) {
            // Maakt een lijst met de css van alle @font-face items.
            if ($.inArray(font, this.fonts)) {
                return true;
            }
            if (this.fonts.length === 0) {
                var o = [],
                        sheets = document.styleSheets,
                        rules = null,
                        i = sheets.length, j;
                while (0 <= --i) {
                    rules = sheets[i].cssRules || sheets[i].rules || [];
                    j = rules.length;

                    while (0 <= --j) {
                        if (rules[j].style) {
                            var fontFamily = '';
                            if (rules[j].style.fontFamily) {
                                fontFamily = rules[j].style.fontFamily;
                            }
                            else {
                                fontFamily = rules[j].style.cssText.match(/font-family\s*:\s*([^;\}]*)\s*[;}]/i);
                                if (fontFamily) {
                                    fontFamily = fontFamily[1];
                                }
                            }
                            if (fontFamily === font) {
                                return true;
                            }
                            if (fontFamily) {
                                o.push(fontFamily);
                            }
                        }
                    }
                }
                this.fonts = $.unique(o);
            }
            return $.inArray(font, this.fonts);
        },
        lightboxCallback: function ($el, $args) {
            this.LoadAsync(themify_vars.url + '/js/themify.gallery.js', function () {
                Themify.GalleryCallBack($el, $args);
            }, null, null, function () {
                return ('undefined' !== typeof ThemifyGallery);
            });
        },
        InitGallery: function( $el, $args ) {
			var self = this,
				lightboxConditions = false,
				lbox = typeof themifyScript === 'object' && themifyScript.lightbox;

			if( ! Themify.is_builder_active ) {
				lightboxConditions = lbox && ( ( lbox.lightboxContentImages
					&& $( lbox.contentImagesAreas ).length ) || $( lbox.lightboxSelector ).length );
				
				if( ! lightboxConditions ) {
					lightboxConditions = lbox && lbox.lightboxGalleryOn
						&& ( $( lbox.lightboxContentImagesSelector ).length
						|| ( lbox.gallerySelector && $( lbox.gallerySelector ).length ) );
				}

				if ( lightboxConditions ) {
					this.LoadCss( themify_vars.url + '/css/lightbox.min.css', null );
					this.LoadAsync( themify_vars.url + '/js/lightbox.min.js', function () {
						Themify.lightboxCallback( $el, $args );
					}, null, null, function () {
						return ( 'undefined' !== typeof $.fn.magnificPopup );
					});
				}
			}

			if( ! lightboxConditions ) {
				self.body.addClass( 'themify_lightbox_loaded' ).removeClass( 'themify_lightboxed_images' );
			}
		},
        GalleryCallBack: function ($el, $args) {
            if (!$el) {
                $el = $(themifyScript.lightboxContext);
            }
            $args = !$args && themifyScript.extraLightboxArgs ? themifyScript.extraLightboxArgs : {};
            ThemifyGallery.init({'context': $el, 'extraLightboxArgs': $args});
            Themify.body.addClass('themify_lightbox_loaded').removeClass('themify_lightboxed_images');
        },
        parseVideo: function (url) {
                var m = url.match(/(http:|https:|)\/\/(player.|www.)?(vimeo\.com|youtu(be\.com|\.be|be\.googleapis\.com))\/(video\/|embed\/|watch\?v=|v\/)?([A-Za-z0-9._%-]*)(\&\S+)?/i);
                return {
                        type: m!==null?(m[3].indexOf('youtu') > -1?'youtube':(m[3].indexOf('vimeo') > -1?'vimeo':false)):false,
                        id: m!==null?m[6]:false
                };
        },
        hash: function (str) {
            var hash = 0;
            for (var i = 0, len = str.length; i < len; ++i) {
                hash = ((hash << 5) - hash) + str.charCodeAt(i);
                hash = hash & hash; // Convert to 32bit integer
            }
            return hash;
        },
        getVendorPrefix:function () {
            if (this.vendor === undefined) {
                var e = document.createElement('div'),
                        prefixes = ['Moz', 'Webkit', 'O', 'ms'];
                for (var i=0,len=prefixes.length;i<len;++i) {
                        if (typeof e.style[prefixes[i] + 'Transform'] !== 'undefined') {
                                this.vendor = prefixes[i].toLowerCase();
                                break;
                        }
                }
                e = null;
                this.vendor = '-'+this.vendor+'-';
            }
            return this.vendor;
        }
    };

    Themify.Init();

}(jQuery, window, document));
