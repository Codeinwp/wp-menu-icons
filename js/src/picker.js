(function( $ ) {
'use strict';

if ( ! menuIcons.activeTypes || _.isEmpty( menuIcons.activeTypes ) ) {
	return;
}

var miPicker = {
	templates: {},
	wrapClass: 'div.menu-icons-wrap',
	frame:     null,
	target:    new wp.media.model.IconPickerTarget(),

	// TODO: Move to frame view
	typesFilter: function( type ) {
		return ( -1 < $.inArray( type.id, menuIcons.activeTypes ) );
	},

	createFrame: function() {
		miPicker.frame = new wp.media.view.MediaFrame.MenuIcons({
			target:  miPicker.target,
			ipTypes: _.filter( iconPicker.types, miPicker.typesFilter )
		});
	},

	pickIcon: function( model ) {
		miPicker.frame.target.set( model, { silent: true } );
		miPicker.frame.open();
	},

	setUnset: function( e ) {
		var $el      = $( e.currentTarget ),
		    $clicked = $( e.target );

		e.preventDefault();

		if ( $clicked.hasClass( '_select' ) || $clicked.hasClass( '_icon' ) ) {
			miPicker.setIcon( $el );
		} else if ( $clicked.hasClass( '_remove' ) ) {
			miPicker.unsetIcon( $el );
		}
	},

	setIcon: function( $el ) {
		var id     = $el.data( 'id' ),
		    frame  = miPicker.frame,
		    items  = frame.menuItems,
		    model  = items.get( id );

		if ( model ) {
			miPicker.pickIcon( model.toJSON() );
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
		miPicker.pickIcon( model );
	},

	unsetIcon: function( $el ) {
		var id = $el.data( 'id' );

		$el.find( 'div._settings input' ).val( '' );
		$el.trigger( 'mi:update' );
		miPicker.frame.menuItems.remove( id );
	},

	updateField: function( e ) {
		var $el    = $( e.currentTarget ),
		    $set   = $el.find( 'a._select' ),
		    $unset = $el.find( 'a._remove' ),
		    type   = $el.find( 'input._mi-type' ).val(),
		    icon   = $el.find( 'input._mi-icon' ).val(),
		    url    = $el.find( 'input._mi-url' ).val(),
		    template;

		if ( '' === type || '' === icon || 0 > _.indexOf( menuIcons.activeTypes, type ) ) {
			$set.text( menuIcons.text.select ).attr( 'title', '' );
			$unset.hide();

			return;
		}

		if ( miPicker.templates[ type ] ) {
			template = miPicker.templates[ type ];
		} else {
			template = miPicker.templates[ type ] = wp.template( 'menu-icons-item-field-preview-' + iconPicker.types[ type ].templateId );
		}

		$unset.show();
		$set.attr( 'title', menuIcons.text.change );
		$set.html( template({
			type: type,
			icon: icon,
			url:  url
		}) );
	},

	init: function() {
		miPicker.createFrame();
		$( document )
			.on( 'click', miPicker.wrapClass, miPicker.setUnset )
			.on( 'mi:update', miPicker.wrapClass, miPicker.updateField );

		$( miPicker.wrapClass ).trigger( 'mi:update' );
	}
};

miPicker.init();
}( jQuery ) );
