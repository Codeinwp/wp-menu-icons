/* global menuIcons:false */

require( './media' );

( function( $ ) {
	var miPicker;

	if ( ! menuIcons.activeTypes || _.isEmpty( menuIcons.activeTypes ) ) {
		return;
	}

	/**
	 * @namespace
	 * @property {object} templates - Cached templates for the item previews on the fields
	 * @property {string} wrapClass - Field wrapper's class
	 * @property {object} frame     - Menu Icons' media frame instance
	 * @property {object} target    - Frame's target model
	 */
	miPicker = {
		templates: {},
		wrapClass: 'div.menu-icons-wrap',
		frame: null,
		target: new wp.media.model.IconPickerTarget(),

		/**
		 * Callback function to filter active icon types
		 *
		 * TODO: Maybe move to frame view?
		 *
		 * @param {string} type - Icon type.
		 */
		typesFilter: function( type ) {
			return ( $.inArray( type.id, menuIcons.activeTypes ) >= 0 );
		},

		/**
		 * Create Menu Icons' media frame
		 */
		createFrame: function() {
			miPicker.frame = new wp.media.view.MediaFrame.MenuIcons({
				target:      miPicker.target,
				ipTypes:     _.filter( iconPicker.types, miPicker.typesFilter ),
				SidebarView: wp.media.view.MenuIconsSidebar
			});
		},

		/**
		 * Pick icon for a menu item and open the frame
		 *
		 * @param {object} model - Menu item model.
		 */
		pickIcon: function( model ) {
			miPicker.frame.target.set( model, { silent: true });
			miPicker.frame.open();
		},

		/**
		 * Set or unset icon
		 *
		 * @param {object} e - jQuery click event.
		 */
		setUnset: function( e ) {
			var $el      = $( e.currentTarget ),
			    $clicked = $( e.target );

			e.preventDefault();

			if ( $clicked.hasClass( '_select' ) || $clicked.hasClass( '_icon' ) ) {
				miPicker.setIcon( $el );
			} else if ( $clicked.hasClass( '_remove' ) ) {
				miPicker.unsetIcon( $el );
			}
		},

		/**
		 * Set Icon
		 *
		 * @param {object} $el - jQuery object.
		 */
		setIcon: function( $el ) {
			var id     = $el.data( 'id' ),
			    frame  = miPicker.frame,
			    items  = frame.menuItems,
			    model  = items.get( id );

			if ( model ) {
				miPicker.pickIcon( model.toJSON() );
				return;
			}

			model = {
				id:      id,
				$el:     $el,
				$title:  $( '#edit-menu-item-title-' + id ),
				$inputs: {}
			};

			// Collect menu item's settings fields and use them
			// as the model's attributes.
			$el.find( 'div._settings input' ).each( function() {
				var $input = $( this ),
				    key    = $input.attr( 'class' ).replace( '_mi-', '' ),
				    value  = $input.val();

				if ( ! value ) {
					if ( _.has( menuIcons.menuSettings, key ) ) {
						value = menuIcons.menuSettings[ key ];
					} else if ( _.has( menuIcons.settingsFields, key ) ) {
						value = menuIcons.settingsFields[ key ]['default'];
					}
				}

				model[ key ]         = value;
				model.$inputs[ key ] = $input;
			});

			items.add( model );
			miPicker.pickIcon( model );
		},

		/**
		 * Unset icon
		 *
		 * @param {object} $el - jQuery object.
		 */
		unsetIcon: function( $el ) {
			var id = $el.data( 'id' );

			$el.find( 'div._settings input' ).val( '' );
			$el.trigger( 'mi:update' );
			miPicker.frame.menuItems.remove( id );
		},

		/**
		 * Update valeus of menu item's setting fields
		 *
		 * When the type and icon is set, this will (re)generate the icon
		 * preview on the menu item field.
		 *
		 * @param {object} e - jQuery event.
		 */
		updateField: function( e ) {
			var $el    = $( e.currentTarget ),
			    $set   = $el.find( 'a._select' ),
			    $unset = $el.find( 'a._remove' ),
			    type   = $el.find( 'input._mi-type' ).val(),
			    icon   = $el.find( 'input._mi-icon' ).val(),
			    url    = $el.find( 'input._mi-url' ).val(),
			    template;

			if ( type === '' || icon === '' || _.indexOf( menuIcons.activeTypes, type ) < 0 ) {
				$set.text( menuIcons.text.select ).attr( 'title', '' );
				$unset.addClass( 'hidden' );

				return;
			}

			if ( miPicker.templates[ type ]) {
				template = miPicker.templates[ type ];
			} else {
				template = miPicker.templates[ type ] = wp.template( 'menu-icons-item-field-preview-' + iconPicker.types[ type ].templateId );
			}

			$unset.removeClass( 'hidden' );
			$set.attr( 'title', menuIcons.text.change );
			$set.html( template({
				type: type,
				icon: icon,
				url:  url
			}) );
		},

		/**
		 * Initialize picker functionality
		 *
		 * #fires mi:update
		 */
		init: function() {
			miPicker.createFrame();
			$( document )
				.on( 'click', miPicker.wrapClass, miPicker.setUnset )
				.on( 'mi:update', miPicker.wrapClass, miPicker.updateField );

			// Trigger 'mi:update' event to generate the icons on the item fields.
			$( miPicker.wrapClass ).trigger( 'mi:update' );
		}
	};

	miPicker.init();
}( jQuery ) );
