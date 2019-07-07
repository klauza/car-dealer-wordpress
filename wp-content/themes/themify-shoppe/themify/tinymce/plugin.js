/**
 * Routines to add a menu button in WP 3.9 Editor
 */
tinymce.PluginManager.add('themifyMenu', function( editor, url ) {

	'use strict';

	function createColorPickAction() {
		var colorPickerCallback = editor.settings.color_picker_callback;

		if ( colorPickerCallback ) {
			return function() {
				var self = this;

				colorPickerCallback.call(
					editor,
					function( value ) {
						self.value( value ).fire( 'change' );
					},
					self.value()
				);
			};
		}
	}

	/**
	 * Create and return a TinyMCE menu item
	 */
	function add_item( shortcode ) {
		var item = {
			'text' : shortcode.label,
			'body' : {
				type: shortcode.id
			},
			onclick : function(){
				if( jQuery.isEmptyObject( shortcode.fields ) ) {
					// this shortcode has no options to configure
					var values = {};
					values.selectedContent = editor.selection.getContent();
					var template = wp.template( 'themify-shortcode-' + shortcode.id );
					editor.insertContent( template( values ) );
				} else {

					var fields = [];
					jQuery.each( shortcode.fields, function( i, field ){
						if( field.type == 'colorbox' ) {
							field.onaction = createColorPickAction()
						} else if( field.type == 'image' ) {
							/* create an image uploader */
							field = {
								type : 'container',
								label : field.label,
								layout : 'flex',
								direction : 'row',
								items : [
									{ type : 'textbox', name : field.name },
									{ type : 'button', text : field.text, onclick : function(){
										var $this = jQuery( this.$el );
										var file_frame = wp.media.frames.file_frame = wp.media({
											multiple: false  // Set to true to allow multiple files to be selected
										});
										// When an image is selected, run a callback.
										file_frame.on( 'select', function() {
											// We set multiple to false so only get one image from the uploader
											var attachment = file_frame.state().get( 'selection' ).first().toJSON();

											$this.prev().val( attachment.url );

										});
										file_frame.open();
									} }
								]
							};
						} else if( field.type == 'iconpicker' ) {
							/* create an icon picker */
							field = {
								type : 'container',
								label : field.label,
								layout : 'flex',
								direction : 'row',
								items : [
									{ type : 'textbox', name : field.name },
									{ type : 'button', text : field.text, onclick : function(){
										var $this = jQuery( this.$el );
										if(document.getElementById("themify_builder_site_canvas_iframe"))
											var Themify_Icons = document.getElementById("themify_builder_site_canvas_iframe").contentWindow.Themify_Icons;
										Themify_Icons.target = $this.prev(); // set the input text box that recieves the value
										Themify_Icons.showLightbox( Themify_Icons.target.val() ); // show the icon picker lightbox
									} }
								]
							};
						}
						fields.push( field );
					} );

					editor.windowManager.open({
						'title' : shortcode.label,
						'body' : fields,
						onSubmit : function( e ){
							var values = this.toJSON(); // get form field values
							values.selectedContent = editor.selection.getContent();
							var template = wp.template( 'themify-shortcode-' + shortcode.id );
							editor.insertContent( template( values ) );
						}
					});
				}
			}
		};

		return item;
	}

	var items = [];
	jQuery.each( themifyEditor.shortcodes, function( key, shortcode ){
		shortcode.id = key;

		if( typeof shortcode.menu == 'object' ) {
			var menu = []; // list of submenus
			jQuery.each( shortcode.menu, function( sub_key, sub_item ){
				sub_item.id = sub_key;
				menu.push( add_item( sub_item ) );
			});
			items.push( {
				'text' : shortcode.label,
				'menu' : menu
			} );
		} else {
			items.push( add_item( shortcode ) );
		}
	} );

	editor.addButton( 'btnthemifyMenu', {
		type: 'menubutton',
		text: '',
		image: themifyEditor.editor.icon,
		tooltip: themifyEditor.editor.menuTooltip,
		menu: items
	} );

});