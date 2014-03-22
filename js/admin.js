/* global jQuery, wp, menuIcons */
/**
 * Menu Icons
 *
 * @author  Dzikri Aziz <kvcrvt@gmail.com>
 * @version 0.1.0
 *
 */
(function($) {
	'use strict';

	$.inputDependencies({
		selector: 'select.hasdep'
	});

	if ( 'undefined' === typeof menuIcons ) {
		return;
	}

	if ( undefined === window.menuIcons.iconTypes ) {
		return;
	}

	menuIcons.frames      = {};
	menuIcons.currentItem = {};
	menuIcons.updateItem  = function( args ) {
		var current = menuIcons.currentItem.values = _.defaults( args.values, menuIcons.currentItem.values );
		var id      = menuIcons.currentItem.id;
		var preview = media.template('menu-icons-'+ current.type +'-preview');

		_.each( current, function( value, key ) {
			$('#menu-icons-'+ id +'-'+ key)
				.val( value )
				.trigger( 'change' )
		});

		$('#menu-icons-'+ id +'-remove').show();
		$('#menu-icons-'+ id +'-select').html( preview(args.data.attributes) );
	};
	menuIcons.removeIcon = function(e) {
		e.preventDefault();
		e.stopPropagation();

		var $el     = $(this);
		var id      = $el.data('id');
		var $select = $('#menu-icons-'+ id +'-select');

		$el.hide();
		$select.text( $select.data('text') );
		$('#menu-icons-'+ id +'-type').val('').trigger('change');
	};
	menuIcons.getState = function() {
		var current = menuIcons.currentItem.values;
		var type;

		if (
			undefined !== current.type
			&& '' !== current.type
			&& menuIcons.iconTypes.hasOwnProperty( current.type )
		) {
			type = current.type;
		}
		else {
			type = menuIcons.typeNames[0];
		}

		return 'mi-'+type;
	}

	// Media View
	var media      = wp.media;
	var Attachment = media.model.Attachment;


	// Controller: Image icon
	media.controller.miImage = media.controller.Library.extend({
		defaults: _.defaults({
			id         : 'mi-image',
			multiple   : false, // false, 'add', 'reset'
			describe   : false,
			toolbar    : 'mi-image',
			sidebar    : 'settings',
			content    : 'upload',
			router     : 'browse',
			menu       : 'default',
			searchable : true,
			filterable : 'uploaded',
			sortable   : false,
			title      : 'Image',

			// Uses a user setting to override the content mode.
			contentUserSetting: true,

			// Sync the selection from the last state when 'multiple' matches.
			syncSelection: true
		}, media.controller.Library.prototype.defaults ),

		initialize: function() {
			var library, comparator;

			// If we haven't been provided a `library`, create a `Selection`.
			this.set( 'library', media.query({ type: 'image' }) );

			media.controller.Library.prototype.initialize.apply( this, arguments );
		},

		activate: function() {
			this.on( 'select', menuIcons.updateItem, this );
			this.frame.on( 'open', this.updateSelection, this );
			media.controller.Library.prototype.activate.apply( this, arguments );
		},

		updateSelection: function() {
			var values    = menuIcons.currentItem.values;
			var selection = this.get('selection');

			if ( 'image' === values.type  ) {
				var id = parseInt( values['image-icon'] );
				var attachment;


				if ( !isNaN(id) && id > 0 ) {
					attachment = Attachment.get( id );
					attachment.fetch();
				}
			}

			selection.reset( attachment ? [ attachment ] : [] );
		}
	});


	// Custom Frame
	media.view.MediaFrame.MenuIcons = media.view.MediaFrame.Select.extend({
		initialize: function() {
			_.defaults( this.options, {
				multiple : false,
				editing  : false,
				state    : menuIcons.getState()
			});
			media.view.MediaFrame.Select.prototype.initialize.apply( this, arguments );
		},

		createStates: function() {
			var options = this.options;

			// Add the default states.
			this.states.add( new media.controller.miImage() );

			//new media.controller.FontIcon()
		},

		bindHandlers: function() {
			media.view.MediaFrame.Select.prototype.bindHandlers.apply( this, arguments );

			_.each( menuIcons.iconTypes, function( props, type ) {
				this.on( 'toolbar:create:mi-'+type, this.createToolbar, this );
				this.on( 'toolbar:render:mi-'+type, this.miToolbarRender, this );
			}, this );

			this.on( 'menu:render:default', this.mainMenu, this );
		},

		// Menus
		mainMenu: function( view ) {
			view.set({
				'library-separator': new media.View({
					className: 'separator',
					priority: 100
				})
			});
		},

		// Toolbars
		selectionStatusToolbar: function( view ) {
			view.set( 'selection', new media.view.Selection({
				controller: this,
				collection: this.state().get('selection'),
				priority:   -40
			}).render() );
		},

		miToolbarRender: function( view ) {
			this.selectionStatusToolbar( view );

			var controller = this;
			var type       = controller.state().id.replace('mi-', '');

			view.set( 'mi-image', {
				style    : 'primary',
				priority : 80,
				text     : menuIcons.labels.select,
				requires : { selection: true },
				click    : function() {
					var state    = controller.state();
					var selected = state.get('selection').single();
					var args     = {
						data   : selected,
						values : { type : type }
					};
					args.values[type+'-icon'] = selected.id;

					controller.close();
					state.trigger( 'select', args ).reset();
				}
			});
		}
	});


	$('div.menu-icons-wrap').each(function() {
		var $wrap = $(this);
		$wrap.find('div.original').hide();
		$wrap.find('div.easy').show();

		var $select = $wrap.find('a._select');
		if ( ! $select.children().length ) {
			$select.siblings('a._remove').hide();
		}
	});
	$('body')
		.on('click', 'div.menu-icons-wrap a._select', function(e) {
			e.preventDefault();
			e.stopPropagation();

			var $el    = $(this);
			var id     = $el.data('id');
			var values = {};

			$el.closest('div.menu-icons-wrap').find(':input').each(function(i, input) {
				var name = input.name.match( /\d+\]\[(.*)\]$/ );
				values[ name[1] ] = input.value;
			});

			media.view.settings.post.id = id;
			menuIcons.currentItem = {
				id : id,
				values : values
			};

			if ( menuIcons.frames[id] ) {
				menuIcons.frames[id].open();
				return;
			}

			var frame = menuIcons.frames[id] = new media.view.MediaFrame.MenuIcons();
			frame.open();
		})
		.on( 'click', 'div.menu-icons-wrap a._remove', menuIcons.removeIcon );
}(jQuery));
