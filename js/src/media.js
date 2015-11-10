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
 */
var MenuIconsItemSettings = Backbone.Collection.extend({
	model: wp.media.model.MenuIconsItemSettingField
});

module.exports = MenuIconsItemSettings;

},{}],4:[function(require,module,exports){
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

},{}],5:[function(require,module,exports){
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
		var data     = _.extend( this.model.toJSON(), this.options.data ),
		    template = 'menu-icons-item-sidebar-preview-' + data.templateId + '-';

		data.title = this.model.get( '$title' ).val();

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
		var frame    = this.controller,
		    state    = frame.state(),
		    selected = state.get( 'selection' ).single();

		this.set( 'preview', new wp.media.view.MenuIconsItemPreview({
			controller: frame,
			model:      frame.target,
			priority:   80,
			data:       {
				icon:       selected.id,
				type:       state.id,
				templateId: iconPicker.types[ state.id ].templateId
			}
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
