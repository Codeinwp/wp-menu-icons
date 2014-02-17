<?php

/**
 * Menu Icons
 *
 * @package Menu_Icons
 * @version 0.1.0
 * @author Dzikri Aziz <kvcrvt@gmail.com>
 *
 *
 * Plugin name: Menu Icons
 * Plugin URI: http://kucrut.org/
 * Description: Easily add icons to your navigation menu items
 * Version: 0.1.0
 * Author: Dzikri Aziz
 * Author URI: http://kucrut.org/
 * License: GPLv2
 * Text Domain: menu-icons
 */


/**
 * Main plugin class
 *
 * @since 0.1.0
 */
class Menu_Icons {

	const VERSION = '0.1.0';

	/**
	 * Holds plugin data
	 *
	 * @access protected
	 * @since  0.1.0
	 * @var    array
	 */
	protected static $data;


	/**
	 * Get plugin data
	 *
	 * @since  0.1.0
	 * @param  string $name
	 *
	 * @return mixed
	 */
	public static function get( $name = null ) {
		if ( is_null( $name ) ) {
			return self::$data;
		}

		if ( isset( self::$data[ $name ] ) ) {
			return self::$data[ $name ];
		}

		return false;
	}


	/**
	 * Initialize plugin
	 *
	 * @since   0.1.0
	 * @access  protected
	 * @wp_hook action plugins_loaded
	 */
	public static function init() {
		self::$data = array(
			'dir' => plugin_dir_path( __FILE__ ),
			'url' => plugin_dir_url( __FILE__ ),
		);

		// Load files
		require_once self::$data['dir'] . '/includes/type-genericons.php';

		// Admin
		add_action( 'load-nav-menus.php', array( __CLASS__, '_load_nav_menus' ) );

		// Extra stylesheet
		add_action( 'wp_enqueue_scripts', array( __CLASS__, '_enqueue_scripts_styles' ), 6 );
	}


	/**
	 * Prepare page: wp-admin/nav-menus.php
	 *
	 * @since   0.1.0
	 * @access  protected
	 * @wp_hook action load-nav-menus.php
	 */
	public static function _load_nav_menus() {
		// Load menu item custom fields plugin
		require_once self::$data['dir'] . 'includes/menu-item-custom-fields/menu-item-custom-fields.php';

		// Load custom fields
		require_once self::$data['dir'] . 'includes/admin.php';
		Menu_Icons_Admin_Nav_Menus::init();
	}


	/**
	 * Enqueue extra stylesheet
	 *
	 * This stylesheet will override some styles of the icons
	 *
	 * @since   0.1.0
	 * @access  protected
	 * @wp_hook action          wp_enqueue_scripts
	 * @uses    apply_filters() Calls 'menu_icons_load_extra_style' allow plugins/themes to
	 *                          enable/disable the loading of the extra stylesheet
	 */
	public static function _enqueue_scripts_styles() {
		/**
		 * A
		 *
		 * @since 0.1.0
		 * @param bool $load_extra_style
		 */
		$load_extra_style = (bool) apply_filters( 'menu_icons_load_extra_style', true );

		if ( true === $load_extra_style ) {
			wp_enqueue_style(
				'menu-icons-extra',
				Menu_Icons::get( 'url' ) . '/css/extra.css',
				false,
				Menu_Icons::VERSION
			);
		}
	}


	/**
	 * Get icon types
	 *
	 * @since  0.1.0
	 * @uses   apply_filters() Calls 'menu_icons_types' on returned array.
	 *
	 * @return array
	 */
	public static function get_icon_types() {
		$types = apply_filters( 'menu_icons_types', array() );
		ksort( $types );

		return (array) $types;
	}
}
add_action( 'plugins_loaded', array( 'Menu_Icons', 'init' ) );
