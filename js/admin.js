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

		selectIcon : function(e) {
			e.preventDefault();
			e.stopPropagation();

			var $el   = $(this);
			var id    = media.view.settings.post.id = $el.data('id');
			var attrs = {
				id : id
			};

			$el.closest('div.menu-icons-wrap').find(':input').each(function(i, input) {
				var key = $(input).data('key');
				attrs[ key ] = input.value;
			});

			menuIcons.currentItem = attrs;

			if ( ! ( menuIcons.frame instanceof media.view.MediaFrame.menuIcons ) ) {
				menuIcons.frame = new media.view.MediaFrame.menuIcons;
			}

			menuIcons.frame.open();
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


	// Font icon: Menu Items
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
			currentID : '',
			type      : '',
			icon      : '',
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
			var view = new media.view.miFont.Icon({
				controller : this.controller,
				model      : model,
				collection : this.collection,
				selection  : this.options.selection,
				type       : this.options.type,
				data       : this.options.data
			});

			return view.render().el;
		},

		refresh : function() {
			this.clearItems();
			this.render();
		},
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
	media.view.miFont.Icon = media.view.Attachment.extend({
		className : 'mi-item attachment',
		events    : {
			'click .attachment-preview' : 'toggleSelectionHandler',
			'click a'                   : 'preventDefault'
		},

		initialize : function() {
			this.template = media.template( 'menu-icons-' + this.options.type + '-item' );

			media.view.Attachment.prototype.initialize.apply( this, arguments );
			this.model.off( 'selection:single selection:unsingle', this.details, this );
		},

		render: function() {
			this.$el.html( this.template( this.model.toJSON() ) );
			this.updateSelect();

			return this;
		},

		toggleSelection : function() {
			media.view.Attachment.prototype.toggleSelection.apply( this, arguments );
		},

		selected : function() {
			var selection = this.options.selection;

			if ( selection ) {
				return !! ( selection.get( this.model.id ) );
			}
		},

		select: function( model, collection ) {
			var selection = this.options.selection;

			// Check if a selection exists and if it's the collection provided.
			// If they're not the same collection, bail; we're in another
			// selection's event loop.
			if ( ! selection || ( collection && collection !== selection ) )
				return;

			this.$el.addClass('selected details');
		},

		deselect: function( model, collection ) {
			var selection = this.options.selection;

			// Check if a selection exists and if it's the collection provided.
			// If they're not the same collection, bail; we're in another
			// selection's event loop.
			if ( ! selection || ( collection && collection !== selection ) )
				return;

			this.$el.removeClass('selected details');
		}
	});


	// Font icon: Controller
	media.controller.miFont = media.controller.State.extend({
		defaults: {
			id      : 'mi-font',
			menu    : 'default',
			toolbar : 'mi-select',
			type    : '',
			group   : 'all'
		},

		initialize : function() {
			var state = this;

			if ( ! this.get('library') ) {
				var Icons = Backbone.Collection.extend({
					props : new Backbone.Model({
						group : state.get('group')
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
			if ( ! (selection instanceof media.model.Selection) ) {
				this.set( 'selection', new media.model.Selection( selection, {
					multiple : false
				}) );
			}
		},

		activate: function() {
			this.miUpdateSelection();
			this.frame.on( 'open', this.miUpdateSelection, this );
			media.controller.State.prototype.activate.apply( this, arguments );
		},

		deactivate: function() {
			this.frame.off( 'open', this.miUpdateSelection, this );
			media.controller.State.prototype.deactivate.apply( this, arguments );
		},

		miResetLibrary : function() {
			var library = this.get('library');

			library.reInitialize();
			this.set( 'library', library );
		},

		miUpdateSelection : function() {
			var current = menuIcons.currentItem;
			var icon;

			if (
				this.get('type') === current.type
				&& ! _.isUndefined( current[ current.type+'-icon' ] )
			) {
				icon = current[ current.type+'-icon' ];
			}

			this.get('selection').reset( icon ? [{id: icon}] : [] );
		}
	});


	// Custom Frame
	media.view.MediaFrame.menuIcons = media.view.MediaFrame.extend({
		miMenuItems : {},

		initialize : function() {
			media.view.MediaFrame.prototype.initialize.apply( this, arguments );

			_.defaults( this.options, {
				selection : [],
				multiple  : false,
				editing   : false,
				toolbar   : 'mi-select'
			});

			this.miMenuItems = new media.model.miMenuItems;
			this.miInitialize();
			this.createStates();
			this.bindHandlers();
		},

		createStates : function() {
			var options = this.options;
			var controller;

			if ( options.states ) {
				return;
			}

			_.each( menuIcons.iconTypes, function( props, type ) {
				if ( ! media.controller.hasOwnProperty( props.data.controller ) ) {
					delete menuIcons.iconTypes[ type ];
					return;
				}

				controller = media.controller[ props.data.controller ];

				_.defaults( props, {
					content   : props.id,
					selection : options.selection
				} );

				// States
				this.states.add( new controller( props ) );
			}, this );
		},

		bindHandlers : function() {
			this.on( 'toolbar:create:mi-select', this.createToolbar, this );
			this.on( 'toolbar:render:mi-select', this.miSelectToolbar, this );
			this.on( 'open', this.miInitialize, this );

			_.each( menuIcons.iconTypes, function( props, type ) {
				this.on( 'content:activate:'+props.id, this.miContentRender, this, props );
			}, this );
		},

		// Toolbars
		miSelectToolbar : function( view ) {
			var frame = this;
			var state = frame.state();
			var type  = state.get('type');

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
						type : type,
						icon : selected.id,
					};
					args[ type+'-icon' ] = selected.id;

					frame.close();
					frame.miUpdateItem( args );
				}
			});
		},

		// Content
		miContentRender : function() {
			var state = this.state();
			var view  = media.view[ state.get('data').controller ];

			this.content.set( new view({
				controller : this,
				model      : state,
				collection : state.get('library'),
				selection  : state.get('selection'),
				type       : state.get('type')
			}) );
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

		miGetCurrentItem : function() {
			return this.miMenuItems.get( menuIcons.currentItem.id )
		},

		miUpdateMenuItems : function() {
			var item = this.miGetCurrentItem();
			var icon = '';

			if ( _.isUndefined( item ) ) {
				this.miMenuItems.add( menuIcons.currentItem );
				item = menuIcons.currentItem;
			}
			else {
				item = item.toJSON();
			}

			if ( ! _.isUndefined( item[ item.type+'-icon' ] ) ) {
				icon = item[ item.type+'-icon' ];
			}

			this.miMenuItems.props.set({
				currentID : item.id,
				type      : item.type,
				icon      : icon
			});
		},

		miInitialize : function() {
			this.miUpdateMenuItems();
			this.setState( this.miGetState() );
		},

		miUpdateItem : function( args ) {
			_.defaults( menuIcons.currentItem, args );
			var id      = menuIcons.currentItem.id;
			var preview = media.template('menu-icons-'+ args.type +'-preview');

			_.each( args, function( value, key ) {
				if ( 'icon' !== key ) {
					$('#menu-icons-'+ id +'-'+ key).val(value).trigger('change');
				}
			});

			args.id = args.icon;
			delete args.icon;

			$('#menu-icons-'+ id +'-remove').show();
			$('#menu-icons-'+ id +'-select').html( preview(args) );
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
		.on( 'click', 'div.menu-icons-wrap a._select', menuIcons.selectIcon )
		.on( 'click', 'div.menu-icons-wrap a._remove', menuIcons.removeIcon );
}(jQuery));
