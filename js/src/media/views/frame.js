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
		this.menuItems = new Backbone.Collection([], {
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
