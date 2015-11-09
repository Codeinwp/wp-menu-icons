(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
wp.media.model.MenuIconsItem = require( './models/item.js' );
wp.media.view.MenuIconsSidebar = require( './views/sidebar.js' );
wp.media.view.MediaFrame.MenuIcons = require( './views/frame.js' );

},{"./models/item.js":2,"./views/frame.js":3,"./views/sidebar.js":4}],2:[function(require,module,exports){
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

},{}],4:[function(require,module,exports){
/**
 * wp.media.view.MenuIconsSidebar
 */
var MenuIconsSidebar = wp.media.view.IconPickerSidebar.extend({
	createSingle: function() {
		console.log( 'create single' );
	},
	disposeSingle: function() {}
});

module.exports = MenuIconsSidebar;

},{}]},{},[1]);
