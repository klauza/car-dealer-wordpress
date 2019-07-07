;(function ( $, window, document, undefined ) {

	'use strict';

	$.fn.themifyDropdown = function( options ) {

		return this.each(function(){
			if( $(this).hasClass( 'with-sub-arrow' ) )
				return;

			$(this).addClass( 'with-sub-arrow' )
				.find( 'li.menu-item-has-children > a:not(.themify_lightbox), li.page_item_has_children > a:not(.themify_lightbox)' )
				.append( '<span class="sub-arrow closed" />' );
		});
	};

	var startX,
		startY;

	$('body').on('touchstart', '.sub-arrow, .with-sub-arrow a', function(e){
		e.stopPropagation();
		startX = getCoord(e, 'X');
		startY = getCoord(e, 'Y');
	})
	.on( 'click touchend', '.sub-arrow', function(e){
		e.stopPropagation();
		// If movement is less than 20px, execute the handler
		if (Math.abs(getCoord(e, 'X') - startX) < 20 && Math.abs(getCoord(e, 'Y') - startY) < 20) {
			var menu_item = $( this ).closest( 'li' ),
                            active_tree = $( this ).parents( '.dropdown-open' );
			$( this ).closest( '.with-sub-arrow' ) // get the menu container
				.find( 'li.dropdown-open' ).not( active_tree ) // find open (if any) dropdowns
				.each(function(){
					close_dropdown( $( this ) );
				});

			if( menu_item.hasClass( 'dropdown-open' ) ) {
				close_dropdown( menu_item );
			} else {
				open_dropdown( menu_item );
			}

		}

		return false;
	} )
	// clicking menu items where the URL is only "#" is the same as clicking the dropdown arrow
	.on( 'click touchend', '.with-sub-arrow a', function(e){
		// If movement is less than 20px, execute the handler
		if (Math.abs(getCoord(e, 'X') - startX) < 20 && Math.abs(getCoord(e, 'Y') - startY) < 20) {
			if( $( this ).attr( 'href' ) === '#' ) {
				e.stopPropagation();
				$( this ).find( '> .sub-arrow' ).click();
				return false;
			}
		}
	} );

	function getCoord(e, c) {
		return /touch/.test(e.type) ? (e.originalEvent || e).changedTouches[0]['page' + c] : e['page' + c];
	}

	function open_dropdown( $li ) {
		$li.find( '.sub-menu, .children' ).first()
			.show().css( 'visibility', 'visible' );

		$li.addClass( 'dropdown-open' ).find( '> a .sub-arrow' ).removeClass( 'closed' ).addClass( 'open' );
		$li.trigger( 'dropdown_open' );
	}

	function close_dropdown( $li ) {
		$li.find( '.sub-menu, .children' ).first()
			.hide().css( 'visibility', 'hidden' );

		$li.removeClass( 'dropdown-open' ).find( '> a .sub-arrow' ).removeClass( 'open' ).addClass( 'closed' );
		$li.trigger( 'dropdown_close' );
	}
})( jQuery, window, document );