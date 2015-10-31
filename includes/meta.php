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

		if ( empty( $values ) || ! is_array( $values ) ) {
			$values = array();
		}

		if ( isset( $values['size'] ) && ! isset( $values['font_size'] ) ) {
			$values['font_size'] = $values['size'];
			unset( $values['size'] );
		}

		return $values;
	}
}
