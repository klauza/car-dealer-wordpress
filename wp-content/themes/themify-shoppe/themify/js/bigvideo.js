/*Themify Background Video*/
(function ($, window, document) {

    var pluginName = 'ThemifyBgVideo',
            isInitialized = false,
            defaults = {
                url: false,
                autoPlay: true,
                doLoop: false,
                ambient: false,
                id: '',
                onload:null,
                onStart: null,
                onPlay: null,
                onPause: null,
                onEnd: null
            };
    function Plugin(element, options) {
        this.element = element;
        this.options = $.extend({}, defaults, options);
        this.init();
    }
    Plugin.prototype = {
        isPlaying: false,
        mediaAspect: 16 / 9,
        prefix:'tf-',
        items: {},
        player: null,
        init: function () {
            if (this.options.url) {

                this.player = document.createElement('video');
                this.player.id = this.prefix + this.options.id;
                this.player.preload = 'auto';
                this.player.className = 'tf-video';
                this.player.width = 1;
                this.player.height = 1;
                this.player.src = this.options.url;
                this.player.setAttribute('webkit-playsinline', 1);
                if (this.options.doLoop) {
                    this.player.loop = true;
                }
                 if (this.options.autoPlay) {
                    this.player.autoplay = true;
                    this.options.ambient = true;
                }
                 if (this.options.ambient) {
                    this.player.muted = true;
                    this.player.setAttribute('muted', 'muted');
                }
                var self = this;
                if ($.isFunction(this.options.onload)) {
                    this.player.addEventListener('loadedmetadata',function(e){
                        self.options.onload.call(self, e);
                    });
                }
                function bind(e) {
                    self.player.removeEventListener('canplay', bind, false);
                    self.bindEvents(e);
                }
                self.player.addEventListener('canplay', bind);
                $('<div class="big-video-wrap">').append(this.player).prependTo(this.element);

            }
        },
        bindEvents: function (e) {
            var self = this;
            if ($.isFunction(self.options.onStart)) {
                self.options.onStart.call(self, e);
            }
            self.player.addEventListener('play', function (e) {
                self.isPlaying = true;
                if ($.isFunction(self.options.onPlay)) {
                    self.options.onPlay.call(self, e);
                }
            });
            self.player.addEventListener('pause', function (e) {
                self.isPlaying = false;
                if ($.isFunction(self.options.onPause)) {
                    self.options.onPause.call(self, e);
                }
            });
            self.player.addEventListener('ended', function (e) {
                self.isPlaying = false;
                setTimeout(function () {
                    if (self.player.loop) {
                        self.play();
                    }
                }, 50);
                if ($.isFunction(self.options.onEnd)) {
                    self.options.onEnd.call(self, e);
                }

            });
            if (self.options.autoPlay) {
                self.play();
            }
            self.updateSize();
            this.items[this.options.id] = self;
            if (!isInitialized) {
                isInitialized = true;
                self.onResize();
            }
        },
        onResize: function () {
            var self = this;
            $(window).on('tfsmartresize.tfVideo', function () {
                for (var i in self.items) {
                    self.items[i].updateSize();
                }
            });
        },
        getPlayer: function () {
            return this.player;
        },
        play: function () {
            if (!this.isPlaying) {
                this.player.play();
            }
        },
        pause: function () {
            if (this.isPlaying) {
                this.player.pause();
            }
        },
        update: function (url) {
            this.player.src = url;
            this.player.load();
        },
        muted: function (mute) {
            this.player.muted = mute;
        },
        loop: function (loop) {
            this.player.loop = loop;
            if (!this.isPlaying && loop) {
                this.play();
            }
        },
        updateSize: function () {
            if (document.getElementById(this.prefix + this.options.id) !== null) {
                var Winw = $(window).width(),
                        el = $(this.element),
                        player = $(this.player),
                        containerW = el.outerWidth(),
                        containerH = el.outerHeight();
                containerW = containerW < Winw ? containerW : Winw;
                var containerAspect = containerW / containerH,
                        elW = '',
                        elH = '',
                        top = 0,
                        left = 0,
                        mediaAspect = player.prop('videoWidth') / player.prop('videoHeight');
                if (containerAspect < mediaAspect) {
                    // taller
                    elW = containerH * mediaAspect;
                    elH = containerH;
                    left = -(containerH * mediaAspect - containerW) / 2;
                    top = 0;

                } else {
                    elW = containerW;
                    elH = containerW / mediaAspect;
                    left = 0;
                    top = -(containerW / mediaAspect - containerH) / 2;
                }
                player.css({
                    width: elW + 'px',
                    height: elH + 'px',
                    top: top,
                    left: left
                });
            }
            else {
                this.dispose();
            }
        },
        dispose: function () {
            var el = $(this.player);
            if (el.length > 0) {
                // stop all the downloading
                this.player.pause();
                this.player.src = '';
                el.find('source').prop('src', '');
                el.parent().remove();
                this.player = null;
            }
            delete this.items[this.options.id];
            if (Object.keys(this.items).length === 0) {
                $(window).off('tfsmartresize.tfVideo');
            }
        }

    };
    $.fn[pluginName] = function (options) {
        return this.each(function () {
            $.data(this, 'plugin_' + pluginName, new Plugin(this, options));
        });
    };
})(jQuery, window, document);