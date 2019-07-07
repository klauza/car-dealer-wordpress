/*! Themify Builder - Asynchronous Script and Styles Loader */
(function ($, window, document) {
    'use strict';
    $(document).ready(function () {
        var isTouch = tbLoaderVars.isTouch ? true : false;
        function remove_tinemce() {
            if (tinymce !== undefined && tinyMCE) {
                tinyMCEPreInit.mceInit['tb_lb_hidden_editor']['wp_autoresize_on'] = false;
                var content_css = tinyMCEPreInit.mceInit['tb_lb_hidden_editor']['content_css'].split(',');
                tinyMCEPreInit.mceInit['tb_lb_hidden_editor']['content_css'] = content_css[1] !== undefined ? content_css[1] : content_css[0];
                tinyMCEPreInit.mceInit['tb_lb_hidden_editor']['plugins'] = 'charmap,colorpicker,hr,lists,media,paste,tabfocus,textcolor,fullscreen,wordpress,wpautoresize,wpeditimage,wpemoji,wpgallery,wpdialogs,wptextpattern,wpview,wplink';
                tinyMCEPreInit.mceInit['tb_lb_hidden_editor']['indent'] = 'simple';
                tinyMCEPreInit.mceInit['tb_lb_hidden_editor']['ie7_compat'] = false;
                tinyMCEPreInit.mceInit['tb_lb_hidden_editor']['root_name'] = 'div';
                tinyMCEPreInit.mceInit['tb_lb_hidden_editor']['relative_urls'] = true;
                tinyMCE.execCommand('mceRemoveEditor', true, 'tb_lb_hidden_editor');
                $('#wp-tb_lb_hidden_editor-editor-container,#wp-tb_lb_hidden_editor-editor-tools').remove();
            }
        }
        var builderLoader = false;
        if (wp === undefined || wp.customize === undefined) {
            var builder = document.querySelectorAll('.themify_builder_content:not(.not_editable_builder)');
            for(var i=0,len=builder.length;i<len;++i){
                builder[i].insertAdjacentHTML('afterEnd','<a class="tb_turn_on js-turn-on-builder" href="javascript:void(0);"><span class="dashicons dashicons-edit" data-id="' + builder[i].dataset.postid + '"></span>' + tbLoaderVars.turnOnBuilder + '</a>');
            }
            builder = null;
        }
        
        var responsiveSrc = window.location.href.indexOf('?') > 0 ? '&' : '?';
        responsiveSrc = window.location.href.replace(window.location.hash, '').replace('#', '') + responsiveSrc + 'tb-preview=1&ver=' + tbLocalScript.version;
        function init(){
            Themify.body.one('click.tbloader', '.toggle_tb_builder a:first, a.js-turn-on-builder', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var is_locked = Themify.body.hasClass('tb_restriction');
                    Themify.LoadAsync(tbLocalScript.builder_url+'/js/themify-ticks.js', function(){
                    if(is_locked){
                        TB_Ticks.init(tbLocalScript.ticks).show();
                        init(); 
                    }
                    },null,null,function(){
                        return typeof TB_Ticks!=='undefined';
                    });
                if(is_locked){
                    return;
                }
                var post_id = $( this ).find( '> span' ).data('id');
                setTimeout(remove_tinemce, 1);
                //remove unused the css/js to make faster switch mode/window resize
                var $children = Themify.body.children(),
                    css = Array.prototype.slice.call(document.head.getElementsByTagName('link')),
                    js_styles = Array.prototype.slice.call(document.head.getElementsByTagName('script')).concat(Array.prototype.slice.call(document.head.getElementsByTagName('style'))),
                    isScrolling = isTouch ? 'no' : 'yes';

                Themify.body[0].insertAdjacentHTML('beforeend', '<div class="tb_workspace_container"><div class="tb_vertical_bars"><iframe src="' + responsiveSrc + '" id="tb_iframe" name="tb_iframe" class="tb_iframe" scrolling="' + isScrolling + '"></iframe></div></div>');

                if (!builderLoader) {
                    var css_items = [];
                    setTimeout(function () {
                        for (var i = 0, len = tbLoaderVars.styles.length; i < len; ++i) {
                            var fullHref = tbLoaderVars.styles[i]+'?ver='+tbLocalScript.version;
                            if($("link[href='" + fullHref + "']").length===0){
                                Themify.LoadCss(tbLoaderVars.styles[i]);
                            }
                            else{
                                css_items[fullHref] = 1;
                            }
                        }
                        for (var i = 0, len = tbLoaderVars.js.length; i < len; ++i) {
                            if (tbLoaderVars.js[i].external) {
                                var s = document.createElement('script');
                                s.type = 'text/javascript';
                                s.text = tbLoaderVars.js[i].external;
                                var t = document.getElementsByTagName('script')[0];
                                t.parentNode.insertBefore(s, t);
                            }
                            Themify.LoadAsync(tbLoaderVars.js[i].src, null, tbLoaderVars.js[i].ver);
                        }
                        builderLoader = $('<div/>', {
                            id: 'tb_alert',
                            class: 'tb_busy'
                        });
                        Themify.body[0].insertAdjacentHTML('afterbegin', '<div class="tb_fixed_scroll" id="tb_fixed_bottom_scroll"></div>');
                        Themify.body.append(builderLoader);
                        // Change text to indicate it's loading
                        $('.tb_front_icon').length>0 && $('.tb_front_icon').parent()[0].insertAdjacentHTML('beforeend', '<div id="builder_progress"><div></div></div>');
                    }, 1);
                }
                $('#tb_iframe').one('load', function () {
                    var scrollPos = $(document).scrollTop(),
                        _this = this, contentWindow, b;
                        Themify.body.one('themify_builder_ready', function (e) {
                            builderLoader.fadeOut(100, function () {
                                $(this).removeClass('tb_busy');
                            });
                            $('.tb_workspace_container').show();
							verticalResponsiveBars();
							$children.hide();
                            for (var i = 0, len = js_styles.length; i < len; ++i) {
                                if (js_styles[i] && js_styles[i].parentNode) {
                                    js_styles[i].parentNode.removeChild(js_styles[i]);
                                }
                            }
                            js_styles = null;
                            for (var i = 0, len = css.length; i < len; ++i) {
                                if (css[i] && css[i].parentNode) {
                                    var href = css[i].getAttribute( 'href' );
                                    if(css_items[href]===undefined && href.search( /(?=wp\-includes\/(css|js).(?!admin\-bar)).+/ ) === -1){
                                        css[i].parentNode.removeChild(css[i]);
                                    }
                                }
                            }
                            css = css_items= tbLoaderVars = builderLoader= null;
                            $('.themify_builder_content,#wpadminbar,header').remove();
                            $children.filter( 'ul,a,video,audio' ).filter( ':not(:has(link))' ).remove();
                            $(window).off();
                                Themify.body.off('scroll');
                            $(document).off();
                            $('html').removeAttr('style class');
                                Themify.body.prop('class', 'themify_builder_active builder-breakpoint-desktop'+(Themify.body.hasClass('tb_module_panel_locked')?' tb_module_panel_locked':'')+(isTouch?' tb_touch':'')).removeAttr('style');
                            contentWindow.scrollTo(0, scrollPos);
                            if(!b.hasClass('tb_restriction')){
                                setTimeout(function(){
                                    TB_Ticks.init(tbLocalScript.ticks,contentWindow).ticks();
                                },5000);
                            }
                            else{
                                setTimeout(function(){
                                    document.body.appendChild(b.find('#tmpl-builder-restriction')[0]);
                                    TB_Ticks.init(tbLocalScript.ticks,contentWindow).show();
                                },1000);
                            }
                        });
                        // Cloudflare compatibility fix
                        if( '__rocketLoaderLoadProgressSimulator' in _this.contentWindow ) {
                                var rocketCheck = setInterval( function() {
                                        if( _this.contentWindow.__rocketLoaderLoadProgressSimulator.simulatedReadyState === 'complete' ) {
                                                clearInterval( rocketCheck );
                                                contentWindow = _this.contentWindow;
                                                b = contentWindow.jQuery('body');
                                                contentWindow.themifyBuilder.post_ID = post_id;
                                                b.trigger( 'builderiframeloaded.themify', _this );
                                        }
                                }, 10 );
                        } else {
                                contentWindow = _this.contentWindow;
                                b = contentWindow.jQuery('body');
                                contentWindow.themifyBuilder.post_ID = post_id;
                                b.trigger( 'builderiframeloaded.themify', _this );
                        }
                });
            });
        }

	    function verticalResponsiveBars() {
        	/* initialization */
        	var bar = 'none',
                start_x = -1, 
                start_with = -1,
                min_width = 320,
                is_draging = false,
                iframe = $( 'iframe#tb_iframe' ),
		        activeBreakPoint = '',
		        frameForms = $('iframe#tb_iframe')[0].contentWindow.themifybuilderapp.Forms;

		    $( '.tb_vertical_bars' )
			    .append( '<div class="tb_right_bar"><div class="tb_middle_bar">' )
			    .prepend( '<div class="tb_left_bar"><div class="tb_middle_bar">' );

            /* bind events */
            $( '.tb_right_bar,.tb_left_bar' ).mousedown(function (e) {
                resizeBarMousedownHandler(this.classList.contains('tb_right_bar')?'right':'left', e);
            });

            /* functions */
            var resizeBarMousedownHandler =function (bar_type, e) {
                bar = bar_type;
                start_x = e.clientX;
                start_with = iframe.width();
                is_draging = true;

                iframe.css('transition','none');
                if( $( '.tb_vertical_change_tooltip' ).length === 0) {
                    $( '.tb_vertical_bars' ).append("<div class='tb_vertical_change_tooltip'></div>");
                }
                $( '.tb_vertical_bars' ).append( '<div class="tb_mousemove_cover"></div>');
                var cover = $('.tb_mousemove_cover');
                $('.tb_middle_bar').css('background-color', '#3ebaea');

                $(cover).on('mousemove.cover', coverMousemoveHandler)
                .mouseup(function(e) {
                    if(is_draging) {
                        $(cover).off('mousemove.cover mouseup');

                        $(cover).remove();
                        iframe.css('transition','');
			    $('.tb_vertical_change_tooltip')
                            .attr('style','')
                            .hide();
                        $('.tb_middle_bar').css('background-color', '');                            

                        is_draging = false;
                        bar = 'none';
                        start_x = -1;
                        start_with = -1;
                    }
                });
            };
            var coverMousemoveHandler = function(e) {
                    if(bar === 'none') return;

                    var width = compute_new_width(e);
                    showWidth(width,e.clientY);

                    var breakpoint = 'desktop';

                    if(width <= tbLocalScript.breakpoints.mobile )
                        breakpoint = 'mobile';
                    else if(width <= tbLocalScript.breakpoints.tablet[1] )
                        breakpoint = 'tablet';
                    else if(width <= tbLocalScript.breakpoints.tablet_landscape[1])
                        breakpoint =  'tablet_landscape';
                    if(activeBreakPoint !== breakpoint)
                        set_breakpoint_state(breakpoint);

                    if ( width !== min_width ) {
                        iframe.css( 'width', width );
                    } else {
                        width = min_width;
                    }
            };
            function showWidth(w, top) {
                    $('.tb_vertical_change_tooltip')
                            .show()
                            .html(w + 'px')
                            .css(('left' === bar) ? 'right':'left',
                                'calc((100% - '+w+'px)/2 + '+(w+15)+'px)'
                    ).css('top',
                                top
                            );
            };
            function compute_new_width(e) {
                if(bar === 'none') return iframe.width();
                    var diff = e.clientX - start_x;
                    diff *= 2;
                    if(bar === 'left')
                        diff = - diff;
                    return ((start_with + diff) < min_width  ? min_width : (start_with + diff));
            };
            function set_breakpoint_state(breakpoint){
                activeBreakPoint = breakpoint;
                if(Themify.body.hasClass('builder-breakpoint-' + breakpoint))return;
                    frameForms.lightbox_switcher({
                            preventDefault : function (  ) {},
                            currentTarget : "<a href='#"+breakpoint+"'></a>"
                    });
            }
        }

        init();
        if(!Themify.body.hasClass('tb_restriction')){  
            if (window.location.hash === '#builder_active') {
                $('.js-turn-on-builder').first().trigger('click');
                window.location.hash = '';
            }
            else {
                //cache iframe content in background and tincymce content_css
                var link = '<link href="' + responsiveSrc + '" rel="prerender prefetch"/>',
                    $tinemce = tinyMCEPreInit.mceInit['tb_lb_hidden_editor']['content_css'],
                    cache_suffix = tinyMCEPreInit.mceInit['tb_lb_hidden_editor']['cache_suffix'];

				if( $tinemce ) {
					$tinemce = $tinemce.split( ',' );

					for (var i = 0, len = $tinemce.length; i < len; ++i) {
						$tinemce[i] += ($tinemce[i].indexOf('?') > -1 ? '&' : '?') + cache_suffix;
						link += '<link href="' + $tinemce[i] + '" rel="prefetch"/>';
					}
				}
				
				document.head.insertAdjacentHTML( 'beforeend', link );
            }
        }
    });
})(jQuery, window, document);