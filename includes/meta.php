<?php

/**
 * Menu item metadata
 *
 * @package Menu_Icons
 * @author  Dzikri Aziz <kvcrvt@gmail.com>
 */
final class Menu_Icons_Meta {

	const KEY = 'menu-icons';

	/**
	 * Default meta value
	 *
	 * @since  0.9.0
	 * @access protected
	 * @var    array
	 */
	protected static $defaults = array(
		'type' => '',
		'icon' => '',
		'url'  => '',
	);


	/**
	 * Initialize metadata functionalities
	 *
	 * @since 0.9.0
	 */
	public static function init() {
		add_filter( 'is_protected_meta', array( __CLASS__, '_protect_meta_key' ), 10, 3 );
	}


	/**
	 * Protect meta key
	 *
	 * This prevents our meta key from showing up on Custom Fields meta box.
	 *
	 * @since   0.3.0
	 * @wp_hook filter is_protected_meta
	 * @param   bool   $protected        Protection status.
	 * @param   string $meta_key         Meta key.
	 * @param   string $meta_type        Meta type.
	 * @return  bool   Protection status.
	 */
	public static function _protect_meta_key( $protected, $meta_key, $meta_type ) {
		if ( self::KEY === $meta_key ) {
			$protected = true;
		}

		return $protected;
	}


	/**
	 * Get menu item meta value
	 *
	 * @since  0.3.0
	 * @since  0.9.0  Add $defaults parameter.
	 * @param  int    $id       Menu item ID.
	 * @param  array  $defaults Optional. Default value.
	 * @return array
	 */
	public static function get( $id, $defaults = array() ) {
		$defaults = wp_parse_args( $defaults, self::$defaults );
		$value    = get_post_meta( $id, self::KEY, true );
		$value    = wp_parse_args( (array) $value, $defaults );

		// Backward-compatibility.
		if ( empty( $value['icon'] ) &&
			! empty( $value['type'] ) &&
			! empty( $value[ "{$value['type']}-icon" ] )
		) {
			$value['icon'] = $value[ "{$value['type']}-icon" ];
		}

		if ( ! empty( $value['width'] ) ) {
			$value['svg_width'] = $value['width'];
		}
		unset( $value['width'] );

		if ( isset( $value['position'] ) &&
			! in_array( $value['position'], array( 'before', 'after' ), true )
		) {
			$value['position'] = $defaults['position'];
		}

		if ( isset( $value['size'] ) && ! isset( $value['font_size'] ) ) {
			$value['font_size'] = $value['size'];
			unset( $value['size'] );
		}

		// The values below will NOT be saved
		if ( ! empty( $value['icon'] ) &&
			in_array( $value['type'], array( 'image', 'svg' ), true )
		) {
			$value['url'] = wp_get_attachment_image_url( $value['icon'], 'thumbnail', false );
		}

		return $value;
	}


	/**
	 * Update menu item metadata
	 *
	 * @since 0.9.0
	 *
	 * @param int   $id    Menu item ID.
	 * @param mixed $value Metadata value.
	 *
	 * @return void
	 */
	public static function update( $id, $value ) {
		/**
		 * Allow plugins/themes to filter the values
		 *
		 * Deprecated.
		 *
		 * @since 0.1.0
		 * @param array $value Metadata value.
		 * @param int   $id    Menu item ID.
		 */
		$_value = apply_filters( 'menu_icons_values', $value, $id );

		if ( $_value !== $value && WP_DEBUG ) {
			_deprecated_function( 'menu_icons_values', '0.8.0', 'menu_icons_item_meta_values' );
		}

		/**
		 * Allow plugins/themes to filter the values
		 *
		 * @since 0.8.0
		 * @param array $value Metadata value.
		 * @param int   $id    Menu item ID.
		 */
		$value = apply_filters( 'menu_icons_item_meta_values', $_value, $id );

		// Don't bother saving if `type` or `icon` is not set.
		if ( empty( $value['type'] ) || empty( $value['icon'] ) ) {
			$value = false;
		}

		// Update
		if ( ! empty( $value ) ) {
			update_post_meta( $id, self::KEY, $value );
		} else {
			delete_post_meta( $id, self::KEY );
		}
	}
}
