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
	 * #fires mi:update
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
