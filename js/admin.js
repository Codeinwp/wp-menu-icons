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
			var view = new media.view.miFont.Item({
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
		},

		toggleSelection : function() {
			media.view.Attachment.prototype.toggleSelection.apply( this, arguments );
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
			icon    : ''
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
				this.set( 'selection', new media.model.Selection([]) );
			}
		},

		activate: function() {
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
			var item      = this.frame.miGetCurrentItem().toJSON();
			var icon      = '';

			if (
				this.get('type') === item.type
				&& ! _.isUndefined( item[item.type+'-icon'] )
			) {
				icon = item[item.type+'-icon'];
			}

			this.get('selection').reset( icon ? [{icon: icon}] : [] );
		}
	});


	// Custom Frame
	media.view.MediaFrame.menuIcons = media.view.MediaFrame.Select.extend({
		miMenuItems : {},

		initialize : function() {
			_.defaults( this.options, {
				multiple  : false,
				editing   : false,
				toolbar   : 'mi-select'
			});

			this.miMenuItems = new media.model.miMenuItems;
			this.miUpdateMenuItems();
			media.view.MediaFrame.Select.prototype.initialize.apply( this, arguments );
		},

		createStates : function() {
			var options = this.options;

			if ( this.options.states ) {
				return;
			}

			var current = this.miMenuItems.props.toJSON();
			var selection;

			_.each( menuIcons.iconTypes, function( props, type ) {
				if ( ! media.controller.hasOwnProperty( props.data.controller ) ) {
					delete menuIcons.iconTypes[ type ];
					return;
				}

				if ( current.type === type && ! _.isUndefined(current.icon) ) {
					selection = [ { icon : current.icon } ];
				}
				else {
					selection = [];
				}

				_.defaults( props, {
					content   : props.id,
					selection : new media.model.Selection(selection)
				} );

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
						icon : selected.get('icon'),
					};
					args[ type+'-icon' ] = selected.get('icon');

					frame.close();
					frame.miUpdateItem( args );
				}
			});
		},

		// Content
		miContentRender : function( props ) {
			var state = this.state();

			_.defaults( props, {
				controller : this,
				model      : state,
				collection : state.get('library'),
				selection  : state.get('selection'),
				icon       : this.miMenuItems.props.get('icon')
			} );

			var view = new media.view[ props.data.controller ]( props );
			this.content.set( view );
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

		miReinitialize : function() {
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
