(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
wp.media.model.MenuIconsItem = require( './models/item.js' );
wp.media.view.MediaFrame.MenuIcons = require( './views/frame.js' );

},{"./models/item.js":2,"./views/frame.js":3}],2:[function(require,module,exports){
/**
 * wp.media.model.MenuIconsItem
 */
var Item = Backbone.Model.extend({
	initialize: function() {
		this.on( 'change', this.updateValues, this );
	},

	updateValues: function() {
		_.each( this.get( '$inputs' ), function( $input, key ) {
			$input.val( this.get( key ) );
		}, this );

		this.get( '$el' ).trigger( 'mi:update' );
	}
});

module.exports = Item;

},{}],3:[function(require,module,exports){
/**
 * wp.media.view.MediaFrame.MenuIcons
 */
var MenuIcons = wp.media.view.MediaFrame.IconPicker.extend({
	initialize: function() {
		this.menuItems = new Backbone.Collection( [], {
			model: wp.media.model.MenuIconsItem
		});

		wp.media.view.MediaFrame.IconPicker.prototype.initialize.apply( this, arguments );

		this.listenTo( this.target, 'change', this.miUpdateItemProps );
	},

	miUpdateItemProps: function() {
		var id    = this.target.id,
		    model = this.menuItems.get( id ),
		    data  = this.target.toJSON();

		model.set( data );
		this.target.clear({ silent: true });
	}
});

module.exports = MenuIcons;

},{}]},{},[1]);

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
