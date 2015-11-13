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
