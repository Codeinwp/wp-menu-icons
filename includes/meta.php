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
		if ( static::KEY === $meta_key ) {
			$protected = true;
		}

		return $protected;
	}


	/**
	 * Get menu item meta value
	 *
	 * @since  0.3.0
	 * @param  int   $item_id Menu item ID.
	 * @return array
	 */
	public static function get( $item_id ) {
		$values = get_post_meta( $item_id, self::KEY, true );
		$values = wp_parse_args(
			(array) $values,
			array(
				'type' => '',
				'icon' => '',
				'url'  => '',
			)
		);

		// Backward-compatibility.
		if ( empty( $values['icon'] ) &&
			! empty( $values['type'] ) &&
			! empty( $values[ "{$values['type']}-icon" ] )
		) {
			$values['icon'] = $values[ "{$values['type']}-icon" ];
		}

		if ( isset( $values['size'] ) && ! isset( $values['font_size'] ) ) {
			$values['font_size'] = $values['size'];
			unset( $values['size'] );
		}

		// The values below will NOT be saved
		if ( ! empty( $values['icon'] ) && in_array( $values['type'], array( 'image', 'svg' ) ) ) {
			$values['url'] = wp_get_attachment_image_url( $values['icon'], 'thumbnail', false );
		}

		return $values;
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

		// Update
		if ( ! empty( $value ) ) {
			update_post_meta( $id, self::KEY, $value );
		} else {
			delete_post_meta( $id, self::KEY );
		}
	}
}
