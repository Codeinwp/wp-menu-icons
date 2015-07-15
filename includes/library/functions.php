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


if ( ! function_exists( 'kucrut_validate' ) ) {
	/**
	 * Validate settings values
	 *
	 * @param  array $values Settings values
	 * @return array
	 */
	function kucrut_validate( $values, $sanitize_cb = 'wp_kses_data' ) {
		foreach ( $values as $key => $value ) {
			if ( is_array( $value ) ) {
				$values[ $key ] = kucrut_validate( $value );
			} else {
				$values[ $key ] = call_user_func_array(
					$sanitize_cb,
					array( $value )
				);
			}
		}

		return $values;
	}
}
