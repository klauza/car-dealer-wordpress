var Themibox = {};

(function ($) {

    Themibox = {
        topOffset: '50%',
        init: function (config) {
            // private
            this.isFrameLoading = false;
            this.lightboxOpen = false;

            if ($('body').hasClass('iphone')) {
                window.addEventListener('orientationchange', this.doOnOrientationChange);
            }

            // public
            this.config = config;
            this.bindEvents();
            this.setupLightbox();
        },
        doOnOrientationChange: function () {
            if ($('body').hasClass('post-lightbox')) {
                window.setTimeout(function () {
                    Themibox.topOffset = $(window).scrollTop();
                    $("#post-lightbox-wrap").animate({top: Themibox.topOffset}, 300);
                }, 300);
            }
        },
        bindEvents: function () {
            var $body = $('body');

            $body.on('click', '.themify-lightbox', this.clickLightBox);
            $body.on('click', '.close-lightbox', this.closeLightBox);
            $(document).keyup(this.keyUp);
            // Set up overlay
            $('<div/>', {id: 'pattern'}).appendTo('body').on('click', this.closeLightBox);

        },
        setupLightbox: function () {
            $('<div id="post-lightbox-wrap">' + themifyShop.themibox.close + '<div id="post-lightbox-container"><div class="carousel"></div></div></div><a href="#" class="lightbox-direction-nav lightbox-prev">»</a><a href="#" class="lightbox-direction-nav lightbox-next">«</a>')
                    .hide()
                    .prependTo('body');
        },
        clickLightBox: function (e) {
			e.preventDefault();
			
			if (Themibox.isFrameLoading) return false;

			var url = $(this).prop('href'),
				img = $(this).closest('.product').find('.product-image img'),
				container = $('#post-lightbox-container'),
				wrap = $("#post-lightbox-wrap"),
				width = 960,
				$body = $('body'),
				item = $(this).closest('.post').find('.product-image'),
				w;

			img = img.length ? img.prop( 'src' ) : $( this ).data( 'image' );
			img = typeof img !== 'undefined' ? img : themifyShop.placeholder;
			item = item.length ? item : $( this ).closest( '.post' );
			w = item.outerWidth( true ) < 180 ? item.outerWidth( true ) : 180;

            $('.themibox-clicked').removeClass('themibox-clicked');
            $(this).addClass('themibox-clicked');
            Themibox.isFrameLoading = true;
            $body.addClass('post-lightbox');
            $body.css('overflow-y', 'hidden');
            wrap.hide().removeClass('lightbox-message').addClass('post-lightbox-prepare');
            container.html('<div class="post-lightbox-iframe"><div class="post-lightbox-main-image"><img src="' + img + '"/></div></div>');
            wrap.css({'width': item.outerWidth(true), 'height': item.outerHeight(true), 'top': item.offset().top - $(window).scrollTop() + (item.outerHeight(true) / 2), 'left': item.offset().left - $(window).scrollLeft() + parseInt(item.outerWidth(true) / 2)}).
                    fadeIn().animate(
                    {
                        'width': w,
                        'top': '50%',
                        'left': '50%'
                    },
            'fast',
                    function () {
                        $.ajax({
                            url: url,
                            beforeSend: function () {
                                $('#pattern').hide().fadeIn(300);
                                container.addClass('post-lightbox-flip-infinite');
                            },
                            success: function (resp) {
                                var $loaded = true;
                                $('<div class="post-lightbox-temp" id="post-lightbox-wrap"><div id="post-lightbox-container"><div class="post-lightbox-iframe">' + resp + '</div></div></div>').prependTo('body').imagesLoaded().always(function () {
                                    var outherheight = $('#post-lightbox-wrap.post-lightbox-temp').outerHeight(true);
                                    $('#post-lightbox-wrap.post-lightbox-temp').remove();
                                    container.children('.post-lightbox-iframe').append(resp);
                                    container.one('animationiteration webkitAnimationIteration oanimationiteration MSAnimationIteration', function () {
                                        if ($loaded) {
                                            $loaded = false;
                                            $(this).removeClass('post-lightbox-flip-infinite');
                                            wrap.addClass('animate_start').animate({
                                                'width': width,
                                                'height': outherheight,
                                            }, 'normal', function () {
                                                if ($body.hasClass('iphone')) {
                                                    Themibox.topOffset = $(window).scrollTop() + 10;
                                                }

                                                $('.lightbox-direction-nav').show();

                                                var prev = container.find('.post-nav .prev a'),
                                                        next = container.find('.post-nav .next a');

                                                if (prev.length === 0) {
                                                    $('.lightbox-prev').hide();
                                                }

                                                if (next.length === 0) {
                                                    $('.lightbox-next').hide();
                                                }
                                                // also for the form should exit the lightbox
                                                container.find("form").attr('target', '_top');
                                                Themibox.isFrameLoading = false; // update current status
                                                Themibox.lightboxOpen = true;
                                                $body.trigger('themiboxloaded');
                                                $(this).removeClass('post-lightbox-prepare animate_start');
                                            });
                                        }
                                    });

                                });
                            }
                        });

                    });
        },
        closeLightBox: function (e) {
            e.preventDefault();
            if (Themibox.lightboxOpen) {
                $('#pattern').fadeOut(300, function () {
                    var wrap = $("#post-lightbox-wrap");
                    if (wrap.hasClass('lightbox-message')) {
                        wrap.animate({
                            top: Math.max(
                                    Math.max(document.body.scrollHeight, document.documentElement.scrollHeight),
                                    Math.max(document.body.offsetHeight, document.documentElement.offsetHeight),
                                    Math.max(document.body.clientHeight, document.documentElement.clientHeight)
                                    )
                        }, 800, function () {
                            $('body').removeClass('post-lightbox').css('overflow-y', 'visible');
                            $('.post-lightbox-iframe').empty();
                            $(window).resize(); // fix issue
                            $('body').trigger('themiboxclosed');
                            Themibox.lightboxOpen = false;
                        });
                    }
                    else {

						var item = $('.themibox-clicked').closest('.post').find('.product-image'),
							thumb = $('.post-lightbox-main-image');

						item = item.length ? item : $( '.themibox-clicked' ).closest( '.post' );

                        wrap.addClass('animate_start post-lightbox-prepare animate_closing').animate({
                            'width': item.outerWidth(true),
                            'height': item.outerHeight(true),
                        }, 'normal', function () {
                            $('.lightbox-direction-nav').hide();
                            $(this).find('#pagewrap').remove();
                            $(this).removeClass('animate_start animate_closing').delay(100).animate({'top': item.offset().top - $(window).scrollTop() + (item.outerHeight(true) / 2), 'left': item.offset().left - $(window).scrollLeft() + parseInt(item.outerWidth(true) / 2)}, 'normal', function () {
                                $(this).fadeOut();
                                $('.post-lightbox-iframe').empty();
                                $('body').removeClass('post-lightbox').css('overflow-y', 'visible');
                                $(window).resize(); // fix issue
                                $('body').trigger('themiboxclosed');
                                Themibox.lightboxOpen = false;
                            });
                        });
                    }
                });
            }
        },
        keyUp: function (e) {
            if (Themibox.lightboxOpen && e.keyCode == 27) {
                $('.close-lightbox').trigger('click');
            }
        }
    };
})(jQuery);