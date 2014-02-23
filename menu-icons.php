<?php

/**
 * Menu Icons
 *
 * @package Menu_Icons
 * @version 0.1.2
 * @author Dzikri Aziz <kvcrvt@gmail.com>
 *
 *
 * Plugin name: Menu Icons
 * Plugin URI: http://kucrut.org/
 * Description: Easily add icons to your navigation menu items
 * Version: 0.1.2
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
final class Menu_Icons {

	const VERSION = '0.1.2';

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
	 * Load plugin
	 *
	 * 1. Load translation
	 * 2. Set plugin data (directory and URL paths)
	 * 3. Attach plugin initialization at wp_loaded hook
	 *
	 * @since   0.1.0
	 * @wp_hook action plugins_loaded/10
	 * @link    http://codex.wordpress.org/Plugin_API/Action_Reference/plugins_loaded Action: plugins_loaded/10
	 */
	public static function load() {
		load_plugin_textdomain( 'stream', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		self::$data = array(
			'dir'        => plugin_dir_path( __FILE__ ),
			'url'        => plugin_dir_url( __FILE__ ),
			'icon_types' => array(),
		);

		add_action( 'wp_loaded', array( __CLASS__, 'init' ), 9 );
	}


	/**
	 * Initialize plugin
	 *
	 * 1. Collect registered types
	 * 2. Add hook callbacks
	 *
	 * @since   0.1.0
	 * @wp_hook action wp_loaded/9
	 * @link    http://codex.wordpress.org/Plugin_API/Action_Reference/wp_loaded Action: wp_loaded/9
	 */
	public static function init() {
		// Load icon types
		require_once self::$data['dir'] . '/includes/type-fonts.php';
		require_once self::$data['dir'] . '/includes/type-dashicons.php';
		require_once self::$data['dir'] . '/includes/type-genericons.php';

		self::_collect_icon_types();

		// Nothing to do if there are no icon types registered
		if ( empty( self::$data['icon_types'] ) ) {
			return;
		}

		add_action( 'load-nav-menus.php', array( __CLASS__, '_load_nav_menus' ) );
		add_action( 'get_header', array( __CLASS__, '_load_front_end' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, '_enqueue_styles' ), 7 );
	}


	/**
	 * Collect icon types
	 *
	 * @since  0.1.0
	 * @access private
	 * @uses   apply_filters() Calls 'menu_icons_types' to get registered types.
	 */
	private static function _collect_icon_types() {
		$types    = (array) apply_filters( 'menu_icons_types', array() );
		$defaults = array(
			'label'      => '',
			'field_cb'   => '',
			'front_cb'   => '',
			'stylesheet' => '',
			'version'    => get_bloginfo( 'version' ),
		);

		foreach ( $types as $type => $props ) {
			$type_props = wp_parse_args( $props, $defaults );
			foreach ( $type_props as $key => $value ) {
				if ( empty( $value ) ) {
					continue 2;
				}

				if ( 'field_cb' === $key && ! is_callable( $value ) ) {
					continue 2;
				}

				if ( 'front_cb' === $key && ! is_callable( $value ) ) {
					continue 2;
				}
			}

			self::$data['icon_types'][ $type ] = $type_props;
		}

		ksort( self::$data['icon_types'] );
	}


	/**
	 * Prepare page: wp-admin/nav-menus.php
	 *
	 * @since   0.1.0
	 * @access  protected
	 * @wp_hook action    load-nav-menus.php/10
	 * @link    http://codex.wordpress.org/Plugin_API/Action_Reference/load-(page) Action: load-nav-menus.php/10
	 */
	public static function _load_nav_menus() {
		// Load menu item custom fields plugin
		require_once self::$data['dir'] . 'includes/menu-item-custom-fields/menu-item-custom-fields.php';

		// Load custom fields
		require_once self::$data['dir'] . 'includes/admin.php';
		Menu_Icons_Admin_Nav_Menus::init();
	}


	/**
	 * Load front-end tasks
	 *
	 * @since   0.1.0
	 * @access  protected
	 * @wp_hook action    load-nav-menus.php/10
	 * @link    http://codex.wordpress.org/Plugin_API/Action_Reference/get_header Action: get_header/10
	 */
	public static function _load_front_end() {
		foreach ( self::$data['icon_types'] as $props ) {
			call_user_func( $props['front_cb'] );
		}
	}


	/**
	 * Enqueue extra stylesheet
	 *
	 * This stylesheet will override some styles of the icons
	 *
	 * @since   0.1.0
	 * @access  protected
	 * @wp_hook action          wp_enqueue_scripts/10
	 * @uses    apply_filters() Calls 'menu_icons_load_extra_style' allow plugins/themes to
	 *                          enable/disable the loading of the extra stylesheet
	 * @link   http://codex.wordpress.org/Plugin_API/Action_Reference/wp_enqueue_scripts Action: wp_enqueue_scripts/10
	 */
	public static function _enqueue_styles() {
		// Enqueue icon types' stylesheets
		foreach ( self::$data['icon_types'] as $id => $props ) {
			if ( wp_style_is( $props['stylesheet'], 'registered' ) ) {
				wp_enqueue_style( $id );
			}
			else {
				wp_enqueue_style( $id, $props['stylesheet'], false, $props['version'] );
			}
		}

		/**
		 * Enable/disable loading of extra stylesheet
		 *
		 * @since 0.1.0
		 * @param bool $load_extra_style
		 */
		$load_extra_style = (bool) apply_filters( 'menu_icons_load_extra_style', true );

		if ( true === $load_extra_style ) {
			wp_enqueue_style(
				'menu-icons-extra',
				Menu_Icons::get( 'url' ) . 'css/extra.css',
				false,
				Menu_Icons::VERSION
			);
		}
	}
}
add_action( 'plugins_loaded', array( 'Menu_Icons', 'load' ) );
