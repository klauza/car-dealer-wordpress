/*                                                                                                                                  _
 jquery.mb.YTPlayer.src.js                                                                                                                  _
 last modified: 05/01/16 17.43                                                                                                                    _                                                                                       _
 */
var ytp = ytp || {};
(function ($, ytp) {

    $.themify_ytb = {
        version: "3.0.8",
        apiKey: "",
        defaults: {
            containment: "self",
            ratio: "auto", // "auto", "16/9", "4/3"
            videoID: null,
            startAt: 0,
            stopAt: 0,
            fs:0,
            autoPlay: true,
            vol: 100, // 1 to 100
            addRaster: false,
            quality: "default", //or “small”, “medium”, “large”, “hd720”, “hd1080”, “highres”
            mute: false,
            loop: true,
            showYTLogo: false,
            mobileFallbackImage: null,
            optimizeDisplay: true,
            align: "center,center", // top,bottom,left,right
            onReady: function (player) {
            }
        },
        /**
         *  @fontface icons
         *  */
        controls: {
            play: "P",
            pause: "p",
            mute: "M",
            unmute: "A",
            onlyYT: "O",
            showSite: "R",
            ytLogo: "Y"
        },
        loading: null,
        locationProtocol: location.protocol,
        onYouTubeIframeAPIReady: function (YTPlayer, playerId, playerBox) {
            var $YTPlayer = $(YTPlayer),
                    playerVars = {
                        'modestbranding': 1,
                        'autoplay': 1,
                        'controls': 0,
                        'showinfo': 0,
                        'rel': 0,
                        'disablekb': '',
                        'fs': 0,
                        'loop': YTPlayer.opt.loop?1:0,
                        'enablejsapi': 1,
                        'version': 3,
                        'playerapiid': playerId,
                        'origin': '*',
                        'allowfullscreen': false,
                        'wmode': 'transparent',
                        'iv_load_policy': 3,
                        'html5': 1
                    };
            YTPlayer.opt.autoPlay = typeof YTPlayer.opt.autoPlay === "undefined" ? false : YTPlayer.opt.autoPlay;

            YTPlayer.opt.vol = YTPlayer.opt.vol ? YTPlayer.opt.vol : 100;
            $.themify_ytb.getDataFromAPI(YTPlayer);
            $YTPlayer.on("YTPChanged", function () {

                YTPlayer.isInit = true;
                var v = $('<div>').get(0);
                playerBox.append(v);
                //if is mobile && isPlayer fallback to the default YT player
                if (ThemifyBuilderModuleJs._isMobile() && YTPlayer.canPlayOnMobile) {
                    // Try to adjust the player dimention
                    if (YTPlayer.opt.containment.outerWidth() > $(window).width()) {
                        YTPlayer.opt.containment.css({
                            maxWidth: "100%"
                        });
                        var h = YTPlayer.opt.containment.outerWidth() * .563;
                        YTPlayer.opt.containment.css({
                            maxHeight: h
                        });
                    }
                    new YT.Player(v, {
                        videoId: YTPlayer.videoID.toString(),
                        width: '100%',
                        height: h,
                        playerVars: playerVars,
                        events: {
                            'onReady': function (event) {
                                YTPlayer.player = event.target;
                            }
                        }
                    });
                    return;
                }

                new YT.Player(v, {
                    videoId: YTPlayer.videoID.toString(),
                    playerVars: playerVars,
                    events: {
                        'onReady': function (event) {
                            YTPlayer.player = event.target;
                            YTPlayer.isReady = YTPlayer.isPlayer && !YTPlayer.opt.autoPlay ? false : true;
                            YTPlayer.playerEl = YTPlayer.player.getIframe();
                            $(YTPlayer.playerEl).attr("unselectable", "on");
                            $YTPlayer.optimizeDisplay();
                            $(window).off("tfsmartresize." + playerId).on("tfsmartresize.tfVideo tfsmartresize." + playerId, function () {
                                $YTPlayer.optimizeDisplay();
                            });

                            $.themify_ytb.checkForState(YTPlayer);
                        },
                        'onStateChange': function (event) {
                            if (typeof event.target.getPlayerState !== "function")
                                return;
                            var state = event.target.getPlayerState();

                            if (YTPlayer.preventTrigger) {
                                YTPlayer.preventTrigger = false;
                                return;
                            }

                            YTPlayer.state = state;

                            var eventType;
                            switch (state) {
                                case -1: //----------------------------------------------- unstarted
                                    eventType = "YTPUnstarted";
                                    break;
                                case 0: //------------------------------------------------ ended
                                    eventType = "YTPEnd";
                                    if(YTPlayer.opt.loop){
                                       YTPlayer.player.seekTo(0);
                                    }
                                    break;
                                case 1: //------------------------------------------------ play
                                    eventType = "YTPPlay";

                                case 2: //------------------------------------------------ pause
                                    eventType = "YTPPause";
                                    break;
                                case 3: //------------------------------------------------ buffer
                                    YTPlayer.player.setPlaybackQuality(YTPlayer.opt.quality);
                                    eventType = "YTPBuffering";
                                    break;
                                case 5: //------------------------------------------------ cued
                                    eventType = "YTPCued";
                                    break;
                                default:
                                    break;
                            }

                            // Trigger state events
                            var YTPEvent = $.Event(eventType);
                            YTPEvent.time = YTPlayer.currentTime;
                            if (YTPlayer.canTrigger)
                                $(YTPlayer).trigger(YTPEvent);
                        },
                        /**
                         *
                         * @param e
                         */
                        'onPlaybackQualityChange': function (e) {
                            var quality = e.target.getPlaybackQuality();
                            var YTPQualityChange = $.Event("YTPQualityChange");
                            YTPQualityChange.quality = quality;
                            $(YTPlayer).trigger(YTPQualityChange);
                        },
                        /**
                         *
                         * @param err
                         */
                        'onError': function (err) {

                            if (err.data == 150) {
                                console.log("Embedding this video is restricted by Youtube.");
                            }

                            if (typeof YTPlayer.opt.onError === "function")
                                YTPlayer.opt.onError($YTPlayer, err);
                        }
                    }
                });
            });
        },
        /**
         *
         * @param options
         * @returns [players]
         */
        buildPlayer: function (options) {

            return this.each(function () {
                var YTPlayer = this,
                        playerId = "themify_ytb_" + options.id,
                        $YTPlayer = $(YTPlayer);

                YTPlayer.loop = 0;
                YTPlayer.opt = {};
                YTPlayer.state = {};
                $.extend(YTPlayer.opt, $.themify_ytb.defaults, options);
                if (!YTPlayer.hasChanged) {
                    YTPlayer.defaultOpt = {};
                    $.extend(YTPlayer.defaultOpt, $.themify_ytb.defaults, options);
                }

                YTPlayer.isRetina = (window.retina || window.devicePixelRatio > 1);
                YTPlayer.isAlone = false;
                YTPlayer.hasFocus = true;
                YTPlayer.videoID = options.videoID;


                YTPlayer.defaultOpt.containment = YTPlayer.opt.containment = YTPlayer.opt.containment === "self" ? $(this) : $(YTPlayer.opt.containment);

                var isPlayer = YTPlayer.opt.containment.is($(this));

                YTPlayer.canPlayOnMobile = isPlayer && $(this).children().length === 0;
                YTPlayer.isPlayer = false;

                if (!isPlayer) {
                    $YTPlayer.hide();
                } else {
                    YTPlayer.isPlayer = true;
                }

                var wrapper = $("<div/>").addClass("themify_ytb_wrapper big-video-wrap").attr("id", "wrapper_" + playerId),
                        playerBox = $("<div/>").addClass("themify_ytb_playerbox").prop("id", playerId);
                playerBox.appendTo(wrapper);

                YTPlayer.opt.containment.prepend(wrapper);
                YTPlayer.wrapper = wrapper;

                if (ThemifyBuilderModuleJs._isMobile() && !YTPlayer.canPlayOnMobile) {

                    if (YTPlayer.opt.mobileFallbackImage) {
                        YTPlayer.opt.containment.css({
                            backgroundImage: "url(" + YTPlayer.opt.mobileFallbackImage + ")",
                            backgroundPosition: "center center",
                            backgroundSize: "cover",
                            backgroundRepeat: "no-repeat"
                        });
                    }
                    ;

                    $YTPlayer.remove();
                    $(document).trigger("YTPUnavailable");
                    return;
                }

                $.themify_ytb.onYouTubeIframeAPIReady(YTPlayer, playerId, playerBox);

            });

        },
        /**
         *
         * @param YTPlayer
         */
        getDataFromAPI: function (YTPlayer) {
            YTPlayer.videoData = $(YTPlayer.playerEl).data("YTPlayer_data_" + YTPlayer.videoID);
            $(YTPlayer).off("YTPData.YTPlayer").on("YTPData.YTPlayer", function () {
                if (YTPlayer.hasData) {

                    if (YTPlayer.isPlayer && !YTPlayer.opt.autoPlay) {
                        var bgndURL = YTPlayer.videoData.thumb_max || YTPlayer.videoData.thumb_high || YTPlayer.videoData.thumb_medium;
                        YTPlayer.opt.containment.css({
                            background: "rgba(0,0,0,0.5) url(" + bgndURL + ") center center",
                            backgroundSize: "cover"
                        });
                        YTPlayer.opt.backgroundUrl = bgndURL;
                    }
                }
            });

            if (YTPlayer.videoData) {

                setTimeout(function () {
                    YTPlayer.opt.ratio = YTPlayer.opt.ratio == "auto" ? "16/9" : YTPlayer.opt.ratio;
                    YTPlayer.dataReceived = true;
                    $(YTPlayer).trigger("YTPChanged");
                    var YTPData = $.Event("YTPData");
                    YTPData.prop = {};
                    for (var x in YTPlayer.videoData)
                        YTPData.prop[ x ] = YTPlayer.videoData[ x ];
                    $(YTPlayer).trigger(YTPData);
                }, 500);

                YTPlayer.hasData = true;
            } else if ($.themify_ytb.apiKey) {
                // Get video info from API3 (needs api key)
                // snippet,player,contentDetails,statistics,status
                $.getJSON($.themify_ytb.locationProtocol + "//www.googleapis.com/youtube/v3/videos?id=" + YTPlayer.videoID + "&key=" + $.themify_ytb.apiKey + "&part=snippet", function (data) {
                    YTPlayer.dataReceived = true;
                    $(YTPlayer).trigger("YTPChanged");

                    function parseYTPlayer_data(data) {
                        YTPlayer.videoData = {};
                        YTPlayer.videoData.id = YTPlayer.videoID;
                        YTPlayer.videoData.channelTitle = data.channelTitle;
                        YTPlayer.videoData.title = data.title;
                        YTPlayer.videoData.description = data.description.length < 400 ? data.description : data.description.substring(0, 400) + " ...";
                        YTPlayer.videoData.aspectratio = YTPlayer.opt.ratio == "auto" ? "16/9" : YTPlayer.opt.ratio;
                        YTPlayer.opt.ratio = YTPlayer.videoData.aspectratio;
                        YTPlayer.videoData.thumb_max = data.thumbnails.maxres ? data.thumbnails.maxres.url : null;
                        YTPlayer.videoData.thumb_high = data.thumbnails.high ? data.thumbnails.high.url : null;
                        YTPlayer.videoData.thumb_medium = data.thumbnails.medium ? data.thumbnails.medium.url : null;
                        $(YTPlayer.playerEl).data("YTPlayer_data_" + YTPlayer.videoID, YTPlayer.videoData);
                    }
                    parseYTPlayer_data(data.items[ 0 ].snippet);
                    YTPlayer.hasData = true;
                    var YTPData = $.Event("YTPData");
                    YTPData.prop = {};
                    for (var x in YTPlayer.videoData)
                        YTPData.prop[ x ] = YTPlayer.videoData[ x ];
                    $(YTPlayer).trigger(YTPData);
                });
            } else {
                setTimeout(function () {
                    $(YTPlayer).trigger("YTPChanged");
                }, 50);
                if (YTPlayer.isPlayer && !YTPlayer.opt.autoPlay) {
                    var bgndURL = $.themify_ytb.locationProtocol + "//i.ytimg.com/vi/" + YTPlayer.videoID + "/hqdefault.jpg";
                    YTPlayer.opt.containment.css({
                        background: "rgba(0,0,0,0.5) url(" + bgndURL + ") center center",
                        backgroundSize: "cover"
                    });
                    YTPlayer.opt.backgroundUrl = bgndURL;
                }
                YTPlayer.videoData = null;
                YTPlayer.opt.ratio = YTPlayer.opt.ratio == "auto" ? "16/9" : YTPlayer.opt.ratio;
            }
            if (YTPlayer.isPlayer && !YTPlayer.opt.autoPlay && !ThemifyBuilderModuleJs._isMobile()) {
                YTPlayer.loading = $("<div/>").addClass("loading").html("Loading").hide();
                $(YTPlayer).append(YTPlayer.loading);
                YTPlayer.loading.fadeIn();
            }
        },
        /**
         *
         * @returns {*|YTPlayer.videoData}
         */
        getVideoData: function () {
            return this.get(0).videoData;
        },
        /**
         *
         * @returns {*|YTPlayer.videoID|boolean}
         */
        getVideoID: function () {
            return this.get(0).videoID || false;
        },
        /**
         *
         * @param quality
         */
        setVideoQuality: function (quality) {
            this.get(0).player.setPlaybackQuality(quality);
        },
        /**
         *
         * @param opt
         */
        changeMovie: function (opt) {
            var YTPlayer = this.get(0);
            YTPlayer.opt.startAt = 0;
            YTPlayer.opt.stopAt = 0;
            YTPlayer.opt.mute = false;
            YTPlayer.opt.loop = true;
            YTPlayer.hasData = false;
            YTPlayer.hasChanged = true;
            YTPlayer.player.loopTime = undefined;

            if (opt) {
                $.extend(YTPlayer.opt, opt); //YTPlayer.defaultOpt,
            }
            YTPlayer.videoID = opt.videoID;
            var YTPChangeMovie = $.Event("YTPChangeMovie");
            YTPChangeMovie.time = YTPlayer.currentTime;
            YTPChangeMovie.videoId = YTPlayer.videoID;
            $(YTPlayer).trigger(YTPChangeMovie);

            $(YTPlayer).ThemifyYTBGetPlayer().cueVideoByUrl(encodeURI($.themify_ytb.locationProtocol + "//www.youtube.com/v/" + YTPlayer.videoID), 1, YTPlayer.opt.quality);
            $(YTPlayer).optimizeDisplay();

            $.themify_ytb.checkForState(YTPlayer);
            $.themify_ytb.getDataFromAPI(YTPlayer);

        },
        /**
         *
         * @returns {player}
         */
        getPlayer: function () {
            return $(this).get(0).player;
        },
        playerDestroy: function () {
            var YTPlayer = this.get(0);
            YTPlayer.isInit = false;
            YTPlayer.videoID = null;
            YTPlayer.wrapper.remove();
            return this;
        },
        /**
         *
         * @returns {$.themify_ytb}
         */
        play: function () {
            var YTPlayer = this.get(0);
            if (!YTPlayer.isReady)
                return this;

            YTPlayer.player.playVideo();
            YTPlayer.state = 1;
            $(YTPlayer).css("background-image", "none");
            return this;
        },
        /**
         *
         * @returns {$.themify_ytb}
         */
        stop: function () {
            var YTPlayer = this.get(0);
            YTPlayer.player.stopVideo();
            return this;
        },
        /**
         *
         * @returns {$.themify_ytb}
         */
        pause: function () {
            var YTPlayer = this.get(0);
            YTPlayer.player.pauseVideo();
            YTPlayer.state = 2;
            return this;
        },
        /**
         *
         * @param val
         * @returns {$.themify_ytb}
         */
        seekTo: function (val) {
            this.get(0).player.seekTo(val, true);
            return this;
        },
        /**
         *
         * @param val
         * @returns {$.themify_ytb}
         */
        setVolume: function (val) {
            var YTPlayer = this.get(0);
            YTPlayer.opt.vol = val;
            YTPlayer.player.setVolume(val);
            return this;
        },
        /**
         *
         * @returns {$.themify_ytb}
         */
        mute: function () {
            var YTPlayer = this.get(0);
            YTPlayer.player.mute();
            YTPlayer.player.setVolume(0);
            var YTPEvent = $.Event("YTPMuted");
            YTPEvent.time = YTPlayer.currentTime;
            if (YTPlayer.canTrigger){
                $(YTPlayer).trigger(YTPEvent);
            }
            return this;
        },
        /**
         *
         * @returns {$.themify_ytb}
         */
        unmute: function () {
            var YTPlayer = this.get(0);
            YTPlayer.player.unMute();
            YTPlayer.player.setVolume(YTPlayer.opt.vol);
            var YTPEvent = $.Event("YTPUnmuted");
            YTPEvent.time = YTPlayer.currentTime;
            if (YTPlayer.canTrigger)
                $(YTPlayer).trigger(YTPEvent);
            return this;
        },
        /**
         *
         * @param YTPlayer
         */
        checkForState: function (YTPlayer) {
            //Checking if player has been removed from scene
            if (!$.contains(document, YTPlayer)) {
                $(YTPlayer).ThemifyYTBPlayerDestroy();
                return;
            }

            YTPlayer.preventTrigger = true;
            YTPlayer.state = 2;
            if (YTPlayer.opt.mute) {
                $(YTPlayer).ThemifyYTBMute();
            }
            YTPlayer.player.seekTo((YTPlayer.opt.startAt ? YTPlayer.opt.startAt : 1), true);
        },
        /**
         *
         * @returns {string} time
         */
        getTime: function () {
            return $.themify_ytb.formatTime(this.get(0).currentTime);
        },
        /**
         *
         * @returns {string} total time
         */
        getTotalTime: function () {
            return $.themify_ytb.formatTime(this.get(0).totalTime);
        },
        /**
         *
         * @param align
         */
        setAlign: function (align) {
            this.optimizeDisplay(align);
        },
        /**
         *
         * @param align
         */
        getAlign: function () {
            return this.get(0).opt.align;
        },
        /**
         *
         * @param s
         * @returns {string}
         */
        formatTime: function (s) {
            var min = Math.floor(s / 60),
                    sec = Math.floor(s - (60 * min));
            return(min <= 9 ? "0" + min : min) + " : " + (sec <= 9 ? "0" + sec : sec);
        }
    };

    /**
     *
     * @param align
     * can be center, top, bottom, right, left; (default is center,center)
     */
    $.fn.optimizeDisplay = function (align) {
        var YTPlayer = this.get(0),
                vid = {};

        YTPlayer.opt.align = align || YTPlayer.opt.align;

        YTPlayer.opt.align = typeof YTPlayer.opt.align !== "undefined " ? YTPlayer.opt.align : "center,center";
        var YTPAlign = YTPlayer.opt.align.split(",");

        if (YTPlayer.opt.optimizeDisplay) {
            var win = {};
            var el = YTPlayer.wrapper;

            win.width = el.outerWidth();
            win.height = el.outerHeight();

            vid.width = win.width + 100;
            vid.height = YTPlayer.opt.ratio === "16/9" ? Math.ceil(vid.width * (9 / 16)) : Math.ceil(vid.width * (3 / 4));
            vid.marginTop = -((vid.height - win.height) / 2);
            vid.marginLeft = 0;

            var lowest = vid.height < win.height;

            if (lowest) {

                vid.height = win.height;
                vid.width = YTPlayer.opt.ratio === "16/9" ? Math.floor(win.height * (16 / 9)) : Math.floor(win.height * (4 / 3));

                vid.marginTop = 0;
                vid.marginLeft = -((vid.width - win.width) / 2);

            }

            for (var a in YTPAlign) {

                //var al = YTPAlign[ a ].trim();
                var al = YTPAlign[ a ].replace(/ /g, "");

                switch (al) {

                    case "top":
                        vid.marginTop = lowest ? -((vid.height - win.height) / 2) : 0;
                        break;

                    case "bottom":
                        vid.marginTop = lowest ? 0 : -(vid.height - win.height);
                        break;

                    case "left":
                        vid.marginLeft = 0;
                        break;

                    case "right":
                        vid.marginLeft = lowest ? -(vid.width - win.width) : 0;
                        break;

                    default:
                        break;
                }

            }

        } else {
            vid.width = "100%";
            vid.height = "100%";
            vid.marginTop = 0;
            vid.marginLeft = 0;
        }

        $(YTPlayer.playerEl).css({
            width: vid.width,
            height: vid.height,
            marginTop: vid.marginTop,
            marginLeft: vid.marginLeft
        });

    };


    /* Exposed public method */
    $.fn.ThemifyYTBPlayer = $.themify_ytb.buildPlayer;
    $.fn.ThemifyYTBGetPlayer = $.themify_ytb.getPlayer;
    $.fn.ThemifyYTBGetVideoID = $.themify_ytb.getVideoID;
    $.fn.ThemifyYTBChangeMovie = $.themify_ytb.changeMovie;
    $.fn.ThemifyYTBPlayerDestroy = $.themify_ytb.playerDestroy;

    $.fn.ThemifyYTBPlay = $.themify_ytb.play;
    $.fn.ThemifyYTBStop = $.themify_ytb.stop;
    $.fn.ThemifyYTBPause = $.themify_ytb.pause;
    $.fn.ThemifyYTBSeekTo = $.themify_ytb.seekTo;

    $.fn.ThemifyYTBMute = $.themify_ytb.mute;
    $.fn.ThemifyYTBUnmute = $.themify_ytb.unmute;
    $.fn.ThemifyYTBSetVolume = $.themify_ytb.setVolume;

    $.fn.ThemifyYTBGetVideoData = $.themify_ytb.getVideoData;
    $.fn.ThemifyYTBSetVideoQuality = $.themify_ytb.setVideoQuality;

    $.fn.ThemifyYTBGetTime = $.themify_ytb.getTime;
    $.fn.ThemifyYTBGetTotalTime = $.themify_ytb.getTotalTime;

    $.fn.ThemifyYTBSetAlign = $.themify_ytb.setAlign;
    $.fn.ThemifyYTBGetAlign = $.themify_ytb.getAlign;

})(jQuery, ytp);
