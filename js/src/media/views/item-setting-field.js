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
