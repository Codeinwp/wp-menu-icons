(function( $ ) {
	/**
	 * Settings box tabs
	 *
	 * We can't use core's tabs script here because it will clear the
	 * checkboxes upon tab switching
	 */
	$( '#menu-icons-settings-tabs' )
		.on( 'click', 'a.mi-settings-nav-tab', function( e ) {
			e.preventDefault();
			e.stopPropagation();

			var $el     = $( this ).blur(),
			    $target = $( '#' + $el.data( 'type' ) );

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
			url:  menuIcons.ajaxUrls.update,
			data: $( '#menu-icons-settings :input' ).serialize(),

			success: function( response ) {
				if ( true === response.success && response.data.redirectUrl ) {
					window.location = response.data.redirectUrl;
				} else {
					$button.prop( 'disabled', false );
				}
			},

			always: function() {
				$spinner.hide();
			}
		});
	});
})( jQuery );

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
			target:      miPicker.target,
			ipTypes:     _.filter( iconPicker.types, miPicker.typesFilter ),
			SidebarView: wp.media.view.MenuIconsSidebar
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
