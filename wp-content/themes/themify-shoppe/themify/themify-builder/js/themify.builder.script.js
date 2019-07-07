// Sticky Plugin v1.0.4 for jQuery
// =============
// Author: Anthony Garand
// Improvements by German M. Bravo (Kronuz) and Ruud Kamphuis (ruudk)
// Improvements by Leonardo C. Daronco (daronco)
// Created: 02/14/2011
// Date: 07/20/2015
// Website: http://stickyjs.com/
// Description: Makes an element on the page stick on the screen as you scroll
//              It will only set the 'top' and 'position' of your element, you
//              might need to adjust the width in some cases.

(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['jquery'], factory);
    } else if (typeof module === 'object' && module.exports) {
        // Node/CommonJS
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals
        factory(jQuery);
    }
}(function ($) {
    var slice = Array.prototype.slice; // save ref to original slice()
    var splice = Array.prototype.splice; // save ref to original slice()

  var defaults = {
      topSpacing: 0,
      bottomSpacing: 0,
      className: 'is-sticky',
      wrapperClassName: 'sticky-wrapper',
      center: false,
      getWidthFrom: '',
      widthFromWrapper: true, // works only when .getWidthFrom is empty
      responsiveWidth: false
    },
    $window = $(window),
    $document = $(document),
    sticked = [],
    windowHeight = $window.height(),
    scroller = function() {
      var scrollTop = $window.scrollTop(),
        documentHeight = $document.height(),
        dwh = documentHeight - windowHeight,
        extra = (scrollTop > dwh) ? dwh - scrollTop : 0;

      for (var i = 0, l = sticked.length; i < l; i++) {
        var s = sticked[i],
          elementTop = s.stickyWrapper.offset().top,
          etse = elementTop - s.topSpacing - extra;

        //update height in case of dynamic content
        s.stickyWrapper.css('height', s.stickyElement.outerHeight());

        if (scrollTop <= etse) {
          if (s.currentTop !== null) {
            s.stickyElement
              .css({
                'width': '',
                'position': '',
                'top': ''
              });
            s.stickyElement.parent().removeClass(s.className);
            s.stickyElement.trigger('sticky-end', [s]);
            s.currentTop = null;
          }
        }
        else {
          var newTop = documentHeight - s.stickyElement.outerHeight()
            - s.topSpacing - s.bottomSpacing - scrollTop - extra;
          if (newTop < 0) {
            newTop = newTop + s.topSpacing;
          } else {
            newTop = s.topSpacing;
          }
          if (s.currentTop !== newTop) {
            var newWidth;
            if (s.getWidthFrom) {
                padding =  s.stickyElement.innerWidth() - s.stickyElement.width();
                newWidth = $(s.getWidthFrom).width() - padding || null;
            } else if (s.widthFromWrapper) {
                newWidth = s.stickyWrapper.width();
            }
            if (newWidth == null) {
                newWidth = s.stickyElement.width();
            }
            s.stickyElement
              .css('width', newWidth)
              .css('position', 'fixed')
              .css('top', newTop);

            s.stickyElement.parent().addClass(s.className);

            if (s.currentTop === null) {
              s.stickyElement.trigger('sticky-start', [s]);
            } else {
              // sticky is started but it have to be repositioned
              s.stickyElement.trigger('sticky-update', [s]);
            }

            if (s.currentTop === s.topSpacing && s.currentTop > newTop || s.currentTop === null && newTop < s.topSpacing) {
              // just reached bottom || just started to stick but bottom is already reached
              s.stickyElement.trigger('sticky-bottom-reached', [s]);
            } else if(s.currentTop !== null && newTop === s.topSpacing && s.currentTop < newTop) {
              // sticky is started && sticked at topSpacing && overflowing from top just finished
              s.stickyElement.trigger('sticky-bottom-unreached', [s]);
            }

            s.currentTop = newTop;
          }

          // Check if sticky has reached end of container and stop sticking
          var stickyWrapperContainer = s.stickyWrapper.parent();
          var unstick = (s.stickyElement.offset().top + s.stickyElement.outerHeight() >= stickyWrapperContainer.offset().top + stickyWrapperContainer.outerHeight()) && (s.stickyElement.offset().top <= s.topSpacing);

          if( unstick ) {
            s.stickyElement
              .css('position', 'absolute')
              .css('top', '')
              .css('bottom', 0);
          } else {
            s.stickyElement
              .css('position', 'fixed')
              .css('top', newTop)
              .css('bottom', '');
          }
        }
      }
    },
    resizer = function() {
      windowHeight = $window.height();

      for (var i = 0, l = sticked.length; i < l; i++) {
        var s = sticked[i];
        var newWidth = null;
        if (s.getWidthFrom) {
            if (s.responsiveWidth) {
                newWidth = $(s.getWidthFrom).width();
            }
        } else if(s.widthFromWrapper) {
            newWidth = s.stickyWrapper.width();
        }
        if (newWidth != null) {
            s.stickyElement.css('width', newWidth);
        }
      }
    },
    methods = {
      init: function(options) {
        return this.each(function() {
          var o = $.extend({}, defaults, options);
          var stickyElement = $(this);

          var stickyId = stickyElement.attr('id');
          var wrapperId = stickyId ? stickyId + '-' + defaults.wrapperClassName : defaults.wrapperClassName;
          var wrapper = $('<div></div>')
            .attr('id', wrapperId)
            .addClass(o.wrapperClassName);

          stickyElement.wrapAll(function() {
            if ($(this).parent("#" + wrapperId).length == 0) {
                    return wrapper;
            }
});

          var stickyWrapper = stickyElement.parent();

          if (o.center) {
            stickyWrapper.css({width:stickyElement.outerWidth(),marginLeft:"auto",marginRight:"auto"});
          }

          if (stickyElement.css("float") === "right") {
            stickyElement.css({"float":"none"}).parent().css({"float":"right"});
          }

          o.stickyElement = stickyElement;
          o.stickyWrapper = stickyWrapper;
          o.currentTop    = null;

          sticked.push(o);

          methods.setWrapperHeight(this);
          methods.setupChangeListeners(this);
        });
      },

      setWrapperHeight: function(stickyElement) {
        var element = $(stickyElement);
        var stickyWrapper = element.parent();
        if (stickyWrapper) {
          stickyWrapper.css('height', element.outerHeight());
        }
      },

      setupChangeListeners: function(stickyElement) {
        if (window.MutationObserver) {
          var mutationObserver = new window.MutationObserver(function(mutations) {
            if (mutations[0].addedNodes.length || mutations[0].removedNodes.length) {
              methods.setWrapperHeight(stickyElement);
            }
          });
          mutationObserver.observe(stickyElement, {subtree: true, childList: true});
        } else {
          if (window.addEventListener) {
            stickyElement.addEventListener('DOMNodeInserted', function() {
              methods.setWrapperHeight(stickyElement);
            }, false);
            stickyElement.addEventListener('DOMNodeRemoved', function() {
              methods.setWrapperHeight(stickyElement);
            }, false);
          } else if (window.attachEvent) {
            stickyElement.attachEvent('onDOMNodeInserted', function() {
              methods.setWrapperHeight(stickyElement);
            });
            stickyElement.attachEvent('onDOMNodeRemoved', function() {
              methods.setWrapperHeight(stickyElement);
            });
          }
        }
      },
      update: scroller,
      unstick: function(options) {
        return this.each(function() {
          var that = this;
          var unstickyElement = $(that);

          var removeIdx = -1;
          var i = sticked.length;
          while (i-- > 0) {
            if (sticked[i].stickyElement.get(0) === that) {
                splice.call(sticked,i,1);
                removeIdx = i;
            }
          }
          if(removeIdx !== -1) {
            unstickyElement.unwrap();
            unstickyElement
              .css({
                'width': '',
                'position': '',
                'top': '',
                'float': ''
              })
            ;
          }
        });
      }
    };

  // should be more efficient than using $window.scroll(scroller) and $window.resize(resizer):
  if (window.addEventListener) {
    window.addEventListener('scroll', scroller, false);
    window.addEventListener('resize', resizer, false);
  } else if (window.attachEvent) {
    window.attachEvent('onscroll', scroller);
    window.attachEvent('onresize', resizer);
  }

  $.fn.sticky = function(method) {
    if (methods[method]) {
      return methods[method].apply(this, slice.call(arguments, 1));
    } else if (typeof method === 'object' || !method ) {
      return methods.init.apply( this, arguments );
    } else {
      $.error('Method ' + method + ' does not exist on jQuery.sticky');
    }
  };

  $.fn.unstick = function(method) {
    if (methods[method]) {
      return methods[method].apply(this, slice.call(arguments, 1));
    } else if (typeof method === 'object' || !method ) {
      return methods.unstick.apply( this, arguments );
    } else {
      $.error('Method ' + method + ' does not exist on jQuery.sticky');
    }
  };
  $(function() {
    setTimeout(scroller, 0);
  });
}));

/**
 * Tabify
 */
;
(function ($) {

    'use strict';

    $.fn.tabify = function () {
        return this.each(function () {
            var tabs = $(this);
            if (!tabs.data('tabify')) {
                tabs.data('tabify', true);
                $('.tab-nav:first li', tabs).click(function () {  
                    $(this).addClass('current').attr('aria-expanded', 'true').siblings().removeClass('current').attr('aria-expanded', 'false');
                    $(this).closest('.module-tab').find('.tab-nav-current-active').text($(this).text());
                    var activeTab = $(this).find('a').attr('href');
                    $(activeTab,tabs).attr('aria-hidden', 'false').trigger('resize').siblings('.tab-content').attr('aria-hidden', 'true');
                    Themify.body.trigger('tb_tabs_switch', [activeTab, tabs]);
                    if(!Themify.is_builder_active){
                        Themify.triggerEvent(window, 'resize');
                    }
					$(this).closest('.module-tab').find('.tab-nav-current-active').click();
                    return false;
                }).first().addClass('current');
                $('.tab-nav:first', tabs).siblings('.tab-content').find('a[href^="#tab-"]').on('click', function (e) {
                  
                    e.preventDefault();
                    var dest = $(this).prop('hash').replace('#tab-', ''),
                        contentID = $('.tab-nav:first', tabs).siblings('.tab-content').eq(dest - 1).prop('id');
                    if ($('a[href^="#' + contentID + '"]').length > 0) {
                        $('a[href^="#' + contentID + '"]').trigger('click');
                    }
                });
                $('.tab-nav-current-active', tabs).click( function () {
                	var $this = $(this);
					if( $this.hasClass('clicked') ){
						$this.removeClass('clicked')
					}else{


						if ( ( $this.position().left > 0 ) && ( $this.position().left <= $this.closest('.module-tab').width() / 2 ) ) {
							$this.next('.tab-nav').removeClass('right-align').addClass('center-align');
						}else if(  $this.position().left > $this.closest('.module-tab').width() / 2  ){
							$this.next('.tab-nav').removeClass('center-align').addClass('right-align');
						}else{
							$this.next('.tab-nav').removeClass('center-align right-align')
						}
						$this.addClass('clicked');

					}
				});

            }
        });
    };

    // $('img.photo',this).themifyBuilderImagesLoaded(myFunction)
    // execute a callback when all images have loaded.
    // needed because .load() doesn't work on cached images
    if (!$.fn.themifyBuilderImagesLoaded) {
        $.fn.themifyBuilderImagesLoaded = function (callback) {
            var elems = this.filter('img'),
                    len = elems.length,
                    blank = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==";

            elems.bind('load.imgloaded', function () {
                if (--len <= 0 && this.src !== blank) {
                    elems.unbind('load.imgloaded');
                    callback.call(elems, this);
                }
            }).each(function () {
                // cached images don't fire load sometimes, so we reset src.
                if (this.complete || this.complete === undefined) {
                    var src = this.src;
                    // webkit hack from http://groups.google.com/group/jquery-dev/browse_thread/thread/eee6ab7b2da50e1f
                    // data uri bypasses webkit log warning (thx doug jones)
                    this.src = blank;
                    this.src = src;
                }
            });

            return this;
        };
    }
})(jQuery);

/*
 * Parallax Scrolling Builder
 */
(function ($, window) {

    'use strict';

    var $window = $(window),
            wH = null,
            is_mobile = false,
            isInitialized = false,
            className = 'builder-parallax-scrolling',
            defaults = {
                xpos: '50%',
                speedFactor: 0.1
            };
    function Plugin(element, options) {
        this.element = element;
        this.options = $.extend({}, defaults, options);
        this.init();
    }
    Plugin.prototype = {
        items: [],
        top: 0,
        index: 0,
        init: function () {
            this.top = this.element.offset().top;
            this.items.push(this);
            if (!isInitialized) {
                wH = $window.height();
                is_mobile = ThemifyBuilderModuleJs._isMobile();
                $window.on('tfsmartresize.builderParallax', this.resize.bind(this))
                        .on('scroll.builderParallax', function () {
                            for (var i in this.items) {
                                this.items[i].update(i);
                            }
                        }.bind(this));
                isInitialized = true;
            }
            this.update();
        },
        resize: function () {
            wH = $window.height();
            for (var i in this.items) {
                this.items[i].top = this.items[i].element.offset().top;
                this.items[i].update(i);
            }
        },
        destroy: function (index) {
            if (this.items[index] !== undefined) {
                this.items.splice(index, 1);
                if (this.items.length === 0) {
                    $window.off('scroll.builderParallax').off('tfsmartresize.builderParallax');
                    isInitialized = false;
                }
            }
        },
        update: function (i) {
            if (document.body.contains(this.element[0]) === false || this.element[0].className.indexOf(className) === -1) {
                this.destroy(i);
                return;
            }
            var pos = $window.scrollTop(),
                    top = this.element.offset().top,
                    outerHeight = this.element.outerHeight(true);
            // Check if totally above or totally below viewport
            if ((top + outerHeight) < pos || top > (pos + wH)) {
                return;
            }
            if (is_mobile) {
                /* #3699 = for mobile devices increase background-size-y in 30% (minimum 400px) and decrease background-position-y in 15% (minimum 200px) */
                var outerWidth = this.element.outerWidth(true),
                        dynamicDifference = outerHeight > outerWidth ? outerHeight : outerWidth;
                dynamicDifference = Math.round(dynamicDifference * 0.15);
                if (dynamicDifference < 200) {
                    dynamicDifference = 200;
                }
                this.element.css({
                    backgroundSize: 'auto ' + Math.round(outerHeight + (dynamicDifference * 2)) + 'px',
                    'background-position-y': Math.round(((this.top - pos) * this.options.speedFactor) - dynamicDifference) + 'px'
                });
            }
            else {
                this.element.css('background-position-y', Math.round((this.top - pos) * this.options.speedFactor) + 'px');
            }
        }
    };
    $.fn['builderParallax'] = function (options) {
        return this.each(function () {
            $.data(this, 'plugin_builderParallax', new Plugin($(this), options));

        });
    };
})(jQuery, window);

var ThemifyBuilderModuleJs;
(function ($, window, document, undefined) {

    'use strict';

    ThemifyBuilderModuleJs = {
        wow: null,
        is_mobile: null,
        fwvideos: [], // make it accessible to public
		resized:0,
        init: function () {
            this.bindEvents();
        },
        bindEvents: function () {
            if ('complete' !== document.readyState) {
                $(document).ready(this.document_ready);
            } else {
                this.document_ready();
            }
            if (window.loaded) {
                this.window_load();
            } else {
                $(window).load(this.window_load);
            }

            if ( window.onresize ){
            	this.window_resize()
			}else{
            	$(window).resize( this.window_resize );
			}

        },
        /**
         * Executed on jQuery's document.ready() event.
         */
        document_ready: function () {
            var self = ThemifyBuilderModuleJs;
            self.setupBodyClasses();
            self.pageBreakPagination();
            $.event.trigger('themify_builder_loaded');
            if (tbLocalScript.fullwidth_support === '') {
                $(window).on('tfsmartresize.tbfullwidth', function (e) {
                    self.setupFullwidthRows();
                });
            }
            self.tabsClick();
            if (!Themify.is_builder_active) {
                if (tbLocalScript.fullwidth_support === '') {
                    self.setupFullwidthRows();
                }
                self.GridBreakPoint();
                if (tbLocalScript.isAnimationActive) {
                    self.wowInit();
                }
                self.carousel();
                self.touchdropdown();
                self.tabs();
                self.onInfScr();
                self.menuModuleMobileStuff();
                self.playFocusedVideoBg();
                self.alertModule();
                $(window).on('hashchange', this.tabsDeepLink);
            }
            $(window).on('tfsmartresize.tblink', function () {
                self.menuModuleMobileStuff(true);
            });
            self.InitScrollHighlight();
            self.showcaseGallery();
			self.sliderGallery();
            self.readMoreLink();
            self.galleryPagination();
        },
        /**
         * Executed on JavaScript 'load' window event.
         */
        window_load: function () {
            var self = ThemifyBuilderModuleJs;
            window.loaded = true;
            self.galleryMasonry();
			if (!Themify.is_builder_active) {
                self.parallaxScrollingInit();
                self.charts();
                self.fullwidthVideo();
                self.backgroundSlider();
                self.backgroundZoom();
                self.backgroundZooming();
                self.stickyElementInit();
                if (tbLocalScript.isParallaxActive) {
                    self.backgroundScrolling();
                }
                self.tabsDeepLink();
            }
            else{
                self.wowApplyOnHover();
            }
            self.accordion();
        },
		window_resize:function () {

			clearTimeout(this.resized);
			this.resized = setTimeout(function () {

				var windowWidth = window.innerWidth;

				$('.module-tab[data-tab-breakpoint]').each(function () {
					if( parseInt( $(this).attr('data-tab-breakpoint') ) >= windowWidth ){
						$(this).addClass('responsive-tab-style');
					}else{
						$(this).removeClass('responsive-tab-style');
						$(this).find('.tab-nav').removeClass('right-align center-align');
					}
				});


			}, 200);
		},
        wowInit: function (callback, resync) {
            var self = ThemifyBuilderModuleJs;
            if (resync && self.wow) {
                self.wow.doSync();
                self.wow.sync();
                return;
            }
            function wowCallback() {
                function wowDuckPunch() {
                    // duck-punching WOW to get delay and iteration from classnames
                    if (typeof self.wow.__proto__ !== 'undefined') {
                        self.wow.__proto__.applyStyle = function (box, hidden) {
                            var duration = box.getAttribute('data-wow-duration'),
                                cl = box.getAttribute('class'),
                                iteration = cl.match(/animation_effect_repeat_(\d*)/),
                                delay = cl.match(/animation_effect_delay_((?:\d+\.?\d*|\.\d+))/);
                            if (null !== delay) {
                                delay = delay[1] + 's';
                            }
                            if (null !== iteration)
                                iteration = iteration[1];
                            return this.animate((function (_this) {
                                return function () {
                                    return _this.customStyle(box, hidden, duration, delay, iteration);
                                };
                            })(this));
                        };
                    }
                }
                self.animationOnScroll(resync);
                self.wow = new WOW({
                    live: true,
                    offset: typeof tbLocalScript !== 'undefined' && tbLocalScript ? parseInt(tbLocalScript.animationOffset) : 100
                });
                self.wow.init();
                wowDuckPunch();
                if(!Themify.is_builder_active){
                    self.wowApplyOnHover();
                }
            }
            callback = callback || wowCallback;
			if (typeof tbLocalScript !== 'undefined'
				&& typeof tbLocalScript.animationInviewSelectors !== 'undefined'
				&& ( $(tbLocalScript.animationInviewSelectors.toString()).length || $('.hover-wow').length ) ){
				if (!self.wow) {
                    Themify.LoadCss(tbLocalScript.builder_url + '/css/animate.min.css', null, null, null, function () {
                        Themify.LoadAsync(themify_vars.url + '/js/wow.min.js', callback, null, null, function () {
                            return (self.wow);
                        });
                    });
                }
                else {
                    callback();
                    return (self.wow);
                }
            }
        },
        wowApplyOnHover:function () {
                var is_working = false;
                $(document).off('mouseenter','.hover-wow').on('mouseenter','.hover-wow',function () {
                    if(is_working===false){
                        is_working = true;
                        var hoverAnimation = this.getAttribute('class').match(/hover-animation-(\w*)/),
                            animation = this.style.animationName;
                        if( '' != animation ){
                            $(this).css('animation-name','').removeClass( animation );
                        }
                        $(this).one('animationend webkitAnimationEnd',function(){
                            $(this).removeClass('animated ' + hoverAnimation[1]);
                            is_working = false;
                        }).addClass('animated ' + hoverAnimation[1]);
                    }
                });
        },
        setupFullwidthRows: function (el) {
            if (tbLocalScript.fullwidth_support !== '') {
                return;
            }
            if (!el) {
                if (!Themify.is_builder_active && this.rows !== undefined) {
                    el = this.rows;
                }
                else {
                    el = document.querySelectorAll('.themify_builder_content .module_row.fullwidth,.themify_builder_content .module_row.fullwidth_row_container');
                    if (!Themify.is_builder_active) {
                        this.rows = el;
                    }
                }
                if (el.length === 0) {
                    return;
                }
            }
            else if (!el.hasClass('fullwidth') && !el.hasClass('fullwidth_row_container')) {
                return;
            }
            else {
                el = el.get();
            }
            var container = $(tbLocalScript.fullwidth_container),
                    outherWith = container.outerWidth(),
                    outherLeft = container.offset().left;
            if (outherWith === 0) {
                return;
            }
            var styleId = 'tb-fulllwidth-styles',
                    style = '',
                    tablet = tbLocalScript.breakpoints.tablet,
                    tablet_landscape = tbLocalScript.breakpoints.tablet_landscape,
                    mobile = tbLocalScript.breakpoints.mobile,
                    arr = ['mobile', 'tablet', 'tablet_landscape', 'desktop'],
                    width = $(window).width(),
                    type = 'desktop';
            if (width <= mobile) {
                type = 'mobile';
            }
            else if (width <= tablet[1]) {
                type = 'tablet';
            }
            else if (width <= tablet_landscape[1]) {
                type = 'tablet_landscape';
            }
            function getCurrentValue(prop) {
                var val = $this.data(type + '-' + prop);
                if (val === undefined) {
                    if (type !== 'desktop') {
                        for (var i = arr.indexOf(type) + 1; i < 4; ++i) {
                            if (arr[i] !== undefined) {
                                val = $this.data(arr[i] + '-' + prop);
                                if (val !== undefined) {
                                    $this.data(type + '-' + prop, val);
                                    break;
                                }
                            }
                        }
                    }
                }
                return val !== undefined ? val.split(',') : [];
            }
            for (var i = 0, len = el.length; i < len; ++i) {
                var $this = $(el[i]),
                        row = $this.closest('.themify_builder_content'),
                        left = row.offset().left - outherLeft,
                        right = outherWith - left - row.outerWidth();

                // set to zero when zoom is enabled
                if (row.hasClass('tb_zooming_50') || row.hasClass('tb_zooming_75')) {
                    left = 0;
                    right = 0;
                }
                if (!Themify.is_builder_active) {
                    var index = $this.attr('class').match(/module_row_(\d+)/)[1];
                    style += '.themify_builder.themify_builder_content .themify_builder_' + row.data('postid') + '_row.module_row_' + index + '.module_row{';
                }
                if (el[i].classList.contains('fullwidth')) {
                    var margin = getCurrentValue('margin'),
                            sum = '';
                    if (margin[0]) {
                        sum = margin[0];
                        style += 'margin-left:calc(' + margin[0] + ' - ' + Math.abs(left) + 'px);';
                    }
                    else {
                        style += 'margin-left:' + (-left) + 'px;';
                    }
                    if (margin[1]) {
                        if (sum !== '') {
                            sum += ' + ';
                        }
                        sum += margin[1];
                        style += 'margin-right:calc(' + margin[1] + ' - ' + Math.abs(right) + 'px);';
                    }
                    else {
                        style += 'margin-right:' + (-right) + 'px;';
                    }
                    style += sum !== '' ? 'width:calc(' + outherWith + 'px - (' + sum + '));' : 'width:' + outherWith + 'px;';
                }
                else {
                    style += 'margin-left:' + (-left) + 'px;margin-right:' + (-right) + 'px;width:' + outherWith + 'px;';
                    if (left || right) {
                        var padding = getCurrentValue('padding'),
                                sign = '+';
                        if (left) {
                            if (padding[0]) {
                                if (left < 0) {
                                    sign = '-';
                                }
                                style += 'padding-left:calc(' + padding[0] + ' ' + sign + ' ' + Math.abs(left) + 'px);';
                            }
                            else {
                                style += 'padding-left:' + Math.abs(left) + 'px;';
                            }
                        }
                        if (right) {
                            if (padding[1]) {
                                sign = right > 0 ? '+' : '-';
                                style += 'padding-right:calc(' + padding[1] + ' ' + sign + ' ' + Math.abs(right) + 'px);';
                            }
                            else {
                                style += 'padding-right:' + Math.abs(right) + 'px;';
                            }
                        }
                    }
                }

                if (Themify.is_builder_active) {
                    el[i].style['paddingRight'] = el[i].style['paddingLeft'] = el[i].style['marginRight'] = el[i].style['marginLeft'] = '';
                    el[i].style.cssText += style;
                    style = '';
                }
                else {
                    style += '}';
                }
            }
            if (!Themify.is_builder_active) {
                style = '<style id="' + styleId + '" type="text/css">' + style + '</style>';
                $('#' + styleId).remove();
                document.getElementsByTagName('head')[0].insertAdjacentHTML('beforeend', style);
            }
        },
        addQueryArg: function (e, n, l) {
            l = l || window.location.href;
            var r, f = new RegExp("([?&])" + e + "=.*?(&|#|$)(.*)", "gi");
            if (f.test(l))
                return 'undefined' !== typeof n && null !== n ? l.replace(f, "$1" + e + "=" + n + "$2$3") : (r = l.split("#"), l = r[0].replace(f, "$1$3").replace(/(&|\?)$/, ""), 'undefined' !== typeof r[1] && null !== r[1] && (l += "#" + r[1]), l);
            if ('undefined' !== typeof n && null !== n) {
                var i = -1 !== l.indexOf("?") ? "&" : "?";
                return r = l.split("#"), l = r[0] + i + e + "=" + n, 'undefined' !== typeof r[1] && null !== r[1] && (l += "#" + r[1]), l
            }
            return l;
        },
        onInfScr: function () {
            var self = ThemifyBuilderModuleJs;
            $(document).ajaxSend(function (e, request, settings) {
                var page = settings.url.replace(/^(.*?)(\/page\/\d+\/)/i, '$2'),
                        regex = /^\/page\/\d+\//i,
                        match;

                if ((match = regex.exec(page)) !== null) {
                    if (match.index === regex.lastIndex) {
                        regex.lastIndex++;
                    }
                }

                if (null !== match) {
                    settings.url = self.addQueryArg('themify_builder_infinite_scroll', 'yes', settings.url);
                }
            });
        },
        InitScrollHighlight: function () {
            if (tbLocalScript.loadScrollHighlight == true && (Themify.is_builder_active || $('div[class*=tb_section-]').length > 0)) {
                Themify.LoadAsync(tbLocalScript.builder_url + '/js/themify.scroll-highlight.js', function(){
					Themify.body.themifyScrollHighlight( tbScrollHighlight ? tbScrollHighlight : {});
					
                    }, null, null, function () {
                    return('undefined' !== typeof $.fn.themifyScrollHighlight);
                });
            }
        },
        // Row, col, sub-col, sub_row: Background Slider
        backgroundSlider: function ($bgSlider) {
            $bgSlider = $bgSlider || $('.row-slider, .column-slider, .subrow-slider, .sub_column-slider');
            function callBack() {
                var themifySectionVars = {
                    autoplay: tbLocalScript.backgroundSlider.autoplay
                };
                // Parse injected vars
                themifySectionVars.autoplay = parseInt(themifySectionVars.autoplay, 10);
                if (themifySectionVars.autoplay <= 10) {
                    themifySectionVars.autoplay *= 1000;
                }
                // Initialize slider
                $bgSlider.each(function () {
                    var $thisRowSlider = $(this),
						$backel = $thisRowSlider.parent(),
						rsImages = [],
						rsImagesAlt = [],
						imagesCount,
						bgMode = $thisRowSlider.data('bgmode'),
						speed = $thisRowSlider.data('sliderspeed');

                    // Initialize images array with URLs
                    $thisRowSlider.find('li').each(function () {
                        rsImages.push($(this).attr('data-bg'));
                        rsImagesAlt.push($(this).attr('data-bg-alt'));
                    });

					imagesCount = ( rsImages.length > 4 ) ? 4 : rsImages.length;

					// Call backstretch for the first time
                    $backel.tb_backstretch(rsImages, {
                        speed: parseInt( speed ),
                        duration: themifySectionVars.autoplay,
                        mode: bgMode
                    });
                    rsImages = null;

                    // Cache Backstretch object
                    var thisBGS = $backel.data('tb_backstretch');

                    // Previous and Next arrows
                    $thisRowSlider.find('.row-slider-prev,.row-slider-next').on('click', function (e) {
                        e.preventDefault();
                        if ($(this).hasClass('row-slider-prev')) {
                            thisBGS.prev();
                        }
                        else {
                            thisBGS.next();
                        }
                    });

                    // Dots
                    $thisRowSlider.find('.row-slider-dot').on('click', function () {
                        thisBGS.show($(this).data('index'));
                    });

                    // Active Dot
                    var sliderDots = $thisRowSlider.find( '.row-slider-slides > li' ),
                            currentClass = 'row-slider-dot-active';

                    if( sliderDots.length ) {
						sliderDots.eq( 0 ).addClass( currentClass );

						$thisRowSlider.parent().on( 'tb_backstretch.show', function( e, data ) {
							var currentDot = sliderDots.eq( thisBGS.index );

							if( currentDot.length ) {
								sliderDots.removeClass( currentClass );
								currentDot.addClass( currentClass );
							}
						} );
                    }

                    if ($thisRowSlider.attr('data-bgmode') === 'kenburns-effect') {

						var lastIndex,
							kenburnsActive = 0,
							createKenburnIndex = function(){
								return (kenburnsActive + 1 > imagesCount) ? kenburnsActive = 1 : ++kenburnsActive;
							};

						$thisRowSlider.parent().on('tb_backstretch.before', function (e, data) {

							setTimeout(function () {

								if (lastIndex != data.index) {
									var $img = data.$wrap.find('img').last();
									$img.addClass('kenburns-effect' + createKenburnIndex());
									lastIndex = data.index;
								}

							}, 50);

						}).on('tb_backstretch.after', function (e, data) {

							var $img = data.$wrap.find('img').last(),
									expr = /kenburns-effect\d/;
							if (!expr.test($img.attr('class'))) {
									$img.addClass('kenburns-effect' + createKenburnIndex());
									lastIndex = data.index;
							}

						});

                    }

					// Add alt tag
                    $(window).on('backstretch.before backstretch.show', function (e, instance, index) {
                        // Needed for col styling icon and row grid menu to be above row and sub-row top bars.
                        if (Themify.is_builder_active) {
                            $backel.css('zIndex', 0);
                        }
                        if (rsImagesAlt[ index ] !== undefined) {
                            setTimeout(function () {
                                instance.$wrap.find('img:not(.deleteable)').attr('alt', rsImagesAlt[ index ]);
                            }, 1);
                        }
                    });
                });
            }
            if ($bgSlider.length > 0) {
                Themify.LoadAsync(
                        themify_vars.url + '/js/backstretch.themify-version.js',
                        callBack,
                        null,
                        null,
                        function () {
                            return ('undefined' !== typeof $.fn.tb_backstretch);
                        }
                );
            }
        },
        // Row: Fullwidth video background
        fullwidthVideo: function ($videoElm, parent) {
            if ($videoElm) {
                $videoElm = $videoElm.data('fullwidthvideo')? $videoElm : $('[data-fullwidthvideo]', $videoElm);
            }

            parent = parent || $('.themify_builder');
            $videoElm = $videoElm || $('[data-fullwidthvideo]', parent);

            if ($videoElm.length > 0) {
                
                var self = this,
                    is_mobile = this._isMobile(),
                    is_youtube = [],
                    is_vimeo = [],
                    is_local = [];

                $videoElm.each(function (i) {
                    var $video = $(this),
                        url = $video.data('fullwidthvideo');
                    if (!url) {
                        return true;
                    }

                    $video.children('.big-video-wrap').remove();
                    var provider = Themify.parseVideo(url);
                    if (provider.type === 'youtube') {
                        if (!is_mobile && provider.id) {
                            is_youtube.push({'el': $video, 'id': provider.id});
                        }
                    } 
                    else if (provider.type === 'vimeo') {
                        if (!is_mobile && provider.id) {
                            is_vimeo.push({'el': $video, 'id': provider.id});
                        }
                    } else {
                        is_local.push($video);
                    }
                });
                $videoElm = null;
                if (is_local.length > 0) {
                    if(!is_mobile){
                        Themify.LoadAsync(
                                themify_vars.url + '/js/bigvideo.js',
                                function () {
                                    self.fullwidthVideoCallBack(is_local);
                                    is_local = null;
                                },
                                null,
                                null,
                                function () {
                                    return ('undefined' !== typeof $.fn.ThemifyBgVideo);
                                }
                        );
                    }
                    else{
                        for(var i=0,len=is_local.length;i<len;++i){
                            if ( 'play' === is_local[i].data('playonmobile') ) {
                                    var videoURL = is_local[i].data('fullwidthvideo');
                                    var id = Themify.hash(i + '-' + videoURL),
                                            videoEl = '<div class="big-video-wrap">'
                                                    + '<video class="video-' + id + '" muted="true" autoplay="true" loop="true" playsinline="true" >' +
                                                    '<source src="' + videoURL + '" type="video/mp4">' +
                                                    '</video></div>';
                                    is_local[i][0].insertAdjacentHTML('afterbegin', videoEl);
                            }
                       }
                       is_local = null;
                   }
                }

                if (is_vimeo.length > 0) {
                    Themify.LoadAsync(
                            tbLocalScript.builder_url + '/js/froogaloop.min.js',
                            function () {
                                self.fullwidthVimeoCallBack(is_vimeo);
                                is_vimeo = null;
                            },
                            null,
                            null,
                            function () {
                                return ('undefined' !== typeof $f);
                            }
                    );
                }
                if (is_youtube.length > 0) {
                    if (!$.fn.ThemifyYTBPlayer) {
                        Themify.LoadAsync(
                                tbLocalScript.builder_url + '/js/themify-youtube-bg.js',
                                function () {
                                    self.fullwidthYoutobeCallBack(is_youtube);
                                    is_youtube = null;
                                },
                                null,
                                null,
                                function () {
                                    return typeof $.fn.ThemifyYTBPlayer !== 'undefined';
                                }
                        );
                    } else {
                        self.fullwidthYoutobeCallBack(is_youtube);
                    }
                }
            }
        },
        videoParams: function ($el) {
            var mute = 'mute' === $el.data('mutevideo'),
                    loop = 'undefined' !== typeof $el.data('unloopvideo') ? 'loop' === $el.data('unloopvideo') : 'yes' === tbLocalScript.backgroundVideoLoop;

            return {'mute': mute, 'loop': loop};
        },
        // Row: Fullwidth video background
        fullwidthVideoCallBack: function ($videos) {
            for(var i=0,len=$videos.length;i<len;++i){
                  var videoURL = $videos[i].data('fullwidthvideo'),
                    params = ThemifyBuilderModuleJs.videoParams($videos[i]);
                $videos[i].ThemifyBgVideo({
                    url: videoURL,
                    doLoop: params.loop,
                    ambient: params.mute,
                    id: Themify.hash(i + '-' + videoURL)
                });
            }
        },
        fullwidthYoutobeCallBack: function ($videos) {
            var self = this;
            if (typeof YT === 'undefined' || typeof YT.Player === 'undefined') {
                Themify.LoadAsync(
                        '//www.youtube.com/iframe_api',
                        function () {
                            window.onYouTubePlayerAPIReady = _each;
                        },
                        null,
                        null,
                        function () {
                            return typeof YT !== 'undefined' && typeof YT.Player !== 'undefined';
                        });

            }
            else {
                _each();
            }
            function _each() {
                for(var i=0,len=$videos.length;i<len;++i){
                    var params = self.videoParams($videos[i].el);
                    $videos[i].el.ThemifyYTBPlayer({
                        videoID: $videos[i].id,
                        id: $videos[i].el.closest('.themify_builder_content').data('postid') + '_' + i,
                        mute: params.mute,
                        loop: params.loop,
                        mobileFallbackImage: tbLocalScript.videoPoster
                    });
                }
            }

        },
        fullwidthVimeoCallBack: function ($videos) {
            var self = this;
             if (typeof self.fullwidthVimeoCallBack.counter === 'undefined') {
                self.fullwidthVimeoCallBack.counter = 1;
                $(window).on('tfsmartresize.tfVideo', function vimeoResize(e) {
                    for(var i in $videos){
                        if($videos[i]){
                            var ch = $videos[i].el.children('.themify-video-vmieo');
                            if(ch.length>0){
                                VimeoVideo(ch);
                            }
                            else{
                                delete $videos[i];
                                $videos[i] = null;
                            }
                        }
                    }
                    if($videos.length===0){
                        self.fullwidthVimeoCallBack.counter = 'undefined';
                        $(window).off('tfsmartresize.tfVideo', vimeoResize);
                    }
                });

            }
            function VimeoVideo($video) {
                var width = $video.outerWidth(true),
                    height = $video.outerHeight(true),
                    pHeight = Math.ceil(width / 1.7), //1.7 ~ 16/9 aspectratio
                    iframe = $video.find('iframe');
                iframe.width(width).height(pHeight).css({
                    left: 0,
                    top: (height - pHeight) / 2
                });
            }
            for(var i in $videos){
                 if($videos[i]){
                 var $video = $videos[i].el,
                    params = self.videoParams($video);
                    $video[0].insertAdjacentHTML('afterbegin','<div class="big-video-wrap themify-video-vmieo"><iframe id="themify-vimeo-' + i + '" src="https://player.vimeo.com/video/' + $videos[i].id + '?api=1&portrait=0&title=0&badge=0&player_id=themify-vimeo-' + i + '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>');
                    var player = $f($('#themify-vimeo-' + i)[0]);
                    player.addEvent('ready', function () {
                        player.api('setLoop', params.loop);
                        player.api('setVolume', params.mute ? 0 : 1);
                        player.api('fullscreen', 0);
                        var v = $video.children('.themify-video-vmieo');
                        if (v.length > 1) {
                            v.slice(1).remove();
                        }
                        VimeoVideo(v);
                        player.api('play');
                    });
                 }
            }
        },
        playFocusedVideoBg: function () {
            var self = ThemifyBuilderModuleJs,
                    playOnFocus = function () {
                        if (!self.fwvideos.length > 0)
                            return;
                        var h = window.innerHeight;
                        for (var i in self.fwvideos) {
                            var el = self.fwvideos[i].getPlayer();
                            if (el.isPlaying || !el.source) {
                                return;
                            }
                            var rect = el.P.getBoundingClientRect();
                            if (rect.bottom >= 0 && rect.top <= h) {
                                el.show(el.source);
                                el.isPlaying = true;
                            }
                        }
                    };
            $(window).on('scroll mouseenter keydown assignVideo', playOnFocus);
        },
        charts: function (el) {
            var elements = $('.module-feature-chart-html5', el),
                    self = this;
            if (elements.length > 0) {
                if (this.charts_data === undefined) {
                    this.charts_data = {};
                }
                Themify.LoadAsync(themify_vars.url + '/js/waypoints.min.js', callback, null, null, function () {
                    return ('undefined' !== typeof $.fn.waypoint);
                });
            }
            function callback() {

                function chartsCSS(charts) {
                    var styleId = 'chart-html5-styles',
                            styleHTML = '<style id="' + styleId + '">',
                            prefix = Themify.getVendorPrefix();
                    for (var i in charts) {
                        styleHTML += '.module-feature-chart-html5[data-progress="' + i + '"] .chart-html5-full,' +
                                '.module-feature-chart-html5[data-progress="' + i + '"] .chart-html5-fill {' + prefix + 'transform: rotate(' + charts[i] + 'deg);transform:rotate(' + charts[i] + 'deg);}';
                    }
                    styleHTML += '</style>';
                    $('#' + styleId).remove();
                    document.getElementsByTagName('head')[0].insertAdjacentHTML('beforeend', styleHTML);
                }

                // this mess adjusts the size of the chart, to make it responsive
                var setChartSize = function ($this) {
                    var width = Math.min($this.data('size'), $this.closest('.module-feature').width()),
                            halfw = Math.ceil(width / 2);
                    $this.css( {width: width, height: width} ).find( '.chart-html5-mask' ).css( {
						borderRadius: '0 ' + halfw + 'px ' + halfw + 'px 0',
						clip: 'rect(0px, ' + width + 'px, ' + width + 'px, ' + halfw + 'px)'
					} );
					
					$this.find( '.chart-html5-fill' ).addClass('chart-loaded').css( {
						borderRadius: halfw + 'px 0 0 ' + halfw + 'px',
						clip: 'rect(0px, ' + halfw + 'px, ' + width + 'px, 0px)'
					} );
                };
                var deg = parseFloat(180 / 100).toFixed(2),
                        reinit = false;
                elements.each(function () {
                    var progress = $(this).data('progress-end');
                    if (progress === undefined) {
                        progress = 100;
                    }
                    if (self.charts_data [progress] === undefined) {
                        self.charts_data [progress] = parseFloat(deg * progress).toFixed(2) - 0.1;
                        reinit = true;
                    }
                    setChartSize($(this));
                });
                if (reinit === true) {
                    chartsCSS(self.charts_data);
                }
                if(!Themify.body.hasClass('full-section-scrolling-horizontal')) {
                    elements.each(function () {
                        var $this = $(this);
                        var horizontal = ($this.closest('.module_row_slide').is(':not(:first-child)'));
                        if(horizontal) {
                            $this.waypoint(function() {
                                $this.attr('data-progress', $this.data('progress-end'));
                            }, {
                                horizontal: true,
                                offset: 'right-in-view'
                            });
                        } else {
                            $this.waypoint(function () {
                                $this.attr('data-progress', $this.data('progress-end'));
                            }, {
                                offset: '100%',
                                triggerOnce: true
                            });
                        }
                    });
                } else if(!Themify.is_builder_active){
                    Themify.body.on('themify_onepage_slide_onleave', function(event, $slide) {
                        $slide.find('.module-feature-chart-html5').each(function() {
                            $(this).attr('data-progress', $(this).data('progress-end'));
                        });
                    });

                }
                $(window).on('tfsmartresize.charts', function () {
                    elements.each(function () {
                        setChartSize($(this));
                    });
                });
            }
        },
        carousel: function (el) {
            if ($('.themify_builder_slider', el).length > 0) {
                var $self = this;
                Themify.LoadAsync(themify_vars.includesURL + 'js/imagesloaded.min.js', function () {
                    if ('undefined' === typeof $.fn.carouFredSel) {
                        Themify.LoadAsync(themify_vars.url + '/js/carousel.min.js', function () {
                            $self.carouselCalback(el);
                        }, null, null, function () {
                            return ('undefined' !== typeof $.fn.carouFredSel);
                        });
                    }
                    else {
                        $self.carouselCalback(el);
                    }
                }, null, null, function () {
                    return ('undefined' !== typeof $.fn.imagesLoaded);
                });
            }

        },
		sliderAutoHeight: function ($this) {
			if( 'video' === $this.data('type') ) {
				// Get all the possible height values from the slides
				var heights = $this.children().map( function () { return $(this).height(); });
				$this.parent().height( Math.max.apply( null, heights ) );
			} else if( $this.closest( '.module-slider' ).is( '.slider-overlay, .slider-caption-overlay' ) ) {
				var sliderContent = $this.find( '.slide-content' ),
					originalOffset = 0;
				if( sliderContent.eq(0).attr( 'style' ) === undefined ) {
					originalOffset = parseFloat( sliderContent.eq(0).css( 'bottom' ) );
					$this.data( 'captionOffset', originalOffset );
				} else if( $this.data( 'captionOffset' ) ) {
					originalOffset = $this.data( 'captionOffset' );
				}

				sliderContent.each( function() {
					var $el = $( this ),
						captionOffset = $el.closest( '.slide-inner-wrap' ).height() - $this.parent().height();
					$el.css( 'bottom', captionOffset + originalOffset );
				} );
			}
		},
        carouselCalback: function (el) {
            var self = this,
                isMobile = function() {
                        return tbLocalScript && tbLocalScript.breakpoints
                        ? tbLocalScript.breakpoints.mobile > window.innerWidth : self._isMobile();
                };

                $( '.themify_builder_slider', el ).each( function () {
                    if( $( this ).closest( '.caroufredsel_wrapper' ).length > 0 ) {
                        return;
                    }

                var $this = $( this ),
                        img_length = $this.find( 'img' ).length,
                        $height = ( typeof $this.data( 'height' ) === 'undefined' ) ? 'variable' : $this.data( 'height' ),
                        visibleItems = isMobile() && $this.data( 'mob-visible' ) ? $this.data( 'mob-visible' ) : { min: 1, max: $this.data( 'visible' ) },
                        $args = {
                            responsive: true,
                            circular: true,
                            infinite: true,
                            height: $height,
                            items: {
                                visible: visibleItems,
                                width: 150,
                                height: 'variable'
                            },
                            onCreate: function( items ) {
                                $('.themify_builder_slider_wrap').css({'visibility': 'visible', 'height': 'auto'});
                                $this.trigger('updateSizes');
                                $('.tb_slider_loader').remove();

                                if( 'auto' === $height ) {
                                    ThemifyBuilderModuleJs.sliderAutoHeight( $this );
                                }
                            }
                        };

                // fix the one slide problem
                if ($this.children().length < 2) {
                    $('.themify_builder_slider_wrap').css({'visibility': 'visible', 'height': 'auto'});
                    $('.tb_slider_loader').remove();
                    $(window).resize();
                    return;
                }

                // Auto
                if (parseInt($this.data('auto-scroll')) > 0) {
                    $args.auto = {
                        play: true,
                        timeoutDuration: parseInt($this.data('auto-scroll') * 1000)
                    };
                } 
                else if ($this.data('effect') !== 'continuously' && (typeof $this.data('auto-scroll') !== 'undefined' || parseInt($this.data('auto-scroll')) === 0)) {
                    $args.auto = false;
                }

                // Scroll
                if ($this.data('effect') === 'continuously') {
                    var speed = $this.data('speed'), duration;
                    if (speed == .5) {
                        duration = 0.10;
                    } else if (speed == 4) {
                        duration = 0.04;
                    } else {
                        duration = 0.07;
                    }
                    $args.auto = {timeoutDuration: 0};
                    $args.align = false;
                    $args.scroll = {
                        delay: 1000,
                        easing: 'linear',
                        items: $this.data('scroll'),
                        duration: duration,
                        pauseOnHover: $this.data('pause-on-hover')
                    };
                } else {
                    $args.scroll = {
                        items: $this.data('scroll'),
                        pauseOnHover: $this.data('pause-on-hover'),
                        duration: parseInt($this.data('speed') * 1000),
                        fx: $this.data('effect')
                    };
                }

                if ($this.data('arrow') === 'yes') {
                    $args.prev = '#' + $this.data('id') + ' .carousel-prev';
                    $args.next = '#' + $this.data('id') + ' .carousel-next';
                }

                if ($this.data('pagination') === 'yes') {
                    $args.pagination = {
                        container: '#' + $this.data('id') + ' .carousel-pager',
                        items: $this.data('visible')
                    };
                }

                if ($this.data('wrap') === 'no') {
                    $args.circular = false;
                    $args.infinite = false;
                }

				if( $this.data( 'sync' ) ) {
					$args.synchronise = [$this.data( 'sync' ), false];
				}
				
                if (img_length > 0) {
                    $this.imagesLoaded(function () {
                        self.carouselInitSwipe($this, $args);
                    });
                } else {
                    self.carouselInitSwipe($this, $args);
                }

                $('.mejs__video').on('resize', function (e) {
                    e.stopPropagation();
                });

                $(window).on('tfsmartresize', function () {
                    $('.mejs__video').resize();
                    $this.trigger('updateSizes');

                    if ( 'auto' === $height ) {
                            ThemifyBuilderModuleJs.sliderAutoHeight($this);
                    }

                    var vMode;
                    if( ( ! vMode || vMode === 'desktop' ) && $this.data( 'mob-visible' ) && isMobile() ) {
                        vMode = 'mobile';
                        $this.trigger( 'finish' ).trigger( 'configuration', { items: { visible: $this.data( 'mob-visible' ) } } );
                    } else if( ! vMode || vMode === 'mobile' ) {
                        vMode = 'desktop';
                        $this.trigger( 'finish' ).trigger( 'configuration', { items: { visible: $this.data( 'visible' ) } } );
                    }
                });

            });
        },
        carouselInitSwipe: function ($this, $args) {
            $this.carouFredSel($args);
            $this.swipe({
                excludedElements: 'label, button, input, select, textarea, .noSwipe',
                swipeLeft: function () {
                    $this.trigger('next', true);
                },
                swipeRight: function () {
                    $this.trigger('prev', true);
                },
                tap: function (event, target) {
                    // in case of an image wrapped by a link click on image will fire parent link
                    $(target).parent().trigger('click');
                }
            });

			if( $args.auto && $args.auto.timeoutDuration === 0 && $args.next && $args.prev && $args.scroll ) {
				Themify.body.on( 'click', [$args.next, $args.prev].join(), function() {
					$this.trigger( 'finish' );
					$this.trigger( $( this ).is( $args.next ) ? 'next' : 'prev', [{duration: $args.scroll.duration * 2}]);
				} );
			}
        },
        loadOnAjax: function (el, type) {
            var self = ThemifyBuilderModuleJs;
            if (type === 'row') {
                self.setupFullwidthRows(el);
            }
            self.touchdropdown(el);
            self.tabs(el);
            self.carousel(el);
            self.charts(el);
            self.fullwidthVideo(el, null);
            if (el) {
                self.backgroundSlider(el.find('.row-slider, .column-slider, .subrow-slider'));
            } else {
                self.backgroundSlider();
            }
            var zoomScrolling = null,
                    zoom = null,
                    bgscrolling = null;
            if (el) {
                zoomScrolling = el.find('.builder-zoom-scrolling');
                if (el.hasClass('builder-zoom-scrolling')) {
                    zoomScrolling = zoomScrolling.add(el);
                }
                zoom = el.find('.builder-zooming');
                if (el.hasClass('builder-zooming')) {
                    zoom = zoom.add(el);
                }
                if (tbLocalScript.isParallaxActive) {
                    bgscrolling = el.find('.builder-parallax-scrolling');
                    if (el.hasClass('builder-parallax-scrolling')) {
                        bgscrolling = bgscrolling.add(el);
                    }
                }
            }
            if (zoomScrolling === null || zoomScrolling.length > 0) {
                self.backgroundZoom(zoomScrolling);
            }
            zoomScrolling = null;
            if (zoom === null || zoom.length > 0) {
                self.backgroundZooming(zoom);
            }
            zoom = null;
            if (tbLocalScript.isParallaxActive && (bgscrolling === null || bgscrolling.length > 0)) {
                self.backgroundScrolling(bgscrolling);
            }
            bgscrolling = null;
            self.menuModuleMobileStuff(false, el);
            if (tbLocalScript.isAnimationActive) {
                self.wowInit(null, el);
            }
			self.galleryMasonry();
        },
        touchdropdown: function (el) {
            if (tbLocalScript.isTouch) {
                if (!$.fn.themifyDropdown) {
                    Themify.LoadAsync(themify_vars.url + '/js/themify.dropdown.js', function () {
                        $('.module-menu .nav', el).themifyDropdown();
                    },
                            null,
                            null,
                            function () {
                                return ('undefined' !== typeof $.fn.themifyDropdown);
                            });
                }
                else {
                    $('.module-menu .nav', el).themifyDropdown();
                }
            }
        },
        accordion: function () {
            Themify.body.off('click.themify', '.accordion-title').on('click.themify', '.accordion-title', function (e) {
                var $this = $(this),
                        $panel = $this.next(),
                        $item = $this.closest('li'),
						$parent = $item.parent(),
                        type = $this.closest('.module.module-accordion').data('behavior'),
                        def = $item.toggleClass('current').siblings().removeClass('current'); /* keep "current" classname for backward compatibility */

				if( ! $parent.hasClass( 'tf-init-accordion' ) ) {
					$parent.addClass( 'tf-init-accordion' );
				}

                if ('accordion' === type) {
                    def.find('.accordion-content').slideUp().attr('aria-expanded', 'false').closest('li').removeClass('builder-accordion-active');
                }
                if ($item.hasClass('builder-accordion-active')) {
                    $panel.slideUp();
                    $item.removeClass('builder-accordion-active');
                    $panel.attr('aria-expanded', 'false');
                } else {
                    $item.addClass('builder-accordion-active');
                    $panel.slideDown(function () {
                        if (type === 'accordion' && window.scrollY > $panel.offset().top) {
                            var $scroll = $('html,body');
                            $scroll.animate({
                                scrollTop: $this.offset().top
                            },
                            {duration: tbScrollHighlight.speed,
                                complete: function () {
                                    if (tbScrollHighlight.fixedHeaderSelector != '' && $(tbScrollHighlight.fixedHeaderSelector).length > 0) {
                                        var to = Math.ceil($this.offset().top - $(tbScrollHighlight.fixedHeaderSelector).outerHeight(true));
                                        $scroll.stop().animate({scrollTop: to}, 300);
                                    }
                                }
                            }
                            );
                        }
                    });
                    $panel.attr('aria-expanded', 'true');

                    // Show map marker properly in the center when tab is opened
                    var existing_maps = $panel.hasClass('default-closed') ? $panel.find('.themify_map') : false;
                    if (existing_maps && existing_maps.length>0) {
                        for (var i = 0; i < existing_maps.length; ++i) { // use loop for multiple map instances in one tab
                            var current_map = $(existing_maps[i]).data('gmap_object'); // get the existing map object from saved in node
                            if (typeof current_map.already_centered !== 'undefined' && !current_map.already_centered)
                                current_map.already_centered = false;
                            if (!current_map.already_centered) { // prevent recentering
                                var currCenter = current_map.getCenter();
                                google.maps.event.trigger(current_map, 'resize');
                                current_map.setCenter(currCenter);
                                current_map.already_centered = true;
                            }
                        }
                    }
                }

                Themify.body.trigger('tb_accordion_switch', [$panel]);
                if (!Themify.is_builder_active) {
                    Themify.triggerEvent(window, 'resize');
                }
                e.preventDefault();
            });
        },
        tabs: function (el) {
            var items= $('.module.module-tab', el);
            if(el && el.hasClass('module-tab')){
                items = items.add(el);
            }
            items.each(function () {
                var tab = $('.tab-nav:first', this),
                    $height = tab.outerHeight();
                if ($height > 200) {
                    tab.siblings('.tab-content').css('min-height', $height);
                }
                if (Themify.is_builder_active) {
                    $(this).data('tabify',false);
                }
                $(this).tabify();
            });
        },
        tabsClick:function(){
                Themify.body.off( 'click' ,'a[href*="#tab-"]').on( 'click','a[href*="#tab-"]',  function(e) {
                    
                    if ( $( this ).closest( '.tab-nav' ).length )
                                    return;
                    var hash = $( this.hash);
                    if ( hash.length && hash.closest( '.module-tab' ).length ) {
                        hash.closest( '.module-tab' ).find( '.tab-nav a[href="' + this.hash +'"]' ).click();
                        e.preventDefault();
                    }
            } );  
        },
        tabsDeepLink: function () {
            var hash = decodeURIComponent( window.location.hash );
            hash = hash.replace('!/', ''); // fix conflict with section highlight
            if ('' != hash && '#' !== hash && -1 === hash.search('/') && $(hash + '.tab-content').length > 0) {
                var cons = 100,
                        $moduleTab = $(hash).closest('.module-tab');
                if ($moduleTab.length > 0) {
                    $('a[href="' + hash + '"]').click();
                    $('html, body').animate({scrollTop: $moduleTab.offset().top - cons}, 1000);
                }
            }
        },
        backgroundScrolling: function (el) {
            if (!el) {
                el = $('.builder-parallax-scrolling');
            }
            el.builderParallax();
        },
        backgroundZoom: function (el) {
            var selector = '.themify_builder .builder-zoom-scrolling';
            if (!el) {
                el = $(selector);
            }
            function doZoom(e) {
                if (e !== null) {
                    el = $(selector);
                }
                if (el.length > 0) {
                    var height = window.innerHeight;
                    el.each(function () {
                        var rect = this.getBoundingClientRect();
                        if (rect.bottom >= 0 && rect.top <= height) {
                            var zoom = 140 - (rect.top + this.offsetHeight) / (height + this.offsetHeight) * 40;
                            $(this).css('background-size', zoom + '%');
                        }
                    });
                }
                else {
                    $(window).off('scroll', doZoom);
                }
            }
            if (el.length > 0) {
                doZoom(null);
                $(window).off('scroll', doZoom).on('scroll', doZoom);
            }
        },
        backgroundZooming: function (el) {
            var selector = '.themify_builder .builder-zooming';
            if (!el) {
                el = $(selector);
            }
            function isZoomingElementInViewport(item, innerHeight, clientHeight, bclientHeight) {
                var rect = item.getBoundingClientRect();
                return (
                        rect.top + item.clientHeight >= (innerHeight || clientHeight || bclientHeight) / 2 &&
                        rect.bottom - item.clientHeight <= (innerHeight || clientHeight || bclientHeight) / 3
                        );
            }

            function doZooming(e) {
                 
                if (e !== null) {
                    el = $(selector);
                }
                if (el.length > 0) {
                    var height = window.innerHeight,
                            clientHeight = document.documentElement.clientHeight,
                            bclientHeight = document.body.clientHeight,
                            zoomingClass = 'active-zooming';

                    el.each(function () {
                        if (!this.classList.contains(zoomingClass) && isZoomingElementInViewport(this, height, clientHeight, bclientHeight)) {
                            $(this).addClass(zoomingClass);
                        }
                    });
                }
                else {
                    $(window).off('scroll', doZooming);
                }
            }
            if (el.length > 0) {
                doZooming(null);
                $(window).off('scroll', doZooming).on('scroll', doZooming);
            }
        },
        animationOnScroll: function (resync) {
            var self = ThemifyBuilderModuleJs,
                    selectors = tbLocalScript.animationInviewSelectors;
            function doAnimation() {
                resync = resync || false;
                // On scrolling animation
                if ($(selectors).length > 0) {
                    if (!Themify.body.hasClass('animation-running')) {
                        Themify.body.addClass('animation-running');
                    }
                } else if (Themify.body.hasClass('animation-running')) {
                    Themify.body.removeClass('animation-running');
                }

                // Core Builder Animation
                $.each(selectors, function (i, selector) {
                    $(selector).addClass('wow');
                });

                if (resync) {
                    if (self.wow) {
                        self.wow.doSync();
                    }
                    else {
                        var wow = self.wowInit();
                        if (wow) {
                            wow.doSync();
                        }
                    }
                }
            }
           Themify.body.addClass('animation-on');
            doAnimation();
        },
        setupBodyClasses: function () {
            var classes = [];
            if (ThemifyBuilderModuleJs._isTouch()) {
                classes.push('builder-is-touch');
            }
            if (ThemifyBuilderModuleJs._isMobile()) {
                classes.push('builder-is-mobile');
            }
            if (tbLocalScript.isParallaxActive) {
                classes.push('builder-parallax-scrolling-active');
            }
            if (!Themify.is_builder_active) {
                $('.themify_builder_content').each(function () {
                    if ($(this).children(':not(.js-turn-on-builder)').length > 0) {
                        classes.push('has-builder');
                        return false;
                    }
                });
            }
            Themify.body.addClass(classes.join(' '));
        },
        _isTouch: function () {
            var isTouchDevice = this._isMobile(),
                    isTouch = isTouchDevice || (('ontouchstart' in window) || (navigator.msMaxTouchPoints > 0) || (navigator.maxTouchPoints));
            return isTouch;
        },
        _isMobile: function () {
            if (this.is_mobile === null) {
                this.is_mobile = navigator.userAgent.match(/(iPhone|iPod|iPad|Android|playbook|silk|BlackBerry|BB10|Windows Phone|Tizen|Bada|webOS|IEMobile|Opera Mini)/);
            }
            return this.is_mobile;
        },
        galleryPagination: function () {
           Themify.body.on( 'click', '.module-gallery .pagenav a',  function (e) {
                e.preventDefault();
                var $wrap = $(this).closest('.module-gallery');
                $.ajax({
                    url: this,
                    beforeSend: function () {
                        $wrap.addClass('builder_gallery_load');
                    },
                    complete: function () {
                        $wrap.removeClass( 'builder_gallery_load' );
						ThemifyBuilderModuleJs.galleryMasonry();
                    },
                    success: function (data) {
                        if (data) {
                            var $id = $wrap.prop('id');
                            $wrap.html( $(data).find('#' + $id).html() );
                        }
                    }
                });
            });
        },
        pageBreakPagination: function () {
            var $pagination = $( ".post-pagination" )
            if(!$pagination.length){
                return;
            }
            $pagination.addClass('pagenav clearfix');
            var content = $(this).text().replace(/ /g,'');
            $pagination.contents().filter(function(){
                if(this.nodeValue != null && this.nodeValue.replace(/ /g,'') != '')
                return this.nodeType !== 1;
            }).wrap( "<span class='current number'></span>" );
            $pagination.find('a').addClass('number');
            $pagination.find('strong').remove();
            $pagination.css('display','block');
         },
		galleryMasonry : function() {
			var gallery = $( '.module-gallery.gallery-masonry' );
			if( gallery.length ) {
				Themify.LoadAsync( themify_vars.includesURL + 'js/imagesloaded.min.js', function () {
					Themify.LoadAsync( themify_vars.includesURL + 'js/masonry.min.js', function () {
						var rtl = ! Themify.body[0].classList.contains( 'rtl' );

						gallery.each( function() {
							var $this = $( this );

							$( this ).imagesLoaded( function () {
								$this.data( 'masonry' ) && $this.data( 'masonry' ).destroy();

								$this.masonry({
									itemSelector: '.gallery-item',
									originLeft: rtl,
									stamp: '.module-title',
									gutter: '.module-gallery-gutter'
								});
							});
						});
					}, null, null, function () {
						return 'undefined' !== typeof $.fn.masonry;
					} );
				}, null, null, function () {
					return 'undefined' !== typeof $.fn.imagesLoaded;
				} );
			}
		},
        showcaseGallery: function () {
            Themify.body.on('click', '.module-gallery.layout-showcase a', function (e) {
                e.preventDefault();
				var showcaseContainer = $(this).closest('.gallery').find('.gallery-showcase-image'),
					titleBox = showcaseContainer.find('.gallery-showcase-title'),
					that = $(this);

				showcaseContainer.addClass('builder_gallery_load');

				titleBox.css({opacity: '', visibility: ''});
				showcaseContainer.find('img').prop('src', that.data('image'));
				showcaseContainer.find('.gallery-showcase-title-text').html(that.prop('title'));
				showcaseContainer.find('.gallery-showcase-caption').html(that.data('caption'));
				!$.trim(titleBox.text()) && titleBox.css({opacity: 0, visibility: 'hidden'});

				showcaseContainer.find('img').one('load', function() {
					showcaseContainer.removeClass('builder_gallery_load');
				})


            });

            if (Themify.body.hasClass('themify_builder_active')){
				window.top.jQuery('body').one('themify_builder_ready',function () {
					Themify.body.find('.module-gallery.layout-showcase a').first().trigger('click');
				});
            }else{
				Themify.body.find('.module-gallery.layout-showcase a').first().trigger('click');
			}





        },
		sliderGallery: function() {
			var galleries = $( '.module-gallery.layout-slider' );

			galleries.length && galleries.each( function() {
				var slides = $( '.themify_builder_slider', this );

				if( slides.length === 2 ) {
					var items = slides.eq(1).children( 'li' );
					items.on( 'click', function( e ) {
						e.preventDefault();

						slides.eq(0).trigger( 'slideTo', items.index( this ) );

					} );
				}
			} );
		},
		parallaxScrollingInit: function ( el, is_live ) {
			if ( tbLocalScript.isParallaxScrollActive ) {
				if ( el ) {
					if (is_live) {
						el = el.get();
					} else {
						var is_rellax = el.data('parallax-element-speed'),
							p = el.get(0);

						el = Array.prototype.slice.call( p.querySelectorAll( '[data-parallax-element-speed]' ) );
						is_rellax && el.push(p);
					}
				} else {
					el = document.querySelectorAll( '[data-parallax-element-speed]' );
				}

				if ( el.length ) {
					if ( typeof Rellax === 'undefined' ) {
						Themify.LoadAsync( tbLocalScript.builder_url + '/js/premium/themify.parallaxit.js', parallaxScrollingCallback, false, false, function () {
							return typeof Rellax !== 'undefined';
						});
					} else {
						parallaxScrollingCallback();
					}
				}
			}
			function parallaxScrollingCallback() {
				function rellaxInit( items ) {
					new Rellax( items, {
						round: false
					});
				}

				rellaxInit( el );

				if ( ! Themify.is_builder_active ) {
					$( document ).ajaxComplete( function () {
						var elem = document.querySelectorAll( '[data-parallax-element-speed]' );
						elem.length && rellaxInit( elem );
					});
				}
			}
		},
        menuModuleMobileStuff: function (is_resize, el) {
            var menuModules = $('.module.module-menu', el);

            if (menuModules.length > 0) {
                var windowWidth = window.innerWidth,
                        closeMenu = function () {
                			var mobileMenu = $('.mobile-menu-module');
							mobileMenu
								.prop('class', 'mobile-menu-module')
								.next('.body-overlay')
								.removeClass('body-overlay-on');

							Themify.body.removeClass('menu-module-left menu-module-right');
							setTimeout(function () {

								if( Themify.body.hasClass('close-left-menu') || Themify.body.hasClass('close-right-menu') ){
									Themify.body.removeClass('close-left-menu close-right-menu');
									mobileMenu.empty();
								}

							},300);
                        };
                if ($('.mobile-menu-module').length === 0) {
                    Themify.body[0].insertAdjacentHTML('beforeend','<div class="themify_builder"><div class="mobile-menu-module"></div><div class="body-overlay"></div></div>');
                }

                menuModules.each(function () {
                    var $this = $(this),
                            breakpoint = $this.data('menu-breakpoint');

                    if (breakpoint) {
                        var menuContainer = $this.find('div[class*="-container"]'),
                                menuBurger = $this.find('.menu-module-burger');

                        if (windowWidth >= breakpoint) {
                            menuContainer.show();
                            menuBurger.hide();
                        } else {
                            menuContainer.hide();
                            menuBurger.css('display', 'block');
                        }

                        if (!is_resize) {
                            if ($this.next('style').length > 0) {
                                var styleContent = $this.next('style').html().replace(/\.[^{]+/g, function (match) {
                                    return match + ', .mobile-menu-module' + match.replace(/\.themify_builder\s|\.module-menu/g, '');
                                });

                                $this.next('style').html(styleContent);
                            }
                            $this[0].insertAdjacentHTML('beforeend','<a class="menu-module-burger"></a>');
                        }
                    }
                });

                if (!is_resize && !Themify.is_builder_active) {
                    Themify.body.on('click', '.menu-module-burger', function (e) {
                        e.preventDefault();

                        var menuDirection = $(this).parent().data('menu-direction'),
                                menuContent = $(this).parent().find('div[class*="-container"] > ul').clone(),
                                menuUI = menuContent.prop('class').replace(/nav|menu-bar|fullwidth|vertical|with-sub-arrow/g, ''),
                                customStyle = $(this).parent().prop('class').match(/menu-[\d\-]+/g);

                        customStyle = customStyle ? customStyle[0] : '';
                        menuContent = menuContent.removeAttr('id').removeAttr('class').addClass('nav');
                        if( menuContent.find('ul').length ){
                            menuContent.find('ul').prev('a').append('<i class="toggle-menu "></i>');
                        }
                        Themify.body.addClass('menu-module-' + menuDirection);
                        $('.mobile-menu-module').addClass(menuDirection + ' ' + menuUI + ' ' + customStyle)
                                .html(menuContent)
                                .prepend('<a class="menu-close"></a>')
                                .next('.body-overlay')
                                .addClass('body-overlay-on');

                    })
                            .on('click', '.mobile-menu-module ul .toggle-menu', function (e) {

								var $linkIcon = $(this),
									$this = $linkIcon.closest('a');
                                    e.preventDefault();
                                    $this.next('ul').toggle();
                                    if (!$linkIcon.hasClass('menu-close')) {
                                        $linkIcon.addClass('menu-close');
                                    } else {
                                        $linkIcon.removeClass('menu-close');
                                    }

                            }).on('click', '.mobile-menu-module ul a[href="#"]', function (e) {
                                e.preventDefault();
                            })
                            .on('click', '.mobile-menu-module > .menu-close, .mobile-menu-module + .body-overlay', function(){
								var closeClass = 'close-';
								closeClass+= $('.mobile-menu-module').hasClass('right') ? 'right' :'left';
								closeClass+= '-menu';

								Themify.body.addClass(closeClass);

								closeMenu();
							});
                } else {
                    closeMenu();
                }
            }
        },
        GridBreakPoint: function () {
			var tablet_landscape = tbLocalScript.breakpoints.tablet_landscape,
				tablet = tbLocalScript.breakpoints.tablet,
				mobile = tbLocalScript.breakpoints.mobile,
				rows = document.querySelectorAll('.row_inner,.subrow_inner'),
				prev = false;

			function Breakpoints() {
				var width = $(window).width(),
					type = 'desktop';

				if (width <= mobile) {
					type = 'mobile';
				} else if (width <= tablet[1]) {
					type = 'tablet';
				} else if (width <= tablet_landscape[1]) {
					type = 'tablet_landscape';
				}

				if ( type !== prev ) {
					var is_desktop = type === 'desktop',
						set_custom_width = is_desktop || prev === 'desktop';

					if (is_desktop) {
						Themify.body.removeClass( 'tb_responsive_mode' );
					} else {
						Themify.body.addClass( 'tb_responsive_mode' );
					}

					for (var i = 0, len = rows.length; i < len; ++i) {
						var columns = rows[i].children,
							grid = rows[i].dataset['col_' + type],
							first = columns[0],
							last = columns[columns.length - 1],
							base = rows[i].dataset['basecol'];

						if ( set_custom_width ) {
							for (var j = 0, clen = columns.length; j < clen; ++j) {
								var w = columns[j].dataset['w'];
								if (w) {
									if (is_desktop) {
										columns[j].style['width'] = w + '%';
									} else {
										columns[j].style['width'] = '';
									}
								}
							}
						}
						var dir = rows[i].dataset[type + '_dir'];

						if( first && last ) {
							if ( dir === 'rtl' ) {
								first.classList.remove( 'first' );
								first.classList.add( 'last' );
								last.classList.remove('last' );
								last.classList.add( 'first' );
								rows[i].classList.add( 'direction-rtl' );
							} else {
								first.classList.remove( 'last' );
								first.classList.add( 'first' );
								last.classList.remove( 'first' );
								last.classList.add( 'last');
								rows[i].classList.remove( 'direction-rtl' );
							}
						}

						if( base && ! is_desktop ) {
							if (prev !== false && prev !== 'desktop') {
								rows[i].classList.remove( 'tb_3col' );
								var prev_class = rows[i].dataset['col_' + prev];

								if (prev_class) {
									rows[i].classList.remove( $.trim( prev_class.replace( 'tb_3col', '' ).replace( 'mobile', 'column' ).replace( 'tablet', 'column' ) ) );
								}
							}

							if ( ! grid || grid === '-auto' ) {
								rows[i].classList.remove( 'tb_grid_classes' );
								rows[i].classList.remove( 'col-count-' + base );
							} else {
								var cl = rows[i].dataset['col_' + type];

								if (cl) {
									rows[i].classList.add( 'tb_grid_classes' );
									rows[i].classList.add( 'col-count-' + base );
									cl = cl.split(' ');

									cl.map( function( el ) {
										rows[i].classList.add( $.trim( el.replace( 'mobile', 'column' ).replace( 'tablet', 'column' ) ) );
									} );
								}
							}
						}
					}
					prev = type;
				}
			}

			Breakpoints();
			$( window ).on( 'tfsmartresize.themify_grid', function (e) {
				if (!e.isTrigger) {
					Breakpoints();
				}
			} );
        },
        readMoreLink: function () {
            Themify.body.on('click','.module-text-more', function (e) {
                e.preventDefault();
                if($(this).hasClass('tb-text-more-link')) {
                    $(this).removeClass("tb-text-more-link").addClass("tb-text-less-link");
                    $(this).parent().find('.more-text').slideDown(400, "linear");

                } else {
                    $(this).removeClass("tb-text-less-link").addClass("tb-text-more-link");
                    $(this).parent().find('.more-text').slideUp(400, "linear");
                }
            } );
        },
        stickyElementInit: function() {
          if ( ! tbLocalScript.isStickyScrollActive ) return true;

          var self = this;
          self.stickyElementRun();
          $(window).on('tfsmartresize.sticky', function () {
              $('[data-sticky-active]').each(function(){
                $(this).unstick();
              });
              self.stickyElementRun();
          });
        },
        stickyElementRun: function() {
            var body = document.body,
                html = document.documentElement;

            var documentHeight = Math.max( body.scrollHeight, body.offsetHeight, 
                html.clientHeight, html.scrollHeight, html.offsetHeight ),
                wH = window.innerHeight || html.clientHeight || body.clientHeight;

            $('[data-sticky-active]').each(function(){
                var $this = $(this),
                    opts = $this.data('sticky-active'),
                    stickVal = opts.stick.value ? parseInt( opts.stick.value ) : 0,
                    topSpacing = 'px' === opts.stick.val_unit ? stickVal : ( ( stickVal / 100 ) * wH ),
                    stickArgs = { topSpacing: topSpacing, zIndex: null, className: 'tb_sticky_scroll_active' },
                    bottomSpacing = 0;

                if ( 'bottom' === opts.stick.position ) {
                    stickArgs.topSpacing = wH - this.offsetHeight - topSpacing;
                }

                if ( opts.unstick ) {
                    if ( 'builder_end' === opts.unstick.el_type ) {
                      var $builder = $this.closest('.themify_builder_content'),
                          stopAt = $builder.offset().top + $builder.outerHeight(true);

                      stickArgs.bottomSpacing = documentHeight - stopAt;
                    } else {
                      var targetEl = 'row' === opts.unstick.el_type ? opts.unstick.el_row_target : opts.unstick.el_mod_target,
                          $target = $('[data-id="'+ targetEl +'"]'),
                          unstickVal = opts.unstick.value ? parseInt( opts.unstick.value ) : 0,
                          targetTop;

                      if ( '%' === opts.unstick.val_unit ) {
                          unstickVal = ( ( unstickVal / 100 ) * wH );
                          console.log(unstickVal, 'unstickVal');
                      }

                      if ( $target.length ) {
                          targetTop = documentHeight - ( $target.offset().top + this.offsetHeight + topSpacing );
                          
                          if ( 'bottom' === opts.unstick.current ) {
                              if ( 'hits' === opts.unstick.rule ) {
                                targetTop += wH - unstickVal;
                              } else {
                                targetTop += wH - ( $target.outerHeight(true) + unstickVal );
                              }
                          } else if ( 'this' === opts.unstick.current ) {
                            targetTop = documentHeight - $target.offset().top;

                            if ( 'passes' === opts.unstick.rule ) {
                              targetTop -= this.offsetHeight;
                            }
                          } else {
                              if ( 'hits' === opts.unstick.rule ) {
                                targetTop += unstickVal;
                              } else {
                                targetTop -= unstickVal;
                              }
                          }
                          stickArgs.bottomSpacing = targetTop;
                      }
                    }
                }
                $this.sticky( stickArgs );
            });
        },
        alertModule: function(el) {
                var alertBox = $( '.module.module-alert',el );
                if(el && el.hasClass('module-alert')){
                    alertBox = alertBox.add(el);
                }
                var isNumber = function( number ) {
                                return number && ! isNaN( parseFloat( number ) ) && isFinite( number );
                        },
                        setCookie = function( name, value, days ) {
                                var date = new Date();
                                date.setTime( date.getTime() + ( days * 24 * 60 * 60 * 1000 ) );

                                document.cookie = name + "=" + value + ";expires=" + date.toUTCString() + ";path=/";
                        },
                        getCookie = function( name ) {
                                name = name + '=';
                                var ca = document.cookie.split( ';' );

                                for( var i = 0,len=ca.length; i < len; ++i ) {
                                        var c = ca[i];
                                        while( c.charAt(0) === ' ' ) {
                                                c = c.substring( 1 );
                                        }
                                        if ( c.indexOf( name ) === 0)  {
                                                return c.substring( name.length, c.length );
                                        }
                                }
                                return '';
                        },
                        closeAlert = function( $button ) {

                                var buttonMessage, alertBox,
                                        speed = 400;

                                if( $button ) {
                                        buttonMessage = $button.data( 'alert-message' );
                                        alertBox = buttonMessage ? $button.closest( '.alert-inner' ) : $button.closest( '.module-alert' );
                                } else {
                                        alertBox = $( this );
                                }

                                alertBox.slideUp( speed, function() {
                                        if( buttonMessage && ! alertBox.parent().find( '.alert-message' ).length ) {
                                                var message = $( '<div class="alert-message" />' ).html( buttonMessage + '<div class="alert-close ti-close" />' );

                                                alertBox.parent().html( message );
                                                message.hide().slideDown( speed );
                                        }
                                } );
                        };

                alertBox.each( function() {
                        var $this = $( this ),
                                currentViews = 0,
                                currentLimit = 0,
                                alertID = $this.data( 'module-id' ),
                                alertLimit = $this.data( 'alert-limit' ),
                                autoClose = $this.data( 'auto-close' );

                        if( isNumber( alertLimit ) ) {
                                var cookies = getCookie( alertID );

                                if( cookies ) {
                                        cookies = cookies.split( '|' );

                                        if( cookies[1] ) {
                                                currentLimit = + cookies[0];
                                                currentViews = + cookies[1];
                                        }
                                }

                                if( alertLimit !== currentLimit ) {
                                        setCookie( alertID, alertLimit + '|1', 365 );
                                } else if( alertLimit > currentViews ) {
                                        ++currentViews;
                                        setCookie( alertID, alertLimit + '|' + currentViews, 365 );
                                }
                        }

                        if(isNumber( autoClose ) ) {
                                setTimeout( closeAlert.bind( this ), autoClose * 1000 );
                        }
                } );
                Themify.body.on( 'click', '.module-alert .alert-close', function( e ) {
                        e.preventDefault();
                        closeAlert( $( this ) );
                } );
        }
	};

	// Initialize
	ThemifyBuilderModuleJs.init();

}( jQuery, window, document ) );
