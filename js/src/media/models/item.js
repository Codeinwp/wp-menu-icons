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
