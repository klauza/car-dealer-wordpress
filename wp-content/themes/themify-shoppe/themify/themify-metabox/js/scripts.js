window.Themify_Metabox = (function($){

	'use strict';

	var api = {};

	api.init = function(){
		this.bind_events();
	},

	api.bind_events = function(){
		$(document).ready( api.document_ready );
	};

	api.document_ready = function(){
		api.init_metabox_tabs();
		api.gallery_shortcode();
		api.repeater();

		// revisit these three
		api.enable_toggle();
		api.query_category();
		api.post_meta_checkbox();

		api.init_fields( $( 'body' ) );
		api.audioRemoveAction();
	},

	// create the tabs in custom meta boxes
	api.init_metabox_tabs = function(){
		// Tabs for Custom Panel
		$( '.themify-meta-box-tabs' ).each(function(){
			var context = $( this );

			var $ilcHTabsLi = $( '.ilc-htabs li', context );
			if( $ilcHTabsLi.length > 1 ) {
				ilcTabs( {ilctabs  : '#' + context.attr( 'id' ) } );
			} else {
				$( '.ilc-tab', context ).show();
				$ilcHTabsLi.addClass( 'select' );
			}
		});

		// set tabs cookie
		$('.ilc-htabs a').on('click', function(){
			api.set_cookie('themify-builder-tabs', $(this).attr('id'));
		});

		// active tabs cookie
		if( typeof(api.get_cookie('themify-builder-tabs')) != 'undefined' && api.get_cookie('themify-builder-tabs') !== null ){
			$( '#' + api.get_cookie('themify-builder-tabs')).trigger('click');
		}
	}

	// initialize different field types
	api.init_fields = function( $context ) {

		api.layout( $context );
		api.color_picker( $context );
		api.date_picker( $context );
		api.assignments( $context );
		api.dropdownbutton( $context );

		// custom event trigger
		$( document ).trigger( 'themify_metabox_init_fields', [api] );
	}

	api.repeater = function(){
		$( 'body' ).on( 'click', '.themify-repeater-add', function(e){
			e.preventDefault();
			var $this = $( this ),
				container = $this.closest( '.themify_field_row' ),
				rows = container.find( '.themify-repeater-rows' ),
				template = container.find( '.themify-repeater-template' ).html();

			var new_id = 1;
			if( rows.find( '> div' ).length ) {
				rows.find( '> div' ).each(function(){
					new_id = Math.max( new_id, $( this ).data( 'id' ) );
				});
				new_id++;
			}

			var $template = $( template.replace( /__i__/g, new_id ) );
			$template.find( '.ajaxnonceplu' ).attr( 'id', '' );
			rows.append( $template );

			if( $template.has( '.plupload-upload-uic' ).length ) {
				$template.find( '.plupload-upload-uic' )
					.each( function () {
						themify_create_pluploader( $( this ) );
					} );
			}
			// init field types for the new row
			api.init_fields( rows.find('.themify-repeater-row:last-child') );
		} );

		$( 'body' ).on( 'click', '.themify-repeater-remove-row', function( e ) {
			e.preventDefault();

			$( this ).parent().remove();
		} );
	}

	api.audioRemoveAction = function() {
		$( 'body' ).on( 'click', '[data-audio-remove] a', function( e ) {
			e.preventDefault();

			var $self = $( this ).parent(),
				data = $self.data( 'audio-remove' ),
				callback = function() {
					$self.parent().find( '.themify_upload_field' ).val('');
					$self.addClass( 'hide' );
				};
			
			callback();
			data.action = 'themify_remove_audio';
			$.post( ajaxurl, data, callback );
		} );
	}

	api.color_picker = function( $context ) {
		// color picker
		$context.find( '.colorSelectInput' ).each(function(){
			var args = {},
				$this = $( this ),
				format = $this.data( 'format' );
			if( format == 'rgba' ) {
				args.format = 'rgb';
				args.opacity = true;
			} else if( format == 'rgb' ) {
				args.format = 'rgb';
			} else {
			}
			$( this ).minicolors( args );
		});

		// Set or clear color swatch based on input value
		// Clear swatch and input
		$context.find( '.clearColor' ).on( 'click', function() {
			$(this).parent().find('.colorSelect').minicolors('value', '');
			$(this).parent().find('.colorSelectInput').val('');
			$(this).hide();
		});
	}

	api.date_picker = function( $context ) {
		$context.find( '.themifyDatePicker' ).each(function(){
			var $self = $(this),
				label = $self.data('label'),
				close = $self.data('close' ),
				dateformat = $self.data('dateformat' ),
				timeformat = $self.data('timeformat' ),
				timeseparator = $self.data('timeseparator' );
			
			( $.fn.themifyDatetimepicker 
				? $.fn.themifyDatetimepicker 
				: $.fn.datetimepicker ).call( $self, {
					showOn: 'both',
					showButtonPanel: true,
					closeButton: close,
					buttonText: label,
					dateFormat: dateformat,
					timeFormat: timeformat,
					stepMinute: 5,
					firstDay: $self.data( 'first-day' ),
					separator: timeseparator,
					onClose: function( date ) {
						if ( '' != date ) {
							$( '#' + $self.data('clear') ).addClass('themifyFadeIn');
						}
					},
					beforeShow: function() {
						$('#ui-datepicker-div').addClass( 'themifyDateTimePickerPanel' );
					}
				});
			$self.next().addClass('button');
		});

		$context.find( '.themifyClearDate' ).on( 'click', function(e) {
			e.preventDefault();
			var $self = $(this);
			$( '#' + $self.data('picker') ).val('').trigger( 'change' );
			$self.removeClass('themifyFadeIn');
		});
	}

	api.assignments = function( $context ){
		$context.find( '.themify-assignments, .themify-assignment-inner-tabs' ).tabs();
	}

	api.layout = function( $context ) {
		$context.find( '.preview-icon' ).each( function() {
			var $self = $(this),
				$parent = $self.parent(),
				$val = $parent.find('.val'),
				$dataHide,
				dataHide = '',
				context = '';

			if ( $self.closest('.group-hide').length > 0 ) {
				context = 'theme-settings';
				$dataHide = $self.closest('.group-hide');
				dataHide = $dataHide.data( 'hide' );
			} else if ( $self.closest('.themify_field_row').length > 0 ) {
				context = 'custom-panel';
				$dataHide = $self.closest('.themify_field_row');
				if ( 'undefined' !== typeof $dataHide.data( 'hide' ) ) {
					dataHide = $dataHide.data( 'hide' );
				}
			}

			$self.click(function(e){
				e.preventDefault();

				// Change value
				$parent.find('.selected').removeClass('selected');
				$self.addClass('selected');
				$val.val( $self.find('img').attr('alt') ).trigger('change');

				// There are elements to show/hide so do it
				if ( '' !== dataHide ) {
					if ( 'custom-panel' == context ) {
						// All until next data-hide, minus toggled-off those are nested and handled by toggle code, minus items not in list to hide
						var $list = $dataHide.nextUntil('[data-hide]');
						$list.add( $list.find( '.themify_field .hide-if' ) ).not('.toggled-off').filter( '.' + dataHide.replace( /\s/g, ',.' ) ).show().filter( '.' + $val.val() ).hide();
					} else if ( 'theme-settings' == context ) {
						$dataHide.find('.hide-if').filter( '.' + dataHide.replace( /\s/g, ',.' ) ).show().filter( '.' + $val.val() ).hide();
					}
				}

			});

			// All until next data-hide, minus toggled-off those are nested and handled by toggle code, minus items not in list to hide
			if ( '' !== dataHide ) {
				if ( 'custom-panel' == context ) {
					var $list = $dataHide.nextUntil('[data-hide]');
					$list.add( $list.find( '.themify_field .hide-if' ) ).not('.toggled-off').filter( '.' + dataHide.replace( /\s/g, ',.' ) ).filter( '.' + $val.val() ).hide();
				} else if ( 'theme-settings' == context ) {
					$dataHide.find('.hide-if').filter( '.' + dataHide.replace( /\s/g, ',.' ) ).show().filter( '.' + $val.val() ).hide();
				}
			}

		});

		/**
		 * Map layout icons to values and bind clicks
		 */
		$context.find( ".themify_field .preview-icon" ).on( 'click', function(e){
			e.preventDefault();
			$(this).parent().find(".selected").removeClass("selected");
			$(this).addClass("selected");
			$(this).parent().find(".val").val($(this).find("img").attr("alt")).trigger('change');
		});

		$context.find( '.themify_field_row[data-hide]' ).each( function() {
			var dataHide = $( this ).data( 'hide' ),
				hideValues, $selector;

			if( typeof dataHide === 'string' ) {
				dataHide = dataHide.split( ' ' );

				if( dataHide.length > 1 ) {
					hideValues = dataHide.shift();
					hideValues = hideValues.split( '|' );
					$selector = $( '.' + dataHide.join( ', .' ) );

					$( 'select, input', this ).on( 'change', function() {
						var value = $( this ).val();

						if( ! hideValues.includes( value ) && $selector.is( ':visible' ) ) return;
						$selector.toggle( ! hideValues.includes( value ) );
					} ).trigger( 'change' );
				}
			}
		} );
	}

	api.dropdownbutton = function( $context ) {
		$context.find( '.dropdownbutton-group' ).each(function(){
			var $elf = $(this);
			$elf.on('mouseenter mouseleave', '.dropdownbutton-list', function(event){
				event.preventDefault();
				var $a = $(this);
				if($a.hasClass('disabled')) {
					return false;
				}
				if(event.type == 'mouseenter') {
					if(!$a.children('.dropdownbutton').is(':visible')) {
						$a.children('.dropdownbutton').show();
					}
				}
				if(event.type == 'mouseleave') {
					if($a.children('.dropdownbutton').is(':visible')) {
						$a.children('.dropdownbutton').hide();
					}
				}
			});
			$elf.on('click', '.first-ddbtn a', function(event){
				event.preventDefault();
			});
			$elf.on('click', '.ddbtn a', function(event){
				event.preventDefault();
				var ddimgsrc = $(this).find('img').attr('src'),
					val = $(this).data('val'),
					parent = $(this).closest('.dropdownbutton-list'),
					inputID = parent.attr('id');
				$(this).closest('.dropdownbutton-list').find('.first-ddbtn img').attr('src', ddimgsrc);
				$(this).closest('.dropdownbutton').hide();
				$('input#' + inputID).val(val);
				if(parent.next().hasClass('ddbtn-all')) {
					var $ddbtnList, $ddbtnInput;
					if($elf.hasClass('multi-ddbtn')) {
						$ddbtnList = $('.multi-ddbtn-sub', $elf.parent().parent());
						$ddbtnInput = $('.multi-ddbtn-sub + input', $elf.parent().parent());
					} else {
						var inputVal = parent.next();
						$ddbtnList = inputVal.prev().siblings('.dropdownbutton-list');
						$ddbtnInput = inputVal.siblings('input');
					}

					if(parent.next().val() == 'yes') {
						$ddbtnList.addClass('disabled opacity-5');
						$ddbtnList.each(function(){
							var defIcon = $(this).data('def-icon');
							$(this).find('.first-ddbtn img').attr('src', defIcon);
						});
						$ddbtnInput.val(''); // clear value
					} else {
						$ddbtnList.removeClass('disabled opacity-5');
					}

				}
			});
			// disabled other options on dom load
			var selectAll = $elf.find('input.ddbtn-all');
			if( selectAll.val() == 'yes' ) {
				if($elf.hasClass('multi-ddbtn')) {
					$('.multi-ddbtn-sub', $elf.parent().parent()).addClass('disabled opacity-5');
				} else {
					selectAll.prev().siblings('.dropdownbutton-list').addClass('disabled opacity-5');
				}
			}
		});
	}

	api.gallery_shortcode = function(){
		var clone = wp.media.gallery.shortcode, wpgallery = wp.media.gallery, file_frame, frame;

		$( 'body' ).on( 'click', '.themify-gallery-shortcode-btn', function(event) {
			var shortcode_val = $(this).closest('.themify_field').find('.themify-gallery-shortcode-input');
	
			if(shortcode_val.html()){
				shortcode_val.val(shortcode_val.html());
				shortcode_val.html('');
				shortcode_val.text('');
			}
	
			if (file_frame) {
				file_frame.open();
			} else {
				if ($.trim(shortcode_val.val()).length > 0) {
					file_frame = wpgallery.edit($.trim(shortcode_val.val()));
				} else {
					file_frame = wp.media.frames.file_frame = wp.media({
						frame : 'post',
						state : 'gallery-edit',
						title : wp.media.view.l10n.editGalleryTitle,
						editing : true,
						multiple : true,
						selection : false
					});
				}
			}
	
			wp.media.gallery.shortcode = function(attachments) {
				var props = attachments.props.toJSON(), attrs = _.pick(props, 'orderby', 'order');
	
				if (attachments.gallery)
					_.extend(attrs, attachments.gallery.toJSON());
	
				attrs.ids = attachments.pluck('id');
	
				// Copy the `uploadedTo` post ID.
				if (props.uploadedTo)
					attrs.id = props.uploadedTo;
	
				// Check if the gallery is randomly ordered.
				if (attrs._orderbyRandom)
					attrs.orderby = 'rand';
				delete attrs._orderbyRandom;
	
				// If the `ids` attribute is set and `orderby` attribute
				// is the default value, clear it for cleaner output.
				if (attrs.ids && 'post__in' === attrs.orderby)
					delete attrs.orderby;
	
				// Remove default attributes from the shortcode.
				_.each(wp.media.gallery.defaults, function(value, key) {
					if (value === attrs[key])
						delete attrs[key];
				});
	
				var shortcode = new wp.shortcode({
					tag : 'gallery',
					attrs : attrs,
					type : 'single'
				});
	
				shortcode_val.val(shortcode.string());
	
				wp.media.gallery.shortcode = clone;
				return shortcode;
			};
	
			file_frame.on('update', function(selection) {
				var shortcode = wp.media.gallery.shortcode(selection).string().slice(1, -1);
				shortcode_val.val('[' + shortcode + ']');
			});
	
			if ($.trim(shortcode_val.val()).length == 0) {
				$('.media-menu').find('.media-menu-item').last().trigger('click');
			}
			event.preventDefault();
		});
	};

	api.set_cookie = function (name, value) {
		document.cookie = name+"="+value+"; path=/";
	}

	api.get_cookie = function (name) {
		name = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0; i < ca.length; i++) {
			var c = ca[i];
			while (' ' == c.charAt(0)) c = c.substring(1,c.length);
			if (0 == c.indexOf(name)) return c.substring(name.length,c.length);	}
		return null;
	}

	// @deprecated
	// revision needed
	api.enable_toggle = function(){
		var $enableToggle = $( '.enable_toggle' );
		if($enableToggle.length > 0){
			$enableToggle.each(function(){
				var context = $(this).closest('.themify_write_panel');
				$('.themify-toggle', context).hide().addClass('toggled-off');
			});
		}
		$('.enable_toggle .preview-icon').on('click', function(e){
			// toggle
			var img_alt = $(this).find("img").attr("alt"),
				toggle_class = ($.trim(img_alt) != '') ? '.'+img_alt+'-toggle' : '.default-toggle';
			$(this).closest('.inside').find('.themify-toggle').hide().addClass('toggled-off');
			$(this).closest('.inside').find( toggle_class ).show().removeClass('toggled-off');
			e.preventDefault();
		});
		$('.enable_toggle .preview-icon.selected').each(function(){
			var img_alt = $(this).find("img").attr("alt"),
				toggle_class = (img_alt != '' && img_alt != 'default') ? '.'+img_alt+'-toggle' : '.default-toggle';
			$( toggle_class ).show().removeClass('toggled-off');
		});

		// Toggle Post Format Fields by Radio Button
		$('.enable_toggle input[type=radio]').on('click', function() {
			var val = $(this).val(),
				toggle_class = (val != 0 && val != '') ? '.'+val+'-toggle' : '.default-toggle',
				$siblings = $(this).siblings('input[type=radio]');

			$siblings.each(function(){
				var sib_val = $(this).val();
				if ( sib_val != 0 && sib_val !== '' ) $( '.' + sib_val + '-toggle').hide().addClass('toggled-off');
			});

			$(toggle_class).each(function(){
				$(this).show().removeClass('toggled-off');
				if ( $(this).hasClass('enable_toggle_child') ) {
					var $child_siblings = $(this).find('input[type=radio]:checked').siblings('input[type=radio]');
					$child_siblings.each(function(){
						var sib_val = $(this).val();
						setTimeout(function(){
							if ( sib_val != 0 && sib_val !== '' ) $( '.' + sib_val + '-toggle').hide().addClass('toggled-off');
						}, 500);
					});
				}
			});
		});
		$enableToggle.each(function(){
			var $checked = $(this).find('input[type="radio"]:checked'),
				val = $checked.val(),
				toggle_class = (val != 0 && val !== '') ? '.'+val+'-toggle' : '.default-toggle';
			
			$(toggle_class).each(function(){
				$(this).show().removeClass('toggled-off');
				if ( $(this).hasClass('enable_toggle_child') ) {
					var $child_siblings = $(this).find('input[type=radio]:checked').siblings('input[type=radio]');
					$child_siblings.each(function(){
						var sib_val = $(this).val();
						setTimeout(function(){
							if ( sib_val != 0 && sib_val !== '' ) $( '.' + sib_val + '-toggle').hide().addClass('toggled-off');
						}, 500);
					});
				}
			});
		});

		// Toggle Post Format Fields by Checkbox.
		// Works with single checkbox selection, not yet with combinations.
		$('.enable_toggle input[type="checkbox"]').on('click', function() {
			var val = $(this).data('val'),
				toggle_class = (val != 0 && val != '') ? '.'+val+'-toggle' : '.default-toggle';

			$(this).closest('.inside').find('.themify-toggle').hide().addClass('toggled-off');

			if($(this).prop('checked')){
				$(this).closest('.inside').find( toggle_class ).show().removeClass('toggled-off');
			}
		});
		$('.enable_toggle input[type="checkbox"]:checked').each(function() {
			var val = $(this).data('val'),
				toggle_class = (val != 0 && val !== '') ? '.'+val+'-toggle' : '.default-toggle';
			$( toggle_class ).show().removeClass('toggled-off');
		});
	}

	api.query_category = function(){
		/**
		 * Bind categories select to value field
		 */
		var $themifyField = $('.themify_field'),
			$themifyInfoLink = $('.themify-info-link');

		$themifyField.find('.query_category').blur(function(){
			var $self = $(this), value = $self.val();
			$(this).parent().find('.val').val( value );
			toggleQueryCategoryFields( $self, value );
		}).keyup(function(){
			var $self = $(this), value = $self.val();
			$(this).parent().find('.val').val( value );
			toggleQueryCategoryFields( $self, value );
		});

		$themifyField.find('.query_category_single').change(function() {
			var $self = $(this), value = $self.val();
			$self.parent().find('.query_category, .val').val( value );
			toggleQueryCategoryFields( $self, value );
		}).closest('.themify_field_row').addClass('query-field-visible');
		$themifyInfoLink.closest('.themify_field_row').addClass('query-field-visible');

		$('.query_category_single, .query_category').each(function(){
			var $self = $(this), value = $self.val();
			toggleQueryCategoryFields( $self, value );
		});
		$themifyInfoLink.closest('.themify_field_row').removeClass('query-field-hide');

		function toggleQueryCategoryFields( $obj, value ) {
			if ( '' != value ) {
				$obj.closest('.inside').find('.themify_field_row').removeClass('query-field-hide');
			} else {
				$obj.closest('.inside').find('.themify_field_row').not( $obj.closest( '.themify_field_row' ) ).not('.query-field-visible').addClass('query-field-hide');
			}
		}
	}

	api.post_meta_checkbox = function() {
		$('.post-meta-group').each(function(){
			var $elf = $(this);
			if($('.meta-all', $elf).prop('checked')){
				$('.meta-sub', $elf).prop('disabled', true).parent().addClass('opacity-7');
			}
			$elf.on('click', '.meta-all', function(){
				var $all = $(this);
				if($all.prop('checked')){
					//$all.prop('checked', true);
					$('.meta-sub', $elf).prop('disabled', true).prop('checked', false).parent().addClass('opacity-7');
				} else {
					//$all.prop('checked', false);
					$('.meta-sub', $elf).prop('disabled', false).parent().removeClass('opacity-7');
				}
			});
		});

		/**
		* Post meta checkboxes - Mostly the same than before, but adding hidden field update.
		*/
		$('.custom-post-meta-group').each(function(){
			var $elf = $(this),
				states_str = $('input[type="text"]', $elf).val(),
				states = {},
				state = [],
				states_arr = [];

			// Backwards compatibility
			if('yes' === states_str){
				$('.meta-all', $elf).val('yes').prop('checked', true);
				$('.meta-sub', $elf).val('yes').prop('disabled', true).parent().addClass('opacity-7');
			} else {
				// Parse string
				states_arr = states_str.split('&');
				for (var i = 0; i < states_arr.length; i++) {
					state = states_arr[i].split('=');
					states[state[0]] = state[1];
				}
				for ( var meta in states ) {
					if ( states.hasOwnProperty(meta) ) {
						if ( 'yes' === states[meta] ) {
							$('#' + meta, $elf).val('yes').prop('checked', true);
						}
					}
				}
				if($('.meta-all', $elf).prop('checked')){
					$('.meta-sub', $elf).prop('disabled', true).prop('checked', false).parent().addClass('opacity-7');
				}
			}
			$elf.on('click', '.meta-all', function(){
				var $all = $(this);
				if($all.prop('checked')){
					$('.meta-sub', $elf).val('yes').prop('disabled', true).prop('checked', false).parent().addClass('opacity-7');
					$all.val('yes');
				} else {
					$('.meta-sub', $elf).val('no').prop('disabled', false).parent().removeClass('opacity-7');
					$all.val('no');
				}
				savePostMetaStates($elf);
			});
			$elf.on('click', '.meta-sub', function(){
				var $sub = $(this);
				if($sub.prop('checked')){
					$sub.val('yes');
				} else {
					$sub.val('no');
				}
				savePostMetaStates($elf);
			});
		});
	}

	function savePostMetaStates( $et ) {
		var state = '';
		$('input[type="checkbox"]', $et).each(function(){
			state += $(this).attr('id') + '=' + $(this).val() + '&';
		});
		$('input[type="text"]', $et).val(state.slice(0,-1));
	}

	api.init();
	return api;
})(jQuery);