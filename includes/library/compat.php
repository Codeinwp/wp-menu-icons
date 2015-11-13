<?php

/**
 * Misc. functions for backward-compatibility.
 */

if ( ! function_exists( 'wp_get_attachment_image_url' ) ) {
	/**
	 * Get the URL of an image attachment.
	 *
	 * @since 4.4.0
	 *
	 * @param int          $attachment_id Image attachment ID.
	 * @param string|array $size          Optional. Image size to retrieve. Accepts any valid image size, or an array
	 *                                    of width and height values in pixels (in that order). Default 'thumbnail'.
	 * @param bool         $icon          Optional. Whether the image should be treated as an icon. Default false.
	 * @return string|false Attachment URL or false if no image is available.
	 */
	function wp_get_attachment_image_url( $attachment_id, $size = 'thumbnail', $icon = false ) {
		$image = wp_get_attachment_image_src( $attachment_id, $size, $icon );
		return isset( $image['0'] ) ? $image['0'] : false;
	}
}
