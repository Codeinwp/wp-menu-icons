(function( $ ) {
var self = {
	templates: {},
	wrapClass: 'div.menu-icons-wrap',
	frame:     null,
	target:    new wp.media.model.IconPickerTarget(),

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

		if ( $clicked.hasClass( '_select' ) || $clicked.hasClass( '_icon' ) ) {
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

	updateField: function( e ) {
		var $el    = $( e.currentTarget ),
		    $set   = $el.find( 'a._select' ),
		    $unset = $el.find( 'a._remove' ),
		    type   = $el.find( 'input._mi-type' ).val(),
		    icon   = $el.find( 'input._mi-icon' ).val(),
		    template;

		if ( '' === type || '' === icon || 0 > _.indexOf( menuIconsPicker.activeTypes, type ) ) {
			$set.text( $set.data( 'text' ) ).attr( 'title', '' );
			$unset.hide();

			return;
		}

		if ( self.templates[ type ] ) {
			template = self.templates[ type ];
		} else {
			template = self.templates[ type ] = wp.template( 'menu-icons-item-field-preview-' + iconPicker.types[ type ].templateId );
		}

		$unset.show();
		$set.attr( 'title', $set.data( 'text' ) );
		$set.html( template({
			type: type,
			icon: icon
		}) );
	},

	init: function() {
		self.createFrame();
		$( document )
			.on( 'click', self.wrapClass, self.setUnset )
			.on( 'mi:update', self.wrapClass, self.updateField );

		$( self.wrapClass ).trigger( 'mi:update' );
	}
};

self.init();
}( jQuery ) );
