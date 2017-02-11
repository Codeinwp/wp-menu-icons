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
			tagName: 'h3',
			priority: - 10
		});

		var info = new wp.media.View({
			tagName:   'p',
			className: '_info',
			priority:  1000
		});

		wp.media.view.IconPickerSidebar.prototype.initialize.apply( this, arguments );

		title.$el.text( window.menuIcons.text.preview );
		this.set( 'title', title );

		info.$el.html( window.menuIcons.text.settingsInfo );
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
		if ( state.dfd && state.dfd.state() === 'pending' ) {
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
			var field = window.menuIcons.settingsFields[ fieldId ],
			    model;

			if ( ! field ) {
				return;
			}

			model = _.defaults({
				value: frame.target.get( fieldId ) || field['default']
			}, field );

			fields.push( model );
		});

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
