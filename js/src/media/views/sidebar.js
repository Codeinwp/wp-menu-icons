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
		this.createSettings();
	},

	disposeSingle: function() {
		this.unset( 'settings' );
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
