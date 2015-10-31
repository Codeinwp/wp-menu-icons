<?php

/**
 * Front end functionalities
 *
 * @package Menu_Icons
 * @author  Dzikri Aziz <kvcrvt@gmail.com>
 */
final class Menu_Icons_Front_End {

	/**
	 * Add hooks for front-end
	 *
	 * @since 0.9.0
	 */
	public static function init() {
		// TODO: Get registered icon types from Icon_Picker
		// TODO: Call front-end functions of each icon types

		add_action( 'wp_enqueue_scripts', array( __CLASS__, '_enqueue_styles' ), 7 );
	}


	/**
	 * Enqueue stylesheets
	 *
	 * @since   0.1.0
	 * @access  protected
	 * @wp_hook action    wp_enqueue_scripts/7
	 * @link    http://codex.wordpress.org/Plugin_API/Action_Reference/wp_enqueue_scripts
	 */
	public static function _enqueue_styles() {
		// TODO: Enqueue icon types' stylesheets

		wp_enqueue_style(
			'menu-icons-extra',
			sprintf( '%scss/extra%s.css', Menu_Icons::get( 'url' ), Menu_Icons::get_script_suffix() ),
			false,
			Menu_Icons::VERSION
		);
	}
}
