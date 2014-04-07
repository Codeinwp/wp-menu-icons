<?php

/**
 * Menu Icons
 *
 * @package Menu_Icons
 * @version 0.1.5
 * @author Dzikri Aziz <kvcrvt@gmail.com>
 *
 *
 * Plugin name: Menu Icons
 * Plugin URI: http://kucrut.org/
 * Description: Easily add icons to your navigation menu items
 * Version: 0.1.5
 * Author: Dzikri Aziz
 * Author URI: http://kucrut.org/
 * License: GPLv2
 * Text Domain: menu-icons
 */


/**
 * Main plugin class
 *
 * @version 0.1.5
 */
final class Menu_Icons {

	const VERSION = '0.1.5';

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
	 * 3. Register built-in icon types
	 * 4. Attach plugin initialization at wp_loaded hook
	 *
	 * @since   0.1.0
	 * @wp_hook action plugins_loaded/10
	 * @link    http://codex.wordpress.org/Plugin_API/Action_Reference/plugins_loaded Action: plugins_loaded/10
	 */
	public static function load() {
		load_plugin_textdomain( 'menu-icons', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		self::$data = array(
			'dir'        => plugin_dir_path( __FILE__ ),
			'url'        => plugin_dir_url( __FILE__ ),
			'icon_types' => array(),
		);

		add_filter( 'menu_icons_types', array( __CLASS__, '_register_icon_types' ), 7 );
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
		self::_collect_icon_types();

		// Nothing to do if there are no icon types registered
		if ( empty( self::$data['icon_types'] ) ) {
			return;
		}

		// Back
		add_filter( 'load-nav-menus.php', array( __CLASS__, '_load_nav_menus' ) );
		add_filter( 'wp_edit_nav_menu_walker', array( __CLASS__, '_filter_wp_edit_nav_menu_walker' ), 99 );

		// Front
		add_action( 'get_header', array( __CLASS__, '_load_front_end' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, '_enqueue_styles' ), 7 );
	}


	/**
	 * Register built-in icon types
	 *
	 * @since   0.1.5
	 * @access  protected
	 * @wp_hook filter    menu_icons_types
	 *
	 * @param   array     $icon_types      Current icon types
	 * @return  array
	 */
	public static function _register_icon_types( $icon_types ) {
		$builtin_types = array(
			'dashicons',
			'genericons',
			'fontawesome',
		);

		foreach ( $builtin_types as $type ) {
			require_once sprintf( '%s/includes/type-%s.php', self::$data['dir'], $type );

			$class_name    = sprintf( 'Menu_Icons_Type_%s', ucfirst( $type ) );
			$type_instance = new $class_name;
			$icon_types    = $type_instance->register( $icon_types );
		}

		return $icon_types;
	}


	/**
	 * Collect icon types
	 *
	 * @since  0.1.0
	 * @access private
	 * @uses   apply_filters() Calls 'menu_icons_types' to get registered types.
	 */
	private static function _collect_icon_types() {
		$types     = (array) apply_filters( 'menu_icons_types', array() );
		$defaults  = array(
			'label'      => '',
			'field_cb'   => '',
			'front_cb'   => '',
			'stylesheet' => '',
			'version'    => get_bloginfo( 'version' ),
		);
		$callbacks = array( 'field_cb', 'front_cb', 'frame_cb' );
		$optionals = array( 'stylesheet', 'frame_cb' );
		$messages  = array(
			'empty'    => _x(
				'%1$s cannot be empty, %2$s has been disabled.',
				'1: Property key, 2: Icon type ID',
				'menu-icons'
			),
			'callback' => _x(
				'%1$s must be callable, %2$s has been disabled.',
				'1: Property key, 2: Icon type ID',
				'menu-icons'
			),
		);

		foreach ( $types as $type => $props ) {
			$type_props = wp_parse_args( $props, $defaults );
			foreach ( $type_props as $key => $value ) {
				if ( ! in_array( $key, $optionals ) && empty( $value ) ) {
					trigger_error(
						'<strong>Menu Icons</strong>: ' . vsprintf(
							$messages['empty'],
							array( '<em>'.$key.'</em>', '<em>'.$type.'</em>' )
						)
					);
					continue 2;
				}

				if ( in_array( $key, $callbacks ) && ! is_callable( $value ) ) {
					trigger_error(
						'<strong>Menu Icons</strong>: ' . vsprintf(
							$messages['callback'],
							array( '<em>'.$key.'</em>', '<em>'.$type.'</em>' )
						)
					);
					continue 2;
				}
			}

			self::$data['icon_types'][ $type ] = $type_props;
		}

		ksort( self::$data['icon_types'] );
	}


	/**
	 * Prepare custom walker and custom field
	 *
	 * @since   0.1.3
	 * @access  protected
	 * @wp_hook filter    wp_edit_nav_menu_walker/10/1
	 */
	public static function _filter_wp_edit_nav_menu_walker( $walker ) {
		// Load custom fields
		require_once self::$data['dir'] . 'includes/admin.php';
		add_filter( 'menu_item_custom_fields', array( 'Menu_Icons_Admin_Nav_Menus', '_fields' ), 10, 3 );

		// Load menu item custom fields plugin
		if ( ! class_exists( 'Menu_Item_Custom_Fields_Walker' ) ) {
			require_once self::$data['dir'] . '/includes/walker-nav-menu-edit.php';
		}
		$walker = 'Menu_Item_Custom_Fields_Walker';

		return $walker;
	}


	/**
	 * Prepare wp-admin/nav-menus.php
	 *
	 * @since   0.1.5
	 * @access  protected
	 * @wp_hook action    load-nav-menus.php/10
	 * @link    http://codex.wordpress.org/Plugin_API/Action_Reference/load-%28page%29 Action: load-nav-menus.php/10
	 */
	public static function _load_nav_menus() {
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
	 *
	 */
	public static function enqueue_type_stylesheet( $id, $props ) {
		if ( empty( $props['stylesheet'] ) ) {
			return;
		}

		if ( wp_style_is( $props['stylesheet'], 'registered' ) ) {
			wp_enqueue_style( $id );
		}
		else {
			wp_enqueue_style( $id, $props['stylesheet'], false, $props['version'] );
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
			self::enqueue_type_stylesheet( $id, $props );
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
