(function( $ ) {
var self = {
	wrapClass: 'div.menu-icons-wrap',
	frame:      null,
	target:     new wp.media.model.IconPickerTarget(),

	// TODO: Move to frame view
	typesFilter: function( type ) {
		return ( -1 < $.inArray( type.id, menuIconsPicker.activeTypes ) );
	},

	createFrame: function() {
		self.frame = new wp.media.view.MediaFrame.MenuIcons({
			target:  self.target,
			ipTypes: _.filter( iconPicker.types, self.typesFilter )
		});
	},

	pickIcon: function( model ) {
		self.frame.target.set( model, { silent: true } );
		self.frame.open();
	},

	setUnset: function( e ) {
		var $el      = $( e.currentTarget ),
		    $clicked = $( e.target );

		e.preventDefault();

		if ( $clicked.hasClass( '_select' ) ) {
			self.setIcon( $el );
		} else if ( $clicked.hasClass( '_remove' ) ) {
			self.unsetIcon( $el );
		}
	},

	setIcon: function( $el ) {
		var id     = $el.data( 'id' ),
		    frame  = self.frame,
		    items  = frame.menuItems,
		    model  = items.get( id );

		if ( model ) {
			self.pickIcon( model.toJSON() );
			return;
		}

		model = {
			id:      id,
			$el:     $el,
			$inputs: {}
		};

		$el.find( 'div._settings input' ).each( function() {
			var $input = $( this ),
			    key    = $input.attr( 'class' ).replace( '_mi-', '' );

			model[ key ]         = $input.val();
			model.$inputs[ key ] = $input;
		});

		items.add( model );
		self.pickIcon( model );
	},

	unsetIcon: function( $el ) {
		var id = $el.data( 'id' );

		$el.find( 'div._settings input' ).val( '' );
		$el.trigger( 'mi:update' );
		self.frame.menuItems.remove( id );
	},

	toggleSetUnset: function( e ) {
		var $el    = $( e.currentTarget ),
		    $set   = $el.find( 'a._select' ),
		    $unset = $el.find( 'a._remove' ),
		    type   = $el.find( 'input._mi-type' ).val(),
		    icon   = $el.find( 'input._mi-icon' ).val();

		if ( '' !== type && '' !== icon ) {
			$set.show(); // TODO: Update preview
			$unset.show();
		} else {
			$set.text( $set.data( 'text' ) ).show();
			$unset.hide();
		}
	},

	init: function() {
		self.createFrame();
		$( document )
			.on( 'click', self.wrapClass, self.setUnset )
			.on( 'mi:update', self.wrapClass, self.toggleSetUnset );

		$( self.wrapClass ).trigger( 'mi:update' );
	}
};

self.init();
}( jQuery ) );
