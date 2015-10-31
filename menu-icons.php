<?php

/**
 * Menu Icons
 *
 * @package Menu_Icons
 * @version 0.8.1
 * @author Dzikri Aziz <kvcrvt@gmail.com>
 *
 *
 * Plugin name: Menu Icons
 * Plugin URI: http://kucrut.org/
 * Description: Spice up your navigation menus with pretty icons, easily.
 * Version: 0.8.1
 * Author: Dzikri Aziz
 * Author URI: http://kucrut.org/
 * License: GPLv2
 * Text Domain: menu-icons
 * Domain Path: /languages
 */


/**
 * Main plugin class
 */
final class Menu_Icons {

	const VERSION = '0.8.1';

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
	 * @since  0.9.0  Return NULL if $name is not set in $data.
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

		return null;
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
			'dir'                => plugin_dir_path( __FILE__ ),
			'url'                => plugin_dir_url( __FILE__ ),
			'icon_types'         => array(),
			'default_style'      => array(
				'font-size'      => '1.2em',
				'vertical-align' => 'middle',
			),
		);

		require_once self::$data['dir'] . 'includes/meta.php';
		require_once self::$data['dir'] . 'includes/library/functions.php';

		Menu_Icons_Meta::init();

		add_action( 'wp_loaded', array( __CLASS__, '_init' ), 9 );
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
			// trigger_error( esc_html__( 'Menu Icons: No registered icon types found.', 'menu-icons' ) );

			return;
		}

		// Load settings
		require_once self::$data['dir'] . 'includes/settings.php';
		Menu_Icons_Settings::init();

		if ( ! is_admin() ) {
			require_once self::$data['dir'] . '/includes/front.php';
			Menu_Icons_Front_End::init();
		}

		do_action( 'menu_icons_loaded' );
	}


	/**
	 * Collect icon types
	 *
	 * @since  0.1.0
	 * @access private
	 * @uses   apply_filters() Calls 'menu_icons_types' to get registered types.
	 */
	private static function _collect_icon_types() {
		return array();
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
	 * Get nav menu ID based on arguments passed to wp_nav_menu()
	 *
	 * @since  0.3.0
	 * @param  array $args wp_nav_menu() Arguments
	 * @return mixed Nav menu ID or FALSE on failure
	 */
	public static function get_nav_menu_id( $args ) {
		$args = (object) $args;
		$menu = wp_get_nav_menu_object( $args->menu );

		// Get the nav menu based on the theme_location
		if ( ! $menu
			&& $args->theme_location
			&& ( $locations = get_nav_menu_locations() )
			&& isset( $locations[ $args->theme_location ] )
		) {
			$menu = wp_get_nav_menu_object( $locations[ $args->theme_location ] );
		}

		// get the first menu that has items if we still can't find a menu
		if ( ! $menu && ! $args->theme_location ) {
			$menus = wp_get_nav_menus();
			foreach ( $menus as $menu_maybe ) {
				if ( $menu_items = wp_get_nav_menu_items( $menu_maybe->term_id, array( 'update_post_term_cache' => false ) ) ) {
					$menu = $menu_maybe;
					break;
				}
			}
		}

		if ( is_object( $menu ) && ! is_wp_error( $menu ) ) {
			return $menu->term_id;
		} else {
			return false;
		}
	}


	/**
	 * Get hidden label class
	 *
	 * @return string
	 */
	public static function get_hidden_label_class() {
		/**
		 * Allow themes/plugins to overrride the hidden label class
		 *
		 * @since  0.8.0
		 * @param  string $hidden_label_class Hidden label class.
		 * @return string
		 */
		$hidden_label_class = apply_filters( 'menu_icons_hidden_label_class', 'visuallyhidden' );

		return $hidden_label_class;
	}
}
add_action( 'plugins_loaded', array( 'Menu_Icons', '_load' ) );
