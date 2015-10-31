(function( $ ) {
var self = {
	frame: null,
	target: new wp.media.model.IconPickerTarget(),

	typesFilter: function( type ) {
		return ( -1 < $.inArray( type.id, window.menuIconsPicker.activeTypes ) );
	},

	updateFields: function() {},

	unBindUpdateFields: function() {
		self.frame.off( 'open', self.updateFields );
	},

	createFrame: function() {
		self.frame = new wp.media.view.MediaFrame.IconPicker({
			target:  self.target,
			ipTypes: _.filter( window.iconPicker.types, self.typesFilter )
		});

		self.frame.on( 'close', self.unBindUpdateFields );
	}
};

self.createFrame();

$( document ).on( 'click', 'div.menu-icons-wrap a._select', function( e ) {
	e.preventDefault();
	self.frame.open();
});
}( jQuery ) );
