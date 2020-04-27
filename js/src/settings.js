( function( $ ) {
	/**
	 * Settings box tabs
	 *
	 * We can't use core's tabs script here because it will clear the
	 * checkboxes upon tab switching
	 */
	$( '#menu-icons-settings-tabs' )
		.on( 'click', 'a.mi-settings-nav-tab', function( e ) {
			var $el     = $( this ).blur(),
			    $target = $( '#' + $el.data( 'type' ) );

			e.preventDefault();
			e.stopPropagation();

			$el.parent().addClass( 'tabs' ).siblings().removeClass( 'tabs' );
			$target
				.removeClass( 'tabs-panel-inactive' )
				.addClass( 'tabs-panel-active' )
				.show()
				.siblings( 'div.tabs-panel' )
					.hide()
					.addClass( 'tabs-panel-inactive' )
					.removeClass( 'tabs-panel-active' );
		})
		.find( 'a.mi-settings-nav-tab' ).first().click();

	// Settings meta box
	$( '#menu-icons-settings-save' ).on( 'click', function( e ) {
		var $button  = $( this ).prop( 'disabled', true ),
		    $spinner = $button.siblings( 'span.spinner' );

		e.preventDefault();
		e.stopPropagation();

		$spinner.css({
			display: 'inline-block',
			visibility: 'visible'
		});

		$.ajax({
			type: 'POST',
			url:  window.menuIcons.ajaxUrls.update,
			data: $( '#menu-icons-settings :input' ).serialize(),

			success: function( response ) {
				if ( response.success && response.data.redirectUrl === true ) {
					window.location = response.data.redirectUrl;
				} else {
					$button.prop( 'disabled', false );
				}
				$spinner.hide();
			},

			fail: function() {
				$spinner.hide();
			}
		});
	});
})( jQuery );
