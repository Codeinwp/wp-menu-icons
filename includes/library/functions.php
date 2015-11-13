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


if ( ! function_exists( 'kucrut_get_image_sizes' ) ) {
	/**
	 * Get image sizes
	 *
	 * @since  0.9.0
	 * @access protected
	 * @return array
	 */
	function kucrut_get_image_sizes() {
		$_sizes = array(
			'thumbnail' => __( 'Thumbnail', 'menu-icons' ),
			'medium'    => __( 'Medium', 'menu-icons' ),
			'large'     => __( 'Large', 'menu-icons' ),
			'full'      => __( 'Full Size', 'menu-icons' ),
		);

		$_sizes = apply_filters( 'image_size_names_choose', $_sizes );

		$sizes = array();
		foreach ( $_sizes as $value => $label ) {
			$sizes[] = array(
				'value' => $value,
				'label' => $label,
			);
		}

		return $sizes;
	}
}


if ( ! function_exists( 'kucrut_get_script_suffix' ) ) {
	/**
	 * Get script & style suffix
	 *
	 * When SCRIPT_DEBUG is defined true, this will return '.min'.
	 *
	 * @return string
	 */
	function kucrut_get_script_suffix() {
		return ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	}
}
