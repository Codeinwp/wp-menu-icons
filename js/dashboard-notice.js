jQuery( document ).ready( function( $ ) {
  $( '.menu-icon-dashboard-notice' ).on( 'click', 'button.notice-dismiss', function() {
    $.post( window.menuIcons.ajaxUrls,
    {
      action: 'wp_menu_icons_dismiss_dashboard_notice',
      _nonce: window.menuIcons._nonce,
      dismiss: 1,
    },
    function( res ) {}
    )
  } );
} );