(function ($) {

    'use strict';
    if ( ! themifyBuilder.is_gutenberg_editor && $('#page-builder.themify_write_panel').length === 0 )
        return;

    var api = themifybuilderapp,
    $body = $('body'),
    saved = false;
    api.redirectFrontend = false;
    api.toolbarCallback = function(){
        api.undoManager.btnUndo = api.toolbar.el.getElementsByClassName('tb_undo_btn')[0];
        api.undoManager.btnRedo = api.toolbar.el.getElementsByClassName('tb_redo_btn')[0];
        api.undoManager.compactBtn = api.toolbar.el.getElementsByClassName('tb_compact_undo')[0]
    };
    api.render = function () {
		if ( themifyBuilder.is_gutenberg_editor && !document.getElementById('tb_canvas_block') ) return;
       
        $body[0].insertAdjacentHTML('afterbegin', '<div class="tb_fixed_scroll" id="tb_fixed_bottom_scroll"></div>');
        $body.append($('<div/>', {id: 'tb_alert'}));
        if (themifyBuilder.builder_data.length === 0) {
            themifyBuilder.builder_data = {};
        }

		if ( themifyBuilder.is_gutenberg_editor ) {
			var template = wp.template('builder_admin_canvas_block');
			document.getElementById('tb_canvas_block').innerHTML = template();
		}
        this.toolbar = new api.Views.Toolbar({el: '#tb_toolbar'});
		this.toolbar.render();
        this.toolbarCallback();

        api.Instances.Builder[0] = new api.Views.Builder({el: '#tb_row_wrapper', collection: new api.Collections.Rows(themifyBuilder.builder_data)});
        api.Instances.Builder[0].render();
        api.toolbar.pageBreakModule.countModules();
        /* hook save to publish button */
        $('input#publish,input#save-post').one('click', function (e) {
            if (!saved) {
                var $this = $(this);
                $this.addClass('disabled');
                api.Utils.saveBuilder(function(){
                    // Clear undo history
                    api.undoManager.reset();
                    $this.removeClass('disabled').trigger('click');
                });
                e.preventDefault();
            }
        });
        // switch frontend
		var switchButton = $('<a href="#" id="tb_switch_frontend_button" class="button tb_switch_frontend">' + themifyBuilder.i18n.switchToFrontendLabel + '</a>'),
			editorPlaceholder = $( '.themify-wp-editor-holder' );

		if( editorPlaceholder.length ) {
			switchButton = editorPlaceholder.find( 'a' );
		} else {
			switchButton.appendTo( '#postdivrich #wp-content-media-buttons' );
		}

		switchButton.on('click', function (e) {
			e.preventDefault();
			$('#tb_switch_frontend').trigger('click');
		});

        $('input[name*="builder_switch_frontend"]').closest('.themify_field_row').remove(); // hide the switch to frontend field check

        api.Views.bindEvents();

        api.Forms.bindEvents();

        api.vent.trigger('dom:builder:init', true);
    };

    api._backendSwitchFrontend = function(link){
        $('#builder_switch_frontend_noncename').val('ok');
        saved = true;
        if ( 'publish' === $('#original_post_status').val() ) {
			if ( themifyBuilder.is_gutenberg_editor ) {
				if ( $('.editor-post-publish-button').length ) {
					$('.editor-post-publish-button').trigger('click');
				} else {
					$('.editor-post-publish-panel__toggle').trigger('click');
				}
				api.redirectFrontend = link;
				$('#tb_switch_frontend').trigger('click.frontend-btn');
			} else {
            $('#publish').trigger('click');
			}
        } else {
			if ( themifyBuilder.is_gutenberg_editor ) {
				$('.editor-post-save-draft').trigger('click');
				api.redirectFrontend = link;
			} else {
            $('#save-post').trigger('click');
        }
		}
    };
    api._backendBuilderFocus = function(){
        $( '#page-buildert' ).trigger( 'click' );
        $( 'html, body' ).animate( {
                scrollTop: $( '#tb_toolbar' ).offset().top - $( '#wpadminbar' ).height()
        }, 2000 );
    };

    $(function () {
        if ( $body.hasClass( 'post-php' ) && $( '#post-lock-dialog' ).length ) {
			if ( ! $( '#post-lock-dialog' ).hasClass( 'hidden' ) ) {
				return;
			}
			Themify.LoadAsync( themifyBuilder.builder_url + '/js/themify-ticks.js', function() {
				if ( $body.hasClass( 'tb_restriction' ) ) {
					TB_Ticks.init( themifyBuilder.ticks, window ).show();
				} else {
					TB_Ticks.init( themifyBuilder.ticks, window ).ticks();
				}
			}, null, null, function() {
				return typeof TB_Ticks !== 'undefined';
			} );
        }
        var _original_icl_copy_from_original;

        // WPML compat
        if (typeof window.icl_copy_from_original === 'function') {
            _original_icl_copy_from_original = window.icl_copy_from_original;
            // override "copy content" button action to load Builder modules as well
            window.icl_copy_from_original = function (lang, id) {
                _original_icl_copy_from_original(lang, id);
                jQuery.ajax({
                    url: ajaxurl,
                    type: "POST",
                    data: {
                        action: 'themify_builder_icl_copy_from_original',
                        source_page_id: id,
                        source_page_lang: lang
                    },
                    success: function (data) {
                        if (data != '-1') {
                            jQuery('#page-builder .themify_builder.themify_builder_admin').empty().append(data).contents().unwrap();

                            // redo module events
                            //ThemifyPageBuilder.moduleEvents();
                        }
                    }
                });
            };
        }

    });

    // Run on WINDOW load
    $(window).load(function () {

        // Init builder
        api.render();

        var $panel = $('#tb_toolbar'),
			$module_tmp_helper = $('#tb_module_tmp'),
			$scroll_anchor = $('#tb_scroll_anchor'),
			$top = 0,
			$left = 0,
			$scrollTimer = null,
			$panel_top = 0,
			$wpadminbar = $('#wpadminbar'),
			$wpadminbarHeight = $wpadminbar.css( 'position' ) === 'fixed' ? $wpadminbar.outerHeight(true) : 0;
        if ($panel.length > 0) {
            if ($panel.is(':visible') && ! themifyBuilder.is_gutenberg_editor ) {
                themify_sticky_pos();
            }
            else {
                $('.themify-meta-box-tabs a').one('click', function () {
                    if ($(this).attr('id') === 'page-buildert') {
                        themify_sticky_pos();
                    }
                });
            }
        }

		function isStickyBar() {
			var $bottom = $panel_top + $('#page-builder').height(),
				$scroll = $( window ).scrollTop();

			return $scroll > $top && $scroll < $bottom;
		}

		function themify_sticky_pos() {
			$panel.width( $panel.width() );
			$top = $scroll_anchor.offset().top;
			$left = $scroll_anchor.offset().left;
			$panel_top = Math.round( $('#page-builder').offset().top );
			$module_tmp_helper.height( $panel.outerHeight(true) );

			$(window).scroll(function () {
				$scrollTimer && clearTimeout( $scrollTimer );
				$scrollTimer = setTimeout(handleScroll, 15);
			}).resize(function () {
				$top = $scroll_anchor.offset().top;
				$left = $scroll_anchor.offset().left;
				$wpadminbarHeight = $wpadminbar.css( 'position' ) === 'fixed' ? $wpadminbar.outerHeight(true) : 0;
				$panel.width( $('#page-builder .themify_builder_admin' ).width() )
					.css( 'top', isStickyBar() ? $wpadminbarHeight : '' );
				$module_tmp_helper.height( $panel.outerHeight(true) );
			});
		}

		function handleScroll() {
			$scrollTimer = null;
			
			if ( isStickyBar() ) {
				$panel.addClass('tb_toolbar_fixed').css({
					top: $wpadminbarHeight,
					left: $left
				});
				$module_tmp_helper.css('display', 'block');
			} else {
				$panel.removeClass('tb_toolbar_fixed').css({
					top: 0,
					left: 0
				});
				$module_tmp_helper.css('display', 'none');
			}
		}
		if( sessionStorage.getItem( 'focusBackendEditor' ) ) {
			api._backendBuilderFocus();
			sessionStorage.removeItem( 'focusBackendEditor' );
		}
    });
})(jQuery);