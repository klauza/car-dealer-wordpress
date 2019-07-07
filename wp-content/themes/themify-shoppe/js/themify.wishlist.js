/*
 * Themify Wishlist Plugin
 */
var ThemifyWishilist;
(function ($) {
    'use strict';

    ThemifyWishilist = {
        cookie: themifyShop.wishlist.cookie,
        expires: Date.prototype.toUTCString.call( new Date( themifyShop.wishlist.expiration * 1000 ) ),
        path: themifyShop.wishlist.cookie_path,
        domain: themifyShop.wishlist.domain,
        init: function () {
            this.addCart();
            this.removeCart();
        },
        isCookieEnbled: function () {
            return navigator.cookieEnabled;
        },
        getTotal: function () {
            var cookies = this.getCookie();
            return cookies.length;
        },
        getCookie: function () {
            var cookie = " " + document.cookie,
                    search = " " + this.cookie + "=",
                    setStr = [],
                    offset = 0,
                    end = 0;
            if (cookie.length > 0) {
                offset = cookie.indexOf(search);
                if (offset != -1) {
                    offset += search.length;
                    end = cookie.indexOf(";", offset)
                    if (end == -1) {
                        end = cookie.length;
                    }
                    var arr = JSON.parse(unescape(cookie.substring(offset, end)));
                    for (var x in arr) {
                        setStr.push(arr[x]);
                    }
                }
            }
            return setStr;
        },
        delCookie: function () {
            document.cookie = this.cookie + "=" + "; expires=Thu, 01 Jan 1970 00:00:01 GMT;path=" + this.path + ";";
        },
        removeItem: function (value) {
            value = parseInt(value);
            var cookies = this.getCookie(),
                index = $.inArray(value, cookies);
            if (index !== -1) {
                cookies.splice(index, 1);
                this.setCookie(cookies);
                return true;
            }
            return false;
        },
        setValue: function (value) {
            value = parseInt(value);
            var cookies = this.getCookie();
            if ($.inArray(value, cookies) === -1) {
                cookies.push(value);
                this.setCookie(cookies);
                return true;
            }
            return false;
        },
        setCookie: function (cookies) {
            document.cookie = this.cookie + "=" + JSON.stringify(cookies) +
                "; expires=" + this.expires +
                "; path=" + this.path +";";
        },
        response: function (item, count, remove) {
            var total = count ? count : this.getTotal(),
                el = $('.wishlist .icon-menu-count');
			if(el.length>0){
				if (total > 0) {
					el.removeClass('wishlist_empty');
				}
				else {
					el.addClass('wishlist_empty');
				}
				el.replaceWith(el[0].outerHTML);
				$('.wishlist .icon-menu-count').text(total);
			}
            if (remove) {
                if ($('#wishlist-wrapper').length > 0) {
                    item.closest('.product').fadeOut(function () {
                        $(this).remove();
                        if ($('.wishlisted').length === 0) {
                            $('#wishlist-wrapper').html('<p class="themify_wishlist_no_items">' + themifyShop.wishlist.no_items + '</p>');
                        }
                    });
                }
                else {
                    item.removeClass('wishlisted');
                }
            }
            else {
                item.addClass('wishlisted');
            }
            //Set ClickSpark events//
            if (typeof clickSpark !== 'undefined') {
                clickSpark.setParticleText("\ue634");
                clickSpark.setParticleColor(window.sparkling_color);
                clickSpark.setParticleDuration(500);
                clickSpark.setParticleCount(15);
                clickSpark.setParticleSpeed(8);
                clickSpark.setAnimationType('explosion');
                clickSpark.setParticleRotationSpeed(20);
                clickSpark.fireParticles(item);
            }

        },
        addCart: function () {
            var self = this;

            $('body').delegate('.wishlist-button', 'click', function (e) {
                e.preventDefault();
                if ($(this).hasClass('wishlisted')) {
                    return;
                }
                var item = $(this);
                if (self.isCookieEnbled()) { 
                    if (self.setValue($(this).data('id'))) {
                        self.response(item, false, false);
                    }

                }
                else {
                    //trying to set cookie by php
                    $.ajax({
                        url: this,
                        success: function (resp) {
                            if (resp) {
                                self.response(item, resp, false);
                            }
                        }
                    });
                }

            });
        },
        removeCart: function () {
            var self = this;
            $('body').delegate('.wishlisted', 'click', function (e) {
                e.preventDefault();
                var item = $(this);
                if (self.isCookieEnbled()) {
                    if (self.removeItem($(this).data('id'))) {
                        self.response(item, false, true);
                    }
                }
                else {
                    //trying to set cookie by php
                    $.ajax({
                        url: this,
                        data: {'type': 'remove'},
                        success: function (resp) {
                            self.response(item, resp, true);
                        }
                    });
                }

            });
        }
    };
    ThemifyWishilist.init();

})(jQuery);