<?php
/**
 * Abilities API integration
 *
 * @package Menu_Icons
 */

/**
 * Registers Menu Icons abilities with the WordPress Abilities API.
 */
final class Menu_Icons_Abilities {

	const CATEGORY = 'menu-icons';

	/**
	 * Capability required by the nav menus screen, where menu icons are managed.
	 */
	const CAPABILITY = 'edit_theme_options';

	const PER_PAGE = 50;

	/**
	 * Initialize
	 */
	public static function init() {
		if ( ! function_exists( 'wp_register_ability' ) ) {
			return;
		}

		add_action( 'wp_abilities_api_categories_init', array( __CLASS__, '_register_category' ) );
		add_action( 'wp_abilities_api_init', array( __CLASS__, '_register_abilities' ) );
	}


	/**
	 * Register ability category
	 *
	 * @wp_hook action wp_abilities_api_categories_init
	 */
	public static function _register_category() {
		if ( ! function_exists( 'wp_register_ability_category' ) ) {
			return;
		}

		wp_register_ability_category(
			self::CATEGORY,
			array(
				'label'       => __( 'Menu Icons', 'menu-icons' ),
				'description' => __( 'Manage the icons displayed on navigation menu items.', 'menu-icons' ),
			)
		);
	}


	/**
	 * Register abilities
	 *
	 * @wp_hook action wp_abilities_api_init
	 */
	public static function _register_abilities() {
		wp_register_ability(
			'menu-icons/search-icons',
			array(
				'label'               => __( 'Search menu icons', 'menu-icons' ),
				'description'         => __( 'Find icons in the enabled icon catalogs. With no type and no query, lists the enabled icon types and the settings each one supports.', 'menu-icons' ),
				'category'            => self::CATEGORY,
				'input_schema'        => array(
					'type'                 => 'object',
					'properties'           => array(
						'type'  => array(
							'type'        => 'string',
							'description' => __( 'Icon type ID to search in, e.g. dashicons or fa.', 'menu-icons' ),
						),
						'query' => array(
							'type'        => 'string',
							'description' => __( 'Text to look for in icon IDs and names.', 'menu-icons' ),
						),
						'page'  => array(
							'type'        => 'integer',
							'description' => __( 'Results page, 50 icons per page.', 'menu-icons' ),
							'minimum'     => 1,
							'default'     => 1,
						),
					),
					'additionalProperties' => false,
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'types'       => array(
							'type'  => 'array',
							'items' => array(
								'type'       => 'object',
								'properties' => array(
									'type'       => array( 'type' => 'string' ),
									'label'      => array( 'type' => 'string' ),
									'searchable' => array( 'type' => 'boolean' ),
									'icon_count' => array( 'type' => 'integer' ),
									'id_format'  => array( 'type' => 'string' ),
									'fields'     => array(
										'type'  => 'array',
										'items' => array( 'type' => 'object' ),
									),
								),
							),
						),
						'icons'       => array(
							'type'  => 'array',
							'items' => array(
								'type'       => 'object',
								'properties' => array(
									'type'  => array( 'type' => 'string' ),
									'id'    => array( 'type' => 'string' ),
									'label' => array( 'type' => 'string' ),
								),
							),
						),
						'total'       => array( 'type' => 'integer' ),
						'page'        => array( 'type' => 'integer' ),
						'per_page'    => array( 'type' => 'integer' ),
						'total_pages' => array( 'type' => 'integer' ),
					),
				),
				'execute_callback'    => array( __CLASS__, 'search_icons' ),
				'permission_callback' => array( __CLASS__, 'check_permission' ),
				'meta'                => array(
					'annotations'  => array(
						'readonly'    => true,
						'destructive' => false,
						'idempotent'  => true,
					),
					'show_in_rest' => true,
				),
			)
		);

		wp_register_ability(
			'menu-icons/set-item-icon',
			array(
				'label'               => __( 'Set menu item icon', 'menu-icons' ),
				'description'         => __( 'Set, change or remove the icon of a navigation menu item and return the stored icon. Icon types and IDs must come from menu-icons/search-icons.', 'menu-icons' ),
				'category'            => self::CATEGORY,
				'input_schema'        => array(
					'type'                 => 'object',
					'properties'           => array(
						'item_id' => array(
							'type'        => 'integer',
							'description' => __( 'Navigation menu item ID.', 'menu-icons' ),
							'minimum'     => 1,
						),
						'remove'  => array(
							'type'        => 'boolean',
							'description' => __( 'Set to true to remove the icon from the menu item.', 'menu-icons' ),
							'default'     => false,
						),
						'icon'    => array(
							'type'                 => 'object',
							'description'          => __( 'Icon to set. Settings left out keep their current value.', 'menu-icons' ),
							'properties'           => array(
								'type'           => array(
									'type'        => 'string',
									'description' => __( 'Icon type ID.', 'menu-icons' ),
								),
								'id'             => array(
									'type'        => 'string',
									'description' => __( 'Icon ID for font icon types.', 'menu-icons' ),
								),
								'attachment_id'  => array(
									'type'        => 'integer',
									'description' => __( 'Media library attachment ID for the image and svg types.', 'menu-icons' ),
									'minimum'     => 1,
								),
								'position'       => array(
									'type' => 'string',
									'enum' => array( 'before', 'after' ),
								),
								'hide_label'     => array(
									'type' => 'boolean',
								),
								'vertical_align' => array(
									'type' => 'string',
									'enum' => array( 'top', 'middle', 'baseline', 'bottom' ),
								),
								'font_size'      => array(
									'type'        => 'number',
									'description' => __( 'Font icon size in em.', 'menu-icons' ),
								),
								'svg_width'      => array(
									'type'        => 'number',
									'description' => __( 'SVG width in em.', 'menu-icons' ),
								),
								'svg_padding'    => array(
									'type'        => 'number',
									'description' => __( 'SVG padding in px.', 'menu-icons' ),
								),
								'image_size'     => array(
									'type'        => 'string',
									'description' => __( 'Registered image size name for the image type.', 'menu-icons' ),
								),
							),
							'required'             => array( 'type' ),
							'additionalProperties' => false,
						),
					),
					'required'             => array( 'item_id' ),
					'additionalProperties' => false,
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'item_id'  => array( 'type' => 'integer' ),
						'has_icon' => array( 'type' => 'boolean' ),
						'icon'     => array( 'type' => 'object' ),
					),
				),
				'execute_callback'    => array( __CLASS__, 'set_item_icon' ),
				'permission_callback' => array( __CLASS__, 'check_permission' ),
				'meta'                => array(
					'annotations'  => array(
						'readonly'    => false,
						'destructive' => false,
						'idempotent'  => true,
					),
					'show_in_rest' => true,
				),
			)
		);
	}


	/**
	 * Permission check, same capability as the nav menus screen
	 *
	 * @return bool
	 */
	public static function check_permission() {
		return current_user_can( self::CAPABILITY );
	}


	/**
	 * Get the enabled icon types
	 *
	 * @return array Icon_Picker_Type objects keyed by type ID.
	 */
	protected static function _get_active_types() {
		$types = Menu_Icons::get( 'types' );

		if ( empty( $types ) || ! class_exists( 'Menu_Icons_Settings' ) ) {
			return array();
		}

		$active = (array) Menu_Icons_Settings::get( 'global', 'icon_types' );

		return array_intersect_key( $types, array_flip( $active ) );
	}


	/**
	 * Get the catalog items of an icon type
	 *
	 * @param  Icon_Picker_Type $type Icon type.
	 * @return array
	 */
	protected static function _get_type_items( $type ) {
		if ( ! $type instanceof Icon_Picker_Type_Font ) {
			return array();
		}

		$items = $type->items;

		return is_array( $items ) ? $items : array();
	}


	/**
	 * Get the setting field IDs supported by an icon type
	 *
	 * @param  Icon_Picker_Type $type Icon type.
	 * @return array
	 */
	protected static function _get_type_field_ids( $type ) {
		if ( ! class_exists( 'Menu_Icons_Picker' ) ) {
			require_once Menu_Icons::get( 'dir' ) . 'includes/picker.php';
		}

		$props = Menu_Icons_Picker::_add_extra_type_props_data(
			array(
				'controller' => $type->controller,
				'data'       => array(),
			),
			$type->id,
			$type
		);

		return $props['data']['settingsFields'];
	}


	/**
	 * Search icons
	 *
	 * @param  array $input Ability input.
	 * @return array|WP_Error
	 */
	public static function search_icons( $input = array() ) {
		$input   = is_array( $input ) ? $input : array();
		$type_id = isset( $input['type'] ) ? sanitize_text_field( $input['type'] ) : '';
		$query   = isset( $input['query'] ) ? trim( sanitize_text_field( $input['query'] ) ) : '';
		$page    = isset( $input['page'] ) ? max( 1, absint( $input['page'] ) ) : 1;
		$types   = self::_get_active_types();

		if ( '' !== $type_id && ! isset( $types[ $type_id ] ) ) {
			return new WP_Error(
				'menu_icons_invalid_type',
				__( 'Unknown or disabled icon type.', 'menu-icons' ),
				array( 'status' => 400 )
			);
		}

		if ( '' === $type_id && '' === $query ) {
			return array( 'types' => self::_describe_types( $types ) );
		}

		if ( '' !== $type_id ) {
			$types = array( $type_id => $types[ $type_id ] );
		}

		$icons = array();
		foreach ( $types as $type ) {
			foreach ( self::_get_type_items( $type ) as $item ) {
				if ( '' !== $query &&
					false === stripos( $item['id'], $query ) &&
					false === stripos( $item['name'], $query )
				) {
					continue;
				}

				$icons[] = array(
					'type'  => $type->id,
					'id'    => (string) $item['id'],
					'label' => (string) $item['name'],
				);
			}
		}

		$total = count( $icons );

		return array(
			'icons'       => array_slice( $icons, ( $page - 1 ) * self::PER_PAGE, self::PER_PAGE ),
			'total'       => $total,
			'page'        => $page,
			'per_page'    => self::PER_PAGE,
			'total_pages' => (int) ceil( $total / self::PER_PAGE ),
		);
	}


	/**
	 * Describe icon types and their supported settings
	 *
	 * @param  array $types Icon types.
	 * @return array
	 */
	protected static function _describe_types( $types ) {
		$fields = Menu_Icons_Settings::get_settings_fields();
		$result = array();

		foreach ( $types as $type ) {
			$count       = count( self::_get_type_items( $type ) );
			$type_fields = array();

			foreach ( self::_get_type_field_ids( $type ) as $field_id ) {
				if ( ! isset( $fields[ $field_id ] ) ) {
					continue;
				}

				$field = array(
					'id'      => $field_id,
					'label'   => $fields[ $field_id ]['label'],
					'type'    => $fields[ $field_id ]['type'],
					'default' => (string) $fields[ $field_id ]['default'],
				);

				if ( ! empty( $fields[ $field_id ]['choices'] ) ) {
					$field['choices'] = wp_list_pluck( $fields[ $field_id ]['choices'], 'value' );
				}

				$type_fields[] = $field;
			}

			$result[] = array(
				'type'       => $type->id,
				'label'      => $type->name,
				'searchable' => $count > 0,
				'icon_count' => $count,
				'id_format'  => $count > 0 ? 'icon id' : 'attachment id',
				'fields'     => $type_fields,
			);
		}

		return $result;
	}


	/**
	 * Set or remove a menu item icon
	 *
	 * @param  array $input Ability input.
	 * @return array|WP_Error
	 */
	public static function set_item_icon( $input = array() ) {
		$input   = is_array( $input ) ? $input : array();
		$item_id = isset( $input['item_id'] ) ? absint( $input['item_id'] ) : 0;
		$remove  = ! empty( $input['remove'] );
		$icon    = ( isset( $input['icon'] ) && is_array( $input['icon'] ) ) ? $input['icon'] : array();

		if ( ! $item_id || 'nav_menu_item' !== get_post_type( $item_id ) ) {
			return new WP_Error(
				'menu_icons_item_not_found',
				__( 'Menu item not found.', 'menu-icons' ),
				array( 'status' => 404 )
			);
		}

		if ( ! current_user_can( 'edit_post', $item_id ) ) {
			return new WP_Error(
				'menu_icons_forbidden',
				__( 'Sorry, you are not allowed to edit this menu item.', 'menu-icons' ),
				array( 'status' => 403 )
			);
		}

		if ( $remove === ! empty( $icon ) ) {
			return new WP_Error(
				'menu_icons_invalid_input',
				__( 'Provide either an icon to set or remove: true.', 'menu-icons' ),
				array( 'status' => 400 )
			);
		}

		if ( ! class_exists( 'Menu_Icons_Settings' ) ) {
			return new WP_Error(
				'menu_icons_no_types',
				__( 'No icon types are registered.', 'menu-icons' ),
				array( 'status' => 500 )
			);
		}

		$menus   = wp_get_object_terms( $item_id, 'nav_menu', array( 'fields' => 'ids' ) );
		$menu_id = ( ! is_wp_error( $menus ) && ! empty( $menus ) ) ? (int) reset( $menus ) : 0;

		if ( $remove ) {
			Menu_Icons_Meta::update( $item_id, array() );

			return self::_get_stored_icon( $item_id, $menu_id );
		}

		if ( $menu_id && Menu_Icons_Settings::is_menu_icons_disabled_for_menu( $menu_id ) ) {
			return new WP_Error(
				'menu_icons_disabled_for_menu',
				__( 'Menu icons are disabled for the menu this item belongs to.', 'menu-icons' ),
				array( 'status' => 409 )
			);
		}

		$types   = self::_get_active_types();
		$type_id = isset( $icon['type'] ) ? sanitize_text_field( $icon['type'] ) : '';

		if ( '' === $type_id || ! isset( $types[ $type_id ] ) ) {
			return new WP_Error(
				'menu_icons_invalid_type',
				__( 'Unknown or disabled icon type.', 'menu-icons' ),
				array( 'status' => 400 )
			);
		}

		$type    = $types[ $type_id ];
		$icon_id = self::_resolve_icon_id( $type, $icon );

		if ( is_wp_error( $icon_id ) ) {
			return $icon_id;
		}

		$allowed  = self::_get_type_field_ids( $type );
		$settings = self::_sanitize_settings( $icon, $allowed );

		if ( is_wp_error( $settings ) ) {
			return $settings;
		}

		// Keep the current settings that were not provided.
		$current = get_post_meta( $item_id, Menu_Icons_Meta::KEY, true );
		if ( is_array( $current ) ) {
			$settings = array_merge(
				array_intersect_key( $current, array_flip( $allowed ) ),
				$settings
			);
		}

		$value = array_merge(
			array(
				'type' => $type_id,
				'icon' => $icon_id,
			),
			$settings
		);

		Menu_Icons_Meta::update( $item_id, array_map( 'sanitize_text_field', $value ) );

		return self::_get_stored_icon( $item_id, $menu_id );
	}


	/**
	 * Validate the requested icon and get the value to store
	 *
	 * @param  Icon_Picker_Type $type Icon type.
	 * @param  array            $icon Icon input.
	 * @return string|WP_Error
	 */
	protected static function _resolve_icon_id( $type, $icon ) {
		$items = self::_get_type_items( $type );

		if ( ! empty( $items ) ) {
			$icon_id = isset( $icon['id'] ) ? trim( sanitize_text_field( $icon['id'] ) ) : '';

			foreach ( $items as $item ) {
				$classes = explode( ' ', $item['id'] );
				if ( '' !== $icon_id && ( $item['id'] === $icon_id || end( $classes ) === $icon_id ) ) {
					return (string) $item['id'];
				}
			}

			return new WP_Error(
				'menu_icons_invalid_icon',
				__( 'Icon not found in this icon type. Use menu-icons/search-icons to find a valid icon ID.', 'menu-icons' ),
				array( 'status' => 400 )
			);
		}

		$attachment_id = 0;
		if ( ! empty( $icon['attachment_id'] ) ) {
			$attachment_id = absint( $icon['attachment_id'] );
		} elseif ( ! empty( $icon['id'] ) ) {
			$attachment_id = absint( $icon['id'] );
		}

		$mime   = $attachment_id ? (string) get_post_mime_type( $attachment_id ) : '';
		$is_svg = 'image/svg+xml' === $mime;

		if ( ! $attachment_id ||
			'attachment' !== get_post_type( $attachment_id ) ||
			0 !== strpos( $mime, 'image/' ) ||
			( 'svg' === $type->id ) !== $is_svg
		) {
			return new WP_Error(
				'menu_icons_invalid_attachment',
				__( 'attachment_id must be a media library image matching the icon type.', 'menu-icons' ),
				array( 'status' => 400 )
			);
		}

		return (string) $attachment_id;
	}


	/**
	 * Validate the provided icon settings
	 *
	 * @param  array $icon    Icon input.
	 * @param  array $allowed Setting field IDs supported by the icon type.
	 * @return array|WP_Error
	 */
	protected static function _sanitize_settings( $icon, $allowed ) {
		$fields   = Menu_Icons_Settings::get_settings_fields();
		$settings = array();

		foreach ( $fields as $field_id => $field ) {
			if ( ! isset( $icon[ $field_id ] ) ) {
				continue;
			}

			$error = null;
			$value = $icon[ $field_id ];

			if ( ! in_array( $field_id, $allowed, true ) ) {
				$error = __( 'is not supported by this icon type.', 'menu-icons' );
			} elseif ( 'hide_label' === $field_id ) {
				$value = ! empty( $value ) ? '1' : '';
			} elseif ( 'number' === $field['type'] ) {
				$min = isset( $field['attributes']['min'] ) ? (float) $field['attributes']['min'] : 0;
				if ( ! is_numeric( $value ) || (float) $value < $min ) {
					$error = __( 'must be a number within the allowed range.', 'menu-icons' );
				}
				$value = (string) $value;
			} else {
				$value   = sanitize_text_field( $value );
				$choices = wp_list_pluck( $field['choices'], 'value' );
				if ( ! in_array( $value, array_map( 'strval', $choices ), true ) ) {
					$error = __( 'is not one of the allowed values.', 'menu-icons' );
				}
			}

			if ( $error ) {
				return new WP_Error(
					'menu_icons_invalid_setting',
					sprintf( '%s %s', $field_id, $error ),
					array( 'status' => 400 )
				);
			}

			$settings[ $field_id ] = $value;
		}

		return $settings;
	}


	/**
	 * Get the icon stored on a menu item
	 *
	 * @param  int $item_id Menu item ID.
	 * @param  int $menu_id Menu ID.
	 * @return array
	 */
	protected static function _get_stored_icon( $item_id, $menu_id ) {
		$defaults = $menu_id ? Menu_Icons_Settings::get_menu_settings( $menu_id ) : array();
		$meta     = Menu_Icons_Meta::get( $item_id, $defaults );
		$result   = array(
			'item_id'  => (int) $item_id,
			'has_icon' => ! empty( $meta['type'] ) && ! empty( $meta['icon'] ),
		);

		if ( $result['has_icon'] ) {
			$keys = array_merge(
				array( 'type', 'icon', 'url' ),
				array_keys( Menu_Icons_Settings::get_settings_fields() )
			);

			$stored = array_intersect_key( $meta, array_flip( $keys ) );

			$stored['id'] = (string) $stored['icon'];
			unset( $stored['icon'] );

			$result['icon'] = $stored;
		}

		return $result;
	}
}
