(function( $ ) {
var self = {
	frame:  null,
	target: new wp.media.model.IconPickerTarget(),

	typesFilter: function( type ) {
		return ( -1 < $.inArray( type.id, menuIconsPicker.activeTypes ) );
	},

	createFrame: function() {
		self.frame = new wp.media.view.MediaFrame.MenuIcons({
			target:  self.target,
			ipTypes: _.filter( iconPicker.types, self.typesFilter )
		});
	},

	setIcon: function( e ) {
		var $el      = $( e.currentTarget ),
		    $clicked = $( e.target );

		e.preventDefault();

		if ( $clicked.hasClass( '_select' ) ) {
			self.frame.miPick( $el );
		}
	},

	init: function() {
		self.createFrame();
		$( document ).on( 'click', 'div.menu-icons-wrap', self.setIcon );
	}
};

self.init();
}( jQuery ) );
