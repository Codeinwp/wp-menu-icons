/**
 * wp.media.view.MenuIconsItemPreview
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
		var data     = _.extend( this.model.toJSON(), this.options.data ),
		    template = 'menu-icons-item-sidebar-preview-' + data.templateId + '-';

		data.title = this.model.get( '$title' ).val();

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
