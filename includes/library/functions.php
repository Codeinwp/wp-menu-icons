<?php

/**
 * Misc. helper functions
 *
 * @author Dzikri Aziz <kvcrvt@gmail.com>
 */


if ( ! function_exists( 'kucrut_get_array_value_deep' ) ) {
	/**
	 * Get value of a multidimensional array
	 *
	 * @since  0.1.0
	 * @param  array $array Haystack
	 * @param  array $keys  Needles
	 * @return mixed
	 */
	function kucrut_get_array_value_deep( Array $array, Array $keys ) {
		if ( empty( $array ) || empty( $keys ) ) {
			return $array;
		}

		foreach ( $keys as $idx => $key ) {
			unset( $keys[ $idx ] );

			if ( ! isset( $array[ $key ] ) ) {
				return null;
			}

			if ( ! empty( $keys ) ) {
				$array = $array[ $key ];
			}
		}

		if ( ! isset( $array[ $key ] ) ) {
			return null;
		}

		return $array[ $key ];
	}
}
