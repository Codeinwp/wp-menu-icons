/**
 * wp.media.view.MenuIconsItemPreview
 *
 * @class
 * @augments wp.media.View
 * @augments wp.Backbone.View
 * @augments Backbone.View
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
		var frame    = this.controller,
			state    = frame.state(),
			selected = state.get( 'selection' ).single(),
			props    = this.model.toJSON(),
			data     = _.extend( props, {
				type:  state.id,
				icon:  selected.id,
				title: this.model.get( '$title' ).val(),
				url:   state.ipGetIconUrl( selected, props.image_size )
			}),
			template = 'menu-icons-item-sidebar-preview-' + iconPicker.types[ state.id ].templateId + '-';

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
