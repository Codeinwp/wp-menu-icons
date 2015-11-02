var $ = jQuery,
    MenuIcons;
/**
 * wp.media.view.MediaFrame.MenuIcons
 */
MenuIcons = wp.media.view.MediaFrame.IconPicker.extend({
	initialize: function() {
		this.menuItems = new Backbone.Collection( [], {
			model: wp.media.model.MenuIconsItem
		});

		wp.media.view.MediaFrame.IconPicker.prototype.initialize.apply( this, arguments );

		this.listenTo( this.target, 'change', this.miUpdateItemProps );
	},

	miPick: function( $el ) {
		var id    = $el.data( 'id' ),
		    model = this.menuItems.get( id );

		if ( model ) {
			this.target.set( model.toJSON(), { silent: true } );
			this.open();

			return;
		}

		model = {
			id:      id,
			$inputs: {}
		};

		$el.find( 'div._settings input' ).each( function() {
			var $input = $( this ),
			    key    = $input.data( 'key' );

			model[ key ]         = $input.val();
			model.$inputs[ key ] = $input;
		});

		this.menuItems.add( model );
		this.target.set( model, { silent: true } );
		this.open();
	},

	miUpdateItemProps: function() {
		var id      = this.target.id,
		    model   = this.menuItems.get( id ),
		    data    = this.target.toJSON();

		model.set( data );
		this.target.clear({ silent: true });
	}
});

module.exports = MenuIcons;
