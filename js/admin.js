(function( $ ) {
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
wp.media.model.MenuIconsItemSettingField = require( './models/item-setting-field.js' );
wp.media.model.MenuIconsItemSettings = require( './models/item-settings.js' );
wp.media.model.MenuIconsItem = require( './models/item.js' );

wp.media.view.MenuIconsItemSettingField = require( './views/item-setting-field.js' );
wp.media.view.MenuIconsItemSettings = require( './views/item-settings.js' );
wp.media.view.MenuIconsItemPreview = require( './views/item-preview.js' );
wp.media.view.MenuIconsSidebar = require( './views/sidebar.js' );
wp.media.view.MediaFrame.MenuIcons = require( './views/frame.js' );

},{"./models/item-setting-field.js":2,"./models/item-settings.js":3,"./models/item.js":4,"./views/frame.js":5,"./views/item-preview.js":6,"./views/item-setting-field.js":7,"./views/item-settings.js":8,"./views/sidebar.js":9}],2:[function(require,module,exports){
/**
 * wp.media.model.MenuIconsItemSettingField
 *
 * @class
 * @augments Backbone.Model
 */
var MenuIconsItemSettingField = Backbone.Model.extend({
	defaults: {
		id:    '',
		label: '',
		value: '',
		type:  'text'
	}
});

module.exports = MenuIconsItemSettingField;

},{}],3:[function(require,module,exports){
/**
 * wp.media.model.MenuIconsItemSettings
 *
 * @class
 * @augments Backbone.Collection
 */
var MenuIconsItemSettings = Backbone.Collection.extend({
	model: wp.media.model.MenuIconsItemSettingField
});

module.exports = MenuIconsItemSettings;

},{}],4:[function(require,module,exports){
/**
 * wp.media.model.MenuIconsItem
 *
 * @class
 * @augments Backbone.Model
 */
var Item = Backbone.Model.extend({
	initialize: function() {
		this.on( 'change', this.updateValues, this );
	},

	/**
	 * Update the values of menu item's settings fields
	 *
	 * @fires mi:update
	 */
	updateValues: function() {
		_.each( this.get( '$inputs' ), function( $input, key ) {
			$input.val( this.get( key ) );
		}, this );

		// Trigger the 'mi:update' event to regenerate the icon on the field.
		this.get( '$el' ).trigger( 'mi:update' );
	}
});

module.exports = Item;

},{}],5:[function(require,module,exports){
/**
 * wp.media.view.MediaFrame.MenuIcons
 *
 * @class
 * @augments wp.media.view.MediaFrame.IconPicker
 * @augments wp.media.view.MediaFrame.Select
 * @augments wp.media.view.MediaFrame
 * @augments wp.media.view.Frame
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var MenuIcons = wp.media.view.MediaFrame.IconPicker.extend({
	initialize: function() {
		this.menuItems = new Backbone.Collection( [], {
			model: wp.media.model.MenuIconsItem
		});

		wp.media.view.MediaFrame.IconPicker.prototype.initialize.apply( this, arguments );

		this.listenTo( this.target, 'change', this.miUpdateItemProps );
		this.on( 'select', this.miClearTarget, this );
	},

	miUpdateItemProps: function( props ) {
		var model = this.menuItems.get( props.id );

		model.set( props.changed );
	},

	miClearTarget: function() {
		this.target.clear({ silent: true });
	}
});

module.exports = MenuIcons;

},{}],6:[function(require,module,exports){
/**
 * wp.media.view.MenuIconsItemPreview
 *
 * @class
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var MenuIconsItemPreview = wp.media.View.extend({
	tagName:   'p',
	className: 'mi-preview menu-item attachment-info',
	events:    {
		'click a': 'preventDefault'
	},

	initialize: function() {
		wp.media.View.prototype.initialize.apply( this, arguments );
		this.model.on( 'change', this.render, this );
	},

	render: function() {
		var frame    = this.controller,
			state    = frame.state(),
			selected = state.get( 'selection' ).single(),
			props    = this.model.toJSON(),
			data     = _.extend( props, {
				type:  state.id,
				icon:  selected.id,
				title: this.model.get( '$title' ).val(),
				url:   state.ipGetIconUrl( selected, props.image_size )
			}),
			template = 'menu-icons-item-sidebar-preview-' + iconPicker.types[ state.id ].templateId + '-';

		if ( data.hide_label ) {
			template += 'hide_label';
		} else {
			template += data.position;
		}

		this.template = wp.media.template( template );
		this.$el.html( this.template( data ) );

		return this;
	},

	preventDefault: function( e ) {
		e.preventDefault();
	}
});

module.exports = MenuIconsItemPreview;

},{}],7:[function(require,module,exports){
var $ = jQuery,
    MenuIconsItemSettingField;

/**
 * wp.media.view.MenuIconsItemSettingField
 *
 * @class
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
MenuIconsItemSettingField = wp.media.View.extend({
	tagName:   'label',
	className: 'setting',
	events:    {
		'change :input': '_update'
	},

	initialize: function() {
		wp.media.View.prototype.initialize.apply( this, arguments );

		this.template = wp.media.template( 'menu-icons-settings-field-' + this.model.get( 'type' ) );
		this.model.on( 'change', this.render, this );
	},

	prepare: function() {
		return this.model.toJSON();
	},

	_update: function( e ) {
		var value = $( e.currentTarget ).val();

		this.model.set( 'value', value );
		this.options.item.set( this.model.id, value );
	}
});

module.exports = MenuIconsItemSettingField;

},{}],8:[function(require,module,exports){
/**
 * wp.media.view.MenuIconsItemSettings
 *
 * @class
 * @augments wp.media.view.PriorityList
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var MenuIconsItemSettings = wp.media.view.PriorityList.extend({
	className: 'mi-settings attachment-info',

	prepare: function() {
		_.each( this.collection.map( this.createField, this ), function( view ) {
			this.set( view.model.id, view );
		}, this );
	},

	createField: function( model ) {
		var field = new wp.media.view.MenuIconsItemSettingField({
			item:       this.model,
			model:      model,
			collection: this.collection
		});

		return field;
	}
});

module.exports = MenuIconsItemSettings;

},{}],9:[function(require,module,exports){
/**
 * wp.media.view.MenuIconsSidebar
 *
 * @class
 * @augments wp.media.view.IconPickerSidebar
 * @augments wp.media.view.Sidebar
 * @augments wp.media.view.PriorityList
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
 */
var MenuIconsSidebar = wp.media.view.IconPickerSidebar.extend({
	initialize: function() {
		var title = new wp.media.View({
			tagName:  'h3',
			priority: -10
		});

		var info = new wp.media.View({
			tagName:   'p',
			className: '_info',
			priority:  1000
		});

		wp.media.view.IconPickerSidebar.prototype.initialize.apply( this, arguments );

		title.$el.text( menuIcons.text.preview );
		this.set( 'title', title );

		info.$el.html( menuIcons.text.settingsInfo );
		this.set( 'info', info );
	},

	createSingle: function() {
		this.createPreview();
		this.createSettings();
	},

	disposeSingle: function() {
		this.unset( 'preview' );
		this.unset( 'settings' );
	},

	createPreview: function() {
		var self  = this,
			frame = self.controller,
			state = frame.state();

		// If the selected icon is still being downloaded (image or svg type),
		// wait for it to complete before creating the preview.
		if ( state.dfd && 'pending' === state.dfd.state() ) {
			state.dfd.done( function() {
				self.createPreview();
			});

			return;
		}

		self.set( 'preview', new wp.media.view.MenuIconsItemPreview({
			controller: frame,
			model:      frame.target,
			priority:   80
		}) );
	},

	createSettings: function() {
		var frame    = this.controller,
		    state    = frame.state(),
		    fieldIds = state.get( 'data' ).settingsFields,
		    fields   = [];

		_.each( fieldIds, function( fieldId ) {
			var field = menuIcons.settingsFields[ fieldId ],
			    model;

			if ( ! field ) {
				return;
			}

			model = _.defaults({
				value: frame.target.get( fieldId ) || field['default']
			}, field );

			fields.push( model );
		} );

		if ( ! fields.length ) {
			return;
		}

		this.set( 'settings', new wp.media.view.MenuIconsItemSettings({
			controller: this.controller,
			collection: new wp.media.model.MenuIconsItemSettings( fields ),
			model:      frame.target,
			type:       this.options.type,
			priority:   120
		}) );
	}
});

module.exports = MenuIconsSidebar;

},{}]},{},[1]);

(function( $ ) {
'use strict';

var miPicker;

if ( ! menuIcons.activeTypes || _.isEmpty( menuIcons.activeTypes ) ) {
	return;
}

/**
 * @namespace
 * @property {object} templates - Cached templates for the item previews on the fields
 * @property {string} wrapClass - Field wrapper's class
 * @property {object} frame     - Menu Icons' media frame instance
 * @property {object} target    - Frame's target model
 */
miPicker = {
	templates: {},
	wrapClass: 'div.menu-icons-wrap',
	frame: null,
	target: new wp.media.model.IconPickerTarget(),

	/**
	 * Callback function to filter active icon types
	 *
	 * TODO: Maybe move to frame view?
	 *
	 * @param {string} type - Icon type.
	 */
	typesFilter: function( type ) {
		return ( -1 < $.inArray( type.id, menuIcons.activeTypes ) );
	},

	/**
	 * Create Menu Icons' media frame
	 */
	createFrame: function() {
		miPicker.frame = new wp.media.view.MediaFrame.MenuIcons({
			target:      miPicker.target,
			ipTypes:     _.filter( iconPicker.types, miPicker.typesFilter ),
			SidebarView: wp.media.view.MenuIconsSidebar
		});
	},

	/**
	 * Pick icon for a menu item and open the frame
	 *
	 * @param {object} model - Menu item model.
	 */
	pickIcon: function( model ) {
		miPicker.frame.target.set( model, { silent: true } );
		miPicker.frame.open();
	},

	/**
	 * Set or unset icon
	 *
	 * @param {object} e - jQuery click event.
	 */
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

	/**
	 * Set Icon
	 *
	 * @param {object} $el - jQuery object.
	 */
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
			$title:  $( '#edit-menu-item-title-' + id ),
			$inputs: {}
		};

		// Collect menu item's settings fields and use them
		// as the model's attributes.
		$el.find( 'div._settings input' ).each( function() {
			var $input = $( this ),
			    key    = $input.attr( 'class' ).replace( '_mi-', '' ),
			    value  = $input.val();

			if ( ! value ) {
				if ( _.has( menuIcons.menuSettings, key ) ) {
					value = menuIcons.menuSettings[ key ];
				} else if ( _.has( menuIcons.settingsFields, key ) ) {
					value = menuIcons.settingsFields[ key ]['default'];
				}
			}

			model[ key ]         = value;
			model.$inputs[ key ] = $input;
		});

		items.add( model );
		miPicker.pickIcon( model );
	},

	/**
	 * Unset icon
	 *
	 * @param {object} $el - jQuery object.
	 */
	unsetIcon: function( $el ) {
		var id = $el.data( 'id' );

		$el.find( 'div._settings input' ).val( '' );
		$el.trigger( 'mi:update' );
		miPicker.frame.menuItems.remove( id );
	},

	/**
	 * Update valeus of menu item's setting fields
	 *
	 * When the type and icon is set, this will (re)generate the icon
	 * preview on the menu item field.
	 *
	 * @param {object} e - jQuery event.
	 */
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
			$unset.addClass( 'hidden' );

			return;
		}

		if ( miPicker.templates[ type ] ) {
			template = miPicker.templates[ type ];
		} else {
			template = miPicker.templates[ type ] = wp.template( 'menu-icons-item-field-preview-' + iconPicker.types[ type ].templateId );
		}

		$unset.removeClass( 'hidden' );
		$set.attr( 'title', menuIcons.text.change );
		$set.html( template({
			type: type,
			icon: icon,
			url:  url
		}) );
	},

	/**
	 * Initialize picker functionality
	 *
	 * @fires mi:update
	 */
	init: function() {
		miPicker.createFrame();
		$( document )
			.on( 'click', miPicker.wrapClass, miPicker.setUnset )
			.on( 'mi:update', miPicker.wrapClass, miPicker.updateField );

		// Trigger 'mi:update' event to generate the icons on the item fields.
		$( miPicker.wrapClass ).trigger( 'mi:update' );
	}
};

miPicker.init();
}( jQuery ) );
