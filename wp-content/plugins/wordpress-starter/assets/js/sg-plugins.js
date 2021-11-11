/*global ajaxurl*/
;(function( $ ) {
	$(document).ready(function() {
		let hash = window.location.hash;

		if ( hash ) {
			$( hash )
				.addClass( 'sg-tabs_content--active' )
				.siblings()
					.removeClass( 'sg-tabs_content--active' );

			$( '.sg-tabs__tab' ).removeClass( 'sg-tabs__tab--active' );
			$( '[data-tab="' + hash.substr(1) + '"]' ).addClass( 'sg-tabs__tab--active' );
		}

		$( '.sg-tabs__tab' ).click( function( e ) {
			e.preventDefault();
			e.stopPropagation();

			changeTab( this )
		})

		if ( $('body.sg-plugin-information').length ) {
			$(this)
				.removeClass( 'sg-plugin-information' )

			$('html').addClass( 'sg-plugin-information' );
		}

		$( '.sg-button-load-more' ).on( 'click', function (e) {
			e.preventDefault();
			e.stopPropagation();

			$('.sg-load-more-section').addClass( 'visible' );

			let button  = $(this);
				page_id = $(this).attr( 'data-page' ) ;
				type    = $( '.sg-tabs_content--active .sg-tab-content' ).data( 'tab-content' );
				data = {
					action: "ajax_plugins",
					page_id: page_id,
					type: type,
					elemendId: type,
				}

			if ( $('#s').val() ) {
				data.s = $( '#s' ).val();
				data.searchType = $( '#typeselector' ).val();
			}
			
			loadMorePlugins( data, button );
		})

		$('.sg-button-search').on('click', function (e) {
			e.preventDefault();
			e.stopPropagation();
			$('.sg-load-more-section').addClass( 'visible' );

			let tabId = 'default'
				button = $( '#' + tabId + ' .sg-button-load-more' );
				data = {
					action: "ajax_plugins",
					page_id: 1,
					type: 'default',	
					elemendId: tabId,
					s: $('#s').val(),
					searchType: $( '#typeselector' ).val()
				}

			$( '[data-tab-content=' + tabId + ']' ).html('');

			loadMorePlugins( data, button );
			changeTab( $( '[data-tab="' + tabId + '"]' ) );
		})

		$('.sg-central-tab-content').on('click', '.sg-plugin-install', function (e) {
			e.preventDefault();
			e.stopPropagation();
			let button = $(this)

			button.find( '.sg-button__content' ).text( 'Installing...' );

			$.post({
				type : "GET",
				url : button.attr('href'),
				success: function( response ) {
					button.removeClass( 'sg-plugin-install' ).addClass( 'sg-plugin-activate' )
					button.find( '.sg-button__content' ).text( 'Activate' );
					button.find( '.sg-ripple-container' ).removeClass('sg-button--outlined')
					button.attr( 'href', button.data( 'activate' ) )
				}
			})
		})

		$('.sg-central-tab-content').on('click', '.sg-plugin-update', function (e) {
			e.preventDefault();
			e.stopPropagation();
			let button = $(this)

			button.find( '.sg-button__content' ).text( 'Updating...' );

			$.post({
				type : "GET",
				url : button.attr('href'),
				success: function( response ) {
					button.removeClass( 'sg-plugin-update' ).addClass( 'sg-plugin-activate' )
					button.find( '.sg-button__content' ).text( 'Activate' );
					button.find( 'button' ).removeClass( 'sg-button--plugin-update' );
					button.attr( 'href', button.data( 'activate' ) )
				}
			})
		})

		$('.sg-central-tab-content').on('click', '.sg-plugin-activate', function (e) {
			e.preventDefault();
			e.stopPropagation();
			let button = $(this)

			button.find( '.sg-button__content' ).text( 'Activating...' );

			$.post({
				type : "GET",
				url : button.attr('href'),
				success: function( response ) {

					button.find( '.sg-button__content' ).text( 'Active' );
					button.find( '.sg-ripple-container' ).removeClass('sg-button--outlined')
					button.find( 'button' ).removeClass( 'sg-button--primary' );
					button.attr( 'href', '' );
				}
			})
		})
	})

	function loadMorePlugins( data, element ) {
		$.post({
			type : "GET",
			url : ajaxurl,
			data : data,
			success: function( response ) {
				$('.sg-load-more-section').removeClass( 'visible' );
				$( '[data-tab-content=' + data.elemendId + ']' ).append( response );
				element
					.removeAttr( 'data-page' )
					.attr( 'data-page', ( parseInt( data.page_id ) + 1 ) );
			}
		})
	}

	function changeTab( tab ) {
		$(tab)
			.addClass( 'sg-tabs__tab--active' )
			.siblings()
				.removeClass( 'sg-tabs__tab--active' );

		let contentId = '#' + $(tab).attr('data-tab');

		$( contentId )
			.addClass( 'sg-tabs_content--active' )
			.siblings()
				.removeClass( 'sg-tabs_content--active' );

		window.location.hash = contentId
	}
})( jQuery )
