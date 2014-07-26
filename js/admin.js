/* global jQuery, wp, window: false, Backbone: false, _: false */
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
		selector : 'select.hasdep',
		disable  : false
	});

	/**
	 * Settings box tabs
	 *
	 * We can't use core's tabs script here because it will clear the
	 * checkboxes upon tab switching
	 */
	$('#menu-icons-settings-tabs')
		.on('click', 'a.mi-settings-nav-tab', function(e) {
			e.preventDefault();
			e.stopPropagation();

			var $el     = $(this).blur();
			var $target = $( '#'+$el.data('type') );

			$el.parent().addClass('tabs').siblings().removeClass('tabs');
			$target
				.removeClass('tabs-panel-inactive')
				.addClass('tabs-panel-active')
				.show()
				.siblings('div.tabs-panel')
					.hide()
					.addClass('tabs-panel-inactive')
					.removeClass('tabs-panel-active');
		})
		.find('a.mi-settings-nav-tab').first().trigger('click');


	if ( 'undefined' === typeof window.menuIcons ) {
		return;
	}

	if ( undefined === window.menuIcons.iconTypes ) {
		return;
	}

	window.menuIcons = _.defaults({
		frame       : '',
		currentItem : {},

		toggleSelect : function(e) {
			var $type   = $(e.currentTarget);
			var $wrapr  = $type.closest('div.menu-icons-wrap');
			var $select = $wrapr.find('a._select');
			var $remove = $wrapr.find('a._remove');

			if ( '' !== $type.val() ) {
				$remove.show();
			}
			else {
				$select.text( $select.data('text') );
				$remove.hide();
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

			window.menuIcons.currentItem = attrs;

			if ( ! ( window.menuIcons.frame instanceof media.view.MediaFrame.menuIcons ) ) {
				window.menuIcons.frame = new media.view.MediaFrame.menuIcons();
			}

			window.menuIcons.frame.open();
		},

		removeIcon : function(e) {
			e.preventDefault();
			e.stopPropagation();

			var id = $(this).data('id');

			$('#menu-icons-'+ id +'-type').val('').trigger('change');
		}
	}, window.menuIcons);


	// WP Media
	var media      = wp.media;
	var Attachment = media.model.Attachment;


	// Font icon: Menu Items
	media.model.miMenuItem = Backbone.Model.extend({
		defaults : {
			type           : '',
			group          : 'all',
			icon           : '',
			font_size      : '1.2',
			vertical_align : 'middle',
			hide_label     : ''
		}
	});

	media.model.miMenuItems = Backbone.Collection.extend({
		model : media.model.miMenuItem,
		props : new Backbone.Model({
			item : ''
		})
	});


	// All: Sidebar
	media.view.miSidebar = media.view.Sidebar.extend({
		initialize : function() {
			media.view.Sidebar.prototype.initialize.apply( this, arguments );

			this.views.add( new media.view.miSidebar.Title() );
		}
	});


	// All: Sidebar title
	media.view.miSidebar.Title = media.View.extend({
		tagName : 'h3',
		render  : function() {
			this.$el.text( menuIcons.text.preview );

			return this;
		}
	});


	// All: Settings
	media.view.miSidebar.Settings = media.view.Settings.extend({
		className : 'mi-settings attachment-info',

		initialize : function() {
			this.template = media.template( 'menu-icons-settings' );
			media.view.Settings.prototype.initialize.apply( this, arguments );
		},

		render : function() {
			this.$el.html( this.template( this.model.toJSON() ) );
			_( this.model.attributes ).chain().keys().each( this.update, this );

			return this;
		},

		update: function( key ) {
			media.view.Settings.prototype.update.call( this, key );

			var $field     = $('#menu-icons-'+ this.model.id +'-'+ key +'._setting');
			var modelValue = this.model.get( key );
			var fieldValue;

			// Bail if we didn't find a matching field.
			if ( ! $field.length ) {
				return;
			}

			fieldValue = $field.val();
			// Only update as needed
			if ( fieldValue !== modelValue ) {
				$field.val( modelValue ).trigger('change');
			}
		}
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
				controller : this.controller,
				model      : this.collection.props,
				priority   : 60
			}).render() );
		},

		createSidebar : function() {
			var options   = this.options;
			var selection = options.selection;
			var sidebar   = this.sidebar = new media.view.miSidebar({
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
			this.controller.miUpdateItemProps();

			var sidebar = this.sidebar;
			var item    = this.controller.miGetCurrentItem();

			sidebar.set( 'preview', new media.view.miFont.Icon.Preview({
				controller : this.controller,
				model      : item,
				type       : this.options.type,
				priority   : 80
			}) );

			sidebar.set( 'settings', new media.view.miSidebar.Settings({
				controller : this.controller,
				model      : item,
				type       : this.options.type,
				priority   : 120
			}) );
		},

		disposeSingle : function() {
			this.controller.miUpdateItemProps();

			var sidebar = this.sidebar;
			sidebar.unset('preview');
			sidebar.unset('settings');
		}
	});


	// Font icon: Library
	media.view.miFont.Library = media.View.extend({
		tagName   : 'ul',
		className : 'attachments mi-items clearfix',

		initialize : function() {
			this._viewsByCid = {};
			this.collection.on( 'reset', this.refresh, this );
			this.controller.on( 'open', this.scrollToSelected, this );
		},

		render : function() {
			this.collection.each( function( model ) {
				this.views.add( this.renderItem( model ), {
					at : this.collection.indexOf( model )
				} );
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

		clearItems : function() {
			_.each( this._viewsByCid, function( view ) {
				delete this._viewsByCid[ view.cid ];
				view.remove();
			}, this );
		},

		refresh : function() {
			this.clearItems();
			this.render();
		},

		ready : function() {
			this.scrollToSelected();
		},

		scrollToSelected : function() {
			var single = this.options.selection.single();
			var singleView;

			if ( ! single ) {
				return;
			}

			singleView = this.getView( single );
			if ( singleView && ! this.isInView( singleView.$el ) ) {
				this.$el.scrollTop( singleView.$el.offset().top - this.$el.offset().top + this.$el.scrollTop() - parseInt( this.$el.css('paddingTop') ) );
			}
		},

		getView : function( model ) {
			return _.findWhere( this._viewsByCid, { model : model } );
		},

		isInView: function( $elem ) {
			var $window       = $(window);
			var docViewTop    = $window.scrollTop();
			var docViewBottom = docViewTop + $window.height();
			var elemTop       = $elem.offset().top;
			var elemBottom    = elemTop + $elem.height();

			return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
		}
	});


	// Font icon: Dropdown filter
	media.view.miFont.Filters = media.view.AttachmentFilters.extend({
		createFilters : function() {
			this.filters = {
				all : {
					text  : window.menuIcons.text.all,
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
			}, this );
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


	// Font icon: Preview
	media.view.miFont.Icon.Preview = media.View.extend({
		tagName   : 'p',
		className : 'mi-preview menu-item attachment-info',
		events    : {
			'click a' : 'preventDefault'
		},

		initialize : function() {
			media.View.prototype.initialize.apply( this, arguments );
			this.model.on( 'change', this.render, this );
		},

		render : function() {
			var data     = this.model.toJSON();
			var template = 'menu-icons-' + this.options.type + '-preview-';

			if ( data.hide_label ) {
				template += 'hide_label';
			}
			else {
				template += data.position;
			}

			this.template = media.template( template );
			this.$el.html( this.template( data ) );

			return this;
		},

		preventDefault: function(e) {
			e.preventDefault();
		}
	});


	// Font icon: Controller
	media.controller.miFont = media.controller.State.extend({
		defaults : {
			id      : 'mi-font',
			menu    : 'default',
			toolbar : 'mi-select',
			type    : ''
		},

		initialize : function() {
			var icons = this.get('data').items;

			if ( ! this.get('library') ) {
				var library = new media.controller.miFont.Library( icons );
				library.props.on( 'change', this.miResetLibrary, this );

				this.set( 'library', library );
			}

			var selection = this.get('selection');
			if ( ! ( selection instanceof media.model.Selection ) ) {
				this.set( 'selection', new media.model.Selection( selection, {
					multiple : false
				}) );
			}
		},

		activate : function() {
			media.controller.State.prototype.activate.apply( this, arguments );
			this.frame.on( 'open', this.refresh, this );
			this.miUpdateSelection();
		},

		deactivate : function() {
			media.controller.State.prototype.deactivate.apply( this, arguments );
			this.frame.off( 'open', this.refresh, this );
		},

		refresh : function() {
			var library = this.get('library');
			var item    = this.frame.miGetCurrentItem();
			var groups  = this.get('data').groups;
			var group   = item.get('group');

			if ( _.isUndefined( groups[ group ] ) ) {
				group = 'all';
			}

			library.props.set( 'group', group );
			this.miUpdateSelection();
		},

		miResetLibrary : function() {
			var library = this.get('library');
			var group   = library.props.get('group');
			var item    = this.frame.miGetCurrentItem();

			item.set( 'group', group );

			library.reInitialize();
			this.set( 'library', library );

			this.miUpdateSelection();
		},

		miUpdateSelection : function() {
			var selection = this.get('selection');
			var type      = this.get('type');
			var key       = type+'-icon';
			var item      = this.frame.miGetCurrentItem();
			var icon      = item.get(key);
			var selected;

			if ( type === item.get('type') && icon ) {
				selected = this.get('library').findWhere({id: icon});
			}

			selection.reset( selected ? selected : [] );
		},

		miGetIcon : function() {
			var single = this.get('selection').single();

			return single ? single.id : '';
		}
	});


	// Font icon: Library collection
	media.controller.miFont.Library = Backbone.Collection.extend({
		props : new Backbone.Model({
			group  : 'all',
			search : ''
		}),

		initialize : function( models ) {
			this.icons = new Backbone.Collection( models );
		},

		reInitialize : function() {
			var library = this;
			var icons   = this.icons.toJSON();
			var props   = this.props.toJSON();

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

				return ( 'all' === group || icon.group === group || '' === icon.group );
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


	// Frame
	media.view.MediaFrame.menuIcons = media.view.MediaFrame.extend({
		initialize : function() {
			media.view.MediaFrame.prototype.initialize.apply( this, arguments );

			_.defaults( this.options, {
				selection : [],
				multiple  : false,
				editing   : false,
				toolbar   : 'mi-select'
			});

			this.miMenuItems = new media.model.miMenuItems();
			this.createStates();
			this.bindHandlers();
		},

		createStates : function() {
			var options = this.options;
			var Controller;

			if ( options.states ) {
				return;
			}

			_.each( window.menuIcons.iconTypes, function( props, type ) {
				if ( ! media.controller.hasOwnProperty( props.data.controller ) ) {
					delete window.menuIcons.iconTypes[ type ];
					return;
				}

				Controller = media.controller[ props.data.controller ];

				_.defaults( props, {
					content   : props.id,
					selection : options.selection
				} );

				// States
				this.states.add( new Controller( props ) );
			}, this );
		},

		bindHandlers : function() {
			this.on( 'toolbar:create:mi-select', this.createToolbar, this );
			this.on( 'toolbar:render:mi-select', this.miSelectToolbar, this );
			this.on( 'open', this.miInitialize, this );

			_.each( window.menuIcons.iconTypes, function( props, type ) {
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
				text     : window.menuIcons.text.select,
				requires : {
					selection : true
				},
				click    : function() {
					frame.close();
					frame.miUpdateItem();
				}
			});
		},

		// Content
		miContentRender : function() {
			var state = this.state();
			var View  = media.view[ state.get('data').controller ];

			this.content.set( new View({
				controller : this,
				model      : state,
				collection : state.get('library'),
				selection  : state.get('selection'),
				type       : state.get('type')
			}) );
		},

		miGetState : function() {
			var item = window.menuIcons.currentItem;
			var type;

			if ( ! _.isUndefined( item.type ) && '' !== item.type && window.menuIcons.iconTypes.hasOwnProperty( item.type ) ) {
				type = item.type;
			}
			else {
				type = window.menuIcons.typeNames[0];
			}

			return 'mi-'+type;
		},

		miGetCurrentItem : function() {
			return this.miMenuItems.get( window.menuIcons.currentItem.id );
		},

		miUpdateMenuItems : function() {
			var item = this.miGetCurrentItem();

			if ( _.isUndefined( item ) ) {
				this.miMenuItems.add( window.menuIcons.currentItem );
			}
			else {
				item.set( window.menuIcons.currentItem );
			}

			this.miMenuItems.props.set( 'item', window.menuIcons.currentItem.id );
		},

		miInitialize : function() {
			this.miUpdateMenuItems();
			this.setState( this.miGetState() );
		},

		miUpdateItemProps : function() {
			var state     = this.state();
			var type      = state.get('type');
			var selection = state.get('selection');
			var single    = selection.single();
			var item      = this.miGetCurrentItem();
			var icon      = single ? single.id : '';

			item.set( 'type', type );
			item.set( type+'-icon', icon );
			item.set( 'icon', state.miGetIcon() );
		},

		miUpdateItem : function() {
			var attrs = this.miGetCurrentItem().toJSON();
			var id    = attrs.id;
			var field = media.template( 'menu-icons-'+ attrs.type +'-field' );
			var $el;

			delete attrs.id;
			delete attrs.title;

			_.each( attrs, function( value, key ) {
				$el = $('#menu-icons-'+ id +'-'+ key).not('._setting');
				if ( $el.length ) {
					$el.val( value ).trigger('change');
				}
			});

			$('#menu-icons-'+ id +'-select').html( field(attrs) );
		}
	});


	$('body')
		.on( 'click', 'div.menu-icons-wrap a._select', window.menuIcons.selectIcon )
		.on( 'click', 'div.menu-icons-wrap a._remove', window.menuIcons.removeIcon )
		.on( 'change', 'div.menu-icons-wrap select._type', window.menuIcons.toggleSelect );

	$('div.menu-icons-wrap select._type').trigger('change');
}(jQuery));
