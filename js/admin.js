/* global jQuery, wp, window: false, Backbone: false, _: false */
/**
 * Menu Icons
 *
 * @author  Dzikri Aziz <kvcrvt@gmail.com>
 * @version 0.1.0
 *
 */
( function( $ ) {
	'use strict';

	$.inputDependencies( {
		selector: 'select.hasdep',
		disable:  false
	});

	/**
	 * Settings box tabs
	 *
	 * We can't use core's tabs script here because it will clear the
	 * checkboxes upon tab switching
	 */
	$( '#menu-icons-settings-tabs' )
		.on( 'click', 'a.mi-settings-nav-tab', function( e ) {
			e.preventDefault();
			e.stopPropagation();

			var $el     = $( this ).blur(),
			    $target = $( '#' + $el.data( 'type' ) );

			$el.parent().addClass( 'tabs' ).siblings().removeClass( 'tabs' );
			$target
				.removeClass( 'tabs-panel-inactive' )
				.addClass( 'tabs-panel-active' )
				.show()
				.siblings( 'div.tabs-panel' )
					.hide()
					.addClass( 'tabs-panel-inactive' )
					.removeClass( 'tabs-panel-active' );
		})
		.find( 'a.mi-settings-nav-tab' ).first().click();

	if ( _.isUndefined( window.menuIcons ) ) {
		return;
	}

	if ( _.isUndefined( window.menuIcons.iconTypes ) ) {
		return;
	}

	window.menuIcons = _.defaults( {
		frame:       '',
		currentItem: {},

		toggleSelect: function( e ) {
			var $type   = $( e.currentTarget ),
			    $wrapr  = $type.closest( 'div.menu-icons-wrap' ),
			    $select = $wrapr.find( 'a._select' ),
			    $remove = $wrapr.find( 'a._remove' );

			if ( '' !== $type.val() ) {
				$remove.show();
			} else {
				$select.text( $select.data( 'text' ) );
				$remove.hide();
			}
		},

		selectIcon: function( e ) {
			e.preventDefault();
			e.stopPropagation();

			var $el   = $( this ),
			    id    = media.view.settings.post.id = $el.data( 'id' ),
			    attrs;

			attrs = {
				id:    id,
				title: $( '#edit-menu-item-title-' + id ).val()
			};

			$el.closest( 'div.menu-icons-wrap' ).find( ':input' ).each( function( i, input ) {
				var key = $( input ).data( 'key' );
				attrs[ key ] = input.value;
			});

			window.menuIcons.currentItem = attrs;

			if ( ! ( window.menuIcons.frame instanceof media.view.MediaFrame.menuIcons ) ) {
				window.menuIcons.frame = new media.view.MediaFrame.menuIcons();
			}

			window.menuIcons.frame.open();
		},

		removeIcon: function( e ) {
			e.preventDefault();
			e.stopPropagation();

			var id = $( this ).data( 'id' );

			$( '#menu-icons-' + id + '-type' ).val( '' ).trigger( 'mi:update' );
		}
	}, window.menuIcons );

	// WP Media
	var media      = wp.media,
	    Attachment = media.model.Attachment;

	// Models
	media.model.mi = {};

	// Model: Menu Items
	media.model.mi.MenuItems = Backbone.Collection.extend({
		props: new Backbone.Model({ item: '' }),
		model: Backbone.Model.extend({
			defaults: {
				type:  '',
				group: 'all',
				icon:  ''
			}
		})
	});

	// Model: Settings fields
	media.model.mi.MenuItems.Settings = Backbone.Collection.extend({
		model: Backbone.Model.extend({
			defaults: {
				id:    '',
				label: '',
				value: '',
				type:  'text'
			}
		})
	});

	// All: Sidebar
	media.view.miSidebar = media.view.Sidebar.extend({
		initialize: function() {
			var title = new media.View({
				tagName:  'h3',
				priority: -10
			});

			var info = new media.View({
				tagName:   'p',
				className: '_info',
				priority:  1000
			});

			media.view.Sidebar.prototype.initialize.apply( this, arguments );

			title.$el.text( window.menuIcons.text.preview );
			this.set( 'title', title );

			info.$el.html( window.menuIcons.text.settingsInfo );
			this.set( 'info', info );
		}
	});

	// View: Settings wrapper
	media.view.miSidebar.Settings = media.view.PriorityList.extend({
		className: 'mi-settings attachment-info',

		prepare: function() {
			_.each( this.collection.map( this.createField, this ), function( view ) {
				this.set( view.model.id, view );
			}, this );
		},

		createField: function( model ) {
			var field = new media.view.miSidebar.Settings.Field({
				item:       this.model,
				model:      model,
				collection: this.collection
			});

			return field;
		}
	});

	// View: Settings field
	media.view.miSidebar.Settings.Field = media.View.extend({
		tagName:   'label',
		className: 'setting',
		events:    {
			'change :input': '_update'
		},

		initialize: function() {
			media.View.prototype.initialize.apply( this, arguments );
			this.template = media.template( 'menu-icons-settings-field-' + this.model.get( 'type' ) );
			this.model.on( 'change', this.render, this );
		},

		prepare: function() {
			return this.model.toJSON();
		},

		_update: function( e ) {
			var item   = this.options.item,
			    $input = $( e.currentTarget ),
			    value  = $input.val(),
			    $field = $( '#menu-icons-' + item.id + '-' + this.model.id + '._setting' );

			this.model.set( 'value', value );
			item.set( this.model.id, value );
			$field.val( value ).trigger( 'mi:update' );
		}
	});

	// View: Item preview on the sidebar
	media.view.miPreview = media.View.extend({
		tagName:   'p',
		className: 'mi-preview menu-item attachment-info',
		events:    {
			'click a': 'preventDefault'
		},

		initialize: function() {
			media.View.prototype.initialize.apply( this, arguments );
			this.model.on( 'change', this.render, this );
		},

		render: function() {
			var data     = _.extend( this.model.toJSON(), this.options.data ),
			    template = 'menu-icons-' + data.type + '-preview-';

			if ( data.hide_label ) {
				template += 'hide_label';
			} else {
				template += data.position;
			}

			this.template = media.template( template );
			this.$el.html( this.template( data ) );

			return this;
		},

		preventDefault: function( e ) {
			e.preventDefault();
		}
	});

	// Methods for the browser view
	media.view.miBrowser = {
		createSidebar: function() {
			var options   = this.options;
			var selection = options.selection;
			var sidebar   = this.sidebar = new media.view.miSidebar({
				controller: this.controller,
				type:       options.type
			});

			this.views.add( sidebar );

			selection.on( 'selection:single', this.createSingle, this );
			selection.on( 'selection:unsingle', this.disposeSingle, this );

			if ( selection.single() ) {
				this.createSingle();
			}
		},

		createSingle: function() {
			this.createPreview();
		},

		createSettings: function() {
			var item   = this.controller.miGetCurrentItem(),
			    fields = this.model.get( 'settings' );

			if ( ! fields.length ) {
				return;
			}

			_.each( fields, function( field ) {
				field.value = item.get( field.id );
			} );

			this.sidebar.set( 'settings', new media.view.miSidebar.Settings({
				controller: this.controller,
				collection: new media.model.mi.MenuItems.Settings( fields ),
				model:      item,
				type:       this.options.type,
				priority:   120
			}) );
		}
	};

	// View: Font icon: Browser
	media.view.miFont = media.View.extend({
		className: 'attachments-browser mi-items-wrap',

		initialize: function() {
			this.createToolbar();
			this.createLibrary();
			this.createSidebar();
		},

		createLibrary: function() {
			this.items = new media.view.miFont.Library({
				controller: this.controller,
				collection: this.collection,
				selection:  this.options.selection,
				type:       this.options.type,
				data:       this.options.data
			});

			this.views.add( this.items );
		},

		createToolbar: function() {
			this.toolbar = new media.view.Toolbar({
				controller: this.controller
			});

			this.views.add( this.toolbar );

			// Dropdown filter
			this.toolbar.set( 'filters', new media.view.miFont.Filters({
				controller: this.controller,
				model:      this.collection.props,
				priority:   -80
			}).render() );

			// Search field
			this.toolbar.set( 'search', new media.view.Search({
				controller: this.controller,
				model:      this.collection.props,
				priority:   60
			}).render() );
		},

		createPreview: function() {
			var controller = this.controller,
			    menuItem   = controller.miGetCurrentItem(),
			    selected   = this.model.get( 'selection' ).single();

			this.createSettings();
			this.sidebar.set( 'preview', new media.view.miPreview({
				controller: controller,
				model:      menuItem,
				priority:   80,
				data:       {
					type: selected.get( 'type' ),
					icon: selected.id
				}
			}) );
		},

		disposeSingle: function() {
			var sidebar = this.sidebar;

			sidebar.unset( 'preview' );
			sidebar.unset( 'settings' );
		}
	});

	_.extend( media.view.miFont.prototype, media.view.miBrowser );

	// View: Font icon: Library
	media.view.miFont.Library = media.View.extend({
		tagName:   'ul',
		className: 'attachments mi-items clearfix',

		initialize: function() {
			this._viewsByCid = {};
			this.collection.on( 'reset', this.refresh, this );
			this.controller.on( 'open', this.scrollToSelected, this );
		},

		render: function() {
			this.collection.each( function( model ) {
				this.views.add( this.renderItem( model ), {
					at: this.collection.indexOf( model )
				} );
			}, this );

			return this;
		},

		renderItem: function( model ) {
			var view = new media.view.miFont.Icon({
				controller: this.controller,
				model:      model,
				collection: this.collection,
				selection:  this.options.selection,
				type:       this.options.type,
				data:       this.options.data
			});

			return this._viewsByCid[ view.cid ] = view;
		},

		clearItems: function() {
			_.each( this._viewsByCid, function( view ) {
				delete this._viewsByCid[ view.cid ];
				view.remove();
			}, this );
		},

		refresh: function() {
			this.clearItems();
			this.render();
		},

		ready: function() {
			this.scrollToSelected();
		},

		scrollToSelected: function() {
			var single = this.options.selection.single(),
			    singleView;

			if ( ! single ) {
				return;
			}

			singleView = this.getView( single );
			if ( singleView && ! this.isInView( singleView.$el ) ) {
				this.$el.scrollTop( singleView.$el.offset().top - this.$el.offset().top + this.$el.scrollTop() - parseInt( this.$el.css( 'paddingTop' ), 10 ) );
			}
		},

		getView: function( model ) {
			return _.findWhere( this._viewsByCid, { model: model } );
		},

		isInView: function( $elem ) {
			var $window       = $( window ),
			    docViewTop    = $window.scrollTop(),
			    docViewBottom = docViewTop + $window.height(),
			    elemTop       = $elem.offset().top,
			    elemBottom    = elemTop + $elem.height();

			return ( ( elemBottom <= docViewBottom ) && ( elemTop >= docViewTop ) );
		}
	});

	// View: Font icon: Dropdown filter
	media.view.miFont.Filters = media.view.AttachmentFilters.extend({
		createFilters: function() {
			this.filters = {
				all: {
					text:  window.menuIcons.text.all,
					props: {
						group: 'all'
					}
				}
			};

			var groups = this.controller.state().get( 'data' ).groups;
			_.each( groups, function( text, id ) {
				this.filters[ id ] = {
					text:  text,
					props: {
						group: id
					}
				};
			}, this );
		},

		change: function() {
			var filter = this.filters[ this.el.value ];

			if ( filter ) {
				this.model.set( 'group', filter.props.group );
			}
		}
	});

	// View: Font icon: Item
	media.view.miFont.Icon = media.view.Attachment.extend({
		className: 'attachment mi-item',
		events:    {
			'click .attachment-preview': 'toggleSelectionHandler',
			'click a':                   'preventDefault'
		},

		initialize: function() {
			this.template = media.template( 'menu-icons-' + this.options.type + '-item' );
			media.view.Attachment.prototype.initialize.apply( this, arguments );
		},

		render: function() {
			this.$el.html( this.template( this.model.toJSON() ) );
			this.updateSelect();

			return this;
		}
	});

	// Font icon state
	media.controller.miFont = media.controller.State.extend({
		defaults: {
			id:       'mi-font',
			menu:     'default',
			toolbar:  'mi-select',
			type:     '',
			settings: [ 'hide_label', 'position', 'font_size', 'vertical_align' ]
		},

		initialize: function() {
			var icons     = this.get( 'data' ).items,
			    library   = this.get( 'library' ),
			    selection = this.get( 'selection' ),
			    fieldIds  = this.get( 'settings' ),
			    fields;

			if ( ! ( library instanceof media.controller.miFont.Library ) ) {
				library = new media.controller.miFont.Library( icons );
				library.props.on( 'change', this.miResetLibrary, this );

				this.set( 'library', library );
			}

			if ( ! ( selection instanceof media.model.Selection ) ) {
				this.set( 'selection', new media.model.Selection( selection, {
					multiple: false
				}) );
			}

			fields = _.filter( window.menuIcons.settingsFields, function( field ) {
				return ( -1 !== $.inArray( field.id, fieldIds ) );
			});
			this.set( 'settings', fields );
		},

		activate: function() {
			this.frame.on( 'open', this.refresh, this );
			this.miUpdateSelection();
		},

		deactivate: function() {
			media.controller.State.prototype.deactivate.apply( this, arguments );
			this.frame.off( 'open', this.refresh, this );
		},

		refresh: function() {
			this.miResetFilter();
			this.miUpdateSelection();
		},

		miGetContent: function() {
			this.miResetFilter();

			return new media.view.miFont({
				controller: this.frame,
				model:      this,
				collection: this.get( 'library' ),
				selection:  this.get( 'selection' ),
				type:       this.get( 'type' )
			});
		},

		miResetLibrary: function() {
			var library = this.get( 'library' ),
			    group   = library.props.get( 'group' ),
			    item    = this.frame.miGetCurrentItem();

			item.set( 'group', group );

			library.reInitialize();
			this.set( 'library', library );

			this.miUpdateSelection();
		},

		miResetFilter: function() {
			var library = this.get( 'library' ),
			    item    = this.frame.miGetCurrentItem(),
			    groups  = this.get( 'data' ).groups,
			    group   = item.get( 'group' );

			if ( _.isUndefined( groups[ group ] ) ) {
				group = 'all';
			}

			library.props.set( 'group', group );
		},

		miUpdateSelection: function() {
			var selection = this.get( 'selection' ),
			    type      = this.get( 'type' ),
			    key       = type + '-icon',
			    item      = this.frame.miGetCurrentItem(),
			    icon      = item.get( key ),
			    selected;

			if ( type === item.get( 'type' ) && icon ) {
				selected = this.get( 'library' ).findWhere({ id: icon });
			}

			selection.reset( selected ? selected : [] );
		}
	});

	// Font icon collection
	media.controller.miFont.Library = Backbone.Collection.extend({
		props: new Backbone.Model({
			group:  'all',
			search: ''
		}),

		initialize: function( models ) {
			this.icons = new Backbone.Collection( models );
		},

		reInitialize: function() {
			var library = this,
			    icons   = this.icons.toJSON(),
			    props   = this.props.toJSON();

			_.each( props, function( val, filter ) {
				if ( library.filters[ filter ] ) {
					icons = _.filter( icons, library.filters[ filter ], val );
				}
			}, this );

			this.reset( icons );
		},

		filters: {
			group: function( icon ) {
				var group = this;

				return ( 'all' === group || icon.group === group || '' === icon.group );
			},
			search: function( icon ) {
				var term = this,
				    result;

				if ( '' === term ) {
					result = true;
				} else {
					result = _.any( [ 'id', 'label' ], function( key ) {
						var value = icon[ key ];

						return value && -1 !== value.search( this );
					}, term );
				}

				return result;
			}
		}
	});

	// Image icon state
	media.controller.miImage = media.controller.Library.extend({
		defaults: _.defaults({
			id:            'browse',
			menu:          'default',
			router:        'browse',
			toolbar:       'mi-select',
			filterable:    'uploaded',
			settings:      [ 'hide_label', 'position', 'image_size', 'vertical_align' ],
			syncSelection: false
		}, media.controller.Library.prototype.defaults ),

		initialize: function( options ) {
			var selection = this.get( 'selection' ),
			    fieldIds  = this.get( 'settings' ),
			    fields;

			if ( ! options.data.browserView ) {
				options.data.browserView = 'miImage';
			}

			this.options = options;

			this.set( 'library', media.query( options.data.library ) );

			this.routers = {
				upload: {
					text:     media.view.l10n.uploadFilesTitle,
					priority: 20
				},
				browse: {
					text:     media.view.l10n.mediaLibraryTitle,
					priority: 40
				}
			};

			if ( ! ( selection instanceof media.model.Selection ) ) {
				this.set( 'selection', new media.model.Selection( selection, {
					multiple: false
				}) );
			}

			fields = _.filter( window.menuIcons.settingsFields, function( field ) {
				return ( -1 !== $.inArray( field.id, fieldIds ) );
			});
			this.set( 'settings', fields );

			media.controller.Library.prototype.initialize.apply( this, arguments );
		},

		activate: function() {
			media.controller.Library.prototype.activate.apply( this, arguments );
			this.frame.on( 'open', this.miUpdateSelection, this );
			this.get( 'library' ).observe( wp.Uploader.queue );
			this.miUpdateSelection();
		},

		deactivate: function() {
			media.controller.Library.prototype.deactivate.apply( this, arguments );
			this.get( 'library' ).unobserve( wp.Uploader.queue );
			this.frame.off( 'open', this.miUpdateSelection, this );
		},

		miUpdateSelection: function() {
			var selection = this.get( 'selection' ),
			    type      = this.get( 'type' ),
			    key       = type + '-icon',
			    item      = this.frame.miGetCurrentItem(),
			    icon      = item.get( key ),
			    attachment;

			if ( type === item.get( 'type' ) && icon ) {
				attachment = Attachment.get( icon );
				this.dfd   = attachment.fetch();
			}
			selection.add( attachment ? attachment : [] );
		},

		miGetContent: function( mode ) {
			var content = ( 'upload' === mode ) ? this.uploadContent() : this.browseContent();

			this.frame.$el.removeClass( 'hide-toolbar' );

			return content;
		},

		browseContent: function() {
			var state = this,
			    type  = state.get( 'type' ),
			    options;

			options = {
				type:             type,
				controller:       state.frame,
				collection:       state.get( 'library' ),
				selection:        state.get( 'selection' ),
				model:            state,
				sortable:         state.get( 'sortable' ),
				search:           state.get( 'searchable' ),
				filters:          state.get( 'filterable' ),
				display:          state.get( 'displaySettings' ),
				dragInfo:         state.get( 'dragInfo' ),
				idealColumnWidth: state.get( 'idealColumnWidth' ),
				suggestedWidth:   state.get( 'suggestedWidth' ),
				suggestedHeight:  state.get( 'suggestedHeight' ),
				AttachmentView:   state.get( 'AttachmentView' )
			};

			if ( 'svg' === type ) {
				options.AttachmentView = media.view.Attachment.miSvg;
			}

			return new media.view.AttachmentsBrowser.miImage( options );
		},

		/**
		 * Render callback for the content region in the `upload` mode.
		 */
		uploadContent: function() {
			return new media.view.UploaderInline({
				controller: this.frame
			});
		}
	});

	// SVG icon state
	media.controller.miSvg = media.controller.miImage.extend({
		defaults: _.defaults({
			settings: [ 'hide_label', 'position', 'vertical_align', 'width' ]
		}, media.controller.miImage.prototype.defaults )
	});

	// View: Image Icon: Browser
	media.view.AttachmentsBrowser.miImage = media.view.AttachmentsBrowser.extend({
		disposeSingle: function() {
			media.view.AttachmentsBrowser.prototype.disposeSingle.apply( this, arguments );
			this.sidebar.unset( 'preview' );
			this.sidebar.unset( 'settings' );
		},

		createPreview: function() {
			var self  = this,
			    state = this.model,
			    selected, controller, menuItem;

			if ( state.dfd && 'pending' === state.dfd.state() ) {
				state.dfd.done( function() {
					self.createPreview();
				} );

				return;
			}

			selected = state.get( 'selection' ).single();

			// Disallow anything but image
			if ( 'image' !== selected.get( 'type' ) ) {
				state.get( 'selection' ).reset();

				return;
			}

			// Wait for the upload process to finish
			if ( selected.get( 'uploading' ) ) {
				selected.on( 'change:uploading', self.createPreview, this );

				return;
			}

			controller = this.controller;
			menuItem   = controller.miGetCurrentItem();

			this.createSettings();
			this.sidebar.set( 'preview', new media.view.miPreview.miImage({
				controller: controller,
				settings:   this.sidebar.get( 'settings' ),
				model:      menuItem,
				priority:   80,
				data:       {
					type:  state.get( 'type' ),
					alt:   selected.get( 'alt' ),
					sizes: selected.get( 'sizes' ),
					url:   selected.get( 'url' )
				}
			}) );
		}
	});

	_.extend( media.view.AttachmentsBrowser.miImage.prototype, media.view.miBrowser );

	// View: SVG item
	media.view.Attachment.miSvg = media.view.Attachment.Library.extend({
		template: wp.template( 'menu-icons-svg-item' )
	});

	// View: Image Icon: Preview on the sidebar
	media.view.miPreview.miImage = media.view.miPreview.extend({
		render: function() {
			var size       = this.options.model.get( 'image_size' ),
			    imageSizes = this.options.data.sizes,
			    sizeField  = this.options.settings.get( 'image_size' ),
			    newChoices = [];

			if ( ! _.isUndefined( imageSizes ) && ! _.isUndefined( sizeField ) ) {
				if ( ! imageSizes.hasOwnProperty( size ) ) {
					size = 'full';
				}

				_.each( sizeField.model.get( 'choices' ), function( choice ) {
					if ( imageSizes.hasOwnProperty( choice.value ) ) {
						newChoices.push( choice );
					}
				} );

				this.options.data.url = imageSizes[ size ].url;
				sizeField.model.set( 'choices', newChoices );
				this.options.model.set( 'image_size', size, { silent: true } );
			}

			return media.view.miPreview.prototype.render.apply( this, arguments );
		}
	});

	// Frame
	media.view.MediaFrame.menuIcons = media.view.MediaFrame.extend({
		defaults: _.defaults({
			selection: [],
			multiple:  false,
			editing:   false,
			toolbar:   'mi-select'
		}, media.view.MediaFrame.prototype.defaults ),

		initialize: function() {
			media.view.MediaFrame.prototype.initialize.apply( this, arguments );

			this.miMenuItems = new media.model.mi.MenuItems();
			this.createStates();
			this.bindHandlers();
		},

		createStates: function() {
			var options = this.options,
			    Controller;

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
					content:   props.id,
					selection: options.selection
				});

				// States
				this.states.add( new Controller( props ) );
			}, this );
		},

		bindHandlers: function() {
			this.on( 'router:create:browse', this.createRouter, this );
			this.on( 'router:render:browse', this.browseRouter, this );
			this.on( 'content:render', this.miRenderContent, this );
			this.on( 'toolbar:create:mi-select', this.createToolbar, this );
			this.on( 'toolbar:render:mi-select', this.miSelectToolbar, this );
			this.on( 'open', this.miInitialize, this );
		},

		browseRouter: function( routerView ) {
			var routers = this.state().routers;

			if ( routers ) {
				routerView.set( routers );
			}
		},

		miRenderContent: function() {
			var state   = this.state(),
			    mode    = this.content.mode(),
			    content = state.miGetContent( mode );

			this.content.set( content );
		},

		// Toolbars
		miSelectToolbar: function( view ) {
			var frame = this,
			    state = frame.state();

			view.set( state.id, {
				style:    'primary',
				priority: 80,
				text:     window.menuIcons.text.select,
				requires: {
					selection: true
				},

				click: function() {
					frame.close();
					frame.miUpdateItemProps();
					frame.miUpdateItem();
				}
			});
		},

		// Content
		miContentRender: function() {
			var state   = this.state(),
			    content = state.miGetContent();

			this.content.set( content );
		},

		miGetState: function() {
			var item = window.menuIcons.currentItem,
			    type;

			if ( ! _.isUndefined( item.type ) && '' !== item.type && window.menuIcons.iconTypes.hasOwnProperty( item.type ) ) {
				type = item.type;
			} else {
				type = window.menuIcons.typeNames[0];
			}

			return 'mi-' + type;
		},

		miGetCurrentItem: function() {
			return this.miMenuItems.get( window.menuIcons.currentItem.id );
		},

		miUpdateMenuItems: function() {
			var item = this.miGetCurrentItem();

			if ( _.isUndefined( item ) ) {
				this.miMenuItems.add( window.menuIcons.currentItem );
			} else {
				item.set( window.menuIcons.currentItem );
			}

			this.miMenuItems.props.set( 'item', window.menuIcons.currentItem.id );
		},

		miInitialize: function() {
			this.miUpdateMenuItems();
			this.setState( this.miGetState() );
		},

		miUpdateItemProps: function() {
			var state     = this.state(),
			    type      = state.get( 'type' ),
			    selection = state.get( 'selection' ),
			    single    = selection.single(),
			    icon      = single ? single.id : '',
			    item      = this.miGetCurrentItem();

			item.set( 'type', type );
			item.set( type + '-icon', icon );
			item.set( 'icon', icon );
		},

		miUpdateItem: function() {
			var attrs    = this.miGetCurrentItem().toJSON(),
			    id       = attrs.id,
			    state    = this.state(),
			    selected = state.get( 'selection' ).single(),
			    template = media.template( 'menu-icons-' + attrs.type + '-field' ),
			    data     = selected.toJSON(),
			    $el;

			data._settings = attrs;
			delete attrs.id;
			delete attrs.title;

			_.each( attrs, function( value, key ) {
				$el = $( '#menu-icons-' + id + '-' + key ).not( '._setting' );
				if ( $el.length ) {
					$el.val( value ).trigger( 'mi:update' );
				}
			});

			$( '#menu-icons-' + id + '-select' ).html( template( data ) );
		}
	});

	$( 'body' )
		.on( 'click', 'div.menu-icons-wrap a._select', window.menuIcons.selectIcon )
		.on( 'click', 'div.menu-icons-wrap a._remove', window.menuIcons.removeIcon )
		.on( 'mi:update', 'div.menu-icons-wrap select._type', window.menuIcons.toggleSelect );

	$( 'div.menu-icons-wrap select._type' ).trigger( 'mi:update' );

	// Settings meta box
	$( '#menu-item-settings-save' ).on( 'click', function( e ) {
		var $button  = $( this ).prop( 'disabled', true ),
		    $spinner = $button.siblings( 'span.spinner' );

		e.preventDefault();

		$spinner.css( 'display', 'inline-block' );

		$.ajax({
			type: 'POST',
			url:  window.menuIcons.ajaxUrls.update,
			data: $( '#menu-icons-settings :input' ).serialize(),

			success: function( response ) {
				if ( true === response.success && response.data.redirectUrl ) {
					window.location = response.data.redirectUrl;
				} else {
					$button.prop( 'disabled', false );
				}
			},

			always: function() {
				$spinner.hide();
			}
		});
	});

	// A hack to prevent error because of the click callback set by wp-admin/js/nav-menu.js#811
	$( '#update-nav-menu svg' ).bind( 'click', function() {
		$( this ).closest( 'a' ).trigger( 'click' );

		return false;
	} );
}( jQuery ) );
