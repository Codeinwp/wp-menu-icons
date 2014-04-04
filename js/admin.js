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
		selector: 'select.hasdep',
		disable: false
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

		toggleSelect : function(e) {
			e.stopPropagation();

			var $type   = $(e.currentTarget);
			var $wrapr  = $type.closest('div.menu-icons-wrap');
			var $select = $wrapr.find('a._select');

			if ( '' !== $type.val() ) {
				$select.siblings('a._remove').show();
			}
			else {
				$select.text( $select.data('text') );
			}
		},

		selectIcon : function(e) {
			e.preventDefault();
			e.stopPropagation();

			var $el   = $(this);
			var id    = media.view.settings.post.id = $el.data('id');
			var attrs = {
				id    : id,
				title : $('#edit-menu-item-title-'+id).val()
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

			var $el = $(this);
			var id  = $el.data('id');

			$el.hide();
			$('#menu-icons-'+ id +'-type').val('').trigger('change');
		}
	}, menuIcons );


	// WP Media
	var media      = wp.media;
	var Attachment = media.model.Attachment;


	// Font icon: Menu Items
	media.model.miMenuItem = Backbone.Model.extend({
		defaults : {
			type  : ''
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


	// Font icon: Browser
	media.view.miFont = media.View.extend({
		className : 'attachments-browser mi-items-wrap',

		initialize : function() {
			this.createToolbar();
			this.createLibrary();
			this.createSidebar();
		},

		createLibrary : function() {
			this.items = new media.view.miFont.Library({
				controller : this.controller,
				collection : this.collection,
				selection  : this.options.selection,
				type       : this.options.type,
				data       : this.options.data
			});
			this.views.add( this.items );
		},

		createToolbar : function() {
			this.toolbar = new media.view.Toolbar({
				controller : this.controller
			});
			this.views.add( this.toolbar );

			// Dropdown filter
			this.toolbar.set( 'filters', new media.view.miFont.Filters({
				controller : this.controller,
				model      : this.collection.props,
				priority   : -80
			}).render() );

			// Search field
			this.toolbar.set( 'search', new media.view.Search({
				controller: this.controller,
				model:      this.collection.props,
				priority:   60
			}).render() );
		},

		createSidebar : function() {
			var options   = this.options;
			var selection = options.selection;
			var sidebar   = this.sidebar = new media.view.Sidebar({
				controller : this.controller,
				type       : options.type
			});

			this.views.add( sidebar );

			selection.on( 'selection:single', this.createSingle, this );
			selection.on( 'selection:unsingle', this.disposeSingle, this );

			if ( selection.single() ) {
				this.createSingle();
			}
		},

		createSingle : function() {
			var sidebar = this.sidebar;
			var single  = this.options.selection.single();

			sidebar.set( 'details', new media.view.miFont.Icon.Preview({
				controller : this.controller,
				model      : single,
				type       : this.options.type,
				priority   : 80
			}) );
		},

		disposeSingle : function() {
			var sidebar = this.sidebar;
			sidebar.unset('details');
		},

		render : function() {
			var selection = this.options.selection;

			return this;
		},
	});


	// Font icon: Library
	media.view.miFont.Library = media.View.extend({
		tagName   : 'ul',
		className : 'attachments mi-items clearfix',

		initialize : function() {
			this.collection.on( 'reset', this.refresh, this );
			this._viewsByCid = {};
		},

		render : function() {
			this.collection.each( function( model ) {
				var icon = this.renderItem( model );
				this.views.add( icon );
			}, this );

			return this;
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

			return this._viewsByCid[ view.cid ] = view;
		},

		clearItems: function() {
			_.each( this._viewsByCid, function( view, x ) {
				delete this._viewsByCid[ view.cid ];
				view.remove();
			}, this );
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
		className : 'attachment mi-item',
		events    : {
			'click .attachment-preview' : 'toggleSelectionHandler',
			'click .check'              : 'removeFromSelection',
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

	media.view.miFont.Icon.Preview = Backbone.View.extend({
		tagName   : 'div',
		className : 'mi-preview',
		events    : {
			'click a' : 'preventDefault'
		},

		initialize : function() {
			this.template = media.template( 'menu-icons-' + this.options.type + '-preview' );
		},

		render: function() {
			var model = this.model.toJSON();
			model.title = menuIcons.currentItem.title;

			this.$el.html( this.template( model ) );

			return this;
		},
	});


	// Font icon: Controller
	media.controller.miFont = media.controller.State.extend({
		defaults : {
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
						group  : state.get('group'),
						search : ''
					}),

					initialize : function( models ) {
						this.original = new Backbone.Collection(models);
					},

					reInitialize : function() {
						var library  = this;
						var icons    = state.get('data').items;
						var props    = this.props.toJSON();

						_.each( props, function( val, filter ) {
							if ( library.filters[ filter ] ) {
								icons = _.filter( icons, library.filters[ filter ], val );
							}
						}, this);

						this.reset( icons );
					},

					filters : {
						group : function( icon ) {
							var group = this;

							return (
								'all' === group
								|| icon.group === group
								|| '' === icon.group // fallback
							);
						},
						search : function( icon ) {
							var term = this;
							var result;

							if ( '' === term ) {
								result = true;
							}
							else {
								result = _.any(['id','label'], function( key ) {
									var value = icon[key];
									return value && -1 !== value.search( this );
								}, term );
							}

							return result;
						}
					}
				});

				var library = new Icons( this.get('data').items );
				library.props.on( 'change', this.miResetLibrary, this );

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
			media.controller.State.prototype.activate.apply( this, arguments );
			this.miUpdateSelection();
		},

		deactivate: function() {
			media.controller.State.prototype.deactivate.apply( this, arguments );
			this.frame.off( 'open', this.miUpdateSelection, this );
		},

		miResetLibrary : function() {
			var library = this.get('library');

			library.reInitialize();
			this.set( 'library', library );
		},

		miUpdateSelection : function() {
			var selection = this.get('selection');
			var current   = menuIcons.currentItem;
			var selected;

			if (
				this.get('type') === current.type
				&& ! _.isUndefined( current[ current.type+'-icon' ] )
			) {
				selected = this.get('library').where({id: current[ current.type+'-icon' ]});
			}

			selection.reset( selected ? selected : [] );
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
			var id    = menuIcons.currentItem.id;
			var field = media.template('menu-icons-'+ args.type +'-field');

			_.each( args, function( value, key ) {
				if ( 'icon' !== key ) {
					$('#menu-icons-'+ id +'-'+ key).val(value).trigger('change');
				}
			});

			args.id = args.icon;
			delete args.icon;

			$('#menu-icons-'+ id +'-select').html( field(args) );
		}
	});


	$('body')
		.on( 'click', 'div.menu-icons-wrap a._select', menuIcons.selectIcon )
		.on( 'click', 'div.menu-icons-wrap a._remove', menuIcons.removeIcon )
		.on( 'change', 'div.menu-icons-wrap select._type', menuIcons.toggleSelect );

	$('div.menu-icons-wrap select._type').trigger('change');
}(jQuery));
