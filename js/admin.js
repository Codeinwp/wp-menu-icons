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

	menuIcons = _.defaults({
		frame       : '',
		currentItem : {},

		updateItem : function( args ) {
			var current = menuIcons.currentItem = _.defaults( args, menuIcons.currentItem );
			var id      = menuIcons.currentItem.id;
			var preview = media.template('menu-icons-'+ current.type +'-preview');

			_.each( current, function( value, key ) {
				$('#menu-icons-'+ id +'-'+ key)
					.val( value )
					.trigger( 'change' )
			});

			$('#menu-icons-'+ id +'-remove').show();
			$('#menu-icons-'+ id +'-select').html( preview(args.data.attributes) );
		},

		removeIcon : function(e) {
			e.preventDefault();
			e.stopPropagation();

			var $el     = $(this);
			var id      = $el.data('id');
			var $select = $('#menu-icons-'+ id +'-select');

			$el.hide();
			$select.text( $select.data('text') );
			$('#menu-icons-'+ id +'-type').val('').trigger('change');
		}
	}, menuIcons );


	// WP Media
	var media      = wp.media;
	var Attachment = media.model.Attachment;


	// Model: Menu Items
	media.model.miMenuItem = Backbone.Model.extend({
		defaults : {
			type  : '',
			views : {
				filters : 'all'
			}
		}
	});

	media.model.miMenuItems = Backbone.Collection.extend({
		model : media.model.miMenuItem,
		props : new Backbone.Model({
			currentID : ''
		})
	});


	// Font icon: Wrapper
	media.view.miFont = media.View.extend({
		className : 'mi-items-wrap attachments-browser',

		initialize : function() {
			var list = '<ul class="mi-items attachments clearfix"></ul>';
			this.$el.append( list );

			this.collection.props.on( 'change:group', this.refresh, this );

			this.createToolbar();
		},

		createToolbar : function() {
			this.toolbar = new media.view.Toolbar({
				controller : this.controller
			});
			this.views.add( this.toolbar );

			this.toolbar.set( 'filters', new media.view.miFont.Filters({
				controller : this.controller,
				model      : this.collection.props,
				priority   : -80
			}).render() );
		},

		render : function() {
			var container = document.createDocumentFragment();

			this.collection.each( function( model ) {
				container.appendChild( this.renderItem( model ) );
			}, this );

			this.$el.find('ul.mi-items').append( container );

			return this;
		},

		clearItems: function() {
			this.$el.find( '.mi-items' ).empty();
		},

		renderItem : function( model ) {
			var view = new media.view.miFont.Item({
				controller : this.controller,
				model      : model,
				type       : this.options.type,
				data       : this.options.data
			});

			return view.render().el;
		},

		refresh : function() {
			this.clearItems();
			this.render();
		}
	});


	// Font icon: Dropdown filter
	media.view.miFont.Filters = media.view.AttachmentFilters.extend({
		createFilters: function() {
			this.filters = {
				all : {
					text  : menuIcons.text.all,
					props : {
						group : 'all'
					}
				}
			};

			var groups = this.controller.state().get('data').groups;
			_.each( groups, function( text, id ) {
				this.filters[ id ] = {
					text  : text,
					props : {
						group : id
					}
				};
			}, this);
		},

		change : function() {
			var filter = this.filters[ this.el.value ];

			if ( filter ) {
				this.model.set( 'group', filter.props.group );
			}
		}
	});


	// Font icon: Item
	media.view.miFont.Item = media.view.Attachment.extend({
		className : 'mi-item attachment',
		events    : {
			'click .attachment-preview' : 'toggleSelectionHandler',
			'click a'                   : 'preventDefault'
		},

		initialize : function() {
			this.template = media.template( 'menu-icons-' + this.options.type + '-item' );

			media.view.Attachment.prototype.initialize.apply( this, arguments );
		},

		render: function() {
			this.$el.html( this.template( this.model.toJSON() ) );
			this.updateSelect();

			return this;
		}
	});


	// Font icon: Controller
	media.controller.miFont = media.controller.State.extend({
		defaults: {
			id      : 'mi-font',
			menu    : 'default',
			content : 'mi-font',
			toolbar : 'mi-select',
			group   : 'all',
			name    : ''
		},

		initialize : function() {
			var _this = this;

			if ( ! this.get('library') ) {
				var Icons = Backbone.Collection.extend({
					props : new Backbone.Model({
						group : _this.get('group')
					}),

					initialize : function( models ) {
						this.icons    = models;
						this.original = new Backbone.Collection(models);
					},

					reInitialize : function() {
						var models = this.icons;
						var filtered;

						var group = this.props.get('group');
						if ( 'all' !== group ) {
							filtered = this.original.where({group: group});
						}
						else {
							filtered = this.icons;
						}

						this.reset( filtered );
					}
				});

				var library = new Icons( this.get('data').items );
				library.props.on( 'change:group', this.miResetLibrary, this );

				this.set( 'library', library );
			}

			var selection = this.get('selection');
			// If a selection instance isn't provided, create one.
			if ( ! (selection instanceof media.model.Selection) ) {
				this.set( 'selection', new media.model.Selection( [menuIcons.currentItem] ) );
			}
		},

		activate: function() {
			//this.get('selection').on( 'add remove reset', this.refreshContent, this );
			media.controller.State.prototype.activate.apply( this, arguments );
		},

		deactivate: function() {
			//this.frame.off( 'open', this.frame.miUpdateSelection, this );
			media.controller.State.prototype.deactivate.apply( this, arguments );
		},

		refresh: function() {
			this.frame.toolbar.get().refresh();
		},

		miResetLibrary : function() {
			var library = this.get('library');

			library.reInitialize();
			this.set( 'library', library );
		}
	});


	// Custom Frame
	media.view.MediaFrame.menuIcons = media.view.MediaFrame.Select.extend({
		miMenuItems : {},

		initialize: function() {
			_.defaults( this.options, {
				multiple  : false,
				editing   : false,
				toolbar   : 'mi-select'
			});

			media.view.MediaFrame.Select.prototype.initialize.apply( this, arguments );

			this.miMenuItems = new media.model.miMenuItems;
		},

		createStates: function() {
			var options = this.options;

			if ( this.options.states ) {
				return;
			}

			_.each( menuIcons.iconTypes, function( props, type ) {
				_.defaults( props, {
					content: props.id
				} );

				if ( ! media.controller.hasOwnProperty( props.data.controller ) ) {
					delete menuIcons.iconTypes[ type ];
					return;
				}

				// States
				this.states.add( new media.controller[ props.data.controller ]( props ) );
			}, this );
		},

		bindHandlers : function() {
			this.on( 'toolbar:create:mi-select', this.createToolbar, this );
			this.on( 'toolbar:render:mi-select', this.miSelectToolbar, this );
			this.on( 'open', this.miReinitialize, this );

			_.each( menuIcons.iconTypes, function( props, type ) {
				this.on( 'content:activate:'+props.id, _.bind( this.miContentRender, this, props ) );
			}, this );

			this.on( 'open', this.miUpdateItems, this );
		},

		// Toolbars
		miSelectToolbar : function( view ) {
			var controller = this;
			var state      = controller.state();
			var type       = this.state().get('type');

			view.set( state.id, {
				style    : 'primary',
				priority : 80,
				text     : menuIcons.text.select,
				requires : {
					selection: true
				},
				click    : function() {
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
		},

		// Content
		miContentRender: function( props ) {
			var state = this.state();

			_.defaults( props, {
				controller : this,
				model      : state,
				collection : state.get('library')
			} );

			var view = new media.view[ props.data.controller ]( props );
			this.content.set( view );
		},

		miUpdateSelection : function() {
			var values    = menuIcons.currentItem;
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
		},

		miGetState : function() {
			var item = menuIcons.currentItem;
			var type;

			if (
				! _.isUndefined( item.type )
				&& '' !== item.type
				&& menuIcons.iconTypes.hasOwnProperty( item.type )
			) {
				type = item.type;
			}
			else {
				type = menuIcons.typeNames[0];
			}

			return 'mi-'+type;
		},

		miReinitialize : function() {
			this.setState( this.miGetState() );
		},

		miUpdateItems : function() {
			var item = this.miMenuItems.get( menuIcons.currentItem.id );
			var currentID;

			if ( _.isUndefined( item ) ) {
				this.miMenuItems.add( menuIcons.currentItem );
				currentID = menuIcons.currentItem.id;
			}
			else {
				currentID = item.id;
			}

			this.miMenuItems.props.set( 'currentID', currentID );
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

			var $el   = $(this);
			var id    = $el.data('id');
			var attrs = {
				id : id
			};

			$el.closest('div.menu-icons-wrap').find(':input').each(function(i, input) {
				var key = $(input).data('key');
				attrs[ key ] = input.value;
			});

			media.view.settings.post.id = id;
			menuIcons.currentItem = attrs;

			if ( ! ( menuIcons.frame instanceof media.view.MediaFrame.menuIcons ) ) {
				menuIcons.frame = new media.view.MediaFrame.menuIcons();
			}

			menuIcons.frame.open();
		})
		.on( 'click', 'div.menu-icons-wrap a._remove', menuIcons.removeIcon );
}(jQuery));
