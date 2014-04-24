<?php

/**
 * Menu Icons
 *
 * @package Menu_Icons
 * @version 0.2.3
 * @author Dzikri Aziz <kvcrvt@gmail.com>
 *
 *
 * Plugin name: Menu Icons
 * Plugin URI: http://kucrut.org/
 * Description: Easily add icons to your navigation menu items
 * Version: 0.2.3
 * Author: Dzikri Aziz
 * Author URI: http://kucrut.org/
 * License: GPLv2
 * Text Domain: menu-icons
 */


/**
 * Main plugin class
 */
final class Menu_Icons {

	const VERSION = '0.2.3';

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
	public static function _load() {
		load_plugin_textdomain( 'menu-icons', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		self::$data = array(
			'dir'        => plugin_dir_path( __FILE__ ),
			'url'        => plugin_dir_url( __FILE__ ),
			'icon_types' => array(),
		);

		require_once self::$data['dir'] . 'includes/library/functions.php';

		add_filter( 'menu_icons_types', array( __CLASS__, '_register_icon_types' ), 7 );
		add_filter( 'is_protected_meta', array( __CLASS__, '_protect_meta_key' ), 10, 3 );
		add_action( 'wp_loaded', array( __CLASS__, '_init' ), 9 );
		add_action( 'get_header', array( __CLASS__, '_load_front_end' ) );
	}


	/**
	 * Protect meta key
	 *
	 * This prevents our meta key from showing up on Custom Fields meta box
	 *
	 * @since   0.3.0
	 * @wp_hook filter is_protected_meta
	 * @param   bool   $protected        Protection status
	 * @param   string $meta_key         Meta key
	 * @param   string $meta_type        Meta type
	 * @return  bool   Protection status
	 */
	public static function _protect_meta_key( $protected, $meta_key, $meta_type ) {
		if ( 'menu-icons' === $meta_key ) {
			$protected = true;
		}

		return $protected;
	}


	/**
	 * Initialize
	 *
	 * 1. Collect registered types
	 * 2. Load settings
	 *
	 * @since   0.1.0
	 * @wp_hook action wp_loaded/9
	 * @link    http://codex.wordpress.org/Plugin_API/Action_Reference/wp_loaded Action: wp_loaded/9
	 */
	public static function _init() {
		self::_collect_icon_types();

		// Nothing to do if there are no icon types registered
		if ( empty( self::$data['icon_types'] ) ) {
			trigger_error( __( 'Menu Icons: No registered icon types found.', 'menu-icons' ) );

			return;
		}

		// Load settings
		require_once self::$data['dir'] . 'includes/settings.php';
		Menu_Icons_Settings::init();
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
	 * Load front-end tasks
	 *
	 * @since   0.1.0
	 * @access  protected
	 * @wp_hook action    load-nav-menus.php/10
	 * @link    http://codex.wordpress.org/Plugin_API/Action_Reference/get_header Action: get_header/10
	 */
	public static function _load_front_end() {
		foreach ( Menu_Icons_Settings::get('icon_types') as $id ) {
			if ( isset( self::$data['icon_types'][ $id ] ) ) {
				call_user_func( self::$data['icon_types'][ $id ]['front_cb'] );
			}
		}

		add_action( 'wp_enqueue_scripts', array( __CLASS__, '_enqueue_styles' ), 7 );
	}


	/**
	 * Enqueue icon type's stylesheet
	 *
	 * @since 0.2.0
	 * @param string $id    Stylesheet ID
	 * @param array  $props Icon type properties
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
	 * Get script & style suffix
	 *
	 * When SCRIPT_DEBUG is defined true, this will return '.min'
	 *
	 * @since 0.2.0
	 * @return string
	 */
	public static function get_script_suffix() {
		return ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	}


	/**
	 * Enqueue stylesheets
	 *
	 * @since   0.1.0
	 * @access  protected
	 * @wp_hook action          wp_enqueue_scripts/10
	 * @link   http://codex.wordpress.org/Plugin_API/Action_Reference/wp_enqueue_scripts Action: wp_enqueue_scripts/10
	 */
	public static function _enqueue_styles() {
		// Enqueue icon types' stylesheets
		foreach ( Menu_Icons_Settings::get('icon_types') as $id ) {
			if ( isset( self::$data['icon_types'][ $id ] ) ) {
				self::enqueue_type_stylesheet( $id, self::$data['icon_types'][ $id ] );
			}
		}

		$load_extra_style = (bool) Menu_Icons_Settings::get( 'extra_css' );
		if ( true === $load_extra_style ) {
			wp_enqueue_style(
				'menu-icons-extra',
				Menu_Icons::get( 'url' ) . 'css/extra' . self::get_script_suffix() .'.css',
				false,
				Menu_Icons::VERSION
			);
		}
	}
}
add_action( 'plugins_loaded', array( 'Menu_Icons', '_load' ) );
