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
