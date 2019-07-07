; //defensive semicolon
//////////////////////////////
// Test if touch event exists
//////////////////////////////
function is_touch_device() {
    return jQuery('body').hasClass('touch');
}

function getParameterByName(name, url) {
    name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
    var regexS = "[\\?&]" + name + "=([^&#]*)";
    var regex = new RegExp(regexS);
    var results = regex.exec(url);
    if (results == null)
        return "";
    else
        return decodeURIComponent(results[1].replace(/\+/g, " "));
}

// Begin jQuery functions
(function ($) {

    $.fn.serializeObject = function () {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function () {
            if (o[this.name] !== undefined) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };


    $(document).ready(function () {
        var $body = $('body');
        window.top.jQuery('body').one('themify_builder_ready', function () {
            if ($('.products.loops-wrapper').length > 0) {
                $body.addClass('woocommerce woocommerce-page');
            }
        });

        if ($('.products.loops-wrapper').length > 0) {
            $body.addClass('woocommerce woocommerce-page');
        }
        var first_image = $('.images img').first().prop('src');
        $body.on('woocommerce_update_variation_values', '.variations_form', function (e) {

            if ($('.swiper-wrapper').length > 0) {
                setTimeout(function () {
                    var $current = $('.swiper-wrapper img').first().prop('src');
                    if ($current !== first_image) {
                        first_image = $current;
                        $('.product-images-carousel img').first().data('zoom-image', first_image);

                        $('.thumbnails img').first().prop('src', first_image);
                        if ($('.product-images-carousel').data('swiper')) {
                            $('.product-images-carousel').data('swiper').slideTo(0, 1000, false);
                        }
                    }
                }, 100);
            }
        });

        // Variation zoom carousel fix for Additional Variation Images by WooCommerce
        if (typeof $.wc_additional_variation_images_frontend === 'object') {
            $('form.variations_form').on('wc_additional_variation_images_frontend_image_swap_callback', function (e, response) {
                themifyProductCarousel(response.main_images);
            });

            function themifyProductCarousel(response) {
                var $images = $(response).find('img');
                if ($images.length) {
                    var newCarousel = '<div class="swiper-container"><div class="swiper-wrapper"></div></div>',
                            $imgCarousel = '',
                            $thumbCarousel = '';

                    $('.product-images-carousel, .product-thumbnails-carousel').remove();
                    $imgCarousel = $(newCarousel).addClass('product-images-carousel').appendTo('.woocommerce-product-gallery__wrapper');
                    $thumbCarousel = $(newCarousel).addClass('product-thumbnails-carousel').appendTo('.woocommerce-product-gallery__wrapper');

                    $images.each(function (i) {
                        var $this = $(this),
                                $imgCarouselItem = $('<div data-zoom-image="' + $this.attr('data-large_image') + '" class="swiper-slide woocommerce-main-image woocommerce-product-gallery__image zoom post-image"></div>');

                        if (i === 0) {
                            $imgCarouselItem.append(this.outerHTML);
                        } else {
                            $imgCarouselItem.append('<div class="default_img" style="width:' + $this.attr('width') + 'px; " data-width="' + $this.attr('width') + '" data-height="' + $this.attr('height') + '" data-src="' + $this.attr('src') + '" data-title="' + $this.attr('title') + '" data-alt="' + $this.attr('alt') + '">');
                        }

                        $imgCarousel.find('.swiper-wrapper').append($imgCarouselItem);
                        $thumbCarousel.find('.swiper-wrapper').append('<li class="zoom swiper-slide post-image">' + this.outerHTML + '</li>')
                    });

                    InitGallery();
                    themify_zoom_image();
                }
            }

        }




        /////////////////////////////////////////////
        // Check is_mobile
        /////////////////////////////////////////////
        $body.addClass(is_touch_device() ? 'is_mobile' : 'is_desktop');
        if (is_touch_device()) {
            $('#cart-icon-count>a').click(function (e) {
                e.preventDefault();
            });
        }
        /////////////////////////////////////////////
        // Product slider
        /////////////////////////////////////////////
        var InitProductSlider = function () {

            function ThemifyProductSlider() {
                var direction = $('body').hasClass('rtl');
                $('.product-slider').not('.hovered').each(function (index) {
                    var $slider = $(this).data('product-slider');
                    if ($slider) {
                        var $this = $(this);
                        $this.addClass('hovered').one('mouseover touchstart', function (e) {
                            e.preventDefault();
                            var $product = $this.closest('.product'),
                                    cl = 'slider-' + $product.data('product-id') + '-' + index,
                                    product = $this.closest('.products'),
                                    width = product.data('width'),
                                    height = product.data('height'),
                                    items = '<span class="themify_spinner"></span><div class="themify_swiper_container">';

                            items += '<a href="javascript:void(0);" class="product-slider-arrow product-slider-prev"></a><a href="javascript:void(0);" class="product-slider-arrow product-slider-next"></a>';
                            items += '<div class="swiper-container swiper-container-big"><div class="swiper-wrapper"></div></div>';
                            items += '<div class="swiper-container swiper-container-thumbs"><div class="swiper-wrapper"></div></div></div>';
                            $this.addClass(cl).append(items);

                            $.ajax({
                                url: woocommerce_params.ajax_url,
                                type: 'POST',
                                dataType: 'json',
                                data: {'action': 'themify_product_slider', 'slider': $slider, 'width': width, 'height': height},
                                beforeSend: function () {
                                    $this.addClass('slider-loading');
                                },
                                success: function (result) {
                                    if (result) {

                                        var top_items = '',
                                                thumb_items = '',
                                                url = $this.data('product-link') ? $this.data('product-link') : false,
                                                big = $this.find('.swiper-container-big').children('.swiper-wrapper'),
                                                thumbs = $this.find('.swiper-container-thumbs').children('.swiper-wrapper');

                                        for (var i in result.big) {
                                            top_items += '<div  class="swiper-slide">';
                                            if (url) {
                                                top_items += '<a href="' + url + '">';
                                            }
                                            top_items += '<img src="' + result.big[i] + '"/>';
                                            if (url) {
                                                top_items += '</a>';
                                            }
                                            top_items += '</div>';
                                            thumb_items += '<div class="swiper-slide"><img src="' + result.thumbs[i] + '"/></div>';
                                        }
                                        big.html(top_items);
                                        thumbs.html(thumb_items);
                                        big.imagesLoaded(function () {
                                            var galleryThumbs,
                                                    galleryTop = new Swiper($('.' + cl).find('.swiper-container-big'), {
                                                        navigation: {
                                                            nextEl: $('.' + cl).find('.product-slider-next'),
                                                            prevEl: $('.' + cl).find('.product-slider-prev'),
                                                        },
                                                        loop: 1,
                                                        autoplay: {
                                                            delay: 2500,
                                                            disableOnInteraction: false
                                                        },
                                                        rtl: direction,
                                                        normalizeSlideIndex: false,
                                                        slidesPerView: 1,
                                                        speed: 1500,
                                                        zoom: 1,
                                                        on: {
                                                            slideChangeTransitionStart: function () {
                                                                if (galleryThumbs) {
                                                                    galleryThumbs.slideTo(galleryTop.realIndex, galleryTop.speed, false);
                                                                }
                                                            },
                                                            init: function (top_swiper) {
                                                                galleryThumbs = new Swiper($('.' + cl).find('.swiper-container-thumbs'), {
                                                                    slidesPerView: 'auto',
                                                                    slideToClickedSlide: true,
                                                                    normalizeSlideIndex: false,
                                                                    virtualTranslate: true,
                                                                    rtl: direction,
                                                                    spaceBetween: 0,
                                                                    nested: true,
                                                                    on: {
                                                                        init: function () {
                                                                            $this.removeClass('slider-loading').addClass('slider-finish');

                                                                        },
                                                                        click: function () {
                                                                            if (galleryTop) {
                                                                                var index = $('.' + cl).find('.swiper-slide[data-swiper-slide-index="' + galleryThumbs.realIndex + '"]').not('.swiper-slide-duplicate').index();
                                                                                galleryTop.slideTo(index, galleryTop.speed, false);
                                                                            }
                                                                        }
                                                                    }
                                                                });
                                                            }
                                                        }

                                                    });
                                        });

                                    }
                                }
                            });
                        });
                    }
                });
            }
            if ($('.product-slider').not('.hovered').length > 0 && !$('body').hasClass('wishlist-page')) {
                if (typeof Swiper === 'undefined') {
                    Themify.LoadAsync(themifyScript.theme_url + '/js/swiper.jquery.min.js', ThemifyProductSlider,
                            null,
                            null,
                            function () {
                                return ('undefined' !== typeof Swiper);
                            });
                }
                else {
                    ThemifyProductSlider();
                }

            }
        };



        var InitGallery = function () {

            function ThemifySliderGallery() {
                var productImage = $('.product-images-carousel:not(.themify_swiper_ready)').first();
                productImage.imagesLoaded(function () {
                    if ($('.swiper-slide', productImage).length <= 1) {
                        productImage.addClass('themify_swiper_ready').closest('.images').find('.themify_spinner').remove();
                        return;
                    }
                    var thumbs = productImage.parent().find('.product-thumbnails-carousel').first(),
                            galleryThumbs = new Swiper(thumbs, {
                                direction: "vertical",
                                slidesPerView: 'auto',
                                freeMode: true,
                                watchSlidesVisibility: true,
                                watchSlidesProgress: true,
                                on: {
                                    init: function () {
                                        thumbs.addClass('themify_show').closest('.images').find('.themify_spinner').remove();
                                    }
                                }
                            });
                            new Swiper(productImage, {
                                slideToClickedSlide: true,
                                on: {
                                    init: function (swiper) {
                                        productImage.addClass('themify_swiper_ready').closest('.images').find('.themify_spinner').remove();
                                    },
                                    slideChangeTransitionStart: function () {
                                        var top_el = productImage.find('.swiper-slide-active'),
                                                img = top_el.children('div.default_img');
                                        if (img.length > 0) {
                                            img.replaceWith('<span class="themify_spinner"></span><img class="swiper_img_progress" src="' + img.data('src') + '" width="' + img.data('width') + '" height="' + img.data('height') + '" alt="' + img.data('alt') + '" title="' + img.data('title') + '" />').imagesLoaded(function () {
                                                top_el.children('img.swiper_img_progress').addClass('swiper_img_loaded').one('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend', function (e) {
                                                    $(this).prev('.themify_spinner').remove();
                                                });
                                            });
                                        }
                                    }
                                },
                                navigation: {
                                    nextEl: '.swiper-button-next',
                                    prevEl: '.swiper-button-prev'
                                },
                                thumbs: {
                                    swiper: galleryThumbs
                                }
                            });

                });

                // Variation product slide to right image
                $('img', productImage).on('click', function () {
                    var swiperData = thumbs.data('swiper');

                    if (swiperData) {
                        swiperData.slideTo($('li', productImage).index($(this).parent()));
                    }
                });
            }
            if ($('.swiper-container .swiper-slide').length > 0) {
                if (typeof Swiper === 'undefined') {
                    Themify.LoadAsync(themifyScript.theme_url + '/js/swiper.jquery.min.js', ThemifySliderGallery,
                            null,
                            null,
                            function () {
                                return ('undefined' !== typeof Swiper);
                            });
                }
                else {
                    ThemifySliderGallery();
                }
            }
        }

        $(document).ajaxComplete(function (e, request, settings) {
            if ($('.product-slider').not('.hovered').length > 0) {
                InitProductSlider();
            }
        });
        InitGallery();
        InitProductSlider();

        if (typeof themifyShop.wishlist !== 'undefined') {
            Themify.LoadAsync(themifyScript.theme_url + '/js/themify.wishlist.js', null, null, null);
        }

        // Set Slide Cart Menu /////////////////////////
        $('#cart-link').themifySideMenu({
            panel: '#slide-cart',
            close: '#cart-icon-close'
        });

        //Remove brackets
        var product_category_count = $('.widget_product_categories .count');
        if (product_category_count.length > 0) {
            product_category_count.each(function () {
                $(this).text($(this).text().replace('(', '').replace(')', ''));
            });
        }


        $body.on('wc_fragments_refreshed', function () {
            $('.is_mobile #cart-wrap').show();
        });

        /////////////////////////////////////////////
        // Add to cart ajax
        /////////////////////////////////////////////
        if (woocommerce_params.option_ajax_add_to_cart == 'yes') {

            // Ajax add to cart   
            $body.on('adding_to_cart', function (e, $button, data) {
                add_to_cart_spark($button);
            }).on('added_to_cart removed_from_cart', function (e, fragments, cart_hash) {
                $('.is_mobile #cart-wrap').show();
                // close lightbox
                if ($.fn.prettyPhoto && $('.pp_inline').is(':visible')) {
                    $.prettyPhoto.close();
                }
                if ($('.mfp-content.themify_product_ajax').is(':visible')) {
                    $.magnificPopup.close();
                }

                var cartButton = $('#cart-icon-count');
                if (!cartButton.hasClass('empty-cart') && parseInt(cartButton.find('.icon-menu-count').text()) <= 0) {
                    cartButton.addClass('empty-cart');
                } else if (cartButton.hasClass('empty-cart')) {
                    cartButton.removeClass('empty-cart');
                }

            });

            // remove item ajax
            $(document).on('click', '.remove-item-js', function (e) {
                e.preventDefault();
                // AJAX add to cart request
                var $thisbutton = $(this),
                        data = {
                            action: 'theme_delete_cart',
                            remove_item: $thisbutton.attr('data-product-key')
                        };
                $thisbutton.addClass('themify_spinner');
                // Ajax action
                $.post(woocommerce_params.ajax_url, data, function (response) {
                    var fragments = response.fragments,
                            cart_hash = response.cart_hash;

                    // Changes button classes
                    if ($thisbutton.parent().find('.added_to_cart').size() == 0)
                        $thisbutton.addClass('added');

                    // Replace fragments
                    if (fragments) {
                        $.each(fragments, function (key, value) {
                            $(key).addClass('updating').replaceWith(value);
                        });
                    }

                    // Trigger event so themes can refresh other areas
                    $('body').trigger('removed_from_cart', [fragments, cart_hash]);
                    $thisbutton.removeClass('themify_spinner');
                    if ($('#cart-icon-count').hasClass('cart_empty')) {
                        $body.addClass('wc-cart-empty');
                    }
                });
            });

            // Ajax add to cart in single page
            if (!themifyScript.ajaxSingleCart) {
                ajax_add_to_cart_single_page();
            }

        }

        function add_to_cart_spark(item) {
            if (typeof clickSpark !== 'undefined') {
                clickSpark.setParticleText("\ue60d");
                clickSpark.setParticleColor(window.sparkling_color);
                clickSpark.setParticleDuration(300);
                clickSpark.setParticleCount(15);
                clickSpark.setParticleSpeed(8);
                clickSpark.setAnimationType('splash');
                clickSpark.setParticleRotationSpeed(0);
                clickSpark.fireParticles(item);
            }
        }
        // reply review
        $('.reply-review').click(function () {
            $('#respond').slideToggle('slow');
            return false;
        });

        // add review
        $('.add-reply-js').click(function () {
            $(this).hide();
            $('#respond').slideDown('slow');
            $('#cancel-comment-reply-link').show();
            return false;
        });
        $('#reviews #cancel-comment-reply-link').click(function () {
            $(this).hide();
            $('#respond').slideUp();
            $('.add-reply-js').show();
            return false;
        });

        /*function ajax add to cart in single page */
        function ajax_add_to_cart_single_page() {
            $(document).on('submit', 'form.cart', function (e) {
				if ( $( this ).closest( '.product-type-external' ).length ) {
					return;
				}
                // WooCommerce Subscriptions plugin compatibility
                if (window.location.search.indexOf('switch-subscription') > -1)
                    return this;

                e.preventDefault();

                var data = new FormData(this);

                if ($(this).find('input[name="add-to-cart"]').length === 0) {
                    data.append('add-to-cart', $(this).find('[name="add-to-cart"]').val());
                }

                data.append('action', 'theme_add_to_cart');
                $('body').trigger('adding_to_cart', [$(this).find('[type="submit"]'), data]);

                var xhr,
                        _orgAjax = $.ajaxSettings.xhr,
                        currentLocation = window.location.href;

                $.ajaxSettings.xhr = function () {
                    xhr = _orgAjax();
                    return xhr;
                };

                // Ajax action
                $.ajax({
                    url: woocommerce_params.ajax_url,
                    type: "POST",
                    data: data,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function (response) {
                        if (!response) {
                            return;
                        }
                        if (themifyShop.redirect) {
                            window.location.href = themifyShop.redirect;
                            return;
                        }

                        if (!response.fragments && currentLocation !== xhr.responseURL) {
                            window.location.href = xhr.responseURL;
                            return;
                        }

                        var fragments = response.fragments,
                                cart_hash = response.cart_hash;

                        // Block fragments class
                        if (fragments) {
                            $.each(fragments, function (key, value) {
                                $(key).addClass('updating').replaceWith(value);
                            });
                        }

                        // Trigger event so themes can refresh other areas
                        $('body').trigger('added_to_cart', [fragments, cart_hash]);
                    }
                });
            });
        }

        /**
         * Limit the number entered in the quantity field.
         * @param $obj The quantity field object.
         * @param max_qty The max quantity allowed per the inventory current stock.
         */
        function limitQuantityByInventory($obj, max_qty) {
            var qty = $obj.val();
            if (qty > max_qty) {
                $obj.val(max_qty);
            }
        }

        function lightboxCallback(context) {
            $("a.variable-link", context).each(function () {
                $(this).magnificPopup({
                    type: 'ajax',
                    callbacks: {
                        updateStatus: function (data) {
                            $('.mfp-content').addClass('themify_product_ajax themify_variable_product_ajax');
                            ajax_variation_callback();
                        }
                    }
                });
            });
        }
        function ajax_variation_lightbox(context) {
            if ($("a.variable-link", context).length > 0) {
                Themify.LoadCss(themify_vars.url + '/css/lightbox.css', null);
                Themify.LoadAsync(themify_vars.url + '/js/lightbox.min.js', function () {
                    lightboxCallback(context)
                    return ('undefined' !== typeof $.fn.magnificPopup);
                });
            }
        }

        if (themifyScript.variableLightbox) {
            ajax_variation_lightbox(document);
            // Ajax variation lightbox for infinite scroll items
            $(document).on('newElements', function () {
                ajax_variation_lightbox($('.infscr_newElements'));
            });
        }

        /////////////////////////////////////////////
        // Themibox - Themify Lightbox
        /////////////////////////////////////////////
       
        /* Initialize Themibox */
        if ($('.product').length) {
            if ('undefined' === typeof Themibox) {
                Themify.LoadAsync(themifyScript.theme_url + '/js/themibox.js', function () {
                    Themibox.init();
                },
                        null,
                        null,
                        function () {
                            return ('undefined' !== typeof Themibox);
                        });
            }
            else {
                Themibox.init();
            }
        }
        /* Initialize variations when Themibox is loaded */
        $body.on('themiboxloaded', function (e) {
            ajax_variation_callback();
            // Limit number entered manually in quantity field in single view
            if ($body.hasClass('single-product') || $body.hasClass('post-lightbox')) {
                $('.entry-summary').on('keyup', 'input[name="quantity"][max]', function () {
                    limitQuantityByInventory($('input[name="quantity"]'), parseInt($(this).attr('max'), 10));
                });
            }

            if ($.fn.prettyPhoto) {
                // Run WooCommerce PrettyPhoto after Themibox is loaded
                $(".thumbnails a[data-rel^='prettyPhoto']").prettyPhoto({
                    hook: 'data-rel',
                    social_tools: false,
                    theme: 'pp_woocommerce',
                    horizontal_padding: 20,
                    opacity: 0.8,
                    deeplinking: false
                });
            }
            else {
                InitGallery();
            }
            themify_zoom_image();

            if (typeof themifyShop !== 'undefined' && themifyShop.is_default_gallery && $.fn.wc_product_gallery) {
                $('.woocommerce-product-gallery').each(function () {
                    $(this).wc_product_gallery();
                });
            }
        });
        
        $('.thumbnails a').click(function () {
            $('.product_zoom.zoomed').trigger('click');
        });
        $body.on('themiboxclosed themiboxcanceled', function (e) {
            $('#post-lightbox-wrap').removeClass('lightbox-message');
            themify_remove_image_zoom();
        })
                .on('added_to_cart', function (e) {
                    var $postLightboxContainer = $('#post-lightbox-container'),
						$lightboxAdded = $('.lightbox-added').clone(true);

                    $('#post-lightbox-wrap').addClass('lightbox-message');
                    $postLightboxContainer.slideUp(400, function () {
                        $(this).empty();
                        $lightboxAdded.appendTo($(this)).show();
                        $(this).slideDown();
                        themify_remove_image_zoom();
                        $('.close-themibox', $lightboxAdded).one('click',Themibox.closeLightBox);
                    });

                    $('.added_to_cart:not(.button)').addClass('button');
                    if ($postLightboxContainer.find('#pagewrap').length === 0) {
                        var slideCart = $('#slide-cart');
                        if (slideCart.length > 0) {
                            slideCart.removeClass('sidemenu-on').addClass('sidemenu-off');
                            setTimeout(function () {
                                slideCart.removeClass('sidemenu-off').addClass('sidemenu-on');
                                setTimeout(function () {
                                        slideCart.removeClass('sidemenu-on').addClass('sidemenu-off');

                                }, +themifyScript.ajaxCartSeconds || 1000);
								
                            }, +themifyScript.ajaxCartSeconds || 1000);
                        }
                        else {
                            slideCart = $('#cart-icon-count, #cart-link-mobile #shopdock');
                            slideCart.removeClass('show_cart');
                            setTimeout(function () {
                                slideCart.addClass('show_cart');
                                setTimeout(function () {
                                       slideCart.removeClass('show_cart');
                               }, +themifyScript.ajaxCartSeconds || 1000);
								
                            }, +themifyScript.ajaxCartSeconds || 1000);
                        }
                    }
                    $body.removeClass('wc-cart-empty');
                });

        // Routines for single product
        if ($body.hasClass('single-product')) {
            // Limit number entered manually in quantity field in single view
            $('.entry-summary').on('keyup', 'input[name="quantity"][max]', function () {
                limitQuantityByInventory($('input[name="quantity"]'), parseInt($(this).attr('max'), 10));
            });

            // Add +/- plus/minus buttons to quantity input in single view
            $("div.quantity:not(.buttons_added), td.quantity:not(.buttons_added)").addClass('buttons_added').append('<input type="button" value="+" id="add1" class="plus" />').prepend('<input type="button" value="-" id="minus1" class="minus" />');

            themify_zoom_image();
        }

        $(document).on('click', '.plus, .minus', function () {

            // Get values
            var $qty = $(this).closest('.quantity').find('.qty'),
                    currentVal = parseFloat($qty.val()),
                    max = parseFloat($qty.prop('max')),
                    min = parseFloat($qty.prop('min')),
                    step = parseFloat($qty.prop('step'));

            // Format values
            if (!currentVal) {
                currentVal = 1;
            }
            if (!max) {
                max = false;
            }
            if (!min) {
                min = false;
            }
            if (!step) {
                step = 1;
            }
            // Change the value
            if ($(this).hasClass('plus')) {
                currentVal = max && currentVal >= max ? max : currentVal + step;
            } else {
                currentVal = min && currentVal <= min ? min : (currentVal > step ? currentVal - step : currentVal);
            }
            // Trigger change event
            $qty.val(currentVal).trigger('change');
        }).on('keyup', 'form.cart input[name="quantity"]', function () {
            var $max = parseFloat($(this).prop('max'));
            if ($max > 0) {
                limitQuantityByInventory($(this), parseInt($max, 10));
            }
        });


        function themify_remove_image_zoom() {
            if ($.fn.zoom) {
                $('.woocommerce-main-image.zoom img').trigger('zoom.destroy');
            }
        }

        function themify_zoom_init(el, url) {
            var productZoom = el.find('.product_zoom'),
                    currentImage = el.find('img:not(.zoomImg)'),
                    runZoom = function () {
                        productZoom.off('click.runZoom').one('click.runZoom', function (e) {
                            e.preventDefault();
                            e.stopImmediatePropagation();

                            var $this = $(this);
                            el.addClass('zoom_progress');

                            $this.after('<span class="themify_spinner"></span>');
                            el.prop('href', 'javascript:void(0)');
                            el.zoom({
                                on: 'click',
                                url: url || el.data('zoom-image'),
                                callback: function () {
                                    $this.next('.themify_spinner').remove();
                                    el.removeClass('zoom_progress').trigger('click.zoom');
                                    $(this).css({'top': -($(this).height() / 2) + 120, 'left': -($(this).width() / 2) + 120});
                                },
                                onZoomIn: function () {
                                    $this.addClass('zoomed')
                                },
                                onZoomOut: function () {
                                    productZoom.removeClass('zoomed');
                                }
                            }).trigger('click.zoom');
                        });
                    };

            if (!productZoom.length) {
                productZoom = $('<span class="product_zoom"></span>').prependTo(el);
            }

            runZoom();

            // 3-rd party plugins compatibility

            if ('MutationObserver' in window && currentImage.length) {
                var watchImgSrc = new MutationObserver(function (mutations) {
                    var img = mutations[0].target;

                    if (img) {
                        url = img.src;
                        themify_remove_image_zoom();
                        runZoom();
                    }
                });

                watchImgSrc.observe(currentImage.get(0), {
                    attributes: true,
                    attributeFilter: ['src']
                });
            }
        }

        function themify_zoom_image() {

            function themify_zoom_image_callback() {
                var $link = $('.woocommerce-main-image.zoom');
                $link.each(function () {
                    var $this = $(this);
                    themify_zoom_init($this);
                });
            }
            if (!$.fn.zoom) {
                Themify.LoadAsync(themifyShop.theme_url + '/js/jquery.zoom.min.js', themify_zoom_image_callback, themifyShop.version, null, function () {
                    return ('undefined' !== typeof $.fn.zoom);
                });
            }
            else {
                themify_zoom_image_callback();
            }

        }


        /* function ajax variation callback */
        function ajax_variation_callback() {
            var forms = $('.variations_form');
            if (forms.length) {
                Themify.LoadAsync(themify_vars.includesURL + 'js/underscore.min.js', function () {
                    Themify.LoadAsync(themify_vars.includesURL + 'js/wp-util.min.js', function () {
                        Themify.LoadAsync(themifyShop.wc_variation_url, function () {
                            if (typeof wc_add_to_cart_variation_params === 'undefined') {
                                wc_add_to_cart_variation_params = themifyShop.variations_text;
                            }
                            forms.wc_variation_form();
                        }, themifyShop.wc_version, null, function () {
                            return ('undefined' !== typeof $.fn.wc_variation_form);
                        });
                    }, null, null, function () {
                        return ('undefined' !== typeof window._wpUtilSettings);
                    });
                }, null, null, function () {
                    return ('undefined' !== typeof window._);
                });
            }
        }

        /* Variation fix */
        (function () {
            var variationImage = $('.images .woocommerce-main-image.zoom').eq(0),
                    zoomImage, originalImage, itemTimeout;

            $body.on('found_variation', '.variations_form', function (e, v) {
                if (!variationImage.length) {
                    variationImage = $('.images .woocommerce-main-image.zoom').eq(0);
                }

                zoomImage = variationImage.find('.zoomImg');

                if (!originalImage) {
                    originalImage = variationImage.data('zoom-image');
                }

                if (typeof v.image.full_src === 'string') {
                    variationImage.attr('data-zoom-image', v.image.full_src);

                    if (zoomImage.length) {
                        zoomImage.remove();
                    }

                    variationImage.find('.product_zoom').remove();
                    themify_zoom_init(variationImage, v.image.full_src);
                }

                // Check if is selected the right variation
                if (v.image.full_src) {
                    var imageSrc = v.image.full_src.replace(/\.[^\.]+$/, ''),
                        currentImage = $('.product-thumbnails-carousel > ul img[src*="' + imageSrc + '"]:first');

                    if (currentImage.length && !currentImage.parent().hasClass('swiper-slide-active')) {
                        clearTimeout(itemTimeout);
                        itemTimeout = setTimeout(function () {
                            currentImage.trigger('click');
                        }, 1000);
                    }
                }
            }).on('hide_variation', function () {
                zoomImage = variationImage.find('.zoomImg');

                if (originalImage) {
                    zoomImage.remove();
                    themify_zoom_init(variationImage, originalImage);
                    originalImage = '';
                }
            });
        })();
    });

}(jQuery));
