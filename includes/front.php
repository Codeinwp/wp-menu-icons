<?php

/**
 * Front end functionalities
 *
 * @package Menu_Icons
 * @author  Dzikri Aziz <kvcrvt@gmail.com>
 */
final class Menu_Icons_Front_End {

	/**
	 * Icon types
	 *
	 * @since  0.9.0
	 * @access protected
	 * @var    array
	 */
	protected static $icon_types = array();


	/**
	 * Add hooks for front-end
	 *
	 * @since 0.9.0
	 */
	public static function init() {
		$active_types = Menu_Icons_Settings::get( 'global', 'icon_types' );

		if ( empty( $active_types ) ) {
			return;
		}

		foreach ( Icon_Picker_Types_Registry::instance()->types as $type ) {
			if ( in_array( $type->id, $active_types ) ) {
				self::$icon_types[] = $type;
			}
		}

		// TODO: Call front-end calback of each icon types

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
		foreach ( self::$icon_types as $type ) {
			if ( wp_style_is( $type->stylesheet_id, 'registered' ) ) {
				wp_enqueue_style( $type->stylesheet_id );
			}
		}

		wp_enqueue_style(
			'menu-icons-extra',
			sprintf( '%scss/extra%s.css', Menu_Icons::get( 'url' ), Menu_Icons::get_script_suffix() ),
			false,
			Menu_Icons::VERSION
		);
	}
}
